<?php


namespace Nichozuo\LaravelDevtools\Helper;


use Doctrine\DBAL\Schema\Column;

class ColumnHelper
{
    /**
     * @param Column $column
     * @return string
     */
    public static function getName(Column $column): string
    {
        return $column->getName();
    }

    /**
     * @param Column $column
     * @return string
     */
    public static function getRequired(Column $column): string
    {
        return ($column->getNotNull()) ? 'required' : 'nullable';
    }

    /**
     * @param Column $column
     * @return mixed|null
     */
    public static function getType(Column $column)
    {
        $type = $column->getType()->getName();
        $columnTypes = config('nichozuo.column_types');
        return isset($columnTypes[$type]) ? $columnTypes[$type] : null;
    }

    /**
     * @param Column $column
     * @return string|null
     */
    public static function getComment(Column $column): ?string
    {
        return $column->getComment();
    }
}