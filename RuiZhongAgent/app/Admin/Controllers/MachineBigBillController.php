<?php

namespace App\Admin\Controllers;

use App\Models\MachinePosPay;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class MachineBigBillController extends Controller
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
            ->header('Index')
            ->description('description')
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
        $grid = new Grid(new MachinePosPay);

        $grid->id('Id');
        $grid->auth_id('Auth id');
        $grid->shop_code('Shop code');
        $grid->pay_card('Pay card');
        $grid->pay_money('Pay money');
        $grid->machine_code('Machine code');
        $grid->pay_card_type('Pay card type');
        $grid->pay_type('Pay type');
        $grid->pay_status('Pay status');
        $grid->add_time('Add time');
        $grid->status('Status');

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
        $show = new Show(MachinePosPay::findOrFail($id));

        $show->id('Id');
        $show->auth_id('Auth id');
        $show->shop_code('Shop code');
        $show->pay_card('Pay card');
        $show->pay_money('Pay money');
        $show->machine_code('Machine code');
        $show->pay_card_type('Pay card type');
        $show->pay_type('Pay type');
        $show->pay_status('Pay status');
        $show->add_time('Add time');
        $show->status('Status');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new MachinePosPay);

        $form->number('auth_id', 'Auth id');
        $form->text('shop_code', 'Shop code');
        $form->text('pay_card', 'Pay card');
        $form->text('pay_money', 'Pay money');
        $form->text('machine_code', 'Machine code');
        $form->switch('pay_card_type', 'Pay card type');
        $form->switch('pay_type', 'Pay type');
        $form->switch('pay_status', 'Pay status');
        $form->text('add_time', 'Add time');
        $form->switch('status', 'Status')->default(1);

        return $form;
    }
}
