<?php
namespace Admin\Service\JqGrid\Factory;

use Interop\Container\ContainerInterface;
use Zend\Validator\Translator\TranslatorInterface;

use Admin\Filter\FilterPluginManager;
/*

*/

class JqGridFactory
{

public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
{
    $pluginManager=$container->get('JqGridManager');
    $translator = $container->get(TranslatorInterface::class);
    return new $requestedName($pluginManager,$translator);
}
}

