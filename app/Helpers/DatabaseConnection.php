<?php
namespace App\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatabaseConnection{

    public static function setConnection($params)
    {
        //dd($params);
        config(['database.connections.mysqldynamic' => [
            'driver'    => 'mysql',
            'host'      => $params['host'],
            'username'  => $params['username'],
            'password'  => $params['password'],
			'database'  => $params['dbname'],
			'port'      => $params['port']
        ]]);
        DB::purge('mysqldynamic'); 
        return DB::connection('mysqldynamic');
    }

}