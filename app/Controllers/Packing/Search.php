<?php namespace App\Controllers\Packing;

use \App\Controllers\BaseController;
use \App\Models\GenerateFile;

class Search extends BaseController {

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
    

    public function index()
    {
        if(!($this->ionAuth->isAdmin($this->ionAuth->user()->row()->id)) && !($this->ionAuth->inGroup('electrical_manager')) && !($this->ionAuth->inGroup('stock_controller'))){
            return redirect()->to('/noAccess');
            exit();
        }
        
        $data = $this->data;
        $data["s_date_from"] = -1;
        $data["s_date_to"] = -1; 

        $sql = "SELECT pb.*,source.HUB_NAME AS SOURCE_HUB,dest.HUB_NAME AS DESTINATION_HUB_NAME FROM TBL_PACKING_BILL pb 
        JOIN TBL_HUB source ON source.HUB_ID = pb.SOURCE_HUB_ID JOIN TBL_HUB dest ON dest.HUB_ID = pb.DESTINATION_HUB";
        if ($this->request->getPost('filter') == "true") {
            $data['s_date_from'] = $this->request->getPost('s_date_from');
            $data['s_date_to'] = $this->request->getPost('s_date_to');
            $date_from = $data['s_date_from'];
            $date_to = $data['s_date_to'];

            $sql .= " WHERE pb.CREATED_DATE >= '$date_from' and pb.CREATED_DATE < date_add('$date_to', interval 1 day) ORDER BY pb.CREATED_DATE DESC";
        }else{
            $sql .=  " ORDER BY pb.PACKING_BILL_ID ASC";
        }
        $stockSQL = "SELECT quote_stock.QUOTE_ID,sum(quote_stock.QUANTITY*stock.AVG_COST) as quote_total from TBL_QUOTE_STOCK quote_stock join TBL_STOCK stock on stock.EBQ_CODE = quote_stock.EBQ_CODE GROUP BY QUOTE_ID";
        $stockResult = $this->db->query($stockSQL);
        $data['stock_totals'] = $stockResult->getResult();
         
        $result = $this->db->query($sql);
        $data["result"] = $result;
        $data["selected_stock"] = true;

        
       
        echo view('packing/search',$data);
        
    }

    public function dl($rpt_type = ''){
        $data = $this->data;


        $generatefile = new GenerateFile($this->db);
        $generatefile->output = "download";

        if($rpt_type == 'packinglist'){
            $filters = array("s_date_from"=>$this->request->getGet('s_date_from'),
            "s_date_to"=>$this->request->getGet('s_date_to'),);

            $generatefile->generate_packinglist($data,$filters);
            exit;
        }
    }


   
    

    
}
    