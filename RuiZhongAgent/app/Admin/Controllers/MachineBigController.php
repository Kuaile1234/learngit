<?php
    
    namespace App\Admin\Controllers;
    
    use App\Models\MachineMess;
    use App\Http\Controllers\Controller;
    use Encore\Admin\Controllers\HasResourceActions;
    use Encore\Admin\Form;
    use Encore\Admin\Grid;
    use Encore\Admin\Layout\Content;
    use Encore\Admin\Show;
    use PhpOffice\PhpSpreadsheet\IOFactory;
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
    
    class MachineBigController extends Controller
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
                ->header('大POS机具信息')
                ->description('Pos machine information')
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
                ->header('大POS机具信息详情')
                ->description('POS machine information details')
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
            $grid = new Grid(new MachineMess);
            $grid->model()->where('machine_type', '=', 1);
            $grid->tools(function ($tools) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
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
                    $filter->equal('is_ok', '审核状态')->select([
                        '一审',
                        '二审',
                        '审核成功',
                        '审核失败',
                    ]);
                });
            });
            $grid->column('user.user_name', '代理姓名');
            $grid->machine_code('机具编号');
            $status = $this->status;
            $grid->column('machine_status', '机器状态')->display(function ($machineStatus) use ($status) {
                if (isset($status[$machineStatus])) {
                    return $status[$machineStatus];
                } else {
                    return "解绑下拨";
                }
            });
            $grid->shop_name('商户姓名');
            $grid->shop_phone('商户手机号');
            $grid->shop_card('商户证件号');
//            $grid->column('info.bank_name', '银行名称');
//            $grid->column('info.bank_open', '开卡分行');
//            $grid->column('info.shop_bank', '银行卡号');
            $grid->column('info.status', '结算方式');
            $grid->column('info.point', '费率');
            $grid->column('info.is_ok', '审核状态')->display(function ($isOk) {
                return $isOk == 3 ? "终审成功" : "未提交审核";
            });
            $grid->column('add_time', '提交时间')->display(function ($addTime) {
                return date('Y-m-d H:i:s', $addTime);
            });
            
            $grid->exporter(new class extends Grid\Exporters\AbstractExporter
            {
                
                /**
                 * {@inheritdoc}
                 */
                public function export()
                {
                    $data[] = ['编码', '机器编码', '商户名称', '商户手机号码'];
                    
                    $rows = collect($this->getData())->map(function ($item) {
                        return array_only($item, ['id', 'machine_code', 'shop_name', 'shop_phone']);
                    });
                    
                    foreach ($rows->toArray() as $key => $value) {
                        $data[] = [
                            $value['id'],
                            $value['machine_code'],
                            $value['shop_name'],
                            $value['shop_phone'],
                        ];
                    }
                    $spreadsheet = new Spreadsheet();
                    $spreadsheet->getActiveSheet()
                        ->fromArray($data, null, 'C2');
                    
                    $drawing = new Drawing();
                    $drawing->setPath(__DIR__ . "/WechatIMG39566.jpeg");
                    $drawing->setCoordinates('H2');
                    $drawing->setWorksheet($spreadsheet->getActiveSheet());
                    
                    $drawing = new Drawing();
                    $drawing->setPath(__DIR__ . "/WechatIMG39566.jpeg");
                    $drawing->setCoordinates('M2');
                    $drawing->setWorksheet($spreadsheet->getActiveSheet());
                    $spreadsheet->getActiveSheet()->setTitle('Simple');
                    $spreadsheet->setActiveSheetIndex(0);
                    
                    $drawing = new Drawing();
                    $drawing->setPath(__DIR__ . "/WechatIMG39566.jpeg");
                    $drawing->setCoordinates('R2');
                    $drawing->setWorksheet($spreadsheet->getActiveSheet());
                    $spreadsheet->getActiveSheet()->setTitle('Simple');
                    $spreadsheet->setActiveSheetIndex(0);
                    
                    $drawing = new Drawing();
                    $drawing->setPath(__DIR__ . "/WechatIMG39566.jpeg");
                    $drawing->setCoordinates('W2');
                    $drawing->setWorksheet($spreadsheet->getActiveSheet());
                    $spreadsheet->getActiveSheet()->setTitle('Simple');
                    $spreadsheet->setActiveSheetIndex(0);
                    
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="01simple.xls"');
                    header('Cache-Control: max-age=0');
                    header('Cache-Control: max-age=1');
                    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
                    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
                    header('Pragma: public'); // HTTP/1.0
                    $writer = IOFactory::createWriter($spreadsheet, 'Xls');
                    $writer->save('php://output');
                }
            });

//        $grid->status('Status');
//        $grid->machine_type('Machine type');
//        $grid->add_time('Add time');
//        $grid->ok_return_money('Ok return money');
//        $grid->ok_time('Ok time');
//        $grid->return_time('Return time');
//        $grid->money('Money');
//        $grid->return_num('Return num');
//        $grid->return_status('Return status');
//        $grid->auth_id('Auth id');
            
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
            
            $show->machine_code('机具编号');
            $show->shop_code('商户标号');
            $show->shop_name('商户姓名');
            $show->shop_phone('商户手机号');
            $show->shop_card('商户证件号');
            
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
    }
