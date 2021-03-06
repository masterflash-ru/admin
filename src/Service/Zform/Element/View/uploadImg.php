<?php
/**
* вьевер для генерации элемента вывода фото и загрузки нового
*/

namespace Admin\Service\Zform\Element\View;

use Laminas\Form\View\Helper\FormInput;
use Laminas\Form\ElementInterface;

class uploadImg extends FormInput
{
    //помощник вывода эл-та file
    protected $fileHelper;
    
    
    public function render(ElementInterface $element)
    {
        $view=$this->getView();
        $file=$this->getFileElementHelper();
        if (is_array($element->getValue())){
            //временно пока так, если ошибка не выводим картинку
            return  "<div class='uploadimg-container'>".
            '<div class="uploadimg-file">'.$file->render($element).'</div>'.
           "</div>";
        }
        
        $src=$element->getValue();
        $file_upload_element="";
        //если нет masterflash-ru/storage тогда будет не имя файла, а строка в base64
        if (stripos($src,"data")===false){
            //пакет есть, поэтому все штатно
            $src=$view->basePath($src);
            $file_upload_element='<div class="uploadimg-file">'.$file->render($element).'</div>';
        }
        if ($src=="/"){
            //если пустое значение, тогда рисуем на картинку 1х1
            $im = @imagecreate(1, 1) or die("Image empty");
            imagecolorallocate($im, 255, 255, 255);
            ob_start();
            imagepng($im);
            $image_data = ob_get_contents();
            ob_end_clean();
            $src="data:image/png;base64,".base64_encode($image_data);
        }
        

        $attributes = $element->getAttributes();
        unset ($attributes["name"]);
        
        

        return "<div class='uploadimg-container'>".
            $file_upload_element.
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
