<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CloudDtTrnIn extends Model
{
    use HasFactory;
    protected $connection   = 'mysqldynamic';
    protected $table        = 'cloud_detail_trn_in';

    public $timestamps      = false;
}
