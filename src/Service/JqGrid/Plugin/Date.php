<?php

namespace Admin\Service\JqGrid\Plugin;


class Date extends AbstractPlugin
{
    protected $def_options =[
        "toformat"=>"Y-m-d",
    ];

public function edit($value)    
{
    $value=trim($value);
    if (empty($value)){return null;}
    $d=new \DateTime($value);
    return (string)$d->format($this->options["toformat"]);
}
public function add($value)    
{
    $value=trim($value);
    if (empty($value)){return null;}
    $d=new \DateTime($value);
    return (string)$d->format($this->options["toformat"]);
}
}
