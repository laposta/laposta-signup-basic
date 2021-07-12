<?php

namespace Laposta\SignupBasic;

use Laposta\SignupBasic\Container\Container;
use Laposta\SignupBasic\Service\RequestHelper;

class Plugin
{

    /**
     * @var Container
     */
    protected $c;

    const SHORTCODE_RENDER_FORM = 'laposta_signup_basic_form';
    const SLUG_SETTINGS = 'laposta_signup_basic_settings';

    const TRANSIENT_LISTS = 'laposta_lists';
    const TRANSIENT_STATUS = 'laposta_status';
    const TRANSIENT_LIST_FIELDS_PREFIX = 'laposta_list_fields_';

    const OPTION_GROUP = 'laposta_signup_basic';
    const OPTION_API_KEY = 'laposta-api_key';
    const OPTION_CLASS_TYPE = 'laposta_signup_basic_class_type'; // bootstrap v4, v5 or custom
    const OPTION_CLASS_FORM = 'laposta_signup_basic_class_form';
    const OPTION_CLASS_FIELD_WRAPPER = 'laposta_signup_basic_class_field_wrapper';
    const OPTION_CLASS_INPUT = 'laposta_signup_basic_class_input';
    const OPTION_CLASS_LABEL = 'laposta_signup_basic_class_label';
    const OPTION_CLASS_SELECT = 'laposta_signup_basic_class_select';
    const OPTION_CLASS_CHECKS_WRAPPER = 'laposta_signup_basic_class_checks_wrapper';
    const OPTION_CLASS_CHECK_WRAPPER = 'laposta_signup_basic_class_check_wrapper';
    const OPTION_CLASS_CHECK_INPUT = 'laposta_signup_basic_class_check_input';
    const OPTION_CLASS_CHECK_LABEL = 'laposta_signup_basic_class_check_label';
    const OPTION_CLASS_SUBMIT_BUTTON = 'laposta_signup_basic_class_submit_button';
    const OPTION_SUBMIT_BUTTON_TEXT = 'laposta_signup_basic_submit_button_text';
    const OPTION_CLASS_GLOBAL_ERROR = 'laposta_signup_basic_class_global_error';
    const OPTION_CLASS_SUCCESS_WRAPPER = 'laposta_signup_basic_class_success_wrapper';
    const OPTION_CLASS_SUCCESS_TITLE = 'laposta_signup_basic_class_success_title';
    const OPTION_CLASS_SUCCESS_TEXT = 'laposta_signup_basic_class_success_text';
    const OPTION_SUCCESS_TITLE = 'laposta_signup_basic_success_title';
    const OPTION_SUCCESS_TEXT = 'laposta_signup_basic_success_text';
    const OPTION_INLINE_CSS = 'laposta_signup_basic_inline_css';

    const DEFAULT_CAPABILITY = 'manage_options';
    const FILTER_SETTINGS_PAGE_CAPABILITY = 'laposta_signup_basic_settings_page_capability';

    public function __construct(Container $container)
    {
        $this->c = $container;

        $this->defineConstants();
        $this->init();
    }

    protected function defineConstants()
    {
        $rootDir = realpath(__DIR__.'/..');
        $rootUrl = plugin_dir_url($rootDir.'/laposta-signup-basic.php');

        define('LAPOSTA_SIGNUP_BASIC_ROOT_DIR', $rootDir);
        define('LAPOSTA_SIGNUP_BASIC_TEMPLATE_DIR', $rootDir.DIRECTORY_SEPARATOR.'templates');
        define('LAPOSTA_SIGNUP_BASIC_ASSETS_URL', $rootUrl.'assets');
        define('LAPOSTA_SIGNUP_BASIC_AJAX_ACTION', 'laposta_signup_basic_ajax');
        define('LAPOSTA_SIGNUP_BASIC_AJAX_URL', admin_url('admin-ajax.php').'?action='.LAPOSTA_SIGNUP_BASIC_AJAX_ACTION);
        define('LAPOSTA_SIGNUP_BASIC_ASSETS_VERSION', LAPOSTA_SIGNUP_BASIC_VERSION);

    }

    public function init()
    {
        if (is_admin()) {
            add_action('admin_init', array($this, 'adminInit'));
            add_action('admin_menu', array($this, 'addMenu'));
        }

        add_shortcode(self::SHORTCODE_RENDER_FORM, [$this->c->getFormController(), 'renderFormByShortcode']);
        $this->addAjaxRoutes();
    }

    public function adminInit()
    {
        register_setting(self::OPTION_GROUP, self::OPTION_API_KEY);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_TYPE);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_FORM);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_FIELD_WRAPPER);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_INPUT);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_LABEL);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_SELECT);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_CHECKS_WRAPPER);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_CHECK_WRAPPER);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_CHECK_INPUT);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_CHECK_LABEL);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_SUBMIT_BUTTON);
        register_setting(self::OPTION_GROUP, self::OPTION_SUBMIT_BUTTON_TEXT);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_GLOBAL_ERROR);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_SUCCESS_WRAPPER);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_SUCCESS_TITLE);
        register_setting(self::OPTION_GROUP, self::OPTION_CLASS_SUCCESS_TEXT);
        register_setting(self::OPTION_GROUP, self::OPTION_SUCCESS_TITLE);
        register_setting(self::OPTION_GROUP, self::OPTION_SUCCESS_TEXT);
        register_setting(self::OPTION_GROUP, self::OPTION_INLINE_CSS);
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
            case 'settings_reset_cache':
                if (user_can(wp_get_current_user(), $actualCapability)) {
                    return $this->c->getSettingsController()->ajaxResetCache();
                }
                break;
        }

        RequestHelper::returnJson(['status' => 'error', 'message' => 'Route error'], 400);
    }


}
