<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master_kapal extends CI_Controller {
	
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
			$cari .= " AND m_kapal.nama_kapal LIKE '%".$sSearch."%' ";
		}
		
		$sql = "SELECT *
		FROM `m_kapal` 
		JOIN m_upt b ON b.code = m_kapal.m_upt_code ".$cari; 
	//	var_dump($sql);
		$query = $ci->db->query($sql." LIMIT ".$iDisplayLength." OFFSET ".$iDisplayStart." ");
		$query2 = $ci->db->query($sql);
		$total = $query2->num_rows();
		
		
		if($total > 0){	
			$i = $iDisplayStart;
			foreach ($query->result() as $list){
				$i++;
				$aaData[] = array(
					
					$i,
					$list->nama_kapal,
					$list->code_kapal,
					$list->nama_nakoda,
					$list->nama,
					$list->galangan_pembuat,
					$list->tahun_buat,
					'<button type="button" class="btn btn-secondary" onClick="" title="DETAIL" ><i class="fa fa-eye"></i></button> 
					<button type="button" class="btn btn-warning" onClick="getTab3('.$list->m_kapal_id.')" title="EDIT" ><i class="fa fa-edit"></i></button> 
					<button type="button" class="btn btn-danger" onClick="hapus('.$list->m_kapal_id.')" title="HAPUS" ><i class="fa fa-trash"></i></button>'
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
		$ci = & get_instance();
		$xa = '';


		$xa = '
			<div class="row">
			
				 <div class="col-md-9 ">
					<div class="form-group">
						<label>Nama Kapal</label>
						<input type="text" placeholder="" id="nama_kapal" name="nama_kapal" class="form-control" value="">
					</div>
				</div>
				
				<div class="col-md-9 ">
					<div class="form-group">
						<label>Kode Kapal</label>
						<input type="text" placeholder="" id="code_kapal" name="code_kapal" class="form-control" value="">
					</div>
				</div>
				
				<div class="col-md-9 ">
					<div class="form-group">
						<label>UPT</label>
							<select class="form-control custom-control" id="m_upt_code" name="m_upt_code" >
								<option value="-">- PILIH -</option>';
						
								$sql = "SELECT * FROM m_upt WHERE code != '000' "; 
								$query = $ci->db->query($sql);
								foreach ($query->result() as $list){
									$xa .= '<option value="'.$list->code.'">'.$list->nama.'</option>';
								}
							
						$xa .= '</select>
					</div>
				</div>
				
				
			</div>
				
			<div class="row">
			
				  <div class="col-md-2">
					<label>Panjang </label>
					<input type="text" placeholder="" id="panjang" name="panjang" class="form-control" data-rule-number="true" data-rule-required="true" value="">
				 </div>
				 
				 <div class="col-md-2">
					<label>Tinggi </label>
					<input type="text" placeholder="" id="tinggi" name="tinggi" class="form-control" data-rule-number="true" data-rule-required="true" value="">
				 </div>
				 
				 <div class="col-md-2">
					<label>Lebar </label>
					<input type="text" placeholder="" id="lebar" name="lebar" class="form-control" data-rule-number="true" data-rule-required="true" value="">
				 </div>
				 
				  <div class="col-md-2">
					<label>Bobot</label>
					<input type="text" placeholder="" id="bobot" name="bobot" class="form-control" data-rule-number="true" data-rule-required="true" value="">
				 </div>
				 
			</div>
			
			<br>
			<div class="row">
				
				<div class="col-md-3 ">
					<div class="form-group">
						<label>Mesin Induk </label>
						<input type="text" placeholder="" id="main_engine" name="main_engine" class="form-control" value="">
					</div>
				</div>
				
				<div class="col-md-3 ">
					<div class="form-group">
						<label>Jumlah Mesin Induk </label>
						<input type="text" placeholder="" id="jml_main_engine" name="jml_main_engine" class="form-control" value="">
					</div>
				</div>
				
				<div class="col-md-3 ">
					<div class="form-group">
						<label>PK Mesin Induk </label>
						<input type="text" placeholder="" id="pk_main_engine" name="pk_main_engine" class="form-control" value="">
					</div>
				</div>
				
			</div>
			
			<br>
			<div class="row">
				
				<div class="col-md-3 ">
					<label>Mesin Bantu Utama </label>
					<input type="text" placeholder="" id="aux_engine_utama" name="aux_engine_utama" class="form-control" data-rule-number="true" data-rule-required="true" value="">
				</div>
				
				<div class="col-md-3 ">
					<div class="form-group">
						<label>Jumlah Mesin Induk </label>
						<input type="text" placeholder="" id="jml_aux_engine_utama" name="jml_aux_engine_utama" class="form-control" value="">
					</div>
				</div>
				
				<div class="col-md-3 ">
					<div class="form-group">
						<label>PK Mesin Induk </label>
						<input type="text" placeholder="" id="pk_aux_engine_utama" name="pk_aux_engine_utama" class="form-control" value="">
					</div>
				</div>
				
			</div>
			
			<div class="row">
		          
		          <div class="col-md-4 ">
					<label>Mesin Bantu Emergency </label>
					<input type="text" placeholder="" id="aux_engine_emergency" name="aux_engine_emergency" class="form-control" data-rule-number="true" data-rule-required="true" value="">
				 </div>    
		          
		          <div class="col-md-4 ">
					<div class="form-group">
						<label>Mesin Penggerak (MTU)</label>
						<input type="text" placeholder="" id="gerak_engine" name="gerak_engine" class="form-control" value="">
					</div>
				</div> 
				 
			</div>
			
			
			<div class="row">
			    <div class="col-md-3 ">
					<div class="form-group">
						<label>Kapasitas Tangki BBM</label>
						<input type="text" placeholder="" id="kapasitas_tangki" name="kapasitas_tangki" class="form-control" value="">
					</div>
				</div>	
				 
				
			   <div class="col-md-3 ">
					<div class="form-group">
						<label>Tahun Buat</label>
						<input type="text" placeholder="" id="tahun_buat" name="tahun_buat" class="form-control" value="">
					</div>
				</div>
				
				<div class="col-md-3 ">
					<div class="form-group">
						<label>Jumlah Tangki</label>
						<input type="text" placeholder="" id="jml_tangki" name="jml_tangki" class="form-control" value="">
					</div>
				</div>
				
			</div>
			
			<div class="row">
			    <div class="col-md-7 ">
					<div class="form-group">
						<label>Galangan Pembuatan</label>
						<input type="text" placeholder="" id="galangan_pembuat" name="galangan_pembuat" class="form-control" value="">
					</div>
				</div>
				
		        <div class="col-md-2 ">
					<div class="form-group">
						<label>Jumlah ABK</label>
						<input type="text" placeholder="" id="jml_abk" name="jml_abk" class="form-control" value="">
					</div>
				</div>
				
			</div>
			
			<br>
			<div class="row">
				
				 <div class="col-md-9 ">
					<div class="form-group">
						<label><b>Detail Petugas</b></label>
					</div>
				</div>
				
				 <div class="col-md-5 ">
					<div class="form-group">
						<label><b>Nama Nakhoda</b></label>
						<input type="text" placeholder="" id="nama_nakoda" name="nama_nakoda" class="form-control" value="">
					</div>
				</div>
				
				<div class="col-md-4 ">
					<div class="form-group">
						<label><b>Nip Nakhoda</b></label>
						<input type="text" placeholder="" id="nip_nakoda" name="nip_nakoda" class="form-control" value="">
					</div>
				</div>
				
				 <div class="col-md-5 ">
					<div class="form-group">
						<label><b>Jabatan Nakhoda</b></label>
						<input type="text" placeholder="" id="jabatan_nakoda" name="jabatan_nakoda" class="form-control" value="">
					</div>
				</div>
				
				<div class="col-md-4 ">
					<div class="form-group">
						<label><b>Pangkat Nakhoda</b></label>
						<input type="text" placeholder="" id="pangkat_nakoda" name="pangkat_nakoda" class="form-control" value="">
					</div>
				</div>
				
				<div class="col-md-5 ">
					<div class="form-group">
						<label><b>Nama KKM</b></label>
						<input type="text" placeholder="" id="nama_kkm" name="nama_kkm" class="form-control" value="">
					</div>
				</div>
				
				<div class="col-md-4 ">
					<div class="form-group">
						<label><b>Nip KKM</b></label>
						<input type="text" placeholder="" id="nip_kkm" name="nip_kkm" class="form-control" value="">
					</div>
				</div>
				
				 <div class="col-md-5 ">
					<div class="form-group">
						<label><b>Jabatan KKM</b></label>
						<input type="text" placeholder="" id="jabatan_kkm" name="jabatan_kkm" class="form-control" value="">
					</div>
				</div>
				
				<div class="col-md-4 ">
					<div class="form-group">
						<label><b>Pangkat KKM</b></label>
						<input type="text" placeholder="" id="pangkat_kkm" name="pangkat_kkm" class="form-control" value="">
					</div>
				</div>
				
			</div>
			
			<div class="row">
			
			    <div class="col-md-9 ">
					<div class="form-group">
						<label>Gambar Kapal</label>
						<input type="file" name="my_images" class="form-control" ></input><span><font color="red">format gambar kapal .jpg .png</font></span>
					</div>
				</div>
				
				<div class="col-md-9 ">
					<div class="form-group">
						<label>Lampiran Kapal</label>
						<input type="file" name="lampiran_kapal" class="form-control" /><span><font color="red">format lampiran .pdf</font></span>
					</div>
				</div>
				
			</div>';
		
		
		echo $xa;
	}
	
	public function getDataFormEdit($id){
		
		$ci = & get_instance();
		
		$sql = "SELECT * FROM `m_kapal` WHERE m_kapal_id = ".$id." ";
		$query = $ci->db->query($sql);
		foreach ($query->result() as $list){
			
		}
		
		$lamp_kapal = $list->lampiran_kapal;
		//var_dump($lamp_kapal);
		$gambar_kapal = $list->gambar_kapal;
		$xa = '';


		$xa = '
			<div class="row">
			
				 <div class="col-md-9 ">
					<div class="form-group">
						<label>Nama Kapal</label>
						<input type="text" placeholder="" id="nama_kapal" name="nama_kapal" class="form-control" value="'.$list->nama_kapal.'">
					</div>
				</div>
				
				<div class="col-md-9 ">
					<div class="form-group">
						<label>Kode Kapal</label>
						<input type="text" placeholder="" id="code_kapal" name="code_kapal" readonly="" class="form-control" value="'.$list->code_kapal.'">
					</div>
				</div>
				
				
				<div class="col-md-9 ">
					<div class="form-group">
						<label>UPT</label>
							<select class="form-control custom-control" id="m_upt_code" name="m_upt_code" >
								<option value="-">- PILIH -</option>';
						
								$sql = "SELECT * FROM m_upt"; 
								$query = $ci->db->query($sql);
								foreach ($query->result() as $r){
									$xa .= '<option value="'.$r->code.'" '; if($list->m_upt_code == $r->code){ $xa .= 'selected';} $xa .= '>'.$r->nama.'</option>';
								}
							
						$xa .= '</select>
					</div>
				</div>
			</div>
				
			
			<div class="row">
			
				  <div class="col-md-2">
					<label>Panjang </label>
					<input type="text" placeholder="" id="panjang" name="panjang" class="form-control" data-rule-number="true" data-rule-required="true" value="'.floatval($list->panjang).'">
				 </div>
				 
				 <div class="col-md-2">
					<label>Tinggi </label>
					<input type="text" placeholder="" id="tinggi" name="tinggi" class="form-control" data-rule-number="true" data-rule-required="true" value="'.floatval($list->tinggi).'">
				 </div>
				 
				 <div class="col-md-2">
					<label>Lebar </label>
					<input type="text" placeholder="" id="lebar" name="lebar" class="form-control" data-rule-number="true" data-rule-required="true" value="'.floatval($list->lebar).'">
				 </div>
				 
				  <div class="col-md-2">
					<label>Bobot</label>
					<input type="text" placeholder="" id="bobot" name="bobot" class="form-control" data-rule-number="true" data-rule-required="true" value="'.floatval($list->bobot).'">
				 </div>
				 
			</div>
			
			<br>
			<div class="row">
				
				<div class="col-md-3 ">
					<div class="form-group">
						<label>Mesin Induk </label>
						<input type="text" placeholder="" id="main_engine" name="main_engine" class="form-control" value="'.$list->main_engine.'">
					</div>
				</div>
				
				<div class="col-md-3 ">
					<div class="form-group">
						<label>Jumlah Mesin Induk </label>
						<input type="text" placeholder="" id="jml_main_engine" name="jml_main_engine" class="form-control" value="'.$list->jml_main_engine.'">
					</div>
				</div>
				
				<div class="col-md-3 ">
					<div class="form-group">
						<label>PK Mesin Induk </label>
						<input type="text" placeholder="" id="pk_main_engine" name="pk_main_engine" class="form-control" value="'.$list->pk_main_engine.'">
					</div>
				</div>
				
			</div>
			
			<br>
			<div class="row">
				
				<div class="col-md-3 ">
					<label>Mesin Bantu Utama </label>
					<input type="text" placeholder="" id="aux_engine_utama" name="aux_engine_utama" class="form-control" data-rule-number="true" data-rule-required="true" value="'.$list->aux_engine_utama.'">
				</div>
				
				<div class="col-md-3 ">
					<div class="form-group">
						<label>Jumlah Mesin Induk </label>
						<input type="text" placeholder="" id="jml_aux_engine_utama" name="jml_aux_engine_utama" class="form-control" value="'.$list->jml_aux_engine_utama.'">
					</div>
				</div>
				
				<div class="col-md-3 ">
					<div class="form-group">
						<label>PK Mesin Induk </label>
						<input type="text" placeholder="" id="pk_aux_engine_utama" name="pk_aux_engine_utama" class="form-control" value="'.$list->pk_aux_engine_utama.'">
					</div>
				</div>
				
			</div>
			
			<div class="row">
		          
		          <div class="col-md-4 ">
					<label>Mesin Bantu Emergency </label>
					<input type="text" placeholder="" id="aux_engine_emergency" name="aux_engine_emergency" class="form-control" data-rule-number="true" data-rule-required="true" value="'.$list->aux_engine_emergency.'">
				 </div>    
		          
		          <div class="col-md-4 ">
					<div class="form-group">
						<label>Mesin Penggerak (MTU)</label>
						<input type="text" placeholder="" id="gerak_engine" name="gerak_engine" class="form-control" value="'.$list->gerak_engine.'">
					</div>
				</div> 
				 
			</div>
			
			
			<div class="row">
			    <div class="col-md-3 ">
					<div class="form-group">
						<label>Kapasitas Tangki BBM</label>
						<input type="text" placeholder="" id="kapasitas_tangki" name="kapasitas_tangki" class="form-control" value="'.$list->kapasitas_tangki.'">
					</div>
				</div>
				 
				
			   <div class="col-md-3 ">
					<div class="form-group">
						<label>Tahun Buat</label>
						<input type="text" placeholder="" id="tahun_buat" name="tahun_buat" class="form-control" value="'.$list->tahun_buat.'">
					</div>
				</div>
				
				<div class="col-md-3 ">
					<div class="form-group">
						<label>Jumlah Tangki</label>
						<input type="text" placeholder="" id="jml_tangki" name="jml_tangki" class="form-control" value="'.$list->jml_tangki.'">
					</div>
				</div>
				
			</div>
			
			<div class="row">
			    <div class="col-md-7 ">
					<div class="form-group">
						<label>Galangan Pembuatan</label>
						<input type="text" placeholder="" id="galangan_pembuat" name="galangan_pembuat" class="form-control" value="'.$list->galangan_pembuat.'">
					</div>
				</div>
				
		        <div class="col-md-2 ">
					<div class="form-group">
						<label>Jumlah ABK</label>
						<input type="text" placeholder="" id="jml_abk" name="jml_abk" class="form-control" value="'.$list->jml_abk.'">
					</div>
				</div>
				
			</div>
			
			<br>
			<div class="row">
				
				 <div class="col-md-9 ">
					<div class="form-group">
						<label><b>Detail Petugas</b></label>
					</div>
				</div>
				
				 <div class="col-md-5 ">
					<div class="form-group">
						<label><b>Nama Nakhoda</b></label>
						<input type="text" placeholder="" id="nama_nakoda" name="nama_nakoda" class="form-control" value="'.$list->nama_nakoda.'">
					</div>
				</div>
				
				<div class="col-md-4 ">
					<div class="form-group">
						<label><b>Nip Nakhoda</b></label>
						<input type="text" placeholder="" id="nip_nakoda" name="nip_nakoda" class="form-control" value="'.$list->nip_nakoda.'">
					</div>
				</div>
				
				 <div class="col-md-5 ">
					<div class="form-group">
						<label><b>Jabatan Nakhoda</b></label>
						<input type="text" placeholder="" id="jabatan_nakoda" name="jabatan_nakoda" class="form-control" value="'.$list->jabatan_nakoda.'">
					</div>
				</div>
				
				<div class="col-md-4 ">
					<div class="form-group">
						<label><b>Pangkat Nakhoda</b></label>
						<input type="text" placeholder="" id="pangkat_nakoda" name="pangkat_nakoda" class="form-control" value="'.$list->pangkat_nakoda.'">
					</div>
				</div>
				
				<div class="col-md-5 ">
					<div class="form-group">
						<label><b>Nama KKM</b></label>
						<input type="text" placeholder="" id="nama_kkm" name="nama_kkm" class="form-control" value="'.$list->nama_kkm.'">
					</div>
				</div>
				
				<div class="col-md-4 ">
					<div class="form-group">
						<label><b>Nip KKM</b></label>
						<input type="text" placeholder="" id="nip_kkm" name="nip_kkm" class="form-control" value="'.$list->nip_kkm.'">
					</div>
				</div>
				
				 <div class="col-md-5 ">
					<div class="form-group">
						<label><b>Jabatan KKM</b></label>
						<input type="text" placeholder="" id="jabatan_kkm" name="jabatan_kkm" class="form-control" value="'.$list->jabatan_kkm.'">
					</div>
				</div>
				
				<div class="col-md-4 ">
					<div class="form-group">
						<label><b>Pangkat KKM</b></label>
						<input type="text" placeholder="" id="pangkat_kkm" name="pangkat_kkm" class="form-control" value="'.$list->pangkat_kkm.'">
					</div>
				</div>
				
			</div>
			
			<div class="row">
			
			    <div class="col-md-9 ">
					<div class="form-group">
						<label>Gambar Kapal</label>
						<input type="file" name="my_images" class="form-control" ></input><span><font color="red">format gambar kapal .jpg .png</font></span>
					</div>
				</div>
				
				<div class="col-md-9">
					<div class="form-group">
						<label>Detail gambar</label>';
							if($gambar_kapal != ''){
								
								$xa .= ' <img src="'.base_url().'dokumen/kapal_detail/'.$list->gambar_kapal.'" width="200" height="200">';
							}
					$xa .= '</div>
				</div>
				
				<div class="col-md-9 ">
					<div class="form-group">
						<label>Lampiran Kapal</label>
						<input type="file" name="lampiran_kapal" class="form-control" /><span><font color="red">format lampiran .pdf</font></span>
					</div>
				</div>
				
				<div class="col-md-9">
					<div class="form-group">';
							if($lamp_kapal != ''){
								
								$xa .= '<embed src="'.base_url().'dokumen/lampiran_kapal/'.$list->lampiran_kapal.'" width="500" height="500" type="application/pdf">';
							}
					$xa .= '</div>
				</div>
				
				
			</div>
			
		</div>
				
	    	
			
			<input type="hidden" id="m_kapal_id" name="m_kapal_id"  value='.$id.' />
		';
		
		
		echo $xa;
	
	}
	
	function getDataFormDetail(){
	
		$za = '';
	
		$za .='<div class="col-md-3">
						<div class="form-group">
							<label>TEXT</label>
							<input type="text" placeholder="" id="username" name="username" class="form-control" value="" disabled>
						</div>
					</div>';
					
		echo $za;
	
	}
	
}
