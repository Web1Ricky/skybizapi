<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\ResponseFormatter;
use App\Helpers\InsertDetailTrn;
use App\Models\CloudDtTrnIn;
use App\Models\CloudDtTrnOut;

class InventoryController extends Controller
{
    //
    public function OnHand(Request $request)
    {
        $SessionID                  = $request->header('PrivateAccessToken');
        dd($SessionID);
        $LocationCode               = $request->LocationCode;
        $ItemCode                   = $request->ItemCode;
        $params['SessionID']        = $SessionID;
        $params['arrItemCode']      = array($ItemCode);
        if(!empty($LocationCode)){
            $params['arrLocationCode']  = array($LocationCode);
        }else{
            $params['arrLocationCode']  = array();
        }

        $params['DateFrom']         = $request->DateFrom;
        $params['DateTo']           = date('Y-m-d');
        $params['DatabaseName']     = $request->DatabaseName;
        $InsertDt                   = InsertDetailTrn::setDetail($params);

        //if no
        if(!empty($LocationCode)){
            $dataIN      = CloudDtTrnIn::select(DB::raw("SUM(QtyIN*FactorQty) AS QtyIN"))
                        ->where('ItemCode','=',$ItemCode)
                        ->where('SessionID','=', $SessionID)
                        ->where('LocationCode','=', $LocationCode)
                        ->get();
            $dataOUT     = CloudDtTrnOut::select(DB::raw("SUM(QtyOUT*FactorQty) AS QtyOUT"))
                        ->where('VoidYN','=','0')
                        ->where('ItemCode','=',$ItemCode)
                        ->where('SessionID','=', $SessionID)
                        ->where('LocationCode','=', $LocationCode)
                        ->get();          
        }else{
            $dataIN      = CloudDtTrnIn::select(DB::raw("SUM(QtyIN*FactorQty) AS QtyIN"))
                        ->where('ItemCode','=',$ItemCode)
                        ->where('SessionID','=', $SessionID)
                        ->get();
            $dataOUT     = CloudDtTrnOut::select(DB::raw("SUM(QtyOUT*FactorQty) AS QtyOUT"))
                        ->where('VoidYN','=','0')
                        ->where('ItemCode','=',$ItemCode)
                        ->where('SessionID','=', $SessionID)
                        ->get();                 

        }
        $QtyIN          = $dataIN[0]->QtyIN;
        $QtyOUT         = $dataOUT[0]->QtyOUT;
        $OnHand         = $QtyIN-$QtyOUT;
        $dataOnHand     = array("data"=> array("OnHand" => $OnHand));
        return ResponseFormatter::success($dataOnHand,"success");
        //dd($OnHand);

	
	   
	    //$vExecute = mysqli_query($conn, $StrSQL);
	    //$tOUT = mysqli_fetch_array($vExecute);

    }
}
