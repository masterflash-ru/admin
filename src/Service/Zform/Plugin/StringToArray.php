<?php
namespace Admin\Service\Zform\Plugin;

/*
*/
use Admin\Service\Zform\Plugin\AbstractPlugin;

class StringToArray extends AbstractPlugin
{

    protected $def_options =[
        "separator"=>","
    ];

    public function edit($value)
    {
        return explode($this->def_options["separator"],$value);    
    }

    public function read($value)
    {
        return explode($this->def_options["separator"],$value);    
    }



}