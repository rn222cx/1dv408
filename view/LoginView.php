<?php


class LoginView {
	private static $login = 'LoginView::Login';
	private static $logout = 'LoginView::Logout';
	private static $name = 'LoginView::UserName';
	private static $password = 'LoginView::Password';
	private static $cookieName = 'LoginView::CookieName';
	private static $cookiePassword = 'LoginView::CookiePassword';
	private static $keep = 'LoginView::KeepMeLoggedIn';
	private static $messageId = 'LoginView::Message';

    private $returnMessages;
    private $loginModel;
	private $cookieStorage;

    public function __construct(LoginModel $loginModel){
        $this->loginModel = $loginModel;
		$this->cookieStorage = new view\CookieStorage();
	}

	/**
	 * Create HTTP response
	 *
	 * Should be called after a login attempt has been determined
	 *
	 * @return  void BUT writes to standard output and cookies!
	 */
	public function response() {
		$message = $this->returnMessages;

        if($this->checkIfLoggedIn()){
			$response = $this->generateLogoutButtonHTML($message);
		}
        else{
			$response = $this->generateLoginFormHTML($message);
        }

		return $response;
	}

	/**
	* Generate HTML code on the output buffer for the logout button
	* @param $message, String output message
	* @return  void, BUT writes to standard output!
	*/
	private function generateLogoutButtonHTML($message) {
		return '
			<form  method="post" >
				<p id="' . self::$messageId . '">' . $message .'</p>
				<input type="submit" name="' . self::$logout . '" value="logout"/>
			</form>
		';

	}

	/**
	* Generate HTML code on the output buffer for the logout button
	* @param $message, String output message
	* @return  void, BUT writes to standard output!
	*/
	private function generateLoginFormHTML($message)
    {
        return '
			<form method="post" >
				<fieldset>
					<legend>Login - enter Username and password</legend>
					<p id="' . self::$messageId . '">' . $message . '</p>
					
					<label for="' . self::$name . '">Username :</label>
					<input type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $this->getRequestUserName() . '" />

					<label for="' . self::$password . '">Password :</label>
					<input type="password" id="' . self::$password . '" name="' . self::$password . '" />

					<label for="' . self::$keep . '">Keep me logged in  :</label>
					<input type="checkbox" id="' . self::$keep . '" name="' . self::$keep . '" />
					
					<input type="submit" name="' . self::$login . '" value="login" />
				</fieldset>
			</form>
		';
    }

	//CREATE GET-FUNCTIONS TO FETCH REQUEST VARIABLES
	public function getRequestUserName() {
        if(isset($_POST[self::$name])){
            return trim($_POST[self::$name]);
        }
	}

    public function getRequestPassword() {
        if(isset($_POST[self::$password])){
            return trim($_POST[self::$password]);
        }
    }

    public function userLoggingIn(){
        return isset($_POST[self::$login]);
    }

    public function userLoggingOut(){
        return isset($_POST[self::$logout]);
    }

    public function checkIfLoggedIn(){
        return $this->loginModel->isSessionSet() || $this->doCookieExist();
    }

    public function returnMessages($message){
        return $this->returnMessages = $message;
    }

	public function rememberMe(){
		return isset($_POST[self::$keep]);
	}

	public function setCookiePassword($hashedPassword, $time){
		$this->cookieStorage->save(self::$cookiePassword, $hashedPassword, $time);
	}

    public function setCookieName($time){
        $username = $this->getRequestUserName() ? : $_COOKIE[self::$cookieName];
        $this->cookieStorage->save(self::$cookieName, $username, $time);
    }

    public function doCookieExist(){
       return $this->cookieStorage->load(self::$cookiePassword) && $this->cookieStorage->load(self::$cookieName);
    }

    public function removeCookie(){
        $this->cookieStorage->remove(self::$cookieName);
        $this->cookieStorage->remove(self::$cookiePassword);
    }

    /**
     * Check if cookie has the same value as in database
     *
     * @return bool
     */
    public function checkingManipulatedCookies($cookie){
        if($this->cookieStorage->load(self::$cookiePassword) !== $cookie){
            return true;
        }

        return false;
    }


}