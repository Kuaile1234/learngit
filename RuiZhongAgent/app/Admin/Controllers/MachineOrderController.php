<?php
    
    namespace App\Admin\Controllers;
    
    use App\Models\Order;
    use App\Http\Controllers\Controller;
    use Encore\Admin\Controllers\HasResourceActions;
    use Encore\Admin\Form;
    use Encore\Admin\Grid;
    use Encore\Admin\Layout\Content;
    use Encore\Admin\Show;
    
    class MachineOrderController extends Controller
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
                ->header('机具订单信息')
                ->description('Machine order information')
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
                ->header('Detail')
                ->description('description')
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
            return $content
                ->header('Edit')
                ->description('description')
                ->body($this->form()->edit($id));
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
            return $content
                ->header('机具订单添加')
                ->description('Create Machine order')
                ->body($this->form());
        }
        
        /**
         * Make a grid builder.
         *
         * @return Grid
         */
        protected function grid()
        {
            $grid = new Grid(new Order);
            $grid->expandFilter();
            $grid->tools(function ($tools) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
            $grid->column('user.user_name', '用户名');
            $grid->order_code('订单号');
            $grid->package_code('物流编号');
            $grid->order_money('订单金额');
            $grid->order_name('收货人名称');
            $grid->order_phone('收货人电话');
            $grid->order_address('收货人地址');
            
            $grid->expandFilter();
            $grid->filter(function ($filter) {
                $filter->column(1 / 2, function ($filter) {
                    $filter->disableIdFilter();
                    $filter->like('machine_code', '订单号');
                    $filter->between('column', '绑定时间')->datetime();
                });
                
                $filter->column(1 / 2, function ($filter) {
                    $filter->equal('machine_status', '订单状态')->select([
                        '0' => '未支付未发货',
                        '1' => '支付未发货',
                        '2' => '支付待收货',
                        '3' => '确认收货',
                    ]);
                    $filter->equal('machine_status', '支付方式')->select([
                        '0' => '微信',
                        '1' => '支付宝',
                        '2' => '银联',
                        '3' => '其他',
                    ]);
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
            $show = new Show(Order::findOrFail($id));
            
            $show->id('Id');
            $show->user_id('User id');
            $show->order_code('Order code');
            $show->package_code('Package code');
            $show->goods_info('Goods info');
            $show->discount_money('Discount money');
            $show->order_money('Order money');
            $show->add_time('Add time');
            $show->pay_type('Pay type');
            $show->order_status('Order status');
            $show->order_pic('Order pic');
            $show->order_name('Order name');
            $show->order_phone('Order phone');
            $show->order_address('Order address');
            $show->order_machine('Order machine');
            $show->pay_time('Pay time');
            $show->auth_id('Auth id');
            
            return $show;
        }
        
        /**
         * Make a form builder.
         *
         * @return Form
         */
        protected function form()
        {
            $form = new Form(new Order);
            
            $form->setTitle('机具订单添加');
          
            $form->text('order_code', '用户名称');
    
            
            return $form;
        }
    }
