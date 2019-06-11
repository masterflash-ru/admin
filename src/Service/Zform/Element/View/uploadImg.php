<?php
/**
* вьевер для генерации элемента вывода фото и загрузки нового
*/

namespace Admin\Service\Zform\Element\View;

use Zend\Form\View\Helper\FormInput;
use Zend\Form\ElementInterface;

class uploadImg extends FormInput
{
    //помощник вывода эл-та file
    protected $fileHelper;
    
    
    public function render(ElementInterface $element)
    {
        $view=$this->getView();
        $src=$view->basePath($element->getValue());

        $attributes = $element->getAttributes();
        unset ($attributes["name"]);
        
        $file=$this->getFileElementHelper();

        return "<div class='uploadimg-container'>".
            '<div class="uploadimg-file">'.$file->render($element).'</div>'.
            sprintf(
            '<img %s src="%s" %s',
            $this->createAttributesString($attributes),
            $src,
            $this->getInlineClosingBracket()
        )."</div>";

    }

    /**
     * получить FormFile помощник и закешировать на всякий случай
     *
     * @return FormFile
     */
    protected function getFileElementHelper()
    {
        if ($this->fileHelper) {
            return $this->fileHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->fileHelper = $this->view->plugin('formfile');
        }

        return $this->fileHelper;
    }

}
