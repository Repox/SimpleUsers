<?php

/**
 * This file is part of SimpleUsers.
 *
 */

/**
 * SimpleUsers is a small-scale and flexible way of adding
 * user management to your website or web applications.
 */
class SimpleUsers
{

    private $mysqli, $stmt;
    private $sessionName = "SimpleUsers";
    public $logged_in = false;
    public $userdata;

    /**
     * Object construct verifies that a session has been started and that a MySQL connection can be established.
     * It takes no parameters.
     *
     * @throws Exception
     */

    public function __construct()
    {
        $sessionId = session_id();
        if (strlen($sessionId) == 0) {
            throw new Exception("No session has been started.\n<br />Please add `session_start();` initially in your file before any output.");
        }

        $this->mysqli = new mysqli($GLOBALS["mysql_hostname"], $GLOBALS["mysql_username"], $GLOBALS["mysql_password"], $GLOBALS["mysql_database"]);
        if ($this->mysqli->connect_error) {
            throw new Exception("MySQL connection could not be established: " . $this->mysqli->connect_error);
        }

        $this->_validateUser();
        $this->_populateUserdata();
        $this->_updateActivity();
    }

    /**
     * Returns a (int)user id, if the user was created succesfully.
     * If not, it returns (bool)false.
     *
     * @param $username
     * @param $password
     * @return bool|int
     * @throws Exception
     */

    public function createUser($username, $password)
    {
        $password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users VALUES (NULL, ?, ?, NOW(), NOW())";
        if (!$this->stmt = $this->mysqli->prepare($sql)) {
            throw new Exception("MySQL Prepare statement failed: " . $this->mysqli->error);
        }

        $this->stmt->bind_param("ss", $username, $password);
        if ($this->stmt->execute()) {
            return $this->stmt->insert_id;
        }

        return false;
    }

    /**
     * Pairs up username and password as registrered in the database.
     * If the username and password is correct, it will return (int)user id of
     * the user which credentials has been passed and set the session, for
     * use by the user validating.
     *
     * @param $username
     * @param $password
     * @return bool
     * @throws Exception
     */

    public function loginUser($username, $password)
    {
        $sql = "SELECT userId, uPassword FROM users WHERE uUsername=? LIMIT 1";
        if (!$this->stmt = $this->mysqli->prepare($sql)) {
            throw new Exception("MySQL Prepare statement failed: " . $this->mysqli->error);
        }

        $this->stmt->bind_param("s", $username);
        $this->stmt->execute();
        $this->stmt->store_result();

        if ($this->stmt->num_rows == 0) {
            return false;
        }

        $this->stmt->bind_result($userId, $userPassword);
        $this->stmt->fetch();


        if(!password_verify($password, $userPassword)) {
            return false;
        }

        $_SESSION[$this->sessionName]["userId"] = $userId;
        $this->logged_in = true;

        return $userId;
    }

    /**
     * Sets an information pair, consisting of a key name and that keys value.
     * The information can be retrieved with this objects getInfo() method.
     *
     * @param $key
     * @param $value
     * @param null $userId Can be used if administrative control is needed
     * @return bool
     * @throws Exception
     */

    public function setInfo($key, $value, $userId = null)
    {
        if ($userId == null) {
            if (!$this->logged_in)
                return false;
        }

        $reservedKeys = array("userId", "uUsername", "uActivity", "uCreated", "uLevel");
        if (in_array($key, $reservedKeys))
            throw new Exception("User information key \"" . $key . "\" is reserved for internal use!");

        if ($userId == null)
            $userId = $_SESSION[$this->sessionName]["userId"];

        if ($this->getInfo($key, $userId)) {
            $sql = "UPDATE users_information SET infoValue=? WHERE infoKey=? AND userId=? LIMIT 1";
            if (!$this->stmt = $this->mysqli->prepare($sql)) {
                throw new Exception("MySQL Prepare statement failed: " . $this->mysqli->error);
            }

            $this->stmt->bind_param("ssi", $value, $key, $userId);
            $this->stmt->execute();
        } else {
            $sql = "INSERT INTO users_information VALUES (?, ?, ?)";
            if (!$this->stmt = $this->mysqli->prepare($sql)) {
                throw new Exception("MySQL Prepare statement failed: " . $this->mysqli->error);
            }

            $this->stmt->bind_param("iss", $userId, $key, $value);
            $this->stmt->execute();
        }

        return true;
    }

    /**
     * Use this function to retrieve user information attached to a certain user
     * that has been set by using this objects setInfo() method.
     *
     * @param $key
     * @param null $userId Can be used if administrative control is needed
     * @return bool|string
     * @throws Exception
     */

