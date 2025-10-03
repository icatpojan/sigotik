<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Upt extends CI_Controller {
	
	public function Daftar(){
		parent::__construct();
		
		$this->load->helper('form');
		
	}  
	
	public function getDataTable(){
		
		$ci = & get_instance();
		$sSearch	=  $this->input->post('sSearch');
		$iDisplayStart = $this->input->post('iDisplayStart');
		$iDisplayLength = $this->input->post('iDisplayLength');
		
		$cari = "";
 
		if($sSearch != NULL){
			$sSearch = strtoupper($sSearch);
			$cari .= " WHERE nama LIKE '%".$sSearch."%' ";
		}
		
		$sql = "SELECT * FROM `m_upt` ".$cari; 
		
		$query = $ci->db->query($sql." LIMIT ".$iDisplayLength." OFFSET ".$iDisplayStart." ");
		$query2 = $ci->db->query($sql);
		$total = $query2->num_rows();
		
		
		if($total > 0){	
			$i = $iDisplayStart;
			foreach ($query->result() as $list){
				$i++;
				$aaData[] = array(
					
					$i,
					$list->nama,
					$list->code, 
					$list->alamat1.' / '.$list->kota.' ('.$list->zona_waktu_upt.')',
					$list->alamat2,
					$list->alamat3,
					'<button type="button" class="btn btn-warning" onClick="getTab3('.$list->m_upt_id.')" title="EDIT" ><i class="fa fa-edit"></i></button> 
					<button type="button" class="btn btn-danger" onClick="hapus('.$list->m_upt_id.')" title="HAPUS" ><i class="fa fa-trash"></i></button>'
				);
			}
		}else{
		
			$aaData=array();
		}

		$sOutput = array
			(
			"sEcho" => $this->input->post('sEcho'),
			"iTotalRecords" => $total,
			"iTotalDisplayRecords" => $total,
			"aaData" => $aaData
		);
		
		echo json_encode($sOutput);
		
	}
	
	public function getDataForm(){
		$xa = '';
		
		$xa = '
			<div class="row">
			
				 <div class="col-md-9 ">
					<div class="form-group">
						<label>Nama UPT</label>
						<input type="text" placeholder="" id="nama" name="nama" class="form-control" value="">
					</div>
				</div>
				
				 <div class="col-md-9 ">
					<div class="form-group">
						<label>Kode</label>
						<input type="text" placeholder="" id="code" name="code" class="form-control" value="">
					</div>
				</div>
				
				<div class="col-md-9 ">
					<div class="form-group">
						<label>Alamat 1</label>
						<input type="text" placeholder="" id="alamat1" name="alamat1" class="form-control" value="">
					</div>
				</div>
				
				<div class="col-md-9 ">
					<div class="form-group">
						<label>Alamat 2</label>
						<input type="text" placeholder="" id="alamat2" name="alamat2" class="form-control" value="">
					</div>
				</div>
				
				<div class="col-md-9 ">
					<div class="form-group">
						<label>Alamat 3</label>
						<input type="text" placeholder="" id="alamat3" name="alamat3" class="form-control" value="">
					</div>
				</div>
				
				<div class="col-md-5 ">
					<div class="form-group">
						<label>Kota</label>
						<input type="text" placeholder="" id="kota" name="kota" class="form-control" value="">
					</div>
				</div>
				
				<div class="col-md-4">
				    <div class="form-group">
    					<label>Zona Waktu Wilayah UPT</label>
    					<select class="form-control custom-control" id="zona_waktu_upt" name="zona_waktu_upt">
    						<option value="WIB">WIB</option>
    						<option value="WITA">WITA</option>
    						<option value="WIT">WIT</option>
    					</select>
					</div>
				 </div> 
				
				<div class="col-md-5 ">
					<div class="form-group">
						<label>Nama Petugas</label>
						<input type="text" placeholder="" id="nama_petugas" name="nama_petugas" class="form-control" value="">
					</div>
				</div>
				
				<div class="col-md-4 ">
					<div class="form-group">
						<label>Nip Petugas</label>
						<input type="text" placeholder="" id="nip_petugas" name="nip_petugas" class="form-control" value="">
					</div>
				</div>
				
				<div class="col-md-5 ">
					<div class="form-group">
						<label>Jabatan Petugas</label>
						<input type="text" placeholder="" id="jabatan_petugas" name="jabatan_petugas" class="form-control" value="">
					</div>
				</div>
				
				<div class="col-md-4 ">
					<div class="form-group">
						<label>Pangkat Petugas</label>
						<input type="text" placeholder="" id="pangkat_petugas" name="pangkat_petugas" class="form-control" value="">
					</div>
				</div>
				
			</div>';
		
		echo $xa;
		
	}
	
	public function getDataFormEdit($id){
		
		$ci = & get_instance();
		
		$sql 	= "SELECT * FROM `m_upt` WHERE m_upt_id = ".$id." ";
		$query  = $ci->db->query($sql);
		foreach ($query->result() as $list){
			
		}
		
		$xa = '';
		
		$xa = '
			<div class="row">
			
				 <div class="col-md-9 ">
					<div class="form-group">
						<label>Nama UPT</label>
						<input type="text" placeholder="" id="nama" name="nama" class="form-control" value="'.$list->nama.'">
					</div>
				</div>
				
				 <div class="col-md-9 ">
					<div class="form-group">
						<label>Kode</label>
						<input type="text" placeholder="" id="code" name="code" class="form-control" value="'.$list->code.'">
					</div>
				</div>
				
				<div class="col-md-9 ">
					<div class="form-group">
						<label>Alamat 1</label>
						<input type="text" placeholder="" id="alamat1" name="alamat1" class="form-control" value="'.$list->alamat1.'">
					</div>
				</div>
				
				<div class="col-md-9 ">
					<div class="form-group">
						<label>Alamat 2</label>
						<input type="text" placeholder="" id="alamat2" name="alamat2" class="form-control" value="'.$list->alamat2.'">
					</div>
				</div>
				
				<div class="col-md-9 ">
					<div class="form-group">
						<label>Alamat 3</label>
						<input type="text" placeholder="" id="alamat3" name="alamat3" class="form-control" value="'.$list->alamat3.'">
					</div>
				</div>
				
				<div class="col-md-5 ">
					<div class="form-group">
						<label>Kota</label>
						<input type="text" placeholder="" id="kota" name="kota" class="form-control" value="'.$list->kota.'">
					</div>
				</div>
				
				<div class="col-md-4">
				    <div class="form-group">
    					<label>Zona Waktu Wilayah UPT</label>
    					<select class="form-control custom-control" id="zona_waktu_upt" name="zona_waktu_upt">
    						<option value="WIB" '; if($list->zona_waktu_upt == "WIB"){$xa .='selected';} $xa .= '>WIB</option>
    						<option value="WITA" '; if($list->zona_waktu_upt == "WITA"){$xa .='selected';} $xa .= '>WITA</option>
    						<option value="WIT" '; if($list->zona_waktu_upt == "WIT"){$xa .='selected';} $xa .= '>WIT</option>
    					</select>
					</div>
				 </div>
				
				<div class="col-md-5 ">
					<div class="form-group">
						<label>Nama Petugas</label>
						<input type="text" placeholder="" id="nama_petugas" name="nama_petugas" class="form-control" value="'.$list->nama_petugas.'">
					</div>
				</div>
				
				<div class="col-md-4 ">
					<div class="form-group">
						<label>Nip Petugas</label>
						<input type="text" placeholder="" id="nip_petugas" name="nip_petugas" class="form-control" value="'.$list->nip_petugas.'">
					</div>
				</div>
				
				<div class="col-md-5 ">
					<div class="form-group">
						<label>Jabatan Petugas</label>
						<input type="text" placeholder="" id="jabatan_petugas" name="jabatan_petugas" class="form-control" value="'.$list->jabatan_petugas.'">
					</div>
				</div>
				
				<div class="col-md-4 ">
					<div class="form-group">
						<label>Pangkat Petugas</label>
						<input type="text" placeholder="" id="pangkat_petugas" name="pangkat_petugas" class="form-control" value="'.$list->pangkat_petugas.'">
					</div>
				</div>
				
				<input type="hidden" id="m_upt_id" name="m_upt_id"  value='.$id.' />
				
			</div>
			
		';
		
		
		echo $xa;
	}
	
}
