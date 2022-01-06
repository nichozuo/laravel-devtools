<?php


namespace Nichozuo\LaravelDevtools\Helper;


use Doctrine\DBAL\Schema\Column;

class ColumnHelper
{
    /**
     * @param Column $column
     * @return string
     */
    public static function GetRequired(Column $column): string
    {
        return ($column->getNotNull()) ? 'required' : 'nullable';
    }

    /**
     * @param Column $column
     * @return mixed|null
     */
    public static function GetType(Column $column)
    {
        $type = $column->getType()->getName();
        $columnTypes = config('zuo.dbTypeToPHPType');
        return isset($columnTypes[$type]) ? $columnTypes[$type] : null;
    }
}