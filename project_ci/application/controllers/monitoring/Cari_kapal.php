<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cari_kapal extends CI_Controller {
    
    function __construct() {        
		
		
		parent::__construct();
		
		$this->load->library('pdf');
	}
	
	
	public function getData(){
		$ci = & get_instance();
		if($_POST['m_upt_code'] == '000' or $_POST['m_upt_code'] == '0') {
		$sql = "SELECT  m_kapal_id,code_kapal,nama_kapal
				FROM m_kapal "; 
		} else {	
		$sql = "SELECT  m_kapal_id,code_kapal,nama_kapal
				FROM m_kapal WHERE m_upt_code = ".$_POST['m_upt_code']." "; 
		}		
		$query = $ci->db->query($sql);
		
		/*$data = array();
		foreach ($query->result() as $list){
			
			$data['code_kapal'] = $list->code_kapal;
			$data['nama_kapal'] = $list->nama_kapal;
			
		}*/
        if ($query->num_rows() <> 0) {
            $rows['data_cpa'] = $query->result();
			//$result = $this->db->where("state_id",$id)->get("demo_cities")->result();
			$result = $query->result();
			echo json_encode($rows);
            //return $rows;
        } else {
            return false;
        }
		
		//echo json_encode($data);
	
	}
	
}
