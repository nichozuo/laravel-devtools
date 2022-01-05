<?php


namespace Nichozuo\LaravelDevtools\Helper;


use DocBlockReader\Reader;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Illuminate\Support\Arr;

class DocsHelper
{
    /**
     * @param $type
     * @param $key
     * @return array
     */
    public static function getReadmeContent($type, $key): array
    {
        $path = resource_path('laravel-devtools/readme/' . $key);
        if (!File::exists($path))
            $path = __DIR__ . '/../resources/laravel-devtools/readme/' . $key;
        return [
            'content' => File::get($path)
        ];
    }

    /**
     * @param $key
     * @return array
     * @throws Exception
     */
    public static function getModulesContent($key): array
    {
        foreach (Route::getRoutes() as $route) {
            if (!Str::startsWith($route->uri, 'api/'))
                continue;
            if ($route->getAction()['controller'] != '\\' . $key)
                continue;

            return [
                'content' => GenHelper::genApiMD($route)
            ];
        }
        return [];
    }

    /**
     * @param $key
     * @return array
     * @throws Exception
     */
    public static function getDatabaseContent($key): array
    {
        $tables = TableHelper::listTables();
        foreach ($tables as $table) {
            if ($table->getName() != $key)
                continue;
            return [
                'content' => GenHelper::genDatabaseMD($table)
            ];
        }
        return [];
    }

    /**
     * @param string $path
     * @param string $subDir
     * @return array
     */
    public static function getReadmeChildrenDirs(string $path, string $subDir = ''): array
    {
        $arr = [];

        foreach (File::directories($path . $subDir) as $dir) {
            $key = str_replace($path, '', $dir);
            $t = explode(DIRECTORY_SEPARATOR, $dir);
            $title = end($t);
            $arr[] = [
                'key' => $key,
                'title' => $title,
                'children' => self::getReadmeChildrenDirs($path, $key)
            ];
        }

        foreach (File::files($path . $subDir) as $file) {
            $key = str_replace($path, '', $file->getPath()) . DIRECTORY_SEPARATOR . $file->getRelativePathname();
            $title = $file->getRelativePathname();
//            $name = $file->getPathname();
//            $key = str_replace($path, '', $name);
//            $title = str_replace('.md', '', $key);
//            $title = explode(DIRECTORY_SEPARATOR, $title);
//            $title = end($title);
            $arr[] = [
                'key' => $key,
                'title' => $title,
                'isLeaf' => true
            ];
        }
        return $arr;
    }

    /**
     * @param string $dir
     * @return string
     */
    public static function getModulesSubTitle(string $dir): string
    {
        $base = config('nichozuo.module_names');
        if (isset($base[$dir]))
            return $base[$dir] . '模块';
        else
            return '未知模块';
    }

    /**
     * @param string $dir
     * @return array
     * @throws ReflectionException
     */
    public static function getModulesControllers(string $dir): array
    {
        $dirs = [];
        foreach (File::allFiles(app_path('Modules' . DIRECTORY_SEPARATOR . $dir)) as $file) {
            $pathName = $file->getRelativePathname();
            $controllerName = str_replace('.php', '', $pathName);
            if (count(explode('/', $controllerName)) >= 2) continue;
            $ref = new ReflectionClass('App\\Modules\\' . str_replace('/', '\\', $dir) . '\\' . str_replace('/', '\\', $controllerName));
            $dirs[] = [
                'key' => $dir . DIRECTORY_SEPARATOR . $pathName,
                'title' => $controllerName,
                'subTitle' => self::getSubTitleOfController($ref),
                'children' => self::getModulesActions($ref)
            ];
        }
        return $dirs;
    }

    private static function getSubTitleOfController(ReflectionClass $ref)
    {
        $docs = $ref->getDocComment();
        if (!$docs)
            return '暂时没有名称';
        $t1 = trim(explode('* ', $docs)[1]);
        return str_replace(' * ', '', $t1);
    }

    private static function getModulesActions(ReflectionClass $ref): array
    {
        $files = [];
        foreach ($ref->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->class != $ref->getName() || $method->name == '__construct')
                continue;
            $action = new Reader($ref->getName(), $method->name);
            $files[] = [
                'key' => str_replace('App\\Modules\\', '', $ref->getName()) . '@' . $method->name,
                'title' => $method->name,
                'subTitle' => $action->getParameter('intro'),
                'isLeaf' => true,
            ];
        }
        return $files;
    }

    /**
     * @param $key
     * @return array
     */
    public static function getDir($key): array
    {
        $dir = File::directories($key);
        $arr = [];
        $arr = array_merge($arr, $dir);
        foreach ($dir as $value) {
            if (is_dir($value)) {
                $arr = array_merge($arr, self::getDir($value));
            }
        }
        return $arr;
    }

    /**
     * @intro 获取
     * @param string $path
     * @param string $subDir
     * @return array
     */
    public static function getModulesMenu(string $path, string $subDir = ''): array
    {
        $arr = [];
        // 遍历文件夹
        foreach (File::directories($path . $subDir) as $dir) {
            $key = str_replace($path, '', $dir);
            $t = explode(DIRECTORY_SEPARATOR, $dir);
            $title = end($t);
            $arr[] = [
                'key' => str_replace('/', '\\', $key),
                'title' => $title,
                'children' => self::getModulesMenu($path, $key)
            ];
        }

        // 遍历文件
        foreach (File::files($path . $subDir) as $file) {
            $fileName = $file->getFilename();
            $fileName = str_replace('.php', '', $fileName);
            $nameSpace = str_replace(DIRECTORY_SEPARATOR, '\\', $subDir . DIRECTORY_SEPARATOR . $fileName);
            $ref = new ReflectionClass('App\\Modules\\' . $nameSpace);
            $arr[] = [
                'key' => $nameSpace,
                'title' => $fileName,
                'children' => self::getModulesActions($ref)
            ];
        }
        return $arr;
    }
}