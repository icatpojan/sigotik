<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
				'".$ci->session->userdata('username')."'
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
		
		$q = $ci->db->query("UPDATE conf_user SET
								username	    = '".$_POST['username']."',
								m_upt_code   	= '".$_POST['m_upt_code']."',
								conf_group_id 	= ".$_POST['conf_group_id'].",
								email           = '".$_POST['email']."',
								nama_lengkap    = '".$_POST['nama_lengkap']."',
								nip             = '".$_POST['nip']."',
								golongan        = '".$_POST['golongan']."',
								date_update = NOW(),
								user_update = '".$ci->session->userdata('username')."'
							WHERE conf_user_id = ".$_POST['conf_user_id']." ");
		
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
	
	function updateBox(){
		$ci = & get_instance();
		// var_dump($_POST);die;
		$jml = count($_POST['m_kapal_id']);
		$del = $ci->db->query("DELETE FROM sys_user_kapal WHERE conf_user_id = '".$_POST['conf_user_id']."'");
		for($i=0;$i<$jml;$i++){
			$sql = $ci->db->query("	INSERT INTO sys_user_kapal (
										conf_user_id, 
										m_kapal_id
									)VALUES(
										'".$_POST['conf_user_id']."',
										'".$_POST['m_kapal_id'][$i]."'
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
	
	function reset() {
        $ci = & get_instance();
		
		$new_pass = md5('12345'); 
		
		$q = $ci->db->query("UPDATE conf_user SET
								password	    = '".$new_pass."',
								date_update     = NOW(),
								user_update     = '".$ci->session->userdata('username')."'
							WHERE conf_user_id  = ".$_POST['conf_user_id']." ");
		
		$result = array('rows' => array(), 'message' => '', 'success' => false);
		
		if($q){
			
			$data['rows'] = '';
			$data['success'] = true;
			$data['message'] = 'Pasword Berhasil di Reset';
		
		}else{
			
			$data['rows'] 	 = '';
			$data['success'] = false;
			$data['message'] = 'Pasword Gagal di Reset';
		
		}
		
		return $data;

    }
}

/* End of file Warehouse_controller.php */