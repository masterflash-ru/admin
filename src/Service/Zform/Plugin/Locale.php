<?php
namespace Admin\Service\Zform\Plugin;

use Laminas\Form\FormInterface;

class Locale extends AbstractPlugin
{
    protected $config;

public function __construct($config) 
{
    $this->config=$config;
}
    


 public function rowModel(array $rowModel,FormInterface $form)
 {
        $rez=[];
        foreach ($this->config as $l){
            $rez[$l]=$l;
        }
        $colModel["editoptions"]["value"]=$rez;
     $form->get($rowModel["name"])->setValueOptions($rez);
 }


}