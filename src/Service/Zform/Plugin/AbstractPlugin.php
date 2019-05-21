<?php

namespace Admin\Service\Zform\Plugin;

use Zend\Stdlib\ArrayUtils;

abstract class AbstractPlugin implements ZformPluginInterface
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
