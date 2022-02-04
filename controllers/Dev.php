<?php
namespace demo\controllers;

use gnekoz\rendering\FileRenderer;
use demo\blg\Consuntivazioni;
use demo\report\ConsuntivazioneComposer;
use \DB_DataObject_Generator as Generator;
use demo\App;
use demo\Controller;
use demo\SmartyRenderer;
use \PEAR;

class Dev extends Controller
{
	public function init()
	{
// 		parent::init();
// 		$this->setRequiredRole(Utenti::ROLE_ADMIN);
	}

	public function index()
	{
	  $conf = App::getInstance()->getConfiguration();
	  $conf->addVar("pinco.pallino", "Pinco Pallino");
	  $conf->addVar("luca", "Luca Stauble");
	  echo App::getInstance()->getConfiguration()->getProperty("/test1");
	  echo App::getInstance()->getConfiguration()->getProperty("/test2");

	  exit();
		$renderer = new SmartyRenderer(App::getInstance());
		$renderer->setTemplate("dev/index.tpl");
		$this->getResponse()->addOutput($renderer->render(array()));
	}
	
	public function report()
	{
		$list = Consuntivazioni::getConsuntivazioniExt(null, null, null, null, date('Y'));
		
		$composer = new ConsuntivazioneComposer($list, date('Y'));
		$file = $composer->createReport();

		$data = array('file' => $file, 'name' => basename($file));
		
		$renderer = new FileRenderer();
		$renderer->render($data);
		exit(); // FIXME
	}

	public function dbgenerator()
	{
	  // Inizializzazione DB_DataObject
	  $conf = App::getInstance()->getConfiguration();
	  $options = &PEAR::getStaticProperty('DB_DataObject','options');

	  $generatorOptions = $conf->getProperty("/persistence/db_dataobject_generator/*");

	  foreach ($generatorOptions as $name => $value)
	  {
	    $options[$name] = $value;
	  }
	  
	  $generator = new Generator();
	  //$generator->debugLevel(5);
	  $generator->start();


    $data = array();
	  $renderer = new SmartyRenderer(App::getInstance());
	  $renderer->setTemplate("dev/index.tpl");
	  $this->getResponse()->addOutput($renderer->render($data));
	}
}
