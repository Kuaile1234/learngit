<?php

namespace App\Admin\Controllers;

use App\Models\OutMoney;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class WithdrawalController extends Controller
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
            ->header('提现申请')
            ->description('withdrawal application')
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
        $grid = new Grid(new OutMoney);
        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });
        $grid->filter(function ($filter) {
            $filter->column(1 / 2, function ($filter) {
                $filter->disableIdFilter();
                $filter->like('user_name', '用户');
                $filter->like('user_level', '银行卡号');
                $filter->like('user_area', '提现金额');
            });
        
            $filter->column(1 / 2, function ($filter) {
                $filter->like('user_area', '联系电话');
                $filter->equal('is_ok', '状态')->select([
                    '0' => '禁用',
                    '1' => '启用',
                ]);
                $filter->between('add_time', "开通时间")->datetime();
            });
        });
        $grid->expandFilter();
        $grid->id('编码');
        $grid->user_id('用户编码');
        $grid->bank_card('银行卡号');
        $grid->out_money('提现金额');
        $grid->add_time('申请时间');
        $grid->status('状态');
        $grid->out_name('发卡行');
        $grid->out_phone('联系电话');

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
        $show = new Show(OutMoney::findOrFail($id));

        $show->id('Id');
        $show->user_id('User id');
        $show->bank_card('Bank card');
        $show->out_money('Out money');
        $show->add_time('Add time');
        $show->status('Status');
        $show->month('Month');
        $show->out_name('Out name');
        $show->out_phone('Out phone');
        $show->auth_id('Auth id');
        $show->txt('Txt');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new OutMoney);

        $form->number('user_id', 'User id');
        $form->text('bank_card', 'Bank card');
        $form->decimal('out_money', 'Out money')->default(0.00);
        $form->number('add_time', 'Add time');
        $form->number('status', 'Status');
        $form->number('month', 'Month');
        $form->text('out_name', 'Out name');
        $form->text('out_phone', 'Out phone');
        $form->switch('auth_id', 'Auth id');
        $form->text('txt', 'Txt');

        return $form;
    }
}
