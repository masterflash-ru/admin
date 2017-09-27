<?php
namespace Admin\Service\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Session\SessionManager;
use Admin\Service\AuthManager;
use Admin\Service\RbacManager;

/**
фабрика генерации менеджера авторизации
 */
class AuthManagerFactory implements FactoryInterface
{
    /**
     * собственно сам генератор. 
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {

        $authenticationService = $container->get(AuthenticationService::class);	//сервис авторации
        $sessionManager = $container->get(SessionManager::class);				//менеджер сессии
         $rbacManager = $container->get(RbacManager::class);
		//конфигурация
        $config = $container->get('Config');
        if (isset($config['access_filter']))
            $config = $config['access_filter'];
        else
            $config = [];
                        

        return new AuthManager($authenticationService, $sessionManager, $config,$rbacManager);
    }
}
