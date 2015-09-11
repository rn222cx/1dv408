<?php

require_once('model/LoginModel.php');


class LoginController {
    private $loginModel;
    private $loginView;

    public function __construct(){
        $this->loginModel = new LoginModel();
        $this->loginView = new LoginView();
    }

    public function doControl(){

//        if($this->loginView->rememberMe()){
//            $this->loginView->setCookie();
//        }
//        elseif()

        if($this->loginView->login() && !$this->checkIfLoggedIn()){
            $username = $this->loginView->getRequestUserName();
            $password = $this->loginView->getRequestPassword();

            $this->doLogin($username, $password);
        }

        if($this->loginView->logOut() && $this->checkIfLoggedIn()){
            $this->logout();
        }

        $dtv = new DateTimeView();
        $lv = new LayoutView();

        $lv->render($this->checkIfLoggedIn(), $this->loginView, $dtv);
    }

    public function doLogin($username, $password){
        // Ask the model if we are logged in
        $message = $this->loginModel->authenticate($username, $password);

        $this->loginView->returnMessages($message);
    }

    public function checkIfLoggedIn(){

        // if session message is set return string and reset message afterwards.
        if (isset($_SESSION[LoginModel::$sessionLoginMessage])) {
            $this->loginView->returnMessages($_SESSION[LoginModel::$sessionLoginMessage]);
            $this->loginModel->isLoginMessageUnset();
        }

        return $this->loginModel->IsSessionSet();
    }

    public function logout(){
        $this->loginModel->destroySession();
    }

}
?>