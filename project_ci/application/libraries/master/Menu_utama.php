<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Json library
* @class Warehouse_controller
* @version 07/05/2015 12:18:00
*/
class Menu_utama {

    function simpan() {

        $ci = & get_instance();
		// var_dump($_POST);die();
		$cek = $ci->db->query("SELECT * FROM stm_menu WHERE id = '".$_POST['id']."'")->num_rows();
		if($cek > 0){
			$q = $ci->db->query("UPDATE stm_menu SET
				id__stm_group	= '".$_POST['group']."',
				menu			= '".$_POST['menu']."',
				linka			= NULLIF('".$_POST['linka']."', ''),
				sub_id			= '".$_POST['sub_id']."',
				icon			= '".$_POST['icon']."',
				urutan			= '".$_POST['urutan']."'
				WHERE id = '".$_POST['id']."'
			");
		}else{
			$q = $ci->db->query("INSERT INTO stm_menu(
					id__stm_group,
					menu,
					linka,
					sub_id,
					icon,
					urutan
				) VALUES (
					'".$_POST['group']."',
					'".$_POST['menu']."',
					NULLIF('".$_POST['linka']."', ''),
					'".$_POST['sub_id']."',
					'".$_POST['icon']."',
					'".$_POST['urutan']."'
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
		$q = $ci->db->query("DELETE FROM stm_menu WHERE id = '".$_POST['id']."' ");
		
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