<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Json library
* @class Warehouse_controller
* @version 07/05/2015 12:18:00
*/
class Role {

    function create() {

        $ci = & get_instance();
		// var_dump($_POST);die();
		
		$q = $ci->db->query("INSERT INTO conf_group(
				`group`	  
			) VALUES (
				'".$_POST['role']."'
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

    function update() {
		
        $ci = & get_instance();
		
		$q = $ci->db->query("UPDATE conf_group SET
								`group` = '".$_POST['role']."'
							WHERE conf_group_id = ".$_POST['id']." ");
		
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
		$q = $ci->db->query("DELETE FROM conf_group WHERE conf_group_id = ".$_POST['conf_group_id']." ");
		
		
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

	function insertos(){
		$ci = & get_instance();
		// var_dump($_POST);die;
		$jml = count($_POST['id_stm_menuv2']);
		$del = $ci->db->query("DELETE FROM conf_role_menu WHERE conf_group_id = '".$_POST['conf_group_id']."'");
		for($i=0;$i<$jml;$i++){
			$sql = $ci->db->query("	INSERT INTO conf_role_menu (
										conf_group_id, 
										stm_menu_id
									)VALUES(
										'".$_POST['conf_group_id']."',
										'".$_POST['id_stm_menuv2'][$i]."'
									)");
			
		}
		
		$result = array('rows' => array(), 'message' => '', 'success' => false);
		if($sql){
			$data['rows'] 	 = '';
			$data['success'] = false;
			$data['message'] = 'Data Berhasil Di Simpan';
		
		}else{
			
			$data['rows'] 	 = '';
			$data['success'] = true;
			$data['message'] = 'Data Gagal Di Simpan';
		}
		
		return $data;
		
	}
	


}

/* End of file Warehouse_controller.php */