<?php
namespace Admin\Service\Zform\Plugin\Factory;

use Interop\Container\ContainerInterface;

/*

*/

class Locale
{

public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
{
	$config=$container->get('config');
    if (empty($config["locale_enable_list"])){
        $config["locale_enable_list"]=[$config["locale_default"]];
    }
    return new $requestedName($config["locale_enable_list"]);
}
}

