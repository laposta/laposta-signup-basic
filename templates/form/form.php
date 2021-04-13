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
 * @var string $submitButtonClass (sanitized)
 * @var string $inlineCss (sanitized)
 * @var array $fieldValues
 * @var bool $hasErrors
 * @var string $globalError (sanitized)
 * @var bool $hasDateFields
 * @var string $globalErrorClass
 * @var string $fieldNameHoneypot
 * @var string $fieldNameNonce
 * @var string $nonce
 *
 */
?>

<?php if ($inlineCss): ?>
    <style>
        <?= $inlineCss ?>
    </style>
<?php endif ?>
<form class="<?= $formClass ?> lsb-list-id-<?= $listId ?>" method="post">

    <?php if ($globalError): ?>
        <div class="lsb-form-global-error <?= $globalErrorClass ?>">
            <?= $globalError ?>
        </div>
    <?php endif ?>

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
                $label.='*';
            }
        ?>
        <div class="<?= $fieldWrapperClass ?> lsb-field-tag-<?= $field['key'] ?> lsb-field-type-<?= $fieldType ?>">

            <?php if ($fieldType === 'select'): ?>
                <label for="<?= $uniqueFieldKey ?>" class="<?= $labelClass ?>"><?= $label ?></label>
                <select
                    class="<?= $selectClass ?>"
                    id="<?= $uniqueFieldKey ?>"
                    name="<?= $fieldName ?>"
                    <?php if ($field['required']): ?>required="required"<?php endif ?>
                >
                    <option value="">Maak een keuze</option>
                    <?php foreach ($field['options_full'] as $option): ?>
                        <option
                            value="<?= esc_html($option['value']) ?>"
                            <?php if ($fieldValue === $option['value']): ?>selected="selected"<?php endif ?>>
                            <?= esc_html($option['value']) ?>
                        </option>
                    <?php endforeach ?>
                </select>

            <?php elseif ($fieldType === 'radio' || $fieldType === 'checkbox'): ?>
                <p class="<?= $labelClass ?>"><?= $label ?></p>
                <div class="<?= $checksWrapperClass ?>">
                    <?php foreach ($field['options_full'] as $check): ?>
                        <?php
                            $checked =
                                ($fieldValue === $check['value']) ||
                                (is_array($fieldValue) && in_array($check['value'], $fieldValue));
                        ?>
                        <div class="<?= $checkWrapperClass ?> id-<?= esc_attr($check['id']) ?>">
                            <input
                                class="<?= $checkInputClass ?>"
                                id="<?= esc_attr($uniqueFieldKey.$check['id']) ?>"
                                type="<?= $fieldType ?>"
                                value="<?= esc_attr($check['value']) ?>"
                                name="<?= $fieldName ?>"
                                <?php if ($checked): ?>checked="checked"<?php endif ?>
                            >
                            <label
                                for="<?= esc_attr($uniqueFieldKey.$check['id']) ?>"
                                class="<?= $checkLabelClass ?>">
                                <?= esc_html($check['value']) ?>
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
                ?>
                <label for="<?= $uniqueFieldKey ?>" class="<?= $labelClass ?>"><?= $label ?></label>
                <input
                    id="<?= $uniqueFieldKey ?>"
                    type="<?= $fieldType === 'date' ? 'text' : $fieldType // avoid browser specific behavior ?>"
                    class="<?= $inputClass ?> <?php if ($fieldType === 'date'): ?>js-lsb-datepicker<?php endif ?>"
                    value="<?= $fieldValue ?>"
                    name="<?= $fieldName ?>"
                    <?php if ($field['required']): ?>required="required"<?php endif ?>
                    <?php if ($fieldType === 'number'): ?>step="any"<?php endif ?>
                    <?php if ($fieldType === 'date'): ?>placeholder="dd-mm-jjjj"<?php endif ?>
                >
            <?php endif ?>
        </div>
    <?php endforeach; ?>

    <?php $fieldName = "lsb[$listId][$fieldNameHoneypot]"; ?>
    <input autocomplete="new-password" type="email" id="<?= $fieldName ?>" name="<?= $fieldName ?>" placeholder="Your work e-mail here" style="position:absolute;top:-9999px;left:-9999px;">

    <?php $fieldName = "lsb[$listId][$fieldNameNonce]"; ?>
    <input type="hidden" name="<?= $fieldName ?>" value="<?= $nonce ?>">

    <button class="<?= $submitButtonClass ?>" type="submit" name="lsb_form_submit" value="<?= $listId ?>">Aanmelden</button>

    <?php if ($hasDateFields): ?>
        <script>
          document.addEventListener("DOMContentLoaded", function() {
            flatpickr('.js-lsb-datepicker', {locale: 'nl', altInput: true, altFormat: 'd-m-Y'});
          });
        </script>
    <?php endif ?>

</form>