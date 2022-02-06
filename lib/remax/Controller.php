<?php
namespace demo;

use demo\db\DBUtenti;

/**
 * @author gneko
 *
 */
class Controller extends \gnekoz\Controller 
{
	private $auth;
	
	
	public function init()
	{
		$this->auth = new Auth();
		if (!$this->auth->isAuthenticated())
		{
			$this->getResponse()->sendRedirect($this->auth->getLoginPage());
			return;
		}
	}
	
	
	protected function setRequiredRole($role)
	{
		$user = $this->auth->getCurrentUser();
		
		if ($user->hasRole(DBUtenti::ROLE_ADMIN)) {
			return;
		}
				
		if (!$user->hasRole($role)) {
			$this->getResponse()->sendRedirect("/error/unauthorized");
		}
			
	}
	
	
	protected function getAuth()
	{
		return $this->auth;
	}
}
