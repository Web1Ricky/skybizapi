<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\TokenMismatchException;
use App\Models\DataSettingToken;
use App\Models\DataSetting;
use App\Helpers\DatabaseConnection;

class VerifyApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     * @throws \App\Exceptions\TokenMismatchException
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->verify($request)) {
            $PrivateAccessToken = $request->header('PrivateAccessToken');
            $resToken           = DataSettingToken::select('StoreCode', 'DatabaseName', 'IntegrationType')->where( 'PrivateAccessToken','=', $PrivateAccessToken)->first();
            $StoreCode          = $resToken->StoreCode;
            $DatabaseName       = $resToken->DatabaseName;
            $Setting            = DataSetting::select('DB_IP', 'DB_ID', 'DB_Password', 'DatabaseName' , 'DB_Port', 'URLLink', 'StoreCode')
            ->where(['StoreCode' => $StoreCode, 'DatabaseName'  => $DatabaseName])
            ->first();
            $URLLink            = $Setting->URLLink;
          
            if($URLLink=="https://skybizcloud.com/01/" OR $URLLink==""){
                $params             = fnSetParamDB($Setting->DB_IP, $Setting->DB_ID, $Setting->DB_Password, $Setting->DatabaseName, $Setting->DB_Port);
            }else{
                $Setting2           = $connection->table('datasetting')->where('StoreCode','=', $URLLink)->first();
                $params1            = fnSetParamDB($Setting2->DB_IP, $Setting2->DB_ID, $Setting2->DB_Password, $Setting2->DatabaseName, $Setting2->DB_Port);
                $connection2        = DatabaseConnection::setConnection($params1);
                $Setting3           = $connection->table('datasetting')
                ->where(['StoreCode' => $Setting->StoreCode, 'DatabaseName'  => $Setting->DatabaseName])
                ->first();
                $params             = fnSetParamDB($Setting3->DB_IP, $Setting3->DB_ID, $Setting3->DB_Password, $Setting3->DatabaseName, $Setting3->DB_Port);
            }
            $connection         = DatabaseConnection::setConnection($params);
            //Cookie::queue(Cookie::make('cookieName', 'value', $minutes));
            //$request->headers->add(['Authorization' => "Bearer {$request->PrivateAccessToken}"]);   
            return $next($request);
        }
        throw new TokenMismatchException;
    }
    /**
     * Verify token by querying database for existence of the client:token pair specified in headers.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    public function verify($request): bool //optional return types
    {
        $isExist=DataSettingToken::select('RunNo')
        ->where('PrivateAccessToken', '=', $request->header('PrivateAccessToken'))
        ->whereDate('DateEnd', '>', date('Y-m-d'))
        ->exists();
        return $isExist;
    }
}
