<?php

namespace demo\controllers;

use gnekoz\Controller;
use demo\App;
use demo\db\DBClassificazioni;
use demo\SmartyRenderer;

class Pippo extends Controller {

    private $renderer;

    public function init() 
    {
        $this->renderer = new SmartyRenderer(App::getInstance());
    }

    public function index() 
    {
//        $pass = \demo\db\DBUtenti::decryptPassword('mOjWOJ4cuZS7urKykkFUamIeXZm4e3vKPr+esnsXQyFAYQztGoAL96ptW7BWBAzi5qGuXdgFfC/u3IyBBqjMTA==');
//        $this->getResponse()->addOutput($pass);
//        $this->getResponse()->addOutput(DBClassificazioni::getDes(4));        
        setlocale(LC_MONETARY, 'it_IT');
        $prezzo = money_format('%.2n', '1234567.89');
        $this->getResponse()->addOutput($prezzo);
    }

}
