<?php
namespace demo\controllers;

use demo\db\DBUtenti;
use demo\App;
use demo\Controller;
use demo\db\DBChiamate;
use demo\SmartyRenderer;

/**
 * @author gneko
 *
 */
class Chiamate extends Controller {
	
	const PAGE_SIZE = 15;
	
	public function init()
	{
		parent::init();
		$this->setRequiredRole(DBUtenti::ROLE_PHONEOPERATOR);
	} 
	
	public function index()
	{
		$page = (int) $this->getRequest()->getParameter("page");
		$offset = $page * self::PAGE_SIZE;
		$limit = self::PAGE_SIZE + 1;
				
		$query = <<<EOT
select c.*,
  u.nominativo as nome_destinatario,
  u.email as email_destinatario
from chiamate c
left  join utenti u on u.id = c.id_utente_destinatario
order by c.data DESC, u.nominativo ASC
limit $limit
offset $offset
EOT;
		
		$morePages = false; 
		$chiamate = array();
		$chiamata = new DBChiamate();
		$chiamata->query($query);
		while ($chiamata->fetch())
		{
			$chiamate[] = clone $chiamata;			
		}
		
		if (count($chiamate) > self::PAGE_SIZE)
		{
			$morePages = true;
			array_pop($chiamate);
		}
		
		$data = array("chiamate" => $chiamate,
				      "page" => $page,
					  "morePages" => $morePages);
		
		$renderer = new SmartyRenderer(App::getInstance());
		$renderer->setTemplate("chiamate/list.tpl");
		$this->getResponse()->addOutput($renderer->render($data));
	}
}
