<?php


namespace Nichozuo\LaravelDevtools\Controller;


use Doctrine\DBAL\Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\File;
use Nichozuo\LaravelDevtools\Helper\DbalHelper;
use Nichozuo\LaravelDevtools\Helper\DocsHelper;
use Nichozuo\LaravelDevtools\Helper\TableHelper;
use Nichozuo\LaravelHelpers\Traits\ControllerTrait;
use ReflectionException;

class DocsController extends BaseController
{
    use ControllerTrait;

    private $basePath;

    /**
     * HomeController constructor.
     * @throws Exception
     */
    public function __construct()
    {
        DbalHelper::register();
    }

    /**
     * @title 获取Api文档的菜单
     * @param Request $request
     * @return array
     * @throws Exception|ReflectionException
     */
    public function getMenu(Request $request): array
    {
        $params = $request->validate([
            'type' => 'required|string',
        ]);
        switch ($params['type']) {
            case 'readme':
                return $this->getReadmeMenu();
            case 'modules':
                return $this->getModulesMenu();
            case 'database':
                return $this->getDatabaseMenu();
            default:
                return [];
        }
    }

    /**
     * @params type,required|string,菜单类型，如：readme/modules/database
     * @params key,required|string,菜单值
     * @response {"code":0,"message":"ok","data":{"content":"# admins"}}
     *
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function getContent(Request $request): array
    {
        $params = $request->validate([
            'type' => 'required|string',
            'key' => 'required|string',
        ]);
        switch ($params['type']) {
            case 'readme':
                return DocsHelper::getReadmeContent($params['type'], $params['key']);
            case 'modules':
                return DocsHelper::getModulesContent($params['key']);
            case 'database':
                return DocsHelper::getDatabaseContent($params['key']);
            default:
                return [];
        }
    }

    /**
     * @return array
     */
    private function getReadmeMenu(): array
    {
        $path = resource_path('laravel-devtools/readme');
        if (!File::isDirectory($path))
            $path = __DIR__ . '/../resources/laravel-devtools/readme';
        return DocsHelper::getReadmeChildrenDirs($path);
    }

    /**
     * @return array
     */
    private function getModulesMenu(): array
    {
        $baseDir = app_path('Modules' . DIRECTORY_SEPARATOR);
        return DocsHelper::getModulesMenu($baseDir);
    }

    /**
     * @return array
     * @throws Exception
     */
    private function getDatabaseMenu(): array
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
}
