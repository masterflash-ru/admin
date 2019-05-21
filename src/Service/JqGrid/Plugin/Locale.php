<?php
namespace Admin\Service\JqGrid\Plugin;

use Zend\Form\FormInterface;

class Locale extends AbstractPlugin
{
	protected $config;

    public function __construct($config) 
    {
		$this->config=$config;
    }
    

    /**
    * преобразование элементов colModel, например, для генерации списков
    * $colModel - элемент $colModel из конфигурации
    * возвращает тот же $colModel, с внесенными изменениями
    */
    public function colModel(array $colModel)
    {
        $rez=[];
        foreach ($this->config as $l){
            $rez[$l]=$l;
        }
        $colModel["editoptions"]["value"]=$rez;

        return $colModel;
    }



}