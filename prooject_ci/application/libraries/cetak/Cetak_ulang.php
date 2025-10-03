<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Json library
* @class Warehouse_controller
* @version 07/05/2015 12:18:00
*/
class Cetak_ulang {

    function create() {
		
        $ci = & get_instance();
		$ci->load->model('dokumen/dokumen_cetak');
		$table= $ci->dokumen_cetak;
		
		$sql = "SELECT * FROM bbm_kapaltrans WHERE trans_id = ".$_POST['trans_id']." "; 
		$query = $ci->db->query($sql);
		foreach ($query->result() as $r){
			$status_ba = $r->status_ba;
			$nomor_surat = str_replace('/','_',$r->nomor_surat);
		}
		
		$filename   = uniqid()."-".time();
		
		if($status_ba == 2 ){ 
			$table->cetak_ba_sisa_sblm_pengisian($nomor_surat,$filename);
			
		}else if($status_ba == 6 ){
			$table->cetak_ba_sblm_pelayaran($nomor_surat,$filename); 
		
		}else if($status_ba == 7 ){
			$table->cetak_ba_ssdah_pelayaran($nomor_surat,$filename); 
		
		}else if($status_ba == 3 ){
			$table->cetak_ba_penggunaan_bbm($nomor_surat,$filename); 
		
		}else if($status_ba == 4 ){
			$table->cetak_ba_pemeriksa_sarana($nomor_surat,$filename); 
		
		}else if($status_ba == 5 ){
			$table->cetak_ba_pemenerimaan_bbm($nomor_surat,$filename); 
		
		}else if($status_ba == 1 ){
		    
			$table->cetak_ba_akhir_bulan($nomor_surat,$filename); 
		
		}else if($status_ba == 8 ){
		    
			$table->cetak_ba_penitipan_bbm($nomor_surat,$filename); 
		
		}else if($status_ba == 9 ){
		    
			$table->cetak_ba_pengembalian_bbm($nomor_surat,$filename); 
		
		}else if($status_ba == 10 ){
		    
			$table->cetak_ba_peminjaaman_bbm($nomor_surat,$filename); 
		
		}else if($status_ba == 11 ){
		    
			$table->cetak_ba_penerimaan_pinjaman_bbm($nomor_surat,$filename); 
		
		}else if($status_ba == 12 ){
		    
			$table->cetak_ba_pengembalian_pinjaman_bbm($nomor_surat,$filename); 
		
		}else if($status_ba == 13 ){
		    
			$table->cetak_ba_penerimaan_pengembalian_bbm($nomor_surat,$filename); 
		
		}else if($status_ba == 14 ){
		    
			$table->cetak_ba_pemberi_hibah_bbm_kapal_pengawas($nomor_surat,$filename); 
		
		}else if($status_ba == 15 ){
		    
			$table->cetak_ba_penerima_hibah_bbm_kapal_pengawas($nomor_surat,$filename); 
		
		}else if($status_ba == 16 ){
		    
			$table->cetak_ba_penerima_hibah_bbm_instansi_lain($nomor_surat,$filename); 
		
		}else if($status_ba == 17 ){
		    
			$table->cetak_ba_pemberi_hibah_bbm($nomor_surat,$filename); 
		
		}else if($status_ba == 18 ){
		    
			$table->cetak_ba_pemberi_ba_hibah_bbm_instansi_lain($nomor_surat,$filename); 
		
		}
		
		$result = array('rows' => array(), 'message' => '', 'success' => false);
		
		
		$data['rows'] = '';
		$data['success'] = true;
		$data['message'] = $filename;
		
		return $data;

    }
    
    function delete_cetak(){
        
    	$result = array('rows' => array(), 'message' => '', 'success' => false);
    	

	    // unlink($_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/dokumen/cetakan_ba/'.$nomor_surat.'.pdf');
	    $path = $_SERVER['DOCUMENT_ROOT'].'/sigotik_bbm/dokumen/cetakan_ba/'.$_POST['nomor_surat'].'.pdf';
	    if(is_file($path)){
            unlink($path);
        }
	   
		
		$data['rows'] = '';
		$data['success'] = true;
		$data['message'] = '';
		
		return $data;
        
        
    }
	
}

/* End of file Warehouse_controller.php */