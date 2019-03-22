<?php
namespace Admin\Service\JqGrid\Plugin;

/*
*/
use Admin\Service\JqGrid\Plugin\AbstractPlugin;




class LastMod extends AbstractPlugin

{
public function add($value,$postParameters)
{
    return $this->edit($value,$postParameters);
}
public function edit($value,$postParameters)
{

    return date("Y-m-d H:i:s");    
}




}