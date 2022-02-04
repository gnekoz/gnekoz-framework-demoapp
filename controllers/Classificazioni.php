<?php
namespace demo\controllers;

use demo\SmartyRenderer;
use demo\App;
use demo\Controller;
use demo\db\DBClassificazioni;
use demo\db\DBUtenti;

/**
 * @author Luca Stauble
 *
 */
class Classificazioni extends Controller 
{	
    const PAGE_SIZE = 12;

    public function init()
    {
        parent::init();
        $this->setRequiredRole(DBUtenti::ROLE_ADMIN);
    }
	
    public function index()
    {
        $page = (int) $this->getRequest()->getParameter("page");
        $tipo = (int) $this->getRequest()->getParameter("tipo");
        $offset = $page * self::PAGE_SIZE;
        $limit = self::PAGE_SIZE + 1;		

        $morePages = false;
        $rows = array();
        $class = new DBClassificazioni();
        $class->limit($offset, $limit);
        $class->tipo = $tipo;
        $class->orderBy('des');
        $class->find();		
        while ($class->fetch())
        {
            $rows[] = clone $class;
        }
        if (count($rows) > self::PAGE_SIZE)
        {
            $morePages = true;
            array_pop($rows);
        }		
        $class->free();

        $data = array("rows" => $rows,
                      "page" => $page, 
                      "tipo" => $tipo,
                      "desTipo" => DBClassificazioni::getDescrizioneTipo($tipo),
                      "morePages" => $morePages);

        $renderer = new SmartyRenderer(App::getInstance());
        $renderer->setTemplate("classificazioni/list.tpl");
        $this->getResponse()->addOutput($renderer->render($data));
    }
}
