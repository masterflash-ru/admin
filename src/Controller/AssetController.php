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

class AssetController extends AbstractActionController
{


public function indexAction()
{
    $type=$this->params('type',"");
    $file=$this->params('file',"");
    $folder=$this->params('folder',"");
    $view=new ViewModel(["type"=>$type,"file"=>$file,"dir"=>__DIR__,"folder"=>$folder]);
    $view->setTerminal(true);
    return $view;
}

}
