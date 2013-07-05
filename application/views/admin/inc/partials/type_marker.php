<?php if (URI::segment(1) == URLParams::$year): ?>

<span class="alert-type alert-<?php echo URLParams::$type; ?>"><?php echo strtoupper(URLParams::$type); ?> <?php echo URLParams::$year ?></span> 

<?php else: ?>

<span class="alert-type alert-<?php echo URLParams::$type; ?>"><?php echo strtoupper(URLParams::$type); ?></span> 

<?php endif; ?>