<?php

namespace Admin\Service\JqGrid\Plugin;


class Datetime extends AbstractPlugin
{
    protected $def_options =[
        "toformat"=>"Y-m-d H:i:s",
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
