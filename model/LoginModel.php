<?php

session_start();

require_once('dal/Db.php');

class LoginModel {

    private static $setSessionUser = 'LoginModel::user';
    public static $sessionLoginMessage = 'LoginModel::message';

    /**
     * Check if the login credentials are correct through the database
     *
     * @param $username
     * @param $password
     * @return bool
     */
    public function authenticate($username, $password){

        $records = new Db();
        $records->query('SELECT username,password FROM users WHERE BINARY username = :username');
        $records->bind(':username', $username);
        $results = $records->single();

        if(count($results) > 0 && password_verify($password, $results['password'])){
            return $_SESSION[self::$setSessionUser] = $results['username'];
        }
        else{
            return false;
        }

    }

    public function isSessionSet(){
        return isset($_SESSION[self::$setSessionUser]);
    }

    public function forceLogout($message)
    {
        $_SESSION[self::$sessionLoginMessage] = $message;
        $_SESSION[ self::$setSessionUser ] = false;
    }

    public function destroySession($message){
        unset($_SESSION[self::$setSessionUser]);
        $_SESSION[self::$sessionLoginMessage] = $message;
    }

    /**
     * Return message in session and removes it afterwards
     * @return mixed
     */
    public function unsetSessionMessage() {
        if (isset($_SESSION[self::$sessionLoginMessage])) {
            $message = $_SESSION[self::$sessionLoginMessage];
            $_SESSION[self::$sessionLoginMessage] = null;
            return $message;
        }

    }

    public function setSessionMessage($message){
        $_SESSION[self::$sessionLoginMessage] = $message;
    }

    public function setSessionFromCookie(){
        $_SESSION[self::$setSessionUser] = $_COOKIE['LoginView::CookieName'];
    }

    public function setCookiePassword(){
        return password_hash("random", PASSWORD_BCRYPT);
    }

    public function setCookieTime(){
       // return time() + (7 * 24 * 60 * 60); // a week
        return strtotime('tomorrow');
    }

    /**
     * @param $password
     * @param $time
     */
    public function updateValuesInDatabase($password, $time, $browser){ // Should be better with array in future projects

        $database = new Db();
        $username = $_SESSION[self::$setSessionUser];
        $database->query('UPDATE users SET cookie_password = :cookie_password, coockie_date = :cookie_date, browser = :browser WHERE username = :username');

        $database->bind(':username', $username);
        $database->bind(':cookie_password', $password);
        $database->bind(':cookie_date', $time);
        $database->bind(':browser', $browser);
        $database->execute();
    }

    public function updateSingleValueInDatabase($browser){

        $database = new Db();
        $username = $_SESSION[self::$setSessionUser];
        $database->query('UPDATE users SET browser = :browser WHERE username = :username');

        $database->bind(':username', $username);
        $database->bind(':browser', $browser);
        $database->execute();
    }

    /**
     * @return array
     */
    public function selectRowInDatabase(){

        $database = new Db();
        // use username from session if session isset or else use username from cookie
        $username = $this->isSessionSet() ? $_SESSION[self::$setSessionUser] : $_COOKIE['LoginView::CookieName'];

        $database->query('SELECT username ,cookie_password, coockie_date, browser FROM users WHERE username = :username');
        $database->bind(':username', $username);
        $row = $database->single();

        $var1 = $row['cookie_password'];
        $var2 = $row['coockie_date'];
        $var3 = $row['username'];
        $var4 = $row['browser'];
        return array($var1, $var2, $var3, $var4);
    }

}
?>