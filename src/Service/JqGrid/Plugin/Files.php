<?php
namespace Admin\Service\JqGrid\Plugin;

use Laminas\Filter;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\FileInput;
use Laminas\Validator;
use Exception;


class Files extends AbstractPlugin
{
    protected $FilesLib;
    protected $connection;
    protected $def_options =[
        "file_id"=>"id",                        //имя поля с ID
        "storage_item_name" => "",              //имя секции в хранилище
        "database_table_name" =>"",             //имя таблицы в которую добавляем записи, если напрямую в базу
        "storage_item_rule_name"=>""            ////имя правила из хранилища данного элемента хранилища
    ];


    
public function __construct($FilesLib,$connection) 
{
    $this->FilesLib=$FilesLib;
    $this->connection=$connection;
}
    

/**
* операция чтения
* возвращает строку пути к файлу+файл пригодную для тега IMG
*/
public function read($value,$index,$row)
{
    return $this->FilesLib->loadFile($this->options["storage_item_name"],$row[$this->options["file_id"]],$this->options["storage_item_rule_name"]);
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
* обработка файла по правилам прописанных в парвилах хранилища
* возвращает типа, если успешная обработка:
если загрузки не было, возвращается пустой массив
* если была ошибка - исключение
*/
public function edit($value,&$postParameters)
{
    if (empty($this->options["storage_item_name"])){
        throw new Exception("Не указано имя секции конфига с хранилищем, куда записывать файлы");
    }
    $input_name=$this->options["colModel"]["name"];
    $data_folder='./data/datastorage';
    $file = new FileInput('file_'.$input_name);
    $file->setRequired(false);
    $file ->getValidatorChain()->attach(new Validator\File\UploadFile());
    $file ->getFilterChain() ->attach(new Filter\File\RenameUpload([
        'target'    => $data_folder,
        'use_upload_name' => true,
        'overwrite' => true
        ]));
    
    if (!is_readable($data_folder) || !is_dir($data_folder) || !is_writable($data_folder)) {
        if (!mkdir($data_folder)) {
            throw new Exception("Ошибка создания папки ".$data_folder." или в нее нельзя записать");
        };
    }


    $inputFilter = new InputFilter();
    $inputFilter ->add($file) ->setData($_FILES);

    if ($inputFilter->isValid()) {
        //успешная загрузка
        //Array ( [name] => _0006s_0020_роял ред делишес.jpg [type] => image/jpeg [tmp_name] => ./data/images/_0006s_0020_роял ред делишес.jpg [error] => 0 [size] => 348221 )
        $data = $inputFilter->getValue('file_'.$input_name);
        if (!empty($data)){
            //быда загрузка файла, заносим в хранилище
            $this->FilesLib->selectStorageItem($this->options["storage_item_name"]);
            return $this->FilesLib->saveFiles($data["name"],$this->options["storage_item_name"],$postParameters[$this->options["file_id"]]);
        }
    } else {
        $mess="";
        foreach ($inputFilter->getMessages() as $m){
            $mess.=implode("<br/>\n",$m)."<br/>\n";
        }
        throw new Exception("Ошибка загрузки/обработки файла: <b>{$mess}</b> ");
    }    
    return [];
}

/**
*удаление записи
* $postParameters - то что пришло от сетки, обычно 
* id - ID записи
* oper - равно "del"
*/
public function del(array $postParameters)
{
    $id=(int)$postParameters["id"];
    $this->FilesLib->deleteFile($this->options["storage_item_name"],$id);
}

    
}