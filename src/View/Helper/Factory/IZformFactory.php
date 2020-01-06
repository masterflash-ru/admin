<?php
namespace Admin\View\Helper\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;


/**
 * 
 * 
 */
class IZformFactory implements FactoryInterface
{
public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
{
    $config=$container->get('config');

    return new $requestedName($config["interface"]);
}
}
