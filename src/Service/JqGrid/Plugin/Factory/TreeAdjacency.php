<?php
namespace Admin\Service\JqGrid\Plugin\Factory;

use Interop\Container\ContainerInterface;

/*

*/

class TreeAdjacency
{

public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
{
	$connection=$container->get('DefaultSystemDb');
    return new $requestedName($connection);
}
}

