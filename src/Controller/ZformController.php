<?php
/**
* ввод-вывод для jqGrid
*/

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Exception;
use Admin\Service\JqGrid\Exception as jqGridException;

class ZformController extends AbstractActionController
{
	protected $connection;
    protected $cache;
    protected $config;
    protected $jqgrid;


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
        $acl=$this->acl('interface/'.$interface);
        if (!$acl->isAllowed("r")){
            throw new  jqGridException\AccessDeniedException("Ошибка чтения. Доступ запрещен к interface/".$interface);
        }

        $options=include $this->config[$interface];

        $this->jqgrid->setOptions($options["options"]);

        $rez=$this->jqgrid->load($this->params()->fromQuery());

        $view=new JsonModel($rez);

        return $view;
    } catch (jqGridException\AccessDeniedException $e) {
        $this->getResponse()->setStatusCode(406);
        return $this->getResponse()->setContent('<h2 style="color:red">'.$e->getMessage().'<h2>');
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
        $acl=$this->acl('interface/'.$interface);
        if (!$acl->isAllowed("r")){
            throw new  jqGridException\AccessDeniedException("Ошибка записи. Доступ запрещен к interface/".$interface);
        }

        $options=include $this->config[$interface];
        $this->jqgrid->setOptions($options["options"]);
        $rez=$this->jqgrid->edit($this->params()->fromPost());
        $view=new JsonModel([]);
        return $view;
    } catch (jqGridException\AccessDeniedException $e) {
        $this->getResponse()->setStatusCode(406);
        return $this->getResponse()->setContent('<h2 style="color:red">'.$e->getMessage().'<h2>');
    } catch (Exception $e) {
        $errors="Ошибка: ".$e->getMessage()."\nФайл:".$e->getFile()."\nСтрока:".$e->getLine()."\nТрассировка:".$e->getTraceAsString();
        //любое исключение - 404
        $this->getResponse()->setStatusCode(404);
        return $this->getResponse()->setContent(nl2br($errors));
    }
}
    

/**
* работа с отдельными плагинами
* возвращает json
*/
public function pluginAction()
{
    try {
        $plugin_name=$this->params('name',"");
        $acl=$this->acl('jqgrid/plugin/'.$plugin_name);
        if (!$acl->isAllowed("r")){
            throw new  jqGridException\AccessDeniedException("Ошибка. Доступ к плагину jqgrid/plugin/{$plugin_name} запрещен");
        }

        $plugin=$this->jqgrid->plugin($plugin_name,null);
        $rez=$plugin->ajaxRead();
        $view=new JsonModel($rez);
        return $view;
    } catch (jqGridException\AccessDeniedException $e) {
        $this->getResponse()->setStatusCode(406);
        return $this->getResponse()->setContent('<h2 style="color:red">'.$e->getMessage().'<h2>');
    } catch (Exception $e) {
        $errors="Ошибка: ".$e->getMessage()."\nФайл:".$e->getFile()."\nСтрока:".$e->getLine()."\nТрассировка:".$e->getTraceAsString();
        //любое исключение - 404
        $this->getResponse()->setStatusCode(404);
        return $this->getResponse()->setContent(nl2br($errors));
    }

}
}
