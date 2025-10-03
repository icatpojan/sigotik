<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Json library
* @class Warehouse_controller
* @version 07/05/2015 12:18:00
*/
class Ba_upload {

    function upload() {
		
		$filename   = uniqid()."-".time();
		$extension  = pathinfo( $_FILES["my_images"]["name"][0], PATHINFO_EXTENSION );
		$basename   = $filename . '.' . $extension;
        
        
		$source       = $_FILES["my_images"]["tmp_name"][0];
		$destination  = $_SERVER['DOCUMENT_ROOT'].'/sigotik_bbm/dokumen/dokumen_up_ba/'.$basename;

		move_uploaded_file($source, $destination);
		
		//$nomor_surat = str_replace('_','/',$_POST['nomor_surat']);
		
        $ci = & get_instance();
		
		$q = $ci->db->query("UPDATE bbm_kapaltrans SET
				file_upload = '".$basename."',
				status_upload = '1'
			WHERE trans_id = '".$_POST['trans_id']."'
		");
		
		$result = array('rows' => array(), 'message' => '', 'success' => false);
		
		if($q){
			
			$data['rows'] = '';
			$data['success'] = true;
			$data['message'] = 'Upload Berhasil';
		
		}else{
			
			$data['rows'] 	 = '';
			$data['success'] = false;
			$data['message'] = 'Upload Gagal';
		
		}
		
		return $data;

    }
	
}

/* End of file Warehouse_controller.php */