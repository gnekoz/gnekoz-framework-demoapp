<?php
namespace demo\controllers;

use demo\Auth;
use demo\App;
use demo\SmartyRenderer;
use gnekoz\Controller;

class Login extends Controller {
	
	private $renderer;
	
	public function init() {
		$this->renderer = new SmartyRenderer(App::getInstance());
	}
	
	public function index() {
		$this->renderer->setTemplate("login/index.tpl");
		$this->getResponse()->addOutput($this->renderer->render(array()));		
	} 
	
	public function check() {
		$auth = new Auth();
		$ret = $auth->login($this->getRequest()->getParameter("username"),
				                $this->getRequest()->getParameter("password"));
		if ($ret) {
			$this->getResponse()->sendRedirect("/home");			
		}
		$data = array("errors" => "Autenticazione fallita",
				          "username" => $this->getRequest()->getParameter("username")); 
		$this->renderer->setTemplate("login/error.tpl");		
		$this->getResponse()->addOutput($this->renderer->render($data));
	}
	
	public function logout() {
		$auth = new Application_Auth();
		$auth->logout();
	}
}
