<?php

namespace Admin\Service\JqGrid\Plugin;


class Date extends AbstractPlugin
{
    
public function edit($value)    
{
    $value=trim($value);
    if (empty($value)){return null;}
    $d=new \DateTime($value);
    return (string)$d->format('Y-m-d');
}
public function add($value)    
{
    $value=trim($value);
    if (empty($value)){return null;}
    $d=new \DateTime($value);
    return (string)$d->format('Y-m-d');
}
}
