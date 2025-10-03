<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Json library
* @class Warehouse_controller
* @version 07/05/2015 12:18:00
*/
class Ba_pemeriksaan_sarana_pengisian {

    function create() {
		
		
		$sts = true;
		$q   = false;
		
        $ci = & get_instance();
		$ci->load->model('dokumen/dokumen_cetak');
		$table= $ci->dokumen_cetak;
		
	
		
		$nomor_suratx = str_replace(" ","",$_POST['nomor_surat']);
		
		$sql = "SELECT COUNT(*) AS jml FROM bbm_kapaltrans WHERE nomor_surat = '".$nomor_suratx."' "; 
		$query = $ci->db->query($sql);
		foreach ($query->result() as $r){
			$jml = $r->jml;
		}
		
		
		if($_POST['jenis_tranport'] == '1'){
		    
		    $con = 0;
		    
		}else if($_POST['jenis_tranport'] == '2' || $_POST['jenis_tranport'] == '3'){
		    
		    $con = 1;
		    
		}else{
		    
		    $con = 9;
		}
		
	    if(isset($_FILES["lamp"]["name"][$con])){
			$name   = uniqid(0)."-".time();
			$ext    = pathinfo( $_FILES["lamp"]["name"][$con], PATHINFO_EXTENSION );
			$base   = $name . '.' . $ext;
			$exNon  = array("jpg","png");
			
			$source = $_FILES["lamp"]["tmp_name"][$con];
			$desc  = $_SERVER['DOCUMENT_ROOT'].'/sigotik_bbm/dokumen/gambar_ba_sarana/' . $base;
			
				if(!in_array($ext, $exNon)) {
				    $msg = 'Format File Gambar Segel Salah';
					$sts = false;
				}
		}else{
			$base   = '';
		}
		
		
		
		if($_POST['jenis_tranport'] == '1'){
		    
		    $gambar_segel =  $base;
		    $gambar_flowmeter = '';
		    
		}else if($_POST['jenis_tranport'] == '2'|| $_POST['jenis_tranport'] == '3'){
		    
		    $gambar_flowmeter = $base;
		    $gambar_segel =  '';
		    
		}else{
		    $gambar_segel =  '';
		    $gambar_flowmeter = '';
		}
			
			
		
		$filename   = uniqid(4)."-".time();
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
		    
		    if($sts){
        		$q = $ci->db->query("INSERT INTO bbm_kapaltrans(
        				kapal_code			 ,
        				nomor_surat          ,
        				tanggal_surat        ,
        				jam_surat            ,
        				zona_waktu_surat     ,
        				lokasi_surat         ,
        				status_ba            ,
        				jenis_tranport       ,
        				status_segel		 ,
        				gambar_segel         ,
        				status_flowmeter	,
        				gambar_flowmeter    ,
        				kesimpulan			,
        				penyedia			,
        				jabatan_staf_pangkalan ,
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
        				'".$_POST['jam_surat']."',
        				'".$_POST['zona_waktu_surat']."',
        				'".$_POST['lokasi_surat']."',
        				'4',
        				'".$_POST['jenis_tranport']."',
        				'".$_POST['status_segel']."',
        				'".$gambar_segel."',
        				'".$_POST['status_flowmeter']."',
        				'".$gambar_flowmeter."',
        				'".$_POST['kesimpulan']."',
        				'".$_POST['penyedia']."',
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
		    }
    		
    		$result = array('rows' => array(), 'message' => '', 'success' => false);
    		
    		if($q){
    		    
    		    if(isset($_FILES["lamp"]["name"][$con])){

    				move_uploaded_file($source, $desc);
    			}
    			
    			$table->cetak_ba_pemeriksa_sarana($nomor_surat, $filename);
    			
    			$data['rows'] = '';
    			$data['success'] = true;
    			$data['message'] = $filename;
    		
    		}else{
    		    
    		    if($sts){
				
    				$data['rows'] 	 = '';
    				$data['success'] = false;
    				$data['message'] = 'Data Gagal Tersimpan';
				
			    }else{
				
    				$data['rows'] 	 = '';
    				$data['success'] = false;
    				$data['message'] = $msg;
				
			    }
    		
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
		
	    $sts = true;
		$q   = false;
		
        $ci = & get_instance();
		$ci->load->model('dokumen/dokumen_cetak');
		$table= $ci->dokumen_cetak;
		
	
		if($_POST['jenis_tranport'] == '1'){
		    $con = 0;
		}else if($_POST['jenis_tranport'] == '2' || $_POST['jenis_tranport'] == '3'){
		    $con = 1;
		}else{
		    $con = 9;
		}
		
		
	    if(isset($_FILES["lamp_edit"]["name"][$con])){
	        	
			$name   = uniqid(0)."-".time();
			$ext    = pathinfo( $_FILES["lamp_edit"]["name"][$con], PATHINFO_EXTENSION );
			$base   = $name . '.' . $ext;
			$exNon  = array("jpg","png");
			
			$source = $_FILES["lamp_edit"]["tmp_name"][$con];
			$desc  = $_SERVER['DOCUMENT_ROOT'].'/sigotik_bbm/dokumen/gambar_ba_sarana/' . $base;
			
				if(!in_array($ext, $exNon)) {
				    $msg = 'Format File Gambar Segel Salah';
					$sts = false;
				}
		}else{
			$q = $ci->db->query("SELECT * FROM bbm_kapaltrans WHERE trans_id = ".$_POST['trans_id']." ");
		    $r = $q->row_array();
		    
		    if($_POST['jenis_tranport'] == '1'){
		        $base = $r['gambar_segel'];
    		}else if($_POST['jenis_tranport'] == '2'){
    		    $base = $r['gambar_flowmeter'];
    		}else{
    		    $base = '';
    		}
	    	
		}
		
		if($_POST['jenis_tranport'] == '1'){
		    
		    $gambar_segel =  $base;
		    $gambar_flowmeter = '';
		    
		}else if($_POST['jenis_tranport'] == '2' || $_POST['jenis_tranport'] == '3'){
		    
		    $gambar_flowmeter = $base;
		    $gambar_segel =  '';
		    
		}else{
		    $gambar_segel =  '';
		    $gambar_flowmeter = '';
		}
		
		$filename   = uniqid(4)."-".time();
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
		
		if($sts){
    		$q = $ci->db->query("UPDATE bbm_kapaltrans SET
    				tanggal_surat        = '".$tanggal_surat."',
    				jam_surat            = '".$_POST['jam_surat']."',
    				zona_waktu_surat     = '".$_POST['zona_waktu_surat']."',
    				lokasi_surat         = '".$_POST['lokasi_surat']."',
    				jenis_tranport       = '".$_POST['jenis_tranport']."',
    				status_segel		 = '".$_POST['status_segel']."',
    				gambar_segel		 = '".$gambar_segel."',
    				status_flowmeter	 = '".$_POST['status_flowmeter']."',
    				gambar_flowmeter	 = '".$gambar_flowmeter."',
    				kesimpulan			 = '".$_POST['kesimpulan']."',
    				penyedia			 = '".$_POST['penyedia']."',
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
		}
		
		$result = array('rows' => array(), 'message' => '', 'success' => false);
		
		if($q){
		    
		     if(isset($_FILES["lamp_edit"]["name"][$con])){

    				move_uploaded_file($source, $desc);
    			}
			
			$table->cetak_ba_pemeriksa_sarana($nomor_surat,$filename);
			
			$data['rows'] = '';
			$data['success'] = true;
			$data['message'] = $filename;
		
		}else{
			
			if($sts){
				$data['rows'] 	 = '';
				$data['success'] = false;
				$data['message'] = 'Data Gagal Tersimpan';
				
			}else{
				
				$data['rows'] 	 = '';
				$data['success'] = false;
				$data['message'] = $msg;
				
			}
		
		}
		
		return $data;

    }
	
}

/* End of file Warehouse_controller.php */