<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CusInvDt extends Model
{
    use HasFactory;
    protected $connection   = 'mysqldynamic';
    protected $table        = 'stk_cus_inv_dt';
}
