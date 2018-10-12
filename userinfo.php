<?php

	/**
	* Make sure you started your'e sessions!
	* You need to include su.inc.php to make SimpleUsers Work
	* After that, create an instance of SimpleUsers and your'e all set!
	*/

	session_start();
	require_once(dirname(__FILE__)."/simpleusers/su.inc.php");

	$SimpleUsers = new SimpleUsers();

	// This is a simple way of validating if a user is logged in or not.
	// If the user is logged in, the value is (bool)true - otherwise (bool)false.
	if( !$SimpleUsers->logged_in )
	{
		header("Location: login.php");
		exit;
	}

	// If the user is logged in, we can safely proceed.


	$userId = $_GET["userId"];

	$user = $SimpleUsers->getSingleUser($userId);
	if( !$user )
		die("The user could not be found...");

	$userInfo = $SimpleUsers->getInfoArray($userId);

	if( isset($_POST["newkey"]) )
	{
		if( strlen($_POST["newkey"]) > 0 ) {

		    try {
                $SimpleUsers->setInfo($_POST["newkey"], $_POST["newvalue"], $userId);
            }
            catch (Exception $e) {
		        // Ignore errors for this demo purpose
            }


        }
		if( isset($_POST["userInfo"]) )
		{

            foreach ($_POST["userInfo"] as $pKey => $pValue) {
                try {
                    $SimpleUsers->setInfo($pKey, $pValue, $userId);
                }
                catch (Exception $e) {
                    // Ignore errors for this demo purpose
                }

            }

        }

		header("Location: users.php");
		exit;
	}
		


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

		<h1>User information for <?php echo $user["uUsername"]; ?></h1>

		<?php if( isset($error) ): ?>
		<p>
			<?php echo $error; ?>
		</p>
		<?php endif; ?>

		<form method="post" action="">
			
			<?php foreach($userInfo as $key => $value): ?>
			<p>
				<label for="db_<?php echo $key; ?>">Database key "<?php echo $key; ?>"</label><br />
				<input type="text" name="userInfo[<?php echo $key; ?>]" id="db_<?php echo $key; ?>" value="<?php echo htmlspecialchars($value); ?>" /><br />
				<a href="removeinfo.php?userId=<?php echo $userId; ?>&db_key=<?php echo urlencode($key); ?>">Permanently remove this key</a>
			</p>
			<?php endforeach; ?>
			<h4>Create new database key?</h4>
			<p>
				<label for="newkey">Key name</label><br />
				<input type="text" name="newkey" id="newkey" />
			</p>

			<p>
				<label for="newvalue">Key value</label><br />
				<input type="text" name="newvalue" id="newvalue" />
			</p>

			
			<p>
				<input type="submit" name="submit" value="Save" />
			</p>

		</form>

	</body>
</html>