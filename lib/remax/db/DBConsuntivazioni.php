<?php
namespace demo\db;

/**
 * Table Definition for consuntivazioni
 */
class DBConsuntivazioni extends DBObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'consuntivazioni';     // table name
    public $id;                              // int(4)  primary_key not_null
    public $id_utente;                       // int(4)  unique_key not_null
    public $data;                            // date  unique_key not_null
    public $gen_nuo_con;
    public $gen_not;
    public $gen_ric_spe;
    public $gen_inc;
    public $app_ven;
    public $app_aff;
    public $app_acq;
    public $pro_acq;
    public $pro_acq_col;
    public $pro_loc;
    public $pro_loc_col;
    public $tra_ven;
    public $tra_aff;


    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Consuntivazioni',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    public function validate()
    {
    	parent::validate();
    	
    	if ($this->data == null)
    	{
    		$this->addError("E' necessario indicare la data");
    	}
    	
    	if ($this->id_utente == null)
    	{
    		$this->addError("E' necessario indicare l'utente");
    	}    	
    	
    	return !$this->hasErrors();
    }
}
