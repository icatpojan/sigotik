<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Json library
* @class Warehouse_controller
* @version 07/05/2015 12:18:00
*/
class Master_user {

    function simpan() {

        $ci = & get_instance();
		// var_dump($_POST);die();
		$cek = $ci->db->query("SELECT * FROM stm_user WHERE username = '".$_POST['username']."'")->num_rows();
		if($cek > 0){
			$q = $ci->db->query("UPDATE stm_user SET
				id__stm_petugas	= '".$_POST['petugas']."',
				id__stm_group	= '".$_POST['group']."',
				email			= '".$_POST['email']."',
				is_active		= '".$_POST['status_user']."'
				WHERE username = '".$_POST['username']."'
			");
		}else{
			$password = md5('12345');
			$q = $ci->db->query("INSERT INTO stm_user(
					username,
					password,
					id__stm_petugas,
					id__stm_group,
					email,
					is_active
				) VALUES (
					'".$_POST['username']."',
					'".$password."',
					'".$_POST['petugas']."',
					'".$_POST['group']."',
					'".$_POST['email']."',
					'".$_POST['status_user']."'
				)
			");
		}
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
		$q = $ci->db->query("UPDATE stm_user SET(
				id__stm_petugas	= '".$_POST['petugas']."',
				id__stm_group	= '".$_POST['group']."',
				email			= '".$_POST['email']."',
				is_active		= '".$_POST['status_user']."'
				WHERE username = '".$_POST['username']."'
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
		$q = $ci->db->query("DELETE FROM stm_user WHERE username = '".$_POST['username']."' ");
		
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