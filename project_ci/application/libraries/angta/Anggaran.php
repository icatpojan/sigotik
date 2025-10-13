<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Json library
* @class Warehouse_controller
* @version 07/05/2015 12:18:00
*/
class Anggaran {

    function create() {

        $ci = & get_instance();
		$user = $ci->session->userdata('username');
		$sql = $ci->db->query("SELECT * FROM m_upt");
		foreach($sql->result() as $rows){
			$sace = "INSERT INTO bbm_anggaran (
						periode,
						m_upt_code,
						anggaran,
						perubahan_ke,
						keterangan,
						statusanggaran,
						user_input,
						tanggal_input
					)VALUES(
						'".$_POST['periode']."',
						'".$rows->code."',
						'".str_replace(',','',$_POST['upt_'.$rows->code])."',
						'0',
						'".$_POST['keterangan']."',
						'0',
						'".$user."',
						'".date('Y-m-d h:i:s')."'
					)";
		
			$q = $ci->db->query($sace);
		}
			
		$result = array('rows' => array(), 'message' => '', 'success' => false);
		
		if($q){
			
			$data['rows'] = '';
			$data['success'] = true;
			$data['message'] = 'Anggaran Periode '.$_POST['periode'].' Disimpan';
		
		}else{
			
			$data['rows'] 	 = '';
			$data['success'] = false;
			$data['message'] = 'Penyimpanan Gagal';
		
		}
		
