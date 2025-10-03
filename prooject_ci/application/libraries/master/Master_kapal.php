<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Json library
* @class Warehouse_controller
* @version 07/05/2015 12:18:00
*/
class Master_kapal {

    function create() {

        $ci = & get_instance();
		
		$sts = true;
		$q   = false;
		
		if(isset($_FILES["lamp"]["name"][0])){
			$filename   = uniqid()."-".time();
			$extension  = pathinfo( $_FILES["lamp"]["name"][0], PATHINFO_EXTENSION );
			$basename   = $filename . '.' . $extension;
			$extp = array("jpg","png");
			
			$source       = $_FILES["lamp"]["tmp_name"][0];
			$destination  = $_SERVER['DOCUMENT_ROOT'].'/sigotik_bbm/dokumen/kapal_detail/' . $basename;
			
				if(!in_array($extension,$extp)) {
					$sts = false;
				}
		}else{
			$basename   = '';
		}
		
		if(isset($_FILES["lamp"]["name"][1])){
			$filename2   = uniqid()."-".time();
			$extension2  = pathinfo( $_FILES["lamp"]["name"][1], PATHINFO_EXTENSION );
			$basename2   = $filename2 . '.' . $extension2;
			$extp2 = array("pdf");
			
			$source2       = $_FILES["lamp"]["tmp_name"][1];
			$destination2  = $_SERVER['DOCUMENT_ROOT'].'/sigotik_bbm/dokumen/lampiran_kapal/' . $basename2;
			
				if(!in_array($extension2,$extp2)) {
					$sts = false;
				}
		}else{
			$basename2   = '';
		}
		
		
		if($sts){
    		$q = $ci->db->query("INSERT INTO m_kapal(
    				nama_kapal			,
    				code_kapal			,
    				m_upt_code          ,
    				panjang             ,
    				bobot               ,
    				tinggi              ,
    				lebar               ,
    				gerak_engine        ,
    				main_engine         ,
    				jml_main_engine     ,
    				pk_main_engine      ,
    				aux_engine_utama    ,
    				jml_aux_engine_utama,
    				pk_aux_engine_utama ,
    				aux_engine_emergency,
    				galangan_pembuat    ,
    				kapasitas_tangki    ,
    				tahun_buat          ,
    				jml_abk             ,
    				jml_tangki             ,
    				nama_nakoda         ,
    				nip_nakoda          ,
    				jabatan_nakoda      ,
    				pangkat_nakoda      ,
    				nama_kkm            ,
    				nip_kkm             ,
    				jabatan_kkm         ,
    				pangkat_kkm         ,
    				date_insert         ,
    				user_insert         ,
                    gambar_kapal		,
                    lampiran_kapal		
                    
    			) VALUES (
    				'".$_POST['nama_kapal']."',
    				'".$_POST['code_kapal']."',
    				'".$_POST['m_upt_code']."',
    				NULLIF('".$_POST['panjang']."','0'),
    				NULLIF('".$_POST['bobot']."','0'),
    				NULLIF('".$_POST['tinggi']."','0'),
    				NULLIF('".$_POST['lebar']."','0'),
    				'".$_POST['gerak_engine']."',
    				'".$_POST['main_engine']."',
    				NULLIF('".$_POST['jml_main_engine']."','0'),
    				'".$_POST['pk_main_engine']."',
    				'".$_POST['aux_engine_utama']."',
    				NULLIF('".$_POST['jml_aux_engine_utama']."','0'),
    				'".$_POST['pk_aux_engine_utama']."',
    				'".$_POST['aux_engine_emergency']."',
    				'".$_POST['galangan_pembuat']."',
    				NULLIF('".$_POST['kapasitas_tangki']."','0'),
    				NULLIF('".$_POST['tahun_buat']."','0'),
    				NULLIF('".$_POST['jml_abk']."','0'),
    				NULLIF('".$_POST['jml_tangki']."','0'),
    				'".$_POST['nama_nakoda']."',
    				'".$_POST['nip_nakoda']."',
    				'".$_POST['jabatan_nakoda']."',
    				'".$_POST['pangkat_nakoda']."',
    				'".$_POST['nama_kkm']."',
    				'".$_POST['nip_kkm']."',
    				'".$_POST['jabatan_kkm']."',
    				'".$_POST['pangkat_kkm']."',
    				NOW(),
    				'".$ci->session->userdata('username')."',
    				'".$basename."',
    				'".$basename2."'
    			)
    		");
		}
		
		$result = array('rows' => array(), 'message' => '', 'success' => false);
		if($q){
			
			if(isset($_FILES["lamp"]["name"][0])){

				move_uploaded_file($source, $destination);
			}
			
			if(isset($_FILES["lamp"]["name"][1])){

				move_uploaded_file($source2, $destination2);
			}
			
			$data['rows'] = '';
			$data['success'] = true;
			$data['message'] = 'Data Berhasil Tersimpan';
		
		}else{
			
			if($sts){
				
				$data['rows'] 	 = '';
				$data['success'] = false;
				$data['message'] = 'Data Gagal Tersimpan';
				
			}else{
				
				$data['rows'] 	 = '';
				$data['success'] = false;
				$data['message'] = 'Format File Upload Salah';
				
			}
		
		}
		
		return $data;

    }

