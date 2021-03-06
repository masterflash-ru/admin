<?php
/**
* ввод-вывод для Zform
*/

namespace Admin\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Exception;
use Admin\Service\Zform\Exception as ZformException;
use Laminas\Form\Factory as FormFactory;

class ZformController extends AbstractActionController
{
    protected $connection;
    protected $cache;
    protected $config;
    protected $zform;
    //менеджер эл-тов формы, он создается в фабрике, и берет из конфига наши элементы
    protected $formManager;


public function __construct ($connection,$cache,$config,$zform,$formManager)
{
    $this->connection=$connection;
    $this->cache=$cache;
    $this->config=$config;
    $this->zform=$zform;
    $this->formManager=$formManager;
}


/**
* чтение данных для zform
*/
public function readAction()
{
    try {
        
        $interface=$this->params('interface',"");
        $acl=$this->acl('interface/'.$interface);
        if (!$acl->isAllowed("r")){
            throw new  ZformException\AccessDeniedException("Ошибка чтения. Доступ запрещен к interface/".$interface);
        }

        $options=include $this->config[$interface];

        //обработаем динамические поля, если имеются
        $options["options"]["layout"]["rowModel"]=$this->zform->handlingDynamicFields($options["options"]["layout"]["rowModel"]);
        $this->zform->setOptions($options["options"]);
        //создаем штатными средствами форму
        $factory=new FormFactory($this->formManager);
        $form    = $factory->createForm($options["options"]["layout"]["rowModel"]);
        $this->zform->load($form,$this->params()->fromQuery());
        $view=new ViewModel([
            "form"=>$form,
            "interface"=>$interface,
            "options"=>$options
            ]);
        $view->setTemplate("admin/zform/form-factory");
        $view->setTerminal(true);

        return $view;
    } catch (ZformException\AccessDeniedException $e) {
        $this->getResponse()->setStatusCode(406);
        return $this->getResponse()->setContent('<h2 style="color:red">'.$e->getMessage().'<h2>');
    } catch (Exception $e) {
        $errors="Ошибка: ".$e->getMessage()."\nФайл:".$e->getFile()."\nСтрока:".$e->getLine()."\nТрассировка:".$e->getTraceAsString();
        //любое исключение - 404
        $this->getResponse()->setStatusCode(404);
        return $this->getResponse()->setContent(nl2br($errors));
    }
}

/**
* запись строки
*/
public function editAction()
{
    try {
        $interface=$this->params('interface',"");
        $acl=$this->acl('interface/'.$interface);
        if (!$acl->isAllowed("w")){
            throw new  ZformException\AccessDeniedException("Ошибка записи. Доступ запрещен к interface/".$interface);
        }

        $options=include $this->config[$interface];
        
        //обработаем динамические поля, если имеются
        $options["options"]["layout"]["rowModel"]=$this->zform->handlingDynamicFields($options["options"]["layout"]["rowModel"]);
        $this->zform->setOptions($options["options"]);
        /*
        * формируем форму и пропускаем все через тамошние валидаторы и фильтры
        **/
        $factory=new FormFactory($this->formManager);
        $form    = $factory->createForm($options["options"]["layout"]["rowModel"]);
        $this->zform->initForm($form);
        $form->setData(array_merge_recursive($this->params()->fromPost(),$this->params()->fromFiles()));
        if ($form->isValid()) {
            //валидация прошла, обарбатываем запись
            $this->zform->edit($form,$form->getData(),$this->params()->fromQuery());
            $this->zform->load($form,$this->params()->fromQuery());
        } else {
            //ошибка валидатора, отдает 418 код
            $this->getResponse()->setStatusCode(418);
        }
        $view=new ViewModel([
            "form"=>$form,
            "interface"=>$interface,
            "options"=>$options
            ]);
        $view->setTemplate("admin/zform/form-factory");
        $view->setTerminal(true);
        return $view;
    } catch (ZformException\AccessDeniedException $e) {
        //доступ запрещен
        $this->getResponse()->setStatusCode(406);
        return $this->getResponse()->setContent($e->getMessage());
    } catch (Exception $e) {
        $errors="Ошибка: ".$e->getMessage()."\nФайл:".$e->getFile()."\nСтрока:".$e->getLine()."\nТрассировка:".$e->getTraceAsString();
        //любое исключение - 404
        $this->getResponse()->setStatusCode(404);
        return $this->getResponse()->setContent(nl2br($errors));
    }
}
    

/**
* работа с отдельными плагинами
* возвращает json
* /
public function pluginAction()
{
    try {
        $plugin_name=$this->params('name',"");
        $acl=$this->acl('jqgrid/plugin/'.$plugin_name);
        if (!$acl->isAllowed("r")){
            throw new  ZformException\AccessDeniedException("Ошибка. Доступ к плагину jqgrid/plugin/{$plugin_name} запрещен");
        }

        $plugin=$this->zform->plugin($plugin_name,null);
        $rez=$plugin->ajaxRead();
        $view=new JsonModel($rez);
        return $view;
    } catch (ZformException\AccessDeniedException $e) {
        $this->getResponse()->setStatusCode(406);
        return $this->getResponse()->setContent('<h2 style="color:red">'.$e->getMessage().'<h2>');
    } catch (Exception $e) {
        $errors="Ошибка: ".$e->getMessage()."\nФайл:".$e->getFile()."\nСтрока:".$e->getLine()."\nТрассировка:".$e->getTraceAsString();
        //любое исключение - 404
        $this->getResponse()->setStatusCode(404);
        return $this->getResponse()->setContent(nl2br($errors));
    }

}*/
}
