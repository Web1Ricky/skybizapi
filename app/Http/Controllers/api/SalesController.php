<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use App\Models\CusInv;
use App\Models\CusInvDt;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     * $CusInv     = DB::connection('mysqldynamic')->table('stk_cus_inv_hd')->paginate(500);
     */
    public function ShowAll(Request $request)
    {
        $DateNow    = date("Y-m-d"); 
        if(isset($request->DateFrom) && isset($request->DateTo)){
            if(!strtotime($request->DateFrom)){
                return ResponseFormatter::error(null,"Invalid Date (From) ", 400);
            }
            if(!strtotime($request->DateTo)){
                return ResponseFormatter::error(null,"Invalid Date (To)", 400);
            }

            if(fnVerifyDate($request->DateFrom,$request->DateTo)>31){
                return ResponseFormatter::error(null,"Maximum Date Range 31 days", 400);
            }

            if($request->DateFrom>$request->DateTo){
                return ResponseFormatter::error(null,"Invalid Date Range", 400);
            }

            $from   = $request->DateFrom; 
            $to     = $request->DateTo;
        }else{
            $from   = date("Y-m-d", strtotime($DateNow." - 31 days")); 
            $to     = $DateNow;
        }
        $CusInv = CusInv::whereBetween('D_ate', [$from, $to])->paginate(100);
        return ResponseFormatter::success($CusInv,"success");

    }
    
    /**
     * Display a Sales Detail
     */
    public function SalesDetail(Request $request)
    {
        if(!isset($request->Doc1No)){
            return ResponseFormatter::error(null,"Required Document #", 400);
        }
        $CusInvDt = CusInvDt::where('Doc1No','=', $request->Doc1No)->paginate(100);
        return ResponseFormatter::success($CusInvDt,"success");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function APITest(Request $request)
    {
        try {
            DB::connection()->getPDO();
            $database = DB::connection()->getDatabaseName();
            dd("Connected successfully to database ".$database.".");
        } catch (Exception $e) {
            dd("None");
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        Artisan::call('config:clear');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
