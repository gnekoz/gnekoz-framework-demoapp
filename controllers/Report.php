<?php

namespace demo\controllers;

use gnekoz\rendering\FileRenderer;
use demo\App;
use demo\blg\Budget;
use demo\blg\Consumi;
use demo\blg\Consuntivazioni;
use demo\Controller;
use demo\db\DBUffici;
use demo\db\DBUtenti;
use demo\report\ReportBuilderChiamate;
use demo\report\ReportBuilderConsumi;
use demo\report\ReportBuilderConsuntivazione;
use demo\report\ReportBuilderContatti;
use demo\SmartyRenderer;

class Report extends Controller
{
    /**
     *
     */
    public function init()
    {
        parent::init();
    }


    /**
     *
     */
    public function index()
    {
        $data = array();
        $renderer = new SmartyRenderer(App::getInstance());
        $renderer->setTemplate("report/index.tpl");
        $this->getResponse()->addOutput($renderer->render($data));
    }


    /**
     *
     * @return type
     */
    public function chiamate()
    {
        $curUser = $this->getAuth()->getCurrentUser();
        if (!$curUser->hasRole(DBUtenti::ROLE_PHONEOPERATOR)) {
            $this->getResponse()->sendRedirect("/error/unauthorized");
            return;
        }

        if (count($this->getRequest()->getParameters()) > 0) {
            $from = $this->getRequest()->getParameter("from-date");
            $to = $this->getRequest()->getParameter("to-date");


            if ($from == null) {
                $from = date("Y-m-01");
            }

            if ($to == null) {
                $to = date("Y-m-d");
            }

            $composer = new ReportBuilderChiamate($from, $to);
            $file = $composer->createReport();

            $data = array('file' => $file, 'name' => basename($file));

            $renderer = new FileRenderer();
            $renderer->render($data);
            exit(); // FIXME
        }


        $renderer = new SmartyRenderer(App::getInstance());
        $renderer->setTemplate("report/chiamate.tpl");
        $this->getResponse()->addOutput($renderer->render(array()));
    }
    
    
    /**
     *
     * @return type
     */
    public function contatti()
    {
        $curUser = $this->getAuth()->getCurrentUser();
        if (!$curUser->hasRole(DBUtenti::ROLE_PHONEOPERATOR)) {
            $this->getResponse()->sendRedirect("/error/unauthorized");
            return;
        }

        $reportTypes = array(
            ReportBuilderContatti::REPORT_CONSULENTE => 'Report consulente',
            ReportBuilderContatti::REPORT_GENERALE => 'Report generale',
        );        
        
        if (count($this->getRequest()->getParameters()) > 0) {
            $from = $this->getRequest()->getParameter("from-date");
            $to = $this->getRequest()->getParameter("to-date");
            $type = $this->getRequest()->getParameter("report_type");


            if ($from == null) {
                $from = date("Y-m-01");
            }

            if ($to == null) {
                $to = date("Y-m-d");
            }

            $composer = new ReportBuilderContatti($type, $from, $to);
            $file = $composer->createReport();

            $data = array('file' => $file, 'name' => basename($file));

            $renderer = new FileRenderer();
            $renderer->render($data);
            exit(); // FIXME
        }


        $renderer = new SmartyRenderer(App::getInstance());
        $renderer->setTemplate("report/contatti.tpl");
        $this->getResponse()->addOutput($renderer->render(array(
            "reportTypes" => $reportTypes,
            "defaultReportType" => ReportBuilderContatti::REPORT_GENERALE,
        )));
    }    


    /**
     *
     * @return type
     */
    public function consumi()
    {
        $curUser = $this->getAuth()->getCurrentUser();
        if (!$curUser->hasRole(DBUtenti::ROLE_EXPENSEOPERATOR)) {
            $this->getResponse()->sendRedirect("/error/unauthorized");
            return;
        }

        if (count($this->getRequest()->getParameters()) > 0) {
            $user = $this->getRequest()->getParameter("id_utente");
            $from = $this->getRequest()->getParameter("from-date");
            $to = $this->getRequest()->getParameter("to-date");
            $flgAddebitati = $this->getRequest()->getParameter("flg_addebitati");
            
            //print_r($flgAddebitati); exit();


            if ($from == null) {
                $from = date("Y-m-01");
            }

            if ($to == null) {
                $to = date("Y-m-d");
            }


            $consumi = Consumi::getConsumi($user, $from, $to, $flgAddebitati);

            $composer = new ReportBuilderConsumi($consumi, $flgAddebitati, $user);
            $file = $composer->createReport();

            $data = array('file' => $file, 'name' => basename($file));

            $renderer = new FileRenderer();
            $renderer->render($data);
            exit(); // FIXME
        }

        $data = array(
            "users" => DBUtenti::getUsersMap(),
            "tipiAddebito" => array(
                ReportBuilderConsumi::FLG_ADDEBITATO_TUTTI => 'tutti',                
                ReportBuilderConsumi::FLG_ADDEBITATO_SI => 'addebitati',
                ReportBuilderConsumi::FLG_ADDEBITATO_NO => 'da addebitare',
            )
        );
        $renderer = new SmartyRenderer(App::getInstance());
        $renderer->setTemplate("report/consumi.tpl");
        $this->getResponse()->addOutput($renderer->render($data));
    }


