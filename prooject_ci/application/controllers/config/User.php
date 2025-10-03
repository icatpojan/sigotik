<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
	
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
			$cari .= " WHERE (username LIKE '%".$sSearch."%') OR  (nama_lengkap LIKE '%".$sSearch."%')";
		}
		
		$sql = "SELECT *,

				(SELECT group_concat(distinct a.nama_kapal SEPARATOR ', ') FROM m_kapal a WHERE a.m_kapal_id IN (
					SELECT c.m_kapal_id FROM sys_user_kapal c WHERE b.conf_user_id = c.conf_user_id		
				)) AS kapal
				FROM conf_user b
				LEFT JOIN conf_group ON conf_group.conf_group_id = b.conf_group_id
				LEFT JOIN m_upt ON m_upt.`code` = b.m_upt_code ".$cari; 
		
		$query = $ci->db->query($sql." LIMIT ".$iDisplayLength." OFFSET ".$iDisplayStart." ");
		$query2 = $ci->db->query($sql);
		$total = $query2->num_rows();
		
		
		if($total > 0){	
			$i = $iDisplayStart;
			foreach ($query->result() as $list){
				$i++;
				$aaData[] = array(
					
					$i,
					'<b>Username</b> : '.$list->username.'<br> <b>Nama Lengkap</b> : '.$list->nama_lengkap.'<br> <b>Email</b> : '.$list->email,
					'<b>Role</b> : '.$list->group.'<br><b>UPT</b> : '.$list->nama,
					'<b>Nip</b> : '.$list->nip.'<br> <b>Gol</b> : '.$list->golongan,
					$list->kapal,
					'<button type="button" class="btn btn-xs btn-warning btn-sm" onClick="getTab3('.$list->conf_user_id.')" title="EDIT" ><i class="fa fa-edit"></i></button> 
					<button type="button" class="btn btn-xs btn-danger btn-sm" onClick="hapus('.$list->conf_user_id.')" title="HAPUS" ><i class="fa fa-trash"></i></button>
					<button type="button" class="btn btn-xs btn-info" onClick="getTab4('.$list->conf_user_id.')" title="SET KAPAL" ><i class="fa fa-gear"></i></button>
					<button type="button" class="btn btn-xs btn-secondary" onClick="getResetPass('.$list->conf_user_id.')" title="RESET PASWORD" ><i class="fa fa-refresh"></i></button>'
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
	
	
	function getDataForm($id){ 
		$ci = & get_instance();
		
		$sql = $ci->db->query("SELECT * FROM `conf_user` WHERE conf_user_id = ".$id." ");
		if($sql->num_rows > 0){
			$r = $sql->row();
			
			$conf_user_id = $r->conf_user_id;
			$username = $r->username;
			$conf_group_id = $r->conf_group_id;
			$email = $r->email;
			$nama_lengkap = $r->nama_lengkap;
			$nip = $r->nip;
			$golongan = $r->golongan;
			$m_upt_code = $r->m_upt_code;
			
		}else{
			$username = '';
			$conf_user_id = '';
			$conf_group_id = '';
			$email = '';
			$nama_lengkap = '';
			$nip = '';
			$golongan = '';
			$m_upt_code = '';
		}
		
			
		
		$xa = '';
		$xa .= '
			<input type="hidden" placeholder="" id="conf_user_id" name="conf_user_id" class="form-control" value="'.$conf_user_id.'">
			<div class="row">
			
				 <div class="col-md-5 ">
					<div class="form-group">
						<label>Username</label>
						<input type="text" placeholder="" id="username" name="username" class="form-control" value= "'.$username.'" required>
					</div>
				</div>';
				
				
				if($id == 0){
				    $xa .= '<div class="col-md-4 ">
        					<div class="form-group">
        						<label>Password</label>
        						<input type="password" placeholder = "" id="password" name="password" class="form-control" value="" >
        					</div>
        				</div>';
			    }else{
			        
			        $xa .= '<div class="col-md-4 ">
        					<div class="form-group">
        						<label>Password</label>
        						<input type="password" placeholder = "" id="password" name="password" class="form-control" value="" readonly="">
        					</div>
        				</div>';
			        
			    }
				
			   
		$xa .= '<div class="col-md-5 ">
					<div class="form-group">
						<label>Role</label>
							<select class="form-control custom-control" id="conf_group_id" name="conf_group_id"">
								<option value="-">- PILIH -</option>';
						
								$sql = "SELECT * FROM conf_group"; 
								$query = $ci->db->query($sql);
								foreach ($query->result() as $list){
									$xa .= '<option value="'.$list->conf_group_id.'" '; if($conf_group_id == $list->conf_group_id){ $xa .= 'selected';} $xa .= '>'.$list->group.'</option>';
								}
							
						$xa .= '</select>
					</div>
				</div>
				
				<div class="col-md-4 ">
					<div class="form-group">
						<label>UPT</label>
							<select class="form-control custom-control" id="m_upt_code" name="m_upt_code">
								<option value="-">- PILIH -</option>';
								
								$sql = "SELECT * FROM m_upt "; 
								$query = $ci->db->query($sql);
								foreach ($query->result() as $list){
									$xa .= '<option value="'.$list->code.'" '; if($m_upt_code == $list->code){ $xa .= 'selected';} $xa .= '>'.$list->nama.'</option>';
								}
							
						$xa .= '</select>
					</div>
				</div>
				
				<div class="col-md-5 ">
					<div class="form-group">
						<label>Nama Lengkap</label>
						<input type="text" placeholder="" id="nama_lengkap" name="nama_lengkap" class="form-control" value= "'.$nama_lengkap.'" required>
					</div>
				</div>
				
				<div class="col-md-4 ">
					<div class="form-group">
						<label>Nip</label>
						<input type="text" placeholder="" id="nip" name="nip" class="form-control" value="'.$nip.'">
					</div>
				</div>
				
				 <div class="col-md-5 ">
					<div class="form-group">
						<label>Email</label>
						<input type="text" placeholder="" id="email" name="email" class="form-control" value= "'.$email.'" required>
					</div>
				</div>
				
				<div class="col-md-4 ">
					<div class="form-group">
						<label>Golongan</label>
						<input type="text" placeholder="" id="golongan" name="golongan" class="form-control" value="'.$golongan.'">
					</div>
				</div>
				
				
			</div> ';
		
		
		echo $xa;
	
	}
	
	function getDataCBox($id){
		$ci = & get_instance();
		$xa = '';
		$xa .= "
			<script>
			$(document).ready(function(){
				$('#check_all').on('click',function(){
					if(this.checked){
						$('.custom-control-input').each(function(){
							this.checked = true;
						});
					}else{
						 $('.custom-control-input').each(function(){
							this.checked = false;
						});
					}
				});
				
				$('.custom-control-input').on('click',function(){
					if($('.custom-control-input:checked').length == $('.custom-control-input').length){
						$('#check_all').prop('checked',true);
					}else{
						$('#check_all').prop('checked',false);
					}
				});
			});
			</script>
		";
		$xa .= '
			<div class="row">
				<div class="col-md-12">
					<div class="custom-control custom-checkbox" style="border-bottom: 1px dotted gray;">
						<input type="checkbox" id="check_all">
						<label class="custom-control-label" for="check_all">Check All</label>
					</div>
				</div>
			</div>
			<div class="row" style="margin-top: 10px;">
			';
			
			$r = $ci->db->query("SELECT a.m_upt_code FROM `conf_user` a
			JOIN m_upt b ON b.code = a.m_upt_code 
			WHERE conf_user_id = ".$id." ")->row();
			// var_dump($r);
			if($r->m_upt_code == '000'){ // untuk upt pusat
				$sql = $ci->db->query("SELECT * FROM m_kapal ");			
			}else{
			
			$sql = $ci->db->query("SELECT * FROM m_kapal WHERE m_upt_code = ".$r->m_upt_code." ");
			}
			
			$x = 0;
			foreach($sql->result() AS $list){
				$sql2 = $ci->db->query("SELECT * FROM sys_user_kapal WHERE m_kapal_id = '".$list->m_kapal_id."' AND conf_user_id = ".$id." ");
				if($sql->num_rows() > 0){
					$dt = $sql2->row_array();
					if($list->m_kapal_id == isset($dt['m_kapal_id'])){
						$cek = 'checked';
					}else{
						$cek = '';
					}
				}
				$xa.='
				<div class="col-md-4">
					<div class="custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" id="m_kapal_id['.$x.']" name="m_kapal_id[]" value="'.$list->m_kapal_id.'" '.$cek.'>
						<label class="custom-control-label" for="id_stm_menuv2['.$x.']">'.$list->nama_kapal.'</label>
					</div>
				</div>';
			$x++;
			}
			$xa.='
			<input type="hidden" placeholder="" id="conf_user_id" name="conf_user_id" class="form-control" value="'.$id.'">
			</div>';
		
		
		echo $xa;
	}
	
}
