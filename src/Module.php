<?php
/**
* админка с говнокодом
 */

namespace Admin;

use Zend\Mvc\MvcEvent;
use Zend\Session\Container;
use Zend\EventManager\Event;
use Admin\Service\GetControllersInfo;

class Module
{

public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }


public function onBootstrap(MvcEvent $event)
{
    
	$eventManager = $event->getApplication()->getEventManager();
    $sharedEventManager = $eventManager->getSharedManager();
    // объявление слушателя для изменения макета на админский + проверка авторизации root
    $sharedEventManager->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch'], 1);

	//слушатель для получения списка описания контроллеров, методов для визуального создания меню
	$sharedEventManager->attach("simba.admin", "GetControllersInfoAdmin", [$this, 'GetControllersInfoAdmin']);
}

/*слушатель для проверки авторизован ли админ*/
public function onDispatch(MvcEvent $event)
 {
    //для данного модуля изменить макет
    $controllerName = $event->getRouteMatch()->getParam('controller', null);
    if (false === strpos($controllerName, __NAMESPACE__)) { return; }

    $controller = $event->getTarget();
    $user=$controller->User()->getUserId();
    /*имя метода контроллера*/
    $actionName = $event->getRouteMatch()->getParam('action', null);
    $actionName = str_replace('-', '', lcfirst(ucwords($actionName, '-')));
    

    $viewModel = $event->getViewModel();
    /*проверяем доступ по имени контроллера и метода, без Action*/
    $acl=$controller->acl()->isAllowed("x",[$controllerName,$actionName]);

    if (!$acl){
        if ($user!=1){
            //авторизованы, но доступ запрещен
            $controller->redirect()->toRoute('accessdenied');
            return;
        } else {
            //получилось, что root -у доступа нет, выводим сообщение
            echo "получилось, что root -у доступа нет к <b>{$controllerName}/{$actionName}</b><br>Проверьте таблицу доступа";
            $viewModel->setTemplate('layout/admin_layout');
            return;
        }
        $viewModel->setTemplate('layout/admin_layout_empty');
        return;
    }
    if ($controllerName!="Admin\Controller\LoginController") {
        /*для всех контроллеров меняем макет вывода*/
		$viewModel->setTemplate('layout/admin_layout');
	} 
}

/*
слушает событие GetControllersInfoAdmin 
для визуаллизации в админке маршрутов/путей в меню админки
в параметрах передается:
name=>имя_раздела "admin", ""
container - объект с интерфейсом Interop\Container\ContainerInterface - то что передается в фабрики
*/
public function GetControllersInfoAdmin(Event $event)
{
	$name=$event->getParam("name",NULL);
	$container=$event->getParam("container",NULL);
	
	//сервис который будет возвращать
	$service=$container->build("Admin\Service\GetControllersInfo",["name"=>$name]);
	return $service->GetDescriptors();
}

}
