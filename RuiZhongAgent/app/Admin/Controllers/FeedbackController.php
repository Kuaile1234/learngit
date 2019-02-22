<?php
    
    namespace App\Admin\Controllers;
    
    use App\Models\Question;
    use App\Http\Controllers\Controller;
    use Encore\Admin\Controllers\HasResourceActions;
    use Encore\Admin\Form;
    use Encore\Admin\Grid;
    use Encore\Admin\Layout\Content;
    use Encore\Admin\Show;
    
    class FeedbackController extends Controller
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
                ->header('问题反馈')
                ->description('problem of feedback')
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
            $grid = new Grid(new Question);
            $grid->expandFilter();
            $grid->filter(function ($filter) {
                $filter->column(1 / 2, function ($filter) {
                    $filter->disableIdFilter();
                    $filter->like('user_name', '标题');
                    $filter->like('user_level', '内容');
                });
                
                $filter->column(1 / 2, function ($filter) {
                    $filter->equal('is_ok', '状态')->select([
                        '0' => '禁用',
                        '1' => '启用',
                    ]);
                    $filter->between('add_time', "添加时间")->datetime();
                });
            });
            $grid->tools(function ($tools) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
            $grid->add_time('添加时间')->display(function ($addTime) {
                return date('Y-m-d H:i:s', $addTime);
            });
            $grid->question_title('标题');
            $grid->question_content('内容');
            $grid->question_status('处理状态')->display(function ($status) {
                if ($status == 0) {
                    return "<b style='color: red;'>未处理</b>";
                }
                if ($status == 1) {
                    return "<b style='color:green'>已处理</b>";
                }
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
            $show = new Show(Question::findOrFail($id));
            
            $show->id('Id');
            $show->user_id('User id');
            $show->add_time('Add time');
            $show->question_title('Question title');
            $show->question_content('Question content');
            $show->question_status('Question status');
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
            $form = new Form(new Question);
            
            $form->number('user_id', 'User id');
            $form->number('add_time', 'Add time');
            $form->text('question_title', 'Question title');
            $form->text('question_content', 'Question content');
            $form->switch('question_status', 'Question status');
            $form->switch('auth_id', 'Auth id');
            
            return $form;
        }
    }
