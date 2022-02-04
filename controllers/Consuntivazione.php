<?php

namespace demo\controllers;

use DateInterval;
use DateTime;
use DB_DataObject;
use demo\App;
use demo\blg\Consuntivazioni;
use demo\Controller;
use demo\db\DBConsuntivazioni;
use demo\db\DBUtenti;
use demo\report\ReportBuilderConsuntivazione;
use demo\SmartyRenderer;

/**
 * @author gneko
 *
 */
class Consuntivazione extends Controller
{

    public function init()
    {
        parent::init();
    }

    public function index()
    {
        //var_dump($this->getRequest()->getParameters()); exit();

        $errors = array();

        // Utente corrente
        $curUser = $this->getAuth()->getCurrentUser();

        // Utenti di competenza dell'utente corrente
        $allowedUsers = $curUser->getChildrenMap();

        // ID degli utenti di competenza dell'utente corrente
        $allowedUsersID = array_keys($allowedUsers);

        //var_dump($allowedUsersID); exit();
        // Lettura parametri visualizzazione corrente
        $displayUser = $this->getRequest()->getParameter("display-user");
        $displayDate = $this->getRequest()->getParameter("display-date");
        $displayOffice = $this->getRequest()->getParameter("display-office");

        // Lettura parametri criteri di ricerca
        $searchUser = $this->getRequest()->getParameter("search-user");
        $searchDate = $this->getRequest()->getParameter("search-date");
        $searchOffice = $this->getRequest()->getParameter("search-office");

        // Verifiche di sicurezza per eventuali hacking della pagina
        // Verifica utente
        if ($displayUser != null && !in_array($displayUser, $allowedUsersID)) {
            $this->getResponse()->sendRedirect("/error/securityviolation");
            return;
        }

        // Lettura righe modificate
        $alteredRows = $this->extractAlteredFormRows();
        $allRows = $this->extractFormRows();

        // Salvataggio eventuali righe modificate
        $dbo = new DB_DataObject();
        $dbo->query("begin trans");
        foreach ($alteredRows as $row) {
            // Controllo di sicurezza
            if (!in_array($row->id_utente, $allowedUsersID)) {
                $this->getResponse()->sendRedirect("/error/securityviolation");
                return;
            }

            if (!$row->validate()) {
                $dbo->query("rollback");
                $errors += $row->getErrors();
            } else {
                $row->save();
                if ($row->hasErrors()) {
                    $dbo->query("rollback");
                    $errors += $row->getErrors();
                }
            }
        }

        // Commit transazione
        if (count($alteredRows) > 0 && count($errors) == 0) {
            $dbo->query("commit");
        }

        // In caso di errori viene mostrata nuovamente la pagina
        if (count($errors) > 0) {
            $data = array("user" => $displayUser,
                "date" => $displayDate,
                "users" => $allowedUsers,
                "errors" => $errors,
                "list" => $allRows,
                "reportType" => ReportBuilderConsuntivazione::CONSUNTIVAZIONE_CONSULENTE,
                "prevDate" => $this->getPrevPageDate($displayDate),
                "nextDate" => $this->getNextPageDate($displayDate));

            $this->showPage($data);
            return;
        }


        // Valori di default
        if ($searchUser == null) {
            $searchUser = $curUser->id;
        }

        if ($searchDate == null) {
            $searchDate = date("Y-m-d");
        }

        $list = array();

        // Controllo di sicurezza
        if (!in_array($searchUser, $allowedUsersID)) {
            $this->getResponse()->sendRedirect("/error/securityviolation");
            return;
        } else {
            $list = Consuntivazioni::getConsuntivazioni($searchUser, $searchDate);
        }

        $user = DBUtenti::getUser($searchUser);

        $data = array("user" => $user,
            "date" => $searchDate,
            "users" => $allowedUsers,
            "errors" => $errors,
            "list" => $list,
            "reportType" => ReportBuilderConsuntivazione::CONSUNTIVAZIONE_CONSULENTE,
            "prevDate" => $this->getPrevPageDate($searchDate),
            "nextDate" => $this->getNextPageDate($searchDate));

        $this->showPage($data);
    }

    private function getNextPageDate($curDate)
    {
        return DateTime::createFromFormat("Y-m-d", $curDate)->add(new DateInterval("P7D"))->format("Y-m-d");
    }

    private function getPrevPageDate($curDate)
    {
        return DateTime::createFromFormat("Y-m-d", $curDate)->sub(new DateInterval("P7D"))->format("Y-m-d");
    }

    private function showPage($data)
    {
        //$data['mem'] = memory_get_usage(true);
        $renderer = new SmartyRenderer(App::getInstance());
        $renderer->setTemplate('consuntivazione/list.tpl');
        $this->getResponse()->addOutput($renderer->render($data));
    }

    private function extractAlteredFormRows()
    {
        $formData = $this->getRequest()->getParameters();

        $result = array();

        // Controllo esistenza righe
        if (!isset($formData['id']))
            return $result;

        // Ciclo sulle righe. Il conteggio si basa sulle occorrenze del campo ID
        for ($i = 0; $i < count($formData['id']); $i++) {
            if ($formData['_modified'][$i] != '1') {
                continue;
            }

            $cons = new DBConsuntivazioni();
            foreach ($formData as $name => $values) {
                // Salto campi non appartenenti alla tabella
                if (count($values) < 2)
                    continue;

//                if ($name == 'fatturato') {
//                    $nf = NumberFormatter::create("it_IT", NumberFormatter::DECIMAL);
//                    $values[$i] = $nf->parse($values[$i]);
//                }
                $cons->$name = $values[$i];
            }
            $result[$i] = $cons;
        }
        //var_dump($result); exit();
        return $result;
    }

    private function extractFormRows()
    {
        $formData = $this->getRequest()->getParameters();

        $result = array();

        // Controllo esistenza righe
        if (!isset($formData['id']))
            return $result;

        // Ciclo sulle righe. Il conteggio si basa sulle occorrenze del campo ID
        for ($i = 0; $i < count($formData['id']); $i++) {

            $cons = new DBConsuntivazioni();
            foreach ($formData as $name => $values) {

                // Salto campi non appartenenti alla tabella
                if (count($values) < 2)
                    continue;

//                if ($name == 'fatturato') {
//                    $nf = \NumberFormatter::create("it_IT", NumberFormatter::DECIMAL);
//                    $values[$i] = $nf->parse($values[$i]);
//                }

                $cons->$name = $values[$i];
            }
            $result[$i] = $cons;
        }
        //var_dump($result); exit();
        return $result;
    }

}
