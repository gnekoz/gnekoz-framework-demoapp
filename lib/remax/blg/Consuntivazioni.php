<?php

namespace demo\blg;

use DateInterval;
use DateTime;
use DB_DataObject;
use demo\Auth;
use demo\db\DBConsuntivazioni;
use demo\db\DBUtenti;

class Consuntivazioni
{

    /**
     * Ritorna l'elenco delle consuntivazioni per l'utente indicato e la
     * settimana a cui appartiene la data specificata
     * @param integer $userID - Chiave primaria dell'utente
     * @param string $date - Data in formato ISO
     * @return Un array di righe della tabella <code>consuntivazioni</code>
     */
    public static function getConsuntivazioni($userID, $date)
    {
        // Controllo parametri
        if ($userID == null || $date == null) {
            return array();
        }

        //DB_DataObject::debugLevel(5);
        // Creazione parametri intervallo date
        $dt = DateTime::createFromFormat("Y-m-d", $date);
        $dow = ($dt->format('w') + 6) % 7;
        $dtBegin = DateTime::createFromFormat("Y-m-d", $date)->sub(new DateInterval("P{$dow}D"));
        $dtEnd = DateTime::createFromFormat("Y-m-d", $dtBegin->format("Y-m-d"))->add(new DateInterval("P6D"));

        // Preparazione lista vuota
        $result = array();
        $fakeBegin = new DateTime($dtBegin->format("Y-m-d"));
        for ($day = 0; $day < 7; $day++) {
            $curDate = $fakeBegin->format("Y-m-d");
            $result[$curDate] = new DBConsuntivazioni();
            $result[$curDate]->data = $curDate;
            $result[$curDate]->id_utente = $userID;
            $curDate = $fakeBegin->add(new DateInterval("P1D"));
        }

        // Lettura da db e valorizzazione righe nella lista
        $cons = new DBConsuntivazioni();
        $cons->id_utente = $userID;
        $cons->whereAdd("data >= '" . $dtBegin->format("Y-m-d") . "'");
        $cons->whereAdd("data <= '" . $dtEnd->format("Y-m-d") . "'");
        $cons->find();
        while ($cons->fetch()) {
            $result[$cons->data] = clone $cons;
        }
        $cons->free();

        return $result;
    }

    /**
     * FIXME il nome e forse si può ricondurre tutto a questo metodo
     * @param unknown $user
     * @param unknown $office
     * @param unknown $data
     * @param unknown $month
     * @param unknown $year
     */
    public static function getConsuntivazioniExt($user = null, $brokerManager = null, $office = null, $date = null, $month = null, $year = null)
    {
        $curUser = Auth::getCurrentUser();
        $dtBegin = null;
        $dtEnd = null;

        // Controllo parametri
        if ($date == null && $year == null) {
            return array();
        }

        if ($user != null) {
            $office = null;
        }


        if ($date != null) {
            $dt = DateTime::createFromFormat("Y-m-d", $date);
            $dow = ($dt->format('w') + 6) % 7;
            $dtBegin = DateTime::createFromFormat("Y-m-d", $date)->sub(new DateInterval("P{$dow}D"));
            $dtEnd = DateTime::createFromFormat("Y-m-d", $dtBegin->format("Y-m-d"))->add(new DateInterval("P6D"));
        }

        if ($month > 0) {
            $dtBegin = DateTime::createFromFormat("Y-n-d", "$year-$month-01");
            $dtEnd = DateTime::createFromFormat("Y-m-d", $dtBegin->format("Y-m-t"));
        }

        if ($month == 0 && $year != null) {
            $dtBegin = DateTime::createFromFormat("Y-m-d", "$year-01-01");
            $dtEnd = DateTime::createFromFormat("Y-m-d", "$year-12-31");
        }


        // Costruzione criteri di selezione
        $where = "data >= '" . $dtBegin->format("Y-m-d") . "'"
                . " AND data <= '" . $dtEnd->format("Y-m-d") . "'"
                . " AND coalesce(u.flg_disabilitato, 0) = 0";

        /*
         * Se non è stato indicato alcun utente ma l'utente è un broker manager
         * forzo il criterio di selezione per broker titolare
         */
        if ($user == null
            && $curUser->hasRole(DBUtenti::ROLE_BROKERMANAGER)
            && !$curUser->hasRole(DBUtenti::ROLE_ADMIN))
        {
            $brokerManager = $curUser->id;
        }

        if ($user != null) {
            $where .= " AND id_utente = $user";
        }

        if ($brokerManager != null) {
            $where .= " AND (id_utente = {$brokerManager} OR
                        id_utente IN (SELECT id FROM utenti WHERE id_responsabile = {$brokerManager}))";
        }


        if ($office != null) {
            $where .= " AND id_utente in (select id from utenti where id_ufficio = $office)";
        }

        $query = <<<EOT
			SELECT
                            extract(month from c.data) as mese,
                            u.id as id_utente,
                            u.nominativo as nominativo,
                            sum(coalesce(c.gen_nuo_con, 0)) as gen_nuo_con,
                            sum(coalesce(c.gen_not, 0)) as gen_not,
                            sum(coalesce(c.gen_ric_spe, 0)) as gen_ric_spe,
                            sum(coalesce(c.gen_inc, 0)) as gen_inc,
                            sum(coalesce(c.app_ven, 0)) as app_ven,
                            sum(coalesce(c.app_aff, 0)) as app_aff,
                            sum(coalesce(c.app_acq, 0)) as app_acq,
                            sum(coalesce(c.pro_acq, 0)) as pro_acq,
                            sum(coalesce(c.pro_acq_col, 0)) as pro_acq_col,
                            sum(coalesce(c.pro_loc, 0)) as pro_loc,
                            sum(coalesce(c.pro_loc_col, 0)) as pro_loc_col,
                            sum(coalesce(c.tra_ven, 0)) as tra_ven,
                            sum(coalesce(c.tra_aff, 0)) as tra_aff
  			FROM consuntivazioni c
                        JOIN utenti u on u.id = c.id_utente
  			WHERE $where
  			GROUP BY u.id, u.nominativo, mese
  			ORDER BY u.id, mese
EOT;
        //echo $query; exit();
        $result = array();
        $dao = new DB_DataObject();
        //$dao->debugLevel(5);
        $dao->query($query);
        while ($dao->fetch()) {
            if (!isset($result[$dao->id_utente])) {
                $result[$dao->id_utente] = array(
                    'nominativo' => $dao->nominativo,
                    'id' => $dao->id_utente,
                    'consuntivazioni' => array()
                );
            }
//            if (!isset($result[$dao->id_utente]['consuntivazioni'][$dao->mese])) {
//                $result[$dao->id_utente]['consuntivazioni'][$dao->mese] = array();
//            }
            $result[$dao->id_utente]['consuntivazioni'][$dao->mese] = clone $dao;
        }
        $dao->free();

        //var_dump($result); exit();


        return $result;
    }

}
