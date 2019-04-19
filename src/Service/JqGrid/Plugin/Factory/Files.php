<?php
namespace Admin\Service\JqGrid\Plugin\Factory;

use Interop\Container\ContainerInterface;
use Mf\Storage\Service\FilesLib;
/*

*/

class Files
{

public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
{
	$FilesLib=$container->get(FilesLib::class);
    $connection=$container->get('DefaultSystemDb');
    return new $requestedName($FilesLib,$connection);
}
}

