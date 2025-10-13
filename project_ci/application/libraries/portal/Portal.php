<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Json library
* @class Warehouse_controller
* @version 07/05/2015 12:18:00
*/
class Portal {

    function saveup() {
		$ci = & get_instance();
		$author = $ci->session->userdata('user_realname');
		if($_POST['id'] != '0'){
			$q = $ci->db->query("UPDATE port_news SET
								news_title	= '".$_POST['news_title']."',
								news		= '".$_POST['news']."',
								kategori_id = '".$_POST['kategori_id']."',
								author		= '".$author."',
								date_create	= '".date('Y-m-d H:i:s')."',
								post		= '".$_POST['post']."'
							WHERE id = '".$_POST['id']."' ");
		}else{
			$q = $ci->db->query("
				INSERT INTO port_news(
					news_title,
					news,
					kategori_id,
					author,
					date_create,
					post
				)VALUES(
					'".$_POST['news_title']."',
					'".$_POST['news']."',
					'".$_POST['kategori_id']."',
					'".$author."',
					'".date('Y-m-d H:i:s')."',
					'".$_POST['post']."'
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
		$q = $ci->db->query("DELETE FROM `port_news` WHERE id = ".$_POST['id']." ");
		
		
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

	function upload() {
	
		$filename   = uniqid()."-".time();
		$extension  = pathinfo( $_FILES["images"]["name"][0], PATHINFO_EXTENSION );
		$basename   = $filename . '.' . $extension;

		$source       = $_FILES["images"]["tmp_name"][0];
		$destination  = $_SERVER['DOCUMENT_ROOT'].'/sigotik_bbm/images/' . $basename;

		move_uploaded_file($source, $destination);
		
		// $nomor_surat = str_replace('_','/',$_POST['nomor_surat']);
		
        $ci = & get_instance();
		
		$q = $ci->db->query("UPDATE port_news SET
				img = '".$basename."'
			WHERE id = '".$_POST['id']."'
		");
		
		$result = array('rows' => array(), 'message' => '', 'success' => false);
		
		if($q){
			
			$data['rows'] = '';
			$data['success'] = true;
			$data['message'] = 'Upload Berhasil';
		
		}else{
			
			$data['rows'] 	 = '';
			$data['success'] = false;
			$data['message'] = 'Upload Gagal';
		
		}
		
		return $data;

    }
	
}

/* End of file Warehouse_controller.php */