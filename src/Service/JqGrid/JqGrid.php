<?php
namespace Admin\Service\JqGrid;

/*
*/
use Zend\Stdlib\ArrayUtils;
use Exception;
use DateTime;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\ServiceManager;

use Mf\Storage\Service\ImagesLib;

class JqGrid
{
    
    protected $plugins;
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

    
    public function __construct($pluginManager) 
    {
        $this->setPluginManager($pluginManager);
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
        
        //при помощи плагина читаем содержимое
        foreach ($this->options["read"] as $plugin_name=>$options){
            $plugin=$this->plugin($plugin_name);
            $plugin->setOptions($options);
            $rez=$plugin->read($get);
        }

        //пробежим по всем колонкам и проверим там наличие плагинов обработки
        foreach ($this->options["layout"]["colModel"] as $colModel ){
            if (isset($colModel["plugins"]["read"])){
                //есть плагин/ны для обработки после чтения, применим его
                foreach ($colModel["plugins"]["read"] as $plugin_name=>$options){
                    //пробежим по всем элементам данных и передадим в плагин значение и опции
                    $plugin=$this->plugin($plugin_name);
                    //добавим в опции 
                    $plugin->setOptions($options);
                    foreach ($rez["rows"] as $index=>$value){
                        $rez["rows"][$index][$colModel["name"]]=$plugin->read($rez["rows"][$index][$colModel["name"]],$index,$value);
                    }
                }
            }
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

        //пробежим по всем колонкам и проверим там наличие плагинов обработки
        foreach ($this->options["layout"]["colModel"] as $colModel ){
            if (isset($colModel["plugins"]["write"])){
                //есть плагин/ны для обработки после чтения, применим его
                foreach ($colModel["plugins"]["write"] as $plugin_name=>$options){
                    //пробежим по всем элементам данных и передадим в плагин значение и опции
                    $plugin=$this->plugin($plugin_name);
                    $plugin->setOptions($options);
                    $postParameters[$colModel["name"]]=$plugin->write($postParameters[$colModel["name"]],$postParameters);
                }
            }
        }
        //при помощи плагина пишем содержимое
        $rez=[];
        foreach ($this->options["write"] as $plugin_name=>$options){
            $plugin=$this->plugin($plugin_name);
            $plugin->setOptions($options);
            $r=$plugin->write($postParameters);
            if (!empty($r)){
                $rez[]=$r;
            }
        }
        return implode("",$rez);
    }

/**
* для прямого обращения к плагинам
*/
    public function __call($method, $params)
    {
        $plugin = $this->plugin($method);
        if (is_callable($plugin)) {
            return call_user_func_array($plugin, $params);
        }

        return $plugin;
    }
    /**
     * Get plugin manager instance
     *
     * @return PluginManager
     */
    public function getPluginManager()
    {
        if (! $this->plugins) {
            $this->setPluginManager(new PluginManager(new ServiceManager));
        }
        return $this->plugins;
    }

    /**
     * Set plugin manager instance
     *
     * @param  $plugins Plugin manager
     * @return 
     */
    public function setPluginManager(PluginManager $plugins)
    {
        $this->plugins = $plugins;
        return $this;
    }
    /**
     * Retrieve a  by name
     *
     * @param  string     $name    Name of  to return
     * @param  null|array $options Options to pass to constructor (if not already instantiated)
     * @return 
     */
    public function plugin($name, array $options = null)
    {
        $plugins = $this->getPluginManager();
        return $plugins->get($name, $options);
    }

}