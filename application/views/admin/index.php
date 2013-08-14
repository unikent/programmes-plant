
<div class="row-fluid">
	<div class="span12">
		<h2 class="alert-ug">UG
		<?php for ($year=URLParams::$current_year-1; $year<=(URLParams::$current_year+1); $year++): ?>
        	<a href='<?php echo url($year . '/ug/programmes') ?>' class="alert-year <?php echo $year == URLParams::$current_year ? 'current-year' : '' ?>"><?php echo $year; ?></a>
        <?php endfor; ?>
		</h2>
	</div><!--.span12-->
</div><!--.row-fluid-->

<div class="row-fluid">
	<div class="span12">
		<h2 class="alert-pg">PG
		<?php for ($year=URLParams::$current_year-1; $year<=(URLParams::$current_year+1); $year++): ?>
			<a href='<?php echo url($year . '/pg/programmes') ?>' class="alert-year <?php echo $year == URLParams::$current_year ? 'current-year' : '' ?>"><?php echo $year; ?></a>
		<?php endfor; ?>
		</h2>
	</div><!--.span12-->
</div><!--.row-fluid-->