Старая админка с говнокодом

На данный момент это единственная админка для Simba

для установки используйте composer require masterflash-ru/admin

загрузите дамп базы из папки data ПОСЛЕ загрузки аналогичного дампа из пакета masterflash-ru/permissions и masterflash-ru/users
Если вы обновляете раннее созданные сайты (до 2019года), загрузите дамп update_before_created_2019.sql - он загружает доступы для админки, без них зайти и работать в админпанели невозможно
Обновляя пакет, не забывайте копировать в публичную часть сайта файлы ява-скриптов! 
После обновления обязательно чистите кеш - просто удалите все из папки cache приложения


10.01.19 - перешли на систему доступов, используется пакет masterflash-ru/permissions, пока уровень контроллеров
4.12.18 - изменены в фабриках параметры соединения с базой, после обновления ADO пакета
30.05.18 - интерфейс вывода деревьев переделан на SPL рекурсивный интератор, это значительно увеличило скорость обработки
