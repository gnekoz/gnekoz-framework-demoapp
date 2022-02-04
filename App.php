<?php
namespace demo;

use demo\SmartyRenderer;

use gnekoz\Application;
use \PEAR;

class App extends Application
{
    public static function getInstance()
    {
			return Application::getApplication("demo");
    }

    public function onInitialize()
    {
    	$conf = $this->getConfiguration();
    	
    	// Configurazione renderer smarty    	
    	SmartyRenderer::setTemplateDir($this->getViewsDir());
    	SmartyRenderer::setCompileDir($conf->getProperty('/rendering/smarty/compile_dir'));
    	SmartyRenderer::setCacheDir($conf->getProperty('/rendering/smarty/cache_dir'));
    	SmartyRenderer::setWebRoot($this->getWebRoot());
    	
		    	
    	
    	// Workaround per librerie PEAR versione 1
    	set_include_path($this->getLibDir()
    					 . DIRECTORY_SEPARATOR
    					 . "pyrus"
    					 . DIRECTORY_SEPARATOR
    					 . "php");

    	// Inizializzazione DB_DataObject    	
    	$options = &PEAR::getStaticProperty('DB_DataObject','options');

    	$basicOptions = $conf->getProperty("/persistence/db_dataobject/*");
    	//var_dump($basicOptions); exit();

    	foreach ($basicOptions as $name => $value)
    	{
    	  $options[$name] = $value;
    	}
    	//var_dump($options); exit();
    }

    public function onShutdown()
    {
    }

    public function onSessionCreate($id)
    {
    }
    public function onSessionDestroy($id)
    {
    }

    public function onSessionRestore($id)
    {
    }
}
