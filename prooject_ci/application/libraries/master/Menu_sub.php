<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Json library
* @class Warehouse_controller
* @version 07/05/2015 12:18:00
*/
class Menu_sub {

    function simpan() {

        $ci = & get_instance();
		// var_dump($_POST);die();
		$cek = $ci->db->query("SELECT * FROM stm_menu_sub WHERE id = '".$_POST['id']."'")->num_rows();
		if($cek > 0){
			$q = $ci->db->query("UPDATE stm_menu_sub SET
				id__stm_menu	= '".$_POST['menuutama']."',
				menu			= '".$_POST['menu']."',
				linka			= NULLIF('".$_POST['linka']."', ''),
				urutan			= '".$_POST['urutan']."'
				WHERE id = '".$_POST['id']."'
			");
		}else{
			$q = $ci->db->query("INSERT INTO stm_menu_sub(
					id__stm_menu,
					menu,
					linka,
					urutan
				) VALUES (
					'".$_POST['menuutama']."',
					'".$_POST['menu']."',
					NULLIF('".$_POST['linka']."', ''),
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
		$q = $ci->db->query("DELETE FROM stm_menu_sub WHERE id = '".$_POST['id']."' ");
		
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