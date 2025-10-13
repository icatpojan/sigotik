<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Json library
* @class Warehouse_controller
* @version 07/05/2015 12:18:00
*/
class Ba_penerimaan_hibah_bbm {

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
		
		$volume_sebelum = str_replace(',','',$_POST['volume_sebelum']);
		
		
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
    				jam_surat            ,
    				zona_waktu_surat     ,
    				lokasi_surat         ,
    				status_ba            ,
    				volume_sebelum       ,
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
					keterangan_jenis_bbm ,
					
					no_so				,
					instansi_temp		,
					alamat_instansi_temp		,
					nama_penyedia		,
					link_modul_ba		,
					penyedia
    
    			) VALUES (
    				'".$_POST['code_kapal']."',
    				'".$nomor_suratx."',
    				'".$tanggal_surat."',
    				'".$_POST['jam_surat']."',
    				'".$_POST['zona_waktu_surat']."',
    				'".$_POST['lokasi_surat']."',
    				'17',
    			    ".$volume_sebelum.",
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
					'".$_POST['keterangan_jenis_bbm']."',
					
					'".$_POST['no_so']."',
					'".$_POST['instansi_temp']."',
					'".$_POST['alamat_instansi_temp']."',
					'".$_POST['nama_penyedia']."',
					'".$_POST['link_ba']."',
					'".$_POST['penyedia']."'
    			)
    		");
    		
    		$result = array('rows' => array(), 'message' => '', 'success' => false);
    		
    		if($q){
				
				for($i=0;$i<count($_POST['transportasi']);$i++){
    			        
    			        $volume_isi = str_replace(',','',$_POST['volume_isi'][$i]);
    			        
    					$r = $ci->db->query("INSERT INTO bbm_transdetail(
    								nomor_surat ,
    								transportasi,
    								no_so,
    								no_do,
    								volume_isi,
    								keterangan,
    								tanggalinput,
    								userid
    						) VALUES (
    								'".$_POST['nomor_surat']."',
    								'".$_POST['transportasi'][$i]."',
    								'".$_POST['no_so']."',
    								'".$_POST['no_do'][$i]."',
    								'".$volume_isi."',
    								'".$_POST['keterangan'][$i]."',
    								NOW(),
    								'".$ci->session->userdata('userid')."'
    						)
    					");
    					
    					$q = $ci->db->query("DELETE FROM bbm_transdetail WHERE nomor_surat = '".$_POST['nomor_surat']."' AND transportasi = '' ");
    			}
    			
    		    $sVol = "SELECT SUM(volume_isi) AS volume_pengisian FROM bbm_transdetail WHERE nomor_surat = '".$_POST['nomor_surat']."' "; 
        		$qVol = $ci->db->query($sVol);
        		foreach ($qVol->result() as $rVol){
        			$volome_pengisian = $rVol->volume_pengisian;
        		}
    			
				$volume_sisa = $volume_sebelum + $volome_pengisian;
				
				$qr = $ci->db->query("UPDATE bbm_kapaltrans SET
				                        volume_pengisian = ".$volome_pengisian.",
				                        volume_sisa = ".$volume_sisa."
		                    	WHERE nomor_surat = '".$_POST['nomor_surat']."' ");
				
				$ci->db->query("UPDATE bbm_kapaltrans SET
        			            link_modul_ba = '".$_POST['nomor_surat']."'
        				
        			WHERE nomor_surat = '".$_POST['link_ba']."' ");
					
    			$table->cetak_ba_pemberi_hibah_bbm($nomor_surat,$filename);
    			
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
				
				
				jabatan_staf_pangkalan = '".$_POST['jabatan_staf_pangkalan']."',
				nama_staf_pagkalan    = '".$_POST['nama_petugas']."',
				nip_staf              = '".$_POST['nip_petugas']."',
				nama_nahkoda          = '".$_POST['nama_nakoda']."',
				nip_nahkoda           = '".$_POST['nip_nakoda']."',
				nama_kkm              = '".$_POST['nama_kkm']."',
				nip_kkm               = '".$_POST['nip_kkm']."',
				an_staf				  = ".$an_staf.",
				an_nakhoda			  = ".$an_nakhoda.",
				an_kkm     			  = ".$an_kkm.",
				keterangan_jenis_bbm  = '".$_POST['keterangan_jenis_bbm']."',
				
				no_so				 = '".$_POST['no_so']."',
				instansi_temp		 = '".$_POST['instansi_tempEd']."',
				alamat_instansi_temp = '".$_POST['alamat_instansi_tempEd']."',
				nama_penyedia		 = '".$_POST['nama_penyedia']."',
				penyedia			= '".$_POST['penyedia']."'
				
			WHERE trans_id = ".$_POST['trans_id']." ");
		
		$result = array('rows' => array(), 'message' => '', 'success' => false);
		
		if($q){
			
			$q = $ci->db->query("DELETE FROM bbm_transdetail WHERE nomor_surat = '".$_POST['nomor_surat']."' ");
			
			for($i=0;$i<count($_POST['transportasix']);$i++){
			        
			        $volume_isix = str_replace(',','',$_POST['volume_isix'][$i]);
			        
					$r = $ci->db->query("INSERT INTO bbm_transdetail(
								nomor_surat ,
								transportasi,
								no_so,
								no_do,
								volume_isi,
								keterangan,
								tanggalinput,
								userid
						) VALUES (
								'".$_POST['nomor_surat']."',
								'".$_POST['transportasix'][$i]."',
								'".$_POST['no_so']."',
								'".$_POST['no_dox'][$i]."',
								'".$volume_isix."',
								'".$_POST['keteranganx'][$i]."',
								NOW(),
								'".$ci->session->userdata('userid')."'
						)
					");
					
					$q = $ci->db->query("DELETE FROM bbm_transdetail WHERE nomor_surat = '".$_POST['nomor_surat']."' AND transportasi = '' ");
			
			}
			
			$sVol = "SELECT SUM(volume_isi) AS volume_pengisian FROM bbm_transdetail WHERE nomor_surat = '".$_POST['nomor_surat']."' "; 
    		$qVol = $ci->db->query($sVol);
    		foreach ($qVol->result() as $rVol){
    			$volome_pengisian = $rVol->volume_pengisian;
    		}
    			
			$qr = $ci->db->query("UPDATE bbm_kapaltrans SET
			                        volume_pengisian = ".$volome_pengisian."
	                    	WHERE nomor_surat = '".$_POST['nomor_surat']."' ");
			
			$table->cetak_ba_pemberi_hibah_bbm($nomor_surat,$filename);
			
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