    public function getInfo($key, $userId = null)
    {

        if ($userId == null) {
            if (!$this->logged_in)
                return false;

            $userId = $_SESSION[$this->sessionName]["userId"];
        }

        $sql = "SELECT infoValue FROM users_information WHERE userId=? AND infoKey=? LIMIT 1";
        if (!$this->stmt = $this->mysqli->prepare($sql)) {
            throw new Exception("MySQL Prepare statement failed: " . $this->mysqli->error);
        }

        $this->stmt->bind_param("is", $userId, $key);
        $this->stmt->execute();
        $this->stmt->store_result();

        if ($this->stmt->num_rows == 0) {
            return "";
        }

        $this->stmt->bind_result($value);
        $this->stmt->fetch();

        return $value;

    }

    /**
     * Use this function to permanently remove information attached to a certain user
     * that has been set by using this objects setInfo() method.
     *
     * @param $key
     * @param null $userId Can be used if administrative control is needed
     * @return bool
     * @throws Exception
     */

    public function removeInfo($key, $userId = null)
    {

        if ($userId == null) {
            if (!$this->logged_in) {
                return false;
            }

            $userId = $_SESSION[$this->sessionName]["userId"];
        }

        $sql = "DELETE FROM users_information WHERE userId=? AND infoKey=? LIMIT 1";
        if (!$this->stmt = $this->mysqli->prepare($sql)) {
            throw new Exception("MySQL Prepare statement failed: " . $this->mysqli->error);
        }

        $this->stmt->bind_param("is", $userId, $key);
        $this->stmt->execute();

        if ($this->stmt->affected_rows > 0) {
            return true;
        }

        return false;
    }


    /**
     * Use this function to retrieve all user information attached to a certain user
     * that has been set by using this objects setInfo() method into an array.
     *
     * @param null $userId
     * @return array|bool
     * @throws Exception
     */

    public function getInfoArray($userId = null)
    {
        if ($userId == null) {

            if (!$this->logged_in) {
                return false;
            }

            $userId = $_SESSION[$this->sessionName]["userId"];
        }

        $sql = "SELECT infoKey, infoValue FROM users_information WHERE userId=? ORDER BY infoKey ASC";
        if (!$this->stmt = $this->mysqli->prepare($sql)) {
            throw new Exception("MySQL Prepare statement failed: " . $this->mysqli->error);
        }

        $this->stmt->bind_param("i", $userId);
        $this->stmt->execute();
        $this->stmt->store_result();

        $userInfo = array();
        if ($this->stmt->num_rows > 0) {
            $this->stmt->bind_result($key, $value);
            while ($this->stmt->fetch()) {
                $userInfo[$key] = $value;
            }
        }

        $user = $this->getSingleUser($userId);
        $userInfo = array_merge($userInfo, $user);
        asort($userInfo);

        return $userInfo;
    }

    /**
     * Logout the active user, unsetting the userId session.
     * This is a void function
     */

    public function logoutUser()
    {
        if (isset($_SESSION[$this->sessionName])) {
            unset($_SESSION[$this->sessionName]);
        }

        $this->logged_in = false;
    }

    /**
     * Update the users password with this function.
     * Generates a new salt and a sets the users password with the given parameter
     *
     * @param $password
     * @param null $userId Can be used if administrative control is needed
     * @return bool
     * @throws Exception
     */

    public function setPassword($password, $userId = null)
    {

        if ($userId == null) {
            $userId = $_SESSION[$this->sessionName]["userId"];
        }

        $password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "UPDATE users SET uPassword=? WHERE userId=? LIMIT 1";
        if (!$this->stmt = $this->mysqli->prepare($sql)) {
            throw new Exception("MySQL Prepare statement failed: " . $this->mysqli->error);
        }

        $this->stmt->bind_param("si", $password,$userId);
        return $this->stmt->execute();
    }

    /**
     * Returns an array with each user in the database.
     *
     * @return array
     */

    public function getUsers()
    {

        $sql = "SELECT DISTINCT userId, uUsername, uActivity, uCreated FROM users ORDER BY uUsername ASC";

        if (!$this->stmt = $this->mysqli->prepare($sql)) {
            throw new Exception("MySQL Prepare statement failed: " . $this->mysqli->error);
        }

        $this->stmt->execute();
        $this->stmt->store_result();

        if ($this->stmt->num_rows == 0) {
            return array();
        }

        $this->stmt->bind_result($userId, $username, $activity, $created);

        $users = array();

        $i = 0;
        while ($this->stmt->fetch()) {
            $users[$i]["userId"] = $userId;
            $users[$i]["uUsername"] = $username;
            $users[$i]["uActivity"] = $activity;
            $users[$i]["uCreated"] = $created;

            $i++;
        }

        return $users;

    }

    /**
     * Gets the basic info for a single user based on the userId
     *
     * @param null $userId Can be used if administrative control is needed
     * @return bool
     * @throws Exception
     */

