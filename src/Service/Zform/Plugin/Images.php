<?php
namespace Admin\Service\Zform\Plugin;

use Laminas\Filter;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\FileInput;
use Laminas\Validator;
use Exception;
use Laminas\Form\FormInterface;
use Laminas\Filter\File\RenameUpload;


class Images extends AbstractPlugin
{
    protected $ImagesLib;
    protected $connection;
    protected $def_options =[
        "image_id"=>"id",                        //имя поля с ID
        "storage_item_name" => "",              //имя секции в хранилище
        "storage_item_rule_name"=>"admin_img",  //имя правила из хранилища
        "database_table_name" =>"",             //имя таблицы в которую добавляем записи, если напрямую в базу
        "storage_items_array"=>[]               //массив хранилищ, для опраций в рамках всего интерфейса
    ];


    
public function __construct($ImagesLib,$connection) 
{
    $this->ImagesLib=$ImagesLib;
    $this->connection=$connection;
}
    

/**
* операция чтения
* возвращает строку пути к файлу+файл пригодную для тега IMG
*/
public function read($value,FormInterface $form)
{
    if ($this->ImagesLib){
        return $this->ImagesLib->loadImage($this->options["storage_item_name"],$value,$this->options["storage_item_rule_name"]);
    } else {
        //нет masterflash-ru/storage - рисуем на изображении ошибку
        $im = @imagecreate(410, 30) or die("Package masterflash-ru/storage not installed");
        $background_color = imagecolorallocate($im, 255, 255, 255);
        $text_color = imagecolorallocate($im, 255, 0, 0);
        imagestring($im, 5, 5, 5,  "Package masterflash-ru/storage not installed", $text_color);
        ob_start();
        imagepng($im);
        $image_data = ob_get_contents();
        ob_end_clean();
        return "data:image/png;base64,".base64_encode($image_data);
    }
}

/**
* добвление новой записи, ID еще нет, выисляется следующий и под ним записывается в хранилище
*/
public function add($value,&$postParameters)
{
    $rs=$this->connection->Execute("SELECT AUTO_INCREMENT  FROM information_schema.tables
                                                WHERE
                                                  table_name = '".$this->options["database_table_name"]."'
                                                  AND table_schema = DATABASE()");
    $postParameters["id"]=$rs->Fields->Item['AUTO_INCREMENT']->Value;
    return $this->edit($value,$postParameters);
}


/**
* обработка фото по правилам прописанных в парвилах хранилища
* возвращает типа, если успешная обработка:
если загрузки не было, возвращается пустой массив
* если была ошибка - исключение
*/
public function edit($value,&$postParameters,$getParameters,FormInterface $form)
{
    if (empty($this->options["storage_item_name"])){
        throw new Exception("Не указано имя секции конфига с хранилищем, куда записывать файлы");
    }
    
    //имя поля с элементом file
    $input_name=$this->options["rowModel"]["spec"]["name"];
    //цепочка фильтров
    $FilterChain=$form->getInputFilter()->get($input_name)->getFilterChain();
    //временная папка
    $data_folder='./data/datastorage';
    //ищем элемент фильтра RenameUpload::class и насильно туда ставим нашу временную папку
    $f=false;
    foreach ($FilterChain->getFilters() as $filter_name=>$f){
        if ($filter_name==RenameUpload::class){
            $f->setTarget('./data/datastorage');
            $f=true;
            break;
        }
    }
    if (!$f){
        throw new Exception("Не обнаружен обязательный фильтр ".RenameUpload::class." для обработки загрузки, смотрите секцию 'input_filter' вашего конфига формы");
    }

    //временная папка для загруженных файлов
    if (!is_readable($data_folder) || !is_dir($data_folder) || !is_writable($data_folder)) {
        if (!mkdir($data_folder)) {
            throw new Exception("Ошибка создания папки ".$data_folder." или в нее нельзя записать");
        };
    }
    
    if (!empty($value)){
        //быда загрузка файла, заносим в хранилище
        $this->ImagesLib->selectStorageItem($this->options["storage_item_name"]);
        return $this->ImagesLib->saveImages($value["name"],$this->options["storage_item_name"],$postParameters[$this->options["image_id"]]);
    }

    return [];
    
    
}

/**
* удаление записи в рамках всего интерфейса
*/
public function idel(array $postParameters)
{
    $id=(int)$postParameters["id"];
    if (!is_array($this->options["storage_items_array"])){
        throw new Exception("Имена хранилищ для удаления должны быть в виде массива");
    }
    foreach ($this->options["storage_items_array"] as $name){
        $this->ImagesLib->deleteFile($name,$id);
    }
}

    
/**
*удаление записи
* $postParameters - то что пришло от сетки, обычно 
* id - ID записи
* oper - равно "del"
* /
public function del(array $postParameters)
{
    $id=(int)$postParameters["id"];
    $this->ImagesLib->deleteFile($this->options["storage_item_name"],$id);
}
*/
    
}