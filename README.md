Старая админка с говнокодом

На данный момент это единственная админка для Simba

для установки используйте composer require masterflash-ru/admin

загрузите дамп базы из папки data ПОСЛЕ загрузки аналогичного дампа из пакета masterflash-ru/permissions

10.01.19 - перешли на систему доступов, используется пакет masterflash-ru/permissions, пока уровень контроллеров
4.12.18 - изменены в фабриках параметры соединения с базой, после обновления ADO пакета
30.05.18 - интерфейс вывода деревьев переделан на SPL рекурсивный интератор, это значительно увеличило скорость обработки
