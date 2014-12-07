<h1 style="margin-bottom: 40px;">Install linkTracker - Step 1</h1>
<?php
	$phpVer = phpversion();
	if (version_compare( '5.2', $phpVer) <= 0)
	{
		?>
			<div class="alert alert-success" role="alert"><b>PHP:</b> Version <?=$phpVer?> is newer than 5.2!</div>
		<?php
	}
	else
	{
		?>
			<div class="alert alert-danger" role="alert"><b>PHP:</b> Must be version 5.2 or newer. Ask your server admin for an upgrade, you currently are using version <?=$phpVer?>.</div>
		<?php
	}
	
	
	if (function_exists('mysqli_connect'))
	{
		?>
			<div class="alert alert-success" role="alert"><b>MySQLi:</b> MySQLi PHP extension is successfully installed!</div>
		<?php
	}
	else
	{
		?>
			<div class="alert alert-danger" role="alert"><b>MySQLi:</b> MySQLi PHP extension is NOT installed. Ask your server admin to install this.</div>
		<?php
	}
	
	$dbHostPost = (isset($_POST['host'])) ? trim($_POST['host']) : 'localhost';
	$dbNamePost = (isset($_POST['name'])) ? trim($_POST['name']) : '';
	$dbUserPost = (isset($_POST['user'])) ? trim($_POST['user']) : '';
	$dbPassPost = (isset($_POST['pass'])) ? trim($_POST['pass']) : '';
	
	if ($_POST)
	{
		$db = new PastaDB();
		
		if ($db->connect($dbHostPost, $dbUserPost, $dbPassPost, $dbNamePost))
		{
			
			//check version
			$mySQLVer = cleanMySQLVersion($db->DBH->server_info);
			
			if (version_compare( '5.0', $mySQLVer ) <= 0)
			{
				?>
					<div class="alert alert-success" role="alert"><b>MySQL:</b> Version <?=$mySQLVer?> is newer than 5.0!</div>
				<?php
					
				//write config file and prompt set two
				
				
			}
			else
			{
				?>
					<div class="alert alert-danger" role="alert"><b>MySQL:</b> Must be version 5.0 or newer. Ask your server admin for an upgrade, you currently are using version <?=$mySQLVer?>.</div>
				<?php
			}
			
		}
		else
		{
			?>
				<div class="alert alert-danger" role="alert">Could not connect to the database. Is your database, database name, database username and database password correct? Connect Error: (<?=$db->errorNum?>) <?$db->error?>
				</div>
			<?php
		}
		
	}
	
?>
<div class="well well-lg">
	<h2>Database Information</h2>
	<form class="form-horizontal" role="form" method="post" action="/install/">
		<div class="form-group">
			<label for="inputHost" class="col-sm-2 control-label">Database Host</label>
			<div class="col-sm-10">
				<input type="text" name="host" class="form-control" id="inputHost" autocomplete="off" value="<?=h($dbHostPost)?>">
			</div>
		</div>
		<div class="form-group">
			<label for="inputDatabaseName" class="col-sm-2 control-label">Database Name</label>
			<div class="col-sm-10">
				<input type="text" name="name" class="form-control" id="inputDatabaseName" autocomplete="off" value="<?=h($dbNamePost)?>">
			</div>
		</div>
		<div class="form-group">
			<label for="inputDatabaseUser" class="col-sm-2 control-label">Database User</label>
			<div class="col-sm-10">
				<input type="text" name="user" class="form-control" id="inputDatabaseUser" autocomplete="off" value="<?=h($dbUserPost)?>">
			</div>
		</div>
		<div class="form-group">
			<label for="inputDatabasePass" class="col-sm-2 control-label">Database Pass</label>
			<div class="col-sm-10">
				<input type="text" name="pass" class="form-control" id="inputDatabasePass" autocomplete="off" value="<?=h($dbPassPost)?>">
			</div>
		</div>
		
		<div align="center"><button type="submit" class="btn btn-success btn-lg">Continue</button></div>
	</form>
</div>