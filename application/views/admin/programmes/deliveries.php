<html>
<head>
	<title></title>
	<link href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.2.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>



	<div style="padding:20px;" >
		<div class='row-fluid'>	
			<div class='span2'> Award </div>
			<div class='span2'> POS Code </div>
			<div class='span2'> MCR Code </div>
			<div class='span2'> Attendence pattern </div>
			<div class='span4'> Action</div>
		</div>
		<?php foreach($deliveries as $delivery): ?>
		<div class='row-fluid'>	
			<form action="" method="post">
				<input type="hidden" name="id" value="<?php echo $delivery->id; ?>" />

				<div class='span2'> <input name="award" type='text' value='<?php echo $delivery->award; ?>' /> </div>
				<div class='span2'> <input name="pos_code" type='text' value='<?php echo $delivery->pos_code; ?>' /> </div>
				<div class='span2'> <input name="mcr" type='text' value='<?php echo $delivery->mcr; ?>' /> </div>
				<div class='span2'> <input name="attendance_pattern" type='text' value='<?php echo $delivery->attendance_pattern; ?>' /> </div>
				<div class='span4'> 
					<input type='submit' class='btn' value='Save changes' /> 
					<a href='#' class='btn btn-danger'>Remove</a>
				</div>
			</form>
		</div>
		<?php endforeach; ?>
		
	</div>
	<div style='background:#eee;padding:10px 20px;margin-top:20px;'>
		<form action="" method="post">

			<div class='span2'> <input name="award" type='text' value='' /> </div>
			<div class='span2'> <input name="pos_code" type='text' value='' /> </div>
			<div class='span2'> <input name="mcr" type='text' value='' /> </div>
			<div class='span2'> <input name="attendance_pattern" type='text' value='' /> </div>
			<div class='span3'> 
				<input type='submit' class='btn' value='create' /> 
			</div>
		</form>
	</div>
	

</body>
</html>


