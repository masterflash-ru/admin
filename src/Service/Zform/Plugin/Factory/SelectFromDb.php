<?php
namespace Admin\Service\Zform\Plugin\Factory;

use Interop\Container\ContainerInterface;

/*

*/

class SelectFromDb
{

public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
{
    $connection=$container->get('DefaultSystemDb');
    return new $requestedName($connection);
}
}

