<h1 style="margin-bottom: 20px;">Install linkTracker - Step 2</h1>
<?php
	$alerts = array();
	$showForm = true;
	
	$emailPost = (isset($_POST['email'])) ? trim($_POST['email']) : '';
	$usernamePost = (isset($_POST['username'])) ? trim($_POST['username']) : '';
	$passwordPost = (isset($_POST['password'])) ? trim($_POST['password']) : '';
	
	if ($_POST)
	{
		$processAccCreate = true;
		
		//validate email
		if (strlen(trim($emailPost)) == 0)
		{
			$processAccCreate = false;
			$alerts['Email is empty.'] = 'danger';
		}
		else if (!isEmail($emailPost))
		{
			$processAccCreate = false;
			$alerts['Invalid Email.'] = 'danger';
		}
		
		//validate username
		if (strlen(trim($usernamePost)) == 0)
		{
			$processAccCreate = false;
			$alerts['Username is empty.'] = 'danger';
		}
		else if (!isAlphanumeric($usernamePost))
		{
			$processAccCreate = false;
			$alerts['Username can can only contain letters and numbers.'] = 'danger';
		}
		
		//validate password
		if (strlen(trim($passwordPost)) == 0)
		{
			$processAccCreate = false;
			$alerts['Password is empty.'] = 'danger';
		}
		else if (strlen($passwordPost) < 7)
		{
			$processAccCreate = false;
			$alerts['Password must be atleast 7 characters .'] = 'danger';
		}
		
		if ($processAccCreate)
		{
			//go ahead and create the database tables and insert the appropriate data
			
			$crypt = new Bcrypt;
			
			$hashedPW = $crypt->hash($passwordPost);
			
			if ($db->query('CREATE TABLE IF NOT EXISTS `eventLog` (
				`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
				`user` int(11) NOT NULL,
				`ip` varchar(255) NOT NULL,
				`date` bigint(22) NOT NULL,
				`msg` text NOT NULL
			) AUTO_INCREMENT=1'))
			{
				if ($db->query("CREATE TABLE IF NOT EXISTS `users` (
					`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
					`username` varchar(255) NOT NULL UNIQUE,
					`password` varchar(255) NOT NULL,
					`email` varchar(255) NOT NULL,
					`createdIP` varchar(255) NOT NULL,
					`createdTime` bigint(22) NOT NULL,
					`lastLoginIP` varchar(255) NOT NULL,
					`lastLoginTime` bigint(22) NOT NULL
				) AUTO_INCREMENT=1"))
				{
					//create first user
					if ($result = $db->insert('users', array(
						'username' => $usernamePost,
						'password' => $hashedPW,
						'email' => $emailPost,
						'createdIP' => $_SERVER['REMOTE_ADDR'],
						'createdTime' => time(),
						'lastLoginIP' => '',
						'lastLoginTime' => 0
					)))
					{
						//write to event log about user being created
						writeEventLog($db, $db->insertedID, 'User "' . $usernamePost . '" was created by installer script.');
						
						//TODO LATER: Write the table to store links, brutes and clicks logs
						
						if ($db->query("CREATE TABLE IF NOT EXISTS `options` (
							`kvs_key` varchar(255) NOT NULL,
							`kvs_value` text NOT NULL
						)"))
						{
							if (!kvs_write('dbVer', getCurrentDBVersion()))
							{
								die('Failed to write "dbVer" options key.'); 
							}
						}
						else
						{
							die('Database Error (' . $db->errorNum . ') ' . $db->error); 
						}
						
						$showForm = false;
						
					}
					else
					{
						die('Database Error (' . $db->errorNum . ') ' . $db->error); 
					}

				}
				else
				{
					die('Database Error (' . $db->errorNum . ') ' . $db->error);
				}

			}
			else
			{
				die('Database Error (' . $db->errorNum . ') ' . $db->error);
			}
			
		}
	}
	
	if ($showForm)
	{
		//show alerts
		foreach ($alerts as $msg => $type)
		{
			echo '<div class="alert alert-' . $type . '" role="alert">' . $msg . '</div>';
		}
		
		?>
			<div class="well well-lg">
				<h3>Create Admin Account</h3>
				<form class="form-horizontal" role="form" method="post" action="/install/">
					<div class="form-group">
						<label for="inputEmail" class="col-sm-2 control-label">Email</label>
						<div class="col-sm-10">
							<input type="text" name="email" class="form-control" id="inputEmail" autocomplete="off" value="<?=h($emailPost)?>"><!--  Change type to email once server side validation works -->
						</div>
					</div>
					<div class="form-group">
						<label for="inputUser" class="col-sm-2 control-label">Username</label>
						<div class="col-sm-10">
							<input type="text" name="username" class="form-control" id="inputUser" autocomplete="off" value="<?=h($usernamePost)?>">
						</div>
					</div>
					<div class="form-group">
						<label for="inputPass" class="col-sm-2 control-label">Password</label>
						<div class="col-sm-10">
							<input type="password" name="password" class="form-control" id="inputPass" autocomplete="off"">
						</div>
					</div>
					<div align="center"><button type="submit" class="btn btn-success btn-lg">Continue</button></div>
				</form>
			</div>
		<?php
	}
	else
	{
		?>
			<div style="margin-top: 20px;"></div>
			<div class="alert alert-success" role="alert"><h2>Install Complete!</h2></div>
			<?php
			if (killInstaller())
			{
				redirect('/admin');
			}
			else
			{
				?>
					<br><br><br><b>Self destruct failed. Please manually delete this folder. Also you can visit the admin panel at <a href="/admin"><code>/admin</code></a></b>
				<?php
			}
		
	}
?>