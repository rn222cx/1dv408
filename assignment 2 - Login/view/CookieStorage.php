<?php



class CookieStorage {

    public function save($cookieName, $key){
        setcookie( $cookieName, $key, -1);
    }

    public function load($cookieName){
        $ret = isset($_COOKIE[$cookieName]) ? $_COOKIE[$cookieName] : "";

        setcookie($cookieName, "", time() -1);
        return $ret;
    }
}