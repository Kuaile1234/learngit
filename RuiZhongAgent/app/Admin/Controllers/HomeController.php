<?php
    
    namespace App\Admin\Controllers;
    
    use App\Http\Controllers\Controller;
    use Encore\Admin\Controllers\Dashboard;
    use Encore\Admin\Layout\Column;
    use Encore\Admin\Layout\Content;
    use Encore\Admin\Layout\Row;
    
    class HomeController extends Controller
    {
        public function index(Content $content)
        {
            return $content
                ->header('控制面板')
                ->description('Welcome to use agent background')
                ->body(view('admin.index'))
                ->body(view('admin.chartjs'))
                ->body(view('admin.table'));
        }
    }
