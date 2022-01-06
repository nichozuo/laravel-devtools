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

class DocsHelper
{
    /**
     * @return array
     */
    public static function getReadmeMenu(): array
    {
        $path = resource_path('laravel-devtools/readme');
        if (!File::isDirectory($path))
            $path = __DIR__ . '/../resources/laravel-devtools/readme';
        return self::getReadmeChildrenDirs($path);
    }

    /**
     * @param string $path
     * @param string $subDir
     * @return array
     */
    private static function getReadmeChildrenDirs(string $path, string $subDir = ''): array
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
            $arr[] = [
                'key' => $key,
                'title' => $title,
                'isLeaf' => true
            ];
        }
        return $arr;
    }

    /**
     * @intro 获取
     * @param string $path
     * @param string $subDir
     * @return array
     * @throws ReflectionException
     * @throws Exception
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

    /**
     * @param ReflectionClass $ref
     * @return array
     * @throws Exception
     */
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
     * @return array
     * @throws Exception
     */
    public static function getDatabaseMenu(): array
    {
        $tables = TableHelper::listTables();
        $return = null;
        foreach ($tables as $table) {
            $return[] = [
                'key' => $table->getName(),
                'title' => $table->getName(),
                'subTitle' => $table->getComment(),
                'isLeaf' => true
            ];
        }
        return $return;
    }

    /**
     * @param $key
     * @return array
     */
    public static function getReadmeContent($key): array
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
        $fullName = '\\App\\Modules\\' . $key;
        $t1 = explode('@', $fullName);
        $className = $t1[0];
        $methodName = $t1[1];
        $filePath = app_path('Modules') . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, explode('@', $key)[0]) . '.php';
        $content = '# 暂时没有内容';
        foreach (Route::getRoutes() as $route) {
            if (!Str::startsWith($route->uri, 'api/'))
                continue;
            if (!Str::startsWith($route->getAction()['controller'], '\\App\\'))
                continue;
            if ($route->getAction()['controller'] != $fullName)
                continue;
            $content = GenHelper::genApiMD($route, $filePath, $className, $methodName);
            break;
        }
        return [
            'content' => $content
        ];
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
}