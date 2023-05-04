<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CloudDtTrnOut extends Model
{
    use HasFactory;
    protected $connection   = 'mysqldynamic';
    protected $table        = 'cloud_detail_trn_out';
    public $timestamps      = false;
}
