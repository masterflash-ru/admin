<?php
/**
обработка галерей для zrkuban 
* костыльный контроллер!!!!
*/

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ADO\Service\RecordSet;
use Admin\Lib\Simba;
use Zend\Session\Container as SessionContainer;
use Admin\Lib\Formitem;

class ZrkubanBlockController extends AbstractActionController
{
    protected $connection;
    protected $container;
    protected $Formitem;
    protected $config;

public function __construct ($connection,$container)
{
    $this->connection=$connection;
    Simba::$connection=$connection;
    $this->container=$container;
    $this->Formitem=new Formitem(null,$container->get("config"));
    Simba::$container=$container;
    $this->config=$container->get("config");
}
    



    /*костыли для сайт zrkuban для обработки старого*/
    public function optionsAction()
    {
        $view=new viewModel();
        $view->setTemplate("admin/zrkuban-block/options");
        $block_id=(int)$this->Params()->fromQuery('id', 0);
        $rs=$this->connection->Execute("select id,model,type  from block where id=$block_id");
        
        //смотрим сценарий опций, если есть
        if (array_key_exists($rs->Fields->Item["model"]->Value,$this->config["blocks"]["options_tpl"])){
            $view->setTemplate($this->config["blocks"]["options_tpl"][$rs->Fields->Item["model"]->Value]);
        } else {
            //опций нет, сразу выходим
            return $view;
        }
        $view->setVariable("block_type",$rs->Fields->Item["type"]->Value);
        
        //записываем если пришел запрос
        if (isset($_POST["save"])){
            $this->connection->Execute("delete from block_options where block_id={$block_id}",$a,adExecuteNoRecords);
			$rso=new RecordSet();
			$rso->CursorType =adOpenKeyset;
			$rso->Open("select * from block_options where block_id={$block_id}",$this->connection);
            foreach ($this->Params()->fromPost() as $k=>$v){
                if ($k=="save"){continue;}
                $rso->AddNew();
                if ($k=="source" && is_array($v)){
                    $v=implode(",",$v);
                }
                $rso->Fields->Item["val"]->Value=$v;
                $rso->Fields->Item["sysname"]->Value=$k;
                $rso->Fields->Item["block_id"]->Value=$block_id;
                $rso->Fields->Item["block_type"]->Value=$rs->Fields->Item["type"]->Value;
                $rso->Update();
            }
            //чистим кеш
            $cache=Simba::$container->get('DefaultSystemCache');
            //$cache->removeItems("block_options");//ключи
            $cache->clearByTags(["block_options"],true);//теги

        }
        
        
        
        
        //читаем опции
        $rs=$this->connection->Execute("select * from block_options where block_id=".(int)$rs->Fields->Item["id"]->Value);
        $rez=[];
        while (!$rs->EOF){
            $rez[$rs->Fields->Item["sysname"]->Value]=$rs->Fields->Item["val"]->Value;
            $rs->moveNext();
        }
        $view->setVariable("options",$rez);
        
        
        return $view;

    }
}
