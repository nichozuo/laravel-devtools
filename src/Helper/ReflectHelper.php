<?php

namespace Nichozuo\LaravelDevtools\Helper;

use DocBlockReader\Reader;
use Exception;
use Illuminate\Support\Arr;
use ReflectionException;
use ReflectionMethod;

class ReflectHelper
{
    /**
     * @param string $filePath
     * @param string $className
     * @param string $methodName
     * @return array|false
     * @throws ReflectionException
     */
    public static function GetMethodCode(string $filePath, string $className, string $methodName)
    {
        $ref = new ReflectionMethod($className, $methodName);
        $startLine = $ref->getStartLine();
        $endLine = $ref->getEndLine();
        $length = $endLine - $startLine;
        $source = file($filePath);
        return array_slice($source, $startLine, $length);
    }

    /**
     * @param string $className
     * @param string $methodName
     * @return array
     * @throws Exception
     */
    public static function GetMethodAnnotation(string $className, string $methodName): array
    {
        $reader = new Reader($className, $methodName);
        $data = $reader->getParameters();
        return Arr::only($data, ['intro', 'responseParams']);

    }

    /**
     * @param string $className
     * @return mixed|string
     * @throws Exception
     */
    public static function GetControllerAnnotation(string $className)
    {
        $reader = new Reader($className);
        $data = $reader->getParameters();
        return $data['intro'] ?? '';
    }
}