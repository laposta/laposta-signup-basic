<?php
/**
 * @var string $listId (valid id)
 * @var array $listFields
 * @var string $formClass (sanitized)
 * @var string $formBodyClass (sanitized)
 * @var string $fieldWrapperClass (sanitized)
 * @var string $labelClass (sanitized)
 * @var string $labelNameClass (sanitized)
 * @var string $labelRequiredClass (sanitized)
 * @var string $inputClass (sanitized)
 * @var string $selectClass (sanitized)
 * @var string $checksWrapperClass (sanitized)
 * @var string $checkWrapperClass (sanitized)
 * @var string $checkInputClass (sanitized)
 * @var string $checkLabelClass (sanitized)
 * @var string $fieldErrorFeedbackClass (sanitized)
 * @var string $submitButtonAndLoaderWrapperClass (sanitized)
 * @var string $submitButtonClass (sanitized)
 * @var string $submitButtonText (sanitized)
 * @var string $loaderClass (sanitized)
 * @var string $inlineCss (sanitized)
 * @var array $fieldValues (sanitized)
 * @var string $globalErrorClass (sanitized)
 * @var string $successContainerClass (sanitized)
 * @var string $fieldNameHoneypot
 * @var string $fieldNameNonce
 * @var string $nonce
 * @var string $formPostUrl
 */

$visualHiddenClass = 'lsb-visually-hidden';

use Laposta\SignupBasic\Plugin;

?>

<?php if ($inlineCss): ?>
    <style>
        <?php echo $inlineCss ?>
    </style>
<?php endif ?>

<form class="<?php echo $formClass ?> lsb-list-id-<?php echo $listId ?> js-lsb-form"
      method="post"
      data-form-post-url="<?php echo $formPostUrl ?>"
      role="form"
      novalidate
