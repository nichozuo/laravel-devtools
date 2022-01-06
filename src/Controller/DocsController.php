<?php


namespace Nichozuo\LaravelDevtools\Controller;


use Doctrine\DBAL\Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Nichozuo\LaravelDevtools\Helper\DbalHelper;
use Nichozuo\LaravelDevtools\Helper\DocsHelper;
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
     * @throws \Exception
     */
    public function getMenu(Request $request): array
    {
        $params = $request->validate([
            'type' => 'required|string',
        ]);
        switch ($params['type']) {
            case 'readme':
                return DocsHelper::getReadmeMenu();
            case 'modules':
                return DocsHelper::getModulesMenu(app_path('Modules' . DIRECTORY_SEPARATOR));
            case 'database':
                return DocsHelper::getDatabaseMenu();
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
                return DocsHelper::getReadmeContent($params['key']);
            case 'modules':
                return DocsHelper::getModulesContent($params['key']);
            case 'database':
                return DocsHelper::getDatabaseContent($params['key']);
            default:
                return [];
        }
    }
}
