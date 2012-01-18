<?php

	/**
   * This file is part of SimpleUsers.
   *

	 */

	/**
	* Installation script for SimpleUsers
	* Did you remember to edit config.inc.php? Otherwise, this script might fail
	* This script is fairly simple to use - open it in your browser, press the
	* install button and watch the magic...
	*/

	require_once(dirname(__FILE__)."/su.inc.php");

	/* You shouldn't edit anything below this comment */

	$mysqli = new mysqli($GLOBALS["mysql_hostname"], $GLOBALS["mysql_username"], $GLOBALS["mysql_password"], $GLOBALS["mysql_database"]);
	if( $mysqli->connect_error )
	{
		$error = true;
		$title = "MySQL Connection failed!";
		$message = "Did you remember to edit config.inc.php? MySQL said: ".$mysqli->connect_error;
	}

	if(isset($_POST["step"]))
		$install = true;


	$tables["users"] = "CREATE TABLE IF NOT EXISTS `users` (
  `userId` int(11) NOT NULL auto_increment,
  `uUsername` varchar(128) NOT NULL,
  `uPassword` varchar(40) NOT NULL,
  `uSalt` varchar(128) NOT NULL,
  `uActivity` datetime NOT NULL,
  `uCreated` datetime NOT NULL,
  PRIMARY KEY  (`userId`),
  UNIQUE KEY `uUsername` (`uUsername`)
	) ENGINE=MyISAM AUTO_INCREMENT=1 ;";

	$tables["users_information"] = "CREATE TABLE IF NOT EXISTS `users_information` (
  `userId` int(11) NOT NULL,
  `infoKey` varchar(128) NOT NULL,
  `InfoValue` text NOT NULL,
  KEY `userId` (`userId`)
	) ENGINE=MyISAM;";






?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>SimpleUsers Installation</title>
	  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	  <style type="text/css">

			* {	margin: 0px; padding: 0px; }
			body
			{
				padding: 30px;
				font-family: Calibri, Verdana, "Sans Serif";
				font-size: 12px;
			}

	  	h1
	  	{
	  		color: #FF0000;
	  		border-bottom: 2px solid #000000;
	  	}

	  	p { margin: 10px 0px; }
	  	p.faded { color: #A0A0A0; }

	  </style>
	</head>
	<body>
		<h1>Installation of SimpleUsers</h1>
			<?php if( isset($error) ): ?>

				<p>
					<strong><?php echo $title; ?></strong>
				</p>
				<p class="faded">
					<?php echo $message; ?>
				</p>

			<?php else: ?>
				<form method="post" action="">
					<?php if( !isset($install) ): ?>
					<p>
						<strong>Ready to install</strong>
					</p>
					<p>
						<input type="hidden" name="step" value="1" />
						When ready to proceed, press `Install!` - when your'e done, consider deleting, moving or renaming this install script for security purposes.<br />
					<p>
						<input type="submit" name="submit" value="Install!" />
					</p>
					<?php else: ?>

					<p>
						<strong>Creating database tables</strong>
					</p>

					<?php foreach( $tables as $table => $query ): ?>
					<p class="faded">
						<?php
							if( $stmt = $mysqli->prepare($query) )
							{
								if( $stmt->execute() )
									$status = "done...";
								else
									$status = "failed! MySQL returned: ".$stmt->error;
							}
							else
								$status = "failed! MySQL returned: ".$mysqli->error;
						?>
						Table `<?php echo $table; ?>`: <?php echo $status; ?>
					</p>
					<?php endforeach; ?>

					<?php endif; ?>
				</form>
			<?php endif; ?>
		<hr />
	</body>
</html>