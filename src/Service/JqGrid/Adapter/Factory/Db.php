<?php
namespace Admin\Service\JqGrid\Adapter\Factory;

use Interop\Container\ContainerInterface;

/*

*/

class Db
{

public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
{
	$connection=$container->get('DefaultSystemDb');
    return new $requestedName($connection);
}
}

