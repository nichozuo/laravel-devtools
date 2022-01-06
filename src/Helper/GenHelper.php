<?php


namespace Nichozuo\LaravelDevtools\Helper;


use Doctrine\DBAL\Schema\Table;
use Exception;
use ReflectionException;

class GenHelper
{
    /**
     * @param Table $table
     * @return string
     */
    public static function GenTableString(Table $table): string
    {
        return "protected \$table = '{$table->getName()}';";
    }

    /**
     * @param Table $table
     * @return string
     */
    public static function GenCommentString(Table $table): string
    {
        return "protected \$comment = '{$table->getComment()}';";
    }

    /**
     * @param array $columns
     * @return string
     */
    public static function GenFillableString(array $columns): string
    {
        $t1 = '';
        $columns = array_keys($columns);
        $fillable = implode("', '", $columns);
        $t1 .= "protected \$fillable = ['$fillable'];" . PHP_EOL;
        return $t1;
    }

    /**
     * @param array $columns
     * @param string $tab
     * @return string
     */
    public static function GenRequestValidateString(array $columns, string $tab = ''): string
    {
        $t1 = '';
        foreach ($columns as $item) {
            $name = $item->getName();
            $required = ColumnHelper::GetRequired($item);
            $type = ColumnHelper::GetType($item);
            $comment = $item->getComment();
            $t1 .= $tab . "'$name' => '$required|$type', // $comment";
            if ($item != end($columns))
                $t1 .= PHP_EOL;
        }
        return $t1;
    }

    /**
     * @param array $columns
     * @return string
     */
    public static function GenInsertString(array $columns): string
    {
        $t1 = '';
        foreach ($columns as $item) {
            $name = $item->getName();
            $comment = $item->getComment();
            $t1 .= "'$name' => '', // $comment" . PHP_EOL;
        }
        return $t1;
    }

    /**
     * @param $route
     * @param $filePath
     * @param $className
     * @param $methodName
     * @return mixed|string|string[]
     * @throws Exception
     */
    public static function GenApiMD($route, $filePath, $className, $methodName)
    {
        $data = ReflectHelper::GetMethodAnnotation($className, $methodName);
        $data['title'] = $data['title'] ?? $methodName;
        $data['intro'] = isset($data['intro']) ? ' > ' . $data['intro'] : '';
        $data['url'] = $route->uri;
        $data['method'] = $route->methods[0];
        $data['params'] = self::getParams($filePath, $className, $methodName);
        $data['response'] = isset($data['response']) ?
            json_encode($data['response'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) :
            json_encode([
                'code' => 0,
                'message' => 'ok'
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $data['responseParams'] = self::getResponseParams($data);
        $stubContent = StubHelper::GetStub('api.md');
        return StubHelper::Replace([
            '{{title}}' => $data['title'],
            '{{intro}}' => $data['intro'],
            '{{url}}' => $data['url'],
            '{{method}}' => $data['method'],
            '{{params}}' => $data['params'],
            '{{response}}' => $data['response'],
            '{{responseParams}}' => $data['responseParams'],
        ], $stubContent);
    }

    /**
     * @param $filePath
     * @param $className
     * @param $methodName
     * @return string
     * @throws ReflectionException
     */
    private static function getParams($filePath, $className, $methodName): string
    {
        $code = ReflectHelper::GetMethodCode($filePath, $className, $methodName);
        $start = $end = false;
        $arr = [];
        foreach ($code as $line) {
            $t = trim($line);
            if ($t == ']);') $end = true;
            if ($start && !$end)
                $arr[] = $t;
            if ($t == '$params = $request->validate([') $start = true;
        }
        $arr1 = [];
        foreach ($arr as $item) {
            $t1 = explode('\'', $item);
            $t2 = explode('|', $t1[3]);
            $t3 = explode('//', $t1[4]);
            $t4 = [
                $t1[1],
                $t2[0] == 'nullable' ? '-' : 'Y',
                $t2[1],
                trim($t3[1])
            ];
            $arr1[] = implode('|', $t4);
        }
        return implode(PHP_EOL, $arr1);
    }

    /**
     * @param $data
     * @return string
     */
    private static function getResponseParams($data): string
    {
        $t1 = '';

        if (!isset($data['responseParams']))
            return $t1;

        if (!is_array($data['responseParams'])) {
            $item = $data['responseParams'];
            $item = str_replace('nullable|', '- |', $item);
            $item = str_replace('required|', '是 |', $item);
            $t1 .= '|' . str_replace(',', '|', $item) . '|' . PHP_EOL;
            return $t1;
        }

        foreach ($data['responseParams'] as $item) {
            $item = str_replace('nullable|', '- |', $item);
            $item = str_replace('required|', '是 |', $item);
            $t1 .= '|' . str_replace(',', '|', $item) . '|' . PHP_EOL;
        }

        return $t1;
    }

    /**
     * @param $table
     * @return mixed|string|string[]
     */
    public static function GenDatabaseMD($table)
    {
        $data['tableName'] = $table->getName();
        $data['tableComment'] = $table->getComment() ? '> ' . $table->getComment() : '';
        $data['columns'] = '';

        $columns = $table->getColumns();
        foreach ($columns as $column) {
            $data['columns'] .= '|' . implode('|', [
                    $column->getName(),
                    $column->getType()->getName(),
                    $column->getPrecision(),
                    $column->getScale(),
                    $column->getNotNull() ? '是' : ' ',
                    $column->getDefault() ? $column->getDefault() : ' ',
                    $column->getComment() ? $column->getComment() : ' ',
                ]) . '|' . PHP_EOL;
        }
        $stubContent = StubHelper::GetStub('db.md');
        return StubHelper::Replace([
            '{{tableName}}' => $data['tableName'],
            '{{tableComment}}' => $data['tableComment'],
            '{{columns}}' => $data['columns'],
        ], $stubContent);
    }
}