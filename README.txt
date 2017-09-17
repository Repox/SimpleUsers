SimpleUsers v0.1

SimpleUsers needs PHP 5, MySQL (no special version needed) and the MySQL Improved extension (http://php.net/mysqli).

SKILLS
Basic knowledge of PHP is required. Assuming that you know basic PHP, you most likely know your way around HTML.

IS IT SECURE
SimpleUsers doesn't store passwords in clear text, which makes the application secure for you AND your users.
Every user gets a unique 128 character long salt prepended to their chosen password, which then gets stored in the database as a SHA1 hash. The hashing is one way.

Additionally, to avoid SQL injection attacks, SimpleUsers uses prepared statements, when communicating with the database.

CSRF (Cross-site request forgery) protection has been integrated - the method doesn't require user is logged as CSRF also could be prefered in other situations. The CSRF protection works only with POST data (as GET data already is too easy to manipulate).

Please be aware that SimpleUsers isn't a fullblown application; it's not safer than what you use SimpleUsers in. When developing your application, think of SimpleUsers as an aid in adding user management to your application.

HOW TO INSTALL
It's simple.
What you need to do is edit the configuration file, located at simpleusers/config.inc.php, then run the install script which is located at simpleusers/install.php
As an alternative to the install script, edit the configuration file. The tables can be created manually, by opening tables.sql and pasting the tables into phpMyAdmin.

HOW DO I USE IT?
Include and use
You simply make sure that your session has been started (http://php.net/session_start), include the su.inc.file, located at simpleusers/su.inc.php and finally, you create an instance of the SimpleUsers object - now you're ready to go.

Sample files are included with this package

The following files are simply sample files and should not be used as useradministration `as is`.
The sample files are only for reference and learning purposes, so you can build SimpleUsers into your own application.
 - users.php - A simple administration interface to manage registered users (login required).
 - userinfo.php - A simple administration interface to manage registered data to a specific user (login required).
 - removeinfo.php - A simple example of how to permanently remove specific data registered to a specific user (login required).
 - deleteuser.php - A simple example of how to delete users (login required).
 - changepassword.php - A simple example of how to change a users password (login required).
 - logout.php - A simple example of how to log out a user.
 - login.php - A simple example of how to log in a user.
 - newuser.php - A simple example of how to create a new user.
 - csrf.php - A simple example of how to use and implement CSRF protection.
		
Not all the methods available in SimpleUsers is covered in these files.
A full reference can be found in reference.html.


TROUBLESHOOTING
Please make sure you configure the config file!
It might seem like a trivial thing to mention, but when things doesn't work as expected, the most likely cause is a misconfigured configuration file.
The config file is located at simpleusers/config.inc.php and is fairly simple to understand.

Did you run the install script?
If not, you would probably want to; this will create the tables in your database needed for this to work.
The install script is located at simpleusers/install.php.
If you did, you should remove or rename the file or something similar, to avoid unwelcome users having access to it.
