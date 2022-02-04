<?php
namespace demo\controllers;

use demo\db\DBUtenti;
use demo\App;
use demo\Controller;
use demo\db\DBConsumi;
use demo\SmartyRenderer;

/**
 * @author gneko
 *
 */
class Consumi extends Controller {

	const PAGE_SIZE = 10;

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

		$criteria = "";
		$userID = $this->getRequest()->getParameter("id_utente");
		if ($userID != null)
		{
		  $criteria = "where id_utente = $userID";
		}


		$query = <<<EOT
		select 
                    c.*, 
                    concat(p.des, case when c.note is not null then concat(' - ', c.note) else '' end) as prodotto,
                    --p.des as prodotto,
                    u.nominativo as utente
		from consumi c
		inner join prodotti p on p.id = c.id_prodotto
		inner join utenti u on u.id = c.id_utente
		$criteria
		order by c.data DESC, u.nominativo ASC
		limit $limit
		offset $offset
EOT;

		$morePages = false;
		$consumi = array();
		$consumo = new DBConsumi();
		$consumo->query($query);
		while ($consumo->fetch())
		{
			$consumi[] = clone $consumo;
		}
		if (count($consumi) > self::PAGE_SIZE)
		{
			$morePages = true;
			array_pop($consumi);
		}

// 		$utente = Utenti::getUser($userID);
//     var_dump($utente); exit();

		$data = array("consumi" => $consumi,
		              "utente" => DBUtenti::getUser($userID),
		              "allUsers" => DBUtenti::getUsersMap(false),
				      "page" => $page,
					  "morePages" => $morePages);

		$renderer = new SmartyRenderer(App::getInstance());
		$renderer->setTemplate("consumi/list.tpl");
		$this->getResponse()->addOutput($renderer->render($data));
	}
}
