<?php
namespace Admin\Service\JqGrid\Factory;

use Interop\Container\ContainerInterface;

/*

*/

class JqGridFactory
{

public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
{
    $pluginManager=$container->get('JqGridManager');
    return new $requestedName($pluginManager);
}
}
