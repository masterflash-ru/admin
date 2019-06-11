<?php
/**
* помощник вывода картинки и элемента выбора файла
 */

namespace Admin\Service\Zform\Element\Form;


use Zend\Form\Element\File;

class uploadImg extends File
{
    protected $attributes = [
        'type' => 'uploadimg',
    ];

}
