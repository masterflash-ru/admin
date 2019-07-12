<?php
namespace Admin\View\Helper;

use Zend\View\Helper\AbstractHelper;



/**
 * помощник - универсальный для вывода интерфейса админки
 */
class IUniversal extends AbstractHelper 
{
    protected $config;

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
    //это тип интерфейса
    $type=strtolower($config["type"]);
    //передаем управление тому типу, который указан
    return $this->getView()->$type($interface);
}
}
