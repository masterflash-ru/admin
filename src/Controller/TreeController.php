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


class TreeController extends AbstractActionController
{
    protected $container;

public function __construct($container)
{
    $this->container=$container;
}

/*собственно вывод, через одно место*/
public function indexAction()
{
    $table=$this->params('table',"");
    return new ViewModel(
        ["table"=>$table,
         "container"=>$this->container,
        ]);

}

}
