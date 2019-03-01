<?php
namespace Admin\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Stdlib\ArrayUtils;
use Exception;



/**
 * помощник - универсальный для вывода интерфейса админки
 */
class IJqgrid extends AbstractHelper 
{
    protected $config;
    protected $def_options=[
        "container" => "my1",
        "caption" => "",
        "podval" => "",
        "read"=>[],
        "layout"=>[]
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

    //\Zend\Debug\Debug::dump($options);
    return $this->getView()->partial("admin/jq-grid/index",["options"=>$options,"interface"=>$interface]);
    return "IJqgrid";
}
}
