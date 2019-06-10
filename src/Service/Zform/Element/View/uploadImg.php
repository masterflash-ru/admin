<?php
/**
* вьевер для генерации элемента вывода фото и загрузки нового
*/

namespace Admin\Service\Zform\Element\View;

use Zend\Form\View\Helper\FormInput;
use Zend\Form\ElementInterface;

class uploadImg extends FormInput
{
    public function render(ElementInterface $element)
    {
       


        return $element->getValue();
    }
}
