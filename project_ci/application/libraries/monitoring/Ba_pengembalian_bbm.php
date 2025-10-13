<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Json library
* @class Warehouse_controller
* @version 07/05/2015 12:18:00
*/
class Ba_pengembalian_bbm {

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
		
		$nomor_surat = str_replace('/','_',$_POST['nomor_surat']);
		$filename   = uniqid(9)."-".time();
		$penggunaan = str_replace(',','',$_POST['penggunaan']);
		
		$tanggal_surat = date("Y-m-d", strtotime($_POST['tanggal_surat']));
   		
    	$volume_sebelum   = str_replace(',','',$_POST['volume_sebelum']);
    	$tanggal_sebelum  = date("Y-m-d", strtotime($_POST['tanggal_surat_penitip']));
    	$volume_pemakaian = str_replace(',','',$_POST['volume_pemakaian']);
		
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
    				jam_surat            ,
    				zona_waktu_surat     ,
    				lokasi_surat         ,
    
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
    				penyedia_penitip        ,
    				nama_penitip            ,
    				jabatan_penitip         ,
    				alamat_penitip          ,  
    				penggunaan              ,
    				alamat_penyedia_penitip ,
    				keterangan_jenis_bbm    ,
    				link_modul_ba			,
                    volume_sebelum			,
                    tanggal_sebelum			,
                    volume_pemakaian		,
					an_staf				 	,
					an_nakhoda			 	,
					an_kkm     
    
    
    			) VALUES (
    				'".$_POST['code_kapal']."',
    				'".$nomor_suratx."',
    				'".$tanggal_surat."',
    				'".$_POST['jam_surat']."',
    				'".$_POST['zona_waktu_surat']."',
    				'".$_POST['lokasi_surat']."',
    				'9',
    				'".$_POST['jabatan_staf_pangkalan']."',
    				'".$_POST['nama_petugas']."',
    				'".$_POST['nip_petugas']."',
    				'".$_POST['nama_nakoda']."',
    				'".$_POST['nip_nakoda']."',
    				'".$_POST['nama_kkm']."',
    				'".$_POST['nip_kkm']."',
    				NOW(),
    				'".$ci->session->userdata('userid')."',
    				'".$_POST['penyedia_penitip']."',
    				'".$_POST['nama_penitip']."',
    				'".$_POST['jabatan_penitip']."',
    				'".$_POST['alamat_penitip']."',
    				".$penggunaan.",
    				'".$_POST['alamat_penyedia_penitip']."',
    				'".$_POST['keterangan_jenis_bbm']."',
    				'".$_POST['nomor_surat_penitip']."',
                    ".$volume_sebelum.",
                    '".$tanggal_sebelum."',
                    ".$volume_pemakaian.",
					".$an_staf.",
					".$an_nakhoda.",
					".$an_kkm."
    			)
    		");
    		
    		$result = array('rows' => array(), 'message' => '', 'success' => false);
    		
    		if($q){
    		    
    		    $Up = $ci->db->query("UPDATE bbm_kapaltrans SET
        			            link_modul_ba = '".$_POST['nomor_surat']."'
        				
        			WHERE nomor_surat = '".$_POST['nomor_surat_penitip']."' ");
    			
    			$table->cetak_ba_pengembalian_bbm($nomor_surat, $filename);
    			
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
		
		$nomor_surat = str_replace('/','_',$_POST['nomor_surat']);
		$filename   = uniqid(7)."-".time();
		$penggunaan = str_replace(',','',$_POST['penggunaan']);
		
		$tanggal_surat = date("Y-m-d", strtotime($_POST['tanggal_surat']));
    
    	$volume_sebelum   = str_replace(',','',$_POST['volume_sebelum']);
    	$tanggal_sebelum  = date("Y-m-d", strtotime($_POST['tanggal_surat_penitip']));
    	$volume_pemakaian = str_replace(',','',$_POST['volume_pemakaian']);
		
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
				lokasi_surat         = '".$_POST['lokasi_surat']."',
				jabatan_staf_pangkalan   = '".$_POST['jabatan_staf_pangkalan']."',
				nama_staf_pagkalan   = '".$_POST['nama_petugas']."',
				nip_staf             = '".$_POST['nip_petugas']."',
				nama_nahkoda         = '".$_POST['nama_nakoda']."',
				nip_nahkoda          = '".$_POST['nip_nakoda']."',
				nama_kkm             = '".$_POST['nama_kkm']."',
				nip_kkm              = '".$_POST['nip_kkm']."',
                penyedia_penitip        = '".$_POST['penyedia_penitip']."',
                nama_penitip            = '".$_POST['nama_penitip']."',
                jabatan_penitip         = '".$_POST['jabatan_penitip']."',
                alamat_penitip          = '".$_POST['alamat_penitip']."',
                penggunaan              = ".$penggunaan.",
                alamat_penyedia_penitip = '".$_POST['alamat_penyedia_penitip']."',
                keterangan_jenis_bbm    = '".$_POST['keterangan_jenis_bbm']."',
                volume_sebelum			= ".$volume_sebelum.",
                tanggal_sebelum			= '".$tanggal_sebelum."',
                volume_pemakaian        = ".$volume_pemakaian.",
				an_staf              = ".$an_staf.",
				an_nakhoda           = ".$an_nakhoda.",
				an_kkm               = ".$an_kkm."
				
			WHERE trans_id = ".$_POST['trans_id']." ");
		
                   
                   
		$result = array('rows' => array(), 'message' => '', 'success' => false);
		
		if($q){
			
			$table->cetak_ba_pengembalian_bbm($nomor_surat,$filename);
			
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