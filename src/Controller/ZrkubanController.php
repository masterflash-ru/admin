<?php
/**
обработка полей каталога товара
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Lib\Simba;
use Zend\Session\Container as SessionContainer;

class ZrkubanController extends AbstractActionController
{
    protected $connection;
    protected $config;
    protected $container;
    protected $GalleryLib;

public function __construct ($GalleryLib,$connection,$config,$container)
{
    $this->connection=$connection;
    Simba::$connection=$connection;
    $this->config=$config;
    $this->container=$container;
    $this->GalleryLib=$GalleryLib;
}



/*костыли для сайт zrkuban для обработки старого*/
public function indexAction()
{
    $view=new ViewModel();
    $view->setVariables(["config"=>$this->config,"container"=>$this->container]);
    
    $g=array_intersect_key($_GET,["test_drive"=>1,"news"=>2,"article"=>3,"interview"=>4]);
    $id=current($g);    //ID материала
    $razdel=key($g);    //имя раздела: news, article, test_drive.....
    
    $session=new SessionContainer($razdel.$id);
    
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
    

    
	//начальное условие
	if (!$session->gallery_numbers){$session->gallery_numbers=[0,1,2];}
	if (empty($session->gallery_numbers)) {$session->gallery_numbers=[0];}
    $view->setVariable("gallery_numbers",$session->gallery_numbers);
    $view->setVariable("gallery_number",(int)$this->Params()->fromPost('gallery_number', 0)); //из выпадающего списка формы или 0
    
    
    
    
    
    
    return $view;
}



}
