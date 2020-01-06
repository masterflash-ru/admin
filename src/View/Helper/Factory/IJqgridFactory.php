<?php
namespace Admin\View\Helper\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;


/**
 * 
 * 
 */
class IJqgridFactory implements FactoryInterface
{
public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
{
    $config=$container->get('config');
    $pluginManager=$container->get('JqGridManager');
    $ZformpluginManager=$container->get('ZformManager');
    return new $requestedName($config["interface"],$pluginManager,$ZformpluginManager);
}
}
