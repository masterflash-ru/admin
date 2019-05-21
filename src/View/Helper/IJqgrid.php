<?php
namespace Admin\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Stdlib\ArrayUtils;
use Exception;
use Zend\ServiceManager\ServiceManager;
use Admin\Service\JqGrid\PluginManager;
use Admin\Service\Zform\PluginManager as ZformPluginManager;
use Admin\Service\Zform\Exception as ZformException;
use Zend\Form\Factory as FormFactory;


/**
 * помощник - универсальный для вывода интерфейса админки
 */
class IJqgrid extends AbstractHelper 
{
    protected $config;
    protected $plugins;
    protected $zplugins;
    protected $def_options=[
        "container" => "my1",
        "caption" => "",
        "podval" => "",
        "read"=>[],
        "write"=>[],
        "layout"=>["colModel"=>[]]
    ];

public function __construct ($config,$pluginManager,$ZformpluginManager)
{
	$this->config=$config;
    $this->setPluginManager($pluginManager);
    $this->ZsetPluginManager($ZformpluginManager);
}

/**
* собственно вызов вывода инетрфейса
* на входе имя интерфейса из конфига приложения
*/
public function __invoke(string $interface)
{
    
    $options=$this->config[$interface];
    $options=include $options;
    $options=ArrayUtils::merge($this->def_options,$options["options"]);

    //пройдем по всем моделям колонок и исполним там плагины, если они есть
    foreach ($options["layout"]["colModel"] as &$colModel){
        if (isset($colModel["plugins"]) && is_array($colModel["plugins"])){
            foreach ($colModel["plugins"] as $plugin_group=>$plugins){
                if ($plugin_group=="colModel"){
                    foreach ($plugins as $plugin=>$plugin_options){
                        $plugin=$this->plugin($plugin);
                        $plugin->setOptions($plugin_options);
                        $colModel=$plugin->colModel($colModel);
                    }
                }
            }
            
            
        }
    }
    //формируем toolbar из элементов формы Zend, если есть
    if (isset($options["layout"]["rowModel"])){
        $factory=new FormFactory();
        $form  = $factory->createForm($options["layout"]["rowModel"]);
        $rez=[];
        //пройдем по всем моделям колонок и исполним там плагины, если они есть
        // предназначено для формирования самих полей через их конфиг, например, наполнение выпадающих списков значениями
        foreach ($options["layout"]["rowModel"]['elements'] as $rowModel){
            if (isset($rowModel["spec"]["plugins"]) && is_array($rowModel["spec"]["plugins"])){
                foreach ($rowModel["spec"]["plugins"] as $plugin_group=>$plugins){
                    if ($plugin_group=="rowModel"){
                        foreach ($plugins as $plugin=>$plugin_options){
                            $plugin=$this->Zplugin($plugin);
                            $plugin->setOptions($plugin_options);
                            $plugin->rowModel($rowModel["spec"],$form);
                        }
                    }
                }
            }
        }
        //пробежим по всем строкам формы и проверим там наличие плагинов обработки НАЧАЛЬНЫХ ЗНАЧЕНИЙ
        //которые будут переданы в элементы формы
        foreach ($options["layout"]["rowModel"]['elements'] as $rowModel ){
            if (isset($rowModel["spec"]["plugins"]["read"])){
                //есть плагин/ны для обработки после чтения, применим его
                foreach ($rowModel["spec"]["plugins"]["read"] as $plugin_name=>$options){
                    //пробежим по всем элементам данных и передадим в плагин значение и опции
                    $plugin=$this->Zplugin($plugin_name);
                    //добавим в опции 
                    $options["rowModel"]=$rowModel["spec"];
                    $plugin->setOptions($options);
                    $rez[$rowModel["spec"]["name"]]=$plugin->read($rez[$rowModel["spec"]["name"]]);
                }
            }
        }
        //наполнение формы данными
        foreach ($form as $fieldName=>$item){
            if (array_key_exists($fieldName,$rez)){
                $item->setValue($rez[$fieldName]);
            }
        }

    } else {
        $form=null;
    }

    
    
    
    return $this->getView()->partial("admin/jq-grid/index",["options"=>$options,"interface"=>$interface,"toolbarForm"=>$form]);
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

    
    
    /**
     * Get plugin manager instance
     *
     * @return PluginManager
     */
    public function ZgetPluginManager()
    {
        if (! $this->zplugins) {
            $this->ZsetPluginManager(new ZformPluginManager(new ServiceManager));
        }
        return $this->zplugins;
    }

    /**
     * Set plugin manager instance
     *
     * @param  $plugins Plugin manager
     * @return 
     */
    public function ZsetPluginManager(ZformPluginManager $plugins)
    {
        $this->zplugins = $plugins;
        return $this;
    }
    /**
     * Retrieve a  by name
     *
     * @param  string     $name    Name of  to return
     * @param  null|array $options Options to pass to constructor (if not already instantiated)
     * @return 
     */
    public function Zplugin($name, array $options = null)
    {
        $plugins = $this->ZgetPluginManager();
        return $plugins->get($name, $options);
    }


}
