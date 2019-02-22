<?php
    
    namespace App\Admin\Controllers;
    
    use App\Models\MachinePay;
    use App\Http\Controllers\Controller;
    use Encore\Admin\Controllers\HasResourceActions;
    use Encore\Admin\Form;
    use Encore\Admin\Grid;
    use Encore\Admin\Layout\Content;
    use Encore\Admin\Show;
    
    class MachineBillController extends Controller
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
                ->header('机具交易信息')
                ->description('Machine trade information')
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
                ->header('Create')
                ->description('description')
                ->body($this->form());
        }
        
        /**
         * Make a grid builder.
         *
         * @return Grid
         */
        protected function grid()
        {
            $grid = new Grid(new MachinePay);
            $grid->tools(function ($tools) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
            $grid->expandFilter();
            $grid->filter($this->indexSearch());
            $grid->agent_name('代理名');
            $grid->machine_code('机器号');
            $grid->shop_name('商户名');
            $grid->shop_phone('商户联系电话');
            $grid->pay_money('交易金额');
            $grid->add_time('交易时间')->display(function ($addTime) {
                return date('Y-m-d H:i:s', $addTime);
            });
            $grid->bank_card('商户银行卡');
            $grid->pay_status('交易类型')->display(function ($payStatus) {
                switch ($payStatus) {
                    case 1:
                        return "闪付";
                        break;
                    case 2:
                        return "云闪付";
                        break;
                    case 3:
                        return "普通交易";
                        break;
                    case 4:
                        return "其他";
                        break;
                    default:
                        return "错误";
                        break;
                }
            });
            $grid->status('交易状态')->display(function ($status) {
                return $status == 0 ? "失败" : "成功";
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
            $show = new Show(MachinePay::findOrFail($id));
            
            $show->id('Id');
            $show->agent_name('Agent name');
            $show->machine_code('Machine code');
            $show->shop_name('Shop name');
            $show->shop_phone('Shop phone');
            $show->pay_money('Pay money');
            $show->add_time('Add time');
            $show->bank_card('Bank card');
            $show->pay_status('Pay status');
            $show->status('Status');
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
            $form = new Form(new MachinePay);
            
            $form->text('agent_name', 'Agent name');
            $form->text('machine_code', 'Machine code');
            $form->text('shop_name', 'Shop name');
            $form->text('shop_phone', 'Shop phone');
            $form->decimal('pay_money', 'Pay money')->default(0.00);
            $form->number('add_time', 'Add time');
            $form->text('bank_card', 'Bank card');
            $form->switch('pay_status', 'Pay status')->default(3);
            $form->switch('status', 'Status')->default(1);
            $form->switch('auth_id', 'Auth id');
            
            return $form;
        }
        
        /**
         * Index in search action
         * @return \Closure
         */
        protected function indexSearch()
        {
            return function ($filter) {
                $filter->column(1 / 2, function ($filter) {
                    $filter->disableIdFilter();
                    $filter->like('machine_code', '机构名称');
                    $filter->like('machine_code', '商户名称');
                    $filter->like('shop_name', '机器编码');
                    $filter->between('column', '时间')->datetime();
                    
                });
                
                $filter->column(1 / 2, function ($filter) {
                    $filter->equal('is_ok', '交易状态')->select([
                        '一审',
                        '二审',
                        '审核成功',
                        '审核失败',
                    ]);
                    $filter->equal('is_ok', '交易类型')->select([
                        '一审',
                        '二审',
                        '审核成功',
                        '审核失败',
                    ]);
                });
            };
        }
    }
