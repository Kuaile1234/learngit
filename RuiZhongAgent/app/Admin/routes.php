<?php
    
    use Illuminate\Routing\Router;
    
    Admin::registerAuthRoutes();
    
    Route::group([
        'prefix'     => config('admin.route.prefix'),
        'namespace'  => config('admin.route.namespace'),
        'middleware' => config('admin.route.middleware'),
    ], function (Router $router) {
        
        $router->get('/', 'HomeController@index');
        $router->resource('machine/hand', 'MachineHandController');
        $router->get('machine/shipment', 'MachineHandController@shipment');
        $router->resource('machine/big', 'MachineBigController');
        $router->resource('machine/bigBill', 'MachineBigBillController');
        $router->resource('machine/data/month', 'MachineMonthController');
        $router->resource('machine/order', 'MachineOrderController');
        $router->resource('machine/bill', 'MachineBillController');
        $router->resource('machine/log', 'MachineLogController');
        $router->resource('agent', 'AgentController');
        $router->resource('verify', 'VerifyController');
        $router->resource('withdrawal', 'WithdrawalController');
        $router->resource('feedback', 'FeedbackController');
        
    });
