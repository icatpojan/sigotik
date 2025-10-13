<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Json library
* @class Warehouse_controller
* @version 07/05/2015 12:18:00
*/
class Master_group {

    function simpan() {

        $ci = & get_instance();
		// var_dump($_POST);die();
			$q = $ci->db->query("INSERT INTO stm_group(
					`group`
				) VALUES (
					'".$_POST['group']."'
				)
			");
		
		$result = array('rows' => array(), 'message' => '', 'success' => false);
		if($q){
			
			$data['rows'] = '';
			$data['success'] = true;
			$data['message'] = 'Data Berhasil Tersimpan';
		
		}else{
			
			$data['rows'] 	 = '';
			$data['success'] = false;
			$data['message'] = 'Data Gagal Tersimpan';
		
		}
		
		return $data;

    }

    function destroy() {
        $ci = & get_instance();
		$q = $ci->db->query("DELETE FROM stm_group WHERE id = '".$_POST['id']."' ");
		
		$result = array('rows' => array(), 'message' => '', 'success' => false);
		if($q){
		
			$data['rows'] 	 = '';
			$data['success'] = false;
			$data['message'] = 'Data Berhasil Di Hapus';
		
		}else{
			
			$data['rows'] 	 = '';
			$data['success'] = true;
			$data['message'] = 'Data Gagal Di Hapus';
		
		}
		
		return $data;
		
    }
}

/* End of file Warehouse_controller.php */