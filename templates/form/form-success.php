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
        <?= $inlineCss ?>
    </style>
<?php endif ?>

<div class="lsb-success <?= $successWrapperClass ?>">
    <h2 class="lsb-success-title <?= $successTitleClass ?>"><?= $successTitle ?></h2>
    <p class="lsb-success-text <?= $successTitleClass ?>"><?= $successText ?></p>
</div>