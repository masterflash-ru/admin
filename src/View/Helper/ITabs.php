<?php
namespace Admin\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Stdlib\ArrayUtils;
use Exception;


/**
 * помощник -  для вывода интерфейсов админки в табах
 */
class ITabs extends AbstractHelper 
{
    protected static $containers=[];
    protected $config;
    protected $def_options=[
        "container" => "my1",
        "caption" => "",
        "podval" => "",
        "tabs"=>[]
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
    
    $tabs=["<ul>"];
    $containers=[];
    $container=$options["container"];
    if (in_array($container,static::$containers)){
        throw new  \Exception("Не допускается повторения имен контейнеров: ".$container." в интерфейсе $interface");
    }
    static::$containers[]=$container;
    foreach ($options["tabs"] as $k=>$item){
        $tabs[]="<li><a href=\"#{$container}-{$k}\">".$item["label"]."</a></li>";
        $interface=strtolower($item["interface"]);
        $config_item=include $this->config[$interface];
        $type=$config_item["type"];
        $containers[]="<div id=\"{$container}-{$k}\">".$this->getView()->$type($interface)."</div>";
    }
    $tabs[]="</ul>";
    return $options["caption"]."<div id=\"$container\">".implode("\n",$tabs).implode("\n",$containers)."</div>
    <script>$('#{$container}').tabs();</script>".$options["podval"];
}
}
