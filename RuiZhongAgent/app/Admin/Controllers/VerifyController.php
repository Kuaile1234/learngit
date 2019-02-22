<?php

namespace App\Admin\Controllers;

use App\Models\UserCard;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class VerifyController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('实名认证')
            ->description('real-name authentication')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
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
     * @param mixed $id
     * @param Content $content
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
        $grid = new Grid(new UserCard);
        $grid->expandFilter();
        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });
        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->column(1 / 2, function ($filter) {
                $filter->like('machine_code', '机器编码');
                $filter->like('shop_name', '名称');
            });
        
            $filter->column(1 / 2, function ($filter) {
                $filter->like('shop_card', '身份证号');
                $filter->equal('machine_status', '查询状态')->select([
                    '0' => '未出库',
                    '1' => '已出库',
                    '2' => '已绑定',
                    '3' => '已激活',
                ]);
            });
        });
        $grid->id('编码');
        $grid->user_id('用户编码');
        $grid->card_type('卡状态');
        $grid->is_ok('是否验证');
        $grid->add_time('添加时间');

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(UserCard::findOrFail($id));

        $show->id('Id');
        $show->user_id('User id');
        $show->card_type('Card type');
        $show->card_up('Card up');
        $show->card_down('Card down');
        $show->hold_card('Hold card');
        $show->is_ok('Is ok');
        $show->add_time('Add time');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new UserCard);

        $form->number('user_id', 'User id');
        $form->switch('card_type', 'Card type')->default(1);
        $form->text('card_up', 'Card up');
        $form->text('card_down', 'Card down');
        $form->text('hold_card', 'Hold card');
        $form->switch('is_ok', 'Is ok');
        $form->number('add_time', 'Add time');

        return $form;
    }
}
