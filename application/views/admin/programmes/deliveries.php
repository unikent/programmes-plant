<html>
<head>
	<title></title>
	<link href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.2.2/css/bootstrap.min.css" rel="stylesheet">
	<style>
		table input, table select {width:auto;}
	</style>
</head>
<body>

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
						<td> <input name="award" type='text' value='<?php echo $delivery->award; ?>' /> </td>
						<td> <input name="pos_code" type='text' value='<?php echo $delivery->pos_code; ?>' /> </td>
						<td> <?php echo Form::text('mcr', $delivery->mcr); ?>  </td>
						<td> <?php echo Form::select('attendance_pattern',array('full-time'=>'Full time', 'part-time'=> 'Part time'),$delivery->attendance_pattern); ?></td>
						<td>
						 		<input type="hidden" name="id" value="<?php echo $delivery->id; ?>" />
							 	<input type='submit' class='btn btn-primary' value='Save' /> 
								<a href='#' class='btn btn-danger'>Remove</a>
						</td>
					</form>
				</tr>
			
		<?php endforeach; ?>
		<tr>
			<form action="" method="post">
				<td> <input name="award" type='text' value='' /> </td>
				<td> <input name="pos_code" type='text' value='' /> </td>
				<td> <?php echo Form::text('mcr'); ?> </td>
				<td> <?php echo Form::select('attendance_pattern',array('full-time'=>'Full time', 'part-time'=> 'Part time'),''); ?></td>
				<td><input type='submit' class='btn btn-success' value='create' /></td>
			</form>
		</tr>
	</table>
</div>
</body>
</html>


