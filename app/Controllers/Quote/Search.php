<?php namespace App\Controllers\Quote;

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
        if(!($this->ionAuth->isAdmin($this->ionAuth->user()->row()->id)) && !($this->ionAuth->inGroup('electrical_manager')) && !($this->ionAuth->inGroup('electrical_administrator'))){
            return redirect()->to('/noAccess');
            exit();
        }

        $data = $this->data;

        $data["s_date_from"] = -1;
        $data["s_date_to"] = -1; 


        $sql = " SELECT quote.*, CASE 
            WHEN O.QUOTE_ID IS NULL THEN
                0
            ELSE
                1
            END IS_ORDERED
            , store.STORE_NAME,
        quote_type.TYPE_NAME,
        contractor.CONTRACTOR_NAME  
        FROM TBL_QUOTE quote 
        JOIN TBL_QUOTE_TYPE quote_type 
            ON quote_type.TYPE_ID = quote.QUOTE_TYPE_ID 
        JOIN TBL_STORE store 
            ON store.STORE_ID = quote.STORE_ID
        JOIN TBL_CONTRACTOR contractor
            ON contractor.CONTRACTOR_ID = quote.CONTRACTOR_ID
        LEFT JOIN 
            (SELECT * FROM TBL_ORDER) O ON O.QUOTE_ID = quote.QUOTE_ID";

        $stockSQL = "SELECT quote_stock.QUOTE_ID,sum(quote_stock.QUANTITY*(stock.AVG_COST*(1+(stock.MARKUP/100)))) as quote_total 
        from TBL_QUOTE_STOCK quote_stock 
        join TBL_STOCK stock on stock.EBQ_CODE = quote_stock.EBQ_CODE GROUP BY QUOTE_ID";
        
        
        if ($this->request->getPost('filter') == "true") {
            $data['s_date_from'] = $this->request->getPost('s_date_from');
            $data['s_date_to'] = $this->request->getPost('s_date_to');
            $date_from = $data['s_date_from'];
            $date_to = $data['s_date_to'];

            $sql .= " WHERE quote.CREATED_DATE >= '$date_from' and quote.CREATED_DATE < date_add('$date_to', interval 1 day) ORDER BY quote.CREATED_DATE DESC";
        }
        else{
            $sql .= " ORDER BY quote.QUOTE_ID DESC";
        }
        $stockResult = $this->db->query($stockSQL);
        $data['stock_totals'] = $stockResult->getResult();
         
        $result = $this->db->query($sql);
        $data["result"] = $result;
        $data["selected_stock"] = true;


        echo view('quote/search', $data);
            
    }

    public function dl($rpt_type = ''){
        $data = $this->data;

        $generatefile = new GenerateFile($this->db);
        $generatefile->output = "download";

        if($rpt_type == 'quotelist'){
            $filters = array("s_date_from"=>$this->request->getGet('s_date_from'),
            "s_date_to"=>$this->request->getGet('s_date_to'),);

            $generatefile->generate_quotelist($data,$filters);
            exit;
        }
    }
}
    