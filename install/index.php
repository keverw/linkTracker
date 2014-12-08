<?php
	ob_start();
	session_start();
	require_once $_SERVER['DOCUMENT_ROOT'] . '/system/helpers.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/system/PastaDB.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/system/validator.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/system/bcrypt.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/system/keyValueStore.php';
	
	function killInstaller()
	{
		//todo: write this function later!
		return false;
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="icon" href="/favicon.ico">

		<title>Install linkTracker</title>

		<!-- Bootstrap core CSS -->
		<link href="/css/bootstrap.min.css" rel="stylesheet">
		<link href="/css/bootstrap-theme.min.css" rel="stylesheet">
		
		<!-- Custom styles for this template -->
		<link href="/css/custom.css" rel="stylesheet">

		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>

	<body>

		<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
			<div class="container">
				<div class="navbar-header">
					<!--
<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
-->
					<a class="navbar-brand" href="#">linkTracker Installer</a>
				</div>
				<!--
<div id="navbar" class="collapse navbar-collapse">
					<ul class="nav navbar-nav">
						<li class="active"><a href="#">Home</a></li>
						<li><a href="#about">About</a></li>
						<li><a href="#contact">Contact</a></li>
					</ul>
				</div><!--/.nav-collapse -->
-->
			</div>
		</nav>

		<div class="container">

			<div id="content">
				<?php
					if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/system/config.php'))
					{
						require_once $_SERVER['DOCUMENT_ROOT'] . '/system/config.php';
						
						$db = new PastaDB();
						
						if ($db->connect($dbHost, $dbUser, $dbPass, $dbName))
						{
							//check if installed - check if options database table exists
							if ($result = $db->select("show tables like 'options';"))
							{
								if ($db->numRows > 0)
								{
									//this is the very first version, so obviously there wouldn't be any upgrades.
									echo '<b>No Upgrades available. The installer will self destruct.</b>';
									
									if (killInstaller())
									{
										redirect('/install'); //will 404 :)
									}
									else
									{
										?>
											<br><br><br><b>Self destruct failed. Please manually delete this folder.</b>
										<?php
									}
									
								}
								else //go to install step 2
								{
									require_once $_SERVER['DOCUMENT_ROOT'] . '/system/installer/installStep2.php';
								}
								
							}
							else
							{
								die('Database Error (' . $db->errorNum . ') ' . $db->error); 
							}
							
						}
						else
						{
							?>
								<div class="alert alert-danger" role="alert">Could not connect to the database. Connect Error: (<?=$db->errorNum?>) <?$db->error?></div>
							<?php
						}
						
					}
					else
					{
						require_once $_SERVER['DOCUMENT_ROOT'] . '/system/installer/installStep1.php';
					}
				?>
			</div>

		</div><!-- /.container -->

		<!-- Bootstrap core JavaScript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="/js/bootstrap.min.js"></script>
	</body>
</html>
