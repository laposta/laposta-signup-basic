<?php
/**
 * @var string $globalErrorClass
 * @var string $errorMessage
 * @var string $inlineCss (sanitized)
 */
?>

<?php if ($inlineCss): ?>
    <style>
        <?php echo $inlineCss ?>
    </style>
<?php endif ?>

<div class="lsb-form-global-error <?php echo $globalErrorClass ?>">
    Laposta Signup Basic foutmelding:<br>
    <?php echo esc_html($errorMessage) ?>
</div>