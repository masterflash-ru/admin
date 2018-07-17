<?php
/**
обработка полей каталога товара
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Lib\Simba;

class TovarController extends AbstractActionController
{
	protected $connection;
    protected $config;

public function __construct ($connection,$config)
	{
		$this->connection=$connection;
        Simba::$connection=$connection;
    $this->config=$config;
	}



/*вывод левой части фрейма с меню*/
public function indexAction()
{
	$view=new ViewModel();
	$view->setVariables(["config"=>$this->config]);
	return $view;
}



}
