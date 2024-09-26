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

<div class="<?php echo $globalErrorClass ?>">
    <?php echo esc_html__('Laposta Signup Basic error:', 'laposta-signup-basic') ?><br>
    <?php echo esc_html($errorMessage) ?>
</div>