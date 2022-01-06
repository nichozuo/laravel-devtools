<?php


namespace Nichozuo\LaravelDevtools\Helper;


use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use Illuminate\Support\Facades\DB;

/**
 * Table Helper
 * Class TableHelper
 * @package Nichozuo\LaravelCodegen\Helper
 */
class TableHelper
{
    /**
     * Get Schema Manger instance
     * @return AbstractSchemaManager
     */
    private static function SM(): AbstractSchemaManager
    {
        return DB::connection()->getDoctrineSchemaManager();
    }


    /**
     * Set comment for table
     * @param string $tableName
     * @param string $comment
     */
    public static function SetComment(string $tableName, string $comment)
    {
        DB::statement("ALTER TABLE `{$tableName}` comment '{$comment}'");
    }

    /**
     * Get Table instance
     * @param string $tableName
     * @return Table
     * @throws Exception
     */
    public static function GetTable(string $tableName): Table
    {
        return self::SM()->listTableDetails($tableName);
    }

    /**
     * Get Table columns, skip some fields like: id,created_at...
     * @param Table $table
     * @return Column[]
     */
    public static function GetTableColumns(Table $table): array
    {
        $columns = $table->getColumns();
        $skipColumns = ['id', 'created_at', 'updated_at', 'deleted_at'];
        foreach ($skipColumns as $column) {
            unset($columns[$column]);
        }
        return $columns;
    }

    /**
     * @param array $columns
     * @return bool
     */
    public static function HasSoftDelete(array $columns): bool
    {
        return in_array('deleted_at', array_keys($columns));
    }

    /**
     * @return Table[]
     * @throws Exception
     */
    public static function ListTables(): array
    {
        return self::SM()->listTables();
    }
}