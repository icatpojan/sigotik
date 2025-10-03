<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Json library
* @class Warehouse_controller
* @version 07/05/2015 12:18:00
*/
class User {

    function create() {

        $ci = & get_instance();
		// var_dump($_POST);die();
		
		$q = $ci->db->query("INSERT INTO conf_user(
				username	     ,
				password      ,
				m_upt_code    ,
				conf_group_id ,
				email         ,
				nama_lengkap  ,
				nip           ,
				golongan      ,
				date_insert   ,
				user_insert  
				
			) VALUES (
				'".$_POST['username']."',
				'".md5($_POST['password'])."',
				'".$_POST['m_upt_code']."',
				".$_POST['conf_group_id'].",
				'".$_POST['email']."',
				'".$_POST['nama_lengkap']."',
				'".$_POST['nip']."',
				'".$_POST['golongan']."',
				NOW(),
				'DEFAULT'
			)
		");
		
		$result = array('rows' => array(), 'message' => '', 'success' => false);
		
		
		
		if($q){
			
			$r_cek = $ci->db->query("SELECT conf_user_id FROM conf_user WHERE username = '".$_POST['username']."' ")->row_array();
			
			for($i = 0 ;$i<count($_POST['m_kapal_id']);$i++){
				
				$q = $ci->db->query("INSERT INTO sys_user_kapal(
						conf_user_id,
						m_kapal_id
					) VALUES (
						".$r_cek['conf_user_id'].",
						".$_POST['m_kapal_id'][$i]."
					)
				");
				
			}
			// $cek = "SELECT FROM "
			
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
		
		$q = $ci->db->query("UPDATE m_upt SET
								nama = '".$_POST['nama']."',
								code = '".$_POST['code']."',
								alamat1 = '".$_POST['alamat1']."',
								alamat2 = '".$_POST['alamat2']."',
								date_update = NOW(),
								user_update = 'DEFAULT'
							WHERE m_upt_id = ".$_POST['m_upt_id']." ");
		
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
		$q = $ci->db->query("DELETE FROM conf_user WHERE conf_user_id = ".$_POST['conf_user_id']." ");
		
		
		$result = array('rows' => array(), 'message' => '', 'success' => false);
		if($q){
			$q = $ci->db->query("DELETE FROM sys_user_kapal WHERE conf_user_id = ".$_POST['conf_user_id']." ");
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