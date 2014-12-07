<h1 style="margin-bottom: 40px;">Install linkTracker - Step 1</h1>
<?php
	$alerts = array();
	$showForm = true;
	$configFileMade = false;
	
	//check PHP Version
	$phpVer = phpversion();
	if (version_compare( '5.2', $phpVer) <= 0)
	{
		$alerts['<b>PHP:</b> Version ' . $phpVer . ' is newer than 5.2!'] = 'success';
	}
	else
	{
		$showForm = false;
		$alerts['<b>PHP:</b> Must be version 5.2 or newer. Ask your server admin for an upgrade, you currently are using version ' . $phpVer . '.'] = 'danger';
	}
	
	//Check if MySQLi is installed
	if (function_exists('mysqli_connect'))
	{
		$alerts['<b>MySQLi:</b> MySQLi PHP extension is successfully installed!'] = 'success';
	}
	else
	{
		$showForm = false;
		$alerts['<b>MySQLi:</b> MySQLi PHP extension is NOT installed. Ask your server admin to install this.'] = 'danger';
	}
	
	if ($showForm)
	{
		//show alerts
		foreach ($alerts as $msg => $type)
		{
			echo '<div class="alert alert-' . $type . '" role="alert">' . $msg . '</div>';
		}
		
	}
	
	$dbHostPost = (isset($_POST['host'])) ? trim($_POST['host']) : 'localhost';
	$dbNamePost = (isset($_POST['name'])) ? trim($_POST['name']) : '';
	$dbUserPost = (isset($_POST['user'])) ? trim($_POST['user']) : '';
	$dbPassPost = (isset($_POST['pass'])) ? trim($_POST['pass']) : '';
	
	if ($_POST)
	{
		$alerts2 = array();
		$tryDBConnection = true;
		
		//check if host, db name and user was posted
		
		if (strlen(trim($dbHostPost)) == 0)
		{
			$tryDBConnection = false;
			$alerts2['Database Host is empty'] = 'danger';
		}
		
		if (strlen(trim($dbNamePost)) == 0)
		{
			$tryDBConnection = false;
			$alerts2['Database Name is empty'] = 'danger';
		}
		
		if (strlen(trim($dbUserPost)) == 0)
		{
			$tryDBConnection = false;
			$alerts2['Database User is empty'] = 'danger';
		}
		
		//show alerts
		foreach ($alerts2 as $msg => $type)
		{
			echo '<div class="alert alert-' . $type . '" role="alert">' . $msg . '</div>';
		}
		
		$db = new PastaDB();
		
		if ($tryDBConnection)
		{
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
					
					//check to see if the sample config file exists
					if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/system/installer/config-sample.php'))
					{
						if (is_writable($_SERVER['DOCUMENT_ROOT'] . '/system'))
						{
							if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/system/config.php'))
							{
								?>
									<div class="alert alert-danger" role="alert"><code>/system/config.php</code> already exists.</div>
								<?php
							}
							else
							{
								$configFile = file($_SERVER['DOCUMENT_ROOT'] . '/system/installer/config-sample.php');
								
								$handle = fopen($_SERVER['DOCUMENT_ROOT'] . '/system/config.php', 'w');
								
								foreach ($configFile as $line_num => $line)
								{
									$var = substr(trim($line),0,7);
									
									if ($var == '$dbHost')
									{
										fwrite($handle, str_replace("localhost", $dbHostPost, $line));
									}
									else if ($var == '$dbUser')
									{
										fwrite($handle, str_replace("'UserNameHere'", "'$dbUserPost'", $line));
									}
									else if ($var == '$dbPass')
									{
										fwrite($handle, str_replace("'PassHere'", "'$dbPassPost'", $line));
									}
									else if ($var == '$dbName')
									{
										fwrite($handle, str_replace("DatabaseNameHere", $dbNamePost, $line));
									}
									else
									{
										fwrite($handle, $line);
									}
									
								}
								
								fclose($handle);
								
								$showForm = false;
								$configFileMade = true;
	
							}
							
						}
						else
						{
							?>
								<div class="alert alert-danger" role="alert">Cannot write to the <code>/system</code> directory.</div>
							<?php
						}
						
					}
					else
					{
						?>
							<div class="alert alert-danger" role="alert"><b>Sample config file is missing:</b> <code>/system/installer/config-sample.php</code> is missing.</div>
						<?php
					}
					
					
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
			
	}
	
	if ($showForm)
	{
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
		<?php
	}
	else
	{
		if ($configFileMade)
		{
			?>
				<div class="alert alert-success" role="alert"><h2>Config file written. Click <a href="/install">here</a> to go to step 2.</h2></div>
			<?php
		}
	}
	
?>