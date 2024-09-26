<?php

namespace Laposta\SignupBasic;

use Laposta\SignupBasic\Container\Container;
use Laposta\SignupBasic\Service\Logger;
use Laposta\SignupBasic\Service\RequestHelper;

class Plugin
{
    const SHORTCODE_RENDER_FORM = 'laposta_signup_basic_form';
    const SLUG_SETTINGS = 'laposta_signup_basic_settings';

    const TRANSIENT_LISTS = 'laposta_lists';
    const TRANSIENT_STATUS = 'laposta_status';
    const TRANSIENT_LIST_FIELDS_PREFIX = 'laposta_list_fields_';

    const OPTION_GROUP = 'laposta_signup_basic';
    const OPTION_API_KEY = 'laposta-api_key';
    const OPTION_CLASS_TYPE = 'laposta_signup_basic_class_type'; // bootstrap v4, v5 or custom
    const OPTION_ADD_CLASSES = 'laposta_signup_basic_add_classes';
    const OPTION_CLASS_FORM = 'laposta_signup_basic_class_form';
    const OPTION_CLASS_FORM_BODY = 'laposta_signup_basic_class_form_body';
    const OPTION_CLASS_FIELD_WRAPPER = 'laposta_signup_basic_class_field_wrapper';
    const OPTION_CLASS_FIELD_HAS_ERROR = 'laposta_signup_basic_class_field_has_error';
    const OPTION_CLASS_INPUT_HAS_ERROR = 'laposta_signup_basic_class_input_has_error';
    const OPTION_CLASS_FIELD_ERROR_FEEDBACK = 'laposta_signup_basic_class_field_error_feedback';
    const OPTION_CLASS_INPUT = 'laposta_signup_basic_class_input';
    const OPTION_CLASS_LABEL = 'laposta_signup_basic_class_label';
    const OPTION_CLASS_LABEL_NAME = 'laposta_signup_basic_class_label_name';
    const OPTION_CLASS_LABEL_REQUIRED = 'laposta_signup_basic_class_label_required';
    const OPTION_CLASS_SELECT = 'laposta_signup_basic_class_select';
    const OPTION_CLASS_CHECKS_WRAPPER = 'laposta_signup_basic_class_checks_wrapper';
    const OPTION_CLASS_CHECK_WRAPPER = 'laposta_signup_basic_class_check_wrapper';
    const OPTION_CLASS_CHECK_INPUT = 'laposta_signup_basic_class_check_input';
    const OPTION_CLASS_CHECK_LABEL = 'laposta_signup_basic_class_check_label';
    const OPTION_CLASS_SUBMIT_BUTTON_AND_LOADER_WRAPPER = 'laposta_signup_basic_class_submit_button_and_loader_wrapper';
    const OPTION_CLASS_SUBMIT_BUTTON = 'laposta_signup_basic_class_submit_button';
    const OPTION_SUBMIT_BUTTON_TEXT = 'laposta_signup_basic_submit_button_text';
    const OPTION_CLASS_LOADER = 'laposta_signup_basic_class_loader';
    const OPTION_CLASS_GLOBAL_ERROR = 'laposta_signup_basic_class_global_error';
    const OPTION_CLASS_SUCCESS_CONTAINER = 'laposta_signup_basic_class_success_container';
    const OPTION_CLASS_SUCCESS_WRAPPER = 'laposta_signup_basic_class_success_wrapper';
    const OPTION_CLASS_SUCCESS_TITLE = 'laposta_signup_basic_class_success_title';
    const OPTION_CLASS_SUCCESS_TEXT = 'laposta_signup_basic_class_success_text';
    const OPTION_SUCCESS_TITLE = 'laposta_signup_basic_success_title';
    const OPTION_SUCCESS_TEXT = 'laposta_signup_basic_success_text';
    const OPTION_INLINE_CSS = 'laposta_signup_basic_inline_css';

