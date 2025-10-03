<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Json library
* @class Warehouse_controller
* @version 07/05/2015 12:18:00
*/
class Ba_penerimaan_bbm {

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
		
		$filename   = uniqid(5)."-".time();
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
    				jam_surat            ,
    				zona_waktu_surat     ,
    				lokasi_surat         ,
    				status_ba            ,
    				penyedia			,
    				keterangan_jenis_bbm,
    				jabatan_staf_pangkalan,
    				nama_staf_pagkalan   ,
    				nip_staf             ,
    				nama_nahkoda         ,
    				nip_nahkoda          ,
    				nama_kkm             ,
    				nip_kkm              ,
    				tanggalinput         ,
    				user_input           ,
    				no_so				 ,
					an_staf				 ,
					an_nakhoda			 ,
					an_kkm     
    
    
    			) VALUES (
    				'".$_POST['code_kapal']."',
    				'".$nomor_suratx."',
    				'".$tanggal_surat."',
    				'".$_POST['jam_surat']."',
    				'".$_POST['zona_waktu_surat']."',
    				'".$_POST['lokasi_surat']."',
    				'5',
    				'".$_POST['penyedia']."',
    				'".$_POST['keterangan_jenis_bbm']."',
    				'".$_POST['jabatan_staf_pangkalan']."',
    				'".$_POST['nama_petugas']."',
    				'".$_POST['nip_petugas']."',
    				'".$_POST['nama_nakoda']."',
    				'".$_POST['nip_nakoda']."',
    				'".$_POST['nama_kkm']."',
    				'".$_POST['nip_kkm']."',
    				NOW(),
    				'".$ci->session->userdata('userid')."',
    				'".$_POST['no_so']."',
					".$an_staf.",
					".$an_nakhoda.",
					".$an_kkm."
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
    			
    			$qr = $ci->db->query("UPDATE bbm_kapaltrans SET
				                        volume_pengisian = ".$volome_pengisian."
		                    	WHERE nomor_surat = '".$_POST['nomor_surat']."' ");
    			
    			$table->cetak_ba_pemenerimaan_bbm($nomor_surat, $filename);
    			
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
		
		$filename   = uniqid(5)."-".time();
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
				lokasi_surat     = '".$_POST['lokasi_surat']."',
				penyedia       		 = '".$_POST['penyedia']."',
				keterangan_jenis_bbm = '".$_POST['keterangan_jenis_bbm']."',
				jabatan_staf_pangkalan   = '".$_POST['jabatan_staf_pangkalan']."',
				nama_staf_pagkalan   = '".$_POST['nama_petugas']."',
				nip_staf             = '".$_POST['nip_petugas']."',
				nama_nahkoda         = '".$_POST['nama_nakoda']."',
				nip_nahkoda          = '".$_POST['nip_nakoda']."',
				nama_kkm             = '".$_POST['nama_kkm']."',
				nip_kkm              = '".$_POST['nip_kkm']."',
				no_so                = '".$_POST['no_so']."',
				an_staf              = ".$an_staf.",
				an_nakhoda           = ".$an_nakhoda.",
				an_kkm               = ".$an_kkm."
				
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
			
			$table->cetak_ba_pemenerimaan_bbm($nomor_surat,$filename);
			
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
		
		$sql = "SELECT nomor_surat,link_modul_ba FROM bbm_kapaltrans WHERE trans_id = ".$_POST['trans_id']." "; 
		$query = $ci->db->query($sql);
		foreach ($query->result() as $r){
			$nomor_surat = $r->nomor_surat;
        	$link_modul_ba = $r->link_modul_ba;
		}
    
    	$sql2 = "SELECT count(*) AS status
					FROM bbm_transdetail  WHERE nomor_surat = '".$nomor_surat."' AND status_bayar IN (1) "; 
		$query2 = $ci->db->query($sql2);
		foreach ($query2->result() as $r2){
			$status = $r2->status;
		}
    	
    	if($status == 0){
        	
        	$q2 = $ci->db->query("DELETE FROM bbm_transdetail WHERE TRIM(REPLACE(REPLACE(REPLACE(`nomor_surat`,'\t',''),'\n',''),'\r','')) = '".$nomor_surat."' ");
       		$q = $ci->db->query("DELETE FROM bbm_kapaltrans WHERE trans_id = ".$_POST['trans_id']." ");

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
        	
        
        }else{
        
        	$data['rows'] 	 = '';
            $data['success'] = false;
            $data['message'] = 'Data Sudah Melakukan Pembayaran';
        
        }
		
		
		return $data;
		
    }
	
}

/* End of file Warehouse_controller.php */