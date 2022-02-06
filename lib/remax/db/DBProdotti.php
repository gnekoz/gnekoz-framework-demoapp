<?php
namespace demo\db;

use \NumberFormatter;

/**
 * Table Definition for prodotti
 */
class DBProdotti extends DBObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'prodotti';            // table name
    public $id;                              // int(4)  primary_key not_null
    public $des;                             // varchar(150)   not_null
    public $prezzo;                          // float(8)   not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Prodotti',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    
    public function validate()
    {
    	parent::validate();
    	    	
    	if ($this->des == null)
    	{
    		$this->addError("E' necessario indicare la descrizione");
    	}
    	
    	if ($this->prezzo == null)
    	{
    		$this->addError("E' necessario indicare il prezzo");
    	}
    	
    	return !$this->hasErrors();
    }
    
    protected function fieldFromRequestCallback($name, $value)
    {
    	parent::fieldFromRequestCallback($name, $value);
    	
    	if ($name == 'prezzo')
    	{
    		$nf = NumberFormatter::create("it_IT", NumberFormatter::DECIMAL);
    		$this->prezzo = $nf->parse($value);    		
    	}
    }
    
    public static function getProductsMap()
    {
    	$prods = array();
    	$prod = new DBProdotti();
    	$prod->orderBy('des');
    	$prod->find();
    	while ($prod->fetch())
    	{
    		$prods[$prod->id] = $prod->des;
    	}
    	$prod->free();
    
    	return $prods;
    }
    
    
    public static function getProductPrice($productID)
    {
    	$result = 0;
    	$prod = new DBProdotti();
    	if ($prod->get($productID) == 1)
    	{
    		$result = $prod->prezzo;
    	}
    	$prod->free();
    	return $result;
    }    
}
