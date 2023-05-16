<?php if (Config::get('kent.ug_banner_html') && 'ug' == (URLParams::$type)): ?>
    <div class="alert alert-error" role="alert">
        <?php if (Config::get('kent.ug_banner_heading')): ?>
            <h2 class="alert-heading"><?= Config::get('kent.ug_banner_heading') ?></h2>
        <?php endif; ?>
        <p>
            <?= Config::get('kent.ug_banner_html') ?>
        </p>
    </div>
<?php endif; ?>