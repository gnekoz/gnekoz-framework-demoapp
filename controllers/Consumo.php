<?php
namespace demo\controllers;

use demo\SmartyRenderer;
use demo\App;
use demo\Controller;
use demo\db\DBUtenti;
use demo\db\DBProdotti;
use demo\db\DBConsumi;
use \NumberFormatter;

/**
 * @author gneko
 *
 */
class Consumo extends Controller 
{
	public function init()
	{
		parent::init();
		$this->setRequiredRole(DBUtenti::ROLE_EXPENSEOPERATOR);
	}


	public function index()
	{
		$consumo = new DBConsumi();
		$consumo->data = strtotime(date("Y-m-d"));

		$data = array("consumo" => $consumo,
		              "allUsers" => DBUtenti::getUsersMap(false),
		              "prodotti" => DBProdotti::getProductsMap());
		
		$renderer = new SmartyRenderer(App::getInstance());
		$renderer->setTemplate("consumo/new.tpl");
		$this->getResponse()->addOutput($renderer->render($data));
	}

	public function edit()
	{
		$consumo = new DBConsumi();
		$consumo->setFromRequest($this->getRequest());
//var_dump($consumo); exit();
		
		$data = array("consumo" => $consumo,
		              "allUsers" => DBUtenti::getUsersMap(false),
				      "prodotti" => DBProdotti::getProductsMap());

		$renderer = new SmartyRenderer(App::getInstance());
		$renderer->setTemplate("consumo/edit.tpl");
		$this->getResponse()->addOutput($renderer->render($data));
	}


	public function save()
	{		
		$consumo = new DBConsumi();
		$consumo->setFromRequest($this->getRequest());
		
		if ($consumo->validate())
		{
			$consumo->save();
			if (!$consumo->hasErrors())
			{
                if ($this->getRequest()->getParameter("save_new") != null)
                {
                    $this->getResponse()->sendRedirect("/consumo");
                    return;
                }                
				$this->getResponse()->sendRedirect("/consumo/edit?id={$consumo->id}");
			}
		}
		

		$data = array("consumo" => $consumo,
		              "allUsers" => DBUtenti::getUsersMap(false),
				      "prodotti" => DBProdotti::getProductsMap(),
				      "errors" => $consumo->getErrors());

		$renderer = new SmartyRenderer(App::getInstance());
		$renderer->setTemplate("consumo/save.tpl");
		$this->getResponse()->addOutput($renderer->render($data));
	}


	public function delete()
	{
		$consumo = new DBConsumi();
		$consumo->setFromRequest($this->getRequest());

		if ($consumo->id != null)
		{
			$consumo->delete();
			
			// FIXME
			
			$this->getResponse()->sendRedirect("/consumi");
		}
	}


	public function getPrezzo()
	{
	  $prodotto = $this->getRequest()->getParameter("prodotto");

	  $prezzoUnitario = floatval(DBProdotti::getProductPrice($prodotto));
	  
	  $nf = NumberFormatter::create("it_IT", NumberFormatter::DECIMAL);
	  $nf->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 2);
	  $prezzoUnitario = $nf->format($prezzoUnitario);	  

	  $this->getResponse()->addOutput(json_encode($prezzoUnitario));
	}
	
	
	public function calcolaImporto()
	{
		$nf = NumberFormatter::create("it_IT", NumberFormatter::DECIMAL);		
		
		$prezzo = $nf->parse($this->getRequest()->getParameter("prezzo_unitario"));
		$quantita = $nf->parse($this->getRequest()->getParameter("quantita"));
				
		$importo = floatval($prezzo) * floatval($quantita);
		//var_dump($importo); exit();
		
		$nf->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 2);
		$importo = $nf->format($importo);
		
		$this->getResponse()->addOutput(json_encode($importo));
	}
        
        
        
        public function aggiornaAddebito()
        {
            $id = $this->getRequest()->getParameter("id");
            $addebitato = $this->getRequest()->getParameter("addebitato");
            
            //error_log("$id = $addebitato");
            
            $result = array("error" => 0, "message" => "");
                       
            
            
            $consumo = new DBConsumi();
            if (!$consumo->get($id)) {
                $result['error'] = 1;
                $result['message'] = 'Impossibile trovare consumo con id '. $id;
            } else {
                $consumo->flg_addebitato = $addebitato == 'true' ? 1 : 0;
                $consumo->save();
                if ($consumo->hasErrors()) {
                    $result['error'] = 1;
                    $result['message'] = $consumo->_lastError->getMessage();
                }
            }            
            
            $this->getResponse()->addOutput(json_encode($result));
        }


}
