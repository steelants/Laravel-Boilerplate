<?php

return [
    'database' =>  (bool) env('BACKUP_DATABASE', true),
    'storage' =>  (bool) env('BACKUP_STORAGE', true),
    'enviroment' =>  (bool) env('BACKUP_ENV', true),
];
