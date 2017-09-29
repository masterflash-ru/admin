<?php
/**


для получения главного сервис-менеджера
$this->getEvent()->getApplication()->getServiceManager()

//получить конфиг приложения
$this->getEvent()->getApplication()->GetConfig()
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
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
	Simba::$connection=$this->connection;
  return new ViewModel(["config"=>$this->config]);
	
}



}
