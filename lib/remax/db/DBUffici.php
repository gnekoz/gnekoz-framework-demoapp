<?php
namespace demo\db;
/**
 * Table Definition for uffici
 */

class DBUffici extends DBObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'uffici';              // table name
    public $id;                              // int(4)  primary_key not_null
    public $des;                             // varchar(100)  unique_key not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Uffici',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    public function validate()
    {
    	parent::validate();
    	 
    	if ($this->des == "")
    	{
    		$this->addError("E' necessario indicare la descrizione");
    	}
    
    	return !$this->hasErrors();
    }
    

    public static function getUffici()
    {
    	$result = array();
    	$dao = new DBUffici();
    	$dao->orderBy('des');
    	$dao->find();
    	while ($dao->fetch())
    	{
    		$result[] = clone $dao;
    	}
    	$dao->free();
    
    	return $result;
    }
    
    
    public static function getUfficiMap()
    {
    	$result = array();
    	$list = self::getUffici();
    	foreach ($list as $ufficio)
    	{
    		$result[$ufficio->id] = $ufficio->des;
    	}
    
    	return $result;
    }
    
    
    public static function getUfficio($id)
    {
    	$ufficio = new DBUffici();
    	if ($id != null && $ufficio->get($id) == 1)
    	{
    		$ufficio->fetch();
    	}
    
    	return $ufficio;
    }
}