    const DEFAULT_CAPABILITY = 'manage_options';
    const FILTER_SETTINGS_PAGE_CAPABILITY = 'laposta_signup_basic_settings_page_capability';
    const FILTER_ENABLE_LOGGING = 'laposta_signup_basic_enable_logging';
    const FILTER_REQUIRED_INDICATOR = 'laposta_signup_basic_filter_required_indicator';
    const FILTER_FIELD_LABEL = 'laposta_signup_basic_filter_field_label';
    const FILTER_FIELD_PLACEHOLDER = 'laposta_signup_basic_filter_field_placeholder';
    const FILTER_FIELD_DEFAULT_SELECT_OPTION_TEXT = 'laposta_signup_basic_filter_default_select_option_text';
    const FILTER_SUBMIT_BUTTON_TEXT = 'laposta_signup_basic_filter_submit_button_text';
    const FILTER_SUCCESS_TITLE = 'laposta_signup_basic_filter_success_title';
    const FILTER_SUCCESS_TEXT = 'laposta_signup_basic_filter_success_text';

    /**
     * @var Container
     */
    protected $c;

    /**
     * @var string
     */
    protected $rootDir;

    /**
     * @var string
     */
    protected $rootUrl;

    /**
     * @var string
     */
    protected $pluginBaseName;


    public function __construct(Container $container)
    {
        $this->c = $container;

        $this->rootDir = realpath(__DIR__.'/..');
        $this->rootUrl = plugin_dir_url($this->rootDir.'/laposta-signup-basic.php');
        $this->pluginBaseName = plugin_basename($this->rootDir.'/laposta-signup-basic.php');

        $this->defineConstants();
        $this->init();
    }

    protected function defineConstants()
    {
        define('LAPOSTA_SIGNUP_BASIC_ROOT_DIR', $this->rootDir);
        define('LAPOSTA_SIGNUP_BASIC_TEMPLATE_DIR', $this->rootDir.DIRECTORY_SEPARATOR.'templates');
        define('LAPOSTA_SIGNUP_BASIC_ASSETS_URL', $this->rootUrl.'assets');
        define('LAPOSTA_SIGNUP_BASIC_AJAX_ACTION', 'laposta_signup_basic_ajax');
        define('LAPOSTA_SIGNUP_BASIC_AJAX_URL', admin_url('admin-ajax.php').'?action='.LAPOSTA_SIGNUP_BASIC_AJAX_ACTION);
        define('LAPOSTA_SIGNUP_BASIC_ASSETS_VERSION', LAPOSTA_SIGNUP_BASIC_VERSION);

    }

    public function init()
    {
        if (is_admin()) {
            add_action('admin_init', [$this, 'onAdminInitAction']);
            add_filter("plugin_action_links_{$this->pluginBaseName}", [$this, 'setPluginActionLinks']);
            add_action('admin_menu', [$this, 'addMenu']);
        }
        add_action('init', [$this, 'onInitAction']);
        add_action('plugins_loaded', [$this, 'onPluginsLoaded']);
        add_shortcode(self::SHORTCODE_RENDER_FORM, [$this->c->getFormController(), 'renderFormByShortcode']);
        $this->addAjaxRoutes();
    }

    public function onInitAction()
    {
        $enableLogger = apply_filters(self::FILTER_ENABLE_LOGGING, defined('WP_DEBUG') && WP_DEBUG);
        Logger::setIsEnabled($enableLogger);
    }

