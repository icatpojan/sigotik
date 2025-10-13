<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Json library
* @class Warehouse_controller
* @version 07/05/2015 12:18:00
*/
class Ba_sisa_sblm_pengisian {

    function create() {
		
		
		
        $ci = & get_instance();
		$ci->load->model('dokumen/dokumen_cetak');
		$table= $ci->dokumen_cetak;
		
		$nomor_suratx = str_replace(" ","",$_POST['nomor_surat']);
		
		$sql = "SELECT COUNT(*) AS jml FROM bbm_kapaltrans WHERE nomor_surat = '".$nomor_suratx."' "; 
		$query = $ci->db->query($sql);
		foreach ($query->result() as $r){
			$jml = $r->jml;
		}
		
		$filename   = uniqid(2)."-".time();
		$volume_sisa = str_replace(',','',$_POST['volume_sisa']);
		$nomor_surat = str_replace('/','_',$_POST['nomor_surat']);
		$tanggal_surat = date("Y-m-d", strtotime($_POST['tanggal_surat']));
		
		if(isset($_POST['an_staf'])){
			$an_staf = $_POST['an_staf'];
		}else{
			$an_staf = 0;
		}
		
		if(isset($_POST['an_nakhoda'])){
			$an_nakhoda = $_POST['an_nakhoda'];
		}else{
			$an_nakhoda = 0;
		}
		
		if(isset($_POST['an_kkm'])){
			$an_kkm = $_POST['an_kkm'];
		}else{
			$an_kkm = 0;
		}
		
		if($jml == 0){
		    
    		   $q = $ci->db->query("INSERT INTO bbm_kapaltrans(
    				kapal_code			 ,
    				nomor_surat          ,
    				tanggal_surat        ,
    				zona_waktu_surat     ,
    				jam_surat            ,
    				lokasi_surat         ,
    				volume_sisa          ,
    				status_ba            ,
    				jabatan_staf_pangkalan,
    				nama_staf_pagkalan   ,
    				nip_staf             ,
    				nama_nahkoda         ,
    				nip_nahkoda          ,
    				nama_kkm             ,
    				nip_kkm              ,
    				tanggalinput         ,
    				user_input           ,
					an_staf				 ,
					an_nakhoda			 ,
					an_kkm   					
    
    
    			) VALUES (
    				'".$_POST['code_kapal']."',
    				'".$nomor_suratx."',
    				'".$tanggal_surat."',
    				'".$_POST['zona_waktu_surat']."',
    				'".$_POST['jam_surat']."',
    				'".$_POST['lokasi_surat']."',
    				".$volume_sisa.",
    				'2',
    				'".$_POST['jabatan_staf_pangkalan']."',
    				'".$_POST['nama_petugas']."',
    				'".$_POST['nip_petugas']."',
    				'".$_POST['nama_nakoda']."',
    				'".$_POST['nip_nakoda']."',
    				'".$_POST['nama_kkm']."',
    				'".$_POST['nip_kkm']."',
    				NOW(),
    				'".$ci->session->userdata('userid')."',
					".$an_staf.",
					".$an_nakhoda.",
					".$an_kkm."
					
    			)
    		");
        		
        	$result = array('rows' => array(), 'message' => '', 'success' => false);
    		
        	if($q){
        			
    			$table->cetak_ba_sisa_sblm_pengisian($nomor_surat,$filename);
    			
    			$data['rows'] = '';
    			$data['success'] = true;
    			$data['message'] = $filename;
    		
    		}else{
    			
    			$data['rows'] 	 = '';
    			$data['success'] = false;
    			$data['message'] = 'Dokumen Gagal Dibuat';
    		}
		    
		}else{
		    
	        $result = array('rows' => array(), 'message' => '', 'success' => false);
	    
	        $data['rows'] 	 = '';
			$data['success'] = false;
			$data['message'] = 'Nomor Surat Telah Ada';
		    
		}
		
		
		return $data;

    }
    
