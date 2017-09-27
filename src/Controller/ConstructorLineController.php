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


class ConstructorLineController extends AbstractActionController
{
	protected $connection;
	protected $sessionManager;

public function __construct ($connection,$sessionManager)
	{
		$this->connection=$connection;
		$this->sessionManager=$sessionManager;
	}



/*вывод левой части фрейма с меню*/
public function indexAction()
{
	Simba::$connection=$this->connection;
  return new ViewModel();
	
}



}
