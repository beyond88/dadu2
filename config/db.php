<?php

return [

    /*
    |--------------------------------------------------------------------------
    | DB Management binary paths
    |--------------------------------------------------------------------------
    |
    | Used by the admin DB (Export/Import) module. Leave null to auto-detect
    | the mysqldump / mysql executables on the system PATH. Set an absolute
    | path here (or via .env) if the binaries live somewhere non-standard.
    |
    */

    'mysqldump_path' => env('DB_MYSQLDUMP_PATH'),

    'mysql_path' => env('DB_MYSQL_PATH'),

];
