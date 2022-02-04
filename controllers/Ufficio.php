<?php
namespace demo\controllers;

use demo\App;
use demo\Controller;
use demo\db\DBUffici;
use demo\db\DBUtenti;
use demo\SmartyRenderer;


/**
 * @author gneko
 *
 */
class Ufficio extends Controller {
	
	public function init()
	{
		parent::init();
        $this->setRequiredRole(DBUtenti::ROLE_BROKEROWNER);
	}
	
	
	public function index()
	{		
		$data = array("ufficio" => new DBUffici());
		
		$renderer = new SmartyRenderer(App::getInstance());
		$renderer->setTemplate("ufficio/new.tpl");
		$this->getResponse()->addOutput($renderer->render($data));		
	} 
	
	public function edit()
	{
		$ufficio = DBUffici::getUfficio($this->getRequest()->getParameter('id'));
		$data = array("ufficio" => $ufficio);
		
		$renderer = new SmartyRenderer(App::getInstance());
		$renderer->setTemplate("ufficio/edit.tpl");
		$this->getResponse()->addOutput($renderer->render($data));		
	}
		
	
	public function save()
	{
		$ufficio = new DBUffici();
		$ufficio->setFromRequest($this->getRequest());
		
		if ($ufficio->validate())
		{
			$ufficio->save();
			if (!$ufficio->hasErrors())
			{
				$this->getResponse()->sendRedirect("/uffici");
			}
		}					 
		
		$data = array("ufficio" => $ufficio,
				      "errors" => $ufficio->getErrors());
		
		$renderer = new SmartyRenderer(App::getInstance());
		$renderer->setTemplate("ufficio/save.tpl");
		$this->getResponse()->addOutput($renderer->render($data));		
	}
	
	
	public function delete()
	{
		$ufficio = new DBUffici();
		$ufficio->setFromRequest($this->getRequest());
		
		if ($ufficio->id != null)
		{
			$ufficio->delete();
				
			// FIXME
				
			$this->getResponse()->sendRedirect("/uffici");
		}		
	}
}