    public function getSingleUser($userId = null)
    {

        if ($userId == null) {
            $userId = $_SESSION[$this->sessionName]["userId"];
        }

        $sql = "SELECT userId, uUsername, uActivity, uCreated FROM users WHERE userId=? LIMIT 1";
        if (!$this->stmt = $this->mysqli->prepare($sql)) {
            throw new Exception("MySQL Prepare statement failed: " . $this->mysqli->error);
        }

        $this->stmt->bind_param("i", $userId);
        $this->stmt->execute();
        $this->stmt->store_result();

        if ($this->stmt->num_rows == 0) {
            return false;
        }

        $this->stmt->bind_result($userId, $username, $activity, $created);
        $this->stmt->fetch();

        $user["userId"] = $userId;
        $user["uUsername"] = $username;
        $user["uActivity"] = $activity;
        $user["uCreated"] = $created;

        return $user;

    }

    /**
     * Deletes all information regarding a user.
     * This is a void function.
     *
     * @param $userId
     * @throws Exception
     */

    public function deleteUser($userId)
    {
        $sql = "DELETE FROM users WHERE userId=?";
        if (!$this->stmt = $this->mysqli->prepare($sql)) {
            throw new Exception("MySQL Prepare statement failed: " . $this->mysqli->error);
        }

        $this->stmt->bind_param("i", $userId);
        $this->stmt->execute();

        $sql = "DELETE FROM users_information WHERE userId=?";
        if (!$this->stmt = $this->mysqli->prepare($sql)) {
            throw new Exception("MySQL Prepare statement failed: " . $this->mysqli->error);
        }

        $this->stmt->bind_param("i", $userId);
        $this->stmt->execute();
    }

    /**
     * Returns a hidden input field with a unique token value
     * for CSRF to be used with post data.
     * The token is saved in a session for later validation.
     *
     * @param bool $xhtml
     * @return string
     */

    public function getToken($xhtml = true)
    {
        $token = uniqid($this->sessionName, true);
        $name = "token_" . md5($token);

        $_SESSION[$this->sessionName]["csrf_name"] = $name;
        $_SESSION[$this->sessionName]["csrf_token"] = $token;

        $string = "<input type=\"hidden\" name=\"" . $name . "\" value=\"" . $token . "\"";
        if ($xhtml)
            $string .= " />";
        else
            $string .= ">";

        return $string;
    }

    /**
     * Use this method when you wish to validate the CSRF token from your post data.
     * The method returns true upon validation, otherwise false.
     *
     * @return bool
     */

    public function validateToken()
    {
        $name = $_SESSION[$this->sessionName]["csrf_name"];
        $token = $_SESSION[$this->sessionName]["csrf_token"];
        unset($_SESSION[$this->sessionName]["csrf_token"]);
        unset($_SESSION[$this->sessionName]["csrf_name"]);

        if ($_POST[$name] == $token)
            return true;

        return false;
    }

    /**
     * This function updates the users last activity time
     * This is a void function.
     */

    private function _updateActivity()
    {
        if (!$this->logged_in) {
            return;
        }

        $userId = $_SESSION[$this->sessionName]["userId"];

        $sql = "UPDATE users SET uActivity=NOW() WHERE userId=? LIMIT 1";
        if (!$this->stmt = $this->mysqli->prepare($sql)) {
            throw new Exception("MySQL Prepare statement failed: " . $this->mysqli->error);
        }

        $this->stmt->bind_param("i", $userId);
        $this->stmt->execute();

    }

    /**
     * Validates if the user is logged in or not.
     * This is a void function.
     */

    private function _validateUser()
    {
        if (!isset($_SESSION[$this->sessionName]["userId"])) {
            return;
        }

        if (!$this->_validateUserId()) {
            return;
        }

        $this->logged_in = true;
    }

    /**
     * Validates if the user id, in the session is still valid.
     *
     * @return bool
     * @throws Exception
     */

    private function _validateUserId()
    {
        $userId = $_SESSION[$this->sessionName]["userId"];

        $sql = "SELECT userId FROM users WHERE userId=? LIMIT 1";
        if (!$this->stmt = $this->mysqli->prepare($sql)) {
            throw new Exception("MySQL Prepare statement failed: " . $this->mysqli->error);
        }

        $this->stmt->bind_param("i", $userId);
        $this->stmt->execute();
        $this->stmt->store_result();

        if ($this->stmt->num_rows == 1) {
            return true;
        }

        $this->logoutUser();

        return false;
    }

    /**
     * Populates the current users data information for
     * quick access as an object.
     *
     * @return void
     * @throws Exception
     */

    private function _populateUserdata()
    {
        $this->userdata = array();

        if ($this->logged_in) {
            $data = $this->getInfoArray();
            foreach ($data as $key => $value) {
                $this->userdata[$key] = $value;
            }
        }
    }

}

?>