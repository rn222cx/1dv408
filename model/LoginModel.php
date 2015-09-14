<?php

session_start();

require_once('dal/Db.php');

class LoginModel {

    private static $setSessionUser = 'LoginModel::user';
    public static $sessionLoginMessage = 'LoginModel::message';

    public function authenticate($username, $password){

        if(empty($username)){
            throw new Exception('Username is missing');
        }
        elseif(empty($password)){
            throw new Exception('Password is missing');
        }

        // Check if user and hashed password exist in the database
        $records = new Db();
        $records->query('SELECT username,password FROM users WHERE BINARY username = :username');
        $records->bind(':username', $username);
        $results = $records->single();

        if(count($results) > 0 && password_verify($password, $results['password'])){
            $_SESSION[self::$setSessionUser] = $results['username'];
            $_SESSION[self::$sessionLoginMessage] = 'Welcome';
            return true;
        }
        else{
            throw new Exception('Wrong name or password');
        }

    }

    public function isSessionSet(){
        return isset($_SESSION[self::$setSessionUser]);
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

    public function setCookieMessage($message){
        $_SESSION[self::$sessionLoginMessage] = $message;
    }

    public function setSessionFromCookie(){
        $_SESSION[self::$setSessionUser] = $_COOKIE['LoginView::CookieName'];
    }

    public function setCookiePassword(){
        return password_hash("random", PASSWORD_BCRYPT);
    }

    public function setCookieTime(){
        return time() + (7 * 24 * 60 * 60); // a week
    }

    public function updateCookiesInDatabase($password, $time){

        $database = new Db();

        $username = $_SESSION[self::$setSessionUser];
        $database->query('UPDATE users SET cookie_password = :cookie_password, coockie_date = :cookie_date WHERE username = :username');

        $database->bind(':username', $username);
        $database->bind(':cookie_password', $password);
        $database->bind(':cookie_date', $time);
        $database->execute();
    }

    public function selectRowInDatabase(){

        $database = new Db();
        // use username from session if session isset or else use username from cookie
        $username = $this->isSessionSet() ? $_SESSION[self::$setSessionUser] : $_COOKIE['LoginView::CookieName'];

        $database->query('SELECT cookie_password FROM users WHERE username = :username');
        $database->bind(':username', $username);
        $row = $database->single();

        return $row['cookie_password'];
    }

}
?>