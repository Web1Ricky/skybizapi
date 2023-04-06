<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\DataSettingToken;

class ScopeApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $ScopeType): Response
    {
        $PrivateAccessToken = $request->header('PrivateAccessToken');
        $resToken           = DataSettingToken::select('IntegrationType')->where( 'PrivateAccessToken','=', $PrivateAccessToken)->first();
        $IntegrationType   = $resToken->IntegrationType;
        if($IntegrationType==$ScopeType){
            return $next($request);
        }else{
            return redirect()->route('scopedenied');
        }
    }

   
}
