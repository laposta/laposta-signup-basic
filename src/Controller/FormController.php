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

		$formClass = 'lsb-form';
		$formBodyClass = 'lsb-form-body';
		$fieldWrapperClass = 'lsb-form-field-wrapper';
		$fieldHasErrorClass = 'lsb-form-field-has-error';
		$inputHasErrorClass = 'lsb-form-input-has-error';
		$fieldErrorFeedbackClass = 'lsb-form-field-error-feedback';
		$labelClass = 'lsb-form-label';
		$labelNameClass = 'lsb-form-label-name';
		$labelRequiredClass = 'lsb-form-label-required';
		$inputClass = 'lsb-form-input';
		$selectClass = $inputClass;
		$checksWrapperClass = 'lsb-form-checks';
		$checkWrapperClass = 'lsb-form-check';
		$checkInputClass = 'lsb-form-check-input';
		$checkLabelClass = 'lsb-form-check-label';
		$submitButtonAndLoaderWrapperClass = 'lsb-form-button-and-loader-wrapper';
		$submitButtonClass = 'lsb-form-button';
		$loaderClass = 'lsb-loader';
		$globalErrorClass = 'lsb-form-global-error';
		$successContainerClass = 'lsb-form-success-container';
		$addClasses = get_option(Plugin::OPTION_ADD_CLASSES, '') !== '0'; // if unset, load extra classes, best BC option
		if ($addClasses) {
			$formClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_FORM, ''));
			$formBodyClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_FORM_BODY, ''));
			$fieldWrapperClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_FIELD_WRAPPER, ''));
			$fieldHasErrorClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_FIELD_HAS_ERROR, ''));
			$inputHasErrorClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_INPUT_HAS_ERROR, ''));
			$fieldErrorFeedbackClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_FIELD_ERROR_FEEDBACK, ''));
			$labelClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_LABEL, ''));
			$labelNameClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_LABEL_NAME, ''));
			$labelRequiredClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_LABEL_REQUIRED, ''));
			$inputClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_INPUT, ''));
			$selectClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_SELECT, ''));
			$checksWrapperClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_CHECKS_WRAPPER, ''));
			$checkWrapperClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_CHECK_WRAPPER, ''));
			$checkInputClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_CHECK_INPUT, ''));
			$checkLabelClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_CHECK_LABEL, ''));
			$submitButtonAndLoaderWrapperClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_SUBMIT_BUTTON_AND_LOADER_WRAPPER, ''));
			$submitButtonClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_SUBMIT_BUTTON, ''));
			$loaderClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_LOADER, ''));
			$globalErrorClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_GLOBAL_ERROR, ''));
			$successContainerClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_SUCCESS_CONTAINER, ''));
		}

        $inlineCss = esc_html(get_option(Plugin::OPTION_INLINE_CSS));
		$classType = get_option(Plugin::OPTION_CLASS_TYPE);
        $addDefaultStyling = $classType === DataService::CLASS_TYPE_DEFAULT;

        if (!$atts || !isset($atts['list_id'])) {
            $this->addAssets($addDefaultStyling);
            return $this->getRenderedTemplate('/form/form-error.php', [
                'inlineCss' => $inlineCss,
                'globalErrorClass' => $globalErrorClass,
                'errorMessage' => esc_html__('list_id is missing', 'laposta-signup-basic'),
            ]);
        }

        $listId = sanitize_text_field($atts['list_id']);
        $listFields = $dataService->getListFields($listId);
        if (isset($listFields['error'])) {
            $this->addAssets($addDefaultStyling);
            return $this->getRenderedTemplate('/form/form-error.php', [
                'inlineCss' => $inlineCss,
                'globalErrorClass' => $globalErrorClass,
                'errorMessage' => $listFields['error']['message'],
            ]);
        }

        switch ($classType) {
            case DataService::CLASS_TYPE_BOOTSTRAP_V4:
				$globalErrorClass .= ' alert alert-danger';
				$formClass .= '';
				$fieldWrapperClass .= ' form-group';
				$fieldHasErrorClass .= '';
				$inputHasErrorClass .= ' is-invalid';
				$fieldErrorFeedbackClass .= ' invalid-feedback';
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
				$globalErrorClass .= ' alert alert-danger';
				$formClass .= '';
                $fieldWrapperClass .= ' mb-3';
				$fieldHasErrorClass .= '';
				$inputHasErrorClass .= ' is-invalid';
				$fieldErrorFeedbackClass .= ' invalid-feedback';
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

        $fieldValues = [];
        foreach ($listFields as $field) {
            $fieldValues[$field['key']] = null;
        }

        $nonceAction = $this->createNonceAction($listId);
        $submitButtonText = trim(esc_html(get_option(Plugin::OPTION_SUBMIT_BUTTON_TEXT)));
        $submitButtonText = $submitButtonText ?: esc_html__('Subscribe', 'laposta-signup-basic');
        $submitButtonText = apply_filters(Plugin::FILTER_SUBMIT_BUTTON_TEXT, $submitButtonText, $listId, $atts);

        $this->addAssets($addDefaultStyling, compact('fieldHasErrorClass', 'inputHasErrorClass'));
        return $this->getRenderedTemplate('/form/form.php', [
            'listId' => $listId,
            'listFields' => $listFields,
            'formClass' => $formClass,
            'formBodyClass' => $formBodyClass,
            'fieldWrapperClass' => $fieldWrapperClass,
            'fieldErrorFeedbackClass' => $fieldErrorFeedbackClass,
            'labelClass' => $labelClass,
            'labelNameClass' => $labelNameClass,
            'labelRequiredClass' => $labelRequiredClass,
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
            'globalErrorClass' => $globalErrorClass,
            'successContainerClass' => $successContainerClass,
            'submitButtonText' => $submitButtonText,
            'fieldNameHoneypot' => self::FIELD_NAME_HONEYPOT,
            'fieldNameNonce' => self::FIELD_NAME_NONCE,
            'nonce' => wp_create_nonce($nonceAction),
            'formPostUrl' => LAPOSTA_SIGNUP_BASIC_AJAX_URL.'&route=form_submit',
        ]);
    }

    public function ajaxFormPost()
    {
        $dataService = $this->c->getDataService();
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

        // check honeypot
        $validHoneypot = !isset($submittedFieldValues[self::FIELD_NAME_HONEYPOT]) || !$submittedFieldValues[self::FIELD_NAME_HONEYPOT];
        if (!$validHoneypot) {
            RequestHelper::returnJson([
                'status' => 'error',
                'html' => $globalErrorMessage,
            ]);
        }

        // check nonce
        $nonceAction = $this->createNonceAction($listId);
        $validNonce = false !== wp_verify_nonce($submittedFieldValues[self::FIELD_NAME_NONCE], $nonceAction);
        if (!$validNonce) {
            RequestHelper::returnJson([
                'status' => 'invalid_nonce',
                'nonce' => wp_create_nonce($nonceAction),
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
                'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
                'email' => $submittedFieldValues['email'],
                'source_url' => $_SERVER['HTTP_REFERER'] ?? null,
                'custom_fields' => $submittedFieldValues,
                'options' => [
                    'upsert' => true,
                ],
            ));

			$successWrapperClass = 'lsb-success';
			$successTitleClass = 'lsb-success-title';
			$successTextClass = 'lsb-success-text';
			$addClasses = get_option(Plugin::OPTION_ADD_CLASSES, '') !== '0'; // if unset, load extra classes, best BC option
			if ($addClasses) {
                $successWrapperClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_SUCCESS_WRAPPER, ''));
                $successTitleClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_SUCCESS_TITLE, ''));
                $successTextClass .= ' '.esc_html(get_option(Plugin::OPTION_CLASS_SUCCESS_TEXT, ''));
			}

            $successTitle = trim(esc_html(get_option(Plugin::OPTION_SUCCESS_TITLE)));
            $successTitle = $successTitle ?: esc_html__('Successfully subscribed', 'laposta-signup-basic');
            $successText = trim(esc_html(get_option(Plugin::OPTION_SUCCESS_TEXT)));
            $successText = $successText ?: esc_html__('You have been successfully subscribed.', 'laposta-signup-basic');
            $successText = nl2br($successText);

            $successTitle = apply_filters(Plugin::FILTER_SUCCESS_TITLE, $successTitle, $listId, $submittedFieldValues);
            $successText = apply_filters(Plugin::FILTER_SUCCESS_TEXT, $successText, $listId, $submittedFieldValues);

            $html = $this->getRenderedTemplate('/form/form-success.php', [
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

    public function addAssets(bool $addDefaultStyling, array $jsClasses = [])
    {
		wp_enqueue_style('laposta-signup-basic.lsb-form-always', LAPOSTA_SIGNUP_BASIC_ASSETS_URL.'/css/lsb-form-always.css', [], LAPOSTA_SIGNUP_BASIC_ASSETS_VERSION);
        if ($addDefaultStyling) {
            wp_enqueue_style('laposta-signup-basic.lsb-form-default', LAPOSTA_SIGNUP_BASIC_ASSETS_URL.'/css/lsb-form-default.css', [], LAPOSTA_SIGNUP_BASIC_ASSETS_VERSION);
        }

        wp_enqueue_script('laposta-signup-basic.lsb-form.main', LAPOSTA_SIGNUP_BASIC_ASSETS_URL.'/js/lsb-form/main.js', ['jquery'], LAPOSTA_SIGNUP_BASIC_ASSETS_VERSION);

        // JS config
        $jsConfig = [
			'trans' => [
				'global.unknown_error' => esc_html__('Unknown error, please try again', 'laposta-signup-basic'),
				'global.form_contains_errors' => esc_html__('There are errors in the form. Please review and correct the fields with error messages.', 'laposta-signup-basic'),
				'global.loading' => esc_html__('The form is being submitted. Please wait while we process your request.', 'laposta-signup-basic'),
				'field.error.required' => esc_html__("Please provide a value for '%field_name%'", 'laposta-signup-basic'),
				'field.error.required.email' => esc_html__('Please provide an email address', 'laposta-signup-basic'),
				'field.error.required.date' => esc_html__('Please provide a date', 'laposta-signup-basic'),
				'field.error.required.number' => esc_html__('Please provide a number', 'laposta-signup-basic'),
				'field.error.required.radio' => esc_html__('Please choose an option', 'laposta-signup-basic'),
				'field.error.required.checkbox' => esc_html__('Please select at least one option', 'laposta-signup-basic'),
				'field.error.invalid.email' => esc_html__('Please provide a valid email address, e.g., example@domain.com', 'laposta-signup-basic'),
				'field.error.invalid.date' => esc_html__('Please provide a valid date', 'laposta-signup-basic'),
				'field.error.invalid.number' => esc_html__('Please provide a valid number', 'laposta-signup-basic'),
			],
			'class' => $jsClasses
        ];

        wp_localize_script('laposta-signup-basic.lsb-form.main', 'lsbConfig', $jsConfig);
    }

    public function getTemplateDir()
    {
        return LAPOSTA_SIGNUP_BASIC_TEMPLATE_DIR;
    }

    protected function createNonceAction(string $listId): string
    {
        return crc32($listId);
    }
}