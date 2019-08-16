<?php
namespace Admin\Service\JqGrid\Plugin\Factory;

use Interop\Container\ContainerInterface;

/*

*/

class SelectConfigStorage
{

public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
{
	$config=$container->get('config');
    return new $requestedName($config);
}
}

