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

        if($this->loginView->userWantsToLogin() && !$this->checkIfLoggedIn()){
            $username = $this->loginView->getRequestUserName();
            $password = $this->loginView->getRequestPassword();

            $this->doLogin($username, $password);
        }

        if($this->loginView->userWantsToLogout() && $this->checkIfLoggedIn()){
            $this->logout($this->loginView->successfulLogoutMessage());
        }

        if($this->loginView->doCookieExist()){
            $this->updateCookies(); // Set new cookies
            $getCookieFromDatabase = $this->loginModel->selectRowInDatabase();
            if($message = $this->loginView->checkingManipulatedCookies($getCookieFromDatabase)){
                $this->logout($message);
            }
            //$this->updateCookies(); // Set new cookies
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

        if($this->loginView->MissingInput())
            return;

        if($this->loginModel->authenticate($username, $password) == true) {
            $this->loginModel->updateSingleValueInDatabase($this->loginView->getUsersBrowser());
            $this->loginModel->setSessionMessage($this->loginView->successfulLoginMessage());

            if($this->loginView->rememberMe())
                $this->updateCookies();

            $this->loginView->reloadPageAndStopExecution();
        }
        else{
            return $this->loginView->wrongLoginCredentialsMessage();
        }
    }


    /**
     *
     * @return bool
     */
    public function checkIfLoggedIn(){

        // if session message is set return string and reset message afterwards.
        if (isset($_SESSION[LoginModel::$sessionLoginMessage])) {
            $this->loginView->returnMessages($_SESSION[LoginModel::$sessionLoginMessage]);
            $this->loginModel->unsetSessionMessage();
        }
        // If session is not hijacked and session or cookie is set then it returns true.
        if($this->loginModel->isSessionSet() || $this->loginView->doCookieExist()){
            if($this->loginView->checkIfSessionIsHijacked() == false)
                return true;
        }
        return false;

    }


    public function logout($name){
        $this->loginModel->destroySession($name);
        $this->loginView->removeCookie();
        $this->loginView->reloadPageAndStopExecution();
    }


    public function updateCookies(){

        if(!$this->loginModel->isSessionSet()){
            $this->loginModel->setSessionMessage($this->loginView->loginWithCookiesMessage());
            $this->loginModel->setSessionFromCookie();
        }
        elseif($this->loginView->rememberMe()){
            $this->loginModel->setSessionMessage($this->loginView->loginWithCookiesMessage());
        }

        $cookieTime = $this->loginModel->setCookieTime();
        $cookiePassword = $this->loginModel->setCookiePassword();
        $usersBrowser = $this->loginView->getUsersBrowser();

        $this->loginModel->updateValuesInDatabase($cookiePassword, $cookieTime, $usersBrowser);
        $this->loginView->setCookieName($cookieTime);
        $this->loginView->setCookiePassword($cookiePassword, $cookieTime);
    }

}
?>