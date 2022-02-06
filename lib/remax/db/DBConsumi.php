<?php
namespace demo\db;

use \NumberFormatter;
use \IntlDateFormatter;

/**
 * Table Definition for consumi
 */
class DBConsumi extends DBObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'consumi';             // table name
    public $id;                              // int(4)  primary_key not_null
    public $id_utente;                       // int(4)   not_null
    public $data;                            // date   not_null
    public $id_prodotto;                     // int(4)   not_null
    public $quantita;                        // float(8)  
    public $prezzo_unitario;                 // float(8)   not_null
    public $note;                            // varchar(300)
    public $flg_addebitato;                  // int(4)

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Consumi',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    public $importo;
    
    
    public function fetch()
    {
    	$ret = parent::fetch();
    	
    	$this->importo = floatval($this->prezzo_unitario) * floatval($this->quantita);
    	
    	return $ret;
    }
        
    
	public function validate()
	{
		parent::validate();
		
		if ($this->id_utente == "")
		{
			$this->addError("E' necessario indicare l'utente");
		}
				
		if ($this->data == "")
		{
			$this->addError("E' necessario indicare la data");
		}
				
		if ($this->id_prodotto == "")
		{
			$this->addError("E' necessario indicare il prodotto");
		}
				
		if ($this->quantita == "")
		{
			$this->addError("E' necessario indicare la quantità");
		}
				
		if ($this->prezzo_unitario == "")
		{
			$this->addError("E' necessario indicare il prezzo unitario");
		}
		
		return !$this->hasErrors();
	}
	

	protected function fieldFromRequestCallback($name, $value)
	{	
            error_log("$name = $value");
//                $this->flg_addebitato = '0';
		if ($name == 'prezzo_unitario' || $name == 'quantita')
		{
			$nf = NumberFormatter::create("it_IT", NumberFormatter::DECIMAL);
			$this->$name = $nf->parse($value);
		}		
		else if ($name == 'data')
		{
			$df = new IntlDateFormatter("it_IT", IntlDateFormatter::SHORT, IntlDateFormatter::NONE);
			$this->data = date("Y-m-d", $df->parse($value));			
		}
                else if ($name == 'flg_addebitato')
		{
			$this->flg_addebitato = $value;
		}
		else
		{
			parent::fieldFromRequestCallback($name, $value);
		}
	}
}
