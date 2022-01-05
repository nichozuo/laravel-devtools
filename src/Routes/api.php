<?php

use Illuminate\Support\Facades\Route;
use Nichozuo\LaravelDevtools\Controller\DocsController;
use Nichozuo\LaravelHelpers\Helper\RouteHelper;

Route::middleware('api')->prefix('/api/docs')->name('docs.')->group(function ($router) {
    if (config('app.debug')) {
        RouteHelper::New($router, DocsController::class);
    }
});