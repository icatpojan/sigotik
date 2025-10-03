<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ba_pemberi_hibah_bbm_kapal_pengawas extends CI_Controller {
	
	
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
					FROM bbm_kapaltrans a WHERE status_ba = 14
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
	
	public function getDataHibahBbm(){
		
		// var_dump("assdads");die();
		
		$ci = & get_instance();
		
		$sql = "SELECT  
					a.kapal_code			,
    				a.nomor_surat          ,
    				a.tanggal_surat        ,
    				a.jam_surat            ,
    				a.zona_waktu_surat     ,
    				a.lokasi_surat         ,
    				a.volume_pemakaian      ,
    				a.jabatan_staf_pangkalan,
    				a.nama_staf_pagkalan   ,
    				a.nip_staf             ,
    				a.nama_nahkoda         ,
    				a.nip_nahkoda          ,
    				a.nama_kkm             ,
    				a.nip_kkm              ,
    				a.tanggalinput         ,
    				a.user_input           ,
					a.an_staf			,
					a.an_nakhoda		,
					a.an_kkm     		,
					a.kapal_code_temp  ,
					a.keterangan_jenis_bbm ,
					a.sebab_temp	,
					a.pangkat_nahkoda		,
					a.nama_nahkoda_temp	,
					a.pangkat_nahkoda_temp	,
					a.nip_nahkoda_temp	,
					a.nama_kkm_temp	,
					a.nip_kkm_temp	,
					a.an_kkm_temp	,
					a.an_nakhoda_temp	
				FROM `bbm_kapaltrans` a
				
				WHERE trans_id = ".$_POST['trans_id']." ";
				
		$query = $ci->db->query($sql);
		
		$data = array();
		foreach ($query->result() as $list){
			
			$data['nomor_surat'] = $list->nomor_surat;
			
			$data['kapal_code_temp'] = $list->kapal_code_temp;
			$data['kapal_code'] = $list->kapal_code;
			$data['keterangan_jenis_bbm'] = $list->keterangan_jenis_bbm;
			$data['sebab_temp'] = $list->sebab_temp;
			$data['volume_pemakaian'] = $list->volume_pemakaian;
			
			$data['nama_nakoda'] = $list->nama_nahkoda;
			$data['nip_nakoda'] = $list->nip_nahkoda;
			$data['pangkat_nahkoda'] = $list->pangkat_nahkoda;
			
			$data['nama_kkm'] = $list->nama_kkm;
			$data['nip_kkm'] = $list->nip_kkm;
			
			$data['nama_nahkoda_temp'] = $list->nama_nahkoda_temp;
			$data['pangkat_nahkoda_temp'] = $list->pangkat_nahkoda_temp;
			$data['nip_nahkoda_temp'] = $list->nip_nahkoda_temp;
			
			$data['nama_kkm_temp'] = $list->nama_kkm_temp;
			$data['nip_kkm_temp'] = $list->nip_kkm_temp;
			
			if($list->an_staf == 1){
				$data['an_staf'] = true;
			}else{
				$data['an_staf'] = false;
			}
			
			if($list->an_nakhoda == 1){
				$data['an_nakhoda'] = true;
			}else{
				$data['an_nakhoda'] = false;
			}
			
			if($list->an_kkm == 1){
				$data['an_kkm'] = true;
			}else{
				$data['an_kkm'] = false;
			}
			
			if($list->an_nakhoda_temp == 1){
				$data['an_nakhoda_temp'] = true;
			}else{
				$data['an_nakhoda_temp'] = false;
			}
			
			if($list->an_kkm_temp == 1){
				$data['an_kkm_temp'] = true;
			}else{
				$data['an_kkm_temp'] = false;
			}
			
		}
		
		echo json_encode($data);
		
	}
	
	function getDataBa(){
	    $ci = & get_instance();
	    
		
	    $tanggal_surat = date("Y-m-d", strtotime($_POST['tanggal_surat']));
	    // var_dump($tanggal_surat);die();
    	
    	$sql = "SELECT * FROM `bbm_kapaltrans` 
		            JOIN `m_kapal` ON `m_kapal`.code_kapal = `bbm_kapaltrans`.kapal_code
		    WHERE tanggal_surat <= '".$tanggal_surat."' AND status_ba IN (2) AND link_modul_ba = '' AND m_kapal.m_kapal_id = ".$_POST['m_kapal_id']." 
		    ORDER BY tanggal_surat DESC, jam_surat DESC LIMIT 1"; 
		
		// var_dump($sql);die();
		$query = $ci->db->query($sql);
		$total = $query->num_rows();
		$data = array();
		
		if($total>0){
		    
		    foreach ($query->result() as $list){
			
			$data['jml'] = 1;
		    $data['nomor_surat'] = $list->nomor_surat;
		    $data['volume_sebelum'] = number_format($list->volume_sisa);
		    
		    
		 }
		}else{
		    
		    $data['jml'] = 0;
		    $data['nomor_surat'] = '';
        	$data['volume_sebelum'] = 0;
        	
		    
		}
        	
        echo json_encode($data);
        
		
	    
	}
	
	function getDataUpload($trans_id){
		// var_dump($nomor_surat);die();
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
		
		if($list->status_temp == 1){
			$cek = 'readonly=""';
			$cek2 = 'disabled';
			
		}else{
			
			$cek = '';
			$cek2 = '';
		}
		
		$ed = '';
		
		$ed = "<script>
		
    		$('.datepicker').datepicker({
                format: 'dd-mm-yyyy'
            });
            
        </script>";
		
		$ed .='
			<div class="row">
				
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
						<textarea id="alamat1" name="alamat1" rows="3" cols="50" class="form-control" readonly="">'.$rx->alamat1.'</textarea>
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
			    
			    <div class="col-md-5 ">
					<div class="form-group">
						<label>LINK BA</label>
						    <input type="text" name="link_ba" id="link_ba" readonly="" class="form-control" value="'.$list->link_modul_ba.'">
					</div>
				</div>
				
				<div class="col-md-5 ">
					<div class="form-group">
						<label>NOMOR BA</label>
						<input type="text" name="nomor_surat" id="nomor_surat" readonly="" class="form-control" value="'.$list->nomor_surat.'" readonly="">
					</div>
				</div>	
			    
			</div>	
			
			<div class="row">
		
				<div class="col-md-6">
					<div class="form-group">
						<label>Kapal Penerima Hibah</label>
						<select class="form-control custom-control" id="m_kapal_id_temp" name="m_kapal_id_temp" onChange="getValKapalTemp(this, 1)" '.$cek2.'>';
							$ed .=' <option>--Pilih--</option>';
					
								$sql = "SELECT * FROM m_kapal WHERE m_kapal_id NOT IN (SELECT m_kapal_id FROM sys_user_kapal WHERE conf_user_id = '".$this->session->userdata('userid')."' ) "; 
								$query = $ci->db->query($sql);
								foreach ($query->result() as $r){
									
									$ed .=  '<option value="'.$r->m_kapal_id.'"'; if($list->kapal_code_temp == $r->code_kapal){$ed .='selected';} $ed .= '>'.$r->nama_kapal.'</option>';
								}
							
						$ed .=' </select>
					</div>
				</div>
				
				<div class="col-md-4 ">
					<div class="form-group">
						<label>KODE KAPAL Penerima Hibah</label>
						<input type="text" name="kapal_code_tempEd" id="kapal_code_tempEd" readonly="" class="form-control" value="'.$list->kapal_code_temp.'">
					</div>
				</div>
				
			</div>
			
			<div class="row">
				
				<div class="col-md-5">
					<div class="form-group">
						<label>Berdasarkan persetujuan</label>
						<select class="form-control custom-control" id="m_persetujuan_id" name="m_persetujuan_id">';
															
							$sql9 = "SELECT * FROM m_persetujuan "; 
							$query9 = $ci->db->query($sql9);
							foreach ($query9->result() as $list9){
								$ed .=  '<option value="'.$list9->id.'"'; if($list->m_persetujuan_id == $list9->id){$ed .='selected';} $ed .= '>'.$list9->deskripsi_persetujuan.'</option>';
								
							}

						$ed .= '</select>
					</div>
				</div>
				
				<div class="col-md-3 ">
					<div class="form-group">
						<label>NOMOR PERSETUJUAN</label>
						<input type="text" name="nomer_persetujuan" id="nomer_persetujuan" class="form-control" value="'.$list->nomer_persetujuan.'">
					</div>
				</div>	
				
				<div class="col-md-3 ">
					<label>TANGGAL PERSETUJUAN</label>
					<input type="text" name="tgl_persetujuan" id="tgl_persetujuan" class="form-control datepicker" value="'.date("d-m-Y", strtotime($list->tgl_persetujuan)).'">
				 </div>
				
				
			</div>
			
			<div class="row">
			    <div class="col-md-5">
					<label>Jenis BBM</label>
					<input type="text" placeholder="" id="keterangan_jenis_bbm" name="keterangan_jenis_bbm" class="form-control" value="'.$list->keterangan_jenis_bbm.'">
				 </div>
				
				<div class="col-md-4">
					<label>BBM Sebelum Pengisian</label>
					<input type="text" name="volume_sebelumEd" id="volume_sebelumEd" class="form-control angka" onkeypress="validate(event)" value="'.number_format($list->volume_sebelum).'" readonly="">
				 </div>
				 
				 <div class="col-md-5">
					<label>Jumlah BBM Di Hibahkan</label>
					<input type="text" name="volume_pemakaianEd" id="volume_pemakaianEd" class="form-control angka" onkeypress="validate(event)" onChange="validateVolume(1)" value="'.number_format($list->volume_pemakaian).'" '.$cek.'>
				 </div>
				 
				 <div class="col-md-4">
					<label>Sisa BBM</label>
					<input type="text" name="volume_sisaEd" id="volume_sisaEd" class="form-control angka" onkeypress="validate(event)" readonly="" value="'.number_format($list->volume_sisa).'" >
				 </div>
			</div>

			<div class="row">
			    
			   <div class="col-md-7 ">
					<div class="form-group">
						<label>Penyebab Hibah BBM</label>
						<textarea id="sebab_temp" name="sebab_temp" rows="3" cols="50" class="form-control">'.$list->sebab_temp.'</textarea>
					</div>
				</div>
				
			</div>';
		
		$ed .= '
			
			<div class="row">
			    
			    <div class="col-md-8">
					<label>PEJABAT/STAF UPT &nbsp;&nbsp;| &nbsp;&nbsp;<input type="checkbox" class="custom-control-input" id="an_staf" name="an_staf" value="1" '.$c_staf.' > An. </label>
					<input type="text" placeholder="" id="jabatan_staf_pangkalan" name="jabatan_staf_pangkalan" class="form-control" value="'.$list->jabatan_staf_pangkalan.'">
				 </div>
			 </div>
			 
			<div class="row">
			<br>
				<div class="col-md-4 ">
					<label>NAMA PEJABAT/STAF UPT</label>
					<input type="text" placeholder="" id="nama_petugas" name="nama_petugas" class="form-control" value="'.$list->nama_staf_pagkalan.'">
				 </div>
				 
				 <div class="col-md-4 ">
					<label>NIP </label>
						<input type="text" placeholder="" id="nip_petugas" name="nip_petugas" class="form-control" value="'.$list->nip_staf.'">
				 </div>
			 </div>
			 
			 <div class="row">
			 <br>
				 <div class="col-md-4 ">
					<label>NAMA NAKHODA &nbsp;&nbsp;| &nbsp;&nbsp;<input type="checkbox" class="custom-control-input" id="an_nakhoda" name="an_nakhoda" value="1" '.$c_nakhoda.'> An.  </label>
					<input type="text" placeholder="" id="nama_nakoda" name="nama_nakoda" class="form-control" value="'.$list->nama_nahkoda.'">
				 </div>
				 
				 <div class="col-md-4 ">
					<label>Pangkat/Gol </label>
						<input type="text" placeholder="" id="pangkat_nahkoda" name="pangkat_nahkoda" class="form-control" value="'.$list->pangkat_nahkoda.'">
				 </div>
				 
				 <div class="col-md-4 ">
					<label>NIP </label>
						<input type="text" placeholder="" id="nip_nakoda" name="nip_nakoda" class="form-control" value="'.$list->nip_nahkoda.'">
				 </div>
			 </div>
			 
			 <div class="row">
			 <br>
				 <div class="col-md-4 ">
					<label>NAMA KKM &nbsp;&nbsp;| &nbsp;&nbsp;<input type="checkbox" class="custom-control-input" id="an_kkm" name="an_kkm" value="1" '.$c_kkm.' > An. </label>
					<input type="text" placeholder="" id="nama_kkm" name="nama_kkm" class="form-control" value="'.$list->nama_kkm.'">
				 </div>
				 
				 <div class="col-md-4 ">
					<label>NIP </label>
						<input type="text" placeholder="" id="nip_kkm" name="nip_kkm" class="form-control" value="'.$list->nip_kkm.'">
				 </div>
			
			</div>
			
			<div class="row">
			<br>
				 <div class="col-md-4 ">
					<label>NAMA NAKHODA Penerima Hibah &nbsp;&nbsp;| &nbsp;&nbsp;<input type="checkbox" class="custom-control-input" id="an_nakhoda_temp" name="an_nakhoda_temp" value="1" '.$an_nakhoda_temp.' > An.  </label>
					<input type="text" placeholder="" id="nama_nahkoda_tempEd" name="nama_nahkoda_tempEd" class="form-control" value="'.$list->nama_nahkoda_temp.'">
				 </div>
				 
				 <div class="col-md-4 ">
					<label>Pangkat/Gol </label>
						<input type="text" placeholder="" id="pangkat_nahkoda_tempEd" name="pangkat_nahkoda_tempEd" class="form-control" value="'.$list->pangkat_nahkoda_temp.'">
				 </div>
				 
				 <div class="col-md-4 ">
					<label>NIP </label>
						<input type="text" placeholder="" id="nip_nahkoda_tempEd" name="nip_nahkoda_tempEd" class="form-control" value="'.$list->nip_nahkoda_temp.'">
				 </div>
			 </div>
			
			<div class="row">	
			<br>
				 <div class="col-md-4 ">
					<label>NAMA KKM Penerima Hibah&nbsp;&nbsp;| &nbsp;&nbsp;<input type="checkbox" class="custom-control-input" id="an_kkm_temp" name="an_kkm_temp" value="1" '.$an_kkm_temp.' > An. </label>
					<input type="text" placeholder="" id="nama_kkm_tempEd" name="nama_kkm_tempEd" class="form-control" value="'.$list->nama_kkm_temp.'">
				 </div>
				 
				 <div class="col-md-4 ">
					<label>NIP </label>
						<input type="text" placeholder="" id="nip_kkm_tempEd" name="nip_kkm_tempEd" class="form-control" value="'.$list->nip_kkm_temp.'">
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
		
		if($list->status_temp == 1){
			$cek = 'readonly=""';
			$cek2 = 'disabled';
			
		}else{
			
			$cek = '';
			$cek2 = '';
		}
		
		$ed = '';
		
		$ed = "<script>
		
    		$('.datepicker').datepicker({
                format: 'dd-mm-yyyy'
            });
            
        </script>";
		
		$ed .='
			<div class="row">
				
				<div class="col-md-6">
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
				
				<div class="col-md-4 ">
					<div class="form-group">
						<label>KODE KAPAL</label>
						<input type="text" name="code_kapal" id="code_kapal" readonly="" class="form-control" value="'.$list->kapal_code.'" readonly="">
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
						<textarea id="lokasi_surat" name="lokasi_surat" rows="3" cols="50" class="form-control" readonly="">'.$list->lokasi_surat.'</textarea>
					</div>
				</div>
				
				
			</div>	
			
			<div class="row">
				  <div class="col-md-3 ">
					<label>TANGGAL</label>
					<input type="text" name="tanggal_surat" id="tanggal_surat" class="form-control datepicker" value="'.date("d-m-Y", strtotime($list->tanggal_surat)).'" readonly="">
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
			    
			    <div class="col-md-5 ">
					<div class="form-group">
						<label>LINK BA</label>
						    <input type="text" name="link_ba" id="link_ba" readonly="" class="form-control" value="'.$list->link_modul_ba.'" readonly="">
					</div>
				</div>
				
				<div class="col-md-5 ">
					<div class="form-group">
						<label>NOMOR BA</label>
						<input type="text" name="nomor_surat" id="nomor_surat" readonly="" class="form-control" value="'.$list->nomor_surat.'" readonly="">
					</div>
				</div>	
			    
			</div>	
			
			<div class="row">
				<br>
				
				<div class="col-md-6">
					<div class="form-group">
						<label>Kapal Penerima Hibah</label>
						<select class="form-control custom-control" id="m_kapal_id_temp" name="m_kapal_id_temp" onChange="getValKapalTemp(this, 1)" disabled>';
							$ed .=' <option>--Pilih--</option>';
					
								$sql = "SELECT * FROM m_kapal WHERE m_kapal_id NOT IN (SELECT m_kapal_id FROM sys_user_kapal WHERE conf_user_id = '".$this->session->userdata('userid')."' ) "; 
								$query = $ci->db->query($sql);
								foreach ($query->result() as $r){
									
									$ed .=  '<option value="'.$r->m_kapal_id.'"'; if($list->kapal_code_temp == $r->code_kapal){$ed .='selected';} $ed .= '>'.$r->nama_kapal.'</option>';
								}
							
						$ed .=' </select>
					</div>
				</div>
				
				<div class="col-md-4 ">
					<div class="form-group">
						<label>KODE KAPAL Penerima Hibah</label>
						<input type="text" name="kapal_code_tempEd" id="kapal_code_tempEd" readonly="" class="form-control" value="'.$list->kapal_code_temp.'" readonly="">
					</div>
				</div>
				
				
			</div>
			
			<div class="row">
			    <div class="col-md-5">
					<label>Jenis BBM</label>
					<input type="text" placeholder="" id="keterangan_jenis_bbm" name="keterangan_jenis_bbm" class="form-control" value="'.$list->keterangan_jenis_bbm.'" readonly="">
				 </div>
				
				<div class="col-md-4">
					<label>BBM Sebelum Pengisian</label>
					<input type="text" name="volume_sebelumEd" id="volume_sebelumEd" class="form-control angka" onkeypress="validate(event)" value="'.number_format($list->volume_sebelum).'" readonly="">
				 </div>
				 
				 <div class="col-md-5">
					<label>Jumlah BBM Di Hibahkan</label>
					<input type="text" name="volume_pemakaianEd" id="volume_pemakaianEd" class="form-control angka" onkeypress="validate(event)" onChange="validateVolume(1)" value="'.number_format($list->volume_pemakaian).'" readonly="">
				 </div>
				 
				 <div class="col-md-4">
					<label>Sisa BBM</label>
					<input type="text" name="volume_sisaEd" id="volume_sisaEd" class="form-control angka"  readonly="" value="'.number_format($list->volume_sisa).'" >
				 </div>
			</div>

			<div class="row">
			    
			   <div class="col-md-7 ">
					<div class="form-group">
						<label>Penyebab Hibah BBM</label>
						<textarea id="sebab_temp" name="sebab_temp" rows="3" cols="50" class="form-control" disabled>'.$list->sebab_temp.'</textarea>
					</div>
				</div>
				
			</div>';
		
		$ed .= '
			
			<div class="row">
			    
			    <div class="col-md-8">
					<label>PEJABAT/STAF UPT &nbsp;&nbsp;| &nbsp;&nbsp;<input type="checkbox" class="custom-control-input" id="an_staf" name="an_staf" value="1" '.$c_staf.' > An. </label>
					<input type="text" placeholder="" id="jabatan_staf_pangkalan" name="jabatan_staf_pangkalan" class="form-control" value="'.$list->jabatan_staf_pangkalan.'" readonly="">
				 </div>
			 </div>
			 
			<div class="row">
			<br>
				<div class="col-md-4 ">
					<label>NAMA PEJABAT/STAF UPT</label>
					<input type="text" placeholder="" id="nama_petugas" name="nama_petugas" class="form-control" value="'.$list->nama_staf_pagkalan.'" readonly="">
				 </div>
				 
				 <div class="col-md-4 ">
					<label>NIP </label>
						<input type="text" placeholder="" id="nip_petugas" name="nip_petugas" class="form-control" value="'.$list->nip_staf.'" readonly="">
				 </div>
			 </div>
			 
			 <div class="row">
			 <br>
				 <div class="col-md-4 ">
					<label>NAMA NAKHODA &nbsp;&nbsp;| &nbsp;&nbsp;<input type="checkbox" class="custom-control-input" id="an_nakhoda" name="an_nakhoda" value="1" '.$c_nakhoda.'> An.  </label>
					<input type="text" placeholder="" id="nama_nakoda" name="nama_nakoda" class="form-control" value="'.$list->nama_nahkoda.'" readonly="">
				 </div>
				 
				 <div class="col-md-4 ">
					<label>Pangkat/Gol </label>
						<input type="text" placeholder="" id="pangkat_nahkoda" name="pangkat_nahkoda" class="form-control" value="'.$list->pangkat_nahkoda.'" readonly="">
				 </div>
				 
				 <div class="col-md-4 ">
					<label>NIP </label>
						<input type="text" placeholder="" id="nip_nakoda" name="nip_nakoda" class="form-control" value="'.$list->nip_nahkoda.'" readonly="">
				 </div>
			 </div>
			 
			 <div class="row">
			 <br>
				 <div class="col-md-4 ">
					<label>NAMA KKM &nbsp;&nbsp;| &nbsp;&nbsp;<input type="checkbox" class="custom-control-input" id="an_kkm" name="an_kkm" value="1" '.$c_kkm.' > An. </label>
					<input type="text" placeholder="" id="nama_kkm" name="nama_kkm" class="form-control" value="'.$list->nama_kkm.'" readonly="">
				 </div>
				 
				 <div class="col-md-4 ">
					<label>NIP </label>
						<input type="text" placeholder="" id="nip_kkm" name="nip_kkm" class="form-control" value="'.$list->nip_kkm.'" readonly="">
				 </div>
			
			</div>
			
			<div class="row">
			<br>
				 <div class="col-md-4 ">
					<label>NAMA NAKHODA Penerima Hibah &nbsp;&nbsp;| &nbsp;&nbsp;<input type="checkbox" class="custom-control-input" id="an_nakhoda_temp" name="an_nakhoda_temp" value="1" '.$an_nakhoda_temp.' > An.  </label>
					<input type="text" placeholder="" id="nama_nahkoda_tempEd" name="nama_nahkoda_tempEd" class="form-control" value="'.$list->nama_nahkoda_temp.'" readonly="">
				 </div>
				 
				 <div class="col-md-4 ">
					<label>Pangkat/Gol </label>
						<input type="text" placeholder="" id="pangkat_nahkoda_tempEd" name="pangkat_nahkoda_tempEd" class="form-control" value="'.$list->pangkat_nahkoda_temp.'" readonly="">
				 </div>
				 
				 <div class="col-md-4 ">
					<label>NIP </label>
						<input type="text" placeholder="" id="nip_nahkoda_tempEd" name="nip_nahkoda_tempEd" class="form-control" value="'.$list->nip_nahkoda_temp.'" readonly="">
				 </div>
			 </div>
			
			<div class="row">	
			<br>
				 <div class="col-md-4 ">
					<label>NAMA KKM Penerima Hibah&nbsp;&nbsp;| &nbsp;&nbsp;<input type="checkbox" class="custom-control-input" id="an_kkm_temp" name="an_kkm_temp" value="1" '.$an_kkm_temp.' > An. </label>
					<input type="text" placeholder="" id="nama_kkm_tempEd" name="nama_kkm_tempEd" class="form-control" value="'.$list->nama_kkm_temp.'" readonly="">
				 </div>
				 
				 <div class="col-md-4 ">
					<label>NIP </label>
						<input type="text" placeholder="" id="nip_kkm_tempEd" name="nip_kkm_tempEd" class="form-control" value="'.$list->nip_kkm_temp.'" readonly="">
				 </div>
			
			</div>';
		
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
