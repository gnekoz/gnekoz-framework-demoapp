<?php

namespace demo\controllers;

use demo\db\DBClassificazioni;
use demo\App;
use demo\Controller;
use demo\SmartyRenderer;
use demo\db\DBUtenti;

/**
 * @author LucaStauble
 *
 */
class Classificazione extends Controller 
{
    public function init() 
    {
        parent::init();
        $this->setRequiredRole(DBUtenti::ROLE_ADMIN);
    }

    
    public function index() 
    {
        $tipo = (int) $this->getRequest()->getParameter("tipo");
        $class = new DBClassificazioni();
        $class->tipo = $tipo;
        $data = array(
            "classificazione" => $class,
            "desTipo" => DBClassificazioni::getDescrizioneTipo($tipo)
        );
                
        $renderer = new SmartyRenderer(App::getInstance());
        $renderer->setTemplate("classificazione/new.tpl");
        $this->getResponse()->addOutput($renderer->render($data));
    }

    
    public function edit() 
    {
        $class = new DBClassificazioni();
        $class->setFromRequest($this->getRequest());
        
        $data = array(
            "classificazione" => $class,
            "desTipo" => DBClassificazioni::getDescrizioneTipo($class->tipo)
        );

        $renderer = new SmartyRenderer(App::getInstance());
        $renderer->setTemplate("classificazione/edit.tpl");
        $this->getResponse()->addOutput($renderer->render($data));
    }

    public function save() 
    {
        $class = new DBClassificazioni();
        $class->setFromRequest($this->getRequest());

        if ($class->validate()) {
            $class->save();
            if (!$class->hasErrors()) {
                $this->getResponse()->sendRedirect("/classificazioni?tipo={$class->tipo}");
            }
        }

        $data = array(
            "classificazione" => $class,
            "desTipo" => DBClassificazioni::getDescrizioneTipo($class->tipo),
            "errors" => $class->getErrors());

        $renderer = new SmartyRenderer(App::getInstance());
        $renderer->setTemplate("classificazione/save.tpl");
        $this->getResponse()->addOutput($renderer->render($data));
    }

    
    public function delete() 
    {
        $class = new DBClassificazioni();
        $class->setFromRequest($this->getRequest());

        if ($class->id != null) {
            $class->delete();

            // FIXME

            $this->getResponse()->sendRedirect("/classificazioni");
        }
    }
}
