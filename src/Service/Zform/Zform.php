<?php
namespace Admin\Service\Zform;

/*
*/
use Zend\Stdlib\ArrayUtils;
use Exception;
use DateTime;
use Zend\ServiceManager\ServiceManager;
use Zend\Form\Factory as FormFactory;

use Mf\Storage\Service\ImagesLib;

class Zform
{
    
    protected $plugins;
    protected $container;
    protected $options;
    /*описание колонок из конфига
    * нужно для обработки даты, т.к. присылаются они сюда в ru локали
    */
    protected $rowModel=[];
    
    /*имена колонок (POST ключей) 
    *для конвертирования
    */
    //protected $convert_fields=[];
    
    /*локаль в которой работает сетка*/
    protected $locale="ru";
    

    
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
        //смотрим описание колонок
        $this->rowModel=$options["layout"]["rowModel"];
    }


    /**
    * чтение массива данных
    */
    public function load(array $get=[])
    {
        $rez=[];
        //при помощи плагина читаем содержимое
        foreach ($this->options["read"] as $plugin_name=>$options){
            $plugin=$this->plugin($plugin_name);
            $plugin->setOptions($options);
            $rez=$plugin->read($get);
        }
        
        $factory=new FormFactory();
        $form    = $factory->createForm($this->rowModel);
        //\Zend\Debug\Debug::dump($rez);
        //пробежим по всем колонкам и проверим там наличие плагинов обработки
       /* foreach ($this->options["layout"]["rowModel"] as $rowModel ){
            if (isset($rowModel["plugins"]["read"])){
                //есть плагин/ны для обработки после чтения, применим его
                foreach ($rowModel["plugins"]["read"] as $plugin_name=>$options){
                    //пробежим по всем элементам данных и передадим в плагин значение и опции
                    $plugin=$this->plugin($plugin_name);
                    //добавим в опции 
                    $options["rowModel"]=$rowModel;
                    $plugin->setOptions($options);
                    foreach ($rez["rows"] as $index=>$value){
                        $rez["rows"][$index][$rowModel["name"]]=$plugin->read($rez["rows"][$index][$rowModel["name"]],$index,$value);
                    }
                }
            }
        }*/
        //наполнение формы данными
        foreach ($form as $fieldName=>$item){
            if (array_key_exists($fieldName,$rez)){
                $item->setValue($rez[$fieldName]);
            }
        }

        return $form;
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
        * надо или нет конвертировать проверяется из метаописаний rowModel
        **/

        if (!(isset($postParameters["oper"]) && in_array($postParameters["oper"],["add","edit","del"]))){
            return "";
        }
        //операция
        $oper=$postParameters["oper"];
        //пробежим по всем колонкам и проверим там наличие плагинов обработки
        foreach ($this->options["layout"]["rowModel"] as $rowModel ){
            if (isset($rowModel["plugins"][$oper])){
                //есть плагин/ны для обработки после чтения, применим его
                foreach ($rowModel["plugins"][$oper] as $plugin_name=>$options){
                    //пробежим по всем элементам данных и передадим в плагин значение и опции
                    $plugin=$this->plugin($plugin_name);
                    $options["rowModel"]=$rowModel;
                    $plugin->setOptions($options);
                    if (isset($postParameters[$rowModel["name"]])){
                        $postParameters[$rowModel["name"]]=$plugin->$oper($postParameters[$rowModel["name"]],$postParameters);
                    }
                    if ($oper=="del" && isset($postParameters["id"])){
                        $plugin->del($postParameters);
                    }
                }
            }
        }
        //при помощи плагина пишем содержимое
        $rez=[];
        if (!isset($this->options[$oper])) {
            throw new  Exception ("Операция $oper не описана в конфиге интерфейса");
        }
        foreach ($this->options[$oper] as $plugin_name=>$options){
            $plugin=$this->plugin($plugin_name);
            $plugin->setOptions($options);
            $r=$plugin->$oper($postParameters);
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