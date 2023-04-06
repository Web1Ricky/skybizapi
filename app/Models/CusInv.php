<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CusInv extends Model
{
    use HasFactory;
    protected $connection   = 'mysqldynamic';
    protected $table        = 'stk_cus_inv_hd';
    protected $keyType      = 'string';
    public $incrementing    = false;
    public $timestamps      = false;
    protected $primaryKey   = 'Doc1No';
}
