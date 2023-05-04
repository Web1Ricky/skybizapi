<?php
namespace App\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CloudDtTrnIn;
use App\Models\CloudDtTrnOut;

class InsertDetailTrn{

    public static function setDetail($params)
    {
        //$request->session()->token();
        $SessionID      = $params['SessionID'];
        $arrItemCode    = $params['arrItemCode'];
        $DateFrom       = $params['DateFrom'];
        $DateTo         = $params['DateTo'];
        $DatabaseName   = $params['DatabaseName'];
        $arrLocationCode= $params['arrLocationCode'];

        $strSQL         = "UPDATE stk_grndo_hd SET DocType = 'GRNDO' WHERE DocType = '' ";
        $vExecute       = DB::connection('mysqldynamic')->statement($strSQL);

        // Delete Cloud
        $vExecute       = DB::connection('mysqldynamic')->statement("DELETE FROM cloud_detail_trn_in WHERE SessionID = '$SessionID'");
        //dd($vExecute);
        $vExecute       = DB::connection('mysqldynamic')->statement("DELETE FROM cloud_detail_trn_in WHERE ProcessTimeStamp < NOW() - INTERVAL 2 DAY");
        $vExecute       = DB::connection('mysqldynamic')->statement("DELETE FROM cloud_detail_trn_out WHERE SessionID = '$SessionID'");
        $vExecute       = DB::connection('mysqldynamic')->statement("DELETE FROM cloud_detail_trn_out WHERE ProcessTimeStamp < NOW() - INTERVAL 2 DAY");

        $WhereItemCode = "";
        if(!empty($arrItemCode)){
            $qInItemCode    = join("','", $arrItemCode);
            $WhereItemCode  = "AND D.ItemCode IN ('$qInItemCode')";
        }
         // dd($WhereItemCode);
        $WhereDateTo    = "";
        $WhereDateToH   = "";
        if(!empty($DateTo)){
            $WhereDateTo        = "AND D_ate <= '$DateTo'";
            $WhereDateToH       = "AND H.D_ate <= '$DateTo'";
        }
     
        $WhereLocationCode    = "";
        $WhereLocationCodeTo  = "";
        if(count($arrLocationCode)>0){
            $qInLocationCode        = join("','", $arrLocationCode);
            $WhereLocationCode      = "AND D.LocationCode IN ('$qInLocationCode')";
            $WhereLocationCodeTo    = "AND D.LocationCodeTo IN ('$qInLocationCode')";
        }

        // STK_MASTER_BALANCE
	    $StrSQL             = "SELECT StockOBDate FROM sys_general_setup";
	    $vExecute           = DB::connection('mysqldynamic')
                            ->table('sys_general_setup')
				            ->select(['StockOBDate'])
                            ->first();
        $StockOBDate	    = $vExecute->StockOBDate;
	    $StockOBDate        = ($StockOBDate==""?"0000-00-00":$StockOBDate);
	    $DateFrom           = ( empty($DateFrom) ? "0000-00-00" : date('Y-m-d', strtotime(strtr($DateFrom, '/', '-'))) );

        $WhereDateFrom      = "AND D_ate > '$StockOBDate'";
        $WhereDateFromH     = "AND H.D_ate > '$StockOBDate'";

        $arrColumTrnIn      = ['ItemCode','Doc1No','Doc2No',
                            'Doc3No', 'D_ate', 'BookDate',
                            'ExpireDate', 'QtyIN', 'FactorQty',
                            'UOM', 'UnitCost', 'MovingAveCost',
                            'LandingCost', 'OCCost', 'HCTax',
                            'CusCode', 'DocType1', 'DocType2',
                            'DocType3', 'Doc1NoRunNo', 'Doc2NoRunNo',
                            'Doc3NoRunNo', 'GlobalTaxCode', 'DetailTaxCode',
                            'SessionID', 'ItemBatch', 'LocationCode',
                            'SalesPersonCode', 'DepartmentCode', 'ProjectCode',
                            'BranchCode', 'AnalysisCode1', 'AnalysisCode2',
                            'AnalysisCode3', 'DatabaseName'];
                           
        $arrColumTrnOut     = ['ItemCode', 'Doc1No', 'Doc2No', 
                            'Doc3No', 'D_ate', 'BookDate',
                            'QtyOUT', 'FactorQty', 'UOM', 
                            'UnitPrice', 'HCTax', 'CusCode', 
                            'DocType1', 'DocType2', 'DocType3',
                            'Doc1NoRunNo', 'Doc2NoRunNo', 'Doc3NoRunNo', 
                            'VoidYN', 'GlobalTaxCode', 'DetailTaxCode', 
                            'SessionID',  'ItemBatch', 'LocationCode', 
                            'SalesPersonCode', 'DepartmentCode', 'ProjectCode', 
                            'BranchCode', 'AnalysisCode1', 'AnalysisCode2', 
                            'AnalysisCode3', 'DatabaseName'];                    
        // START IN
        $StrSQL             = "INSERT INTO cloud_detail_trn_in (ItemCode, Doc1No, Doc2No, 
                            Doc3No, D_ate, BookDate, 
                            ExpireDate, QtyIN, FactorQty, 
                            UOM, UnitCost, MovingAveCost, 
                            LandingCost, OCCost, HCTax,
							CusCode, DocType1, DocType2, 
                            DocType3, Doc1NoRunNo, Doc2NoRunNo, 
                            Doc3NoRunNo, GlobalTaxCode, DetailTaxCode, 
                            SessionID, ItemBatch, LocationCode, 
                            SalesPersonCode, DepartmentCode, ProjectCode, 
                            BranchCode, AnalysisCode1, AnalysisCode2, 
                            AnalysisCode3, DatabaseName)
		         		    SELECT 	ItemCode, Doc1No, Doc2No, 
                            Doc3No, D_ate, BookDate, 
                            ExpireDate, QtyIN, FactorQty, 
                            UOM, UnitCost, MovingAveCost, 
                            LandingCost, OCCost, HCTax,
							CusCode, DocType1, DocType2, 
                            DocType3, Doc1NoRunNo, Doc2NoRunNo, 
                            Doc3NoRunNo, GlobalTaxCode, DetailTaxCode, 
                            '$SessionID', ItemBatch, LocationCode, 
                            SalesPersonCode, DepartmentCode, ProjectCode, 
                            BranchCode, AnalysisCode1, AnalysisCode2, 
                            AnalysisCode3, '$DatabaseName' 
							FROM stk_master_balance AS D
							WHERE ItemCode <> '' $WhereItemCode $WhereLocationCode ";
        $StmtIn             = DB::connection('mysqldynamic')->statement($StrSQL);
	    // stk_grndo_dt                    
        $StrSQL             = "INSERT INTO cloud_detail_trn_in (ItemCode, Doc1No, Doc2No, 
                            Doc3No, D_ate, BookDate, 
                            ExpireDate, QtyIN, FactorQty, 
                            UOM, UnitCost, MovingAveCost,
                            LandingCost, OCCost, HCTax,
                            CusCode, DocType1, DocType2, 
                            DocType3, Doc1NoRunNo, Doc2NoRunNo, 
                            Doc3NoRunNo, GlobalTaxCode, DetailTaxCode, 
                            SessionID, ItemBatch, LocationCode, 
                            SalesPersonCode, DepartmentCode, ProjectCode, 
                            BranchCode, AnalysisCode1, AnalysisCode2, 
                            AnalysisCode3, DatabaseName)
                            SELECT  D.ItemCode, '', D.Doc1No, 
                            '', H.D_ate, H.D_ate, 
                            D.WarrantyDate, D.Qty, D.FactorQty, 
                            D.UOMSingular, H.CurRate1 * (D.HCLineAmt / (D.Qty)), 0, 
                            0, 0, H.CurRate1 * (D.HCTax/(D.Qty * D.FactorQty)),
                            H.CusCode, IF(D.PORunNo > 0,'PO', ''), 'GRNDO', 
                            '', D.PORunNo, D.RunNo,
                            0, H.GlobalTaxCode, D.DetailTaxCode, 
                            '$SessionID', D.ItemBatch, D.LocationCode, 
                            '', D.DepartmentCode, D.ProjectCode, 
                            D.BranchCode, D.AnalysisCode1, D.AnalysisCode2, 
                            D.AnalysisCode3, '$DatabaseName' 
                            FROM stk_grndo_dt D INNER JOIN stk_grndo_hd H ON D.Doc1No = H.Doc1No
                            WHERE D.ItemCode <> '' AND D.BlankLine = '0' AND D.Qty <> 0 AND D.FactorQty <> 0
                            AND H.DocType IN ('GRNDO')
                            $WhereItemCode $WhereDateFrom $WhereDateTo $WhereLocationCode
                            ORDER BY D.RunNo";
        $StmtIn             = DB::connection('mysqldynamic')->statement($StrSQL);

        $StrSQLUpdate       = "UPDATE cloud_detail_trn_in tIN 
                            INNER JOIN stk_grndo_dt D ON tIN.Doc2NoRunNo = D.RunNo
                            INNER JOIN stk_grndo_hd H ON D.Doc1No = H.Doc1No
                            INNER JOIN stk_tax TAX ON H.GlobalTaxCode = TAX.TaxCode
                            SET tIN.LocationCode = D.LocationCode, 
                                tIN.ItemBatch = D.ItemBatch, 
                                tIN.HCTax = IF(TAX.TaxType IN ('0', '2'), 
                                (((D.HCUnitCost - (D.HCDiscount/IF(D.Qty = 0, 1, D.Qty)))/(IF(D.FactorQty = 0, 1, D.FactorQty)))/(100+H.GbTaxRate1))*H.GbTaxRate1*H.CurRate1,
                                ((D.HCUnitCost - (D.HCDiscount/IF(D.Qty = 0, 1, D.Qty)))/(IF(D.FactorQty = 0, 1, D.FactorQty)))*(H.GbTaxRate1/100)*H.CurRate1
                                ),
                                tIN.UnitCost = IF(TAX.TaxType IN ('0', '2'), 
                                    tIN.UnitCost,
                                    tIN.UnitCost + ( (((D.HCUnitCost - (D.HCDiscount/IF(D.Qty = 0, 1, D.Qty)))/(IF(D.FactorQty = 0, 1, D.FactorQty)))*(H.GbTaxRate1/100)*H.CurRate1) * tIN.FactorQty)
                                    )
                            WHERE H.GlobalTaxCode <> '' AND H.GbTaxRate1 > 0
                            AND tIN.DocType2 IN ('GRNDO') AND tIN.SessionID = '$SessionID'
                            $WhereItemCode $WhereDateFromH $WhereDateToH $WhereLocationCode";
        $StmtUpdate        = DB::connection('mysqldynamic')->statement($StrSQLUpdate);

        // Update Doc1No
        $StrSQLUpdate2       = "UPDATE cloud_detail_trn_in tIN INNER JOIN stk_pur_order_dt D ON tIN.Doc1NoRUnNo = D.RunNo 
                            SET tIN.Doc1No = D.Doc1No
                            WHERE tIN.Doc1NoRUnNo <> 0 AND tIN.DocType2 = 'GRNDO' AND tIN.SessionID = '$SessionID'";
        $StmtUpdate2         = DB::connection('mysqldynamic')->statement($StrSQLUpdate2); 

        // stk_sup_inv_dt
        $StrSQL             = "INSERT INTO cloud_detail_trn_in (ItemCode, Doc1No, Doc2No, 
                            Doc3No, D_ate, BookDate, 
                            ExpireDate, QtyIN, FactorQty, 
                            UOM, UnitCost, MovingAveCost, 
                            LandingCost, OCCost, HCTax,
                            CusCode, DocType1, DocType2, 
                            DocType3, Doc1NoRunNo, Doc2NoRunNo, 
                            Doc3NoRunNo, GlobalTaxCode, DetailTaxCode, 
                            SessionID, ItemBatch, LocationCode, 
                            SalesPersonCode, DepartmentCode, ProjectCode, 
                            BranchCode, AnalysisCode1, AnalysisCode2, 
                            AnalysisCode3, DatabaseName)
                            SELECT 	D.ItemCode, '', D.GRNNo, 
                            D.Doc1No, H.D_ate, H.D_ate, 
                            D.WarrantyDate, D.Qty, D.FactorQty, 
                            D.UOMSingular, H.CurRate1 * (D.HCLineAmt / (D.Qty)), 0, 
                            0, 0, H.CurRate1 * (D.HCTax/(D.Qty * D.FactorQty)),
                            H.CusCode, IF(D.PORunNo > 0,'PO', ''), IF(D.GRNRunNo > 0,'GRNDO', ''), 
                            H.DocType, D.PORunNo, D.GRNRunNo, 
                            D.RunNo, H.GlobalTaxCode, D.DetailTaxCode, 
                            '$SessionID', D.ItemBatch, D.LocationCode, 
                            '', D.DepartmentCode, D.ProjectCode, 
                            D.BranchCode, D.AnalysisCode1, D.AnalysisCode2, 
                            D.AnalysisCode3, '$DatabaseName'
                            FROM stk_sup_inv_dt D INNER JOIN stk_sup_inv_hd H ON D.Doc1No = H.Doc1No
                            WHERE D.ItemCode <> '' AND D.BlankLine = '0' AND D.Qty <> 0 AND D.FactorQty <> 0
                            AND H.DocType IN ('SupInv', 'SupDN')
                            $WhereItemCode $WhereDateFrom $WhereDateTo $WhereLocationCode
                            ORDER BY D.RunNo";                    
        $StmtIn             = DB::connection('mysqldynamic')->statement($StrSQL); 

        // Update Global Tax
        $StrSQLUpdate       = "UPDATE cloud_detail_trn_in tIN 
                            INNER JOIN stk_sup_inv_dt D ON tIN.Doc3NoRunNo = D.RunNo
                            INNER JOIN stk_sup_inv_hd H ON D.Doc1No = H.Doc1No
                            INNER JOIN stk_tax TAX ON H.GlobalTaxCode = TAX.TaxCode
                            SET tIN.LocationCode = D.LocationCode, 
                                tIN.ItemBatch = D.ItemBatch, 
                                tIN.HCTax = IF(TAX.TaxType IN ('0', '2'), 
                                (((D.HCUnitCost - (D.HCDiscount/IF(D.Qty = 0, 1, D.Qty)))/(IF(D.FactorQty = 0, 1, D.FactorQty)))/(100+H.GbTaxRate1))*H.GbTaxRate1*H.CurRate1,
                                ((D.HCUnitCost - (D.HCDiscount/IF(D.Qty = 0, 1, D.Qty)))/(IF(D.FactorQty = 0, 1, D.FactorQty)))*(H.GbTaxRate1/100)*H.CurRate1
                                ),
                                tIN.UnitCost = IF(TAX.TaxType IN ('0', '2'), 
                                    tIN.UnitCost,
                                    tIN.UnitCost + ( (((D.HCUnitCost - (D.HCDiscount/IF(D.Qty = 0, 1, D.Qty)))/(IF(D.FactorQty = 0, 1, D.FactorQty)))*(H.GbTaxRate1/100)*H.CurRate1) * tIN.FactorQty)
                                    )
                            WHERE H.GlobalTaxCode <> '' AND H.GbTaxRate1 > 0
                            AND tIN.DocType3 IN ('SupInv', 'SupDN') AND tIN.SessionID = '$SessionID'
                            $WhereItemCode $WhereDateFromH $WhereDateToH $WhereLocationCode";
        $StmtUpdate         = DB::connection('mysqldynamic')->statement($StrSQLUpdate);  
        
        //GRNDO ----------------------------------------------------------------------------------------------------------------------------------
        if(count($arrLocationCode)>0){
            $SelectBinGRN       = DB::connection('mysqldynamic')->table('stk_sup_inv_dt as D')
                                ->join('stk_sup_inv_hd as H', 'H.Doc1No', '=', 'D.Doc1No')
                                ->select('GRNRunNo')
                                ->where('H.DocType','=', 'SupInv')
                                ->where('D.ItemCode', '=', $arrItemCode)
                                ->where('D.LocationCode', '=', $arrLocationCode)
                                ->whereBetween('H.D_ate', [$DateFrom, $DateTo]);    
        }else{
            $SelectBinGRN       = DB::connection('mysqldynamic')->table('stk_sup_inv_dt as D')
                                ->join('stk_sup_inv_hd as H', 'H.Doc1No', '=', 'D.Doc1No')
                                ->select('GRNRunNo')
                                ->where('H.DocType','=', 'SupInv')
                                ->where('D.ItemCode', '=', $arrItemCode)
                                ->whereBetween('H.D_ate', [$DateFrom, $DateTo]);    
        }
        $GRNCount           = $SelectBinGRN->count();
        /*Products::whereIn('id', function($query){
            $query->select('paper_type_id')
            ->from(with(new ProductCategory)->getTable())
            ->whereIn('category_id', ['223', '15'])
            ->where('active', 1);
        })->get();*/
        if($GRNCount>0){
            //Delete data From GRNDO
            $DeleteGRN      = CloudDtTrnIn::where('DocType2', 'GRNDO')
                            ->where('SessionID', $SessionID)
                            ->where('Doc3NoRunNo', 0)
                            ->whereIn('Doc2NoRunNo',$SelectBinGRN)
                            ->delete();
            //Get Trn balance
            $SelectTrn      = DB::connection('mysqldynamic')->table('cloud_detail_trn_in as tIN')
                            ->select('DODt.RunNo', 
                            'DODt.Doc1No', 
                            'DODt.Qty',
                            'tIN.QtyIN')
                            ->join('stk_grndo_dt as DODt', 'DODt.RunNo', '=', 'tIN.Doc2NoRunNo')
                            ->where('SessionID', $SessionID)
                            ->where('DocType3', 'SupInv')
                            ->whereIn('DODt.RunNo',$SelectBinGRN)
                            ->whereIn('tIN.Doc2NoRunNo',$SelectBinGRN)
                            ->groupBy('tIN.Doc2NoRunNo')
                            ->havingRaw('DODt.Qty > SUM(tIN.QtyIN)')
                            ->get();
            foreach($SelectTrn as $dataBal) {   
                $vRunNoGRNDO        = $dataBal['RunNo'];
                $vQtyGRNDO          = $dataBal['Qty'] - $dataBal['QtyIN'];
                $GRNDoc1No          = $dataBal['Doc1No'];

                $strSQLGRNDODt      = "INSERT INTO cloud_detail_trn_in (ItemCode, Doc1No, Doc2No, 
                                    Doc3No, D_ate, BookDate, 
                                    ExpireDate, QtyIN, FactorQty, 
                                    UOM, UnitCost, MovingAveCost, 
                                    LandingCost, OCCost, HCTax,
                                    CusCode, DocType1, DocType2, 
                                    DocType3, Doc1NoRunNo, Doc2NoRunNo, 
                                    Doc3NoRunNo, GlobalTaxCode, DetailTaxCode, 
                                    SessionID, ItemBatch, LocationCode, 
                                    SalesPersonCode, DepartmentCode, ProjectCode, 
                                    BranchCode, AnalysisCode1, AnalysisCode2, 
                                    AnalysisCode3, DatabaseName)
                                    SELECT  D.ItemCode, '', D.Doc1No, 
                                    '', H.D_ate, H.D_ate, 
                                    D.WarrantyDate, '$vQtyGRNDO', D.FactorQty, 
                                    D.UOMSingular, H.CurRate1 * (D.HCLineAmt / ('$vQtyGRNDO')), 0, 
                                    0, 0, H.CurRate1 * (D.HCTax/('$vQtyGRNDO' * D.FactorQty)),
                                    H.CusCode, IF(D.PORunNo > 0,'PO', ''), 'GRNDO', 
                                    '', D.PORunNo, D.RunNo, 
                                    0, H.GlobalTaxCode, D.DetailTaxCode, 
                                    '$SessionID', D.ItemBatch, D.LocationCode, 
                                    '', D.DepartmentCode, D.ProjectCode, 
                                    D.BranchCode, D.AnalysisCode1, D.AnalysisCode2, 
                                    D.AnalysisCode3, '$DatabaseName'
                                    FROM stk_grndo_dt D INNER JOIN stk_grndo_hd H ON D.Doc1No = H.Doc1No
                                    WHERE D.RunNo  = '$vRunNoGRNDO'
                                    AND D.ItemCode <> '' AND D.BlankLine = '0' AND D.FactorQty <> 0 
                                    ORDER BY D.RunNo";
                $StmtInDt           = DB::connection('mysqldynamic')->statement($strSQLGRNDODt);
                // Update Global Tax
                $vStrSQL            = "UPDATE cloud_detail_trn_in tIN 
                                    INNER JOIN stk_grndo_dt D ON tIN.Doc2NoRunNo = D.RunNo
                                    INNER JOIN stk_grndo_hd H ON D.Doc1No = H.Doc1No
                                    INNER JOIN stk_tax TAX ON H.GlobalTaxCode = TAX.TaxCode
                                    SET tIN.LocationCode = D.LocationCode, 
                                        tIN.ItemBatch = D.ItemBatch, 
                                        tIN.HCTax = IF(TAX.TaxType IN ('0', '2'), 
                                        (((D.HCUnitCost - (D.HCDiscount/IF(D.Qty = 0, 1, D.Qty)))/(IF(D.FactorQty = 0, 1, D.FactorQty)))/(100+H.GbTaxRate1))*H.GbTaxRate1*H.CurRate1,
                                        ((D.HCUnitCost - (D.HCDiscount/IF(D.Qty = 0, 1, D.Qty)))/(IF(D.FactorQty = 0, 1, D.FactorQty)))*(H.GbTaxRate1/100)*H.CurRate1
                                        ),
                                        tIN.UnitCost = IF(TAX.TaxType IN ('0', '2'), 
                                            tIN.UnitCost,
                                            tIN.UnitCost + ( (((D.HCUnitCost - (D.HCDiscount/IF(D.Qty = 0, 1, D.Qty)))/(IF(D.FactorQty = 0, 1, D.FactorQty)))*(H.GbTaxRate1/100)*H.CurRate1) * tIN.FactorQty)
                                            )
                                    WHERE H.GlobalTaxCode <> '' AND H.GbTaxRate1 > 0
                                    AND H.Doc1No = '$GRNDoc1No' AND D.Doc1No = '$GRNDoc1No' AND tIN.SessionID = '$SessionID'";                    
                $StmtUpdate         = DB::connection('mysqldynamic')->statement($vStrSQL);  
            }
            //Update D_ate trn_in
            $UpdateTrnIn        = CloudDtTrnIn::join('stk_grndo_dt as D', 'D.RunNo', '=', 'cloud_detail_trn_in.Doc2NoRunNo')
                                ->join('stk_grndo_hd as Hd', 'Hd.Doc1No', '=', 'D.Doc1No')
                                ->where('cloud_detail_trn_in.DocType2', '=', 'GRNDO')
                                ->where('cloud_detail_trn_in.SessionID', '=', $SessionID)
                                ->whereIn('cloud_detail_trn_in.Doc2NoRunNo', $SelectBinGRN)
                                ->update(['cloud_detail_trn_in.D_ate' => DB::raw('`Hd`.`D_ate`')]);
                   
        }
        //Update Doc1No
        /*$vStrSQL          = "UPDATE cloud_detail_trn_in tIN
                            INNER JOIN stk_grndo_dt GRNDO ON tIN.Doc2NoRUnNo = GRNDO.RunNo
                            INNER JOIN stk_pur_order_dt PO ON (GRNDO.PORunNo = PO.RunNo)
                            SET tIN.Doc1NoRUnNo = PO.RunNo, 
                                tIN.Doc1No = PO.Doc1No, 
                                tIN.DocType1 = 'PO'
                            WHERE tIN.Doc2NoRUnNo <> 0 AND GRNDO.PORunNo <> 0 
                            AND tIN.SessionID = '$SessionID'";  */               
        $UpdateTrnIn        = CloudDtTrnIn::join('stk_grndo_dt as D', 'D.RunNo', '=', 'cloud_detail_trn_in.Doc2NoRunNo')
                            ->join('stk_pur_order_dt as PO', 'PO.RunNo', '=', 'D.PORunNo')
                            ->where('cloud_detail_trn_in.Doc2NoRUnNo', '<>','0')
                            ->where('D.PORunNo', '<>','0')
                            ->where('SessionID', '=',  $SessionID)
                            ->update([
                            'cloud_detail_trn_in.Doc1NoRUnNo' => 'PO.RunNo',
                            'cloud_detail_trn_in.Doc1No' => 'PO.Doc1No',
                            'cloud_detail_trn_in.DocType1' => 'PO',
                            ]); 
        //----------------------------------------------------------------------------------------------------------------------------------
       
        // stk_inventory_dt RS SA    
        $StrSQL         = "INSERT INTO cloud_detail_trn_in (ItemCode, Doc1No, Doc2No, 
                            Doc3No, D_ate, BookDate, 
                            ExpireDate, QtyIN, FactorQty, 
                            UOM, UnitCost, MovingAveCost, 
                            LandingCost, OCCost, HCTax,
                            CusCode, DocType1, DocType2, 
                            DocType3, Doc1NoRunNo, Doc2NoRunNo, 
                            Doc3NoRunNo, GlobalTaxCode, DetailTaxCode, 
                            SessionID, ItemBatch, LocationCode, 
                            SalesPersonCode, DepartmentCode, ProjectCode, 
                            BranchCode, AnalysisCode1, AnalysisCode2, 
                            AnalysisCode3, DatabaseName)
							SELECT D.ItemCode, '', '', D.Doc1No, H.D_ate, H.D_ate, D.WarrantyDate,
							D.QtyIN, D.FactorQty, D.UOMSingular, H.CurRate1 * (D.HCUnitCost - (D.HCDiscount/D.QtyIN)), 0, 0, 0, H.CurRate1 * (D.HCTax + (H.HCGbTax/(D.QtyIN * D.FactorQty))),
							H.CusCode, '', '', H.DocType, 0, 0, D.RunNo, '', '', '$SessionID',
							D.ItemBatch, D.LocationCode, D.SalesPersonCode, D.DepartmentCode, D.ProjectCode, D.BranchCode,
							D.AnalysisCode1, D.AnalysisCode2, D.AnalysisCode3, '$DatabaseName'
							FROM stk_inventory_dt D INNER JOIN stk_inventory_hd H ON D.Doc1No = H.Doc1No
          		            WHERE D.ItemCode <> '' AND D.BlankLine = '0' AND D.QtyIN <> 0 AND D.FactorQty <> 0 
          		            AND H.DocType IN ('RS', 'SA', 'pd_Assembly', 'MatRet', 'ProdReceipt')
			                $WhereItemCode $WhereDateFrom $WhereDateTo $WhereLocationCode
			                ORDER BY D.RunNo";
        $StmtIn             = DB::connection('mysqldynamic')->statement($StrSQL); 
   
        // stk_inventory_dt TS         
        $StrSQL             ="INSERT INTO cloud_detail_trn_in (ItemCode, Doc1No, Doc2No, 
                            Doc3No, D_ate, BookDate, 
                            ExpireDate, QtyIN, FactorQty, 
                            UOM, UnitCost, MovingAveCost, 
                            LandingCost, OCCost, HCTax,
                            CusCode, DocType1, DocType2, 
                            DocType3, Doc1NoRunNo, Doc2NoRunNo, 
                            Doc3NoRunNo, GlobalTaxCode, DetailTaxCode, 
                            SessionID, ItemBatch, LocationCode, 
                            SalesPersonCode, DepartmentCode, ProjectCode, 
                            BranchCode, AnalysisCode1, AnalysisCode2, 
                            AnalysisCode3, DatabaseName)
                            SELECT D.ItemCode, '', '', 
                            D.Doc1No, H.D_ate, H.D_ate, 
                            D.WarrantyDate, D.QtyIN, D.FactorQty, 
                            D.UOMSingular, H.CurRate1 * (D.HCUnitCost - (D.HCDiscount/D.QtyIN)), 0, 
                            0, 0, H.CurRate1 * (D.HCTax + (H.HCGbTax/(D.QtyIN * D.FactorQty))),
                            H.CusCode, '', '', 
                            H.DocType, 0, 0, 
                            D.RunNo, '', '', 
                            '$SessionID', D.ItemBatch, D.LocationCodeTo, 
                            D.SalesPersonCode, D.DepartmentCode, D.ProjectCode, 
                            D.BranchCode, D.AnalysisCode1, D.AnalysisCode2, 
                            D.AnalysisCode3, '$DatabaseName'
                            FROM stk_inventory_dt D INNER JOIN stk_inventory_hd H ON D.Doc1No = H.Doc1No
                            WHERE D.ItemCode <> '' AND D.BlankLine = '0' AND D.QtyIN <> 0 AND D.FactorQty <> 0 
                            AND H.DocType IN ('TS')
                            $WhereItemCode $WhereDateFrom $WhereDateTo $WhereLocationCodeTo
                            ORDER BY D.RunNo  ";   
        $StmtIn             = DB::connection('mysqldynamic')->statement($StrSQL);

        // stk_inventory_dt FIFO 
        $StrSQL             = "INSERT INTO cloud_detail_trn_in (ItemCode, Doc1No, Doc2No, 
                            Doc3No, D_ate, BookDate, 
                            ExpireDate, QtyIN, FactorQty, 
                            UOM, UnitCost, MovingAveCost, 
                            LandingCost, OCCost, HCTax,
							CusCode, DocType1, DocType2, 
                            DocType3, Doc1NoRunNo, Doc2NoRunNo, 
                            Doc3NoRunNo, GlobalTaxCode, DetailTaxCode, 
                            SessionID, ItemBatch, LocationCode, 
                            SalesPersonCode, DepartmentCode, ProjectCode, 
                            BranchCode, AnalysisCode1, AnalysisCode2, 
                            AnalysisCode3, DatabaseName)
	         		        SELECT 	D.ItemCode, '', '', 
                            D.Doc1No, D.D_ate, D.D_ate, 
                            D.WarrantyDate, D.Qty, D.FactorQty, 
                            D.UOMSingular, HCUnitCost, 0, 
                            0, 0, 0,
							'', '', '', 
                            'OS-FIFO', 0, 0, 
                            D.RunNo, '', '', 
                            '$SessionID', D.ItemBatch, D.LocationCode, 
                            '', '', '', 
                            '', '', '', 
                            '', '$DatabaseName'
							FROM stk_inventory_fifo D
							WHERE D.ItemCode <> '' AND D.Qty <> 0 AND D.FactorQty <> 0 
							$WhereItemCode $WhereDateFrom $WhereDateTo $WhereLocationCode
							ORDER BY D.RunNo ";
        $StmtIn             = DB::connection('mysqldynamic')->statement($StrSQL);
        //stk_cus_inv_dt
        $StrSQL         ="INSERT INTO cloud_detail_trn_in (ItemCode, Doc1No, Doc2No,
                        Doc3No, D_ate, BookDate,
                        QtyIN, FactorQty, UOM, 
                        UnitCost, HCTax, CusCode, 
                        DocType1, DocType2, DocType3, 
                        Doc1NoRunNo, Doc2NoRunNo, Doc3NoRunNo, 
                        GlobalTaxCode, DetailTaxCode, SessionID, 
                        ItemBatch, LocationCode, SalesPersonCode, 
                        DepartmentCode, ProjectCode, BranchCode,
                        AnalysisCode1, AnalysisCode2, AnalysisCode3, 
                        DatabaseName)
	                    SELECT D.ItemCode, '', '', 
                        D.Doc1No, H.D_ate, H.D_ate,
	                    D.Qty, D.FactorQty, D.UOMSingular,
                         H.CurRate1 * (D.HCUnitCost - (D.HCDiscount/D.Qty)), H.CurRate1 * (D.HCTax + (H.HCGbTax/(D.Qty * D.FactorQty))), H.CusCode,
                        '', '', H.DocType, 
                        0, 0, D.RunNo, 
	                    H.GlobalTaxCode, D.DetailTaxCode, '$SessionID',
	                    D.ItemBatch, D.LocationCode, D.SalesPersonCode, 
                        D.DepartmentCode, D.ProjectCode, D.BranchCode,
						D.AnalysisCode1, D.AnalysisCode2, D.AnalysisCode3, 
                        '$DatabaseName'
                        FROM stk_cus_inv_dt D INNER JOIN stk_cus_inv_hd H ON D.Doc1No = H.Doc1No
                        WHERE D.ItemCode <> '' AND D.BlankLine = '0' AND D.Qty <> 0 AND D.FactorQty <> 0 AND Status2 = ''
                        AND H.DocType IN ('CusCN')
                        $WhereItemCode $WhereDateFrom $WhereDateTo $WhereLocationCode
                        ORDER BY D.RunNo ";
        $StmtIn       =DB::connection('mysqldynamic')->statement($StrSQL);
        
        // stk_detail_trn_in
        $StrSQL      = "INSERT INTO cloud_detail_trn_in (ItemCode, Doc1No, Doc2No, 
                            Doc3No, D_ate, BookDate, 
                            ExpireDate, QtyIN, FactorQty, 
                            UOM, UnitCost, MovingAveCost, 
                            LandingCost, OCCost, HCTax,
							CusCode, DocType1, DocType2, 
                            DocType3, Doc1NoRunNo, Doc2NoRunNo, 
                            Doc3NoRunNo, GlobalTaxCode, DetailTaxCode, 
                            SessionID, ItemBatch, LocationCode, 
                            SalesPersonCode, DepartmentCode, ProjectCode, 
                            BranchCode, AnalysisCode1, AnalysisCode2, 
                            AnalysisCode3, DatabaseName)
                            SELECT 	ItemCode, Doc1No, Doc2No, 
                            Doc3No, D_ate, BookDate, 
                            ExpireDate, QtyIN, FactorQty, 
                            UOM, UnitCost, MovingAveCost, 
                            LandingCost, OCCost, HCTax,
                            CusCode, DocType1, DocType2, 
                            DocType3, Doc1NoRunNo, Doc2NoRunNo, 
                            Doc3NoRunNo, '', '', 
                            '$SessionID', D.ItemBatch, D.LocationCode, 
                            '', '', '', 
                            '', '', '', 
                            '', '$DatabaseName'
                            FROM stk_detail_trn_in D
                            WHERE DocType3 IN ('PSC', 'OS-NSI')
                            $WhereItemCode $WhereDateFrom $WhereDateTo $WhereLocationCode
                            ORDER BY D.RunNo";
        $StmtIn             =DB::connection('mysqldynamic')->statement($StrSQL);
        //END IN 
                   
        //---START OUT---

        // stk_do_dt
        $StrSQL           = "INSERT INTO cloud_detail_trn_out (ItemCode, Doc1No, Doc2No, 
                            Doc3No, D_ate, BookDate,
                            QtyOUT, FactorQty, UOM, 
                            UnitPrice, HCTax, CusCode, 
                            DocType1, DocType2, DocType3, 
                            Doc1NoRunNo, Doc2NoRunNo, Doc3NoRunNo, 
                            VoidYN, GlobalTaxCode, DetailTaxCode, 
                            SessionID, ItemBatch, LocationCode, 
                            SalesPersonCode, DepartmentCode, ProjectCode, 
                            BranchCode, AnalysisCode1, AnalysisCode2, 
                            AnalysisCode3, DatabaseName)
                            SELECT D.ItemCode, '', D.Doc1No, 
                            '', H.D_ate, H.D_ate,
                            D.Qty, D.FactorQty, D.UOMSingular, 
                            H.CurRate1 * (D.HCLineAmt / (D.Qty)), H.CurRate1 * (D.HCTax/(D.Qty * D.FactorQty)), H.CusCode, 
                            '', H.DocType, '', 
                            0, D.RunNo, 0,  
                            '0', H.GlobalTaxCode, D.DetailTaxCode, 
                            '$SessionID', D.ItemBatch, D.LocationCode, 
                            D.SalesPersonCode, D.DepartmentCode, D.ProjectCode, 
                            D.BranchCode, D.AnalysisCode1, D.AnalysisCode2, 
                            D.AnalysisCode3, '$DatabaseName'
                            FROM stk_do_dt D INNER JOIN stk_do_hd H ON D.Doc1No = H.Doc1No
                            WHERE D.ItemCode <> '' AND D.BlankLine = '0' AND D.Qty <> 0 AND D.FactorQty <> 0 
                            AND H.DocType IN ('DO')
                            $WhereItemCode $WhereDateFrom $WhereDateTo $WhereLocationCode
                            ORDER BY D.RunNo";
        $StmtIn             =DB::connection('mysqldynamic')->statement($StrSQL);

        //Update Doc1No
        $StrSQLUp           = "UPDATE cloud_detail_trn_out tOUT 
                            INNER JOIN stk_do_dt D ON tOUT.Doc2NoRunNo = D.RunNo
                            INNER JOIN stk_do_hd H ON D.Doc1No = H.Doc1No
                            INNER JOIN stk_tax TAX ON H.GlobalTaxCode = TAX.TaxCode
                            SET tOUT.LocationCode = D.LocationCode, 
                                tOUT.ItemBatch = D.ItemBatch, 
                                tOUT.HCTax = IF(TAX.TaxType IN ('0', '2'), 
                                (((D.HCUnitCost - (D.HCDiscount/IF(D.Qty = 0, 1, D.Qty)))/(IF(D.FactorQty = 0, 1, D.FactorQty)))/(100+H.GbTaxRate1))*H.GbTaxRate1*H.CurRate1,
                                ((D.HCUnitCost - (D.HCDiscount/IF(D.Qty = 0, 1, D.Qty)))/(IF(D.FactorQty = 0, 1, D.FactorQty)))*(H.GbTaxRate1/100)*H.CurRate1
                                ),
                                tOUT.UnitPrice = IF(TAX.TaxType IN ('0', '2'), 
                                tOUT.UnitPrice,
                                tOUT.UnitPrice + ( (((D.HCUnitCost - (D.HCDiscount/IF(D.Qty = 0, 1, D.Qty)))/(IF(D.FactorQty = 0, 1, D.FactorQty)))*(H.GbTaxRate1/100)*H.CurRate1) * tOUT.FactorQty)
                                    )
                            WHERE H.GlobalTaxCode <> '' AND H.GbTaxRate1 > 0
                            AND tOUT.DocType2 IN ('DO') AND tOUT.SessionID = '$SessionID'
                            $WhereItemCode $WhereDateFromH $WhereDateToH $WhereLocationCode";
        $StmtUp             = DB::connection('mysqldynamic')->statement($StrSQLUp);

        $StrSQLUp2          = "UPDATE cloud_detail_trn_out tOUT INNER JOIN stk_quotation_dt D ON tOUT.Doc1NoRUnNo = D.RunNo 
                            SET tOUT.Doc1No = D.Doc1No
                            WHERE tOUT.Doc1NoRUnNo <> 0 AND tOUT.DocType2 = 'DO' AND tOUT.SessionID = '$SessionID'";
        $StmtUp2            = DB::connection('mysqldynamic')->statement($StrSQLUp2);

        // stk_cus_inv_dt
        $StrSQL             = "INSERT INTO cloud_detail_trn_out (ItemCode, Doc1No, Doc2No, 
                            Doc3No, D_ate, BookDate,
                            QtyOUT, FactorQty, UOM, 
                            UnitPrice, HCTax, CusCode, 
                            DocType1, DocType2, DocType3, 
                            Doc1NoRunNo, Doc2NoRunNo, Doc3NoRunNo, 
                            VoidYN, GlobalTaxCode, DetailTaxCode, 
                            SessionID, ItemBatch, LocationCode, 
                            SalesPersonCode, DepartmentCode, ProjectCode, 
                            BranchCode, AnalysisCode1, AnalysisCode2, 
                            AnalysisCode3, DatabaseName)
                            SELECT D.ItemCode, '', D.DONo, 
                            D.Doc1No, H.D_ate, H.D_ate,
                            D.Qty, D.FactorQty, D.UOMSingular, 
                            H.CurRate1 * (D.HCLineAmt / (D.Qty)), H.CurRate1 * (D.HCTax/(D.Qty * D.FactorQty)), H.CusCode, 
                            '', IF(D.DoRunNo > 0,'DO', ''), H.DocType, 
                            0, D.DoRunNo, D.RunNo, 
                            '0', H.GlobalTaxCode, D.DetailTaxCode, 
                            '$SessionID', D.ItemBatch, D.LocationCode, 
                            D.SalesPersonCode, D.DepartmentCode, D.ProjectCode, 
                            D.BranchCode, D.AnalysisCode1, D.AnalysisCode2, 
                            D.AnalysisCode3, '$DatabaseName'
                            FROM stk_cus_inv_dt D INNER JOIN stk_cus_inv_hd H ON D.Doc1No = H.Doc1No
                            WHERE D.ItemCode <> '' AND D.BlankLine = '0' AND D.Qty <> 0 AND D.FactorQty <> 0 AND Status2 = ''
                            AND H.DocType IN ('CusInv', 'CS', 'CusDN')
                            $WhereItemCode $WhereDateFrom $WhereDateTo $WhereLocationCode
                            ORDER BY D.RunNo" ;                        
        $StmtIn             =DB::connection('mysqldynamic')->statement($StrSQL);

        $StrSQLUp           = "UPDATE cloud_detail_trn_out tOUT 
                            INNER JOIN stk_cus_inv_dt D ON tOUT.Doc3NoRunNo = D.RunNo
                            INNER JOIN stk_cus_inv_hd H ON D.Doc1No = H.Doc1No
                            INNER JOIN stk_tax TAX ON H.GlobalTaxCode = TAX.TaxCode
                            SET tOUT.LocationCode = D.LocationCode, 
                                tOUT.ItemBatch = D.ItemBatch, 
                                tOUT.HCTax = IF(TAX.TaxType IN ('0', '2'), 
                                (((D.HCUnitCost - (D.HCDiscount/IF(D.Qty = 0, 1, D.Qty)))/(IF(D.FactorQty = 0, 1, D.FactorQty)))/(100+H.GbTaxRate1))*H.GbTaxRate1*H.CurRate1,
                                ((D.HCUnitCost - (D.HCDiscount/IF(D.Qty = 0, 1, D.Qty)))/(IF(D.FactorQty = 0, 1, D.FactorQty)))*(H.GbTaxRate1/100)*H.CurRate1
                                ),
                                tOUT.UnitPrice = IF(TAX.TaxType IN ('0', '2'), 
                                    tOUT.UnitPrice,
                                    tOUT.UnitPrice + ( (((D.HCUnitCost - (D.HCDiscount/IF(D.Qty = 0, 1, D.Qty)))/(IF(D.FactorQty = 0, 1, D.FactorQty)))*(H.GbTaxRate1/100)*H.CurRate1) * tOUT.FactorQty)
                                    )
                            WHERE H.GlobalTaxCode <> '' AND H.GbTaxRate1 > 0
                            AND tOUT.DocType3 IN ('CusInv', 'CS', 'CusDN') AND tOUT.SessionID = '$SessionID'
                            $WhereItemCode $WhereDateFromH $WhereDateToH $WhereLocationCode";
        $StmtUp             =DB::connection('mysqldynamic')->statement($StrSQLUp);
        
        //DO CUSINV  ---------
        if(count($arrLocationCode)>0){
            $SelectDO       = DB::connection('mysqldynamic')->table('stk_cus_inv_dt as D')
                            ->join('stk_cus_inv_hd as H', 'H.Doc1No', '=', 'D.Doc1No')
                            ->select('DORunNo')
                            ->whereIn('H.DocType',['CusInv', 'CS', 'CusDN'])
                            ->where('D.ItemCode', '=', $arrItemCode)
                            ->where('D.LocationCode', '=', $arrLocationCode)
                            ->whereBetween('H.D_ate', [$DateFrom, $DateTo]);    
        }else{
            $SelectDO       = DB::connection('mysqldynamic')->table('stk_cus_inv_dt as D')
                            ->join('stk_cus_inv_hd as H', 'H.Doc1No', '=', 'D.Doc1No')
                            ->select('DORunNo')
                            ->whereIn('H.DocType',['CusInv', 'CS', 'CusDN'])
                            ->where('D.ItemCode', '=', $arrItemCode)
                            ->whereBetween('H.D_ate', [$DateFrom, $DateTo]);    
        } 
        $DOCount           = $SelectDO->count();
        if($DOCount>0){  
            $DeleteGRN      = CloudDtTrnOut::where('DocType2', 'DO')
                            ->where('SessionID', $SessionID)
                            ->where('Doc3NoRunNo', 0)
                            ->whereIn('Doc2NoRunNo',$SelectDO)
                            ->delete();
            //Get Trn balance
            $SelectTrn      = DB::connection('mysqldynamic')->table('cloud_detail_trn_out as tOUT')
                            ->select('DODt.RunNo', 
                            'DODt.Doc1No', 
                            'DODt.Qty',
                            'tOUT.QtyOUT')
                            ->join('stk_do_dt as DODt', 'DODt.RunNo', '=', 'tOUT.Doc2NoRunNo')
                            ->where('SessionID', $SessionID)
                            ->whereIn('DocType3', ['CusInv', 'CS', 'CusDN'])
                            ->whereIn('DODt.RunNo',$SelectDO)
                            ->whereIn('tOUT.Doc2NoRunNo',$SelectDO)
                            ->groupBy('tOUT.Doc2NoRunNo')
                            ->havingRaw('DODt.Qty > SUM(tOUT.QtyOUT)')
                            ->get();
            foreach($SelectTrn as $dataBal) {   
                $vRunNoDO           = $dataBal['RunNo'];
                $vQtyDO             = $dataBal['Qty'] - $dataBal['QtyOUT'];
                $DODoc1No           = $dataBal['Doc1No'];

                $strSQLDODt         = "INSERT INTO cloud_detail_trn_out (ItemCode, Doc1No, Doc2No,
                                    Doc3No, D_ate, BookDate,
                                    QtyOUT, FactorQty, UOM, 
                                    UnitPrice, HCTax, CusCode, 
                                    DocType1, DocType2, DocType3, 
                                    Doc1NoRunNo, Doc2NoRunNo, Doc3NoRunNo, 
                                    VoidYN, GlobalTaxCode, DetailTaxCode, 
                                    SessionID, ItemBatch, LocationCode, 
                                    SalesPersonCode, DepartmentCode, ProjectCode, 
                                    BranchCode, AnalysisCode1, AnalysisCode2, 
                                    AnalysisCode3, DatabaseName)
                                    SELECT D.ItemCode, '', D.Doc1No, 
                                    '', H.D_ate, H.D_ate,
                                    '$vQtyDO', D.FactorQty, D.UOMSingular, 
                                    H.CurRate1 * (D.HCLineAmt / ('$vQtyDO')), H.CurRate1 * (D.HCTax/('$vQtyDO' * D.FactorQty)), H.CusCode, 
                                    '', 'DO', '', 
                                    0, D.RunNo, 0,  
                                    '0', H.GlobalTaxCode, D.DetailTaxCode, 
                                    '$SessionID', D.ItemBatch, D.LocationCode, 
                                    D.SalesPersonCode, D.DepartmentCode, D.ProjectCode, 
                                    D.BranchCode, D.AnalysisCode1, D.AnalysisCode2, 
                                    D.AnalysisCode3, '$DatabaseName'
                                    FROM stk_do_dt D INNER JOIN stk_do_hd H ON D.Doc1No = H.Doc1No
                                    WHERE D.RunNo  = '$vRunNoDO'
                                    AND D.ItemCode <> '' AND D.BlankLine = '0' AND D.FactorQty <> 0";
                $StmtIn         = DB::connection('mysqldynamic')->statement($strSQLDODt);

                // Update Global Tax
                $vStrSQL            = "UPDATE cloud_detail_trn_out tOUT 
                                    INNER JOIN stk_do_dt D ON tOUT.Doc2NoRunNo = D.RunNo
                                    INNER JOIN stk_do_hd H ON D.Doc1No = H.Doc1No
                                    INNER JOIN stk_tax TAX ON H.GlobalTaxCode = TAX.TaxCode
                                    SET tOUT.LocationCode = D.LocationCode, 
                                        tOUT.ItemBatch = D.ItemBatch, 
                                        tOUT.HCTax = IF(TAX.TaxType IN ('0', '2'), 
                                        (((D.HCUnitCost - (D.HCDiscount/IF(D.Qty = 0, 1, D.Qty)))/(IF(D.FactorQty = 0, 1, D.FactorQty)))/(100+H.GbTaxRate1))*H.GbTaxRate1*H.CurRate1,
                                        ((D.HCUnitCost - (D.HCDiscount/IF(D.Qty = 0, 1, D.Qty)))/(IF(D.FactorQty = 0, 1, D.FactorQty)))*(H.GbTaxRate1/100)*H.CurRate1
                                        ),
                                        tOUT.UnitPrice = IF(TAX.TaxType IN ('0', '2'), 
                                        tOUT.UnitPrice,
                                        tOUT.UnitPrice + ( (((D.HCUnitCost - (D.HCDiscount/IF(D.Qty = 0, 1, D.Qty)))/(IF(D.FactorQty = 0, 1, D.FactorQty)))*(H.GbTaxRate1/100)*H.CurRate1) * tOUT.FactorQty)
                                            )
                                    WHERE H.GlobalTaxCode <> '' AND H.GbTaxRate1 > 0
                                    AND H.Doc1No = '$DODoc1No' AND D.Doc1No = '$DODoc1No' AND tOUT.SessionID = '$SessionID'";                    
                $StmtUpdate         = DB::connection('mysqldynamic')->statement($vStrSQL);  
            }
            //Update D_ate cloud_detail_trn_out
            $strSQLUpTrn          = CloudDtTrnOut::join('stk_do_dt as D', 'D.RunNo', '=', 'cloud_detail_trn_out.Doc2NoRunNo')
                                ->join('stk_do_hd as Hd', 'Hd.Doc1No', '=', 'D.Doc1No')
                                ->where('cloud_detail_trn_out.DocType2', '=', 'DO')
                                ->where('cloud_detail_trn_out.SessionID', '=', $SessionID)
                                ->whereIn('cloud_detail_trn_out.Doc2NoRunNo', $SelectDO)
                                ->update(['cloud_detail_trn_out.D_ate' => DB::raw('`Hd`.`D_ate`')]);
        }                     
        //END DO CUS INV -----

        //Update Doc1No
        $strSQLUp           = "UPDATE cloud_detail_trn_out tOUT
                            INNER JOIN stk_do_dt DO ON tOUT.Doc2NoRUnNo = DO.RunNo
                            INNER JOIN stk_quotation_dt Quo ON (DO.QuoRunNo = Quo.RunNo)
                            SET tOUT.Doc1NoRUnNo = Quo.RunNo, 
                                tOUT.Doc1No = Quo.Doc1No, 
                                tOUT.DocType1 = 'Quo'
                            WHERE tOUT.Doc2NoRUnNo <> 0 AND DO.QuoRunNo <> 0 AND tOUT.SessionID = '$SessionID'";
        $StmtUp          = DB::connection('mysqldynamic')->statement($strSQLUp); 

         //Update Doc1No
        $vStrSQL            = "UPDATE cloud_detail_trn_out tOUT
                            INNER JOIN stk_do_dt DO ON tOUT.Doc2NoRUnNo = DO.RunNo
                            INNER JOIN stk_sales_order_dt SO ON (DO.SORunNo = SO.RunNo)
                            SET tOUT.Doc1NoRUnNo = SO.RunNo, 
                                tOUT.Doc1No = SO.Doc1No, 
                                tOUT.DocType1 = 'SO'
                            WHERE tOUT.Doc2NoRUnNo <> 0 AND DO.SORunNo <> 0 AND tOUT.SessionID = '$SessionID'";
        $StmtUp2            = DB::connection('mysqldynamic')->statement($vStrSQL); 
        
        // stk_inventory_dt
        $StrSQL             = "INSERT INTO cloud_detail_trn_out (ItemCode, Doc1No, Doc2No, 
                            Doc3No, D_ate, BookDate,
							QtyOUT, FactorQty, UOM, 
                            UnitPrice, HCTax, CusCode, 
                            DocType1, DocType2, DocType3, 
                            Doc1NoRunNo, Doc2NoRunNo, Doc3NoRunNo, 
							VoidYN, GlobalTaxCode, DetailTaxCode, 
                            SessionID, ItemBatch, LocationCode, 
                            SalesPersonCode, DepartmentCode, ProjectCode, 
                            BranchCode, AnalysisCode1, AnalysisCode2, 
                            AnalysisCode3, DatabaseName)
                            SELECT  D.ItemCode, '', '', 
                            D.Doc1No, H.D_ate, H.D_ate,
                            D.QtyOUT, D.FactorQty, D.UOMSingular, 
                            H.CurRate1 * (D.HCUnitCost - (D.HCDiscount/D.QtyOUT)), H.CurRate1 * (D.HCTax + (H.HCGbTax/(D.QtyOUT * D.FactorQty))), H.CusCode, 
                            '', '', H.DocType, 
                            0, 0, D.RunNo, 
                            '0', '', '', 
                            '$SessionID', D.ItemBatch, D.LocationCode, 
                            D.SalesPersonCode, D.DepartmentCode, D.ProjectCode, 
                            D.BranchCode, D.AnalysisCode1, D.AnalysisCode2, 
                            D.AnalysisCode3, '$DatabaseName'
                            FROM stk_inventory_dt D INNER JOIN stk_inventory_hd H ON D.Doc1No = H.Doc1No
                            WHERE D.ItemCode <> '' AND D.BlankLine = '0' AND D.QtyOUT <> 0 AND D.FactorQty <> 0 
                            AND H.DocType IN ('IS', 'TS', 'SA', 'pd_Assembly', 'MatReq')
                            $WhereItemCode $WhereDateFrom $WhereDateTo $WhereLocationCode
                            ORDER BY D.RunNo";
        $StmtIn             = DB::connection('mysqldynamic')->statement($StrSQL); 
        // stk_sup_inv_dt
        $StrSQL             = "INSERT INTO cloud_detail_trn_out (ItemCode, Doc1No, Doc2No, 
                            Doc3No, D_ate, BookDate,
							QtyOUT, FactorQty, UOM, 
                            UnitPrice, HCTax, CusCode, 
                            DocType1, DocType2, DocType3, 
                            Doc1NoRunNo, Doc2NoRunNo, Doc3NoRunNo, 
							GlobalTaxCode, DetailTaxCode, SessionID, 
							ItemBatch, LocationCode, SalesPersonCode, 
                            DepartmentCode, ProjectCode, BranchCode,
							AnalysisCode1, AnalysisCode2, AnalysisCode3, 
                            DatabaseName)
                            SELECT 	D.ItemCode, '', D.GRNNo, 
                            D.Doc1No, H.D_ate, H.D_ate,
                            D.Qty, D.FactorQty, D.UOMSingular, 
                            H.CurRate1 * (D.HCUnitCost - (D.HCDiscount/D.Qty)), H.CurRate1 * (D.HCTax + (H.HCGbTax/(D.Qty * D.FactorQty))), H.CusCode, 
                            '', '', H.DocType, 
                            0, 0, D.RunNo, 
                            H.GlobalTaxCode, D.DetailTaxCode, '$SessionID',
                            D.ItemBatch, D.LocationCode, '', 
                            D.DepartmentCode, D.ProjectCode, D.BranchCode,
                            D.AnalysisCode1, D.AnalysisCode2, D.AnalysisCode3, 
                            '$DatabaseName'
                            FROM stk_sup_inv_dt D INNER JOIN stk_sup_inv_hd H ON D.Doc1No = H.Doc1No
                            WHERE D.ItemCode <> '' AND D.BlankLine = '0' AND D.Qty <> 0 AND D.FactorQty <> 0
                            AND H.DocType IN ('SupCN')
                            $WhereItemCode $WhereDateFrom $WhereDateTo $WhereLocationCode
                            ORDER BY D.RunNo";
        $StmtIn             = DB::connection('mysqldynamic')->statement($StrSQL); 
        
       // stk_detail_trn_out
        $StrSQL             = "INSERT INTO cloud_detail_trn_out (ItemCode, Doc1No, Doc2No, 
                            Doc3No, D_ate, BookDate,
                            QtyOUT, FactorQty, UOM, 
                            UnitPrice, HCTax, CusCode, 
                            DocType1, DocType2, DocType3, 
                            Doc1NoRunNo, Doc2NoRunNo, Doc3NoRunNo, 
                            VoidYN, GlobalTaxCode, DetailTaxCode, 
                            SessionID, ItemBatch, LocationCode, 
                            SalesPersonCode, DepartmentCode, ProjectCode, 
                            BranchCode, AnalysisCode1, AnalysisCode2, 
                            AnalysisCode3, DatabaseName)
                            SELECT 	ItemCode, Doc1No, Doc2No, 
                            Doc3No, D_ate, BookDate,
                            QtyOUT, FactorQty, UOM, 
                            UnitPrice, HCTax, CusCode, 
                            DocType1, DocType2, DocType3, 
                            Doc1NoRunNo, Doc2NoRunNo, Doc3NoRunNo, 
                            VoidYN, '', '', 
                            '$SessionID', D.ItemBatch, D.LocationCode, 
                            '', '', '', 
                            '', '', '', 
                            '', '$DatabaseName'
                            FROM stk_detail_trn_out D
                            WHERE DocType3 IN ('PSC')
                            $WhereItemCode $WhereDateFrom $WhereDateTo $WhereLocationCode
                            ORDER BY D.RunNo";
        $StmtIn             = DB::connection('mysqldynamic')->statement($StrSQL);   

        //END OUT

    }
}
/*
        /*$strSQLCusInv       = "D.ItemCode, '', '', 
                            D.Doc1No, H.D_ate, H.D_ate, 
                            '', D.Qty, D.FactorQty, 
                            D.UOMSingular, H.CurRate1 * (D.HCUnitCost - (D.HCDiscount/D.Qty)), '0',
                            0, 0, H.CurRate1 * (D.HCTax + (H.HCGbTax/(D.Qty * D.FactorQty))),
                            H.CusCode, '', '', 
                            H.DocType, 0, 0, 
                            D.RunNo, H.GlobalTaxCode, D.DetailTaxCode, 
                            '$SessionID', D.ItemBatch, D.LocationCode, 
                            D.SalesPersonCode, D.DepartmentCode, D.ProjectCode, 
                            D.BranchCode, D.AnalysisCode1, D.AnalysisCode2, 
                            D.AnalysisCode3, '$DatabaseName' ";
        $SelectBinCusInv    = DB::connection('mysqldynamic')->table('stk_cus_inv_dt as D')
                            ->join('stk_cus_inv_hd as H', 'H.Doc1No', '=', 'D.Doc1No')
                            ->select(DB::raw($strSQLCusInv))
                            ->where('D.ItemCode', '<>', '')
                            ->where('D.BlankLine', '=', '0')
                            ->where('D.FactorQty', '<>', '0')
                            ->where('D.Qty', '<>', '0')
                            ->where('H.DocType','CusCN')
                            ->whereIn('D.ItemCode', $arrItemCode)
                            //->whereIn('D.LocationCode', $arrLocationCode)
                            ->whereBetween('H.D_ate', [$DateFrom, $DateTo])
                            ->groupBy('D.ItemCode')
                            ->get()->all();
        
        foreach ($SelectBinCusInv as $dataBind){        
            $InsertCloudIn      = CloudDtTrnIn::insertUsing($arrColumTrnIn, json_encode($dataBind));
            dd($InsertCloudIn); 
        }*/
