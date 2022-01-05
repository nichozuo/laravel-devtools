# 修改配置

### app\Exceptions\Handler.php
> 用作全局统一处理Exception。当遇到exception，会进入此方法，用json的方式返回错误响应、并记录日志
```php
class Handler extends ExceptionHandler
{
    # 增加Trait
    use ExceptionsRenderTrait;

    # 修改register函数
    public function register()
    {
        $this->renderable(function (Exception $e, $request) {
            $data = $this->renderExceptionsJson($e, $request);
            $status = $data['status'];
            unset($data['status']);
            return response()->json($data, $status);
        });
    }
}
```

### app\Http\Controllers\Controller.php
> 为controller增加一些公用的方法
```php
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    # 增加trait
    use ControllerTrait;
}
```

### app\Http\Middleware\Authenticate.php
> 注释掉这里，让未登陆的auth不会跳转
```php
#注释掉这个方法里的内容
protected function redirectTo($request)
{
//        if (! $request->expectsJson()) {
//            return route('login');
//        }
}
```

### app\Http\Kernel.php
> 增加全局的统一json返回中间件，在控制器里return一个对象或者数组，都会自动包裹成json响应
```php
protected $middlewareGroups = [
    'web' => [
        \App\Http\Middleware\EncryptCookies::class,
        ...
    ],

    'api' => [
        'throttle:api',
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        # 在api的middleware里面，增加这个中间件
        ResponseJson::class
    ],
];
```

### config/app.php
> 配置时区和语言
```php
return [
    'timezone' => 'Asia/Shanghai',
    'locale' => 'zh_CN',
    'fallback_locale' => 'zh_CN',
    'faker_locale' => 'zh_CN',
];
```


