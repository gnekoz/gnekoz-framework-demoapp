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
class Profilo extends Controller 
{
    public function init() 
    {
        parent::init();
    }

    
    public function index() 
    {
        $errors = array();
        $user = $this->getAuth()->getCurrentUser();

        if (count($this->getRequest()->getParameters()) != 0) {
            $password = $this->getRequest()->getParameter('password');
            $repassword = $this->getRequest()->getParameter('repassword');

            if ($password != $repassword) {
                $errors[] = 'La password non coincide';
            }

            if ($password != null) {
                $user->password2 = DBUtenti::encryptPassword($password);
                $user->password = $user->password2; // TODO rimuovere
            }

            if (count($errors) == 0) {
                $user->update();
            }
        }

        $user->password = null; // Per sicurezza...
        $data = array("user" => $user,
            "errors" => $errors,
            "roles" => join(", ", $user->decodeRoles()),
            "ufficio" => $user->getUfficio());

        $renderer = new SmartyRenderer(App::getInstance());
        $renderer->setTemplate("profilo/common.tpl");
        $this->getResponse()->addOutput($renderer->render($data));
    }

}
