<?php namespace App\Controllers\Stock;

use \App\Controllers\BaseController;
use \App\Models\GenerateFile;

class Reports extends BaseController {

    public function _remap($method, ...$params)
    {

        if (method_exists($this, $method))
        {
            return call_user_func_array(array($this, $method), $params);
        } else if ($method == "index") {
            $this->index($method);
            exit;
        } else {
            return $this->index($method, ...$params);
        }
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }

   

    public function index() {
        
        $data = $this->data;

        $data['date_from'] = $this->request->getPost('date_from');
        $data['date_to'] = $this->request->getPost('date_to');

        if ($this->request->getPost('filter') == "true") {
            $data['type_filter'] = $this->request->getPost('type_filter');
            $data['date_from'] = $this->request->getPost('date_from');
            $data['date_to'] = $this->request->getPost('date_to');

            $type = $data['type_filter'];
            $dateFrom = $data['date_from'];
            $dateTo = $data['date_to'];

            if($type == -1)
            {
                $filter = "Stock Wastage"; 
            
                // populate stock list from INVOICE
                $sql = "SELECT I.EBQ_CODE AS I_EBQ_CODE, R.EBQ_CODE AS R_EBQ_CODE, I.DESCRIPTION, I.WASTAGE,
                CASE 
                    WHEN R.EBQ_CODE IS NULL THEN
                        I.QUANTITY 
                    ELSE
                        (I.QUANTITY + R.QUANTITY)
                END QUANTITY
                FROM 
            
                (SELECT DISTINCT TIS.EBQ_CODE,TS.DESCRIPTION AS DESCRIPTION,SUM(TIS.QUANTITY) AS QUANTITY, TS.WASTAGE
                FROM TBL_INVOICE TI
                INNER JOIN
                TBL_INVOICE_STOCK TIS
                ON TI.INVOICE_ID = TIS.INVOICE_ID
                INNER JOIN TBL_STOCK TS 
                ON TIS.EBQ_CODE = TS.EBQ_CODE
                WHERE (TI.INVOICE_DATE_CREATED BETWEEN '$dateFrom' AND '$dateTo')
                GROUP BY TIS.EBQ_CODE) I
                LEFT JOIN 
                
                (SELECT  S.EBQ_CODE, S.DESCRIPTION, SUM(SC.QUANTITY) AS QUANTITY, S.WASTAGE FROM TBL_REQUISITION R
                INNER JOIN TBL_STOCK_COMBINATION SC ON SC.EBQ_CODE_LG = R.EBQ_CODE
                INNER JOIN TBL_STOCK S ON S.EBQ_CODE = SC.EBQ_CODE_SUB
                WHERE (R.IS_COMPLETE = 1) AND (R.REQUISITION_DATE BETWEEN '$dateFrom' AND '$dateTo')
                GROUP BY S.EBQ_CODE
                ) R ON R.EBQ_CODE = I.EBQ_CODE
                
                
                UNION
                
            SELECT I.EBQ_CODE AS I_EBQ_CODE, R.EBQ_CODE AS R_EBQ_CODE, R.DESCRIPTION, R.WASTAGE,
                CASE 
                    WHEN I.EBQ_CODE IS NULL THEN
                        R.QUANTITY 
                    ELSE
                        (I.QUANTITY + R.QUANTITY)
                END QUANTITY
                FROM
                (SELECT DISTINCT TIS.EBQ_CODE,TS.DESCRIPTION AS DESCRIPTION,SUM(TIS.QUANTITY) AS QUANTITY, TS.WASTAGE
                FROM TBL_INVOICE TI
                INNER JOIN
                TBL_INVOICE_STOCK TIS
                ON TI.INVOICE_ID = TIS.INVOICE_ID
                INNER JOIN TBL_STOCK TS 
                ON TIS.EBQ_CODE = TS.EBQ_CODE
                WHERE (TI.INVOICE_DATE_CREATED BETWEEN '$dateFrom' AND '$dateTo')
                GROUP BY TIS.EBQ_CODE) I
                RIGHT JOIN 
                
                (SELECT  S.EBQ_CODE, S.DESCRIPTION, SUM(SC.QUANTITY) AS QUANTITY, S.WASTAGE FROM TBL_REQUISITION R
                INNER JOIN TBL_STOCK_COMBINATION SC ON SC.EBQ_CODE_LG = R.EBQ_CODE
                INNER JOIN TBL_STOCK S ON S.EBQ_CODE = SC.EBQ_CODE_SUB
                WHERE (R.IS_COMPLETE = 1) AND (R.REQUISITION_DATE BETWEEN '$dateFrom' AND '$dateTo')
                GROUP BY S.EBQ_CODE) R ON R.EBQ_CODE = I.EBQ_CODE;"; 
                
                $objectWastage = $this->db->query($sql);

                $data['object_wastage']=$objectWastage;
            }
            elseif ($type == 0)
            {
                $filter = "Stock Report from Each Hub";  
                
                $data['hub_select'] = $this->request->getPost('hub_select');
                
                $hubName = $data['hub_select'];
                
                $sqlStockReport = "SELECT TS.EBQ_CODE,TS.DESCRIPTION,SUM(THS.QUANTITY) AS QUANTITY 
                FROM TBL_HUB_STOCK THS
                INNER JOIN TBL_HUB TH 
                ON THS.HUB_ID = TH.HUB_ID
                INNER JOIN TBL_STOCK TS 
                ON THS.EBQ_CODE = TS.EBQ_CODE  
                WHERE TH.HUB_NAME = '$hubName'
                GROUP BY TS.EBQ_CODE;";

                $objectStockReport = $this->db->query($sqlStockReport);

                $data['object_stock_report']=$objectStockReport;
                
            }
            elseif ($type == 1)
            {
                $filter = "Stock Usage Report";     
                
                // populate stock list from INVOICE
                $sql = "SELECT I.EBQ_CODE AS I_EBQ_CODE, R.EBQ_CODE AS R_EBQ_CODE, I.DESCRIPTION, I.AVG_COST,
                CASE 
                    WHEN R.EBQ_CODE IS NULL THEN
                        I.QUANTITY 
                    ELSE
                        (I.QUANTITY + R.QUANTITY)
                END QUANTITY, 
                CASE
                    WHEN R.TOTAL_VALUE IS NULL THEN
                        I.TOTAL_VALUE
                    ELSE
                        (I.TOTAL_VALUE + R.TOTAL_VALUE) 
                    END TOTAL_VALUE
                
                FROM 
            
                (SELECT DISTINCT TIS.EBQ_CODE,TS.DESCRIPTION AS DESCRIPTION,SUM(TIS.QUANTITY) AS QUANTITY,TS.AVG_COST, (TS.AVG_COST * SUM(TIS.QUANTITY)) AS TOTAL_VALUE
                FROM TBL_INVOICE TI
                INNER JOIN
                TBL_INVOICE_STOCK TIS
                ON TI.INVOICE_ID = TIS.INVOICE_ID
                INNER JOIN TBL_STOCK TS 
                ON TIS.EBQ_CODE = TS.EBQ_CODE
                WHERE (TI.INVOICE_DATE_CREATED BETWEEN '$dateFrom' AND '$dateTo')
                GROUP BY TIS.EBQ_CODE) I
                LEFT JOIN 
                
                (SELECT  S.EBQ_CODE, S.DESCRIPTION, SUM(SC.QUANTITY) AS QUANTITY,S.AVG_COST, (S.AVG_COST * SUM(SC.QUANTITY)) AS TOTAL_VALUE FROM TBL_REQUISITION R
                INNER JOIN TBL_STOCK_COMBINATION SC ON SC.EBQ_CODE_LG = R.EBQ_CODE
                INNER JOIN TBL_STOCK S ON S.EBQ_CODE = SC.EBQ_CODE_SUB
                WHERE (R.IS_COMPLETE = 1) AND (R.REQUISITION_DATE BETWEEN '$dateFrom' AND '$dateTo')
                GROUP BY S.EBQ_CODE
                ) R ON R.EBQ_CODE = I.EBQ_CODE
                
                
                UNION
                
            SELECT I.EBQ_CODE AS I_EBQ_CODE, R.EBQ_CODE AS R_EBQ_CODE, R.DESCRIPTION, R.AVG_COST,
                CASE 
                    WHEN I.EBQ_CODE IS NULL THEN
                        R.QUANTITY 
                    ELSE
                        (I.QUANTITY + R.QUANTITY)
                END QUANTITY,
                 CASE
                    WHEN I.TOTAL_VALUE IS NULL THEN
                        R.TOTAL_VALUE
                    ELSE
                        (I.TOTAL_VALUE + R.TOTAL_VALUE) 
                    END TOTAL_VALUE
                FROM
                (SELECT DISTINCT TIS.EBQ_CODE,TS.DESCRIPTION AS DESCRIPTION,SUM(TIS.QUANTITY) AS QUANTITY,TS.AVG_COST, (TS.AVG_COST * SUM(TIS.QUANTITY)) AS TOTAL_VALUE
                FROM TBL_INVOICE TI
                INNER JOIN
                TBL_INVOICE_STOCK TIS
                ON TI.INVOICE_ID = TIS.INVOICE_ID
                INNER JOIN TBL_STOCK TS 
                ON TIS.EBQ_CODE = TS.EBQ_CODE
                WHERE (TI.INVOICE_DATE_CREATED BETWEEN '$dateFrom' AND '$dateTo')
                GROUP BY TIS.EBQ_CODE) I
                RIGHT JOIN 
                
                (SELECT  S.EBQ_CODE, S.DESCRIPTION, SUM(SC.QUANTITY) AS QUANTITY,S.AVG_COST, (S.AVG_COST * SUM(SC.QUANTITY)) AS TOTAL_VALUE FROM TBL_REQUISITION R
                INNER JOIN TBL_STOCK_COMBINATION SC ON SC.EBQ_CODE_LG = R.EBQ_CODE
                INNER JOIN TBL_STOCK S ON S.EBQ_CODE = SC.EBQ_CODE_SUB
                WHERE (R.IS_COMPLETE = 1) AND (R.REQUISITION_DATE BETWEEN '$dateFrom' AND '$dateTo')
                GROUP BY S.EBQ_CODE) R ON R.EBQ_CODE = I.EBQ_CODE;";  
         
                $objectUsage = $this->db->query($sql);

                $data['object_usage']=$objectUsage;
            }
            elseif ($type == 2)
            {
                $filter = "Stock Work In Progress";       
                
                // populate stock list from database
                $sqlProgress = "SELECT TR.EBQ_CODE, TR.REQUISITION_DATE, TR.EXPECTED_COMPLETION, HB.HUB_NAME
                FROM TBL_REQUISITION TR
                INNER JOIN TBL_HUB HB ON HB.HUB_ID = TR.HUB_ID
                WHERE TR.REQUISITION_DATE BETWEEN '$dateFrom' AND '$dateTo' AND TR.IS_COMPLETE = 0;";                

                $objectProgress = $this->db->query($sqlProgress);

                $data['object_progress']=$objectProgress;
         

            }
        }
        echo view('stock/reports', $data);
    }

    

    public function dl($rpt_type = ''){
        
        $data = $this->data;
        
        // $from_name = $this->db->escape(trim($this->request->getPost('from_value')));
        // $to_name = $this->db->escape(trim($this->request->getPost('to_value')));

        // $data['date_from'] = $from_name;
        // $data['date_to'] = $to_name;

       

        $data['report_type'] = $this->request->getGet('report_type');
        $data['date_from'] = $this->request->getGet('from_value');
        $data['date_to'] = $this->request->getGet('to_value');



        $generatefile = new GenerateFile($this->db);
        $generatefile->output = "download";

        if($rpt_type == 'report'){

    


            
            $generatefile->generate_stock_wastage($data,'');
            exit;
        }
    }
}