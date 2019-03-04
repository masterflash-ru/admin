<?php
namespace Admin\Service\JqGrid;

/*
*/
use Zend\Stdlib\ArrayUtils;
use Exception;
use DateTime;
use Interop\Container\ContainerInterface;

use Mf\Storage\Service\ImagesLib;

class JqGrid
{
    protected $container;
    protected $options;
    /*описание колонок из конфига
    * нужно для обработки даты, т.к. присылаются они сюда в ru локали
    */
    protected $colModel=[];
    
    /*имена колонок (POST ключей) 
    *для конвертирования
    */
    protected $convert_fields=[];
    
    /*локаль в которой работает сетка*/
    protected $locale="ru";
    
    protected $default_getparameters=[
        "_search"=>false,
        "rows"=>10,
        "page"=>1,
        "sidx"=>[],
        "sord"=>[]
    ];
    /**/

    
    public function __construct(ContainerInterface $container) 
    {
        $this->container=$container;
    }
    


    /**
    * установка опций из выбранного интерфейса
    * обязательно секция options из конфига!!!!
    */
    public function setOptions(array $options)
    {
        $this->options=$options;
       /*if (isset($options["locale"])){
            $this->locale=$options["locale"];
        }*/
        //смотрим описание колонок
        $this->colModel=$options["layout"]["colModel"];
        //пробежим по колонкам и поищем что конвертировать
        foreach ($options["layout"]["colModel"] as $col){
            if (!isset($col["formatter"])){
                continue;
            }
            switch ($col["formatter"]){
                case "date":{
                    $this->convert_fields[$col["name"]]="_dateConvert";
                    break;
                }
                case "datetime":{
                    $this->convert_fields[$col["name"]]="_datetimeConvert";
                    break;
                }
            }
            
        }
    }


    /**
    * чтение массива данных
    */
    public function load(array $getParameters=[])
    {
        //нормализация
        $get=[];
        foreach ($getParameters as $key=>$get_item){
            switch ($key){
                case "_search":{
                    if ($get_item=="true"){
                        $v=true;
                    } else {
                        $v=false;
                    }
                    break;
                }
                case "rows":
                case "page":{
                    $v=(int)$get_item;
                    break;
                }
                case "sidx":
                case "sord":{
                    if (!is_array($get_item)){
                        $v=[$get_item];
                    } else {
                        $v=$get_item;
                    }
                    break;
                }
            }
            $get[$key]=$v;
        }
        /*нормализуем GET параметры из грида*/
        $get=ArrayUtils::merge($this->default_getparameters,$get);
        
        $adapter=$this->container->get($this->options["read"]["adapter"]);
        $rez= $adapter->read($get,$this->options["read"]["options"]);
        
        
        foreach ($this->options["layout"]["colModel"] as $colModel ){
            if (isset($colModel["helpers"]["read"])){
                $h=new $colModel["helpers"]["read"]["helper"];
                
                foreach ($rez["rows"] as $k=>$v){
                    $rez["rows"][$k][$colModel["name"]]=$h($rez["rows"][$k][$colModel["name"]]);
                }

            }
            
        }
        
        
        $ImagesLib=$this->container->get(ImagesLib::class);
        foreach ($rez["rows"] as $k=>$v){
            $rez["rows"][$k]["img"]=$ImagesLib->loadImage("news",$v["img"],"admin_img");
        }
        
        
        
        return $rez;
    }


    /**
    * редактирование записей
    Array
(
- массив самих данных
    [oper] => edit - операция
    [id] => 15197 - ID
)
    */
    public function edit(array $postParameters=[])
    {
        /*пробежим по полям и если нужно, конвертируем
        * надо или нет конвертировать проверяется из метаописаний colModel
        **/
        foreach ($postParameters as $k=>$v){
            if (array_key_exists($k,$this->convert_fields)){
                $cc=$this->convert_fields[$k];
                $postParameters[$k]=$this->$cc($postParameters[$k]);
            }
        }
        
        //выполним хелперы, если они есть
        
        
        $adapter=$this->container->get($this->options["write"]["adapter"]);
        return $adapter->write($postParameters,$this->options["write"]["options"]);



    }



/**
* внутренняя для конвертирования даты из ru в ISO
* $in - входное значение
* возвращает конвертированный вариант
*/
protected function _dateConvert($in)
{
    $in=trim($in);
    if (empty($in)){return null;}
    $d=new DateTime($in);
    return (string)$d->format('Y-m-d');
}

/**
* внутренняя для конвертирования даты-времени из ru в ISO
* $in - входное значение
* возвращает конвертированный вариант
*/
protected function _datetimeConvert($in)
{
    $in=trim($in);
    if (empty($in)){return null;}
    $d=new DateTime($in);
    return (string)$d->format('Y-m-d H:i:s');
}

}