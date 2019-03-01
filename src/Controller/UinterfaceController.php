<?php
/**
* новый универсальный интерфейс редактирования
*/

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class UinterfaceController extends AbstractActionController
{


public function indexAction()
{
    $interface=$this->params('interface',"");
    return new ViewModel(["interface"=>$interface]);
}

}
