<?php

namespace Admin\Service\JqGrid\Plugin;

use Zend\Stdlib\ArrayUtils;

abstract class AbstractPlugin implements JqGridPluginsInterface
{
    protected $options=[];
    protected $def_options =[
    ];

    
    /**
    * установка опций
    */
    public function setOptions(array $options)
    {
        $this->options=ArrayUtils::merge($this->def_options,$options);
    }
    
    /**
    * заглушка
    */
    public function colModel(array $colModel)
    {
        
    }

}
