<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notif extends CI_Controller {
	
	public function index(){
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
			$cari .= " AND (subjek LIKE '%".$sSearch."%' OR pesan LIKE '%".$sSearch."%')";
		}
		$id = $this->session->userdata('userid');
		$sql = "SELECT *
				FROM dat_notif WHERE conf_user_id = '".$id."' ORDER BY status DESC ".$cari; 
		
		$query = $ci->db->query($sql." LIMIT ".$iDisplayLength." OFFSET ".$iDisplayStart." ");
		$query2 = $ci->db->query($sql);
		$total = $query2->num_rows();
		
		
		if($total > 0){	
			$i = $iDisplayStart;
			foreach ($query->result() as $list){
				if($list->status == '0'){
					$b1 = '<b>';
					$b2 = '</b>';
				}else{
					$b1 = '';
					$b2 = '';
				}
				$i++;
				$aaData[] = array( 
					
					$b1.$i.$b2,
					$b1.$list->tujuan.$b2,
					$b1.$list->subjek.$b2,
					$b1.$list->pesan.$b2,
					$b1.$list->dateins.$b2,
					'
					<button type="button" class="btn btn-default mb-xl" onClick="lihat('.$list->id.')" title="Lihat"><i class="fa fa-eye"></i></button>
					'
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
		$sql = $ci->db->query("SELECT * FROM dat_notif WHERE id = '".$id."'"); 
		if($sql->num_rows > 0){
			$dt = $sql->row();
			$conf_user_id = $dt->conf_user_id;
			$subjek = $dt->subjek;
			$pesan = $dt->pesan;
		}else{
			$conf_user_id = '';
			$subjek = '';
			$pesan = '';
		}
		$xa = '';
		$xa.= "
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
						<label>Kepada : </label>
						<input type="checkbox" id="check_all">
						<label class="custom-control-label" for="check_all">Pilih Semua</label>
					</div>
				</div>
			</div>
			<div class="row" style="margin-top: 10px;">';
					
			$sql = $ci->db->query("SELECT * FROM conf_user ORDER BY nama_lengkap ASC");
			$x = 0;
			foreach($sql->result() AS $list){
				if($list->conf_user_id == $conf_user_id){
					$cek = 'checked';
				}else{
					$cek = '';
				}
				$xa.='
				<div class="col-md-4">
					<div class="custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" id="id_user['.$x.']" name="id_user[]" value="'.$list->conf_user_id.'" '.$cek.'>
						<label class="custom-control-label" for="id_user['.$x.']">'.$list->nama_lengkap.'</label>
					</div>
				</div>';
			$x++;
			}
			$xa.='
			</div>
			<div class="row">
				<div class="col-md-9 ">
					<div class="form-group">
						<label>Subject</label>
						<input type="text" placeholder="" id="subjek" name="subjek" class="form-control" value="'.$subjek.'" required>
						<input type="hidden" placeholder="" id="id" name="id" class="form-control" value="'.$id.'">
					</div>
				</div>
				
				<div class="col-md-9 ">
					<div class="form-group">
						<label>Pesan</label>
						<input type="text" placeholder="" id="pesan" name="pesan" class="form-control" value="'.$pesan.'" required>
					</div>
				</div>
			</div>
			
		';
		
		
		echo $xa;
	}
	
	function shownotif($id){
		$ci = & get_instance();
		$read = $ci->db->query("UPDATE dat_notif SET `status` = '1' WHERE id = '".$id."'"); 
		$dtnotif = $ci->db->query("SELECT * FROM dat_notif WHERE id = '".$id."'")->row_array(); 
		$x = '
			<div class="row">
				<div class="col-md-12">
					<p style="text-indent: 50px;">'.$dtnotif['pesan'].'</p>
				</div>
			</div>
		';
		
		echo $x;
	}

	
	function getDataTablenotif(){
		$ci = & get_instance();
		$sSearch	=  $this->input->post('sSearch');
		$iDisplayStart = $this->input->post('iDisplayStart');
		$iDisplayLength = $this->input->post('iDisplayLength');
		
		$cari = "";
 
		if($sSearch != NULL){
			$sSearch = strtoupper($sSearch);
			$cari .= " AND (subjek LIKE '%".$sSearch."%' OR pesan LIKE '%".$sSearch."%')";
		}
		$sql = "SELECT *
				FROM dat_notif WHERE 1=1 ".$cari." ORDER BY dateins DESC "; 
		
		$query = $ci->db->query($sql." LIMIT ".$iDisplayLength." OFFSET ".$iDisplayStart." ");
		$query2 = $ci->db->query($sql);
		$total = $query2->num_rows();
		
		
		if($total > 0){	
			$i = $iDisplayStart;
			$pesn = '';
			foreach ($query->result() as $list){
				if($list->status == '0'){
					$pesn = 'Belum Dibaca';
				}else{
					$pesn = 'Sudah Dibaca';
				}
				$i++;
				$aaData[] = array( 
					
					$i,
					$list->tujuan,
					$list->subjek,
					$list->pesan,
					$list->dateins,
					$pesn
				);
					// ,
					// '
					// <button type="button" class="btn btn-default mb-xl" onClick="lihat('.$list->id.')" title="Lihat"><i class="fa fa-eye"></i></button>
					// '
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

}
