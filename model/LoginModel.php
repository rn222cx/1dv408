<?php

session_start();

class LoginModel {

    private $validUsername = 'a';
    private $validPassword = 'a';

    private static $setSessionUser = 'LoginModel::user';
    public static $sessionLoginMessage = 'LoginModel::message';


    public function authenticate($username, $password){

        if($username === $this->validUsername && $password === $this->validPassword){
            $_SESSION[self::$setSessionUser] = $username;
            $_SESSION[self::$sessionLoginMessage] = 'Welcome';
            $this->reloadPageAndstopExecution();
        }

        try{
            if(empty($username)){
                throw new Exception('Username is missing');
            }
            elseif(empty($password)){
                throw new Exception('Password is missing');
            }
            elseif($username !== $this->validUsername || $password !== $this->validPassword){
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