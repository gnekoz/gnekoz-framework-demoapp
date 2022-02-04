<?php
namespace demo\controllers;

use demo\App;
use demo\SmartyRenderer;
use demo\db\DBUffici;
use demo\db\DBUtenti;
use demo\Controller;

/**
 * @author gneko
 *
 */
class Uffici extends Controller 
{	
	public function init()
	{
		parent::init();
        $this->setRequiredRole(DBUtenti::ROLE_BROKEROWNER);
	}
	
	public function index()
	{
		$list = DBUffici::getUffici();
		$renderer = new SmartyRenderer(App::getInstance());
		$renderer->setTemplate("uffici/list.tpl");
		$this->getResponse()->addOutput($renderer->render(array("uffici" => $list)));
	}
}
