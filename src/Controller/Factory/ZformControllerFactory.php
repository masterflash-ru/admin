<?php
namespace Admin\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

use Admin\Service\Zform\Zform;
use Zend\Form\View\Helper\FormElement;



/**
 */
class ZformControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $connection=$container->get('DefaultSystemDb');
        $config=$container->get("config");
        $cache = $container->get('DefaultSystemCache');
        $zform=$container->get(Zform::class);
        $formManager = $container->get('FormElementManager');
        
        //добавляем свои view элементов формы, возможно в будущем будет по другому
        $fe=$container->get('ViewHelperManager')->get(FormElement::class);
        $fe->addType('uploadimg', 'uploadImg');

       return new $requestedName( $connection,$cache,$config["interface"],$zform,$formManager);
    }
}