>
    <div class="<?php echo $formBodyClass ?>">
        <?php foreach ($listFields as $field): ?>
            <?php
                $fieldType = $field['datatype'];
                if ($field['is_email']) {
                    $fieldType = 'email';
                }
                elseif ($fieldType === 'select_single') {
                    $fieldType = $field['datatype_display'] === 'radio' ? 'radio' : 'select';
                }
                elseif ($fieldType === 'select_multiple') {
                    $fieldType = 'checkbox';
                }
                elseif ($fieldType === 'numeric') {
                    $fieldType = 'number';
                }
                $fieldId = esc_attr($field['field_id']);
                $fieldKey = esc_attr($field['key']);
                $uniqueFieldKey = $listId.$fieldId;
                $fieldName = "lsb[$listId][$fieldKey]";
                if ($fieldType === 'checkbox') {
                    $fieldName = $fieldName.'[]';
                }
                $fieldValue = $fieldValues[$field['key']]; // already sanitized
                $label = esc_html($field['name']);
                $label = apply_filters(Plugin::FILTER_FIELD_LABEL, $label, $listId, $field);
			    $requiredIndicator = apply_filters(Plugin::FILTER_REQUIRED_INDICATOR, '*', $listId, $field);
                $autocompleteValue = $field['autocomplete'] ?? null;
                $isGroup = in_array($fieldType, ['radio', 'checkbox']);
                $fieldWrapperTag = $isGroup ? 'fieldset' : 'div';
            ?>

            <<?= $fieldWrapperTag ?>
                class="<?php echo $fieldWrapperClass ?>
                lsb-field-tag-<?php echo esc_attr($field['key']) ?>
                lsb-field-type-<?php echo $fieldType ?>"
                data-field-type="<?php echo $fieldType ?>"
                data-required="<?php if ($field['required']): ?>true<?php else: ?>false<?php endif ?>"
                <?php if ($isGroup): ?>
                    aria-invalid="false"
                    aria-required="<?php if ($field['required']): ?>true<?php else: ?>false<?php endif ?>"
                    aria-describedby="<?php echo $uniqueFieldKey.'_error' ?>"
                <?php endif ?>
            >

                <?php if ($fieldType === 'select'):
                    $defaultSelectOptionText = esc_html__('Please choose', 'laposta-signup-basic');
                    $defaultSelectOptionText = apply_filters(Plugin::FILTER_FIELD_DEFAULT_SELECT_OPTION_TEXT, $defaultSelectOptionText, $listId, $field);
                ?>
                    <label for="<?php echo $uniqueFieldKey ?>" class="<?php echo $labelClass ?>">
                        <span class="<?php echo $labelNameClass ?>"><?php echo $label ?></span><?php if ($field['required']): ?><span class="<?php echo $labelRequiredClass ?>"><?php echo $requiredIndicator ?></span><?php endif ?>
                    </label>
                    <select
                        class="<?php echo $selectClass ?>"
                        id="<?php echo $uniqueFieldKey ?>"
                        name="<?php echo $fieldName ?>"
                        aria-invalid="false"
                        aria-required="<?php if ($field['required']): ?>true<?php else: ?>false<?php endif ?>"
                        aria-describedby="<?php echo $uniqueFieldKey.'_error' ?>"
                    >
                        <option value=""><?php echo $defaultSelectOptionText ?></option>
                        <?php foreach ($field['options_full'] as $option): ?>
                            <option
                                value="<?php echo esc_html($option['value']) ?>"
                                <?php if ($fieldValue === $option['value']): ?>selected="selected"<?php endif ?>>
                                <?php echo esc_html($option['value']) ?>
                            </option>
                        <?php endforeach ?>
                    </select>

                <?php elseif ($isGroup): // radios or checkboxes ?>
                    <legend class="<?php echo $labelClass ?>">
                        <span class="<?php echo $labelNameClass ?>"><?php echo $label ?></span><?php if ($field['required']): ?><span class="<?php echo $labelRequiredClass ?>"><?php echo $requiredIndicator ?></span><?php endif ?>
                    </legend>
                    <div class="<?php echo $checksWrapperClass ?>">
                        <?php foreach ($field['options_full'] as $check): ?>
                            <?php
                                $checked =
                                    ($fieldValue === $check['value']) ||
                                    (is_array($fieldValue) && in_array($check['value'], $fieldValue));
                            ?>
                            <div class="<?php echo $checkWrapperClass ?> id-<?php echo esc_attr($check['id']) ?>">
                                <input
                                    class="<?php echo $checkInputClass ?>"
                                    id="<?php echo esc_attr($uniqueFieldKey.$check['id']) ?>"
                                    type="<?php echo $fieldType ?>"
                                    value="<?php echo esc_attr($check['value']) ?>"
                                    name="<?php echo $fieldName ?>"
                                    <?php if ($checked): ?>checked="checked"<?php endif ?>
                                >
                                <label
                                    for="<?php echo esc_attr($uniqueFieldKey.$check['id']) ?>"
                                    class="<?php echo $checkLabelClass ?>">
                                    <?php echo esc_html($check['value']) ?>
                                </label>
                            </div>
                        <?php endforeach ?>
                    </div>

                <?php else: ?>
                    <?php
                        $defaultPlaceHolder = $fieldType === 'date' ? __('YYYY-MM-DD', 'laposta-signup-basic') : '';
                        $placeholder = apply_filters(Plugin::FILTER_FIELD_PLACEHOLDER, $defaultPlaceHolder, $listId, $field);
                    ?>
                    <label for="<?php echo $uniqueFieldKey ?>" class="<?php echo $labelClass ?>">
                        <span class="<?php echo $labelNameClass ?>"><?php echo $label ?></span><?php if ($field['required']): ?><span class="<?php echo $labelRequiredClass ?>"><?php echo $requiredIndicator ?></span><?php endif ?>
                    </label>
                    <input
                        id="<?php echo $uniqueFieldKey ?>"
                        type="<?php echo $fieldType ?>"
                        class="<?php echo $inputClass ?>"
                        value="<?php echo $fieldValue ?>"
                        name="<?php echo $fieldName ?>"
                        placeholder="<?php echo $placeholder ?>"
                        aria-invalid="false"
                        aria-required="<?php if ($field['required']): ?>true<?php else: ?>false<?php endif ?>"
                        <?php if ($autocompleteValue): ?>autocomplete="<?php echo esc_attr($autocompleteValue) ?>"<?php endif ?>
                        <?php if ($fieldType === 'number'): ?>step="any"<?php endif ?>
                    >
                <?php endif ?>

                <div class="<?php echo $fieldErrorFeedbackClass ?> <?php echo $visualHiddenClass ?>" style="display:block;" id="<?php echo $uniqueFieldKey.'_error' ?>" aria-live="assertive"></div>

            </<?= $fieldWrapperTag ?>>
        <?php endforeach; ?>

        <?php $fieldName = "lsb[$listId][$fieldNameHoneypot]"; ?>
        <input autocomplete="new-password" type="email" id="<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" placeholder="Your work e-mail here" style="position:absolute;top:-9999px;left:-9999px;" tabindex="-1">

        <?php $fieldName = "lsb[$listId][$fieldNameNonce]"; ?>
        <input type="hidden" name="<?php echo $fieldName ?>" value="<?php echo $nonce ?>" class="js-nonce-input">

        <div class="<?php echo $globalErrorClass ?> <?php echo $visualHiddenClass ?>" role="alert"></div>

        <div class="<?= $submitButtonAndLoaderWrapperClass ?>">
            <button class="<?php echo $submitButtonClass ?>" type="submit" name="lsb_form_submit" value="<?php echo $listId ?>" aria-disabled="false">
                <?php echo $submitButtonText ?>
            </button>
            <span class="<?php echo $loaderClass ?>" style="display: none"></span>
            <span class="lsb-loader-aria <?php echo $visualHiddenClass ?>" role="status" aria-live="polite"></span>
        </div>

    </div>

    <div class="<?php echo $successContainerClass ?> <?php echo $visualHiddenClass ?>" role="alert"></div>

</form>