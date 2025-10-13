<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Json library
* @class Warehouse_controller
* @version 07/05/2015 12:18:00
*/
class Ba_penerimaan_pengembalian_bbm {

    function create() {
		
		// var_dump($_POST);DIE();
		
        $ci = & get_instance();
		$ci->load->model('dokumen/dokumen_cetak');
		$table= $ci->dokumen_cetak;
		
		$nomor_suratx = str_replace(" ","",$_POST['nomor_surat']);
		
		$sql = "SELECT COUNT(*) AS jml FROM bbm_kapaltrans WHERE nomor_surat = '".$nomor_suratx."' "; 
		$query = $ci->db->query($sql);
		foreach ($query->result() as $r){
			$jml = $r->jml;
		}
		
		$filename   = uniqid(6)."-".time();
		$nomor_surat = str_replace('/','_',$_POST['nomor_surat']);
		
		$volume_pengisian = str_replace(',','',$_POST['volume_pengisian']);
		$volume_sebelum = str_replace(',','',$_POST['volume_sebelum']);
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
		
		if(isset($_POST['an_nakhoda_temp'])){
			$an_nakhoda_temp = $_POST['an_nakhoda_temp'];
		}else{
			$an_nakhoda_temp = 0;
		}
		
		if(isset($_POST['an_kkm_temp'])){
			$an_kkm_temp = $_POST['an_kkm_temp'];
		}else{
			$an_kkm_temp = 0;
		}
		
		if($jml == 0){
		    
    		$q = $ci->db->query("INSERT INTO bbm_kapaltrans(
    				kapal_code			 ,
    				nomor_surat          ,
    				tanggal_surat        ,
    				jam_surat            ,
    				zona_waktu_surat     ,
    				lokasi_surat         ,
    				volume_pengisian      ,
    				volume_sebelum      ,
    				volume_sisa         ,
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
					an_kkm     			 ,
					kapal_code_temp  ,
					keterangan_jenis_bbm ,
					sebab_temp,
					pangkat_nahkoda		,
					nama_nahkoda_temp	,
					pangkat_nahkoda_temp	,
					nip_nahkoda_temp	,
					nama_kkm_temp	,
					nip_kkm_temp	,
					an_nakhoda_temp,
					an_kkm_temp,
					status_temp,
					link_modul_ba,
                    link_modul_temp
    
    
    			) VALUES (
    				'".$_POST['code_kapal']."',
    				'".$nomor_suratx."',
    				'".$tanggal_surat."',
    				'".$_POST['jam_surat']."',
    				'".$_POST['zona_waktu_surat']."',
    				'".$_POST['lokasi_surat']."',
    				".$volume_pengisian.",
    				".$volume_sebelum.",
    				".$volume_sisa.",
    				'13',
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
					".$an_kkm.",
					'".$_POST['kapal_code_temp']."',
					'".$_POST['keterangan_jenis_bbm']."',
					'".$_POST['sebab_temp']."',
					'".$_POST['pangkat_nahkoda']."',
					'".$_POST['nama_nahkoda_temp']."',
					'".$_POST['pangkat_nahkoda_temp']."',
					'".$_POST['nip_nahkoda_temp']."',
					'".$_POST['nama_kkm_temp']."',
					'".$_POST['nip_kkm_temp']."',
					".$an_kkm_temp.",
					".$an_kkm_temp.",
					0,
					'".$_POST['link_ba']."',
					'".$_POST['link_modulTemp']."'
    			)
    		");
    		
    		$result = array('rows' => array(), 'message' => '', 'success' => false);
    		
    		if($q){
    			
    			$Up = $ci->db->query("UPDATE bbm_kapaltrans SET
        			            link_modul_ba = '".$_POST['nomor_surat']."'
        				
        			WHERE nomor_surat = '".$_POST['link_ba']."' ");
        			
				$ci->db->query("UPDATE bbm_kapaltrans SET
									status_temp	= 1		
								WHERE trans_id = ".$_POST['trans_idPeminjam']." ");
				
    			$table->cetak_ba_penerimaan_pengembalian_bbm($nomor_surat,$filename);
    			
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
		
		// var_dump($_POST);DIE();
		
        $ci = & get_instance();
		$ci->load->model('dokumen/dokumen_cetak');
		$table= $ci->dokumen_cetak;
		
		$filename   = uniqid(6)."-".time();
		$nomor_surat = str_replace('/','_',$_POST['nomor_surat']);
		$volume_pengisian = str_replace(',','',$_POST['volume_pengisian']);
		$volume_sebelum = str_replace(',','',$_POST['volume_sebelum']);
		$volume_sisa = str_replace(',','',$_POST['volume_sisa']);
		
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
		
		if(isset($_POST['an_nakhoda_temp'])){
			$an_nakhoda_temp = $_POST['an_nakhoda_temp'];
		}else{
			$an_nakhoda_temp = 0;
		}
		
		if(isset($_POST['an_kkm_temp'])){
			$an_kkm_temp = $_POST['an_kkm_temp'];
		}else{
			$an_kkm_temp = 0;
		}
		
		$q = $ci->db->query("UPDATE bbm_kapaltrans SET
				
				jam_surat            = '".$_POST['jam_surat']."',
				zona_waktu_surat     = '".$_POST['zona_waktu_surat']."',
				jabatan_staf_pangkalan   = '".$_POST['jabatan_staf_pangkalan']."',
				nama_staf_pagkalan   = '".$_POST['nama_petugas']."',
				nip_staf             = '".$_POST['nip_petugas']."',
				nama_nahkoda         = '".$_POST['nama_nakoda']."',
				pangkat_nahkoda         = '".$_POST['pangkat_nahkoda']."',
				nip_nahkoda          = '".$_POST['nip_nakoda']."',
				nama_kkm             = '".$_POST['nama_kkm']."',
				nip_kkm              = '".$_POST['nip_kkm']."',
				an_staf              = ".$an_staf.",
				an_nakhoda           = ".$an_nakhoda.",
				an_kkm               = ".$an_kkm.",
				keterangan_jenis_bbm = '".$_POST['keterangan_jenis_bbm']."',
				sebab_temp	= '".$_POST['sebab_temp']."',
				an_nakhoda_temp	= ".$an_nakhoda_temp.",
				an_kkm_temp = ".$an_kkm_temp."
				
			WHERE trans_id = ".$_POST['trans_id']." ");
		
		$result = array('rows' => array(), 'message' => '', 'success' => false);
		
		if($q){
			
			$table->cetak_ba_penerimaan_pengembalian_bbm($nomor_surat,$filename);
			
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
	
}

/* End of file Warehouse_controller.php */