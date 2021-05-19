<?php

namespace Laposta\SignupBasic\Controller;

use Laposta\SignupBasic\Container\Container;
use Laposta\SignupBasic\Plugin;
use Laposta\SignupBasic\Service\DataService;

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
                'errorMessage' => 'list_id ontbreekt',
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
        $submitButtonClass = 'lsb-form-button';
        $bootstrapPreInlineCss = <<<EOL
.js-lsb-datepicker.form-control:disabled,
.js-lsb-datepicker.form-control[readonly] {
    background-color: inherit;
}

EOL;


        switch ($classType) {
            case DataService::CLASS_TYPE_CUSTOM:
                $formClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_FORM, ''));
                $fieldWrapperClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_FIELD_WRAPPER, ''));
                $labelClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_LABEL, ''));
                $inputClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_INPUT, ''));
                $selectClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_SELECT, ''));
                $checksWrapperClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_CHECKS_WRAPPER, ''));
                $checkWrapperClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_CHECK_WRAPPER, ''));
                $checkInputClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_CHECK_INPUT, ''));
                $checkLabelClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_CHECK_LABEL, ''));
                $submitButtonClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_SUBMIT_BUTTON, ''));
                break;
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
                break;
        }

        // set all field values to null, but overwrite with form post
        $fieldValues = [];
        foreach ($listFields as $field) {
            $fieldValues[$field['key']] = null;
        }

        $hasDateFields = !!array_filter($listFields, function($field) {
            return $field['datatype'] === 'date';
        });

        $nonceAction = crc32($listId);
        $hasErrors = false;
        $globalError = null;
        $submittedListId = isset($_POST['lsb_form_submit']) ? sanitize_text_field($_POST['lsb_form_submit']) : null;
        $formPosted = $submittedListId === $listId; // multiple forms can be included on the same page
        if ($formPosted) {
            // sanitize form fields
            $submittedFieldValues = $this->sanitizeData(sanitize_post($_POST['lsb'][$listId]));

            // check validity for nonce and honeypot
            $validNonce = false !== wp_verify_nonce($submittedFieldValues[self::FIELD_NAME_NONCE], $nonceAction);
            $validHoneypot = !isset($submittedFieldValues[self::FIELD_NAME_HONEYPOT]) || !$submittedFieldValues[self::FIELD_NAME_HONEYPOT];
            if (!$validNonce || !$validHoneypot) {
                $hasErrors = true;
                $globalError = 'Onbekende fout, probeer het nog eens';
            }

            // keep the actual api form field values
            $submittedFieldValues = array_intersect_key($submittedFieldValues, $fieldValues);

            // set field values to match the submitted values
            foreach ($fieldValues as $key => $fieldValue) {
                $fieldValues[$key] = isset($submittedFieldValues[$key]) ? $submittedFieldValues[$key] : $fieldValue;
            }
        }

        if ($formPosted && !$hasErrors) {
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
                $successTitle = $successTitle ?: 'Succesvol aangemeld';
                $successText = trim(esc_html(get_option(Plugin::OPTION_SUCCESS_TEXT)));
                $successText = $successText ?: 'Het aanmelden is gelukt.';
                $successText = nl2br($successText);
                return $this->getRenderedTemplate('/form/form-success.php', [
                    'inlineCss' => $inlineCss,
                    'successWrapperClass' => $successWrapperClass,
                    'successTitleClass' => $successTitleClass,
                    'successTextClass' => $successTextClass,
                    'successTitle' => $successTitle,
                    'successText' => $successText,
                ]);
            }
            catch (\Laposta_Error $e) {
                $error = $e->json_body['error'];
                $globalError = $e->getMessage();
                if ($error['type'] === 'invalid_input') {
                    $fields = array_filter($listFields, function($field) use ($error) {
                        return $field['field_id'] === $error['id'];
                    });
                    if ($fields) {
                        $field = reset($fields);
                        $fieldName = $field['name'];
                        $globalError = "Er ging iets mis. Controleer het veld '{$fieldName}' en probeer het nog eens.";
                    }
                } else {
                    $globalError = $e->getMessage();
                }
            }
            catch (\Throwable $e) {
                $hasErrors = true;
                $globalError = $e->getMessage();
            }
        }

        $submitButtonText = trim(esc_html(get_option(Plugin::OPTION_SUBMIT_BUTTON_TEXT)));
        $submitButtonText = $submitButtonText ?: 'Aanmelden';

        $this->addAssets($addDefaultStyling, $hasDateFields);
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
            'submitButtonClass' => $submitButtonClass,
            'inlineCss' => $inlineCss,
            'fieldValues' => $fieldValues,
            'hasDateFields' => $hasDateFields,
            'hasErrors' => $hasErrors,
            'globalError' => $globalError,
            'submitButtonText' => $submitButtonText,
            'fieldNameHoneypot' => self::FIELD_NAME_HONEYPOT,
            'fieldNameNonce' => self::FIELD_NAME_NONCE,
            'nonce' => wp_create_nonce($nonceAction),
        ]);
    }

    public function addAssets(bool $addDefaultStyling, bool $addDatepickerAssets)
    {
        if ($addDefaultStyling) {
            wp_enqueue_style('laposta-signup-basic.lsb-form', LAPOSTA_SIGNUP_BASIC_ASSETS_URL.'/css/lsb-form.css', [], LAPOSTA_SIGNUP_BASIC_ASSETS_VERSION);
        }

        if ($addDatepickerAssets) {
            wp_enqueue_style('flatpickr', LAPOSTA_SIGNUP_BASIC_ASSETS_URL.'/flatpickr4.6.9/flatpickr.min.css', [], '4.6.9');
            wp_enqueue_script('flatpickr', LAPOSTA_SIGNUP_BASIC_ASSETS_URL.'/flatpickr4.6.9/flatpickr.min.js', [], '4.6.9');
            wp_enqueue_script('flatpickr_l10n_nl', LAPOSTA_SIGNUP_BASIC_ASSETS_URL.'/flatpickr4.6.9/l10n/nl.js', [], '4.6.9');
        }
    }

    public function getTemplateDir()
    {
        return LAPOSTA_SIGNUP_BASIC_TEMPLATE_DIR;
    }
}