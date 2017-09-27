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



//для вывода меню слева
use ADO\Service\RecordSet;


class TestController extends AbstractActionController
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

  return new ViewModel();
	
}



}
