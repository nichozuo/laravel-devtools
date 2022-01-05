# 常用包
## laravel-utils
- https://github.com/nichozuo/laravel-utils
```bash
composer require nichozuo/laravel-utils -v
```
## nichozuo/laravel-codegen
- https://github.com/nichozuo/laravel-codegen
- https://github.com/nichozuo/laravel-doc
```bash
composer require nichozuo/laravel-codegen --dev -v
php artisan vendor:publish --provider="Nichozuo\LaravelCodegen\ServiceProvider"
```

## 树形结构
- https://github.com/lazychaser/laravel-nestedset
```bash
composer require kalnoy/nestedset
```
```php
Schema::create('table', function (Blueprint $table) {
    ...
    $table->nestedSet();
});
```
## 中文包
- https://github.com/Laravel-Lang/lang
```bash
composer require laravel-lang/lang:~7.0
```
- config/app.php
```php
    'locale' => 'zh_CN',
```

## Api Token
- https://laravel.com/docs/8.x/sanctum
```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

## 权限
- https://github.com/spatie/laravel-permission
- https://spatie.be/docs/laravel-permission/v4/introduction
```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```
```php
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Nichozuo\LaravelCodegen\Helper\DbalHelper;

class CreatePermissionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }

        Schema::create($tableNames['permissions'], function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');       // For MySQL 8.0 use string('name', 125);
            $table->string('guard_name')->default('sanctum'); // For MySQL 8.0 use string('guard_name', 125);

            $table->enum('type', ['目录', '页面', '按钮'])->comment('类型');
            $table->string('url', 100)->comment('菜单链接')->nullable();
            $table->string('permission', 500)->comment('授权(多个用逗号分隔，如：user:list,user:create)')->nullable();
            $table->string('icon', 50)->comment('图标')->nullable();
            $table->nestedSet();    // 树形结构

            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        Schema::create($tableNames['roles'], function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');       // For MySQL 8.0 use string('name', 125);
            $table->string('guard_name')->default('sanctum'); // For MySQL 8.0 use string('guard_name', 125);
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });
        
        DbalHelper::comment($tableNames['permissions'], '权限和菜单');
        DbalHelper::comment($tableNames['roles'], '角色');
        DbalHelper::comment($tableNames['model_has_permissions'], '用户拥有权限');
        DbalHelper::comment($tableNames['model_has_roles'], '用户拥有角色');
        DbalHelper::comment($tableNames['role_has_permissions'], '角色拥有权限');
    }
}
```
```php
<?php


namespace App\Models;

class Admins extends \Illuminate\Foundation\Auth\User
{
    use ModelTrait, HasFactory;
    use HasApiTokens, HasRoles;

    # model defines
    protected $table = 'admins';
    protected $fillable = ['username', 'password'];
    protected $hidden = ['password'];

    protected $guard_name = 'sanctum';

}
```

## iseed
- https://github.com/orangehill/iseed
```bash
composer require orangehill/iseed
```

## Excel
- https://github.com/Maatwebsite/Laravel-Excel
- https://docs.laravel-excel.com/3.1/getting-started/
```bash
composer require maatwebsite/excel
```

## 阿里云oss
- https://github.com/aliyun/aliyun-oss-php-sdk
```bash
composer require aliyuncs/oss-sdk-php
```

## 短信
- https://github.com/overtrue/easy-sms
```bash
composer require overtrue/easy-sms
```

## 微信
- https://github.com/w7corp/easywechat
```bash
composer require overtrue/wechat:^5.0 -vvv
```

## Tag
- https://github.com/rtconner/laravel-tagging
```bash
composer require rtconner/laravel-tagging
```
