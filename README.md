## Админка Simba

На данный момент это единственная админка для Simba

для установки используйте composer require masterflash-ru/admin

загрузите дамп базы из папки data
Обновляя пакет, не забывайте копировать в публичную часть сайта файлы ява-скриптов! 
После обновления обязательно чистите кеш - просто удалите все из папки cache приложения!


В конфиге приложения должны быть настройки кэша с именем DefaultSystemCache:
```php

    'caches' => [
        'DefaultSystemCache' => [
            'adapter' => [
                'name'    => Filesystem::class,
                'options' => [
                    'cache_dir' => './data/cache',
                    'ttl' => 60*60*2 
                ],
            ],
            'plugins' => [
                [
                    'name' => Serializer::class,
                    'options' => [
                    ],
                ],
            ],
        ],
    ],
```
Для работы с базой в конфиге приложения должно быть объявлено DefaultSystemDb:
```php
......
    "databases"=>[
        //соединение с базой + имя драйвера
        'DefaultSystemDb' => [
            'driver'=>'MysqlPdo',
            //"unix_socket"=>"/tmp/mysql.sock",
            "host"=>"localhost",
            'login'=>"root",
            "password"=>"**********",
            "database"=>"simba4",
            "locale"=>"ru_RU",
            "character"=>"utf8"
        ],
    ],
.....
```


Админка использует 2 варианта интерфейса ввода-вывода: сетка JqGrid (линейные и древовидные) и стандартные формы Laminas
Для совместимости оставлено все старое, которое перекочевало из прежних версий. Старая часть не будет обновляться, только будут исправления.

Подробную информацию можно найти в папке doc