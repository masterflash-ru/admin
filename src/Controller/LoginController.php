<?php
/**


для получения главного сервис-менеджера
$this->getEvent()->getApplication()->getServiceManager()

//получить конфиг приложения
$this->getEvent()->getApplication()->GetConfig()
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
		//\Zend\Debug\Debug::dump($_SESSION);
	}


/*
просто форма авторизации вывод
*/
public function loginAction()
{
	$form = new LoginForm();
  return new ViewModel(["form"=>$form]);
}

/*
собственно авторизация
сюда переходим при входе в админку
*/
public function dologinAction()
{
	$form = new LoginForm();
	$form->setHydrator(new ReflectionHydrator);
	$admins=new Login_Admin;
  	$form->bind($admins);
	
	$form->setData($this->params()->fromPost());
	
	if (!$form->isValid()) {return $this->redirect()->toRoute('admin');}

	$result = $this->authManager->login($admins->getLogin(), $admins->getPassword());  

  if ($result->getCode()!=Result::SUCCESS)  {return $this->redirect()->toRoute('admin');}
  //\Zend\Debug\Debug::dump($this->params()->fromPost());
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
