<?php
namespace Admin\Service\JqGrid\Plugin\Factory;

use Interop\Container\ContainerInterface;
use Mf\Storage\Service\ImagesLib;
/*

*/

class Images
{

public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
{
	$ImagesLib=$container->get(ImagesLib::class);
    $connection=$container->get('DefaultSystemDb');
    return new $requestedName($ImagesLib,$connection);
}
}

