<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ba_penerimaan_hibah_bbm extends CI_Controller {
	
	
	public function getDataTable(){
		
		$ci = & get_instance();
		$sSearch	=  $this->input->post('sSearch');
		$iDisplayStart = $this->input->post('iDisplayStart');
		$iDisplayLength = $this->input->post('iDisplayLength');
		
		$cari = "";
 
		if($sSearch != NULL){
			$sSearch = strtoupper($sSearch);
			$cari .= " AND a.nomor_surat LIKE '%".$sSearch."%' ";
		}
		
		$sql = "SELECT * 
					FROM bbm_kapaltrans a WHERE status_ba = 17
				AND a.kapal_code IN (SELECT f.code_kapal FROM sys_user_kapal b JOIN m_kapal f ON b.m_kapal_id = f.m_kapal_id WHERE b.conf_user_id = '".$ci->session->userdata('userid')."')
					".$cari." 
				ORDER BY a.tanggal_surat DESC "; 
		
		$query = $ci->db->query($sql." LIMIT ".$iDisplayLength." OFFSET ".$iDisplayStart." ");
		$query2 = $ci->db->query($sql);
		$total = $query2->num_rows();
		
		if($total > 0){	
			$i = $iDisplayStart;
			foreach ($query->result() as $list){
				$i++;
				
				$nmr = str_replace('/','_',$list->nomor_surat);
				if($list->status_upload == 1){
					$prev = '<a href="'.base_url().'dokumen/dokumen_up_ba/'.$list->file_upload.'" target="_blank" class="btn-xs btn-warning">view</a>';
					$edit = '<button type="button" class="btn btn-xs btn-info" onClick="getTab4('.$list->trans_id.')" title="EDIT DATA" disabled><i class="fa fa-edit"></i></button>';
					$del = '<button type="button" class="btn btn-xs btn-danger" onClick="hapus('.$list->trans_id.')" title="HAPUS DATA" disabled><i class="fa fa-trash"></i></button>';
					
                }else{
				    $edit = '<button type="button" class="btn btn-xs btn-info" onClick="getTab4('.$list->trans_id.')" title="EDIT DATA" ><i class="fa fa-edit"></i></button>';
					$prev = '';
					$del = '<button type="button" class="btn btn-xs btn-danger" onClick="hapus('.$list->trans_id.')" title="HAPUS DATA"><i class="fa fa-trash"></i></button>';
				}
            
            	if($this->session->userdata('conf_group_id') == 1){
	                $rel = '<button type="button" class="btn btn-xs btn-outline-danger" onClick="rel('.$list->trans_id.')" title="RELEASE DATA"><i class="fa fa-times"></i></button>';
	            }else{
                	$rel = '';
                }
				
				$cUlang = '<button onclick="cetak('.$list->trans_id.')" class="btn btn-xs btn-warning">CETAK</button>';
				$upl = '<button type="button" class="btn btn-xs btn-success" onClick="getTab3('.$list->trans_id.')" title="Upload Dok" >Upload Dok</button>';
				
				$vData = '<button type="button" class="btn btn-xs" onClick="getTab5('.$list->trans_id.')" title="VIEW DATA" style="background-color:"red" "><i class="fa fa-eye"></i></button>';
				
	            if($this->session->userdata('conf_group_id') == 2){
	                $edit = '<button type="button" class="btn btn-xs btn-info" onClick="getTab4('.$list->trans_id.')" title="EDIT DATA" disabled><i class="fa fa-edit"></i></button>';
					$del = '<button type="button" class="btn btn-xs btn-danger" onClick="hapus('.$list->trans_id.')" title="HAPUS DATA" disabled><i class="fa fa-trash"></i></button>';
	            }
		
				
				$aaData[] = array(
					
				$i,
					$list->nomor_surat,
					$this->indo_date($list->tanggal_surat).' '.$list->jam_surat,
					$cUlang,
					$edit.' '.$vData.' '.$del.' '.$upl,
					$prev,
                	$rel
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
	
	function getDataUpload($trans_id){
		
		$ca = '';
		$ca .= '
		<div class="row">
			 
			
				 <div class="col-md-9 ">
					<div class="form-group">
						<label></label>
						<input type="file" name="my_images" class="form-control" />
					</div>
				</div>
				
				<input type="hidden" placeholder="" id="trans_id" name="trans_id" class="form-control" value="'.$trans_id.'">
			</div>';
		
		echo $ca;
	
	}
	
	function getUpt(){
		
		$ci = & get_instance();
		
		$sql = "SELECT * FROM `m_upt`
				WHERE code = '".$_POST['code_upt']."' "; 
		
		$query = $ci->db->query($sql);
		$total = $query->num_rows();
		$data = array();
		
		if($total>0){
		    
		    foreach ($query->result() as $list){
			
				$data['alamat1'] =  $list->alamat1;
				$data['nama'] =  $list->nama;
				
			 }
		}else{
		    
		    $data['alamat1'] = '';
		    $data['nama'] = '';
        	
		    
		}
		
		 echo json_encode($data);
	}
	
	function getDataEdit($trans_id){
		$ci = & get_instance();
		
		$sql = "SELECT * FROM `bbm_kapaltrans` WHERE trans_id = ".$trans_id." "; 
		$query = $ci->db->query($sql);
		foreach ($query->result() as $list){
			
		}
		
		$sql2 = "SELECT volume_sisa FROM `bbm_kapaltrans` WHERE nomor_surat = '".$list->link_modul_ba."' "; 
		$query2 = $ci->db->query($sql2);
		foreach ($query2->result() as $modul){
			
		}
		
		$sql3 = "SELECT code FROM `m_upt` WHERE nama = '".$list->instansi_temp."' "; 
		$query3 = $ci->db->query($sql3);
		
		$total = $query3->num_rows();
		if($total > 0){
		
			foreach ($query3->result() as $modul3){
				$code = $modul3->code;
			}
		}else{
			
			$code = '999';
		}
		
		if($list->an_staf == 1){
			$c_staf = 'checked';
		}else{
			$c_staf = '';
		}
		
		if($list->an_nakhoda == 1){
			$c_nakhoda = 'checked';
		}else{
			$c_nakhoda = '';
		}
		
		if($list->an_kkm == 1){
			$c_kkm = 'checked';
		}else{
			$c_kkm = '';
		}
		
		if($list->an_nakhoda_temp == 1){
			$an_nakhoda_temp = 'checked';
		}else{
			$an_nakhoda_temp = '';
		}
		
		if($list->an_kkm_temp == 1){
			$an_kkm_temp = 'checked';
		}else{
			$an_kkm_temp = '';
		}
		$ed = '';
		
		$ed = "<script>
		
    		$('.datepicker').datepicker({
                format: 'dd-mm-yyyy'
            });
            
        </script>";
		$ed .= '
			<div class="col-md-6">
					<div class="form-group">
						<label>Kapal</label>
						<select class="form-control custom-control" id="m_kapal_id" name="m_kapal_id"> ';
						
								$sql = "SELECT * FROM m_kapal WHERE code_kapal = '".$list->kapal_code."' "; 
								$query = $ci->db->query($sql);
								foreach ($query->result() as $r){
									$ed .=  '<option value="'.$r->m_kapal_id.'">'.$r->nama_kapal.'</option>';
								}
								
								$sql = "SELECT * FROM m_upt WHERE code = '".$r->m_upt_code."' "; 
								$query = $ci->db->query($sql);
								foreach ($query->result() as $rx){
								}
						$ed .= '</select>
					</div>
				</div>
				
				<div class="col-md-4 ">
					<div class="form-group">
						<label>KODE KAPAL</label>
						<input type="text" name="code_kapal" id="code_kapal" readonly="" class="form-control" value="'.$list->kapal_code.'">
					</div>
				</div>
				
				<div class="col-md-6 ">
					<div class="form-group">
						<label>ALAMAT UPT</label>
						<textarea name="alamat1" id="alamat1" readonly="" rows="3" cols="50" class="form-control" readonly="">'.$rx->alamat1.'</textarea>
					</div>
				</div>
				
				<div class="col-md-4 ">
					<div class="form-group">
						<label>LOKASI KAPAL</label>
						<textarea id="lokasi_surat" name="lokasi_surat" rows="3" cols="50" class="form-control">'.$list->lokasi_surat.'</textarea>
					</div>
				</div>
				
				
			</div>	
			
			<div class="row">
				  <div class="col-md-3 ">
					<label>TANGGAL</label>
					<input type="text" name="tanggal_surat" id="tanggal_surat" class="form-control datepicker" value="'.date("d-m-Y", strtotime($list->tanggal_surat)).'">
				 </div>
				 
				 <div class="col-md-3 ">
					<label>JAM </label>
					<input type="time" name="jam_surat" id="jam_surat" class="form-control" value="'.$list->jam_surat.'">
				 </div>
				 
				<div class="col-md-3"> 
					<label>Zona Waktu Surat</label>
					<select class="form-control custom-control" id="zona_waktu_surat" name="zona_waktu_surat">
						<option value="WIB" '; if($list->zona_waktu_surat == "WIB"){$ed .='selected';} $ed .= '>WIB</option>
						<option value="WITA" '; if($list->zona_waktu_surat == "WITA"){$ed .='selected';} $ed .= '>WITA</option>
						<option value="WIT" '; if($list->zona_waktu_surat == "WIT"){$ed .='selected';} $ed .= '>WIT</option>
					</select>
				 </div>
			</div>
			
			<div class="row">
			    
			    <div class="col-md-4 ">
					<div class="form-group">
						<label>LINK BA</label>
						    <input type="text" name="link_ba" id="link_ba" readonly="" class="form-control" value="'.$list->link_modul_ba.'">
					</div>
				</div>
				
				<div class="col-md-4 ">
					<div class="form-group">
						<label>NOMOR BA</label>
						<input type="text" name="nomor_surat" id="nomor_surat" readonly="" class="form-control" value="'.$list->nomor_surat.'">
					</div>
				</div>
			    
			</div>
			
			<div class="row">
				<div class="col-md-3 ">
					<div class="form-group">
						<label>INSTANSI PEMBERI</label>
						<select class="form-control custom-control" id="code_uptEd" name="code_uptEd" onChange="getUptEd()">';
								
								if($code == '999'){
									
									$ed .= '<option>INSTANSI LAINNYA</option>';
									
								}else{
									$sql0 = "SELECT * FROM m_upt a
										WHERE a.`code` NOT IN (SELECT d.m_upt_code FROM sys_user_kapal c JOIN m_kapal d ON d.m_kapal_id = c.m_kapal_id
										WHERE conf_user_id = '".$this->session->userdata('userid')."'   ) 
										GROUP BY a.`code` ORDER BY a.`code` ASC "; 
									$query0 = $ci->db->query($sql0);
									foreach ($query0->result() as $list0){
										$ed .= '<option value="'.$list0->code.'"'; if($list0->code == $code){$ed .='selected';} $ed .= '>'.$list0->nama.'</option>';
									}
								}
								
						$ed .='</select>
					</div>
					
					
				</div>
				
				<div class="col-md-3 ">
					<div class="form-group">
						<label>NAMA INSTANSI</label>
						<input type="text" id="instansi_tempEd" name="instansi_tempEd" class="form-control" value="'.$list->instansi_temp.'"/>
					</div>
				</div>
				
				<div class="col-md-3 ">
					<div class="form-group">
						<label>ALAMAT INSTANSI</label>
						<textarea id="alamat_instansi_tempEd" name="alamat_instansi_tempEd" rows="3" cols="50" class="form-control" >'.$list->alamat_instansi_temp.'</textarea>
					</div>
				</div>
			</div>
			
			<div class="row">
			
				<div class="col-md-4 ">
					<div class="form-group">
						<label>Penyedia</label>
						<input type="text" name="penyedia" name="penyedia" class="form-control" value="'.$list->penyedia.'">
					</div>
				</div>
				
				<div class="col-md-4 ">
					<div class="form-group">
						<label>JENIS BBM</label>
						<input type="text" placeholder="" id="keterangan_jenis_bbm" name="keterangan_jenis_bbm" class="form-control" value="'.$list->keterangan_jenis_bbm.'">
					</div>
				</div>
			    
				 
				 <div class="col-md-4">
					<label>Sisa BBM Sebelum Pengisian</label>
					<input type="text" name="volume_sisa" id="volume_sisa" class="form-control angka" value="'.number_format($modul->volume_sisa).'" readonly="">
				 </div>
				 
			</div>
			
			<div class="row">
				<div class="col-md-7 ">
					<div class="form-group">
						<label>NO SO</label>
						<input type="text" name="no_so" id="no_so" class="form-control" value="'.$list->no_so.'">
					</div>
				</div>	
			</div>
			
			<div class="row">
			
				 <div class="col-md-11">
					<label><b>DETAIL </b></label>
					<table id="myTablex" class="table">
					 <tr>
						<th>Transportasi</th>
						<th>NO.DO</th>
						<th>VOLUME</th>
						<th>KETERANGAN</th>
						<th><button type="button"  class="btn btn-info" onclick="myFunctionx()">ADD</button></th>
						<th><button type="button"  class="btn btn-danger" onclick="myDelx()">DEL</button></th>
					  </thead>
					</tr>';
						
						$sqTrans = "SELECT * FROM bbm_transdetail WHERE nomor_surat = '".$list->nomor_surat."' "; 
						$qTrans = $ci->db->query($sqTrans);
						foreach ($qTrans->result() as $rTrans){
							
							$ed .= '<tr>
								<td><input type="text" name="transportasix[]" id="transportasix" class="form-control" value="'.$rTrans->transportasi.'"> </td>
								<td><input type="text" name="no_dox[]" id="no_dox" class="form-control" value="'.$rTrans->no_do.'"></td>
								<td><input type="text" name="volume_isix[]" id="volume_isix" class="form-control angka" value="'.number_format($rTrans->volume_isi).'" onKeyUp="numericFilter(this);"></td>
								<td><input type="text" name="keteranganx[]" id="keteranganx" class="form-control" value="'.$rTrans->keterangan.'"></td>
							</tr>';
						}
						
					$ed .= '</table>
				 </div>
				
				
				
			</div>
			
			<div class="row">
			    
			    <div class="col-md-8">
					<label>PEJABAT/STAF UPT &nbsp;&nbsp;| &nbsp;&nbsp;<input type="checkbox" class="custom-control-input" id="an_staf" name="an_staf" value="1" '.$c_staf.'> An. </label>
					<input type="text" placeholder="" id="jabatan_staf_pangkalan" name="jabatan_staf_pangkalan" class="form-control" value="'.$list->jabatan_staf_pangkalan.'">
				 </div>
			</div>
			
			<div class="row">
			<br>
				<div class="col-md-4 ">
					<label>NAMA PEJABAT/STAF UPT </label>
					<input type="text" placeholder="" id="nama_petugas" name="nama_petugas" class="form-control" value="'.$list->nama_staf_pagkalan.'">
				 </div>
				 
				 <div class="col-md-4 ">
					<label>NIP </label>
						<input type="text" placeholder="" id="nip_petugas" name="nip_petugas" class="form-control" value="'.$list->nip_staf.'">
				</div>
				<div class="col-md-4 ">
					<label>Nama Petugas Penyedia</label>
					<input type="text" placeholder="" id="nama_penyedia" name="nama_penyedia" class="form-control" value="'.$list->penyedia.'">
				 </div>
				 
				
			</div>
			
			<div class="row">
			<br>
				 <div class="col-md-4 ">
					<label>NAMA NAKHODA &nbsp;&nbsp;| &nbsp;&nbsp;<input type="checkbox" class="custom-control-input" id="an_nakhoda" name="an_nakhoda" value="1" '.$c_nakhoda.'> An.  </label>
					<input type="text" placeholder="" id="nama_nakoda" name="nama_nakoda" class="form-control" value="'.$list->nama_nahkoda.'">
				 </div>
				 
				
				 
				 <div class="col-md-4 ">
					<label>NIP </label>
						<input type="text" placeholder="" id="nip_nakoda" name="nip_nakoda" class="form-control" value="'.$list->nip_nahkoda.'">
				 </div>
			 </div>
			
			<div class="row">	
			<br>
				 <div class="col-md-4 ">
					<label>NAMA KKM &nbsp;&nbsp;| &nbsp;&nbsp;<input type="checkbox" class="custom-control-input" id="an_kkm" name="an_kkm" value="1" '.$c_kkm.'> An. </label>
					<input type="text" placeholder="" id="nama_kkm" name="nama_kkm" class="form-control" value="'.$list->nama_kkm.'">
				 </div>
				 
				 <div class="col-md-4 ">
					<label>NIP </label>
						<input type="text" placeholder="" id="nip_kkm" name="nip_kkm" class="form-control" value="'.$list->nip_kkm.'">
				 </div>
			
			</div>
			
			<input type="hidden" id="trans_id" name="trans_id" class="form-control" value="'.$trans_id.'"/>	';
		
		echo $ed;
	
	}
	
	function getDataLihat($trans_id){
		$ci = & get_instance();
		
		$sql = "SELECT * FROM `bbm_kapaltrans` WHERE trans_id = ".$trans_id." "; 
		$query = $ci->db->query($sql);
		foreach ($query->result() as $list){
			
		}
		
		$sql2 = "SELECT volume_sisa FROM `bbm_kapaltrans` WHERE nomor_surat = '".$list->link_modul_ba."' "; 
		$query2 = $ci->db->query($sql2);
		foreach ($query2->result() as $modul){
			
		}
		
		$sql3 = "SELECT code FROM `m_upt` WHERE nama = '".$list->instansi_temp."' "; 
		$query3 = $ci->db->query($sql3);
		$total = $query3->num_rows();
		if($total > 0){
		
			foreach ($query3->result() as $modul3){
				$code = $modul3->code;
			}
		}else{
			
			$code = '999';
		}
		
		if($list->an_staf == 1){
			$c_staf = 'checked';
		}else{
			$c_staf = '';
		}
		
		if($list->an_nakhoda == 1){
			$c_nakhoda = 'checked';
		}else{
			$c_nakhoda = '';
		}
		
		if($list->an_kkm == 1){
			$c_kkm = 'checked';
		}else{
			$c_kkm = '';
		}
		
		// if($list->an_nakhoda_pemi == 1){
			// $an_nakhoda_peminjam = 'checked';
		// }else{
			// $an_nakhoda_peminjam = '';
		// }
		
		// if($list->an_kkm_peminjam == 1){
			// $an_kkm_peminjam = 'checked';
		// }else{
			// $an_kkm_peminjam = '';
		// }
		
    	$ed = '';
		
		$ed = "<script>
		
    		$('.datepicker').datepicker({
                format: 'dd-mm-yyyy'
            });
            
        </script>
        ";
        
        
		$ed .= '
		
			<div class="row">
			
				 <div class="col-md-7">
					<div class="form-group">
						<label>Kapal</label>
						<select class="form-control custom-control" id="m_kapal_id" name="m_kapal_id" disabled> ';
						
								$sql = "SELECT * FROM m_kapal WHERE code_kapal = '".$list->kapal_code."' "; 
								$query = $ci->db->query($sql);
								foreach ($query->result() as $r){
									$ed .=  '<option value="'.$r->m_kapal_id.'">'.$r->nama_kapal.'</option>';
								}
								
								$sql = "SELECT * FROM m_upt WHERE code = '".$r->m_upt_code."' "; 
								$query = $ci->db->query($sql);
								foreach ($query->result() as $rx){
								}
						$ed .= '</select>
					</div>
				</div>
				
				<div class="col-md-7 ">
					<div class="form-group">
						<label>KODE KAPAL</label>
						<input type="text" name="code_kapal" id="code_kapal" readonly="" class="form-control" value="'.$list->kapal_code.'">
					</div>
				</div>
				
				<div class="col-md-6 ">
					<div class="form-group">
						<label>ALAMAT UPT</label>
						<textarea id="alamat1" name="alamat1" rows="3" cols="50" class="form-control" readonly="">'.$rx->alamat1.'</textarea>
					</div>
				</div>
				
				<div class="col-md-4 ">
					<div class="form-group">
						<label>LOKASI KAPAL</label>
						<textarea id="lokasi_surat" name="lokasi_surat" rows="3" cols="50" class="form-control" disabled>'.$list->lokasi_surat.'</textarea>
					</div>
				</div>
				
				<div class="col-md-7 ">
					<div class="form-group">
						<label>NOMOR BA</label>
						<input type="text" name="nomor_surat" id="nomor_surat" readonly="" class="form-control" value="'.$list->nomor_surat.'" readonly="">
					</div>
				</div>	
				
			</div>	
			
			<div class="row">
				  <div class="col-md-3 ">
					<label>TANGGAL</label>
					<input type="text" name="tanggal_surat" id="tanggal_surat" class="form-control datepicker" value="'.date("d-m-Y", strtotime($list->tanggal_surat)).'" disabled>
				 </div>
				 
				 <div class="col-md-3 ">
					<label>JAM </label>
					<input type="time" name="jam_surat" id="jam_surat" class="form-control" value="'.$list->jam_surat.'" readonly="">
				 </div>
				 
				<div class="col-md-3"> 
					<label>Zona Waktu Surat</label>
					<select class="form-control custom-control" id="zona_waktu_surat" name="zona_waktu_surat" disabled>
						<option value="WIB" '; if($list->zona_waktu_surat == "WIB"){$ed .='selected';} $ed .= '>WIB</option>
						<option value="WITA" '; if($list->zona_waktu_surat == "WITA"){$ed .='selected';} $ed .= '>WITA</option>
						<option value="WIT" '; if($list->zona_waktu_surat == "WIT"){$ed .='selected';} $ed .= '>WIT</option>
					</select>
				 </div>
			</div>
			
			<div class="row">
			    
			    <div class="col-md-4 ">
					<div class="form-group">
						<label>LINK BA</label>
						    <input type="text" name="link_ba" id="link_ba" readonly="" class="form-control" value="'.$list->link_modul_ba.'">
					</div>
				</div>
				
				<div class="col-md-4 ">
					<div class="form-group">
						<label>NOMOR BA</label>
						<input type="text" name="nomor_surat" id="nomor_surat" readonly="" class="form-control" value="'.$list->nomor_surat.'">
					</div>
				</div>
			    
			</div>
			
			<div class="row">
				<div class="col-md-3 ">
					<div class="form-group">
						<label>INSTANSI PEMBERI</label>
						<select class="form-control custom-control" id="code_uptEd" name="code_uptEd" disabled>';
								
								if($code == '999'){
									
									$ed .= '<option>INSTANSI LAINNYA</option>';
									
								}else{
									$sql0 = "SELECT * FROM m_upt a
										WHERE a.`code` NOT IN (SELECT d.m_upt_code FROM sys_user_kapal c JOIN m_kapal d ON d.m_kapal_id = c.m_kapal_id
										WHERE conf_user_id = '".$this->session->userdata('userid')."'   ) 
										GROUP BY a.`code` ORDER BY a.`code` ASC "; 
									$query0 = $ci->db->query($sql0);
									foreach ($query0->result() as $list0){
										$ed .= '<option value="'.$list0->code.'"'; if($list0->code == $code){$ed .='selected';} $ed .= '>'.$list0->nama.'</option>';
									}
								}
								
							
						$ed .='</select>
					</div>
					
					
				</div>
				
				<div class="col-md-3 ">
					<div class="form-group">
						<label>NAMA INSTANSI</label>
						<input type="text" id="instansi_tempEd" name="instansi_tempEd" class="form-control" value="'.$list->instansi_temp.'" readonly=""/>
					</div>
				</div>
				
				<div class="col-md-3 ">
					<div class="form-group">
						<label>ALAMAT INSTANSI</label>
						<textarea id="alamat_instansi_tempEd" name="alamat_instansi_tempEd" rows="3" cols="50" class="form-control" disabled>'.$list->alamat_instansi_temp.'</textarea>
					</div>
				</div>
			</div>
			
			<div class="row">
			
				<div class="col-md-4 ">
					<div class="form-group">
						<label>Penyedia</label>
						<input type="text" name="penyedia" name="penyedia" class="form-control" value="'.$list->penyedia.'" readonly="">
					</div>
				</div>
				
				<div class="col-md-4 ">
					<div class="form-group">
						<label>JENIS BBM</label>
						<input type="text" placeholder="" id="keterangan_jenis_bbm" name="keterangan_jenis_bbm" class="form-control" value="'.$list->keterangan_jenis_bbm.'" readonly="">
					</div>
				</div>
			    
				 
				 <div class="col-md-4">
					<label>Sisa BBM Sebelum Pengisian</label>
					<input type="text" name="volume_sisa" id="volume_sisa" class="form-control angka" value="'.number_format($modul->volume_sisa).'" readonly="">
				 </div>
				 
			</div>
			
			<div class="row">
				<div class="col-md-7 ">
					<div class="form-group">
						<label>NO SO</label>
						<input type="text" name="no_so" id="no_so" class="form-control" value="'.$list->no_so.'" readonly="">
					</div>
				</div>	
			</div>
			
			<div class="row">
			
				 <div class="col-md-11">
					<label><b>DETAIL </b></label>
					<table id="myTablex" class="table">
					 <tr>
						<th>Transportasi</th>
						<th>NO.DO</th>
						<th>VOLUME</th>
						<th>KETERANGAN</th>
					  </thead>
					</tr>';
						
						$sqTrans = "SELECT * FROM bbm_transdetail WHERE nomor_surat = '".$list->nomor_surat."' "; 
						$qTrans = $ci->db->query($sqTrans);
						foreach ($qTrans->result() as $rTrans){
							
							$ed .= '<tr>
								<td><input type="text" name="transportasix[]" id="transportasix" class="form-control" value="'.$rTrans->transportasi.'" readonly=""> </td>
								<td><input type="text" name="no_dox[]" id="no_dox" class="form-control" value="'.$rTrans->no_do.'" readonly=""></td>
								<td><input type="text" name="volume_isix[]" id="volume_isix" class="form-control angka" value="'.number_format($rTrans->volume_isi).'" onKeyUp="numericFilter(this);" readonly=""></td>
								<td><input type="text" name="keteranganx[]" id="keteranganx" class="form-control" value="'.$rTrans->keterangan.'" readonly=""></td>
							</tr>';
						}
						
					$ed .= '</table>
				 </div>
				
				
				
			</div>
			
			<div class="row">
			    
			    <div class="col-md-8">
					<label>PEJABAT/STAF UPT &nbsp;&nbsp;| &nbsp;&nbsp;<input type="checkbox" class="custom-control-input" id="an_staf" name="an_staf" value="1" '.$c_staf.'> An. </label>
					<input type="text" placeholder="" id="jabatan_staf_pangkalan" name="jabatan_staf_pangkalan" class="form-control" value="'.$list->jabatan_staf_pangkalan.'" readonly="">
				 </div>
			</div>
			
			<div class="row">
			<br>
				<div class="col-md-4 ">
					<label>NAMA PEJABAT/STAF UPT </label>
					<input type="text" placeholder="" id="nama_petugas" name="nama_petugas" class="form-control" value="'.$list->nama_staf_pagkalan.'" readonly="">
				 </div>
				 
				 <div class="col-md-4 ">
					<label>NIP </label>
						<input type="text" placeholder="" id="nip_petugas" name="nip_petugas" class="form-control" value="'.$list->nip_staf.'" readonly="">
				</div>
				<div class="col-md-4 ">
					<label>Nama Penyedia</label>
					<input type="text" placeholder="" id="nama_penyedia" name="nama_penyedia" class="form-control" value="'.$list->penyedia.'" readonly="">
				 </div>
				 
				
			</div>
			
			<div class="row">
			<br>
				 <div class="col-md-4 ">
					<label>NAMA NAKHODA &nbsp;&nbsp;| &nbsp;&nbsp;<input type="checkbox" class="custom-control-input" id="an_nakhoda" name="an_nakhoda" value="1" '.$c_nakhoda.'> An.  </label>
					<input type="text" placeholder="" id="nama_nakoda" name="nama_nakoda" class="form-control" value="'.$list->nama_nahkoda.'" readonly="">
				 </div>
				 
				 
				 <div class="col-md-4 ">
					<label>NIP </label>
						<input type="text" placeholder="" id="nip_nakoda" name="nip_nakoda" class="form-control" value="'.$list->nip_nahkoda.'" readonly="">
				 </div>
			 </div>
			
			<div class="row">	
			<br>
				 <div class="col-md-4 ">
					<label>NAMA KKM &nbsp;&nbsp;| &nbsp;&nbsp;<input type="checkbox" class="custom-control-input" id="an_kkm" name="an_kkm" value="1" '.$c_kkm.'> An. </label>
					<input type="text" placeholder="" id="nama_kkm" name="nama_kkm" class="form-control" value="'.$list->nama_kkm.'" readonly="">
				 </div>
				 
				 <div class="col-md-4 ">
					<label>NIP </label>
						<input type="text" placeholder="" id="nip_kkm" name="nip_kkm" class="form-control" value="'.$list->nip_kkm.'" readonly="">
				 </div>
			
			</div>
			
			<input type="hidden" id="trans_id" name="trans_id" class="form-control" value="'.$trans_id.'"/>	';
		
		echo $ed;
	
	}
	
	function indo_date($tgl){
		
		$tgl_s = date('j',strtotime($tgl));
		$bln_s = $this->get_bulan(date('n',strtotime($tgl)));
		$thn_s = date('Y',strtotime($tgl));
		return $tgl_s.' '.$bln_s.' '.$thn_s;
	}
	
	function get_bulan($bln){
		switch($bln)
		{
			case '1':
				$nama_bln = 'Januari';
			break;
			case '2':
				$nama_bln = 'Februari';
			break;
			case '3':
				$nama_bln = 'Maret';
			break;
			case '4':
				$nama_bln = 'April';
			break;
			case '5':
				$nama_bln = 'Mei';
			break;
			case '6':
				$nama_bln = 'Juni';
			break;
			case '7':
				$nama_bln = 'Juli';
			break;
			case '8':
				$nama_bln = 'Agustus';
			break;
			case '9':
				$nama_bln = 'September';
			break;
			case '10':
				$nama_bln = 'Oktober';
			break;
			case '11':
				$nama_bln = 'November';
			break;
			case '12':
				$nama_bln = 'Desember';
			break;
		}
		return $nama_bln;
	}
	
}
