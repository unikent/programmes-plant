<html>
<head>
	<title></title>
	<link href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.2.2/css/bootstrap.min.css" rel="stylesheet">
	<style>
		table input, table select {width:auto;}
	</style>
</head>
<body>

<?php

	if(!Auth::user()->can("view_pg_deliveries")) die();

	$disabled = !Auth::user()->can("edit_pg_deliveries");

?>

<div class='row-fluid'>	
	<table class='table table-striped'>
		<tr>
			<th> Award </th>
			<th> POS Code </th>
			<th>  MCR Code </th>
			<th> Attendence pattern  </th>
			<th> Actions</th>
		</tr>

		<?php foreach($deliveries as $delivery): ?>
			
				<tr>
					<form action="" method="post">
						<td> <?php echo Form::select('award', PG_Award::all_as_list(), $delivery->award); ?> </td>
						<td> <?php echo Form::text('pos_code', $delivery->pos_code); ?> </td>
						<td> <?php echo Form::text('mcr', $delivery->mcr); ?>  </td>
						<td> <?php echo Form::select('attendance_pattern',array('full-time'=>'Full time', 'part-time'=> 'Part time'),$delivery->attendance_pattern); ?></td>
						<td>
						<?php if($disabled):?>
						 	<input type="hidden" name="id" value="<?php echo $delivery->id; ?>" />
							<input type='submit' class='btn btn-primary' value='Save' /> 
							<a href='#' class='btn btn-danger' href=''>Remove</a>
						<?php endif;?>
						</td>
					</form>
				</tr>
			
		<?php endforeach; ?>
		<?php if($disabled):?>
		<tr>
			<form action="" method="post">
				<td> <?php echo Form::select('award', PG_Award::all_as_list()); ?> </td>
				<td> <?php echo Form::text('pos_code'); ?> </td>
				<td> <?php echo Form::text('mcr'); ?> </td>
				<td> <?php echo Form::select('attendance_pattern',array('full-time'=>'Full time', 'part-time'=> 'Part time'),''); ?></td>
				<td><input type='submit' class='btn btn-success' value='create' /></td>
			</form>
			<?php endif;?>
		</tr>
	</table>
</div>
</body>
</html>


