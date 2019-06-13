<?php
namespace Admin\Service\Zform\Plugin\Factory;

use Interop\Container\ContainerInterface;
use Mf\Storage\Service\ImagesLib;
/*

*/

class Images
{

public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
{
    if ($container->has(ImagesLib::class)){
        $ImagesLib=$container->get(ImagesLib::class);
    } else {
        $ImagesLib=null;
    }
    $connection=$container->get('DefaultSystemDb');
    return new $requestedName($ImagesLib,$connection);
}
}

