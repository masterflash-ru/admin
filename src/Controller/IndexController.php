<?php
/**
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use ADO\Service\RecordSet;
use Exception;

class IndexController extends AbstractActionController
{
    protected $connection;
    protected $cache;
    protected $rs;
    protected $mrez=[];

public function __construct ($connection,$cache)
	{
		$this->connection=$connection;
		$this->cache=$cache;
	}

/*
сюда переходим при входе в админку после успешной авторизации
*/
public function indexAction()
{
  return new ViewModel();
}



/*меню авдминки слева асинхронно
* возвращает массив записей таблицы admin_menu в формате JSON
*/
public function amenuAction()
{
    try {
        /*читаем доступы из таблицы и сохраним в кеш*/
        $key="admin_menu";
        //пытаемся считать из кеша
        $result = false;
        $mrez= $this->cache->getItem($key, $result);
        if (!$result){
            //сохраним в кеш
            $this->rs=new RecordSet();
            $this->rs->CursorType = adOpenKeyset;
            $this->rs->maxRecords=0;
            $this->rs->open("SELECT * FROM  admin_menu  order by id",$this->connection);
            $this->tree(0);
            $mrez=$this->mrez;
            $this->cache->setItem($key, $mrez);
        }
        return new JsonModel($mrez);
    } catch (Exception $e){
        $this->getResponse()->setStatusCode(406);
        return $this->getResponse()->setContent($e->getMessage());
    }
}

/*
* рекурсия для построения дерева меню в админке
* из-за особенностей JS скрипта делается рекурсия
* что бы элементы были последовательными
* $this->rs - инициализированный RS
* возвращает $this->mrez - массив записей
*/
protected function tree($subid)
{
	$rs=clone $this->rs;
	$rs->Filter="subid=$subid";
	while (!$rs->EOF){
        $r["url"]=$rs->Fields->Item['url']->Value;
        $r["level"]=$rs->Fields->Item['level']->Value;
        $r["id"]=$rs->Fields->Item['id']->Value;
        $r["name"]=$rs->Fields->Item['name']->Value;
        $this->mrez[]=$r;
        $this->tree ((int)$rs->Fields->Item['id']->Value);
        $rs->MoveNext();
    }

}

}
