<?php
namespace Admin\Service\Zform;

/*
*/
use Zend\Stdlib\ArrayUtils;

use Zend\ServiceManager\ServiceManager;
use Zend\Form\Factory as FormFactory;
use Zend\Form\FormInterface;
use Zend\Validator\AbstractValidator;
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
                $rez=$plugin->read($get);
            }
        }
        
        $this->initForm($form);
        
        //пробежим по всем строкам формы и проверим там наличие плагинов обработки НАЧАЛЬНЫХ ЗНАЧЕНИЙ
        //которые будут переданы в элементы формы
        foreach ($this->options["layout"]["rowModel"]['elements'] as $rowModel ){
            if (isset($rowModel["spec"]["plugins"]["read"])){
                //есть плагин/ны для обработки после чтения, применим его
                foreach ($rowModel["spec"]["plugins"]["read"] as $plugin_name=>$options){
                    //пробежим по всем элементам данных и передадим в плагин значение и опции
                    $plugin=$this->plugin($plugin_name);
                    //добавим в опции 
                    $options["rowModel"]=$rowModel["spec"];
                    $plugin->setOptions($options);
                    $rez[$rowModel["spec"]["name"]]=$plugin->read($rez[$rowModel["spec"]["name"]]);
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
    GET - параметры если есть:
    array(1) {
  ["id"] => string(2) "11"
}
POST параметры из формы
array(6) {
  ["login"] => string(5) "admin"
  ["name"] => string(2) "11"
  ["full_name"] => string(4) "2234"
  ["status"] => string(1) "3"
  ["date_registration"] => string(19) "18.04.2019 00:00:00"
  ["gr"] => array(1) {
    [0] => string(1) "2"
  }
}

    */
    public function edit(array $postParameters=[], array $getParameters=[])
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
                        $postParameters[$rowModel["spec"]["name"]]=$plugin->edit($postParameters[$rowModel["spec"]["name"]],$postParameters,$getParameters);
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
            $r=$plugin->edit($postParameters,$getParameters);
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