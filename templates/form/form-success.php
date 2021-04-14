<?php
/**
 * @var string $successWrapperClass (sanitized)
 * @var string $successTitleClass (sanitized)
 * @var string $successTextClass (sanitized)
 * @var string $successTitle (sanitized)
 * @var string $successText (sanitized)
 * @var string $inlineCss (sanitized)
 */
?>

<?php if ($inlineCss): ?>
    <style>
        <?php echo $inlineCss ?>
    </style>
<?php endif ?>

<div class="lsb-success <?php echo $successWrapperClass ?>">
    <h2 class="lsb-success-title <?php echo $successTitleClass ?>"><?php echo $successTitle ?></h2>
    <p class="lsb-success-text <?php echo $successTitleClass ?>"><?php echo $successText ?></p>
</div>