<?php

namespace demo\controllers;

use demo\db\DBUtenti;
use demo\App;
use demo\Controller;
use demo\db\DBContatti;
use demo\SmartyRenderer;

/**
 * @author Luca Stauble
 *
 */
class Contatti extends Controller 
{

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
select
  c.*,
  u.nominativo as nome_destinatario,
  u.email as email_destinatario,
  cl0.des as des_tipo_contatto,
  cl1.des as des_tipo_richiesta
from contatti c
left join utenti u on u.id = c.id_utente_destinatario
left join classificazioni cl0 on cl0.id = c.id_tipo_contatto
left join classificazioni cl1 on cl1.id = c.id_tipo_richiesta
order by c.data DESC, u.nominativo ASC
limit $limit
offset $offset
EOT;

        $morePages = false;
        $contatti = array();
        $contatto = new DBContatti();
        $contatto->query($query);
        while ($contatto->fetch()) {
            $contatti[] = clone $contatto;
        }

        //var_dump($contatti); exit();
        
        if (count($contatti) > self::PAGE_SIZE) {
            $morePages = true;
            array_pop($contatti);
        }

        $data = array(
            "contatti" => $contatti,
            "page" => $page,
            "morePages" => $morePages);

        $renderer = new SmartyRenderer(App::getInstance());
        $renderer->setTemplate("contatti/list.tpl");
        $this->getResponse()->addOutput($renderer->render($data));
    }

}