    public function onAdminInitAction()
    {
        register_setting(self::OPTION_GROUP, self::OPTION_API_KEY);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_TYPE);
        register_setting(self::OPTION_GROUP, self::OPTION_ADD_CLASSES);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_FORM);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_FORM_BODY);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_FIELD_WRAPPER);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_FIELD_HAS_ERROR);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_INPUT_HAS_ERROR);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_FIELD_ERROR_FEEDBACK);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_INPUT);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_LABEL);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_LABEL_NAME);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_LABEL_REQUIRED);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_SELECT);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_CHECKS_WRAPPER);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_CHECK_WRAPPER);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_CHECK_INPUT);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_CHECK_LABEL);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_SUBMIT_BUTTON_AND_LOADER_WRAPPER);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_SUBMIT_BUTTON);
        register_setting(self::OPTION_GROUP, self::OPTION_SUBMIT_BUTTON_TEXT);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_LOADER);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_GLOBAL_ERROR);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_SUCCESS_CONTAINER);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_SUCCESS_WRAPPER);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_SUCCESS_TITLE);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_SUCCESS_TEXT);
        register_setting(self::OPTION_GROUP, self::OPTION_SUCCESS_TITLE);
        register_setting(self::OPTION_GROUP, self::OPTION_SUCCESS_TEXT);
        register_setting(self::OPTION_GROUP, self::OPTION_INLINE_CSS);
    }

    public function onPluginsLoaded()
    {
        // Attempt to load the translation from the global languages directory.
        $loaded = load_plugin_textdomain('laposta-signup-basic');
        if ($loaded) {
            return;
        }

        // Fallback to bundled translations in the plugin's languages directory.
        $locale = determine_locale();
        $textDomain = 'laposta-signup-basic';
        $pathToLanguages = realpath(__DIR__.'/../languages');

        // Check if the current locale is a Dutch variant and attempt to load the bundled translation file.
        if (stripos($locale, 'nl_') === 0) {
            load_textdomain($textDomain, $pathToLanguages."/$textDomain-nl.mo");
        }
    }

    public function setPluginActionLinks($links) {
        $settingsLink = '<a href="options-general.php?page='.self::SLUG_SETTINGS.'">Settings</a>';
        array_unshift($links, $settingsLink);
        return $links;
    }

    public function addMenu()
    {
        $actualCapability = apply_filters(self::FILTER_SETTINGS_PAGE_CAPABILITY, self::DEFAULT_CAPABILITY);
        $actualCapability = is_string($actualCapability) ? $actualCapability : self::DEFAULT_CAPABILITY;

        add_options_page(
            'Laposta Signup Basic',
            'Laposta Signup Basic',
            $actualCapability,
            self::SLUG_SETTINGS,
            [$this->c->getSettingsController(), 'renderSettings']
        );
    }

    public function addAjaxRoutes()
    {
        add_action("wp_ajax_".LAPOSTA_SIGNUP_BASIC_AJAX_ACTION, [$this, 'handleAjaxRequest']);
        add_action("wp_ajax_nopriv_".LAPOSTA_SIGNUP_BASIC_AJAX_ACTION, [$this, 'handleAjaxRequest']);
    }

    public function handleAjaxRequest()
    {
        $route = isset($_GET['route']) ? sanitize_key($_GET['route']) : null;
        if (!$route) {
            die();
        }

        $actualCapability = apply_filters(self::FILTER_SETTINGS_PAGE_CAPABILITY, self::DEFAULT_CAPABILITY);
        $actualCapability = is_string($actualCapability) ? $actualCapability : self::DEFAULT_CAPABILITY;
        switch ($route) {
            case 'form_submit':
                return $this->c->getFormController()->ajaxFormPost();
            case 'settings_reset_cache':
                if (user_can(wp_get_current_user(), $actualCapability)) {
                    return $this->c->getSettingsController()->ajaxResetCache();
                }
                break;
        }

        RequestHelper::returnJson(['status' => 'error', 'message' => 'Route error'], 400);
    }

    /**
     * @return Container
     */
    public function getC(): Container
    {
        return $this->c;
    }

    /**
     * @return string
     */
    public function getRootDir(): string
    {
        return $this->rootDir;
    }

    /**
     * @return string
     */
    public function getRootUrl(): string
    {
        return $this->rootUrl;
    }

    /**
     * @return string
     */
    public function getPluginBaseName(): string
    {
        return $this->pluginBaseName;
    }

}
