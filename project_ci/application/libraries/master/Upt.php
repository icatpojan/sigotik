<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Json library
* @class Warehouse_controller
* @version 07/05/2015 12:18:00
*/
class Upt {

    function create() {

        $ci = & get_instance();
		
		$q = $ci->db->query("INSERT INTO m_upt(
				nama,
				code,
				alamat1,
				alamat2,
				alamat3,
				kota,
				zona_waktu_upt,
				nama_petugas,
				nip_petugas,
				jabatan_petugas,
				pangkat_petugas,
				date_insert,     	   
				user_insert
				
			) VALUES (
				'".$_POST['nama']."',
				'".$_POST['code']."',
				'".$_POST['alamat1']."',
				'".$_POST['alamat2']."',
				'".$_POST['alamat3']."',
				'".$_POST['kota']."',
				'".$_POST['zona_waktu_upt']."',
				'".$_POST['nama_petugas']."',
				'".$_POST['nip_petugas']."',
				'".$_POST['jabatan_petugas']."',
				'".$_POST['pangkat_petugas']."',
				NOW(),
				'".$ci->session->userdata('username')."'
			)
		");
		
		$result = array('rows' => array(), 'message' => '', 'success' => false);
		
		if($q){
			
			$data['rows'] = '';
			$data['success'] = true;
			$data['message'] = 'Data Berhasil Tersimpan';
		
		}else{
			
			$data['rows'] 	 = '';
			$data['success'] = false;
			$data['message'] = 'Data Gagal Tersimpan';
		
		}
		
		return $data;

    }

    function update() {
		
        $ci = & get_instance();
		
		$q = $ci->db->query("UPDATE m_upt SET
								nama = '".$_POST['nama']."',
								code = '".$_POST['code']."',
								alamat1 = '".$_POST['alamat1']."',
								alamat2 = '".$_POST['alamat2']."',
								alamat3 = '".$_POST['alamat3']."',
								kota = '".$_POST['kota']."',
								zona_waktu_upt = '".$_POST['zona_waktu_upt']."',
								nama_petugas = '".$_POST['nama_petugas']."',
								nip_petugas = '".$_POST['nip_petugas']."',
								jabatan_petugas = '".$_POST['jabatan_petugas']."',
								pangkat_petugas = '".$_POST['pangkat_petugas']."',
								date_update = NOW(),
								user_update = '".$ci->session->userdata('username')."'
							WHERE m_upt_id = ".$_POST['m_upt_id']." ");
		
		$result = array('rows' => array(), 'message' => '', 'success' => false);
		
		if($q){
			
			$data['rows'] = '';
			$data['success'] = true;
			$data['message'] = 'Data Berhasil Tersimpan';
		
		}else{
			
			$data['rows'] 	 = '';
			$data['success'] = false;
			$data['message'] = 'Data Gagal Tersimpan';
		
		}
		
		return $data;

    }

    function destroy() {
       
		$ci = & get_instance();
		$q = $ci->db->query("DELETE FROM m_upt WHERE m_upt_id = ".$_POST['m_upt_id']." ");
		
		
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