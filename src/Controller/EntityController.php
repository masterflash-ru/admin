<?php
/**
старый модуль архивации/разархивации базы данных
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Form\EntityForm;

use  Zend\Code\Generator\PropertyGenerator;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\DocBlockGenerator;

class EntityController extends AbstractActionController
{
    protected $connection;

public function __construct ($connection)
{
    $this->connection=$connection;
}



/*вывод левой части фрейма с меню*/
public function indexAction()
{
    $view=new ViewModel();
    $code="";
    $form = new EntityForm();
    if ($this->getRequest()->isPost()) {
        $form->setData($this->params()->fromPost());
        if ($form->isValid()) {
            //данные в норме
            $info=$form->getData();
            $code="";
            if ($info["sql"]) {
                try {
                    $rs=$this->connection->Execute($info["sql"]);
                    $class = new ClassGenerator();

                    foreach ($rs->DataColumns->Item_text as $column_name=>$columninfo) {
                        $table=$columninfo->Table;
                        $class->addProperty($column_name,NULL,PropertyGenerator::FLAG_PROTECTED);
                        $method = new MethodGenerator();
                        $method->setName('set'.ucwords($column_name)) ->setBody("\$this->{$column_name}=\${$column_name};")
                            ->setParameter($column_name);
                        $class->addMethodFromGenerator($method);
                        $method = new MethodGenerator();
                        $method->setName('get'.ucwords($column_name)) ->setBody("return \$this->{$column_name};");
                        $class->addMethodFromGenerator($method);
                    }
                    $class->setName(ucwords($table));
                    $code=$class->generate();
                } catch (\Exception $e){
                    $code=$e->getMessage();
                }
            }
        }
    }

    $view->setVariables(["code"=>$code,"form"=>$form]);
    return $view;
}



}
