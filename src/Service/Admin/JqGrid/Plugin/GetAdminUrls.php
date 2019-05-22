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
        $rez[""]="";$locale="ru_RU";
        foreach ($this->controllers_descriptions as $name=>$desc){
            //внутри контроллера
            if (is_array($desc)) {
                foreach ($desc as $meta) {
                    $r=[];
                    if (isset($meta["urls"]["url"][$locale])){
                        foreach ($meta["urls"]["url"][$locale] as $k=>$item){
                            $r[$item]=$meta["urls"]["name"][$locale][$k];
                        }
                        $rez[$meta["description"]]=$r;
                    }
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
        $rez[""]="";$locale="ru_RU";
        foreach ($this->controllers_descriptions as $name=>$desc){//\Zend\Debug\Debug::dump($desc);
            //внутри контроллера
            if (is_array($desc) && !empty($desc)) {
                foreach ($desc as $meta) {
                    if (isset($meta["urls"]["url"][$locale])){
                        foreach ($meta["urls"]["url"][$locale] as $k=>$item){
                            $rez[$item]=$meta["description"].' -> '.$meta["urls"]["name"][$locale][$k];
                        }
                    }

                }
            }
        }
        $colModel["editoptions"]["value"]=$rez;
        return $colModel;
    }



}