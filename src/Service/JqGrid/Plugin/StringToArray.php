<?php
namespace Admin\Service\JqGrid\Plugin;

/*
*/
use Admin\Service\JqGrid\Plugin\AbstractPlugin;

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