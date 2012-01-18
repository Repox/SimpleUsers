<?php

	/**
	* Make sure you started your'e sessions!
	* You need to include su.inc.php to make SimpleUsers Work
	* After that, create an instance of SimpleUsers and your'e all set!
	*/

	session_start();
	require_once(dirname(__FILE__)."/simpleusers/su.inc.php");

	$SimpleUsers = new SimpleUsers();

	// Validation of input and CSRF
	if( isset($_POST["secretcode"]) )
	{
		// Make sure that data hasn't been tampered with
		$csrf = $SimpleUsers->validateToken();
		
		if($csrf)
		{
			// Proceed with the code to be executed if data hasn't been tampered
		}

	} // Validation end

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title></title>
	  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	  <style type="text/css">

			* {	margin: 0px; padding: 0px; }
			body
			{
				padding: 30px;
				font-family: Calibri, Verdana, "Sans Serif";
				font-size: 12px;
			}
			table
			{
				width: 800px;
				margin: 0px auto;
			}

			th, td
			{
				padding: 3px;
			}

			.right
			{
				text-align: right;
			}

	  	h1
	  	{
	  		color: #FF0000;
	  		border-bottom: 2px solid #000000;
	  		margin-bottom: 15px;
	  	}

	  	p { margin: 10px 0px; }
	  	p.faded { color: #A0A0A0; }

	  </style>
	</head>
	<body>

		<h1>CSRF Protection</h1>

		<?php if( isset($csrf) ): ?>
		<p>
			<?php if($csrf): ?>
				CSRF successfully validated
			<?php else: ?>
				CSRF didn't validate - data probably have been tampered with!
			<?php endif; ?>
		</p>
		<?php endif; ?>

		<form method="post" action="">
			<p>
				<label for="secretcode">Very confidential data:</label><br />
				<input type="text" name="secretcode" id="secretcode" />
				<?php echo $SimpleUsers->getToken(); ?>
			</p>
			<p>
				<input type="submit" name="submit" value="Send sensitive data" />
			</p>

		</form>

	</body>
</html>