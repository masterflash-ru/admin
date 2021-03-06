<?php
/**
обработка галерей для zrkuban 
* костыльный контроллер!!!!
*/

namespace Admin\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use ADO\Service\RecordSet;
use Admin\Lib\Simba;
use Laminas\Session\Container as SessionContainer;
use Admin\Lib\Formitem;

class ZrkubanController extends AbstractActionController
{
    protected $connection;
    protected $config;
    protected $container;
    protected $GalleryLib;
    protected $Formitem;

public function __construct ($GalleryLib,$connection,$config,$container)
{
    $this->connection=$connection;
    Simba::$connection=$connection;
    $this->config=$config;
    $this->container=$container;
    $this->GalleryLib=$GalleryLib;
    $this->Formitem=new Formitem(null,$container->get("config"));
    Simba::$container=$container;
}



/*костыли для сайт zrkuban для обработки старого*/
public function indexAction()
{
    $view=new ViewModel();
    $view->setVariables(["config"=>$this->config,"container"=>$this->container]);
    
    $g=array_intersect_key($_GET,["test_drive"=>1,"news"=>2,"article"=>3,"interview"=>4]);
    $razdel_id=current($g);    //ID материала
    $razdel=key($g);    //имя раздела: news, article, test_drive.....
    
    $session=new SessionContainer($razdel.$razdel_id);
    
    //действие из формы
    $form_action=$this->Params()->fromPost('form_action', "");
    
    
    
    
    //доабвление новой пустой галереи, в сессию пока
    if ($form_action=="create_new_gallery"){
    	if (in_array(0,$session->gallery_numbers) )	{
            $session->gallery_numbers[]=max($session->gallery_numbers)+1;
        } else {
            $session->gallery_numbers[]=0;
        }
    }
    
    //обработка редактирвоания таблицы
    if ($form_action=="edit_gallery"){
        //одиночные операции
        @$s=array_keys($_POST["save"]);//это идентификатор строки когда нажали кнопку сохранить
        @$d=array_keys($_POST["del"]);//это идентификатор строки когда нажали кнопку сохранить
        
        //удаление
        if (is_array($d)){
            $this->deleteRow((int)$d[0],$razdel,$razdel_id);
        }

        //сохранение
        if (is_array($s)){
            $this->saveRow((int)$s[0],$razdel,$razdel_id);
        }

        //сохранить все
        if (isset($_POST["saveall"])){
            foreach (explode(",",$_POST["global_action_id_array"]) as $id){
                $this->saveRow((int)$id,$razdel,$razdel_id);
            }
        }

        //удалить отмеченное
        if (isset($_POST["delete_selected_"]) && is_array($_POST["_select_item"])){
            $ids=explode(",",$_POST["global_action_id_array"]);
            foreach ($_POST["_select_item"] as $id=>$is_delete){
                if ($is_delete=="1") {
                    $this->deleteRow((int)$ids[$id],$razdel,$razdel_id);
                }
            }
        }
    }

    
	$gallery_index=(int)$this->Params()->fromPost('gallery_number', 0);
    //вывод
	if (!$session->gallery_numbers){
        $session->gallery_numbers=$this->GalleryLib->getIndexArray($razdel,$razdel_id);    //получить массив индексов галерей
    }
	if (empty($session->gallery_numbers)) {//начальное условие
        $session->gallery_numbers=[0];
    }
    $gallery_number=$this->Params()->fromPost('gallery_number', $session->gallery_numbers[0]);  //номер галереи
    

    $view->setVariable("gallery_numbers",$session->gallery_numbers);
    $view->setVariable("gallery_number",$gallery_index); //из выпадающего списка формы или 0
    
    //содержимое галереи
	$rs=$this->connection->Execute("
                select storage_gallery.*, id as img from 
                    storage_gallery 
                        where 
                            razdel='$razdel' and 
                            razdel_id='$razdel_id' and 
                            gallery_index=$gallery_index
                                order by poz desc");
    
	$view->setVariable("items", $rs->GetRows(adGetRowsArrType,0));
    return $view;
}

    
   /**
   * удаление строки
   */ 
    protected function deleteRow($id,$razdel,$razdel_id)
    {
        $id=(int)$id;
        //пометим на удаление файлы
         $this->Formitem->del_form_item($id,32,"","img",null,["gallery"]);
        $this->connection->Execute("delete from storage_gallery where id=$id");
    }
    
    
    /*
    * запись строки
    */
    protected function saveRow($id,$razdel,$razdel_id)
    {//\Laminas\Debug\Debug::dump($id);
     //\Laminas\Debug\Debug::dump($_POST);
        
        $rs=$this->connection->Execute("select public, date_public,alt,caption,url from {$razdel} where id={$razdel_id}");
        if ($rs->Fields->Item["alt"]->Value){
            $alt=$rs->Fields->Item["alt"]->Value;
        } else {
            $alt=$rs->Fields->Item["caption"]->Value;
        }
        
        
        $id=(int)$id;
        $filename=$this->Formitem->save_form_item($id,33,"storage_gallery","img",null,null,["gallery"]);
        $this->GalleryLib->setStorageItem("gallery");
        $this->GalleryLib->setIndex((int)$_POST["gallery_number"]);
        $this->GalleryLib->setMeta("alt", ($_POST["alt"][$id]) ? $_POST["alt"][$id]:$alt );
        $this->GalleryLib->setMeta("poz", $_POST["poz"][$id]);
        $this->GalleryLib->setMeta("date_public", $rs->Fields->Item["date_public"]->Value);
        $this->GalleryLib->setMeta("public", $rs->Fields->Item["public"]->Value);
        $this->GalleryLib->setMeta("url", "/".$razdel."/".$rs->Fields->Item["url"]->Value);

        $this->GalleryLib->setRazdel($razdel);
        $this->GalleryLib->setRazdelId($razdel_id);

        if ($filename){//добавление или замена фото
            $this->GalleryLib->saveFiles($filename,$id);
        } else {
            //просто обновление метаданных
            $this->GalleryLib->updateMeta($id);
        }
    }
    
    
    /*модерация объявлений*/
    public function saleeditAction()
    {
        $view=new ViewModel();
        $view->setVariables(["container"=>$this->container]);
        return $view;
    }
    
}
