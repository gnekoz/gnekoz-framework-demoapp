<?php
namespace demo\controllers;

use demo\SmartyRenderer;
use demo\App;
use demo\Controller;
use demo\db\DBProdotti;
use demo\db\DBUtenti;

/**
 * @author gneko
 *
 */
class Prodotti extends Controller 
{	
	const PAGE_SIZE = 12;
	
	public function init()
	{
		parent::init();
        $this->setRequiredRole(DBUtenti::ROLE_EXPENSEOPERATOR);
	}
	
	public function index()
	{
		$page = (int) $this->getRequest()->getParameter("page");
		$offset = $page * self::PAGE_SIZE;
		$limit = self::PAGE_SIZE + 1;		
		
		$morePages = false;
		$prods = array();
		$prod = new DBProdotti();
		$prod->limit($offset, $limit);
		$prod->orderBy('des');
		$prod->find();		
		while ($prod->fetch())
		{
			$prods[] = clone $prod;
		}
		if (count($prods) > self::PAGE_SIZE)
		{
			$morePages = true;
			array_pop($prods);
		}		
		$prod->free();
		
		$data = array("products" => $prods,
				      "page" => $page, 
				      "morePages" => $morePages);
		
		$renderer = new SmartyRenderer(App::getInstance());
		$renderer->setTemplate("prodotti/list.tpl");
		$this->getResponse()->addOutput($renderer->render($data));
	}
}
