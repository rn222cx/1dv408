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

        if($this->loginView->login()){
            $username = $this->loginView->getRequestUserName();
            $password = $this->loginView->getRequestPassword();

            $this->doLogin($username, $password);
        }

//        if($this->loginView->rememberMe()){
//            $this->loginView->setCookie();
//        }
//        elseif()

        if($this->loginView->logOut())
            $this->logout();

        if($this->checkIfLoggedIn()){
            $isLoggedIn = true;
        } else{
            $isLoggedIn = false;
        }



        $dtv = new DateTimeView();
        $lv = new LayoutView();

        $lv->render($isLoggedIn, $this->loginView, $dtv);
    }

    public function doLogin($username, $password){
        // Ask the model if we are logged in
        $message = $this->loginModel->authenticate($username, $password);

        $this->loginView->returnMessages($message);
    }

    public function checkIfLoggedIn(){
        // If user is logged in return welcome message
       // if($this->loginModel->IsSessionSet()){
        if (isset($_SESSION[LoginModel::$sessionLoginMessage])) {
            $this->loginView->returnMessages($_SESSION[LoginModel::$sessionLoginMessage]);
            $this->loginModel->isLoginMessageUnset();
        }
        return $this->loginModel->IsSessionSet();
    }

    public function logout(){
        $this->loginModel->destroySession();
        //$this->loginView->returnMessages($message);
    }

}
?>