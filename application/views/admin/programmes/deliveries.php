<html>
<head>
	<title></title>
	<link href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.2.2/css/bootstrap.min.css" rel="stylesheet">
	<style>
		table input, table select {width:auto;}
		table table {
			width:100%;
		}
		table form, table form input, table form select {margin-bottom:0px;}
		table table td, table table th {width:20%;}
		.row-fluid table.table-striped tbody table tr th,
		.row-fluid table.table-striped tbody table tr td {background-color: transparent; border-top:0;}
	</style>
</head>
<body>

<div class='row-fluid'>
	<table class='table table-striped'>
		<tr>
			<td>
				<table>
					<tr>
						<th> Award </th>
						<th> POS Code </th>
						<th> MCR Code </th>
						<th> Attendence pattern  </th>
						<th> Description</th>
					</tr>
				</table>
			</td>
		</tr>

		<?php foreach($deliveries as $delivery):
            $awardClass = strtoupper($type).'_Award';

            ?>
			<tr>
				<td>
					<table>
						<tr>
							<td> <?php echo implode(', ',$awardClass::replace_ids_with_values($delivery->award,false,true)); ?> </td>
							<td> <?php echo $delivery->pos_code; ?> </td>
							<td> <?php echo $delivery->mcr; ?>  </td>
							<td> <?php echo $delivery->attendance_pattern; ?></td>
							<td> <?php echo $delivery->description; ?> </td>
						</tr>
					</table>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>

	<div class='container-fluid'>
		<div class='span12 alert alert-info'>
			<p>Please contact EMS if you need any of these details to be updated</p>
		</div>
	</div>
</div>
</body>
</html>


