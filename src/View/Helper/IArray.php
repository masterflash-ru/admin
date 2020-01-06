<?php
namespace Admin\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Laminas\Stdlib\ArrayUtils;
use Exception;


/**
 * помощник -  для вывода массива интерфейсов админки
 */
class IArray extends AbstractHelper 
{
    protected $config;
    protected $def_config=[
        "array"=>[]
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
    $config=$this->config[$interface];
    $config=include $config;
    $config=ArrayUtils::merge($this->def_config,$config);
    $rez=[];
    foreach ($config["array"] as $interface){
        //читаем конфиг, что бы определить тип
        $config_item=include $this->config[$interface];
        $type=$config_item["type"];
        $rez[]=$this->getView()->$type($interface);
    }
    return implode("\n",$rez);
}
}