    /**
     *
     * @return type
     */
    public function consuntivazione()
    {
        $monthList = array('-- tutti i mesi --', 'gennaio', 'febbraio', 'marzo',
            'aprile', 'maggio', 'giugno', 'luglio', 'agosto',
            'settembre', 'ottobre', 'novembre', 'dicembre');

        $reportTypes = array(
            ReportBuilderConsuntivazione::CONSUNTIVAZIONE_CONSULENTE => 'Riepilogo consulente',
            ReportBuilderConsuntivazione::CONSUNTIVAZIONE_GENERALE => 'Riepilogo generale',
        );

        // Lettura utente corrente
        $curUser = $this->getAuth()->getCurrentUser();
        $offices = DBUffici::getUfficiMap();
        $allowedUsers = $curUser->getChildrenMap();
        $brokerManagers = array();

        if ($curUser->hasRole(DBUtenti::ROLE_BROKEROWNER)) {
            $brokerManagers = DBUtenti::getUsersMapByRole(DBUtenti::ROLE_BROKERMANAGER);
        }


        if (count($this->getRequest()->getParameters()) > 0) {
            $user = $this->getRequest()->getParameter("id_utente");
            $office = $this->getRequest()->getParameter("id_ufficio");
            $month = $this->getRequest()->getParameter("mese");
            $year = $this->getRequest()->getParameter("anno");
            $reportType = $this->getRequest()->getParameter("report_type");
            $brokerManager = $this->getRequest()->getParameter("id_broker_manager");

            // Controllo sicurezza utenti
            if ($user == null && !$curUser->hasRole(DBUtenti::ROLE_BROKERMANAGER) && !$curUser->hasRole(DBUtenti::ROLE_BROKEROWNER)) {
                $user = $curUser->id;
            }

            // Report di default
            if ($reportType == null) {
                $reportType = ReportBuilderConsuntivazione::CONSUNTIVAZIONE_CONSULENTE;
            }

            if ($user != null && !in_array($user, array_keys($allowedUsers))) {
                $this->getResponse()->sendRedirect("/error/securityviolation");
                return;
            }

            // Controllo sicurezza ufficio (solo titolare)
            if ($office != null && !$curUser->hasRole(DBUtenti::ROLE_BROKEROWNER)) {
                $this->getResponse()->sendRedirect("/error/securityviolation");
                return;
            }


            // Costruzione descrizione criteri di selezione
            $criteria = "Report consuntivazioni";

            if ($office != null) {
                $uff = DBUffici::getUfficio($office);
                $criteria .= " ufficio '{$uff->des}'";
            }

            if ($user != null) {
                $ute = DBUtenti::getUser($user);
                $criteria .= " utente '{$ute->nominativo}'";
            }

            if ($year != null) {
                $criteria .= " anno $year";
            }


            $list = Consuntivazioni::getConsuntivazioniExt($user, $brokerManager, $office, null, $month, $year);

            $budget = Budget::getBudget($user, $brokerManager, $office, $year);

            $composer = new ReportBuilderConsuntivazione($list, $budget, $criteria, $reportType);
            $file = $composer->createReport();

            $data = array('file' => $file, 'name' => basename($file));

            $renderer = new FileRenderer();
            $renderer->render($data);
            exit(); // FIXME
        }

        // Criteri completi
        $data = array(
            "users" => $allowedUsers,
            "brokerManagers" => $brokerManagers,
            "offices" => $offices,
            "months" => $monthList,
            "reportTypes" => $reportTypes,
            "defaultReportType" => ReportBuilderConsuntivazione::CONSUNTIVAZIONE_CONSULENTE,
            "timeCriteria" => array(0 => "Report mensile", 1 => "Report di riepilogo"),
            "reportType" => array(0 => "Dettagliato", 1 => "Ristretto", 2 => "Sintetico"),
        );

        if (!$curUser->hasRole(DBUtenti::ROLE_BROKEROWNER)) {
            $data['offices'] = array();
        }


        $renderer = new SmartyRenderer(App::getInstance());
        $renderer->setTemplate("report/consuntivazione.tpl");
        $this->getResponse()->addOutput($renderer->render($data));
    }

}
