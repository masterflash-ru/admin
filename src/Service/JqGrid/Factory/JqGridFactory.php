<?php
namespace Admin\Service\JqGrid\Factory;

use Interop\Container\ContainerInterface;

/*

*/

class JqGridFactory
{

public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
		//$connection=$container->get('DefaultSystemDb');
        $config=$container->get('config');

        return new $requestedName($container);
    }
}

