<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Json library
* @class Users_controller
* @version 07/05/2015 12:18:00
*/
class Users_controller{

    function updateProfile() {
		
	
        $ci = & get_instance();
		
		$new_pass = md5($_POST['password']);
		
	    $cek = $ci->db->query("SELECT * FROM conf_user WHERE conf_user_id = ".$_POST['userid']." ");
		$row = $cek->row();
		
	    if($row->password == md5($_POST['passwordlama'])){
	        
	        $q = $ci->db->query("UPDATE conf_user SET
								password	    = '".$new_pass."',
    								date_update     = NOW(),
    								user_update     = '".$ci->session->userdata('username')."'
    							WHERE conf_user_id  = ".$_POST['userid']." ");
    		
    		$result = array('rows' => array(), 'message' => '', 'success' => false);
    		
    		if($q){
    			
    			$data['rows'] = '';
    			$data['success'] = true;
    			$data['message'] = 'Pasword Berhasil di Rubah';
    		
    		}else{
    			
    			$data['rows'] 	 = '';
    			$data['success'] = false;
    			$data['message'] = 'Pasword Gagal di Rubah';
    		
    		}
	        
	    }else{
	        
	        $data['rows'] = '';
    		$data['success'] = false;
    		$data['message'] = 'Pasword lama tidak sesuai';
	        
	    }
		
		
		
		return $data;
       
    }
}

/* End of file Users_controller.php */