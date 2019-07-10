<?php
/**
вход в админку, вывод формы авторизации 
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;

use Admin\Form\LoginForm;

use Zend\Authentication\Result;




class LoginController extends AbstractActionController
{
    protected $authManager;
    protected $accessdenied;


public function __construct ($authManager)
{
   $this->authManager=$authManager; /*экземпляр Mf\Users\Service\AuthManager*/
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

    $view=new ViewModel(["error"=>$this->accessdenied]);
    $view->setTemplate("admin/login/login");

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
        $view->setVariables(["error"=>"Ошибка авторизации"]);
    }

    $view->setVariables(["form"=>$form]);
    return $view;
}


public function e403Action()
{
    $this->getResponse()->setStatusCode(403);
    return new ViewModel();
}

public function accessdeniedAction()
{
    $this->accessdenied="Доступ запрещен";
    return $this->loginAction();
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
