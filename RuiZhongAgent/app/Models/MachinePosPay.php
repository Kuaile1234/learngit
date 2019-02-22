<?php
    
    namespace App\Models;
    
    use Illuminate\Database\Eloquent\Model;
    
    class MachinePosPay extends Model
    {
        public $table = 'machine_pos_pay';
        
        protected $connection = 'ruizhong';
    }
