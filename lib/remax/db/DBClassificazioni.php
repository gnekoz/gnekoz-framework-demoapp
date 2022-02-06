<?php

namespace demo\db;

/**
 * Table Definition for classificazioni
 */
class DBClassificazioni extends DBObject {

    const TIPO_TIPO_CONTATTO = 0;
    const TIPO_TIPO_RICHIESTA = 1;
    const TIPO_MOTIVO_RICHIESTA = 2;
    const TIPO_TIPO_IMMOBILE = 3;
    const TIPO_FONTE_PUBBLICITA = 4;
    const TIPO_CAMERE = 5;

    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'classificazioni';            // table name
    public $id;   // int(4)  primary_key not_null
    public $tipo; // int(4)  not null
    public $des;  // varchar(100) not null

    /* Static get */

    function staticGet($k, $v = NULL) {
        return DB_DataObject::staticGet('Classificazione', $k, $v);
    }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    public function validate() {
        parent::validate();

        if ($this->tipo == null) {
            $this->addError("E' necessario impostare il tipo");
        }

        if ($this->des == null) {
            $this->addError("E' necessario impostare la descrizione");
        }

        return !$this->hasErrors();
    }

    public static function getDescrizioneTipo($tipo) {
        switch ($tipo) {
            case self::TIPO_TIPO_CONTATTO : return 'tipo contatto';
            case self::TIPO_TIPO_RICHIESTA : return 'tipo richiesta';
            case self::TIPO_TIPO_IMMOBILE : return 'tipo immobile';
            case self::TIPO_CAMERE : return 'camere o altro';
            case self::TIPO_FONTE_PUBBLICITA : return 'fonte pubblicità';
            case self::TIPO_MOTIVO_RICHIESTA : return 'motivo richiesta';
            default: 'sconosciuto';
        }
    }

    public static function getClassificazioni($tipo)
    {
        $result = array();
        $class = new DBClassificazioni();
        $class->tipo = $tipo;
        $class->orderBy('des');
        $class->find();
        while ($class->fetch()) {
            $result[$class->id] = $class->des;
        }
        $class->free();
        
        return $result;
    }
    
    public static function getDes($id)
    {
        if (!$id) {
            return '';
        }
        $result = array();
        $class = new DBClassificazioni();
        $class->id = $id;
        $class->find();
        while ($class->fetch()) {
            return $class->des;
        }
        $class->free();
        
        return "";
    }    

}
