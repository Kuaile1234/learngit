<?php
    
    namespace App\Admin\Controllers;
    
    use App\Models\Machine;
    use App\Http\Controllers\Controller;
    use App\Models\User;
    use Encore\Admin\Controllers\HasResourceActions;
    use Encore\Admin\Facades\Admin;
    use Encore\Admin\Form;
    use Encore\Admin\Grid;
    use Encore\Admin\Layout\Content;
    use Encore\Admin\Show;
    
    class MachineMonthController extends Controller
    {
        use HasResourceActions;
        
        /**
         * Index interface.
         *
         * @param Content $content
         *
         * @return Content
         */
        public function index(Content $content)
        {
            return $content
                ->header('机具每月信息')
                ->description('Machine monthly information')
                ->breadcrumb(
                    ['text' => '机具每月信息', 'url' => '/admin/machine']
                )
                ->body($this->grid());
        }
        
        /**
         * Show interface.
         *
         * @param mixed   $id
         * @param Content $content
         *
         * @return Content
         */
        public function show($id, Content $content)
        {
            return $content
                ->header('查看详细信息')
                ->description('View details')
                ->body($this->detail($id));
        }
        
        
        /**
         * Make a grid builder.
         *
         * @return Grid
         */
        protected function grid()
        {
            $grid = Admin::grid(Machine::class, function (Grid $grid) {
                $grid->disableCreateButton();
                
                $grid->actions(function ($actions) {
                    $actions->disableDelete();
                    $actions->disableEdit();
                });
                $grid->tools(function ($tools) {
                    $tools->batch(function ($batch) {
                        $batch->disableDelete();
                    });
                });
                
                $grid->column('user.user_name', '用户');
                $grid->column('machine_code', '机器号');
                $grid->column('ok_return_money', '返现')->display(function ($okReturnMoney) {
                    return "¥ {$okReturnMoney}";
                });


                $grid->column('end_time', '失效时间')->display(function ($endTime) {
                    return date("Y-m-d H:i:s", $endTime);
                });
                $grid->column('month', '当前月份');
                
                $grid->column('return_money', '30天累计交易')->display(function ($returnMoney) {
                    return "¥ {$returnMoney}";
                });
                
                $grid->column('month_money', '当月累计量')->display(function ($monthMoney) {
                    return "¥ {$monthMoney}";
                });
                
                $grid->column('month_quick_money', '当月闪付交易量')->display(function ($monthQuickMoney) {
                    return "¥ {$monthQuickMoney}";
                });
                
                $grid->column('month_yquick_money', '当月云闪付交易量')->display(function ($monthYquickMoney) {
                    return "¥ {$monthYquickMoney}";
                });
                
                $grid->column('month_normal_money', '普通交易')->display(function ($monthNormalMoney) {
                    return "¥ {$monthNormalMoney}";
                });
                $grid->expandFilter();
                $grid->filter(function ($filter) {
                    $filter->disableIdFilter();
                    $filter->column(1 / 2, function ($filter) {
                        $filter->between('column', '绑定时间')->datetime();
    
                        $filter->like('machine_code', '机器编号');
    
                        $filter->equal('machine_status', '机具类型')->select([
                            '0' => '手刷',
                            '1' => '大Pos机',
                        ]);
                    });
                    
                    $filter->column(1 / 2, function ($filter) {
                        $filter->like('shop_name', '商户名称');
                        $filter->equal('machine_status', '机具状态')->select([
                            '0' => '未出库',
                            '1' => '已出库',
                            '2' => '已绑定',
                            '3' => '已激活',
                            '4' => '已返现',
                            '5' => '不返现',
                            '6' => '赠送机',
                            '7' => '解绑下拨',
                        ]);
                        
                    });
                });
            });
            
            
            return $grid;
        }
        
        /**
         * Make a show builder.
         *
         * @param mixed $id
         *
         * @return Show
         */
        protected function detail($id)
        {
            $show = new Show(Machine::findOrFail($id));
            
            return $show;
        }
        
        
    }
