<?php
/**
 * @var string $globalErrorClass
 * @var string $errorMessage
 * @var string $inlineCss (sanitized)
 */
?>

<?php if ($inlineCss): ?>
    <style>
        <?= $inlineCss ?>
    </style>
<?php endif ?>

<div class="lsb-form-global-error <?= $globalErrorClass ?>">
    Laposta Signup Basic foutmelding:<br>
    <?= esc_html($errorMessage) ?>
</div>