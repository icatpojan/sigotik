<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Json library
* @class Warehouse_controller
* @version 07/05/2015 12:18:00
*/
class Menu {

    function create() {

        $ci = & get_instance();
		// var_dump($_POST);die();
		
		$q = $ci->db->query("INSERT INTO `stm_menuv2`(
				id_parentmenu,
				level,
				menu,
				linka,
				icon,
				urutan
			) VALUES (
				NULLIF('".$_POST['id_parentmenu']."',''),
				'".$_POST['level']."',
				'".$_POST['menu']."',
				NULLIF('".$_POST['linka']."',''),
				NULLIF('".$_POST['icon']."',''),
				'".$_POST['urutan']."'
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
		
		$q = $ci->db->query("UPDATE `stm_menuv2` SET
								id_parentmenu	= NULLIF('".$_POST['id_parentmenu']."',''),
								level			= '".$_POST['level']."',
								menu			= '".$_POST['menu']."',
								linka			= NULLIF('".$_POST['linka']."',''),
								icon			= NULLIF('".$_POST['icon']."',''),
								urutan			= '".$_POST['urutan']."'
							WHERE id = '".$_POST['id']."' ");
		
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
		$q = $ci->db->query("DELETE FROM `stm_menuv2` WHERE id = ".$_POST['id']." ");
		
		
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