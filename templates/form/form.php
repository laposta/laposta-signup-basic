<?php
/**
 * @var string $listId (valid id)
 * @var array $listFields
 * @var string $formClass (sanitized)
 * @var string $fieldWrapperClass (sanitized)
 * @var string $labelClass (sanitized)
 * @var string $inputClass (sanitized)
 * @var string $selectClass (sanitized)
 * @var string $checksWrapperClass (sanitized)
 * @var string $checkWrapperClass (sanitized)
 * @var string $checkInputClass (sanitized)
 * @var string $checkLabelClass (sanitized)
 * @var string $submitButtonAndLoaderWrapperClass (sanitized)
 * @var string $submitButtonClass (sanitized)
 * @var string $submitButtonText (sanitized)
 * @var string $loaderClass (sanitized)
 * @var string $inlineCss (sanitized)
 * @var array $fieldValues (sanitized)
 * @var bool $hasDateFields
 * @var string $globalErrorClass (sanitized)
 * @var string $fieldNameHoneypot
 * @var string $fieldNameNonce
 * @var string $nonce
 * @var string $formPostUrl
 * @var array|null $datepickerLang
 */

use Laposta\SignupBasic\Plugin;

?>

<?php if ($inlineCss): ?>
    <style>
        <?php echo $inlineCss ?>
    </style>
<?php endif ?>

<?php
    $requiredIndicator = apply_filters(Plugin::FILTER_REQUIRED_INDICATOR, '*', $listId);
?>

<form class="<?php echo $formClass ?> lsb-list-id-<?php echo $listId ?> js-lsb-form"
      method="post"
      data-form-post-url="<?php echo $formPostUrl ?>"
>
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
            if ($field['required']) {
                $label .= $requiredIndicator;
            }
            $label = apply_filters(Plugin::FILTER_FIELD_LABEL, $label, $listId, $field);
            $autocompleteValue = $field['autocomplete'] ?? null;
        ?>
        <div class="<?php echo $fieldWrapperClass ?> lsb-field-tag-<?php echo esc_attr($field['key']) ?> lsb-field-type-<?php echo $fieldType ?>">

            <?php if ($fieldType === 'select'):
                $defaultSelectOptionText = esc_html__('Please choose', 'laposta-signup-basic');
                $defaultSelectOptionText = apply_filters(Plugin::FILTER_FIELD_DEFAULT_SELECT_OPTION_TEXT, $defaultSelectOptionText, $listId, $field);
            ?>
                <label for="<?php echo $uniqueFieldKey ?>" class="<?php echo $labelClass ?>"><?php echo $label ?></label>
                <select
                    class="<?php echo $selectClass ?>"
                    id="<?php echo $uniqueFieldKey ?>"
                    name="<?php echo $fieldName ?>"
                    <?php if ($field['required']): ?>required="required"<?php endif ?>
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

            <?php elseif ($fieldType === 'radio' || $fieldType === 'checkbox'): ?>
                <p class="<?php echo $labelClass ?>"><?php echo $label ?></p>
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
                    if (!in_array($fieldType, ['text', 'email', 'number', 'date'])) {
                        // fallback to text
                        $fieldType = 'text';
                    }
                    $placeholder = apply_filters(Plugin::FILTER_FIELD_PLACEHOLDER, '', $listId, $field);
                ?>
                <label for="<?php echo $uniqueFieldKey ?>" class="<?php echo $labelClass ?>"><?php echo $label ?></label>
                <input
                    id="<?php echo $uniqueFieldKey ?>"
                    type="<?php echo $fieldType === 'date' ? 'text' : $fieldType // avoid browser specific behavior ?>"
                    class="<?php echo $inputClass ?> <?php if ($fieldType === 'date'): ?>js-lsb-datepicker<?php endif ?>"
                    value="<?php echo $fieldValue ?>"
                    name="<?php echo $fieldName ?>"
                    placeholder="<?php echo $placeholder ?>"
                    <?php if ($field['required']): ?>required="required"<?php endif ?>
                    <?php if ($autocompleteValue): ?>autocomplete="<?php echo esc_attr($autocompleteValue) ?>"<?php endif ?>
                    <?php if ($fieldType === 'number'): ?>step="any"<?php endif ?>
                    <?php if ($fieldType === 'date'): ?>placeholder="dd-mm-jjjj"<?php endif ?>
                >
            <?php endif ?>
        </div>
    <?php endforeach; ?>

    <?php $fieldName = "lsb[$listId][$fieldNameHoneypot]"; ?>
    <input autocomplete="new-password" type="email" id="<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" placeholder="Your work e-mail here" style="position:absolute;top:-9999px;left:-9999px;" tabindex="-1">

    <?php $fieldName = "lsb[$listId][$fieldNameNonce]"; ?>
    <input type="hidden" name="<?php echo $fieldName ?>" value="<?php echo $nonce ?>" class="js-nonce-input">

    <div class="lsb-form-global-error <?php echo $globalErrorClass ?>" style="display: none"></div>

    <div class="lsb-button-and-loader-wrapper <?= $submitButtonAndLoaderWrapperClass ?>">
        <button class="<?php echo $submitButtonClass ?>" type="submit" name="lsb_form_submit" value="<?php echo $listId ?>">
            <?php echo $submitButtonText ?>
        </button>
        <span class="<?php echo $loaderClass ?>" style="display: none"></span>
    </div>

    <?php if ($hasDateFields): ?>
        <script>
          document.addEventListener("DOMContentLoaded", function() {
            let config = {};
            <?php
              $format = $datepickerLang['format_long'] ?? $datepickerLang['format_short'] ?? null;
              if ($format): ?>
                  config = {
                    locale: '<?php echo $datepickerLang['file_lang'] ?? $datepickerLang['lang'] ?>',
                    altInput: true,
                    altFormat: '<?php echo $format ?>',
                  }
            <?php endif ?>
            flatpickr('.js-lsb-datepicker', config);
          });
        </script>
    <?php endif ?>

</form>