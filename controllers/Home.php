<?php
namespace demo\controllers;

use demo\App;
use demo\Controller;
use demo\SmartyRenderer;
use \NumberFormatter;
use \IntlDateFormatter;

class Home extends Controller
{
	public function init()
	{
		parent::init();
	}

	public function index()
	{
		//NumberFormatter::create ( string $locale , int $style [, string $pattern ] )
		$nf = NumberFormatter::create("it_IT", NumberFormatter::DECIMAL);
		$x = 987123.45;
		$y = "9.876,32";
		$fx = $nf->format($x);
		$cy = $nf->parse($y);
		
		//IntlDateFormatter::create ( string $locale , int $datetype , int $timetype [, string $timezone [, int $calendar [, string $pattern ]]] )
		$df = new IntlDateFormatter("it_IT", IntlDateFormatter::SHORT, IntlDateFormatter::NONE);
		$it = strtotime('now');		
		$en = $df->parse("15/01/22");
		 		
		$data = array(
			"it" => $it,
			"en" => $en,
			"x" => $x,
			"y" => $y, 
			"fx" => $fx,
			"cy" => $cy,
				"profile" => App::getInstance()->getConfiguration()->getActiveProfile() 				
		);
		
		$renderer = new SmartyRenderer(App::getInstance());
		$renderer->setTemplate("home/index.tpl");		
		$this->getResponse()->addOutput($renderer->render($data));
	}
}
