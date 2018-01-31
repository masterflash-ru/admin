<?php
/**
вход в админку, вывод формы авторизации 
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;

use Admin\Form\LoginForm;
use Zend\Hydrator\Reflection as ReflectionHydrator;
use Admin\Entity\Login as Login_Admin;

use Zend\Authentication\Result;




class LoginController extends AbstractActionController
{
	protected $authManager;
	protected $authService;
	protected $sessionManager;


public function __construct ($authManager, $authService,$sessionManager)
	{
		$this->authManager=$authManager;
		$this->authService=$authService;
		$this->sessionManager=$sessionManager;
	}


/*
*просто форма авторизации вывод
*точка входа авторизации и входа в админку
*/
public function loginAction()
{
	$form = new LoginForm();
	$viewModel=new ViewModel(["form"=>$form]);
  return $viewModel;
}

/*
собственно авторизация
сюда переходим при входе в админку
*/
public function dologinAction()
{
	/*резаультат формы авторизации в объект*/
	$form = new LoginForm();
	$form->setHydrator(new ReflectionHydrator);
	$admins=new Login_Admin;
  	$form->bind($admins);
	
	$form->setData($this->params()->fromPost());
	
	if (!$form->isValid()) {/*ошибка*/
		return $this->redirect()->toRoute('admin');
	}

	$result = $this->authManager->login($admins->getLogin(), $admins->getPassword());
	/*получим результат авторизации в виде и смотрим результат*/
	if ($result->getCode()!=Result::SUCCESS) {
		return $this->redirect()->toRoute('admin');//ошибка
	}
	/*доступ разрешен, редирект*/
	return $this->redirect()->toRoute('adm');
}

public function e403Action()
{
	$this->getResponse()->setStatusCode(403);
	return new ViewModel();
}



/*
     * Мы переопределяем метод родительского класса onDispatch(),
     * чтобы установить альтернативный лэйаут для всех действий в этом контроллере.
     */
    public function onDispatch(MvcEvent $e) 
    {
        // Вызываем метод базового класса onDispatch() и получаем ответ
        $response = parent::onDispatch($e);        
        // Устанавливаем admin лэйаут
        $this->layout()->setTemplate('layout/login_layout');                
        // Возвращаем ответ
        return $response;
    }


}
