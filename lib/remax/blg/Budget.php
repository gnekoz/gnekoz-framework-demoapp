<?php

namespace demo\blg;

use demo\db\DBUtenti;
use demo\Auth;
use \DB_DataObject;

/**
 * Description of Budget
 *
 * @author gneko
 */
class Budget
{
    public static function getBudget($user, $brokerManager, $office, $year)
    {
        $query = <<<EOF
   SELECT sum(b.importo) as importo
   FROM budget b
   WHERE b.anno = $year
EOF;
        
        $auth = new Auth();
        $curUser = $auth->getCurrentUser();
        $where = "";
        
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
        
        if ($user != null) 
        {            
            $where .= " AND b.id_utente = $user";
        }
        
        if ($brokerManager != null) 
        {
             $where .= " AND (b.id_utente = {$brokerManager} OR 
                        b.id_utente IN (SELECT id FROM utenti WHERE id_responsabile = {$brokerManager}))";                                
        }               

        if ($office != null) {
            $where .= " AND b.id_utente in (select id from utenti where id_ufficio = $office)";
        }
        
        
        $query .= $where;
        
        //echo $query; exit();
        $result = 0;
        $dao = new DB_DataObject();
        //$dao->debugLevel(5);
        $dao->query($query);
        while ($dao->fetch()) {
            $result = $dao->importo;
        }
        $dao->free();


        return $result;        
    }
}

?>
