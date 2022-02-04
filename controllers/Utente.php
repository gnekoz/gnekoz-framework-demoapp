<?php

namespace demo\controllers;

use demo\App;
use demo\Controller;
use demo\db\DBBudget;
use demo\db\DBUffici;
use demo\db\DBUtenti;
use demo\SmartyRenderer;
use \NumberFormatter;

/**
 * @author gneko
 *
 */
class Utente extends Controller
{

    public function init()
    {
        parent::init();
        $this->setRequiredRole(DBUtenti::ROLE_BROKEROWNER);
    }

    public function index()
    {
        $data = array("user" => new DBUtenti(),
            "allUsers" => DBUtenti::getUsersMap(true),
            "allRoles" => DBUtenti::getRoles(),
            "uffici" => DBUffici::getUfficiMap(),
            "budget" => new DBBudget());

        $renderer = new SmartyRenderer(App::getInstance());
        $renderer->setTemplate("utente/new.tpl");
        $this->getResponse()->addOutput($renderer->render($data));
    }

    public function edit()
    {
        $user = new DBUtenti();
        $user->setFromRequest($this->getRequest());
        $user->password = null; // Per sicurezza...
        $data = array("user" => $user,
            "userRoles" => $user->decodeRoles(),
            "allUsers" => DBUtenti::getUsersMap(true),
            "allRoles" => DBUtenti::getRoles(),
            "uffici" => DBUffici::getUfficiMap(),
            "budget" => $user->getBudget(date("Y")));

        $renderer = new SmartyRenderer(App::getInstance());
        $renderer->setTemplate("utente/edit.tpl");
        $this->getResponse()->addOutput($renderer->render($data));
    }

    public function save()
    {
        $user = new DBUtenti();
        $user->setFromRequest($this->getRequest());

        // Controllo inserimento password
        if ($this->getRequest()->getParameter("pass") != $this->getRequest()->getParameter("repass")) {
            // Fa un pò schifetto ma è + comodo
            $user->addError("La password non coincide");
        } else if ($this->getRequest()->getParameter("pass") != null) {
            $user->password2 = DBUtenti::encryptPassword($this->getRequest()->getParameter("pass"));
            $user->password = $user->password2; // TODO rimuovere
        }

        // Settaggio ruoli
        $ruoli = $this->getRequest()->getParameter("user-roles");
        $user->encodeRoles($ruoli);


        // Continuo la porcheria...
        if ($user->validate()) {
            $user->save();
            if (!$user->hasErrors()) {

                // Salvataggio budget
                $budget = new DBBudget;
                $budget->id_utente = $user->id;
                $budget->anno = date("Y");

                $id = $this->getRequest()->getParameter('budget_id');
                if ($id != null) {
                    $budget->id = $id;
                    if ($budget->find() != 1) {
                        $user->addError("Impossibile trovare il budget indicato");
                    }
                    $budget->fetch();
                }

                $importo = $this->getRequest()->getParameter('budget_importo');
                $nf = NumberFormatter::create("it_IT", NumberFormatter::DECIMAL);
                $budget->importo = $nf->parse($importo);

                $budget->save();

                if (!$budget->hasErrors()) {
                    $this->getResponse()->sendRedirect("/utenti");
                    return;
                }

                $user->addErrors($budget->getErrors());
            }
        }


        $data = array("user" => $user,
            "errors" => $user->getErrors(),
            "userRoles" => $user->decodeRoles(),
            "allUsers" => DBUtenti::getUsersMap(true),
            "allRoles" => DBUtenti::getRoles(),
            "uffici" => DBUffici::getUfficiMap(),
            "budget" => $user->getBudget(date("Y")));

        $renderer = new SmartyRenderer(App::getInstance());
        $renderer->setTemplate("utente/save.tpl");
        $this->getResponse()->addOutput($renderer->render($data));
    }

    public function delete()
    {
        $user = new DBUtenti();
        $user->setFromRequest($this->getRequest());

        if ($user->id != null) {
            $user->delete();

            // FIXME

            $this->getResponse()->sendRedirect("/utenti");
        }
    }

}
