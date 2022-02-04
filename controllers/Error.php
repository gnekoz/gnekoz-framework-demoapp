<?php
namespace demo\controllers;

use demo\App;
use demo\Controller;
use demo\SmartyRenderer;

/**
 * @author gneko
 *
 */
class Error extends Controller {

	public function Unauthorized()
	{
		$renderer = new SmartyRenderer(App::getInstance());
		$renderer->setTemplate("error/unauthorized.tpl");
		$this->getResponse()->addOutput($renderer->render(array()));
	}
	
	public function Securityviolation()
	{
		$renderer = new SmartyRenderer(App::getInstance());
		$renderer->setTemplate("error/securityviolation.tpl");
		$this->getResponse()->addOutput($renderer->render(array()));
	}	
}
