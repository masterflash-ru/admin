<?php
/**

 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;



class LineController extends AbstractActionController
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
    $view= new ViewModel(
            ["table"=>$table,
            "container"=>$this->container,
            ]);
    
    return $view;
}



}
