<?php
/**
контроллер управления конструктором деревьев
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Lib\Simba;


class ConstructorTreeController extends AbstractActionController
{
	protected $connection;
	protected $sessionManager;
	protected $config;

public function __construct ($connection,$sessionManager,$config)
	{
		$this->connection=$connection;
		$this->sessionManager=$sessionManager;
		$this->config=$config;
	}



/*вывод левой части фрейма с меню*/
public function indexAction()
{
    $view=new ViewModel(["config"=>$this->config]);
    if (!$this->acl([get_class($this),"index"])->isAllowed("r")){
        /*чтение разрешено?*/
        $view->setTemplate("admin/index/accessdenied");
    }

	Simba::$connection=$this->connection;
  return $view;
}



}
