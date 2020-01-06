<?php
/**
обработка полей каталога товара
 */

namespace Admin\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Admin\Lib\Simba;

class TovarController extends AbstractActionController
{
    protected $connection;
    protected $config;
    protected $container;

public function __construct ($connection,$config,$container)
{
    $this->connection=$connection;
    Simba::$connection=$connection;
    $this->config=$config;
    $this->container=$container;
}



/*вывод левой части фрейма с меню*/
public function indexAction()
{
    $view=new ViewModel();
    $view->setVariables(["config"=>$this->config,"container"=>$this->container]);
    return $view;
}



}
