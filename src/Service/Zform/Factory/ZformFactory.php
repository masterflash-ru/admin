<?php
namespace Admin\Service\JqGrid\Factory;

use Interop\Container\ContainerInterface;

/*

*/

class ZformFactory
{

public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
{
    $pluginManager=$container->get('ZformManager');
    return new $requestedName($pluginManager);
}
}

