<?php

return [
    'name' => 'Start-mvc-doctrine Migrations',
    'migrations_namespace' => 'app\migrations',
    'table_name' => 'doctrine_migration',
    'column_name' => 'version',
    'column_length' => 14,
    'executed_at_column_name' => 'executed_at',
    'migrations_directory' => '/app/migrations',
    'all_or_nothing' => true,
];