    function update() {

        $ci = & get_instance();
    	
    	$sts = true;
		$q   = false;
		
		if(isset($_FILES["lamp"]["name"][0])){
			$filename   = uniqid()."-".time();
			$extension  = pathinfo( $_FILES["lamp"]["name"][0], PATHINFO_EXTENSION );
			$basename   = $filename . '.' . $extension;
			$extp = array("jpg","png");
			
			$source       = $_FILES["lamp"]["tmp_name"][0];
			$destination  = $_SERVER['DOCUMENT_ROOT'].'/sigotik_bbm/dokumen/kapal_detail/' . $basename;
			
				if(!in_array($extension,$extp)) {
					$sts = false;
				}
		}else{
		    $q = $ci->db->query("SELECT gambar_kapal FROM m_kapal WHERE m_kapal_id = ".$_POST['m_kapal_id']." ");
			$r = $q->row_array();
			$basename = $r['gambar_kapal'];
			
		}
		
		if(isset($_FILES["lamp"]["name"][1])){
			$filename2   = uniqid()."-".time();
			$extension2  = pathinfo( $_FILES["lamp"]["name"][1], PATHINFO_EXTENSION );
			$basename2   = $filename2 . '.' . $extension2;
			$extp2 = array("pdf");
			
			$source2       = $_FILES["lamp"]["tmp_name"][1];
			$destination2  = $_SERVER['DOCUMENT_ROOT'].'/sigotik_bbm/dokumen/lampiran_kapal/' . $basename2;
			
				if(!in_array($extension2,$extp2)) {
					$sts = false;
				}
		}else{
			$q = $ci->db->query("SELECT lampiran_kapal FROM m_kapal WHERE m_kapal_id = ".$_POST['m_kapal_id']." ");
			$r = $q->row_array();
			$basename2 = $r['lampiran_kapal'];
		}
		
		
		if($sts){
    		    $q = $ci->db->query("UPDATE m_kapal SET
    				nama_kapal 			= '".$_POST['nama_kapal']."',
    				code_kapal          = '".$_POST['code_kapal']."',		
    				m_upt_code          = '".$_POST['m_upt_code']."',
    				bobot               = NULLIF('".$_POST['bobot']."','0'),
    				panjang             = NULLIF('".$_POST['panjang']."','0'),
    				tinggi              = NULLIF('".$_POST['tinggi']."','0'),
    				lebar               = NULLIF('".$_POST['lebar']."','0'),
    				gerak_engine        = '".$_POST['gerak_engine']."',
    				main_engine         = '".$_POST['main_engine']."',
    				jml_main_engine     = NULLIF('".$_POST['jml_main_engine']."','0'),
    				pk_main_engine      = '".$_POST['pk_main_engine']."',
    				aux_engine_utama    = '".$_POST['aux_engine_utama']."',
    				jml_aux_engine_utama     = NULLIF('".$_POST['jml_aux_engine_utama']."','0'),
    				pk_aux_engine_utama      = '".$_POST['pk_aux_engine_utama']."',
    				aux_engine_emergency= '".$_POST['aux_engine_emergency']."',
    				galangan_pembuat    = '".$_POST['galangan_pembuat']."',
    				kapasitas_tangki    = NULLIF('".$_POST['kapasitas_tangki']."','0'),
    				tahun_buat          = NULLIF('".$_POST['tahun_buat']."','0'),
    				jml_abk             = NULLIF('".$_POST['jml_abk']."','0'),
    				jml_tangki          = NULLIF('".$_POST['jml_tangki']."','0'),
    				nama_nakoda         = '".$_POST['nama_nakoda']."',
    				nip_nakoda          = '".$_POST['nip_nakoda']."',
    				jabatan_nakoda      = '".$_POST['jabatan_nakoda']."',
    				pangkat_nakoda      = '".$_POST['pangkat_nakoda']."',
    				nama_kkm            = '".$_POST['nama_kkm']."',
    				nip_kkm             = '".$_POST['nip_kkm']."',
    				jabatan_kkm         = '".$_POST['jabatan_kkm']."',
    				pangkat_kkm         = '".$_POST['pangkat_kkm']."',
    				date_update         = NOW(),
    				user_update         = '".$ci->session->userdata('username')."',
    				gambar_kapal	    = '".$basename."',
    				lampiran_kapal	    = '".$basename2."'
    				
    				
    			WHERE m_kapal_id = ".$_POST['m_kapal_id']." ");
		}
		
		$result = array('rows' => array(), 'message' => '', 'success' => false);
		if($q){
			
			if(isset($_FILES["lamp"]["name"][0])){
              
				move_uploaded_file($source, $destination);
			}
			
			if(isset($_FILES["lamp"]["name"][1])){

				move_uploaded_file($source2, $destination2);
			}
			
			$data['rows'] = '';
			$data['success'] = true;
			$data['message'] = 'Data Berhasil Tersimpan';
		
		}else{
			
	    	if($sts){
				
				$data['rows'] 	 = '';
				$data['success'] = false;
				$data['message'] = 'Data Gagal Tersimpan';
				
			}else{
				
				$data['rows'] 	 = '';
				$data['success'] = false;
				$data['message'] = 'Format File Upload Salah';
				
			}
		
		}
		
		return $data;

    }

    function destroy() {
        
		$ci = & get_instance();
		$q = $ci->db->query("DELETE FROM m_kapal WHERE m_kapal_id = ".$_POST['m_kapal_id']." ");
		
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