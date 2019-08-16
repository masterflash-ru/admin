<?php
/**
* добавляет в выпадающий список сетки список всех хранилищ файлов
*/

namespace Admin\Service\JqGrid\Plugin;

class SelectConfigStorage extends AbstractPlugin 
{
    protected $config;
    protected $def_options =[
        "emptyFirstItem"=>false
    ];

    public function __construct($config) 
    {
        $this->config=$config;
    }
    

    /**
    * преобразование элементов colModel, например, для генерации списков
    * $colModel - элемент $colModel из конфигурации
    * возвращает тот же $colModel, с внесенными изменениями
    */
    public function colModel(array $colModel, array $toolbarData=[])
    {
        if ($this->options["emptyFirstItem"]){
            $rez[$this->options["emptyFirstItemValue"]]=$this->options["emptyFirstItemLabel"];
        } else {
            $rez=[];
        }

        $config=$this->config;
        if (isset($config["storage"]["items"])){
            foreach ($config["storage"]["items"] as $sysname=>$v){
                if (empty($v["description"])){
                    $v["description"]=$sysname;
                }
                $rez[$sysname]=$v["description"];
            }
        }
        $colModel["editoptions"]["value"]=$rez;
        
        return $colModel;
    }





}