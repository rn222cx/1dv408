<?php

require_once('model/LoginModel.php');


class LoginController {
    private $loginModel;
    private $loginView;

    public function __construct(){
        $this->loginModel = new LoginModel();
        $this->loginView = new LoginView($this->loginModel);
    }

    public function doControl(){

        if($this->loginView->userLoggingIn() && !$this->checkIfLoggedIn()){
            $username = $this->loginView->getRequestUserName();
            $password = $this->loginView->getRequestPassword();

            $this->doLogin($username, $password);
        }

        if($this->loginView->userLoggingOut() && $this->checkIfLoggedIn()){
            $this->logout('Bye bye!');
        }

        if($this->loginView->doCookieExist()){
            $getCookieFromDatabase = $this->loginModel->selectRowInDatabase();
            $this->updateCookies(); // Set new cookies

            if($this->loginView->checkingManipulatedCookies($getCookieFromDatabase)){
                $this->logout('Wrong information in cookies');
            }
        }


        $dtv = new DateTimeView();
        $lv = new LayoutView();

        $lv->render($this->checkIfLoggedIn(), $this->loginView, $dtv);
    }

    /**
     * Tries to login with given values.
     *
     * @param $username
     * @param $password
     */
    public function doLogin($username, $password){

        try {
            $loginSuccess = $this->loginModel->authenticate($username, $password);
        } catch (Exception $e) {
            // an error occurred
            $loginSuccess = false;
            $this->loginView->returnMessages($e->getMessage());
        }

        if($loginSuccess){
            if($this->loginView->rememberMe())
                $this->updateCookies();

            $this->reloadPageAndStopExecution();
        }
    }


    public function checkIfLoggedIn(){

        // if session message is set return string and reset message afterwards.
        if (isset($_SESSION[LoginModel::$sessionLoginMessage])) {
            $this->loginView->returnMessages($_SESSION[LoginModel::$sessionLoginMessage]);
            $this->loginModel->unsetSessionMessage();
        }

        return $this->loginModel->isSessionSet() || $this->loginView->doCookieExist();
    }


    public function logout($name){
        $this->loginModel->destroySession($name);
        $this->loginView->removeCookie();
        $this->reloadPageAndStopExecution();
    }


    public function updateCookies(){

        if(!$this->loginModel->isSessionSet()){
            $this->loginModel->setCookieMessage('Welcome back with cookie');
            $this->loginModel->setSessionFromCookie();
        }
        elseif($this->loginView->rememberMe()){
            $this->loginModel->setCookieMessage('Welcome and you will be remembered');
        }

        $cookieTime = $this->loginModel->setCookieTime();
        $cookiePassword = $this->loginModel->setCookiePassword();

        $this->loginModel->updateCookiesInDatabase($cookiePassword, $cookieTime);
        $this->loginView->setCookieName($cookieTime);
        $this->loginView->setCookiePassword($cookiePassword, $cookieTime);
    }


    public function reloadPageAndStopExecution(){
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
    }

}
?>