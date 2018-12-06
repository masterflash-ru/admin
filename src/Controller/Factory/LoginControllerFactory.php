<?php
namespace Admin\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Admin\Controller\LoginController;

use Mf\Users\Service\AuthManager;

use Zend\Authentication\AuthenticationService;

use Zend\Session\SessionManager;

/**
 */
class LoginControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $authManager = $container->get(AuthManager::class);
        $authService = $container->get(AuthenticationService::class);
       $sessionManager = $container->get(SessionManager::class);
       return new LoginController( $authManager, $authService,$sessionManager);
    }
}

