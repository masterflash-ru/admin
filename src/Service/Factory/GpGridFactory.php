<?php
namespace Admin\Service\Factory;

use Interop\Container\ContainerInterface;

/*

*/

class GpGridFactory
{

public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
		$connection=$container->get('DefaultSystemDb');
        $config=$container->get('config');

        return new $requestedName($connection,$config);
    }
}

