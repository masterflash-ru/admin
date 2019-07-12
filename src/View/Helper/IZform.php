<?php
namespace Admin\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Stdlib\ArrayUtils;

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
    return $this->getView()->partial("admin/zform/index",["options"=>$options,"interface"=>$interface]);
}
    

}
