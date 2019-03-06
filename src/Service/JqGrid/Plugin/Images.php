<?php
namespace Admin\Service\JqGrid\Plugin;

/*
*/


class Images extends AbstractPlugin
{
    protected $ImagesLib;
    protected $def_options =[
        "image_id"=>"id",                        //имя поля с ID
        "storage_item_name" => "",              //имя секции в хранилище
        "storage_item_rule_name"=>"admin_img"   //имя правила из хранилища
    ];


    
    public function __construct($ImagesLib) 
    {
		$this->ImagesLib=$ImagesLib;
    }
    

/**
*/
public function read($value,$index,$row)
{
    $value=$this->ImagesLib->loadImage($this->options["storage_item_name"],$row[$this->options["image_id"]],$this->options["storage_item_rule_name"]);
    return $value;    
}



}