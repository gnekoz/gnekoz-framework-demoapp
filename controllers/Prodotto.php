<?php
namespace demo\controllers;

use demo\db\DBProdotti;
use demo\App;
use demo\Controller;
use demo\SmartyRenderer;
use demo\db\DBUtenti;


/**
 * @author gneko
 *
 */
class Prodotto extends Controller {
	
	public function init()
	{
		parent::init();
        $this->setRequiredRole(DBUtenti::ROLE_EXPENSEOPERATOR);
	}
	
	
	public function index()
	{
		$data = array("product" => new DBProdotti());
		
		$renderer = new SmartyRenderer(App::getInstance());
		$renderer->setTemplate("prodotto/new.tpl");
		$this->getResponse()->addOutput($renderer->render($data));		
	} 
	
	public function edit()
	{
		$prod = new DBProdotti();
		$prod->setFromRequest($this->getRequest());
		$data = array("product" => $prod);
		
		$renderer = new SmartyRenderer(App::getInstance());
		$renderer->setTemplate("prodotto/edit.tpl");
		$this->getResponse()->addOutput($renderer->render($data));		
	}
		
	
	public function save()
	{
		$prod = new DBProdotti();
		$prod->setFromRequest($this->getRequest());
		
		if ($prod->validate())
		{
			$prod->save();
			if (!$prod->hasErrors())
			{
				$this->getResponse()->sendRedirect("/prodotti");
			}
		}
		
		$data = array("product" => $prod, "errors" => $prod->getErrors());
		
		$renderer = new SmartyRenderer(App::getInstance());
		$renderer->setTemplate("prodotto/save.tpl");
		$this->getResponse()->addOutput($renderer->render($data));		
	}
	
	
	public function delete()
	{
		$prod = new DBProdotti();
		$prod->setFromRequest($this->getRequest());
		
		if ($prod->id != null)
		{
			$prod->delete();
				
			// FIXME
				
			$this->getResponse()->sendRedirect("/prodotti");
		}
	}
}
