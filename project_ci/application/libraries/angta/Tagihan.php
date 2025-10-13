<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Json library
* @class Warehouse_controller
* @version 07/05/2015 12:18:00
*/
class Tagihan {

    function create() {
        $ci = & get_instance();
		// var_dump($_POST);die;
        #IMAGES BEGIN
		$filename   = uniqid()."-".time();
		$extension  = pathinfo( $_FILES["images"]["name"][0], PATHINFO_EXTENSION );
		$basename   = $filename . '.' . $extension;

		$source       = $_FILES["images"]["tmp_name"][0];
		$destination  = $_SERVER['DOCUMENT_ROOT'].'/sigotik_bbm/images/' . $basename;

		move_uploaded_file($source, $destination);
		#IMAGES END
		$user = $ci->session->userdata('username');
		
		$ceknotag = $ci->db->query("SELECT * FROM bbm_tagihan WHERE no_tagihan = '".$_POST['no_tagihan']."'")->num_rows();
		$alrt = '';
		if($ceknotag > 0){
			$no_tagihan = no_tagihan();
			$alrt = 'Duplikat Nomor Tagihan '.$_POST['no_tagihan'].', Nomor Tagihan anda disesuaikan menjadi : '.$no_tagihan;
		}else{
			$no_tagihan = $_POST['no_tagihan'];
			$alrt = '';
		}
		
		$tgl_invoice = date('Y-m-d', strtotime($_POST['tgl_invoice']));
		$sace = "INSERT INTO bbm_tagihan (
					m_upt_code,
					tanggal_invoice,
					tagihanke,
					no_tagihan,
                    no_spt,
					penyedia,
					quantity,
					total,
					hargaperliter,
					statustagihan,
					user_input,
					tanggal_input,
					file
				)VALUES(
					'".$_POST['real_kode_upt']."',
					'".$tgl_invoice."',
					'".$_POST['tagihanke']."',
					'".$_POST['no_tagihan']."',
                    '".$_POST['no_spt']."',
					'".$_POST['penyedia']."',
					'".$_POST['real_quantity']."',
					'".$_POST['real_harga']."',
					'".$_POST['real_hargaperliter']."',
					'0',
					'".$user."',
					'".date('Y-m-d h:i:s')."',
					'".$basename."'
				)";
		$q = $ci->db->query($sace);
			
		$result = array('rows' => array(), 'message' => '', 'success' => false);
		
		if($q){
			$jdt = count($_POST['transdetaildt_id_']);
			for($i=0;$i<$jdt;$i++){
				$idv = $_POST['transdetaildt_id_'][$i];
				if(isset($_POST['transdetaildt_'][$idv])){
					$sqlupdate = $ci->db->query("UPDATE bbm_transdetail SET status_bayar = '0' WHERE bbm_transdetail_id = '".$idv."'");
					$sqlupdate1 = $ci->db->query("UPDATE bbm_transdetail SET status_bayar = '1', harga_total = '".str_replace(',','',$_POST['detail_harga_'.$idv])."', no_tagihan = '".$_POST['no_tagihan']."', no_invoice = '".$_POST['no_invoice_'.$idv]."' WHERE bbm_transdetail_id = '".$idv."'");
				}
			}
			$data['rows'] = '';
			$data['success'] = true;
			$data['message'] = 'Data Berhasil Disimpan. '.$alrt;
		
		}else{
			
			$data['rows'] 	 = '';
			$data['success'] = false;
			$data['message'] = 'Penyimpanan Gagal';
		
		}
		
