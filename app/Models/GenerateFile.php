<?php  

namespace App\Models;

use \CodeIgniter\Database\ConnectionInterface;
use \Exception;
use \PhpOffice\PhpSpreadsheet\Spreadsheet;
use \PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use \stdClass;
use \DateTime;

class GenerateFile
{
    
    public $output = 'file';
        
    protected $db;

    public function __construct(ConnectionInterface &$db)
    {
        $this->db = &$db;
        set_time_limit(0);
        ini_set('memory_limit','512M');
    }    
    
    
    function clean($string) {
        return preg_replace('/[^ A-Za-z0-9\-]/', '', $string);
    }    
    
    
    private function _clean_file_name($in_file_name = 'no_name')
    {
        $file_name = str_replace(' ','_',trim($in_file_name));
        $file_name = str_replace('__','_',trim($file_name));
        $file_name = preg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $file_name);
        $file_name = preg_replace("([\.]{2,})", '', $file_name);
        return $file_name;
    }    

    public function getColumnRange($min,$max){
        $pointer=strtoupper($min);
        $output=array();
        while($this->positionalcomparison($pointer,strtoupper($max))<=0){
           array_push($output,$pointer);
           $pointer++;
        }
        return $output;
    }
  
    public function positionalcomparison($a,$b){
        $a1=$this->stringtointvalue($a); $b1=$this->stringtointvalue($b);
        if($a1>$b1)return 1;
        else if($a1<$b1)return -1;
        else return 0;
    }
    
    /*
    * e.g. A=1 - B=2 - Z=26 - AA=27 - CZ=104 - DA=105 - ZZ=702 - AAA=703
    */
    public function stringtointvalue($str){
        $amount=0;
        $strarra=array_reverse(str_split($str));
    
        for($i=0;$i<strlen($str);$i++){
            $amount+=(ord($strarra[$i])-64)*pow(26,$i);
        }
        return $amount;
    }

    function _csvToExcel($csv = '', $delimiter = "\t", $dest = '', $file_name = '') {
        
        require_once ROOTPATH . 'vendor/autoload.php';
        
        $full_path = '/var/www/admin.pepinstall.local/writable/pi_data/downloads/tmp';
        if (!file_exists($full_path)) {
            mkdir($full_path, 0777, true);
        }

        $file_name_csv = $full_path."/".$file_name.".csv";
        $full_file_name = $full_path."/".$file_name.".xlsx";
        
        $fp = fopen($file_name_csv, 'w'); 

        fwrite($fp, $csv);

        fclose($fp);  
        
        // Create an excel file from the generated CSV file - Maybe we can create a function to do this for future use
        $csv_reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Csv');
        $csv_reader->setDelimiter("\t");
        $objPhpSpreadsheet = $csv_reader->load($file_name_csv);
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPhpSpreadsheet, 'Xlsx');
        $objWriter->save($full_file_name);  
        //Delete the tmp csv file
        unlink($file_name_csv);
        
        /** Create a new Xlsx Reader  **/
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($full_file_name);

        // Create style for the header
        $styleArray = array(
          'font' => array(
            'bold' => true,
            //'color' => array('rgb' => 'FFFFFF')
          ),
          'alignment' => array(
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
          ),
          //'fill' => array( 
          //    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 
          //    'startColor' => array('argb' => 'FF00b0f0') 
          //),
          'borders' => array(
              'bottom' => array(
                  'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                  'color' => array('rgb' => '4169e1'),
              ),
          ),

        );


        $highestColumm = $spreadsheet->getActiveSheet()->getHighestColumn();
        $highestColumnIndexNum = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumm);
        $icount = 0;

        for ($columnID = 'A'; $columnID !== 'ZZ'; $columnID++){
            // Break out of loop at last header column
            if ($icount >= $highestColumnIndexNum){
                $end_col = $columnID;
                break;
            }
            $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true); 
            $icount++;
        }   		

        $spreadsheet->getActiveSheet()->getStyle('A1:'.$end_col."1")->applyFromArray($styleArray);

        //$new_writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        $objResult = new stdClass();
        $objResult->spreadsheet = $spreadsheet;
        $objResult->full_file_name = $full_file_name;
        return $objResult;
               
        
    }


    public function generate_orderlist($data,$filters){
        $content = '';

        $header = array('Order Number','Order Status','Order Notes','Order Date Created','Order Created By','Quote Type','Quote Total','Quote Created Date','Quote Approved Date','Quote Status','Contractor','Contractor Email','Contractor Contact Number');


        $sql = "select o.ORDER_NO,o.QUOTE_ID,o.STATUS as ORDER_STATUS, o.ORDER_NOTES, u.username as ORDER_CREATED_BY, o.ORDER_DATE_CREATED,
                q.TOTAL as QUOTE_TOTAL, qt.TYPE_NAME as QUOTE_TYPE, q.CREATED_DATE as QUOTE_CREATED_DATE,q.APPROVED_DATE as QUOTE_APPROVED_DATE, q.STATUS as QUOTE_STATUS,
                c.CONTRACTOR_NAME, c.EMAIL as CONTRACTOR_EMAIL, c.CONTACT_NUMBER as CONTRACTOR_CONTACT
                from TBL_ORDER o 
                inner join TBL_QUOTE q on q.QUOTE_ID = o.QUOTE_ID
                inner join TBL_QUOTE_TYPE qt on qt.TYPE_ID = q.QUOTE_TYPE_ID 
                inner join TBL_CONTRACTOR c on c.CONTRACTOR_ID = q.CONTRACTOR_ID
                inner join TBL_USER u on u.id = o.USER_ID";

        $sql .= " where o.ORDER_DATE_CREATED >= '".$filters['s_date_from']."' and o.ORDER_DATE_CREATED < date_add('".$filters['s_date_to']."', interval 1 day)";


        $query = $this->db->query($sql);

        foreach($query->getResult() as $row){

            $content .= $row->ORDER_NO."\t";
            $content .= $row->ORDER_STATUS."\t";
            $content .= $row->ORDER_NOTES."\t";
            $content .= $row->ORDER_DATE_CREATED."\t";
            $content .= $row->ORDER_CREATED_BY."\t";
            $content .= $row->QUOTE_TYPE."\t";
            $content .= $row->QUOTE_TOTAL."\t";
            $content .= $row->QUOTE_CREATED_DATE."\t";
            $content .= $row->QUOTE_APPROVED_DATE."\t";
            $content .= $row->QUOTE_STATUS."\t";
            $content .= $row->CONTRACTOR_NAME."\t";
            $content .= $row->CONTRACTOR_EMAIL."\t";
            $content .= $row->CONTRACTOR_CONTACT."\t";

            $content .= "\n";
        }

        // create csv & then convert to excel
        $csv = implode("\t", $header)."\n".$content;
        $file_name = $this->_clean_file_name('Order_List_'.date('Ymd'));
        $dest = "download";
        $delimiter = "\t";
        $objResult = $this->_csvToExcel($csv,$delimiter,$dest,$file_name);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($objResult->spreadsheet);

        //set headers for download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $file_name .'.xlsx"'); 
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output'); // download file    
        
        exit();
    }

    public function generate_purchase_orderlist($data,$filters){
        $content = '';

        $header = array('Order Number','Order Date','Date Required','Vendor','Total','Approval Status','FulFilled');


        $sql = "SELECT * FROM TBL_PURCHASE_ORDER;";

        $query = $this->db->query($sql);

        foreach($query->getResult() as $row){

            $content .= $row->PURCHASE_ORDER_ID."\t";
            $content .= $row->ORDER_DATE."\t";
            $content .= $row->DATE_REQUIRED."\t";
            $content .= $row->VENDOR_NAME."\t";
            $content .= 'R'.$row->TOTAL."\t";
            $content .= $row->FUL_FILLED."\t";
            $content .= "\n";
        }

        // create csv & then convert to excel
        $csv = implode("\t", $header)."\n".$content;
        $file_name = $this->_clean_file_name('Purchase_Order_List_'.date('Ymd'));
        $dest = "download";
        $delimiter = "\t";
        $objResult = $this->_csvToExcel($csv,$delimiter,$dest,$file_name);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($objResult->spreadsheet);

        //set headers for download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $file_name .'.xlsx"'); 
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output'); // download file    
        
        exit();
    }

    public function generate_deliverylist($data){
        $content = '';

        $header = array('Delivery ID','Packing Bill ID','Delivery Date','Delivery Method','Waybill Number','Notes','Signed Off?');

        $sql = "select d.* 
                from TBL_DELIVERY_NOTE d";

        $query = $this->db->query($sql);

        foreach($query->getResult() as $row){
            $content .= $row->DELIVERY_ID."\t";
            $content .= $row->PACKING_BILL_ID."\t";
            $content .= $row->DELIVERY_DATE."\t";
            $content .= $row->DELIVERY_METHOD."\t";
            $content .= $row->DELIVERY_WAYBILL."\t";
            $content .= $row->NOTES."\t";
            if($row->IS_SIGNED_OFF == 1){
                $content .= "Yes"."\t";
            } else {
                $content .= "No"."\t";
            }

            $content .= "\n";
        }

        // create csv & then convert to excel
        $csv = implode("\t", $header)."\n".$content;
        $file_name = $this->_clean_file_name('Delivery_Note_List_'.date('Ymd'));
        $dest = "download";
        $delimiter = "\t";
        $objResult = $this->_csvToExcel($csv,$delimiter,$dest,$file_name);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($objResult->spreadsheet);

        //set headers for download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $file_name .'.xlsx"'); 
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output'); // download file    
        
        exit();
    }

    public function generate_packinglist($data,$filters){
        $content = '';

        $header = array('ID','Status','Order Number','Order Status','Order Date Created','Order Notes','Source Hub','Destination Hub','Ship Via','Created Date','Pack Date','Delivery Date');

        $sql = "select p.*, sh.HUB_NAME as SOURCE_HUB, dh.HUB_NAME as DESTINATION_HUB,o.STATUS as ORDER_STATUS,o.ORDER_DATE_CREATED,o.ORDER_NOTES
                from TBL_PACKING_BILL p
                inner join TBL_ORDER o on o.ORDER_NO = p.ORDER_NO
                inner join TBL_HUB sh on sh.HUB_ID = p.SOURCE_HUB_ID
                inner join TBL_HUB dh on dh.HUB_ID = p.DESTINATION_HUB";

        if (isset($filters)) {
            $date_from = $filters['s_date_from'];
            $date_to = $filters['s_date_to'];
            $sql .= " WHERE p.CREATED_DATE >= '$date_from' and p.CREATED_DATE < date_add('$date_to', interval 1 day) ORDER BY p.CREATED_DATE DESC";
        }
        else{
            $sql .=  " ORDER BY p.PACKING_BILL_ID ASC";
        }

        $query = $this->db->query($sql);

        foreach($query->getResult() as $row){
            $content .= $row->PACKING_BILL_ID."\t";
            $content .= $row->STATUS."\t";
            $content .= $row->ORDER_NO."\t";
            $content .= $row->ORDER_STATUS."\t";
            $content .= $row->ORDER_DATE_CREATED."\t";
            $content .= $row->ORDER_NOTES."\t";
            $content .= $row->SOURCE_HUB."\t";
            $content .= $row->DESTINATION_HUB."\t";
            $content .= $row->SHIP_VIA."\t";
            $content .= $row->CREATED_DATE."\t";
            $content .= $row->PACK_DATE."\t";
            $content .= $row->DELIVERY_DATE."\t";

            $content .= "\n";
        }

        // create csv & then convert to excel
        $csv = implode("\t", $header)."\n".$content;
        $file_name = $this->_clean_file_name('Packing_Bill_List_'.date('Ymd'));
        $dest = "download";
        $delimiter = "\t";
        $objResult = $this->_csvToExcel($csv,$delimiter,$dest,$file_name);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($objResult->spreadsheet);

        //set headers for download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $file_name .'.xlsx"'); 
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output'); // download file    
        
        exit();
    }

    public function generate_grnlist($data){
        $content = '';

        $header = array('ID','Date Recieved','Hub Name','Actioned By','Total Cost');

        $sql = "select g.*,h.HUB_NAME,u.first_name,u.last_name,
                (select sum(gs.cost) from TBL_GRN_STOCK gs where gs.GRN_ID = g.GRN_ID) as total
                from TBL_GRN g
                inner join TBL_HUB h on h.HUB_ID = g.HUB_ID
                inner join TBL_USER u on u.id = g.FK_USER
                ";

        $query = $this->db->query($sql);

        foreach($query->getResult() as $row){
            $content .= $row->GRN_ID."\t";
            $content .= $row->RECEIVED_DATE."\t";
            $content .= $row->HUB_NAME."\t";
            $content .= $row->first_name." ".$row->last_name."\t";
            $content .= $row->total."\t";

            $content .= "\n";
        }

        // create csv & then convert to excel
        $csv = implode("\t", $header)."\n".$content;
        $file_name = $this->_clean_file_name('GRN_List_'.date('Ymd'));
        $dest = "download";
        $delimiter = "\t";
        $objResult = $this->_csvToExcel($csv,$delimiter,$dest,$file_name);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($objResult->spreadsheet);

        //set headers for download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $file_name .'.xlsx"'); 
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output'); // download file    
        
        exit();
    }

    public function generate_quotelist($data,$filters){
        $content = '';

        $header = array('Quote ID','Store','Quote Type','Created Date','Created By','Approved Date','Delivery Date','Contractor','Contractor Contact','Contractor Email');
        
        $sql = "SELECT quote.*, store.STORE_NAME,
        quote_type.TYPE_NAME,
        contractor.CONTRACTOR_NAME,contractor.CONTACT_NUMBER,contractor.EMAIL,
        concat(user.FIRST_NAME,' ',user.LAST_NAME) AS FULL_NAME 
        FROM TBL_QUOTE quote 
        JOIN TBL_QUOTE_TYPE quote_type 
            ON quote_type.TYPE_ID = quote.QUOTE_TYPE_ID 
        JOIN TBL_STORE store 
            ON store.STORE_ID = quote.STORE_ID
        JOIN TBL_CONTRACTOR contractor
            ON contractor.CONTRACTOR_ID = quote.CONTRACTOR_ID
        JOIN TBL_USER user ON user.ID = quote.USER_ID";

        // $stockSQL = "SELECT quote_stock.QUOTE_ID,sum(quote_stock.QUANTITY*(stock.AVG_COST*(1+(stock.MARKUP/100)))) as quote_total 
        // from TBL_QUOTE_STOCK quote_stock 
        // join TBL_STOCK stock on stock.EBQ_CODE = quote_stock.EBQ_CODE GROUP BY QUOTE_ID";
        
        
        if (isset($filters)) {
            $date_from = $filters['s_date_from'];
            $date_to = $filters['s_date_to'];
            $sql .= " WHERE quote.CREATED_DATE >= '$date_from' and quote.CREATED_DATE < date_add('$date_to', interval 1 day) ORDER BY quote.CREATED_DATE DESC";
        }
        $query = $this->db->query($sql);

        foreach($query->getResult() as $row){
            $content .= $row->QUOTE_ID."\t";
            $content .= $row->STORE_NAME."\t";
            $content .= $row->TYPE_NAME."\t";
            $content .= $row->CREATED_DATE."\t";
            $content .= $row->FULL_NAME."\t";
            $content .= $row->APPROVED_DATE."\t";
            $content .= $row->DELIVERY_DATE."\t";
            $content .= $row->CONTRACTOR_NAME."\t";
            $content .= $row->CONTACT_NUMBER."\t";
            $content .= $row->EMAIL."\t";
            $content .= "\n";
        }

        // create csv & then convert to excel
        $csv = implode("\t", $header)."\n".$content;
        $file_name = $this->_clean_file_name('Quote_List_'.date('Ymd'));
        $dest = "download";
        $delimiter = "\t";
        $objResult = $this->_csvToExcel($csv,$delimiter,$dest,$file_name);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($objResult->spreadsheet);

        //set headers for download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $file_name .'.xlsx"'); 
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output'); // download file    
        
        exit();
    
    
    }
    public function generate_invoicelist($data,$filters){
        $content = '';

        $header = array('Pastel Order No','Created Date','Account','Tax Reference','Discount %','Amount','Is Paid','Notes','Approved Date','Delivery Date','Contractor','Contractor Contact','Contractor Email');
        
         $sql = "SELECT * FROM TBL_INVOICE";

         if (isset($filters)) {
            $date_from = $filters['s_date_from'];
            $date_to = $filters['s_date_to'];
            $sql .= " WHERE INVOICE_DATE_CREATED >= '$date_from' AND INVOICE_DATE_CREATED < date_add('$date_to', interval 1 day) ORDER BY INVOICE_DATE_CREATED DESC;";
        }else{
            $sql .=  " ORDER BY INVOICE_ID ASC;";
        }
        $query = $this->db->query($sql);

        foreach($query->getResult() as $row){
            $content .= $row->PASTEL_INVOICE_NO."\t";
            $content .= $row->PASTEL_INVOICE_DATE."\t";
            $content .= $row->ACCOUNT."\t";
            $content .= $row->TAX_REFERENCE."\t";
            $content .= $row->DISCOUNT_PERCENTAGE."\t";
            $content .= $row->INVOICE_AMOUNT."\t";
            $content .= $row->INVOICE_PAID."\t";
            $content .= "\n";
        }

        // create csv & then convert to excel
        $csv = implode("\t", $header)."\n".$content;
        $file_name = $this->_clean_file_name('Invoice_List_'.date('Ymd'));
        $dest = "download";
        $delimiter = "\t";
        $objResult = $this->_csvToExcel($csv,$delimiter,$dest,$file_name);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($objResult->spreadsheet);

        //set headers for download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $file_name .'.xlsx"'); 
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output'); // download file    
        
        exit();
    
    
    }
    public function generate_joblist($data,$jobtype){
        $content = '';

        $header = array('Job Type','Job Status','Job Notes','Job Level','Job Created Date','Job Completed Date','Store Name','Order Number','Order Notes','Quote Type','Quote Total','Quote Created Date','Quote Approved Date','Quote Notes','Action Type');
       
        $jobsearch = '';

        if($jobtype != -1){
            $jobsearch = " where j.JOB_TYPE_ID = $jobtype ";
        }

        $sql = "select j.NOTES as JOB_NOTES, j.CREATED_DATE as JOB_CREATED, j.COMPLETION_DATE as JOB_COMPLETED, j.JOB_LEVEL, jt.JOB_TYPE_DESCRIPTION,
                        j.JOB_STATUS,o.ORDER_NO, o.ORDER_DATE_CREATED,o.ORDER_NOTES,s.STORE_NAME,qt.TYPE_NAME as QUOTE_TYPE, q.TOTAL as QUOTE_TOTAL, q.CREATED_DATE as QUOTE_CREATED, 
                        q.APPROVED_DATE as QUOTE_APPROVED,q.NOTE as QUOTE_NOTES,at.ACTION_NAME
                from TBL_JOB j
                inner join TBL_JOB_TYPE jt on jt.JOB_TYPE_ID = j.JOB_TYPE_ID
                inner join TBL_ORDER o on o.ORDER_NO = j.ORDER_NO
                inner join TBL_QUOTE q on q.QUOTE_ID = o.QUOTE_ID
                inner join TBL_STORE s on s.STORE_ID = q.STORE_ID
                inner join TBL_QUOTE_TYPE qt on qt.TYPE_ID = q.QUOTE_TYPE_ID
                inner join TBL_ACTION_TYPE at on at.ACTION_ID = q.ACTION_ID 
                $jobsearch";

        $query = $this->db->query($sql);

        foreach($query->getResult() as $row){
            $content .= $row->JOB_TYPE_DESCRIPTION."\t";
            $content .= $row->JOB_STATUS."\t";
            $content .= $row->JOB_NOTES."\t";
            $content .= $row->JOB_LEVEL."\t";
            $content .= $row->JOB_CREATED."\t";
            $content .= $row->JOB_COMPLETED."\t";
            $content .= $row->STORE_NAME."\t";
            $content .= $row->ORDER_NO."\t";
            $content .= $row->ORDER_NOTES."\t";
            $content .= $row->QUOTE_TYPE."\t";
            $content .= $row->QUOTE_TOTAL."\t";
            $content .= $row->QUOTE_CREATED."\t";
            $content .= $row->QUOTE_APPROVED."\t";
            $content .= $row->QUOTE_NOTES."\t";
            $content .= $row->ACTION_NAME."\t";

            $content .= "\n";
        }

        // create csv & then convert to excel
        $csv = implode("\t", $header)."\n".$content;
        $file_name = $this->_clean_file_name('Job_List_'.date('Ymd'));
        $dest = "download";
        $delimiter = "\t";
        $objResult = $this->_csvToExcel($csv,$delimiter,$dest,$file_name);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($objResult->spreadsheet);

        //set headers for download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $file_name .'.xlsx"'); 
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output'); // download file    
        
        exit();

    }

    public function generate_stocklist($data){
        $content = '';

        $header = array('EBQ Code','Purchase Cost','Average Cost','Description','Wastage','Markup','Min Reorder', 'Quantity','Last Cost','Active?','Metric','Built?');

        $sql = "select s.*,SUM(HS.QUANTITY) AS QUANTITY, METRIC_DESCRIPTION
                from TBL_STOCK s
                inner join TBL_METRIC m on m.METRIC_ID = s.METRIC_ID
                INNER JOIN TBL_HUB_STOCK HS ON HS.EBQ_CODE = s.EBQ_CODE
                GROUP BY s.EBQ_CODE;";

        $query = $this->db->query($sql);

        foreach($query->getResult() as $row){
            $content .= $row->EBQ_CODE."\t";
            $content .= $row->PURCHASE_COST."\t";
            $content .= $row->AVG_COST."\t";
            $content .= $row->DESCRIPTION."\t";
            $content .= $row->WASTAGE."\t";
            $content .= $row->MARKUP."\t";
            $content .= $row->MIN_REORDER."\t";
            $content .= $row->QUANTITY."\t";
            $content .= $row->LAST_COST."\t";
            if($row->IS_ACTIVE == 1){
                $content .= "Yes"."\t";
            } else {
                $content .= "No"."\t";
            }
            $content .= $row->METRIC_DESCRIPTION."\t";
            if($row->IS_BUILT == 1 ){
                $content .= "Yes"."\t";
            } else {
                $content .= "No"."\t";
            }
            $content .= "\n";
        }

        // create csv & then convert to excel
        $csv = implode("\t", $header)."\n".$content;
        $file_name = $this->_clean_file_name('Stock_List_'.date('Ymd'));
        $dest = "download";
        $delimiter = "\t";
        $objResult = $this->_csvToExcel($csv,$delimiter,$dest,$file_name);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($objResult->spreadsheet);

        //set headers for download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $file_name .'.xlsx"'); 
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output'); // download file    
        
        exit();

    }

    public function generate_storelist($data){
        $content = '';

        $header = array('Store Type','Brand','Area','Region','Hub','Store Name','FF Code','Contact Number','Store Manager','Trading Size','Branch Size','In Center?','Is Open?','Opening Date','Maintenance Month');

        $sql = "select s.STORE_NAME, s.FF_CODE, s.CONTACT_NUMBER as STORE_CONTACT, s.IN_CENTER,s.TRADING_SIZE,s.BRANCH_SIZE,s.OPENING_DATE,s.MAINTENANCE_MONTH,s.IS_OPEN,
                        st.STORE_TYPE_DESCRIPTION, b.BRAND_NAME,a.AREA_NAME,h.HUB_NAME, u.first_name, u.last_name, r.REGION_NAME
                from TBL_STORE s
                left join TBL_STORE_TYPE st on st.STORE_TYPE_ID = s.STORE_TYPE_ID
                left join TBL_BRAND b on b.BRAND_ID = st.BRAND_ID
                left join TBL_AREA a on a.AREA_NO = s.AREA_ID
                left join TBL_HUB h on h.HUB_ID = s.HUB_ID
                left join TBL_USER u on u.id = s.STORE_MANAGER
                left join TBL_REGION r on r.REGION_NO = a.REGION_NO";
        
        $query = $this->db->query($sql);

        foreach($query->getResult() as $row){
            $content .= $row->STORE_TYPE_DESCRIPTION."\t";
            $content .= $row->BRAND_NAME."\t";
            $content .= $row->AREA_NAME."\t";
            $content .= $row->REGION_NAME."\t";
            $content .= $row->HUB_NAME."\t";
            $content .= $row->STORE_NAME."\t";
            $content .= $row->FF_CODE."\t";
            $content .= $row->STORE_CONTACT."\t";
            $content .= $row->first_name." ".$row->last_name."\t";
            $content .= $row->TRADING_SIZE."\t";
            $content .= $row->BRANCH_SIZE."\t";
            if($row->IN_CENTER == 1){
                $content .= "Yes"."\t";
            } else {
                $content .= "No"."\t";
            }
            if($row->IS_OPEN == 1){
                $content .= "Yes"."\t";
            } else {
                $content .= "No"."\t";
            }
            $content .= $row->OPENING_DATE."\t";
            $content .= $row->MAINTENANCE_MONTH."\t";
            $content .= "\n";

        }

        // create csv & then convert to excel
        $csv = implode("\t", $header)."\n".$content;
        $file_name = $this->_clean_file_name('Store_List_'.date('Ymd'));
        $dest = "download";
        $delimiter = "\t";
        $objResult = $this->_csvToExcel($csv,$delimiter,$dest,$file_name);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($objResult->spreadsheet);

        //set headers for download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $file_name .'.xlsx"'); 
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output'); // download file    
        
        exit();
    }

    public function generate_contractorlist($data){
        $content = '';

        $header = array('Contractor Name','Contact Number','Email','In Business?');

        $sql = "select * from TBL_CONTRACTOR";

        $query = $this->db->query($sql);

        foreach($query->getResult() as $row){
            $content .= $row->CONTRACTOR_NAME."\t";
            $content .= $row->CONTACT_NUMBER."\t";
            $content .= $row->EMAIL."\t";
            if($row->IN_BUSINESS == 1){
                $content .= 'Yes';
            } else {
                $content .= 'No';
            }
            $content .= "\n";

        }

        // create csv & then convert to excel
        $csv = implode("\t", $header)."\n".$content;
        $file_name = $this->_clean_file_name('Contractor_List_'.date('Ymd'));
        $dest = "download";
        $delimiter = "\t";
        $objResult = $this->_csvToExcel($csv,$delimiter,$dest,$file_name);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($objResult->spreadsheet);

        //set headers for download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $file_name .'.xlsx"'); 
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output'); // download file    
        
        exit();

    }

    public function generate_stock_wastage($data,$filters){
        
        $content = '';

        $reportType = $data['report_type'];
        $dateFrom = $data['date_from'];
        $dateTo = $data['date_to'];

        $stockFromHub = "Stock Report from Each Hub";

        if($reportType == "Stock Wastage")
        {
            $header = array('Product Code','Product Name','Unit','Quantity','Total Wastage');

            $sqlMain = "SELECT DISTINCT TIS.EBQ_CODE,TS.DESCRIPTION AS DESCRIPTION,SUM(TIS.QUANTITY) AS QUANTITY
            FROM TBL_INVOICE TI
            INNER JOIN
            TBL_INVOICE_STOCK TIS
            ON TI.INVOICE_ID = TIS.INVOICE_ID
            INNER JOIN TBL_STOCK TS 
            ON TIS.EBQ_CODE = TS.EBQ_CODE
            WHERE TI.INVOICE_DATE_CREATED BETWEEN '$dateFrom' AND '$dateTo'
            GROUP BY TIS.EBQ_CODE;";


            $queryMain = $this->db->query($sqlMain);

            foreach($queryMain->getResult() as $row)
            {
                $wastage = 0;
                $totalWastage = 0;
                $metricID = 0;
                $metricDesc = '';

                $content .= $row->EBQ_CODE."\t";                                     
                $content .= $row->DESCRIPTION."\t";
               
                // get unit
                $sqlMetric = "select METRIC_ID from TBL_STOCK WHERE EBQ_CODE = '".$row->EBQ_CODE."';";
                $queryMetric = $this->db->query($sqlMetric);
                foreach ($queryMetric->getResult('array') as $thisrow): {                                       
                    $thisrow['METRIC_ID'];
                    $metricID = $thisrow['METRIC_ID'];
                } endforeach;

                // get metric desc
                $sqlDesc = "select METRIC_DESCRIPTION from TBL_METRIC WHERE METRIC_ID = $metricID;";
                $queryDesc = $this->db->query($sqlDesc);
                foreach ($queryDesc->getResult('array') as $thisrow): {                                       
                    $thisrow['METRIC_DESCRIPTION'];
                    $metricDesc = $thisrow['METRIC_DESCRIPTION'];
                } endforeach;

                
                $content .= $metricDesc."\t";

                $content .= $row->QUANTITY."\t";
   
                $sqlWastage = "select WASTAGE from TBL_STOCK WHERE EBQ_CODE = '".$row->EBQ_CODE."';";
                $queryWastage = $this->db->query($sqlWastage);
                foreach ($queryWastage->getResult('array') as $thisrow): {                                       
                    $wastage = $thisrow['WASTAGE'];
                    $totalWastage = $wastage * $row->QUANTITY;
                } endforeach;

                $content .= number_format($totalWastage/100, 2, ',', ' ')."\t";
                $content .= "\n";
            }
        }


        if(strpos($reportType, $stockFromHub) !== false){
            
            $splitInput = explode(" : ",$reportType);

            $hubName = $splitInput[1];
        
            $header = array('Product Code','Product Name','Quantity','Total Value');

            $sqlStockReport = "SELECT TS.EBQ_CODE,TS.DESCRIPTION,SUM(THS.QUANTITY) AS QUANTITY 
            FROM TBL_HUB_STOCK THS
            INNER JOIN TBL_HUB TH 
            ON THS.HUB_ID = TH.HUB_ID
            INNER JOIN TBL_STOCK TS 
            ON THS.EBQ_CODE = TS.EBQ_CODE  
            WHERE TH.HUB_NAME = '$hubName'
            GROUP BY TS.EBQ_CODE;";

            $objectStockReport = $this->db->query($sqlStockReport);

            foreach($objectStockReport->getResult() as $row)
            {
                $avgcost = 0;
                $qty = 0;

                $content .= $row->EBQ_CODE."\t";
                $content .= $row->DESCRIPTION."\t";
                $content .= $row->QUANTITY."\t";

                $qty = $row->QUANTITY;
                
                // get the average cost per item
                $sql = "select AVG_COST from TBL_STOCK WHERE EBQ_CODE = '".$row->EBQ_CODE."';";
                $query = $this->db->query($sql);
                foreach ($query->getResult('array') as $thisrow): {                                       
                    $thisrow['AVG_COST'];
                    $avgcost = $thisrow['AVG_COST'];
                } endforeach;
                
                $content .=  "R ".number_format($qty * $avgcost, 2, '.', ' ')."\t";
                $content .= "\n";
            }

            $reportType = "Stock Report from ".$hubName;
        }
        if($reportType == "Stock Usage Report")
        {
            $header = array('Product Code','Product Name','Quantity','Total Value');

            $sqlUsage = "SELECT DISTINCT TIS.EBQ_CODE,
            TS.DESCRIPTION AS DESCRIPTION,
            SUM(TIS.QUANTITY) AS QUANTITY,
            (TS.AVG_COST * SUM(TIS.QUANTITY)) AS TOTAL_VALUE
            FROM TBL_INVOICE TI
            INNER JOIN
            TBL_INVOICE_STOCK TIS
            ON TI.INVOICE_ID = TIS.INVOICE_ID
            INNER JOIN TBL_STOCK TS 
            ON TIS.EBQ_CODE = TS.EBQ_CODE
            WHERE TI.INVOICE_DATE_CREATED BETWEEN '$dateFrom' AND '$dateTo'
            GROUP BY TIS.EBQ_CODE;";                

            $objectUsage = $this->db->query($sqlUsage);

            foreach($objectUsage->getResult() as $row)
            {
                $content .= $row->EBQ_CODE."\t";                         
                $content .= $row->DESCRIPTION."\t";
                $content .= $row->QUANTITY."\t";
                $content .= "R ".number_format($row->TOTAL_VALUE, 2, '.', ' ')."\t";
                $content .= "\n";
            }
        }
        if($reportType == "Stock Work in Progress")
        {
            $header = array('Product Code','Requistion Date','Expected Completion','Hub Name');

            $sqlProgress = "SELECT TR.EBQ_CODE, TR.REQUISITION_DATE, TR.EXPECTED_COMPLETION, HB.HUB_NAME
            FROM TBL_REQUISITION TR
            INNER JOIN TBL_HUB HB ON HB.HUB_ID = TR.HUB_ID
            WHERE TR.REQUISITION_DATE BETWEEN '$dateFrom' AND '$dateTo' AND TR.IS_COMPLETE = 0;";                

            $objectProgress = $this->db->query($sqlProgress);

            foreach($objectProgress->getResult() as $row)
            {
                $content .= $row->EBQ_CODE."\t";
                $content .= $row->REQUISITION_DATE."\t";
                $content .= $row->EXPECTED_COMPLETION."\t";
                $content .= $row->HUB_NAME."\t";
                $content .= "\n";
            }
        }

        // create csv & then convert to excel
        $csv = implode("\t", $header)."\n".$content;
        $file_name = $this->_clean_file_name($reportType.'_'.date('Ymd'));
        $dest = "download";
        $delimiter = "\t";
        $objResult = $this->_csvToExcel($csv,$delimiter,$dest,$file_name);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($objResult->spreadsheet);

        //set headers for download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $file_name .'.xlsx"'); 
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output'); // download file    
        
        exit();
    }


}