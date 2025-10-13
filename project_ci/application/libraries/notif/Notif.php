<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Json library
* @class Warehouse_controller
* @version 07/05/2015 12:18:00
*/
class Notif {

    function create() {

        $ci = & get_instance();
		$jml = count($_POST['id_user']);
		for($i=0;$i<$jml;$i++){
			$user = $ci->db->query("SELECT * FROM conf_user WHERE conf_user_id = '".$_POST['id_user'][$i]."'")->row_array();
			$q = $ci->db->query("
				INSERT INTO dat_notif(
					conf_user_id,
					tujuan,
					subjek,
					pesan,
					dateins,
					status
				)VALUES(
					'".$_POST['id_user'][$i]."',
					'".$user['nama_lengkap']."',
					'".$_POST['subjek']."',
					'".$_POST['pesan']."',
					'".date('Y-m-d H:i:s')."',
					'0'
				)
				
			");
			// var_dump($q);
		}
			// die;
		
		$result = array('rows' => array(), 'message' => '', 'success' => false);
		
		
		
		if($q){
			
			$data['rows'] = '';
			$data['success'] = true;
			$data['message'] = 'Notifikasi Terkirim';
		
		}else{
			
			$data['rows'] 	 = '';
			$data['success'] = false;
			$data['message'] = 'Notifikasi Tidak Terkirim';
		
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