
<div class="row-fluid">
	<div class="span12">
		<h2 class="alert-ug">UG
		<?php 
		$ug_current = Setting::get_setting('ug_editing_year');
		$pg_current = Setting::get_setting('pg_editing_year');

		for ($year=$ug_current -1; $year<=($ug_current+1); $year++): ?>
        	<a href='<?php echo url($year . '/ug/programmes') ?>' class="alert-year <?php echo $year == $ug_current ? 'current-year' : '' ?>"><?php echo $year; ?></a>
        <?php endfor; ?>
		</h2>
	</div><!--.span12-->
</div><!--.row-fluid-->

<div class="row-fluid">
	<div class="span12">
		<h2 class="alert-pg">PG
		<?php for ($year=$pg_current-1; $year<=($pg_current+1); $year++): ?>
			<a href='<?php echo url($year . '/pg/programmes') ?>' class="alert-year <?php echo $year == $pg_current ? 'current-year' : '' ?>"><?php echo $year; ?></a>
		<?php endfor; ?>
		</h2>
	</div><!--.span12-->
</div><!--.row-fluid-->