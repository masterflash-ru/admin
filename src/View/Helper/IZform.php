<?php
namespace Admin\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Stdlib\ArrayUtils;
use Exception;
use Zend\ServiceManager\ServiceManager;



/**
 * помощник - формы Zend для построения инетрфейсов
 */
class IZform extends AbstractHelper 
{
    protected $config;
    protected $def_options=[
        "container" => "my1",
        "caption" => "",
        "podval" => "",
        "read"=>[],
        "write"=>[],
    ];

public function __construct ($config)
{
	$this->config=$config;

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
    /*foreach ($options["layout"]["colModel"] as &$colModel){
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
    }*/
    
    return $this->getView()->partial("admin/zfrom/index",["options"=>$options,"interface"=>$interface]);
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
