<?php
    
    namespace App\Admin\Controllers;
    
    use App\Models\MachineMess;
    use App\Http\Controllers\Controller;
    use Encore\Admin\Controllers\HasResourceActions;
    use Encore\Admin\Facades\Admin;
    use Encore\Admin\Form;
    use Encore\Admin\Grid;
    use Encore\Admin\Layout\Content;
    use Encore\Admin\Show;
    
    class MachineHandController extends Controller
    {
        use HasResourceActions;
        
        
        protected $status = [
            0 => '未出库',
            1 => '已出库',
            2 => '已绑定',
            3 => '已激活',
            4 => '已返现',
            5 => '不返现',
            6 => '赠送机',
        ];
        
        
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
                ->header('手刷机具')
                ->breadcrumb(
                    ['text' => '机具信息'],
                    ['text' => '手刷机具']
                )
                ->description('Hand brush machine')
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
                ->header('手刷机具详情')
                ->description('Hand brush with details')
                ->body($this->detail($id));
        }
        
        /**
         * Edit interface.
         *
         * @param mixed   $id
         * @param Content $content
         *
         * @return Content
         */
        public function edit($id, Content $content)
        {
            $form = new Form(new MachineMess);
            
            $form->select('machine_type', '机具型号')->options([
                0 => '手刷机具',
                1 => '大POS机具',
            ]);
            
            $form->text('machine_code', '机器号');
            $form->text('ok_return_money', '返现金额');
            $form->text('shop_name', '商户名');
            $form->text('shop_phone', '商户联系电话');
            $form->text('shop_card', '商户身份证号');
            $form->select('machine_status', '机器状态')->options($this->status);
            
            return $content
                ->header('更新机具信息')
                ->description('Update machine information')
                ->body($form->edit($id));
        }
        
        /**
         * Create interface.
         *
         * @param Content $content
         *
         * @return Content
         */
        public function create(Content $content)
        {
            $form = new Form(new MachineMess);
            
            $form->select('machine_type', '机具型号')->options([
                0 => '手刷机具',
                1 => '大POS机具',
            ]);
            
            $form->text('machine_code_start', '请输入机器开始编码');
            $form->text('machine_code_end', '请输入机器结束编码');
            $form->html('<b style="color:red">如果机器只有一台请输入相同数字</b>', $label = '');
            
            
            return $content
                ->header('机具信息添加')
                ->description('Create Machine information')
                ->body($form);
        }
        
        /**
         * Make a grid builder.
         *
         * @return Grid
         */
        protected function grid()
        {
            $grid = new Grid(new MachineMess);
            $grid->model()->where('user_id', '=', Admin::user()->id);
            $grid->expandFilter();
            $grid->tools(function ($tools) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
            
            $grid->column('user.user_name', '用户')->display(function ($userName) {
                return $userName == '' ? '-' : $userName;
            });
            $grid->machine_type('机具型号')->display(function ($machineType) {
                if ($machineType == 0) {
                    return "闪付";
                } else {
                    if ($machineType == 1) {
                        return "大pos";
                    }
                }
                
                return "未知机具型号";
            });
            $grid->machine_code('机器编号');
            
            $grid->ok_return_money('返现金额')->display(function ($okReturnMoney) {
                return "¥ " . $okReturnMoney;
            })->sortable();
            
            $grid->shop_name('商户名称')->display(function ($shopName) {
                return $shopName == 0 ? '-' : $shopName;
            });
            
            $grid->shop_phone('商户联系电话')->display(function ($shopPhone) {
                return $shopPhone == 0 ? '-' : $shopPhone;
            });
            
            $grid->shop_card('商户身份证号')->display(function ($shopCard) {
                return $shopCard == 0 ? '-' : $shopCard;
            });
            
            $status = $this->status;
            $grid->machine_status('机器状态')->display(function ($machineStatus) use ($status) {
                if (isset($status[$machineStatus])) {
                    return $status[$machineStatus];
                } else {
                    return "解绑下拨";
                }
            });
            
            
            $grid->add_time('机具绑定时间')->display(function ($addTime) {
                return $addTime == 0 ? "-" : date('Y-m-d H:i:s', $addTime);
            });
            
            $grid->expandFilter();
            $grid->filter(function ($filter) {
                $filter->column(1 / 2, function ($filter) {
                    $filter->disableIdFilter();
                    $filter->like('machine_code', '机器编号');
                    $filter->like('shop_name', '商户名称');
                });
                
                $filter->column(1 / 2, function ($filter) {
                    $filter->like('shop_card', '身份证号');
                    $filter->equal('machine_status', '机器状态')->select($this->status);
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
            $show = new Show(MachineMess::findOrFail($id));
            
            $show->machine_code('机器编号');
            
            return $show;
        }
        
        /**
         * Make a form builder.
         *
         * @return Form
         */
        protected function form()
        {
            $form = new Form(new MachineMess);
            
            $form->number('user_id', 'User id');
            $form->text('machine_code', 'Machine code');
            $form->text('shop_code', 'Shop code');
            $form->text('shop_name', 'Shop name');
            $form->text('shop_phone', 'Shop phone');
            $form->text('shop_card', 'Shop card');
            $form->switch('machine_status', 'Machine status');
            $form->switch('status', 'Status');
            $form->switch('machine_type', 'Machine type');
            $form->number('add_time', 'Add time');
            $form->number('ok_return_money', 'Ok return money');
            $form->number('ok_time', 'Ok time');
            $form->number('return_time', 'Return time');
            $form->decimal('money', 'Money')->default(0.00);
            $form->number('return_num', 'Return num');
            $form->number('return_status', 'Return status');
            $form->switch('auth_id', 'Auth id');
            
            return $form;
        }
        
        public function shipment(Content $content)
        {
            $form = new Form(new MachineMess);
            $form->setTitle("请填写出货信息");
            $form->disableViewCheck();
            $form->disableCreatingCheck();
            $form->disableEditingCheck();

            $form->text('machine_code_start', '代理用户名');
            $form->text('machine_code_start', '代理身份证号');
            $form->text('machine_code_start', '代理手机号');
            
            $form->text('machine_code_start', '请输入机器开始编码');
            $form->text('machine_code_end', '请输入机器结束编码');
            $form->html('<b style="color:red">如果机器只有一台请输入相同数字</b>', $label = '');
            
            $form->select('machine_type', '返现金额')->options([
                '¥ 160',
                '¥ 170',
                '¥ 180',
                '¥ 190',
                '¥ 200',
                '¥ 210',
            ]);
            
            $form->select('machine_type', '机器状态')->options([
                '已出库',
                '已绑定',
                '已激活',
                '已返现',
                '不返现',
                '赠送机',
                '解绑下拨',
            ]);
            
            $form->select('machine_type', '机器属性')->options([
                0 => '赠送机',
                1 => '正版机',
            ]);
            
            return $content
                ->header('手刷机具出货')
                ->description('Hand brush with shipment')
                ->body($form);
        }
    }
