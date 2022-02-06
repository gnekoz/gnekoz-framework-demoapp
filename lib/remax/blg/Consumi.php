<?php

namespace demo\blg;

use DB_DataObject;
use demo\report\ReportBuilderConsumi;

class Consumi
{
    public static function getConsumi($user = null, $from, $to, $flgAddebitati)
    {
        $result = array();

        $query = <<<EOT
   SELECT
       u.nominativo,
       c.data,
       concat(p.des, case when c.note is not null then concat(' - ', c.note) else '' end) as des_prodotto,
       c.quantita,
       c.prezzo_unitario,
       c.flg_addebitato,                
       c.quantita * c.prezzo_unitario as importo
   FROM consumi c
   JOIN utenti u on u.id = c.id_utente
   JOIN prodotti p on p.id = c.id_prodotto
   WHERE
       c.data >= '$from'
       AND c.data <= '$to'
       AND coalesce(u.flg_disabilitato, 0) = 0
EOT;
        
        if ($flgAddebitati == ReportBuilderConsumi::FLG_ADDEBITATO_SI) {
            $query .= " AND c.flg_addebitato = 1";
        } else if ($flgAddebitati == ReportBuilderConsumi::FLG_ADDEBITATO_NO) {
            $query .= " AND c.flg_addebitato = 0";
        }
        
        if ($user != null)
        {
            $query .= " AND c.id_utente = $user";
        }

        $query .= " ORDER BY c.data ASC, u.nominativo ASC";

        //print_r($query); exit();
        
        $consumi = new DB_DataObject();
        $consumi->query($query);
        while ($consumi->fetch())
        {
            if (!isset($result[$consumi->nominativo]))
            {
                $result[$consumi->nominativo] = array();
            }
            $result[$consumi->nominativo][] = clone $consumi;
        }
        $consumi->free();

        return $result;
    }
}
?>
