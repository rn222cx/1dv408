<?php

session_start();

require_once('dal/Db.php');

class LoginModel {

    private static $setSessionUser = 'LoginModel::user';
    public static $sessionLoginMessage = 'LoginModel::message';


    public function authenticate($username, $password){

        try{
            if(empty($username)){
                throw new Exception('Username is missing');
            }
            elseif(empty($password)){
                throw new Exception('Password is missing');
            }

            //echo $hash = password_hash($password, PASSWORD_BCRYPT);

            // Check if user and password exist in the database
            $records = new Db();
            $records->query('SELECT username,password FROM users WHERE BINARY username = :username');
            $records->bind(':username', $username);
            $results = $records->single();

            if(count($results) > 0 && password_verify($password, $results['password'])){
                $_SESSION[self::$setSessionUser] = $results['username'];
                $_SESSION[self::$sessionLoginMessage] = 'Welcome';
                $this->reloadPageAndstopExecution();
            }
            else{
                throw new Exception('Wrong name or password');
            }

        }catch (Exception $ex){
            return $ex->getMessage();
        }

    }

    public function IsSessionSet(){
        return isset($_SESSION[self::$setSessionUser]);
    }

    public function destroySession(){
        unset($_SESSION[self::$setSessionUser]);
        $_SESSION[self::$sessionLoginMessage] = 'Bye bye!';
        $this->reloadPageAndstopExecution();
    }

    public function isLoginMessageUnset() {
        // Return welcome message and unset it afterwards
        if (isset($_SESSION[self::$sessionLoginMessage])) {
            $message = $_SESSION[self::$sessionLoginMessage];
            $_SESSION[self::$sessionLoginMessage] = null;
            return $message;
        }

    }

    private function reloadPageAndstopExecution(){
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
    }


}
?>