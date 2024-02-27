<?php

namespace Laposta\SignupBasic\Controller;

use Laposta\SignupBasic\Container\Container;
use Laposta\SignupBasic\Plugin;
use Laposta\SignupBasic\Service\DataService;
use Laposta\SignupBasic\Service\Logger;
use Laposta\SignupBasic\Service\RequestHelper;

class FormController extends BaseController
{
    /**
     * @var Container
     */
    protected $c;

    const FIELD_NAME_HONEYPOT = 'email987123';
    const FIELD_NAME_NONCE = 'nonce';

    public function __construct(Container $container)
    {
        $this->c = $container;
    }

    public function renderFormByShortcode($atts = [])
    {
        $dataService = $this->c->getDataService();

        $classType = get_option(Plugin::OPTION_CLASS_TYPE);
        $globalErrorClass = esc_html(get_option(Plugin::OPTION_CLASS_GLOBAL_ERROR, ''));
        $inlineCss = esc_html(get_option(Plugin::OPTION_INLINE_CSS));
        $addDefaultStyling = $classType === DataService::CLASS_TYPE_DEFAULT;

        if (!$atts || !isset($atts['list_id'])) {
            $this->addAssets($addDefaultStyling, false);
            return $this->getRenderedTemplate('/form/form-error.php', [
                'inlineCss' => $inlineCss,
                'globalErrorClass' => $globalErrorClass,
                'errorMessage' => esc_html__('list_id is missing', 'laposta-signup-basic'),
            ]);
        }

        $listId = sanitize_text_field($atts['list_id']);
        $listFields = $dataService->getListFields($listId);
        if (isset($listFields['error'])) {
            $this->addAssets($addDefaultStyling, false);
            return $this->getRenderedTemplate('/form/form-error.php', [
                'inlineCss' => $inlineCss,
                'globalErrorClass' => $globalErrorClass,
                'errorMessage' => $listFields['error']['message'],
            ]);
        }

        $formClass = 'lsb-form';
        $fieldWrapperClass = 'lsb-form-field-wrapper';
        $labelClass = 'lsb-form-label';
        $inputClass = 'lsb-form-input';
        $selectClass = $inputClass;
        $checksWrapperClass = 'lsb-form-checks';
        $checkWrapperClass = 'lsb-form-check';
        $checkInputClass = 'lsb-form-check-input';
        $checkLabelClass = 'lsb-form-check-label';
        $submitButtonAndLoaderWrapperClass = 'lsb-form-button-and-loader-wrapper';
        $submitButtonClass = 'lsb-form-button';
        $loaderClass = 'lsb-loader';

        $bootstrapPreInlineCss = <<<EOL
.js-lsb-datepicker.form-control:disabled,
.js-lsb-datepicker.form-control[readonly] {
    background-color: inherit;
}
EOL;


        switch ($classType) {
            case DataService::CLASS_TYPE_BOOTSTRAP_V4:
                $inlineCss = $bootstrapPreInlineCss.$inlineCss;
                $formClass .= '';
                $fieldWrapperClass .= ' form-group';
                $labelClass .= '';
                $inputClass .= ' form-control';
                $selectClass .= ' form-control';
                $checksWrapperClass .= ' form-checks';
                $checkWrapperClass .= ' form-check';
                $checkInputClass .= ' form-check-input';
                $checkLabelClass .= ' form-check-label';
                $submitButtonClass .= ' btn btn-primary';
                $loaderClass .= ' spinner-border';
                break;
            case DataService::CLASS_TYPE_BOOTSTRAP_V5:
                $inlineCss = $bootstrapPreInlineCss.$inlineCss;
                $formClass .= '';
                $fieldWrapperClass .= ' mb-3';
                $labelClass .= ' form-label';
                $inputClass .= ' form-control';
                $selectClass .= ' form-select';
                $checksWrapperClass .= ' form-checks';
                $checkWrapperClass .= ' form-check';
                $checkInputClass .= ' form-check-input';
                $checkLabelClass .= ' form-check-label';
                $submitButtonClass .= ' btn btn-primary';
                $loaderClass .= ' spinner-border';
                break;
        }

        $addClasses = get_option(Plugin::OPTION_ADD_CLASSES, '') !== '0'; // if unset, load extra classes, best BC option
        if ($addClasses) {
            $formClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_FORM, ''));
            $fieldWrapperClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_FIELD_WRAPPER, ''));
            $labelClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_LABEL, ''));
            $inputClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_INPUT, ''));
            $selectClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_SELECT, ''));
            $checksWrapperClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_CHECKS_WRAPPER, ''));
            $checkWrapperClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_CHECK_WRAPPER, ''));
            $checkInputClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_CHECK_INPUT, ''));
            $checkLabelClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_CHECK_LABEL, ''));
            $submitButtonAndLoaderWrapperClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_SUBMIT_BUTTON_AND_LOADER_WRAPPER, ''));
            $submitButtonClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_SUBMIT_BUTTON, ''));
            $loaderClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_LOADER, ''));
        }

        $fieldValues = [];
        foreach ($listFields as $field) {
            $fieldValues[$field['key']] = null;
        }

        $hasDateFields = !!array_filter($listFields, function($field) {
            return $field['datatype'] === 'date';
        });

        $nonceAction = crc32($listId);
        $submitButtonText = trim(esc_html(get_option(Plugin::OPTION_SUBMIT_BUTTON_TEXT)));
        $submitButtonText = $submitButtonText ?: esc_html__('Subscribe', 'laposta-signup-basic');
        $submitButtonText = apply_filters(Plugin::FILTER_SUBMIT_BUTTON_TEXT, $submitButtonText, $listId, $atts);

        $locale = determine_locale();
        $lang = substr($locale, 0, 2);
        $datepickerLang = $this->getDatePickerLang($lang);
        $this->addAssets($addDefaultStyling, $hasDateFields, $datepickerLang);
        return $this->getRenderedTemplate('/form/form.php', [
            'listId' => $listId,
            'listFields' => $listFields,
            'formClass' => $formClass,
            'fieldWrapperClass' => $fieldWrapperClass,
            'labelClass' => $labelClass,
            'inputClass' => $inputClass,
            'selectClass' => $selectClass,
            'checksWrapperClass' => $checksWrapperClass,
            'checkWrapperClass' => $checkWrapperClass,
            'checkInputClass' => $checkInputClass,
            'checkLabelClass' => $checkLabelClass,
            'submitButtonAndLoaderWrapperClass' => $submitButtonAndLoaderWrapperClass,
            'submitButtonClass' => $submitButtonClass,
            'loaderClass' => $loaderClass,
            'inlineCss' => $inlineCss,
            'fieldValues' => $fieldValues,
            'hasDateFields' => $hasDateFields,
            'globalErrorClass' => $globalErrorClass,
            'submitButtonText' => $submitButtonText,
            'fieldNameHoneypot' => self::FIELD_NAME_HONEYPOT,
            'fieldNameNonce' => self::FIELD_NAME_NONCE,
            'nonce' => wp_create_nonce($nonceAction),
            'formPostUrl' => LAPOSTA_SIGNUP_BASIC_AJAX_URL.'&route=form_submit',
            'datepickerLang' => $datepickerLang,
        ]);
    }

    public function ajaxFormPost()
    {
        $dataService = $this->c->getDataService();

        $classType = get_option(Plugin::OPTION_CLASS_TYPE);
        $inlineCss = esc_html(get_option(Plugin::OPTION_INLINE_CSS));
        $addDefaultStyling = $classType === DataService::CLASS_TYPE_DEFAULT;
        $globalErrorMessage = esc_html__('Unknown error, please try again', 'laposta-signup-basic');

        $forms = $_POST['lsb'] ?? null;
        if (!$forms) {
            RequestHelper::returnJson([
                'status' => 'error',
                'html' => $globalErrorMessage,
            ]);
        }

        $listId = array_key_first($forms);
        $listId = sanitize_text_field($listId);
        $listFields = $dataService->getListFields($listId);
        if (isset($listFields['error'])) {
            RequestHelper::returnJson([
                'status' => 'error',
                'html' => $listFields['error']['message'],
            ]);
        }

        // sanitize form fields
        $submittedFieldValues = $this->sanitizeData(sanitize_post($forms[$listId]));

        // check validity for nonce and honeypot
        $nonceAction = crc32($listId);
        $validNonce = false !== wp_verify_nonce($submittedFieldValues[self::FIELD_NAME_NONCE], $nonceAction);
        $validHoneypot = !isset($submittedFieldValues[self::FIELD_NAME_HONEYPOT]) || !$submittedFieldValues[self::FIELD_NAME_HONEYPOT];
        if (!$validNonce || !$validHoneypot) {
            RequestHelper::returnJson([
                'status' => 'error',
                'html' => $globalErrorMessage,
            ]);
        }

        // keep the actual api form field values
        $fieldValues = [];
        foreach ($listFields as $field) {
            $fieldValues[$field['key']] = null;
        }
        $submittedFieldValues = array_intersect_key($submittedFieldValues, $fieldValues);

        try {
            $dataService->initLaposta();
            $member = new \Laposta_Member($listId);
            $result = $member->create(array(
                'ip' => $_SERVER['REMOTE_ADDR'],
                'email' => $submittedFieldValues['email'],
                'source_url' => $_SERVER['HTTP_REFERER'],
                'custom_fields' => $submittedFieldValues,
                'options' => [
                    'upsert' => true,
                ],
            ));


            $this->addAssets($addDefaultStyling, false);
            $successWrapperClass = esc_html(get_option(Plugin::OPTION_CLASS_SUCCESS_WRAPPER, ''));
            $successTitleClass = esc_html(get_option(Plugin::OPTION_CLASS_SUCCESS_TITLE, ''));
            $successTextClass = esc_html(get_option(Plugin::OPTION_CLASS_SUCCESS_TEXT, ''));
            $successTitle = trim(esc_html(get_option(Plugin::OPTION_SUCCESS_TITLE)));
            $successTitle = $successTitle ?: esc_html__('Successfully subscribed', 'laposta-signup-basic');
            $successText = trim(esc_html(get_option(Plugin::OPTION_SUCCESS_TEXT)));
            $successText = $successText ?: esc_html__('You have been successfully subscribed.', 'laposta-signup-basic');
            $successText = nl2br($successText);

            $successTitle = apply_filters(Plugin::FILTER_SUCCESS_TITLE, $successTitle, $listId, $submittedFieldValues);
            $successText = apply_filters(Plugin::FILTER_SUCCESS_TEXT, $successText, $listId, $submittedFieldValues);

            $html = $this->getRenderedTemplate('/form/form-success.php', [
                'inlineCss' => $inlineCss,
                'successWrapperClass' => $successWrapperClass,
                'successTitleClass' => $successTitleClass,
                'successTextClass' => $successTextClass,
                'successTitle' => $successTitle,
                'successText' => $successText,
            ]);
            RequestHelper::returnJson([
                'status' => 'success',
                'html' => $html,
            ]);
        }
        catch (\Laposta_Error $e) {
            $error = $e->json_body['error'];
            $globalError = $e->getMessage();
            if ($error['type'] === 'invalid_input') {
                $errorId = $error['id'] ?? null;
                $fields = array_filter($listFields, function($field) use ($errorId) {
                    return $field['field_id'] === $errorId;
                });
                if ($fields) {
                    $field = reset($fields);
                    $fieldName = $field['name'];
                    $globalError = esc_html__("Something went wrong. Please check the field '%s' and try again.", 'laposta-signup-basic');
                    $globalError = sprintf($globalError, $fieldName);
                }
            } else {
                $globalError = $e->getMessage();
                Logger::logError('FormController::ajaxFormPost, unknown Laposta_Error by submitting form through the API', $e);
            }
            RequestHelper::returnJson([
                'status' => 'error',
                'html' => $globalError
            ]);
        }
        catch (\Throwable $e) {
            Logger::logError('FormController::ajaxFormPost, caught Throwable by submitting form through the API', $e);
            RequestHelper::returnJson([
                'status' => 'error',
                'html' => $globalErrorMessage
            ]);
        }
    }

    public function addAssets(bool $addDefaultStyling, bool $addDatepickerAssets, ?array $datepickerLang = null)
    {
        if ($addDefaultStyling) {
            wp_enqueue_style('laposta-signup-basic.lsb-form', LAPOSTA_SIGNUP_BASIC_ASSETS_URL.'/css/lsb-form.css', [], LAPOSTA_SIGNUP_BASIC_ASSETS_VERSION);
        }

        wp_enqueue_script('laposta-signup-basic.lsb-form.main', LAPOSTA_SIGNUP_BASIC_ASSETS_URL.'/js/lsb-form/main.js', ['jquery'], LAPOSTA_SIGNUP_BASIC_ASSETS_VERSION);

        if ($addDatepickerAssets) {
            wp_enqueue_style('flatpickr', LAPOSTA_SIGNUP_BASIC_ASSETS_URL.'/flatpickr4.6.13/flatpickr.min.css');
            wp_enqueue_script('flatpickr', LAPOSTA_SIGNUP_BASIC_ASSETS_URL.'/flatpickr4.6.13/flatpickr.min.js');
            if ($datepickerLang) {
                $fileLang = $datepickerLang['file_lang'] ?? $datepickerLang['lang'];
                wp_enqueue_script('flatpickr_l10n_'.$fileLang, LAPOSTA_SIGNUP_BASIC_ASSETS_URL."/flatpickr4.6.13/l10n/$fileLang.js");
            }
        }

        // JS translations
        $translations = [
            'global.unknown_error' => __('Unknown error, please try again', 'laposta-signup-basic'),
        ];

        wp_localize_script('laposta-signup-basic.lsb-form.main', 'lsbTranslations', $translations);
    }

    public function getTemplateDir()
    {
        return LAPOSTA_SIGNUP_BASIC_TEMPLATE_DIR;
    }

    protected function getDatePickerLang(string $lang): ?array
    {
        $langs = [
            'ar' => ['lang' => 'ar', 'format_long' => 'j F Y', 'format_short' => 'd/m/Y'], // Arabic
            'at' => ['lang' => 'at', 'format_long' => 'd. M Y', 'format_short' => 'd.m.Y'], // Austrian German
            'az' => ['lang' => 'az', 'format_long' => 'j F Y', 'format_short' => 'd.m.Y'], // Azerbaijani
            'be' => ['lang' => 'be', 'format_long' => 'j F Y', 'format_short' => 'd.m.Y'], // Belarusian
            'bg' => ['lang' => 'bg', 'format_long' => 'j F Y', 'format_short' => 'd.m.Y'], // Bulgarian
            'bn' => ['lang' => 'bn', 'format_long' => 'j F, Y', 'format_short' => 'd/m/Y'], // Bengali
            'cat' => ['lang' => 'cat', 'format_long' => 'j F Y', 'format_short' => 'd/m/Y'], // Catalan
            'cs' => ['lang' => 'cs', 'format_long' => 'j. F Y', 'format_short' => 'd.m.Y'], // Czech
            'cy' => ['lang' => 'cy', 'format_long' => 'j F Y', 'format_short' => 'd/m/Y'], // Welsh
            'da' => ['lang' => 'da', 'format_long' => 'j. F Y', 'format_short' => 'd/m-Y'], // Danish
            'de' => ['lang' => 'de', 'format_long' => 'j. F Y', 'format_short' => 'd.m.Y'], // German
            'en' => ['lang' => 'en', 'format_long' => 'F j, Y', 'format_short' => 'm/d/Y', 'file_lang' => 'default'], // English
            'eo' => ['lang' => 'eo', 'format_long' => null, 'format_short' => null], // Esperanto
            'es' => ['lang' => 'es', 'format_long' => 'j de F de Y', 'format_short' => 'd/m/Y'], // Spanish
            'et' => ['lang' => 'et', 'format_long' => 'j. F Y', 'format_short' => 'd.m.Y'], // Estonian
            'fa' => ['lang' => 'fa', 'format_long' => 'j F Y', 'format_short' => 'Y/m/d'], // Persian
            'fi' => ['lang' => 'fi', 'format_long' => 'j. F Y', 'format_short' => 'd.m.Y'], // Finnish
            'fo' => ['lang' => 'fo', 'format_long' => null, 'format_short' => null], // Faroese
            'fr' => ['lang' => 'fr', 'format_long' => 'j F Y', 'format_short' => 'd/m/Y'], // French
            'gr' => ['lang' => 'gr', 'format_long' => 'd F Y', 'format_short' => 'd-m-Y'], // Greek
            'el' => ['lang' => 'el', 'format_long' => 'd F Y', 'format_short' => 'd-m-Y', 'file_lang' => 'gr'], // Greek
            'he' => ['lang' => 'he', 'format_long' => 'j F Y', 'format_short' => 'd/m/Y'], // Hebrew
            'hi' => ['lang' => 'hi', 'format_long' => null, 'format_short' => null], // Hindi
            'hr' => ['lang' => 'hr', 'format_long' => 'j. F Y', 'format_short' => 'd.m.Y'], // Croatian
            'hu' => ['lang' => 'hu', 'format_long' => 'Y. F j.', 'format_short' => 'Y.m.d.'], // Hungarian
            'id' => ['lang' => 'id', 'format_long' => 'j F Y', 'format_short' => 'd/m/Y'], // Indonesian
            'is' => ['lang' => 'is', 'format_long' => 'j. F Y', 'format_short' => 'd.m.Y'], // Icelandic
            'it' => ['lang' => 'it', 'format_long' => 'j F Y', 'format_short' => 'd/m/Y'], // Italian
            'ja' => ['lang' => 'ja', 'format_long' => null, 'format_short' => 'Y/m/d'], // Japanese
            'ka' => ['lang' => 'ka', 'format_long' => 'j F Y', 'format_short' => null], // Georgian
            'ko' => ['lang' => 'ko', 'format_long' => null, 'format_short' => 'Y-m-d'], // Korean
            'km' => ['lang' => 'km', 'format_long' => null, 'format_short' => 'd/m/Y'], // Khmer
            'kz' => ['lang' => 'kz', 'format_long' => null, 'format_short' => null], // Kazakh
            'lt' => ['lang' => 'lt', 'format_long' => 'Y m. F d.', 'format_short' => 'Y-m-d'], // Lithuanian
            'lv' => ['lang' => 'lv', 'format_long' => 'Y. gada j. F', 'format_short' => 'd.m.Y'], // Latvian
            'mk' => ['lang' => 'mk', 'format_long' => null, 'format_short' => null], // Macedonian
            'mn' => ['lang' => 'mn', 'format_long' => 'Y оны Fын j', 'format_short' => 'Y/m/d'], // Mongolian
            'ms' => ['lang' => 'ms', 'format_long' => 'j F Y', 'format_short' => 'd/m/Y'], // Malay
            'my' => ['lang' => 'my', 'format_long' => null, 'format_short' => 'd/m/Y'], // Burmese
            'nl' => ['lang' => 'nl', 'format_long' => 'j F Y', 'format_short' => 'd-m-Y'], // Dutch
            'no' => ['lang' => 'no', 'format_long' => 'j. F Y', 'format_short' => 'd.m.Y'], // Norwegian
            'pa' => ['lang' => 'pa', 'format_long' => 'j F Y', 'format_short' => 'd/m/Y'], // Punjabi
            'pl' => ['lang' => 'pl', 'format_long' => 'j F Y', 'format_short' => 'd.m.Y'], // Polish
            'pt' => ['lang' => 'pt', 'format_long' => 'j de F de Y', 'format_short' => 'd/m/Y'], // Portuguese
            'ro' => ['lang' => 'ro', 'format_long' => 'j F Y', 'format_short' => 'd.m.Y'], // Romanian
            'ru' => ['lang' => 'ru', 'format_long' => 'j F Y', 'format_short' => 'd.m.Y'], // Russian
            'si' => ['lang' => 'si', 'format_long' => 'Y F j', 'format_short' => null], // Sinhala
            'sk' => ['lang' => 'sk', 'format_long' => 'j. F Y', 'format_short' => 'd.m.Y'], // Slovak
            'sl' => ['lang' => 'sl', 'format_long' => 'j. F Y', 'format_short' => 'd.m.Y'], // Slovenian
            'sq' => ['lang' => 'sq', 'format_long' => 'j F Y', 'format_short' => 'd/m/Y'], // Albanian
            'sr' => ['lang' => 'sr', 'format_long' => 'j. F Y', 'format_short' => 'd.m.Y'], // Serbian
            'sv' => ['lang' => 'sv', 'format_long' => 'j F Y', 'format_short' => 'Y-m-d'], // Swedish
            'th' => ['lang' => 'th', 'format_long' => 'j F Y', 'format_short' => 'd/m/Y'], // Thai
            'tj' => ['lang' => 'tj', 'format_long' => 'j F Y', 'format_short' => 'd.m.Y'], // Tajik
            'tr' => ['lang' => 'tr', 'format_long' => 'j F Y', 'format_short' => 'd.m.Y'], // Turkish
            'uk' => ['lang' => 'uk', 'format_long' => 'j F Y', 'format_short' => 'd.m.Y'], // Ukrainian
            'uz' => ['lang' => 'uz', 'format_long' => 'j F Y', 'format_short' => 'd/m/Y'], // Uzbek
            'vn' => ['lang' => 'vn', 'format_long' => null, 'format_short' => null], // Vietnamese
            'zh' => ['lang' => 'zh', 'format_long' => null, 'format_short' => 'Y/m/d'], // Chinese Simplified
            'zh-tw' => ['lang' => 'zh-tw', 'format_long' => null, 'format_short' => 'Y/m/d'], // Chinese Traditional
        ];

        return $langs[$lang] ?? null;
    }
}