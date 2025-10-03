<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Role extends CI_Controller {
	
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
			$cari .= " AND pemilik LIKE '".$sSearch."'";
		}
		
		$sql = "SELECT *
				FROM conf_group ".$cari; 
		
		$query = $ci->db->query($sql." LIMIT ".$iDisplayLength." OFFSET ".$iDisplayStart." ");
		$query2 = $ci->db->query($sql);
		$total = $query2->num_rows();
		
		
		if($total > 0){	
			$i = $iDisplayStart;
			foreach ($query->result() as $list){
				$i++;
				$aaData[] = array( 
					
					$i,
					$list->group,
					'
					<button type="button" class="btn btn-warning mb-xl" onClick="getTab3('.$list->conf_group_id.')" title="EDIT" ><i class="fa fa-edit"></i></button> 
					<button type="button" class="btn btn-danger mb-xl" onClick="hapus('.$list->conf_group_id.')" title="HAPUS"><i class="fa fa-times"></i></button>',
					'
					<button type="button" class="btn btn-info mb-xl" onClick="getTab4('.$list->conf_group_id.')" title="SET MENU"><i class="fa fa-gear"></i></button>
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
		$sql = $ci->db->query("SELECT * FROM conf_group WHERE conf_group_id = '".$id."'"); 
		if($sql->num_rows > 0){
			$dt = $sql->row();
			$role = $dt->group;
		}else{
			$role = '';
		}
		$xa = '';
		$xa = '
			<div class="row">
				<div class="col-md-9 ">
					<div class="form-group">
						<label>Nama Group</label>
						<input type="text" placeholder="" id="role" name="role" class="form-control" value="'.$role.'" required>
						<input type="hidden" placeholder="" id="id" name="id" class="form-control" value="'.$id.'">
					</div>
				</div>
			</div>
			
		';
		
		
		echo $xa;
	}
	
	function setMenu($id){
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
			
			$sql = $ci->db->query("SELECT *, stm_menuv2.id AS id_menu FROM stm_menuv2 WHERE `level` != '1'  OR (`level` = '1' AND linka != '') ORDER BY menu ASC");
			$x = 0;
			foreach($sql->result() AS $list){
				$sql2 = $ci->db->query("SELECT * FROM conf_role_menu WHERE stm_menu_id = '".$list->id_menu."' AND conf_group_id = '".$id."'");
				if($sql->num_rows() > 0){
					$dt = $sql2->row_array();
					if($list->id_menu == isset($dt['stm_menu_id'])){
						$cek = 'checked';
					}else{
						$cek = '';
					}
				}
				$xa.='
				<div class="col-md-4">
					<div class="custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" id="id_stm_menuv2['.$x.']" name="id_stm_menuv2[]" value="'.$list->id_menu.'" '.$cek.'>
						<label class="custom-control-label" for="id_stm_menuv2['.$x.']">'.$list->menu.'</label>
					</div>
				</div>';
			$x++;
			}
			$xa.='
			<input type="hidden" placeholder="" id="conf_group_id" name="conf_group_id" class="form-control" value="'.$id.'">
			</div>';
		
		
		echo $xa;
	}
	
	
}
