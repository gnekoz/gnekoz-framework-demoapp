<?php
namespace demo\controllers;

use demo\db\DBUtenti;
use demo\SmartyRenderer;
use demo\App;
use demo\Controller;


/**
 * @author gneko
 *
 */
class Utenti extends Controller 
{
	public function init()
	{
		parent::init();
        $this->setRequiredRole(DBUtenti::ROLE_BROKEROWNER);
	}
	
	public function index()
	{			
		$users = array();
		$user = new DBUtenti();
		$user->orderBy("nominativo ASC");
		$user->find();
		$i = 0;				
		while ($user->fetch())
		{
			$users[$i] = clone $user;
			$users[$i]->rolesDes = join(", ", $user->decodeRoles());
			$users[$i++]->ufficioDes = $user->getUfficio()->des;
		}
		$data = array("users" => $users);
		
		//var_dump($users); exit();
		
		$renderer = new SmartyRenderer(App::getInstance());
		$renderer->setTemplate("utenti/list.tpl");
		$this->getResponse()->addOutput($renderer->render($data));
	}
}
