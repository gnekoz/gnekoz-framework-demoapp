<?php

namespace demo;

use \DB_DataObject;
use \DateTime;
use \DateInterval;

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
            $result[$curDate] = DB_DataObject::factory('consuntivazioni');
            $result[$curDate]->data = $curDate;
            $curDate = $fakeBegin->add(new DateInterval("P1D"));
        }

        // Lettura da db e valorizzazione righe nella lista
        $cons = DB_DataObject::factory('consuntivazioni');
        $cons->id_utente = $userID;
        $cons->whereAdd("data >= '" . $dtBegin->format("Y-m-d") . "'");
        $cons->whereAdd("data <= '" . $dtEnd->format("Y-m-d") . "'");
        $cons->find();
        while ($cons->fetch()) {
            $result[$cons->data] = clone $cons;
            //$result[$cons->data] = self::updateComputedFields($result[$cons->data]);
        }
        $cons->free();

        return $result;
    }

//    public static function updateComputedFields($row)
//    {
//        $row->perc_cv_nv = 0.0;
//        $row->perc_iv_aa = 0.0;
//        $row->perc_iv_nv = 0.0;
//        $row->perc_ca_na = 0.0;
//        $row->perc_av_ca = 0.0;
//        $row->perc_ia_na = 0.0;
//        $row->perc_av_pa = 0.0;
//        $row->perc_pa_paa = 0.0;
//
//        if ($row->nv != 0)
//            $row->perc_cv_nv = ((double) $row->cv / (double) $row->nv) * 100;
//
//        if ($row->aa != 0)
//            $row->perc_iv_aa = ((double) $row->iv / (double) $row->aa) * 100;
//
//        if ($row->nv != 0)
//            $row->perc_iv_nv = ((double) $row->iv / (double) $row->nv) * 100;
//
//        if ($row->na != 0)
//            $row->perc_ca_na = ((double) $row->ca / (double) $row->na) * 100;
//
//        if ($row->ca != 0)
//            $row->perc_av_ca = ((double) $row->av / (double) $row->ca) * 100;
//
//        if ($row->na != 0)
//            $row->perc_ia_na = ((double) $row->ia / (double) $row->na) * 100;
//
//        if ($row->pa != 0)
//            $row->perc_av_pa = ((double) $row->av / (double) $row->pa) * 100;
//
//        if ($row->paa != 0)
//            $row->perc_pa_paa = ((double) $row->pa / (double) $row->paa) * 100;
//
//        return $row;
//    }

    public static function extractFormData($formData)
    {
        $result = array();

        // Ciclo sulle righe. Il conteggio si basa sulle occorrenze del campo ID 
        for ($i = 0; $i < count($formData['id']); $i++) {
            $cons = DB_DataObject::factory('consuntivazioni');
            foreach ($formData as $name => $values) {
                // Salto campi non appartenenti alla tabella
                if (count($values) < 2)
                    continue;

                $cons->$name = $values[$i];
            }
            $result[$i] = $cons;
        }
        //var_dump($result); exit();
        return $result;
    }

    public static function extractFormRowData($formData)
    {
        $cons = DB_DataObject::factory('consuntivazioni');
        foreach ($formData as $name => $val) {
            $cons->$name = $val[0];
        }
        return $cons;
    }

}