    function edit() {
		
        $ci = & get_instance();
		$ci->load->model('dokumen/dokumen_cetak');
		$table= $ci->dokumen_cetak;
		
		$filename   = uniqid(2)."-".time();
		$nomor_surat = str_replace('/','_',$_POST['nomor_surat']);
		$volume_sisa = str_replace(',','',$_POST['volume_sisa']);
		
		$tanggal_surat = date("Y-m-d", strtotime($_POST['tanggal_surat']));
		
		if(isset($_POST['an_staf'])){
			$an_staf = $_POST['an_staf'];
		}else{
			$an_staf = 0;
		}
		
		if(isset($_POST['an_nakhoda'])){
			$an_nakhoda = $_POST['an_nakhoda'];
		}else{
			$an_nakhoda = 0;
		}
		
		if(isset($_POST['an_kkm'])){
			$an_kkm = $_POST['an_kkm'];
		}else{
			$an_kkm = 0;
		}
		
		$q = $ci->db->query("UPDATE bbm_kapaltrans SET
				tanggal_surat        = '".$tanggal_surat."',
				jam_surat            = '".$_POST['jam_surat']."',
				zona_waktu_surat     = '".$_POST['zona_waktu_surat']."',
				lokasi_surat     = '".$_POST['lokasi_surat']."',
				volume_sisa          = ".$volume_sisa.",
				jabatan_staf_pangkalan   = '".$_POST['jabatan_staf_pangkalan']."',
				nama_staf_pagkalan   = '".$_POST['nama_petugas']."',
				nip_staf             = '".$_POST['nip_petugas']."',
				nama_nahkoda         = '".$_POST['nama_nakoda']."',
				nip_nahkoda          = '".$_POST['nip_nakoda']."',
				nama_kkm             = '".$_POST['nama_kkm']."',
				nip_kkm              = '".$_POST['nip_kkm']."',
				an_staf              = ".$an_staf.",
				an_nakhoda           = ".$an_nakhoda.",
				an_kkm               = ".$an_kkm."
				
			WHERE trans_id = ".$_POST['trans_id']." ");
		
		$result = array('rows' => array(), 'message' => '', 'success' => false);
		
		if($q){
			
			$table->cetak_ba_sisa_sblm_pengisian($nomor_surat,$filename);
			
			$data['rows'] = '';
			$data['success'] = true;
			$data['message'] = $filename;
		
		}else{
			
			$data['rows'] 	 = '';
			$data['success'] = false;
			$data['message'] = 'Dokumen Gagal Dibuat';
		
		}
		
		return $data;

    }
    
    function destroy() {
        
		$ci = & get_instance();
		
		$sql = "SELECT nomor_surat, link_modul_ba,  link_modul_temp, status_ba, status_temp FROM bbm_kapaltrans WHERE trans_id = ".$_POST['trans_id']." "; 
		$query = $ci->db->query($sql);
		foreach ($query->result() as $r){
			$nomor_surat = $r->nomor_surat;
        	$link_modul_ba = $r->link_modul_ba;
        	$status_ba = $r->status_ba;
			$status_temp = $r->status_temp;
			$link_modul_temp = $r->link_modul_temp;
		}
  
    
    	if($status_ba == 9 || $status_ba == 3 || $status_ba == 14 || $status_ba == 10 || $status_ba == 11 || $status_ba == 12 || $status_ba == 13 || $status_ba == 16 || $status_ba == 17 || $status_ba == 18 ){
			
			$qUpdate = $ci->db->query("UPDATE bbm_kapaltrans SET
					link_modul_ba        = ''
				WHERE nomor_surat = '".$link_modul_ba."' ");
				
        }
		// var_dump();die();
		if($status_ba == 15 || $status_ba == 11 || $status_ba == 13){
			
			$qUpdate = $ci->db->query("UPDATE bbm_kapaltrans SET
					status_temp        = 0
				WHERE nomor_surat = '".$link_modul_temp."' ");
			
        }else if($status_ba == 12){
		
				$qUpdate = $ci->db->query("UPDATE bbm_kapaltrans SET
					status_temp        = 1
				WHERE nomor_surat = '".$link_modul_temp."' ");
		}
		
		if($status_ba == 14 || $status_ba == 10 || $status_ba == 12){
			
			if($status_temp == 1){
				
				$data['rows'] 	 = '';
				$data['success'] = false;
				$data['message'] = 'Harap Hapus Ba penerima Terlebih Dahulu';
				
			}else{
				
				$q = $ci->db->query("DELETE FROM bbm_kapaltrans WHERE trans_id = ".$_POST['trans_id']." ");
				
				if($q){
		    
					$data['rows'] 	 = '';
					$data['success'] = false;
					$data['message'] = 'Data Berhasil Di Hapus';
				
				}else{
					
					$data['rows'] 	 = '';
					$data['success'] = true;
					$data['message'] = 'Data Gagal Di Hapus';
				
				}
				
			}
			
		}else{
		
			$q = $ci->db->query("DELETE FROM bbm_kapaltrans WHERE trans_id = ".$_POST['trans_id']." ");
			
			if($q){
		    
				$data['rows'] 	 = '';
				$data['success'] = false;
				$data['message'] = 'Data Berhasil Di Hapus';
			
			}else{
				
				$data['rows'] 	 = '';
				$data['success'] = true;
				$data['message'] = 'Data Gagal Di Hapus';
			
			}
			
		}
		
		$result = array('rows' => array(), 'message' => '', 'success' => false);
		
		
		return $data;
		
    }

	function realese() {
        
        
		$ci = & get_instance();
		
		$sql = "SELECT status_upload,file_upload FROM bbm_kapaltrans WHERE trans_id = ".$_POST['trans_id']." "; 
		$query = $ci->db->query($sql);
		foreach ($query->result() as $r){
			$status_upload = $r->status_upload;
        	$file_upload = $r->file_upload;
		}
    	
    	$Path = $_SERVER['DOCUMENT_ROOT'].'/sigotik_bbm/dokumen/dokumen_up_ba/'.$file_upload;
    	//var_dump($Path);die();
		if (unlink($Path)) {    
		   
        	
		} else {
 		     
        	
		}
    
    	$qUpdate = $ci->db->query("UPDATE bbm_kapaltrans SET
				status_upload        = '0',
                file_upload          = ''
			WHERE trans_id = ".$_POST['trans_id']." ");
		
		$result = array('rows' => array(), 'message' => '', 'success' => false);
		if($qUpdate){
		    
			$data['rows'] 	 = '';
			$data['success'] = false;
			$data['message'] = 'Data Berhasil Di Release';
		
		}else{
			
			$data['rows'] 	 = '';
			$data['success'] = true;
			$data['message'] = 'Data Gagal Di Release';
		
		}
		
		return $data;
		
    }
	
}

/* End of file Warehouse_controller.php */