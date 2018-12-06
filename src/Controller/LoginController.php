<?php
/**
вход в админку, вывод формы авторизации 
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;

use Admin\Form\LoginForm;
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
    
    $prg = $this->prg();
    if ($prg instanceof Response) {
        //сюда попадаем когда форма отправлена, производим редирект
        return $prg;
    }

    $view=new ViewModel();

    $form = new LoginForm();    
    
    if ($prg === false){
      //вывод страницы и формы
      $view->setVariables(["form"=>$form]);
      return $view;
    }

    $form->setData($prg);

    if ($form->isValid()) {
        //данные в норме
        $info=$form->getData();

        $result = $this->authManager->login($info["login"], $info["password"]);
        /*получим результат авторизации в виде и смотрим результат*/
        if ($result->getCode()==Result::SUCCESS) {
            /*успешная авторизация*/
           return $this->redirect()->toRoute('adm');
        }
        $view->setVariables(["error"=>true]);
    }

    $view->setVariables(["form"=>$form]);
    return $view;
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
