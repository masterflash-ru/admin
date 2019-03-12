<?php
/**
* ввод-вывод для jqGrid
*/

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Exception;

class JqGridController extends AbstractActionController
{
	protected $connection;
    protected $cache;
    protected $config;
    protected $jqgrid;
    protected $default_config=[
    ];


public function __construct ($connection,$cache,$config,$jqgrid)
{
	$this->connection=$connection;
    $this->cache=$cache;
    $this->config=$config;
    $this->jqgrid=$jqgrid;
}





/**
* чтение данных для jqgrid
*/
public function readjqgridAction()
{
    try {
        $interface=$this->params('interface',"");

        $options=include $this->config[$interface];

        $this->jqgrid->setOptions($options["options"]);

        $rez=$this->jqgrid->load($this->params()->fromQuery());

        $view=new JsonModel($rez);

        return $view;
    } catch (Exception $e) {
        $errors="Ошибка: ".$e->getMessage()."\nФайл:".$e->getFile()."\nСтрока:".$e->getLine()."\nТрассировка:".$e->getTraceAsString();
        //любое исключение - 404
        $this->getResponse()->setStatusCode(404);
        return $this->getResponse()->setContent(nl2br($errors));
    }
}

/**
* редактирование строки таблицы
*/
public function editjqgridAction()
{
    try {
        $interface=$this->params('interface',"");
        $options=include $this->config[$interface];
        $this->jqgrid->setOptions($options["options"]);
        $rez=$this->jqgrid->edit($this->params()->fromPost());
        $view=new JsonModel([]);
        return $view;
    } catch (Exception $e) {
        $errors="Ошибка: ".$e->getMessage()."\nФайл:".$e->getFile()."\nСтрока:".$e->getLine()."\nТрассировка:".$e->getTraceAsString();
        //любое исключение - 404
        $this->getResponse()->setStatusCode(404);
        return $this->getResponse()->setContent(nl2br($errors));
    }
}
}
