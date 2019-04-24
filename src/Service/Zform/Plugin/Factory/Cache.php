<?php
namespace Admin\Service\Zform\Plugin\Factory;

use Interop\Container\ContainerInterface;

/*

*/

class Cache
{

public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
{
	$cache=$container->get('DefaultSystemCache');
    return new $requestedName($cache);
}
}

