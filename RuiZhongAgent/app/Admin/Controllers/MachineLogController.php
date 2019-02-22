<?php
    
    namespace App\Admin\Controllers;
    
    use App\Models\MachineLog;
    use App\Http\Controllers\Controller;
    use Encore\Admin\Controllers\HasResourceActions;
    use Encore\Admin\Form;
    use Encore\Admin\Grid;
    use Encore\Admin\Layout\Content;
    use Encore\Admin\Show;
    
    class MachineLogController extends Controller
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
                ->header('机具修改信息')
                ->description('Machine modification information')
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
            $grid = new Grid(new MachineLog);
            $grid->disableCreateButton();
            $grid->tools(function ($tools) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
            
            $grid->expandFilter();
            $grid->filter($this->indexSearch());
            
            $grid->shop_name('商户名称');
            $grid->shop_phone('商户电话');
            $grid->old_machine('旧的机具');
            $grid->new_machine('新的机具');
            $grid->status('机器状态');
            $grid->add_time('更改时间');
            
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
            $show = new Show(MachineLog::findOrFail($id));
            
            $show->id('Id');
            $show->user_id('User id');
            $show->shop_name('Shop name');
            $show->shop_phone('Shop phone');
            $show->old_machine('Old machine');
            $show->new_machine('New machine');
            $show->status('Status');
            $show->add_time('Add time');
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
            $form = new Form(new MachineLog);
            
            $form->number('user_id', 'User id');
            $form->text('shop_name', 'Shop name');
            $form->text('shop_phone', 'Shop phone');
            $form->text('old_machine', 'Old machine');
            $form->text('new_machine', 'New machine');
            $form->switch('status', 'Status')->default(1);
            $form->number('add_time', 'Add time');
            $form->switch('auth_id', 'Auth id');
            
            return $form;
        }
        
        public function indexSearch()
        {
            return function ($filter) {
                $filter->column(1 / 2, function ($filter) {
                    $filter->disableIdFilter();
                    $filter->like('machine_code', '旧机编码');
                    $filter->like('machine_code', '新机编号');
                    $filter->like('shop_name', '商户名称');
                    
                });
                
                $filter->column(1 / 2, function ($filter) {
                    $filter->between('column', '时间')->datetime();
                    
                    $filter->equal('is_ok', '交易状态')->select([
                        '正在处理',
                        '更改成功',
                        '更改失败',
                    ]);
                    
                });
            };
        }
    }
