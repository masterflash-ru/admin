<?php
/**
* новый интерфейс редактирования
*/

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Stdlib\ArrayUtils;
use Exception;

class IoEditController extends AbstractActionController
{
	protected $connection;
    protected $cache;
    protected $config;
    protected $jpgrid;
    protected $default_config=[
        "main"=>[
            "caption"=>"",
            "podval"=>"",
            "name"=>"my",
            "container"=>"my",
            "type"=>"window"
        ],
        "jpgrid"=>[
        ],
        
    ];


public function __construct ($connection,$cache,$config,$jpgrid)
{
	$this->connection=$connection;
    $this->cache=$cache;
    $this->config=$config;
    $this->jpgrid=$jpgrid;
}




public function indexAction()
{
    $interface=$this->params('interface',"");
    
    $options=include $this->config["interface"][$interface];
    $options=ArrayUtils::merge($this->default_config,$options);
    $options["interface"]=$interface;
    return new ViewModel([
            "options"=>$options
        ]);

}

/**
* чтение для jpgrid
*/
public function readjpgridAction()
{
    try {
        $interface=$this->params('interface',"");
        $subinterface=$this->params('subinterface',"");

        $options=include $this->config["interface"][$interface];
        $options=$options[$subinterface];

        $this->jpgrid->setOptions($options);


        $rez=$this->jpgrid->load($this->params()->fromQuery());


        $view=new JsonModel($rez);

        return $view;
    }catch (Exception $e) {
        $errors="Ошибка: ".$e->getMessage()."\nФайл:".$e->getFile()."\nСтрока:".$e->getLine()."\nТрассировка:".$e->getTraceAsString();
        //любое исключение - 404
        $this->getResponse()->setStatusCode(404);
        return $this->getResponse()->setContent($errors);
    }
}
}
