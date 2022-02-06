<?php
namespace demo\db;

use \NumberFormatter;

/**
 * Table Definition for budget
 */
class DBBudget extends DBObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'budget';              // table name
    public $id;                              // int(4)  primary_key not_null
    public $id_utente;                       // int(4)  not_null    
    public $anno;                            // int(4)  not_null    
    public $importo;                         // float(8)   not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Budget',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    
    public function validate()
    {
    	parent::validate();
    	    	
    	if ($this->anno == null)
    	{
    		$this->addError("E' necessario indicare l'anno");
    	}
    	
        if ($this->id_utente == null)
    	{
    		$this->addError("E' necessario indicare l'utente");
    	}
        
    	if ($this->importo == null)
    	{
    		$this->addError("E' necessario indicare l'importo");
    	}
    	
    	return !$this->hasErrors();
    }
    
    protected function fieldFromRequestCallback($name, $value)
    {
    	parent::fieldFromRequestCallback($name, $value);
    	
    	if ($name == 'importo')
    	{
    		$nf = NumberFormatter::create("it_IT", NumberFormatter::DECIMAL);
    		$this->importo = $nf->parse($value);    		
    	}
    }    
}
