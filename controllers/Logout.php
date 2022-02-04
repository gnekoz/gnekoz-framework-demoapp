<?php
namespace demo\controllers;

use demo\Auth;

use gnekoz\Controller;

/**
 * @author gneko
 *
 */
class Logout extends Controller {
	
	public function index()
	{
		$auth = new Auth();
		$auth->logout();
		$this->getResponse()->sendRedirect($auth->getLoginPage());
	}
}
