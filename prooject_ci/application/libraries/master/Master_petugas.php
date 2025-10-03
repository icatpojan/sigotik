<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Json library
* @class Warehouse_controller
* @version 07/05/2015 12:18:00
*/
class Master_petugas {

    function simpan() {

        $ci = & get_instance();
		// var_dump($_POST);die();
		$cek = $ci->db->query("SELECT * FROM stm_petugas WHERE id = '".$_POST['id']."'")->num_rows();
		if($cek > 0){
			$q = $ci->db->query("UPDATE stm_petugas SET
				nama				= '".$_POST['nama']."',
				nip					= '".$_POST['nip']."',
				jabatan				= '".$_POST['jabatan']."',
				id__stm_kepangkatan	= '".$_POST['pangkat']."',
				is_active			= '".$_POST['status_user']."'
				WHERE id = '".$_POST['id']."'
			");
		}else{
			$q = $ci->db->query("INSERT INTO stm_petugas(
					nama,
					nip,
					jabatan,
					id__stm_kepangkatan,
					is_active
				) VALUES (
					'".$_POST['nama']."',
					'".$_POST['nip']."',
					'".$_POST['jabatan']."',
					'".$_POST['pangkat']."',
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

    function destroy() {
        $ci = & get_instance();
		$q = $ci->db->query("DELETE FROM stm_petugas WHERE id = '".$_POST['id']."' ");
		
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