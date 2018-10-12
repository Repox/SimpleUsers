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
	  	p.faded { color: #505050; }

	  	ul
	  	{
	  		margin: 20px;
	  	}

	  </style>
	</head>
	<body>

		<h1>SimpleUsers</h1>

		<h2>The application</h2>
		<p>
			<strong>Software</strong>
		</p>
		<p class="faded">
			SimpleUsers needs PHP 5, MySQL (no special version needed) and the MySQL Improved extension (<a href="http://php.net/mysqli">http://php.net/mysqli</a>).
		</p>


		<p>
			<strong>Skills</strong>
		</p>
		<p class="faded">
			Basic knowledge of PHP is required. Assuming that you know basic PHP, you most likely do know your way around HTML.
		</p>

		<p>
			<strong>Is it secure?</strong>
		</p>
		<p class="faded">
			SimpleUsers doesn't store password in clear text which makes the application secure for you AND your users.<br />
			Every user gets a unique 128 character long salt prepended to their chosen password,
			which then gets stored in the database as a SHA1 hash. The hashing is one way.<br />
			Additionally, to avoid SQL injection attacks, SimpleUsers uses prepared statements, when communicating with the database.
		</p>

		<p class="faded">
			CSRF (Cross-site request forgery) protection has been integrated - the methods doesn't require user is logged as CSRF also could be prefered in other situations.<br />
			The CSRF protection works only with POST data (as GET data already is too easy to manipulate).
		</p>		
		
		<p class="faded">
			Please be aware that SimpleUsers isn't a fullblown application; it's not safer than what you use SimpleUsers in.<br />
			When developing your application, think of SimpleUsers as an aid in adding user management to your application.
		</p>

		<h2>How to install</h2>
		<p class="faded">
			It's simple.<br />
			What you need to do, is to edit the configuration file, located at <strong>simpleusers/config.inc.php</strong>, then run the install script which is located at <strong>simpleusers/install.php</strong>
		</p>

		<h2>How do I use it?</h2>

		<p>
			<strong>Include and use</strong>
		</p>
		<p class="faded">
		 You simply make sure that your sessions has been started (<a href="http://php.net/session_start">http://php.net/session_start</a>), include the su.inc.file, located at <strong>simpleusers/su.inc.php</strong>
		 and finally, you create an instance of the SimpleUsers object - now you're ready to go.
		</p>

		<p>
			<strong>Sample files are included with this package</strong>
		</p>
		<p class="faded">
			The following files are simply sample files and should not be used as useradministration `as is`.<br />
			The sample files are only for reference and learning purposes, so you can build SimpleUsers into your own application.
		</p>
			<ul>
				<li><strong><a href="users.php">users.php</a></strong> - A simple administration interface to manage registered users (login required).</li>
				<li><strong>userinfo.php</strong> - A simple administration interface to manage registered data to a specific user (login required).</li>
				<li><strong>removeinfo.php</strong> - A simple example of how to permanently remove specific data registered to a specific user (login required)</li>
				<li><strong>deleteuser.php</strong> - A simple example of how to delete users (login required).</li>
				<li><strong>changepassword.php</strong> - A simple example of how to change a users password (login required).</li>
				<li><strong><a href="logout.php">logout.php</a></strong> - A simple example of how to log out a user.</li>
				<li><strong><a href="login.php">login.php</a></strong> - A simple example of how to log in a user.</li>
				<li><strong><a href="newuser.php">newuser.php</a></strong> - A simple example of how to create a new user.</li>
				<li><strong><a href="csrf.php">csrf.php</a></strong> - A simple example of how to use and implement CSRF protection.</li>
			</ul>
		<p class="faded">
			Not all the methods available in SimpleUsers is covered in these files.<br />
			A full reference can be found in <strong>reference.html</strong>.
		</p>

		<h2>Troubleshooting</h2>

		<p>
			<strong>Please make sure you configured the config file!</strong>
		</p>
		<p class="faded">
			It might seem like a trivial thing to mention, but when things doesn't work as expected, the most likely cause is a misconfigured configuration file.<br />
			The config file is located at <strong>simpleusers/config.inc.php</strong> and is fairly simple to understand.
		</p>

		<?php if( file_exists(dirname(__FILE__)."/simpleusers/install.php") ): ?>
		<p>
			<strong>Did you run the install script?</strong>
		</p>
		<p class="faded">
			If not, you would probably want to; this will create the tables in your database needed for this to work.<br />
			The install script is located at <strong>simpleusers/install.php</strong>.
		</p>
		<p class="faded">
			If you did, you should remove or rename the file or something similar, to avoid unwelcome users having access to it.
		</p>
		<?php endif; ?>
	</body>
</html>