		return $data;

    }
	
	function createupd(){
		$ci = & get_instance();
		$user = $ci->session->userdata('username');
		$sql = $ci->db->query("SELECT * FROM m_upt");
		foreach($sql->result() as $rows){
			$sql = "
					UPDATE `bbm_anggaran` SET
						anggaran		= '".str_replace(',','',$_POST['upt_'.$rows->code])."',
						perubahan_ke	= '0',
						keterangan		= NULLIF('".$_POST['keterangan']."',''),
						statusanggaran	= '0',
						user_input		= '".$user."',
						tanggal_input	= '".date('Y-m-d h:i:s')."'
					WHERE periode = '".$_POST['periode']."' AND m_upt_code = '".$rows->code."'
			";
		
			$q = $ci->db->query($sql);
		}
		
		$result = array('rows' => array(), 'message' => '', 'success' => false);
		
		if($q){
			
			$data['rows'] = '';
			$data['success'] = true;
			$data['message'] = 'Anggaran Periode '.$_POST['periode'].' Disimpan';
		
		}else{
			
			$data['rows'] 	 = '';
			$data['success'] = false;
			$data['message'] = 'Penyimpanan Gagal';
		
		}
		
		return $data;
	}
	
	function createInternal(){
	    
		$ci = & get_instance();
		$user = $ci->session->userdata('username');
		
		$tanggal_trans = date("y-m-d", strtotime($_POST['tanggal_trans']));
		$nominal_rubah = str_replace(',','',$_POST['nominal_rubah']);
		
		$sql = "INSERT INTO bbm_anggaran_upt (
						tanggal_trans,
						m_upt_code,
						nominal,
						nomor_surat,
						keterangan,
						statusperubahan,
						user_input,
						tanggal_input
					)VALUES(
						'".$tanggal_trans."',
						'".$_POST['real_kode_upt']."',
						'".$nominal_rubah."',
						'".$_POST['nomor_surat']."',
						'".$_POST['keterangan']."',
						'0',
						'".$user."',
						'".date('Y-m-d h:i:s')."'
					)
		";
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
	
	function updteperub(){
	    $ci = & get_instance();
		$user = $ci->session->userdata('username');
		$sql = $ci->db->query("SELECT * FROM m_upt");
		foreach($sql->result() as $rows){
			$sql = "
					UPDATE `bbm_anggaran` SET
						anggaran		= '".str_replace(',','',$_POST['upt_'.$rows->code])."',
						keterangan		= NULLIF('".$_POST['keterangan']."',''),
						user_input		= '".$user."',
						tanggal_input	= '".date('Y-m-d h:i:s')."'
					WHERE periode = '".$_POST['periode']."' AND m_upt_code = '".$rows->code."' AND perubahan_ke = '".$_POST['perubahan_ke']."'
			";
		
			$q = $ci->db->query($sql);
		}
		
		$result = array('rows' => array(), 'message' => '', 'success' => false);
		
		if($q){
			
			$data['rows'] = '';
			$data['success'] = true;
			$data['message'] = 'Anggaran Periode '.$_POST['periode'].' Disimpan';
		
		}else{
			
			$data['rows'] 	 = '';
			$data['success'] = false;
			$data['message'] = 'Penyimpanan Gagal';
		
		}
		
		return $data;
	}
	
    function update() {
		// var_dump($_POST);die;
        $ci = & get_instance();
		$user = $ci->session->userdata('username');
		$perb = $_POST['perubahan_ke'];
		$perubahan_ke = $perb + 1;
		$sql = $ci->db->query("SELECT * FROM m_upt");
		foreach($sql->result() as $rows){
		    
			$sql = "INSERT INTO bbm_anggaran (
						periode,
						m_upt_code,
						anggaran,
						perubahan_ke,
						keterangan,
						statusanggaran,
						user_input,
						tanggal_input
					)VALUES(
						'".$_POST['periode']."',
						'".$rows->code."',
						'".str_replace(',','',$_POST['upt_'.$rows->code])."',
						'".$perubahan_ke."',
						'".$_POST['keterangan']."',
						'0',
						'".$user."',
						'".date('Y-m-d h:i:s')."'
					)";
			$q = $ci->db->query($sql);
		}
		
		$result = array('rows' => array(), 'message' => '', 'success' => false);
		
		if($q){
			
			$data['rows'] = '';
			$data['success'] = true;
			$data['message'] = 'Perubahan Anggaran Periode '.$_POST['periode'].' Disimpan';
		
		}else{
			
			$data['rows'] 	 = '';
			$data['success'] = false;
			$data['message'] = 'Penyimpanan Gagal';
		
		}
		
		return $data;

    }
    
    function updateInternal() {
        
        $ci = & get_instance();
		$user = $ci->session->userdata('username');
		$tanggal_trans = date("y-m-d", strtotime($_POST['tanggal_trans']));
		$nominal_rubah = str_replace(',','',$_POST['nominal_rubah']);
		    
		$sql = "
					UPDATE `bbm_anggaran_upt` SET
						tanggal_trans = '".$tanggal_trans."',
						m_upt_code = '".$_POST['real_kode_upt']."',
						nominal = '".$nominal_rubah."',
						nomor_surat = '".$_POST['nomor_surat']."',
						keterangan = '".$_POST['keterangan']."',
						user_input = '".$user."',
						tanggal_input = '".date('Y-m-d h:i:s')."'
					WHERE anggaran_upt_id = '".$_POST['id']."' ";
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

    function destroy() {
		
		$ci = & get_instance();
		$q = $ci->db->query("DELETE FROM `bbm_anggaran` WHERE periode = ".$_POST['th']." ");
		
		
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
    
    function destroyInternal() {
		
		$ci = & get_instance();
		$q = $ci->db->query("DELETE FROM `bbm_anggaran_upt` WHERE anggaran_upt_id = ".$_POST['id']." ");
		
		
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

	function approv(){
		$ci = & get_instance();
		// var_dump($_POST);die;
		$user = $ci->session->userdata('username');
		$sql = $ci->db->query("SELECT * FROM m_upt");
		foreach($sql->result() as $rows){
			$sql = "
					UPDATE `bbm_anggaran` SET
						statusanggaran	= '1',
						user_app		= '".$user."',
						tanggal_app	= '".date('Y-m-d h:i:s')."'
					WHERE periode = '".$_POST['thn']."' AND m_upt_code = '".$rows->code."'
			";
			$q = $ci->db->query($sql);
		}
		
		$result = array('rows' => array(), 'message' => '', 'success' => false);
		
		if($q){
			
			$data['rows'] = '';
			$data['success'] = true;
			$data['message'] = 'Anggaran Periode '.$_POST['thn'].' Disetujui';
		
		}else{
			
			$data['rows'] 	 = '';
			$data['success'] = false;
			$data['message'] = 'Penyimpanan Gagal';
		
		}
		
		return $data;
	}
	
	function approvInternal(){
		$ci = & get_instance();
		// var_dump($_POST);die;
		$user = $ci->session->userdata('username');
		$sql = "
				UPDATE `bbm_anggaran_upt` SET
					statusperubahan	= '1',
					user_app		= '".$user."',
					tanggal_app	= '".date('Y-m-d h:i:s')."'
				WHERE anggaran_upt_id = '".$_POST['id']."'
		";
		$q = $ci->db->query($sql);
		
		
		$result = array('rows' => array(), 'message' => '', 'success' => false);
		
		if($q){
			
			$data['rows'] = '';
			$data['success'] = true;
			$data['message'] = 'Anggaran Berhasil Disetujui';
		
		}else{
			
			$data['rows'] 	 = '';
			$data['success'] = false;
			$data['message'] = 'Penyimpanan Gagal';
		
		}
		
		return $data;
	}
	
	function batalInternal(){
		$ci = & get_instance();
		// var_dump($_POST);die;
		$user = $ci->session->userdata('username');
		$sql = "
				UPDATE `bbm_anggaran_upt` SET
					statusperubahan	= '2'
				WHERE anggaran_upt_id = '".$_POST['id']."'
		";
		$q = $ci->db->query($sql);
		
		
		$result = array('rows' => array(), 'message' => '', 'success' => false);
		
		if($q){
			
			$data['rows'] = '';
			$data['success'] = true;
			$data['message'] = 'Anggaran Berhasil Dibatalkan';
		
		}else{
			
			$data['rows'] 	 = '';
			$data['success'] = false;
			$data['message'] = 'Penyimpanan Gagal';
		
		}
		
		return $data;
	}
	
	
}

/* End of file Warehouse_controller.php */