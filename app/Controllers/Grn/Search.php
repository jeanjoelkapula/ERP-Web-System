<?php namespace App\Controllers\Grn;

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

        $data['grn_id'] = [];
        $data['grn_date'] = [];
        $data['grn_hub_id'] = [];
        $data['grn_user_id'] = [];
        $data['grn_hub_name'] = [];
        $data['grn_user_name'] = [];
        $data['grn_total_cost'] = [];
        $data['grn_source'] = [];

        //get GRN details
        $sqlGRN = "SELECT * FROM TBL_GRN";
        $queryGRN = $this->db->query($sqlGRN);
        foreach ($queryGRN->getResult('array') as $row){
            array_push($data['grn_id'],$row['GRN_ID']);
            array_push($data['grn_date'],$row['RECEIVED_DATE']);
            array_push($data['grn_hub_id'],$row['HUB_ID']);
            array_push($data['grn_user_id'],$row['FK_USER']);            
            if($row['REQUISITION_NO'] != null) {
                array_push($data['grn_source'],'Requisition: #'.$row['REQUISITION_NO']); 
            }
            else {
                array_push($data['grn_source'],'Purchase: #'.$row['PURCHASE_ORDER_NO']); 
            }
        }

        //get Hub Names  
        foreach($data['grn_hub_id'] as $hub_id ){
                  
            $hubNameSQL = "SELECT HUB_NAME FROM TBL_HUB WHERE HUB_ID = $hub_id;";
            $hubNameQuery = $this->db->query($hubNameSQL);
            foreach ($hubNameQuery->getResult('array') as $row){
                array_push($data['grn_hub_name'],$row['HUB_NAME']);
            }
        }

        //get all Users' Names   
        foreach($data['grn_user_id'] as $user_id ){
                     
            $userNameSQL = "SELECT CONCAT(first_name, ' ', last_name) as USERNAME FROM TBL_USER WHERE id = $user_id;";
            $userNameQuery = $this->db->query($userNameSQL);
            foreach ($userNameQuery ->getResult('array') as $row){
                array_push($data['grn_user_name'],$row['USERNAME']);
            }
        }

        //get total cost for each record
        foreach($data['grn_id'] as $grn_id ){            
            $userNameSQL = "SELECT SUM(COST) as TCOST FROM TBL_GRN_STOCK WHERE GRN_ID = $grn_id;";
            $userNameQuery = $this->db->query($userNameSQL);
            foreach ($userNameQuery->getResult('array') as $row){
                array_push($data['grn_total_cost'],$row['TCOST']);
            }
        }


        echo view('grn/search',$data);
            
    }

    public function dl($rpt_type = ''){
        $data = $this->data;


        $generatefile = new GenerateFile($this->db);
        $generatefile->output = "download";

        if($rpt_type == 'grnlist'){
           
            $generatefile->generate_grnlist($data);
            exit;
        }
    }
    
}
    