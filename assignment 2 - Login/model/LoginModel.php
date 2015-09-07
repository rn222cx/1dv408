<?php

session_start();

class LoginModel {

    private $validUsername = 'Admin';
    private $validPassword = 'Password';


    public static $setSessionUser = 'LoginModel::user';

//    public function __construct(){
//
//    }

    public function authenticate($username, $password){

        if($username === $this->validUsername && $password === $this->validPassword){
            $_SESSION[self::$setSessionUser] = $username;
            return 'Welcome';
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

    public static function destroySession(){
        unset($_SESSION[self::$setSessionUser]);
        return 'Bye bye!';
    }

}
?>