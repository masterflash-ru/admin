<?php


namespace Admin\Service\Admin\JqGrid\Plugin;

use Admin\Service\JqGrid\Plugin\AbstractPlugin;


class GetAdminUrls extends AbstractPlugin
{

    protected $controllers_descriptions;
    
    public function __construct($controllers_descriptions)
    {
        $this->controllers_descriptions=$controllers_descriptions;
    }

    public function ajaxRead()
    {
        $rez[""]="";
        foreach ($this->controllers_descriptions as $name=>$desc){
            //внутри контроллера
            if (is_array($desc)) {
                foreach ($desc as $meta) {
                    $r=[];
                    foreach ($meta["urls"]["url"] as $k=>$item){
                        $r[$item]=$meta["urls"]["name"][$k];
                    }
                    $rez[$meta["description"]]=$r;
                }
            }
        }
        return $rez;
    }

    /**
    * преобразование элементов colModel, например, для генерации списков
    * $colModel - элемент $colModel из конфигурации
    * возвращает тот же $colModel, с внесенными изменениями
    */
    public function colModel(array $colModel,array $toolbarData=[])
    {
        $rez[""]="";
        foreach ($this->controllers_descriptions as $name=>$desc){//\Zend\Debug\Debug::dump($desc);
            //внутри контроллера
            if (is_array($desc)) {
                foreach ($desc as $meta) {
                    foreach ($meta["urls"]["url"] as $k=>$item){
                        $rez[$item]=$meta["description"].' -> '.$meta["urls"]["name"][$k];
                    }
                    
                }
            }
        }
        $colModel["editoptions"]["value"]=$rez;
        return $colModel;
    }



}