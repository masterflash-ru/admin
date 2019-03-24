<?php
/**
* новый универсальный интерфейс редактирования
* вывод нужного интерфейса производится при помощи помощника view: iuniversal
* этот помощник является диспетчером, считывает конфиг и вызывает уже нужный помощник для вывода уже конкретного
* интерфейса
*/

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class UinterfaceController extends AbstractActionController
{


public function indexAction()
{
    $interface=$this->params('interface',"");
    $view=new ViewModel(["interface"=>$interface]);
    /*если у нас AJAX запрос, отключим вывод макета*/
    $view->setTerminal($this->getRequest()->isXmlHttpRequest());

    return $view;
}

}
