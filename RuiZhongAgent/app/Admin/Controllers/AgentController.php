<?php
    
    namespace App\Admin\Controllers;
    
    use App\Models\Agent;
    use App\Http\Controllers\Controller;
    use App\Models\User;
    use Encore\Admin\Controllers\HasResourceActions;
    use Encore\Admin\Form;
    use Encore\Admin\Grid;
    use Encore\Admin\Layout\Content;
    use Encore\Admin\Show;
    
    class AgentController extends Controller
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
                ->header('代理列表')
                ->description('Agent List')
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
            $grid = new Grid(new User);
            $grid->tools(function ($tools) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
            
            $grid->id('Id');
            $grid->user_name('用户名');
            $grid->add_time('开通时间')->display(function ($endTime) {
                return date("Y-m-d H:i:s", $endTime);
            });
            $grid->user_area('所属大区');
            $grid->user_level('代理级别');
            $grid->user_local('代理定位');
            $grid->is_ok('状态')->display(function ($isOk) {
                return $isOk == 1 ? "启用" : "禁用";
            });
            $grid->card_ok('是否实名')->display(function ($cardOk) {
                return $cardOk == 1 ? "已实名" : "未实名";
            });
            
            $grid->expandFilter();
            $grid->filter(function ($filter) {
                $filter->column(1 / 2, function ($filter) {
                    $filter->disableIdFilter();
                    $filter->like('user_name', '用户名');
                    $filter->like('user_level', '级别');
                });
                
                $filter->column(1 / 2, function ($filter) {
                    $filter->equal('is_ok', '代理状态')->select([
                        '1' => '启用',
                        '0' => '禁用',
                    ]);
                    $filter->between('add_time', "开通时间")->datetime();
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
            $show = new Show(User::findOrFail($id));
    
            $show->id('Id');
            $show->user_name('用户名');
            $show->add_time('开通时间')->display(function ($endTime) {
                return date("Y-m-d H:i:s", $endTime);
            });
            $show->user_area('所属大区');
            $show->user_level('代理级别');
            $show->user_local('代理定位');
            $show->is_ok('状态')->display(function ($isOk) {
                return $isOk == 1 ? "启用" : "禁用";
            });
            $show->card_ok('是否实名')->display(function ($cardOk) {
                return $cardOk == 1 ? "已实名" : "未实名";
            });
    
    
            return $show;
        }
        
        /**
         * Make a form builder.
         *
         * @return Form
         */
        protected function form()
        {
            $form = new Form(new User);
            
            $form->text('user_name','用户名');
            $form->text('user_phone','手机号');
            $form->password('user_password','密码');
            $form->text('improt_code','所属上级推荐码');
            $form->number('user_level','代理商级别');
            $form->text('user_area','所在大区');
            
            return $form;
        }
    }
