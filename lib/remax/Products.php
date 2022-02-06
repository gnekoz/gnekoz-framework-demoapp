<?php
namespace demo;

use \DB_DataObject;

/**
 * @author gneko
 *
 */
class Products {
	
	public static function getProductsMap()
	{
		$prods = array();
		$prod = DB_DataObject::factory('prodotti');
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
		$prod = DB_DataObject::factory('prodotti');
		if ($prod->get($productID) == 1) 
		{
			$result = $prod->prezzo;
		}
		$prod->free();		
		return $result;		
	}
}
