<?php

require_once('model/LoginModel.php');


class LoginController {
    private $loginModel;
    private $loginView;

    public function __construct(){
        $this->loginModel = new LoginModel();
        $this->loginView = new LoginView();
    }

    public function CheckUserStatus(){
        $username = $this->loginView->getRequestUserName();
        $password = $this->loginView->getRequestPassword();

        if($this->loginView->login()){
            $this->doLogin($username, $password);
        }

        if($this->loginView->logOut()){
            $this->logout();
        }

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
        return $this->loginModel->IsSessionSet();
    }

    public function logout(){
        $message = $this->loginModel->destroySession();
        $this->loginView->returnMessages($message);
    }



}
?>