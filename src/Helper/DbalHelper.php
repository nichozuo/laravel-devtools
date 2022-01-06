<?php


namespace Nichozuo\LaravelDevtools\Helper;


use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Support\Facades\DB;

class DbalHelper
{
    private static $DB;

    /**
     * @param string $config
     * @return AbstractSchemaManager
     */
    public static function SM(string $config = ''): AbstractSchemaManager
    {
        if (null == self::$DB) {
            self::$DB = DB::connection($config)->getDoctrineSchemaManager();
        }
        return self::$DB;
    }

    /**
     * @throws Exception
     */
    public static function register()
    {
        self::SM()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
    }

    /**
     * @param string $table
     * @param string $comment
     */
    public static function comment(string $table, string $comment)
    {
        self::SM()::statement("ALTER TABLE `{$table}` comment '{$comment}'");
    }

    /**
     * @param $column
     * @return string
     */
    public static function getType($column): string
    {
        $type = $column->getType()->getName();
        switch ($type) {
            case 'float':
            case 'double':
            case 'decimal':
                return 'numeric';
            case 'bigint':
            case 'int':
            case 'integer':
            case 'tinyint':
            case 'smallint':
                return 'integer';
            case 'date':
            case 'datetime':
            case 'timestamp':
                return 'date';
            case 'boolean':
                return 'boolean';
            case 'string':
            case 'text':
            case 'varchar':
            case 'enum':
            case 'sysname':
                return 'string';
            case 'array':
                return 'array';
            case 'json':
                return 'json';
            case 'geometry':
                return 'geometry';
            default:
                return 'null';
        }
    }
}