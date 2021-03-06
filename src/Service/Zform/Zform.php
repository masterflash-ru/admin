<?php
namespace Admin\Service\Zform;

/*
*/
use Laminas\Stdlib\ArrayUtils;

use Laminas\ServiceManager\ServiceManager;
use Laminas\Form\Factory as FormFactory;
use Laminas\Form\FormInterface;
use Laminas\Validator\AbstractValidator;
use Mf\Storage\Service\ImagesLib;

class Zform
{
    
    protected $plugins;
    protected $container;
    protected $options;
    //protected $translator;
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
    

    
    public function __construct($pluginManager,$translator) 
    {
        $this->setPluginManager($pluginManager);
       // $this->translator=$translator;
        $translator->setLocale("ru");
        AbstractValidator::setDefaultTranslator($translator);
    }
    


    /**
    * установка опций из выбранного интерфейса
    * обязательно секция options из конфига!!!!
    */
    public function setOptions(array $options)
    {
        $this->options=$options;
    }


    /**
    * чтение массива данных 
    * $form - экземпляр созданной формы
    * $get - GET данные
    */
    public function load(FormInterface $form,array $get=[])
    {
        $rez=[];
        //при помощи плагина читаем содержимое
        if (!empty($this->options["read"])){
            foreach ($this->options["read"] as $plugin_name=>$options){
                $plugin=$this->plugin($plugin_name);
                $plugin->setOptions($options);
                $rez=$plugin->iread($get,$form);
            }
        }
        
        $this->initForm($form);

        //пробежим по всем строкам формы и проверим там наличие плагинов обработки НАЧАЛЬНЫХ ЗНАЧЕНИЙ
        //которые будут переданы в элементы формы

        foreach ($this->options["layout"]["rowModel"]['elements'] as $rowModel ){
            if (isset($rowModel["spec"]["plugins"]["read"]) && strtolower($rowModel["spec"]["type"])!=="dynamicarray"){
                //есть плагин/ны для обработки после чтения, применим его
                foreach ($rowModel["spec"]["plugins"]["read"] as $plugin_name=>$options){
                    //пробежим по всем элементам данных и передадим в плагин значение и опции
                    $plugin=$this->plugin($plugin_name);
                    //добавим в опции 
                    $options["rowModel"]=$rowModel["spec"];
                    $plugin->setOptions($options);
                    $rez[$rowModel["spec"]["name"]]=$plugin->read($rez[$rowModel["spec"]["name"]],$form);
                }
            }
        }
        //проверим ключ на наличие
       /* if (empty($rez[$rez["PrimaryKeyName"]])){
            throw new Exception\PrimaryKeyEmptyException("Первичный ключ не найден или он пустой");
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
    */
    public function edit(FormInterface $form,array $postParameters=[], array $getParameters=[])
    {

        //пробежим по всем колонкам и проверим там наличие плагинов обработки
        foreach ($this->options["layout"]["rowModel"]['elements'] as $rowModel ){
            if (isset($rowModel["spec"]["plugins"]["edit"])){
                //есть плагин/ны для обработки после чтения, применим его
                foreach ($rowModel["spec"]["plugins"]["edit"] as $plugin_name=>$options){
                    //пробежим по всем элементам данных и передадим в плагин значение и опции
                    $plugin=$this->plugin($plugin_name);
                    $options["rowModel"]=$rowModel;
                    $plugin->setOptions($options);
                    if (isset($postParameters[$rowModel["spec"]["name"]])){
                        $postParameters[$rowModel["spec"]["name"]]=$plugin->edit($postParameters[$rowModel["spec"]["name"]],$postParameters,$getParameters,$form);
                    }
                }
            }
        }
        //при помощи плагина пишем содержимое
        $rez=[];
        if (!isset($this->options["edit"])) {
            throw new  Exception\ConfigError ("Операция edit не описана в конфиге интерфейса");
        }
        foreach ($this->options["edit"] as $plugin_name=>$options){
            $plugin=$this->plugin($plugin_name);
            $plugin->setOptions($options);
            $r=$plugin->iedit($postParameters,$getParameters,$form);
            if (!empty($r)){
                $rez[]=$r;
            }
        }
        return implode("",$rez);
    }


/**
* инициализация формы, наполнение ее выпадающих список и прочее, то что указано в конфиге
* данные беруться из плагинов обработки, указанных в конфиге
* ничего не возвращает
* на входе $form - экземпляр формы
*/
    public function initForm(FormInterface $form)
    {
        //пройдем по всем моделям колонок и исполним там плагины, если они есть
        // предназначено для формирования самих полей через их конфиг, например, наполнение выпадающих списков значениями
        foreach ($this->options["layout"]["rowModel"]['elements'] as $rowModel){
            if (isset($rowModel["spec"]["plugins"]) && is_array($rowModel["spec"]["plugins"])){
                foreach ($rowModel["spec"]["plugins"] as $plugin_group=>$plugins){
                    if ($plugin_group=="rowModel"){
                        foreach ($plugins as $plugin=>$plugin_options){
                            $plugin=$this->plugin($plugin);
                            $plugin->setOptions($plugin_options);
                            $plugin->rowModel($rowModel["spec"],$form);
                        }
                    }
                }
            }
        }

    }

/*
* обработчик динамических полей, подается конфигурация формы,
* эта функция ищет объявления динамических полей и конвертирует в стандартную конфигурацию
* для генерации штатной Laminas фабрикой форм
**/
    public function handlingDynamicFields(array $rowModelArray)
    {
        //собираем элменты формы заново
        $elements=[];
        $input_filter=[];
        foreach ($rowModelArray["elements"] as $k=>$spec_item){
            if (strtolower($spec_item["spec"]["type"])=="dynamicarray"){
                //массив дин. полей найден
                // в опциях есть элемент fields - в нем массив полей (статично), таких же как объявляются Admin\Service\Zform\RowModelHelper
                //вместо элемента dynamicarray вставляем значения, т.е. массив других полей
                foreach ($spec_item["spec"]["fields"] as $field_item){
                    $elements[]=$field_item;
                }
                //аналогично добавляем входные фильтры и валидаторы
                foreach ($spec_item["spec"]["input_filter"] as $field_item){
                    $input_filter[]=$field_item;
                }
            
                //выполним плагины, если они есть в ключе plugins
                foreach ($spec_item["spec"]["plugins"]["read"] as $plugin=>$plugin_options){
                    $plugin=$this->plugin($plugin);
                    $plugin->setOptions($plugin_options);
                    $rez=ArrayUtils::merge($elements,$plugin->ReadDynamicArray($spec_item["spec"]));
                    $elements=$rez["elements"];
                    if (!empty($rez["input_filter"])){//если есть фильтры, добавим в общий массив
                        $rowModelArray["input_filter"]=ArrayUtils::merge($rowModelArray["input_filter"],$rez["input_filter"]);
                    }
                }
            } else {
                $elements[]=$spec_item;
            }
        }
        $rowModelArray["elements"]=$elements;
        return $rowModelArray;
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