		return $data;

    }

    function update() {
        $ci = & get_instance();
		
        #IMAGES BEGIN
		if(isset($_FILES["images"]["name"])){
			
		$filename   = uniqid()."-".time();
		$extension  = pathinfo( $_FILES["images"]["name"][0], PATHINFO_EXTENSION );
		$basename   = $filename . '.' . $extension;

		$source       = $_FILES["images"]["tmp_name"][0];
		$destination  = $_SERVER['DOCUMENT_ROOT'].'/sigotik_bbm/images/' . $basename;

		move_uploaded_file($source, $destination);
		$inpsert = "file                = '".$basename."',";
		}else{
		$inpsert = "";	
		}
		#IMAGES END
		$user = $ci->session->userdata('username');
		$tgl_invoice = date('Y-m-d', strtotime($_POST['tgl_invoice']));
			$sql = "
					UPDATE bbm_tagihan SET
						m_upt_code			= '".$_POST['real_kode_upt']."',
						tanggal_invoice		= '".$tgl_invoice."',
						tagihanke			= '".$_POST['tagihanke']."',
						no_tagihan			= '".$_POST['no_tagihan']."',
                        no_spt		    	= '".$_POST['no_spt']."',
						penyedia			= '".$_POST['penyedia']."',
						quantity			= '".$_POST['real_quantity1']."',
						total				= '".$_POST['real_harga1']."',
						hargaperliter		= '".$_POST['real_hargaperliter1']."',
						statustagihan		= '0',
						user_input			= '".$user."',
						".$inpsert."
						tanggal_input		= '".date('Y-m-d h:i:s')."'
					WHERE tagihan_id = '".$_POST['id']."' ";
					
			$q = $ci->db->query($sql);
			
		$result = array('rows' => array(), 'message' => '', 'success' => false);
		
		if($q){
			
			$jdt = count($_POST['transdetaildt_ids_']);
			for($i=0;$i<$jdt;$i++){
				$idv = $_POST['transdetaildt_ids_'][$i];
				if(isset($_POST['detail_hargas_'.$idv])){
					$sqlupdate = $ci->db->query("UPDATE bbm_transdetail SET status_bayar = '0' WHERE bbm_transdetail_id = '".$idv."'");
					$sqlupdate1 = $ci->db->query("UPDATE bbm_transdetail SET status_bayar = '1', harga_total = '".str_replace(',','',$_POST['detail_hargas_'.$idv])."', no_tagihan = '".$_POST['no_tagihan']."', no_invoice = '".$_POST['no_invoices_'.$idv]."' WHERE bbm_transdetail_id = '".$idv."'");
				}
			}
			
			$data['rows'] = '';
			$data['success'] = true;
			$data['message'] = 'Data Berhasil Disimpan';
		
		}else{
			
			$data['rows'] 	 = '';
			$data['success'] = false;
			$data['message'] = 'Penyimpanan Gagal';
		
		}
		
		return $data;

    }

    function destroy() {
		
		$ci = & get_instance();
		$dtnya = $ci->db->query("SELECT * FROM bbm_tagihan WHERE tagihan_id = '".$_POST['id']."'")->row_array();
		$bbmdtnya = $ci->db->query("SELECT * FROM bbm_transdetail WHERE no_tagihan = '".$dtnya['no_tagihan']."'");
		foreach($bbmdtnya->result() AS $dt){
			$sqlupdate = $ci->db->query("UPDATE bbm_transdetail SET status_bayar = '0', no_tagihan = null, harga_total = null, no_invoice = null WHERE bbm_transdetail_id = '".$dt->bbm_transdetail_id."'");
		}
		$q = $ci->db->query("DELETE FROM `bbm_tagihan` WHERE tagihan_id = ".$_POST['id']." ");
		
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

	function approve(){
		$ci = & get_instance();
		$user = $ci->session->userdata('username');
		// var_dump($_POST);die;
			$sql = "
					UPDATE bbm_tagihan SET
						statustagihan		= '1',
						user_app			= '".$user."',
						tanggal_app		= '".date('Y-m-d h:i:s')."'
					WHERE tagihan_id = '".$_POST['id']."' ";
			$q = $ci->db->query($sql);
		
		$result = array('rows' => array(), 'message' => '', 'success' => false);
		
		if($q){
			
			$data['rows'] = '';
			$data['success'] = true;
			$data['message'] = 'Data Berhasil Disimpan';
		
		}else{
			
			$data['rows'] 	 = '';
			$data['success'] = false;
			$data['message'] = 'Penyimpanan Gagal';
		
		}
		
		return $data;
	}

	function reject(){
		$ci = & get_instance();
		$user = $ci->session->userdata('username');
		// var_dump($_POST);die;
			$sql = "
					UPDATE bbm_tagihan SET
						statustagihan		= '2',
						user_batal			= '".$user."',
						tanggal_batal		= '".date('Y-m-d h:i:s')."'
					WHERE tagihan_id = '".$_POST['id']."' ";
			$q = $ci->db->query($sql);
		
		$result = array('rows' => array(), 'message' => '', 'success' => false);
		
		if($q){
			
			$data['rows'] = '';
			$data['success'] = true;
			$data['message'] = 'Data Berhasil Disimpan';
		
		}else{
			
			$data['rows'] 	 = '';
			$data['success'] = false;
			$data['message'] = 'Penyimpanan Gagal';
		
		}
		
		return $data;
	}
	
	function upsppd(){
		$ci = & get_instance();
		$tgl_sppd = date('Y-m-d', strtotime($_POST['tgl_sppd']));
			$sql = "
					UPDATE bbm_tagihan SET
						tanggal_sppd		= '".$tgl_sppd."'
					WHERE tagihan_id = '".$_POST['id']."' ";
			$q = $ci->db->query($sql);
		
		$result = array('rows' => array(), 'message' => '', 'success' => false);
		
		if($q){
			
			$data['rows'] = '';
			$data['success'] = true;
			$data['message'] = 'Data Berhasil Disimpan';
		
		}else{
			
			$data['rows'] 	 = '';
			$data['success'] = false;
			$data['message'] = 'Penyimpanan Gagal';
		
		}
		
		return $data;
	}
	
	function uploadsppd() {
		// var_dump($_POST);die();
		$filename   = uniqid()."-".time();
		$extension  = pathinfo( $_FILES["my_images"]["name"][0], PATHINFO_EXTENSION );
		$basename   = $filename . '.' . $extension;
        
        
		$source       = $_FILES["my_images"]["tmp_name"][0];
		$destination  = $_SERVER['DOCUMENT_ROOT'].'/sigotik_bbm/dokumen/dokumen_sppd/'.$basename;

		move_uploaded_file($source, $destination);
		
        $ci = & get_instance();
		
		$q = $ci->db->query("UPDATE bbm_tagihan SET
				file_sppd = '".$basename."'
			WHERE tagihan_id = '".$_POST['tagihan_id']."'
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