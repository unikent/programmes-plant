<link href="<?php echo URL::to_asset('bundles/profiler/css/pQp.css'); ?>" rel="stylesheet" />
<script type="text/javascript" src="<?php echo URL::to_asset('bundles/profiler/js/pqp.js'); ?>"></script>

<div id="pqp-container" class="pQp" style="display:none">
	<div id="pQp" class="console">
		<table id="pqp-metrics" cellspacing="0">
			<tr>
				<td class="green" onclick="changeTab('console');">
					<var><?php echo count($logs); ?></var>
					<h4>Console</h4>
				</td>
				<td class="blue" onclick="changeTab('speed');">
					<var><?php echo $load_time * 1000; ?> ms</var>
					<h4>Load Time</h4>
				</td>
				<td class="purple" onclick="changeTab('queries');">
					<var><?php echo count($queries); ?> Queries</var>
					<h4>Database</h4>
				</td>
				<td class="orange" onclick="changeTab('memory');">
					<var><?php echo $memory; ?></var>
					<h4>Memory Used</h4>
				</td>
				<td class="red" onclick="changeTab('files');">
					<var><?php echo count($files); ?> Files</var>
					<h4>Included</h4>
				</td>
			</tr>
		</table>

	<div id="pqp-console" class="pqp-box">
		<?php if(count($logs) == 0): ?>
			<h3>There are no logs.</h3>
		<?php else: ?>
			<table class="side" cellspacing="0">
				<tr>
					<td class="alt1"><var><?php echo $logs_count; ?></var><h4>Logs</h4></td>
					<td class="alt2"><var><?php echo $error_logs; ?></var> <h4>Errors</h4></td>
				</tr>
				<tr>
					<td class="alt3"><var><?php echo $memory_logs; ?></var> <h4>Memory</h4></td>
					<td class="alt4"><var><?php echo $speed_logs; ?></var> <h4>Speed</h4></td>
				</tr>
			</table>
			<table class="main" cellspacing="0">
				<?php foreach($logs as $log): ?>
					<tr class="log-<?php echo $log['type']; ?>">
						<td class="type"><?php echo $log['type']; ?></td>
						<td class="alt">
							<div>
								<?php if(isset($log['data'])): ?>
									<pre><?php echo $log['data']; ?></pre> 
								<?php endif; ?>
								<?php echo $log['message']; ?>
							</div>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
		<?php endif; ?>
	</div>

	<div id="pqp-speed" class="pqp-box">
		<?php if($speed_logs == 0): ?>
			<h3>There are no speed logs.</h3>
		<?php else: ?>
			<table class="side" cellspacing="0">
				<tr><td><var><?php echo $load_time * 1000; ?> ms</var><h4>Load Time</h4></td></tr>
		  		<tr><td class="alt"><var><?php echo $max_execution_time; ?></var> <h4>Max Execution Time</h4></td></tr>
		 	</table>
			<table class="main" cellspacing="0">
				<?php foreach($logs as $log): ?>
					<?php if($log['type'] == 'speed'): ?>		
						<tr class="log-speed">
							<td class="alt">
								<div><pre><?php echo $log['data']; ?></pre> <em><?php echo $log['message']; ?></em></div>
							</td>
						</tr>
					<?php endif; ?>
				<?php endforeach; ?>
			</table>
		<?php endif; ?>
	</div>

	<div id="pqp-queries" class="pqp-box">
		<?php if(count($queries) > 0): ?>
			<table class="side" cellspacing="0">
				<tr><td><var><?php echo count($queries); ?></var><h4>Total Queries</h4></td></tr>
				<tr><td class="alt"><var><?php echo $query_total_time; ?> ms</var> <h4>Total Time</h4></td></tr>
				<tr><td><var><?php echo $query_duplicates; ?></var> <h4>Duplicates</h4></td></tr>
			</table>
			<table class="main" cellspacing="0">
				<?php foreach($queries as $query): ?>
					<tr><td class="alt"><?php echo $query; ?></td></tr>
				<?php endforeach; ?>
			</table>
		<?php else: ?>
			<h3>No queries were executed.</h3>
		<?php endif; ?>
	</div>
		
	<div id="pqp-memory" class="pqp-box">
		<?php if($memory_logs == 0): ?>
			<h3>There are no memory logs.</h3>
		<?php else: ?>
			<table class="side" cellspacing="0">
				<tr>
					<td><var><?php echo $memory; ?></var><h4>Used Memory</h4></td>
				</tr>
		  		<tr>
					<td class="alt"><var><?php echo $memory_limit; ?></var> <h4>Total Available</h4></td>
				</tr>
			</table>

			<table class="main" cellspacing="0">
				<?php foreach($logs as $log): ?>
					<?php if($log['type'] == 'memory'): ?>
						<tr class="log-memory"><td class="alt"><b><?php echo $log['data']; ?></b> <?php echo $log['message']; ?></td></tr>
					<?php endif; ?>
				<?php endforeach; ?>
			</table>
		<?php endif; ?>
	</div>

	<div id="pqp-files" class="pqp-box">
		<table class="side" cellspacing="0">
			<tr><td><var><?php echo count($files); ?></var><h4>Total Files</h4></td></tr>
			<tr><td class="alt"><var><?php echo $files_total_size; ?></var> <h4>Total Size</h4></td></tr>
			<tr><td><var><?php echo $files_largest; ?></var> <h4>Largest</h4></td></tr>
		</table>
		<table class="main" cellspacing="0">
			<?php foreach($files as $file): ?>
				<tr><td class=""><b><?php echo $file['size']; ?></b> <?php echo $file['path']; ?></td></tr>
			<?php endforeach; ?>
		</table>
	</div>

	<table id="pqp-footer" cellspacing="0">
			<tr>
				<td class="credit">
					<a href="http://particletree.com" target="_blank">
					<strong>PHP</strong> 
					<b class="green">Q</b><b class="blue">u</b><b class="purple">i</b><b class="orange">c</b><b class="red">k</b>
					Profiler</a>
				</td>
				<td class="actions">
					<a href="#" onclick="toggleDetails();return false">Details</a>
					<a class="heightToggle" href="#" onclick="toggleHeight();return false">Height</a>
				</td>
			</tr>
		</table>
	</div>
</div>