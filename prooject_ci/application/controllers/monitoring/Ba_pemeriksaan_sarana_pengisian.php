<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ba_pemeriksaan_sarana_pengisian extends CI_Controller {
	
	
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
					FROM bbm_kapaltrans a WHERE status_ba = 4
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
	
	function getDataForm(){
	    
	    $xq = '
	            <script>
	                $(document).ready(function() {
	                
                        getVal();
                    });
                    
                    function getVal(){
	
            	var m_kapal_id = $("#m_kapal_id").val();
            	
            	$.ajax({
            		type : "POST",
            		url	: "'.base_url().'index.php/monitoring/ba_sisa_sblm_pengisian/getData",
            		data: {m_kapal_id:m_kapal_id},
            		dataType: "json",
            		success: function (data) {	
            			$("#code_kapal").val(data.code_kapal);
            			$("#lokasi_surat").val(data.kota);
            			$("#nama_petugas").val(data.nama_petugas);
            			$("#nip_petugas").val(data.nip_petugas);
            			$("#nama_nakoda").val(data.nama_nakoda);
            			$("#nip_nakoda").val(data.nip_nakoda);
            			$("#nama_kkm").val(data.nama_kkm);
            			$("#nip_kkm").val(data.nip_kkm);
            			$("#zona_waktu_surat").val(data.zona_waktu_upt);
            			$("#alamat1").val(data.alamat1);
            			$("#jabatan_staf_pangkalan").val(data.jabatan_petugas);
            		}
            	});
            }
            
            function getJenisTranport(sel){
    
                var val = sel.value; 
                if(val == 1){
                    $("#status_segel").removeAttr("disabled");
                    $("#gambar_segel").removeAttr("disabled");
                    
                    $("#status_flowmeter").attr("disabled", true);
                    $("#gambar_flowmeter").attr("disabled", true);
                }else if(val == 2){
                    $("#status_flowmeter").removeAttr("disabled");
                    $("#gambar_flowmeter").removeAttr("disabled");
                    
                    $("#status_segel").attr("disabled", true);
                    $("#gambar_segel").attr("disabled", true);
                }else if(val == 3){
                    
                    $("#status_flowmeter").removeAttr("disabled");
                    $("#gambar_flowmeter").removeAttr("disabled");
                    
                    $("#status_segel").attr("disabled", true);
                    $("#gambar_segel").attr("disabled", true);
                }else{
                   
                    $("#status_segel").attr("disabled", true);
                    $("#gambar_segel").attr("disabled", true);
                    
                    $("#status_flowmeter").attr("disabled", true);
                    $("#gambar_flowmeter").attr("disabled", true);
                    
                   
                }
    
            }
            
            
            
            $(function() {
               $(".datepicker").datepicker({
                    format: "dd-mm-yyyy"
                });
            });
            
             </script>
	    ';
	    $xq .= '<div class="row">
			
				 <div class="col-md-7">
					<div class="form-group">
						<label>Kapal</label>
						<select class="form-control custom-control" id="m_kapal_id" name="m_kapal_id" onChange="getVal()">';
						
								$ci = & get_instance();
								
								$sql = "SELECT * FROM m_kapal WHERE m_kapal_id IN (SELECT m_kapal_id FROM sys_user_kapal WHERE conf_user_id = '".$this->session->userdata('userid')."' ) "; 
								$query = $ci->db->query($sql);
								foreach ($query->result() as $list){
									 $xq .= '<option value="'.$list->m_kapal_id.'">'.$list->nama_kapal.'</option>';
								}
							
					 $xq .='</select>
					</div>
				</div>
				
				<div class="col-md-7 ">
					<div class="form-group">
						<label>KODE KAPAL</label>
						<input type="text" name="code_kapal" id="code_kapal" readonly="" class="form-control" value="">
					</div>
				</div>
				
				<div class="col-md-6 ">
					<div class="form-group">
						<label>LOKASI UPT</label>
						<textarea name="alamat1" id="alamat1" readonly="" rows="3" cols="50" class="form-control"/>
					</div>
				</div>
				
				<div class="col-md-4 ">
					<div class="form-group">
						<label>LOKASI KAPAL</label>
						<textarea id="lokasi_surat" name="lokasi_surat" rows="3" cols="50" class="form-control"/>
					</div>
				</div>
				
				<div class="col-md-7 ">
					<div class="form-group">
						<label>NOMOR BA</label>
						<input type="text" name="nomor_surat" id="nomor_surat" class="form-control" value="">
					</div>
				</div>	
				
			</div>	
			
			<div class="row">
				  <div class="col-md-3 ">
					<label>TANGGAL</label>
					<input type="text" name="tanggal_surat" id="tanggal_surat" class="form-control datepicker" value="">
				 </div>
				 
				 <div class="col-md-3 ">
					<label>JAM </label>
					<input type="time" name="jam_surat" id="jam_surat" class="form-control" >
				 </div>
				 
				<div class="col-md-3"> 
					<label>Zona Waktu Surat</label>
					<select class="form-control custom-control" id="zona_waktu_surat" name="zona_waktu_surat">
						<option value="WIB">WIB</option>
						<option value="WITA">WITA</option>
						<option value="WIT">WIT</option>
					</select>
				 </div>
			</div>
			
			<div class="row">
				<br>
				<div class="col-md-7 ">
					<div class="form-group">
						<label>PENYEDIA</label>
						<input type="text" name="penyedia" name="penyedia" class="form-control" value="">
					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-7">
					<label><b>RINCIAN PEMERIKSAAN</b></label>
				 </div>
				  
				 <div class="col-md-8">
					<label>JENIS TRANSPORTASI</label>
					<select class="form-control custom-control" id="jenis_tranport" name="jenis_tranport" onChange="getJenisTranport(this)">
						<option value="0">- PILIH -</option>
						<option value="1">MOBIL</option>
						<option value="2">KAPAL</option>
						<option value="3">PENGISIAN LANGSUNG</option>
					</select>
				 </div>
				 </br> </br></br> </br>
				 
				  <div class="col-md-6">
					<label>SEGEL TUTUP TANGKI DALAM KONDISI</label>
					<select class="form-control custom-control" id="status_segel" name="status_segel" readonly>
						<option value="1">BAIK</option>
						<option value="2">RUSAK</option>
					</select>
				 </div>
				 
				 <div class="col-md-5 ">
						<label>Gambar Kondisi Segel</label>
						<input type="file" name="gambar_segel" id="gambar_segel" class="form-control" disabled></input><span><font color="red">format gambar Segel .jpg .png</font></span>
				</div>

				 <div class="col-md-6">
					<label>FLOW METER DALAM KONDISI</label>
					 <select class="form-control custom-control" id="status_flowmeter" name="status_flowmeter" readonly>
						<option value="1">BAIK</option>
						<option value="2">RUSAK</option>
					</select>
				 </div>
				 
				 <div class="col-md-5 ">
						<label>Gambar Kondisi Flow Meter</label>
						<input type="file" name="gambar_flowmeter" id="gambar_flowmeter" class="form-control" disabled></input><span><font color="red">format gambar Flow Meter .jpg .png</font></span>
				</div>
			
			</div>
			
			<div class="row">
				<div class="col-md-7">
					<label><b>KESIMPULAN</b></label>
					<br>
				 </div>
				 
				 <div class="col-md-7">
					<label>PENGISIAN DAPAT</label>
					<select class="form-control custom-control" id="kesimpulan" name="kesimpulan">
						<option value="-">- PILIH -</option>
						<option value="1">DILAKUKAN</option>
						<option value="2">DITUNDA</option>
					</select>
				 </div>
				 <br>
			</div>
			
			<div class="row">
			    
			    <div class="col-md-8">
					<label>PEJABAT/STAF UPT &nbsp;&nbsp;| &nbsp;&nbsp;<input type="checkbox" class="custom-control-input" id="an_staf" name="an_staf" value="1" > An. </label>
					<input type="text" placeholder="" id="jabatan_staf_pangkalan" name="jabatan_staf_pangkalan" class="form-control" value="">
				 </div>
			</div>
			<div class="row">    
				<div class="col-md-5 ">
					<label>NAMA PEJABAT/STAF UPT </label>
					<input type="text" placeholder="" id="nama_petugas" name="nama_petugas" class="form-control" value="">
				 </div>
				 
				 <div class="col-md-4 ">
					<label>NIP </label>
						<input type="text" placeholder="" id="nip_petugas" name="nip_petugas" class="form-control" value="">
				</div>
			</div>
			<div class="row">	 
				 <div class="col-md-5 ">
					<label>NAMA NAKHODA &nbsp;&nbsp;| &nbsp;&nbsp;<input type="checkbox" class="custom-control-input" id="an_nakhoda" name="an_nakhoda" value="1" > An.  </label>
					<input type="text" placeholder="" id="nama_nakoda" name="nama_nakoda" class="form-control" value="">
				 </div>
				 
				 <div class="col-md-4 ">
					<label>NIP </label>
						<input type="text" placeholder="" id="nip_nakoda" name="nip_nakoda" class="form-control" value="">
				 </div>
			 </div>
			<div class="row">	 	 
				 <div class="col-md-5 ">
					<label>NAMA KKM &nbsp;&nbsp;| &nbsp;&nbsp;<input type="checkbox" class="custom-control-input" id="an_kkm" name="an_kkm" value="1" > An. </label>
					<input type="text" placeholder="" id="nama_kkm" name="nama_kkm" class="form-control" value="">
				 </div>
				 
				 <div class="col-md-4 ">
					<label>NIP </label>
						<input type="text" placeholder="" id="nip_kkm" name="nip_kkm" class="form-control" value="">
				 </div>
			
			</div>
			
			
			</div>	';
	    
	    
	    echo $xq;
	    
	    
	}
	
	function getDataEdit($trans_id){
		$ci = & get_instance();
		
		$sql = "SELECT * FROM `bbm_kapaltrans` WHERE trans_id = ".$trans_id." "; 
		$query = $ci->db->query($sql);
		foreach ($query->result() as $list){
			
		}
		
		
		if($list->jenis_tranport == 1){
		    
		    $segel = '';
		    $fSegel = '';
		    
		    $flow = 'disabled';
		    $fFlow = 'disabled';
		    
		}else if($list->jenis_tranport == 2){
		    
		    $segel = 'disabled';
		    $fSegel = 'disabled';
		    
		    $flow = '';
		    $fFlow = '';
		    
		}else if($list->jenis_tranport == 3){
		    
		    $segel = 'disabled';
		    $fSegel = 'disabled';
		    
		    $flow = '';
		    $fFlow = '';
		    
		}else{
		    
		    $segel = 'disabled';
		    $fSegel = 'disabled';
		    
		    $flow = 'disabled';
		    $fFlow = 'disabled';
		    
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
		
	//	var_dump($fFlow);die();
		$ed = '';
		
		$ed .= '<script>
		
		    function getJenisTranport(sel){
    
                var val = sel.value; 
                
                if(val == 1){
                    $("#status_segel").removeAttr("disabled");
                    $("#gambar_segel").removeAttr("disabled");
                    
                    $("#status_flowmeter").attr("disabled", true);
                    $("#gambar_flowmeter").attr("disabled", true);
                }else if(val == 2){
                    $("#status_flowmeter").removeAttr("disabled");
                    $("#gambar_flowmeter").removeAttr("disabled");
                    
                    $("#status_segel").attr("disabled", true);
                    $("#gambar_segel").attr("disabled", true);
                }else if(val == 3){
                    $("#status_flowmeter").removeAttr("disabled");
                    $("#gambar_flowmeter").removeAttr("disabled");
                    
                    $("#status_segel").attr("disabled", true);
                    $("#gambar_segel").attr("disabled", true);
                }else{
                    
                    $("#status_segel").attr("disabled", true);
                    $("#gambar_segel").attr("disabled", true);
                    
                    $("#status_flowmeter").attr("disabled", true);
                    $("#gambar_flowmeter").attr("disabled", true);
                    
                   
                }
    
            }
		
        	 $(function() {
               $(".datepicker").datepicker({
                    format: "dd-mm-yyyy"
                });
            });
            
        </script>';
        
		$ed .= '
			<div class="row">
			
				 <div class="col-md-7">
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
						<textarea id="lokasi_surat" name="lokasi_surat" rows="3" cols="50" class="form-control">'.$list->lokasi_surat.'</textarea>
					</div>
				</div>
				
				<div class="col-md-7 ">
					<div class="form-group">
						<label>NOMOR BA</label>
						<input type="text" name="nomor_surat" id="nomor_surat" readonly="" class="form-control" value="'.$list->nomor_surat.'">
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
					<label>Zona Waktu Wilayah UPT</label>
					<select class="form-control custom-control" id="zona_waktu_surat" name="zona_waktu_surat">
						<option value="WIB" '; if($list->zona_waktu_surat == "WIB"){$ed .='selected';} $ed .= '>WIB</option>
						<option value="WITA" '; if($list->zona_waktu_surat == "WITA"){$ed .='selected';} $ed .= '>WITA</option>
						<option value="WIT" '; if($list->zona_waktu_surat == "WIT"){$ed .='selected';} $ed .= '>WIT</option>
					</select>
				 </div>
			</div>
			
			<div class="row">
				<br>
				<div class="col-md-7 ">
					<div class="form-group">
						<label>PENYEDIA</label>
						<input type="text" name="penyedia" name="penyedia" class="form-control" value="'.$list->penyedia.'">
					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-7">
					<label><b>RINCIAN PEMERIKSAAN</b></label>
					<br>
				 </div>
				 
				 <div class="col-md-8">
					<label>JENIS TRANSPORTASI</label>
					<select class="form-control custom-control" id="jenis_tranport" name="jenis_tranport" onChange="getJenisTranport(this)">
						<option value="0" '; if($list->jenis_tranport == 0){$ed .='selected';} $ed .= '>- PILIH -</option>
						<option value="1" '; if($list->jenis_tranport == 1){$ed .='selected';} $ed .= '>MOBIL</option>
						<option value="2" '; if($list->jenis_tranport == 2){$ed .='selected';} $ed .= '>KAPAL</option>
						<option value="3" '; if($list->jenis_tranport == 3){$ed .='selected';} $ed .= '>PENGISIAN LANGSUNG</option>
					</select>
				 </div>
				 
				 <div class="col-md-6">
					<label>SEGEL TUTUP TANGKI DALAM KONDISI</label>
					<select class="form-control custom-control" id="status_segel" name="status_segel" '.$segel.'>
						<option value="1" '; if($list->status_segel == 1){$ed .='selected';} $ed .= '>BAIK</option>
						<option value="2" '; if($list->status_segel == 2){$ed .='selected';} $ed .= '>RUSAK</option>
					</select>
				 </div> 
				 
				 <div class="col-md-4 ">
						<label>Gambar Kondisi Segel</label>
						<input type="file" name="gambar_segel" id="gambar_segel" '.$fSegel.' class="form-control" ></input><span><font color="red" >format gambar Segel .jpg .png</font></span>
				</div>';
				
				if($list->gambar_segel != '' || $list->gambar_segel != null){
				    $ed .='<div class="col-md-2">
						<a href="'.base_url().'dokumen/gambar_ba_sarana/'.$list->gambar_segel.'" target="_blank" class="btn-xs btn-warning">Lihat</a>
				    </div>';
				}
				

				$ed .='<div class="col-md-6">
					<label>FLOW METER DALAM KONDISI</label>
					 <select class="form-control custom-control" id="status_flowmeter" name="status_flowmeter" '.$flow.'>
						<option value="1" '; if($list->status_flowmeter == 1){$ed .='selected';} $ed .= '>BAIK</option>
						<option value="2" '; if($list->status_flowmeter == 2){$ed .='selected';} $ed .= '>RUSAK</option>
					</select>
				 </div>
				 
				 <div class="col-md-4 ">
						<label>Gambar Kondisi Flow Meter</label>
						<input type="file" name="gambar_flowmeter" id="gambar_flowmeter" class="form-control" '.$fFlow.'/></input><span><font color="red">format gambar Flow Meter .jpg .png</font></span>
				</div>';
				
				if($list->gambar_flowmeter != '' || $list->gambar_flowmeter != null){
    				$ed .='<div class="col-md-2">
    						<a href="'.base_url().'dokumen/gambar_ba_sarana/'.$list->gambar_flowmeter.'" target="_blank" class="btn-xs btn-warning">Lihat</a>
    				</div>';
				}
				
	    	$ed .='	</div>
			
			<div class="row">
				<br>
				<div class="col-md-7">
					<label><b>KESIMPULAN</b></label>
					<br>
				 </div>
				 
				 <div class="col-md-7">
					<label>PENGISIAN DAPAT</label>
					<select class="form-control custom-control" id="kesimpulan" name="kesimpulan">
						<option value="1" '; if($list->kesimpulan == 1){$ed .='selected';} $ed .= '>DILAKUKAN</option>
						<option value="2" '; if($list->kesimpulan == 2){$ed .='selected';} $ed .= '>DITUNDA</option>
					</select>
				 </div>
				 <br>
			</div>
			<br>
			
			<div class="row">
			    
			    <div class="col-md-8">
					<label>PEJABAT/STAF UPT &nbsp;&nbsp;| &nbsp;&nbsp;<input type="checkbox" class="custom-control-input" id="an_staf" name="an_staf" value="1" '.$c_staf.' > An. </label>
					<input type="text" placeholder="" id="jabatan_staf_pangkalan" name="jabatan_staf_pangkalan" class="form-control" value="'.$list->jabatan_staf_pangkalan.'">
				 </div>
			 </div>
			<div class="row">
				<div class="col-md-5 ">
					<label>NAMA PEJABAT/STAF UPT</label>
					<input type="text" placeholder="" id="nama_petugas" name="nama_petugas" class="form-control" value="'.$list->nama_staf_pagkalan.'">
				 </div>
				 
				 <div class="col-md-4 ">
					<label>NIP </label>
						<input type="text" placeholder="" id="nip_petugas" name="nip_petugas" class="form-control" value="'.$list->nip_staf.'">
				 </div>
			 </div>
			 <div class="row">
				 <div class="col-md-5 ">
					<label>NAMA NAKHODA &nbsp;&nbsp;| &nbsp;&nbsp;<input type="checkbox" class="custom-control-input" id="an_nakhoda" name="an_nakhoda" value="1" '.$c_nakhoda.'> An.  </label>
					<input type="text" placeholder="" id="nama_nakoda" name="nama_nakoda" class="form-control" value="'.$list->nama_nahkoda.'">
				 </div>
				 
				 <div class="col-md-4 ">
					<label>NIP </label>
						<input type="text" placeholder="" id="nip_nakoda" name="nip_nakoda" class="form-control" value="'.$list->nip_nahkoda.'">
				 </div>
			 </div>
			 <div class="row">
				 <div class="col-md-5 ">
					<label>NAMA KKM &nbsp;&nbsp;| &nbsp;&nbsp;<input type="checkbox" class="custom-control-input" id="an_kkm" name="an_kkm" value="1" '.$c_kkm.' > An. </label>
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
		
		$ed = '';
		
		$ed .= '<script>
		
        	 $(function() {
               $(".datepicker").datepicker({
                    format: "dd-mm-yyyy"
                });
            });
            
        </script>';
        
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
						<textarea id="lokasi_surat" name="lokasi_surat" rows="3" cols="50" class="form-control" readonly="">'.$list->lokasi_surat.'</textarea>
					</div>
				</div>
				
				<div class="col-md-7 ">
					<div class="form-group">
						<label>NOMOR BA</label>
						<input type="text" name="nomor_surat" id="nomor_surat" readonly="" class="form-control" value="'.$list->nomor_surat.'">
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
					<label>Zona Waktu Wilayah UPT</label>
					<select class="form-control custom-control" id="zona_waktu_surat" name="zona_waktu_surat" disabled>
						<option value="WIB" '; if($list->zona_waktu_surat == "WIB"){$ed .='selected';} $ed .= '>WIB</option>
						<option value="WITA" '; if($list->zona_waktu_surat == "WITA"){$ed .='selected';} $ed .= '>WITA</option>
						<option value="WIT" '; if($list->zona_waktu_surat == "WIT"){$ed .='selected';} $ed .= '>WIT</option>
					</select>
				 </div>
			</div>
			
			<div class="row">
				<br>
				<div class="col-md-7 ">
					<div class="form-group">
						<label>PENYEDIA</label>
						<input type="text" name="penyedia" name="penyedia" class="form-control" value="'.$list->penyedia.'" readonly="">
					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-7">
					<label><b>RINCIAN PEMERIKSAAN</b></label>
					<br>
				 </div>
				 
				 <div class="col-md-8">
					<label>JENIS TRANSPORTASI</label>
					<select class="form-control custom-control" id="jenis_tranport" name="jenis_tranport" onChange="getJenisTranport(this)" disabled>
						<option value="0" '; if($list->jenis_tranport == 0){$ed .='selected';} $ed .= '>- PILIH -</option>
						<option value="1" '; if($list->jenis_tranport == 1){$ed .='selected';} $ed .= '>MOBIL</option>
						<option value="2" '; if($list->jenis_tranport == 2){$ed .='selected';} $ed .= '>KAPAL</option>
						<option value="2" '; if($list->jenis_tranport == 3){$ed .='selected';} $ed .= '>PENGISIAN LANGSUNG</option>
					</select>
				 </div>
				 
				 <div class="col-md-6">
					<label>SEGEL TUTUP TANGKI DALAM KONDISI</label>
					<select class="form-control custom-control" id="status_segel" name="status_segel" disabled>
						<option value="1" '; if($list->status_segel == 1){$ed .='selected';} $ed .= '>BAIK</option>
						<option value="2" '; if($list->status_segel == 2){$ed .='selected';} $ed .= '>RUSAK</option>
					</select>
				 </div> 
				 
				 <div class="col-md-4 ">
						<label>Gambar Kondisi Segel</label>
						<input type="file" name="gambar_segel" id="gambar_segel" disabled class="form-control" ></input><span><font color="red" >format gambar Segel .jpg .png</font></span>
				</div>';
				
				if($list->gambar_segel != '' || $list->gambar_segel != null){
				    $ed .='<div class="col-md-2">
						<a href="'.base_url().'dokumen/gambar_ba_sarana/'.$list->gambar_segel.'" target="_blank" class="btn-xs btn-warning">Lihat</a>
				    </div>';
				}
				

				$ed .='<div class="col-md-6">
					<label>FLOW METER DALAM KONDISI</label>
					 <select class="form-control custom-control" id="status_flowmeter" name="status_flowmeter" disabled>
						<option value="1" '; if($list->status_flowmeter == 1){$ed .='selected';} $ed .= '>BAIK</option>
						<option value="2" '; if($list->status_flowmeter == 2){$ed .='selected';} $ed .= '>RUSAK</option>
					</select>
				 </div>
				 
				 <div class="col-md-4 ">
						<label>Gambar Kondisi Flow Meter</label>
						<input type="file" name="gambar_flowmeter" id="gambar_flowmeter" disabled class="form-control" ></input><span><font color="red">format gambar Flow Meter .jpg .png</font></span>
				</div>';
				
				if($list->gambar_flowmeter != '' || $list->gambar_flowmeter != null){
    				$ed .='<div class="col-md-2">
    						<a href="'.base_url().'dokumen/gambar_ba_sarana/'.$list->gambar_flowmeter.'" target="_blank" class="btn-xs btn-warning">Lihat</a>
    				</div>';
				}
				
	    	$ed .='	</div>
			
			<div class="row">
				<br>
				<div class="col-md-7">
					<label><b>KESIMPULAN</b></label>
					<br>
				 </div>
				 
				 <div class="col-md-7">
					<label>PENGISIAN DAPAT</label>
					<select class="form-control custom-control" id="kesimpulan" name="kesimpulan" disabled>
						<option value="1" '; if($list->kesimpulan == 1){$ed .='selected';} $ed .= '>DILAKUKAN</option>
						<option value="2" '; if($list->kesimpulan == 2){$ed .='selected';} $ed .= '>DITUNDA</option>
					</select>
				 </div>
				 <br>
			</div>
			<br>
			
			<div class="row">
			    
			    <div class="col-md-8">
					<label>PEJABAT/STAF UPT &nbsp;&nbsp;| &nbsp;&nbsp;<input type="checkbox" class="custom-control-input" id="an_staf" name="an_staf" value="1" '.$c_staf.' disabled> An. </label>
					<input type="text" placeholder="" id="jabatan_staf_pangkalan" name="jabatan_staf_pangkalan" class="form-control" value="'.$list->jabatan_staf_pangkalan.'" readonly="">
				 </div>
			 </div>
			<div class="row">
				<div class="col-md-5 ">
					<label>NAMA PEJABAT/STAF UPT</label>
					<input type="text" placeholder="" id="nama_petugas" name="nama_petugas" class="form-control" value="'.$list->nama_staf_pagkalan.'" readonly="">
				 </div>
				 
				 <div class="col-md-4 ">
					<label>NIP </label>
						<input type="text" placeholder="" id="nip_petugas" name="nip_petugas" class="form-control" value="'.$list->nip_staf.'" readonly="">
				 </div>
			 </div>
			 <div class="row">
				 <div class="col-md-5 ">
					<label>NAMA NAKHODA &nbsp;&nbsp;| &nbsp;&nbsp;<input type="checkbox" class="custom-control-input" id="an_nakhoda" name="an_nakhoda" value="1" '.$c_nakhoda.' disabled> An.  </label>
					<input type="text" placeholder="" id="nama_nakoda" name="nama_nakoda" class="form-control" value="'.$list->nama_nahkoda.'" readonly="">
				 </div>
				 
				 <div class="col-md-4 ">
					<label>NIP </label>
						<input type="text" placeholder="" id="nip_nakoda" name="nip_nakoda" class="form-control" value="'.$list->nip_nahkoda.'" readonly="">
				 </div>
			 </div>
			 <div class="row">
				 <div class="col-md-5 ">
					<label>NAMA KKM &nbsp;&nbsp;| &nbsp;&nbsp;<input type="checkbox" class="custom-control-input" id="an_kkm" name="an_kkm" value="1" '.$c_kkm.' disabled> An. </label>
					<input type="text" placeholder="" id="nama_kkm" name="nama_kkm" class="form-control" value="'.$list->nama_kkm.'" readonly="">
				 </div>
				 
				 <div class="col-md-4 ">
					<label>NIP </label>
						<input type="text" placeholder="" id="nip_kkm" name="nip_kkm" class="form-control" value="'.$list->nip_kkm.'" readonly="">
				 </div>
			
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
