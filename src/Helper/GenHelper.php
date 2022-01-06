<?php


namespace Nichozuo\LaravelDevtools\Helper;


use DocBlockReader\Reader;
use Doctrine\DBAL\Schema\Table;
use Exception;
use ReflectionMethod;

class GenHelper
{
    /**
     * @param Table $table
     * @return string
     */
    public static function genTableString(Table $table): string
    {
        return "protected \$table = '{$table->getName()}';";
    }

    /**
     * @param Table $table
     * @return string
     */
    public static function genCommentString(Table $table): string
    {
        return "protected \$comment = '{$table->getComment()}';";
    }

    /**
     * @param array $columns
     * @return string
     */
    public static function genFillableString(array $columns): string
    {
        $t1 = '';
        $columns = array_keys($columns);
        $fillable = implode("', '", $columns);
        $t1 .= "protected \$fillable = ['{$fillable}'];" . PHP_EOL;
        return $t1;
    }

    /**
     * @param array $columns
     * @param string $tab
     * @return string
     */
    public static function genRequestValidateString(array $columns, string $tab = ''): string
    {
        $t1 = '';
        foreach ($columns as $item) {
            $name = ColumnHelper::getName($item);
            $required = ColumnHelper::getRequired($item);
            $type = ColumnHelper::getType($item);
            $comment = ColumnHelper::getComment($item);
            $t1 .= $tab . "'{$name}' => '{$required}|{$type}', // {$comment}";
            if ($item != end($columns))
                $t1 .= PHP_EOL;
        }
        return $t1;
    }

    /**
     * @param array $columns
     * @return string
     */
    public static function genInsertString(array $columns): string
    {
        $t1 = '';
        foreach ($columns as $item) {
            $name = ColumnHelper::getName($item);
            $comment = ColumnHelper::getComment($item);
            $t1 .= "'{$name}' => '', // {$comment}" . PHP_EOL;
        }
        return $t1;
    }

    /**
     * @param array $columns
     * @return string
     */
    public static function genAnnotationString(array $columns): string
    {
        $t1 = '';
        foreach ($columns as $item) {
            $name = ColumnHelper::getName($item);
            $required = ColumnHelper::getRequired($item);
            $type = ColumnHelper::getType($item);
            $comment = ColumnHelper::getComment($item);
            $t1 .= "* @params {$name},{$required}|{$type},{$comment}" . PHP_EOL;
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
    public static function genApiMD($route, $filePath, $className, $methodName)
    {
        $reader = new Reader($className, $methodName);
        $data = $reader->getParameters();
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
        $data['responseParams'] = self::getResponseParams($data, false);
        $stubContent = StubHelper::getStub('api.md');
        return StubHelper::replace([
            '{{title}}' => $data['title'],
            '{{intro}}' => $data['intro'],
            '{{url}}' => $data['url'],
            '{{method}}' => $data['method'],
            '{{params}}' => $data['params'],
            '{{response}}' => $data['response'],
            '{{responseParams}}' => $data['responseParams'],
        ], $stubContent);
    }

    private static function getParams($filePath, $className, $methodName)
    {
        $ref = new ReflectionMethod($className, $methodName);
        $startLine = $ref->getStartLine();
        $endLine = $ref->getEndLine();
        $length = $endLine - $startLine;
        $source = file($filePath);
        $code = array_slice($source, $startLine, $length);
//        dd($code, $startLine, $length);
        $start = $end = false;
        $arr = [];
        foreach ($code as $line) {
            $t = trim($line);
            if ($t == ']);') $end = true;
            if ($start && !$end)
                $arr[] = $t;
            if ($t == '$params = $request->validate([') $start = true;
        }
//        dd($arr);
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
//        dd($arr1);
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
     * @param $route
     * @return array
     */
    public static function getInfoFromRoute($route): array
    {
        $t1 = explode('@', $route->action['controller']);
        $controllerClass = $t1[0];
        $actionName = $t1[1];
        return array($controllerClass, $actionName);
    }

    /**
     * @param $table
     * @return mixed|string|string[]
     */
    public static function genDatabaseMD($table)
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
        $stubContent = StubHelper::getStub('db.md');
        $stubContent = StubHelper::replace([
            '{{tableName}}' => $data['tableName'],
            '{{tableComment}}' => $data['tableComment'],
            '{{columns}}' => $data['columns'],
        ], $stubContent);
        return $stubContent;
    }

    /**
     * @param $controller
     * @param $action
     * @return void
     * @throws Exception
     */
    public static function genModulesMD($controller, $action)
    {
        $className = 'App\\Modules\\' . $controller;
        $path = app_path('Modules') . DIRECTORY_SEPARATOR . str_replace('\\', '/', $controller) . '.php';

        // 读取注解
        $reader = new Reader($className, $action);
        $data = $reader->getParameters();
        $intro = $data['intro'];

        // 读取代码
        $ref = new ReflectionMethod($className, $action);
        $source = file($path);
        $startLine = $ref->getStartLine();
        $endLine = $ref->getEndLine();
        $length = $endLine - $startLine;
        $code = array_slice($source, $startLine, $length);

        $start = $end = false;
        $params = [];
        foreach ($code as $line) {
            $t = trim($line);
            if ($t == ']);') $end = true;

            if ($start && !$end)
                $params[] = $t;

            if ($t == '$params = $request->validate([') $start = true;
        }
//        $params = array:1 [
//            0 => "'name' => 'nullable|string', // 名称"
//        ]
        dd($params);
    }
}