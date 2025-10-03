<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends CI_Controller {
	
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
			$cari .= " AND menu LIKE '%".$sSearch."%'";
		}
		
		$sql = "SELECT * FROM `stm_menuv2` WHERE 1=1 ".$cari." ORDER BY menu ASC "; 
		
		$query = $ci->db->query($sql." LIMIT ".$iDisplayLength." OFFSET ".$iDisplayStart." ");
		$query2 = $ci->db->query($sql);
		$total = $query2->num_rows();
		
		
		if($total > 0){	
			$i = $iDisplayStart;
			foreach ($query->result() as $list){
				$sql2 = $ci->db->query("SELECT menu FROM `stm_menuv2` WHERE id = '".$list->id_parentmenu."'");
				$jml = $sql2->num_rows();
				$dt = $sql2->row_array();
				// var_dump("SELECT menu FROM `stm_menuv2` WHERE id = '".$list->id_parentmenu."'");
				// var_dump($jml);
				$parental = '<font style="color:#8080808f;">-- No Parent --</font>';
				if($jml > 0){
					$parental = $dt['menu'];
				}
				$i++;
				$aaData[] = array(
					
					$i,
					$list->menu,
					$parental,
					$list->linka,
					$list->icon,
					$list->urutan,
					'<button type="button" class="btn btn-warning" onClick="getTab3('.$list->id.')" title="EDIT" ><i class="fa fa-edit"></i></button> 
					<button type="button" class="btn btn-danger" onClick="hapus('.$list->id.')" title="HAPUS" ><i class="fa fa-trash"></i></button>'
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
		
		$sql2 = $ci->db->query("SELECT * FROM `stm_menuv2` WHERE id = '".$id."'");
		if($sql2->num_rows() > 0){
			$dt = $sql2->row();
			$parent = $dt->id_parentmenu;
			$level = $dt->level;
			$menu = $dt->menu;
			$linka = $dt->linka;
			$icon = $dt->icon;
			$urutan = $dt->urutan;
		}else{
			$parent = '';
			$level = '';
			$menu = '';
			$linka = '';
			$icon = '';
			$urutan = '';
			
		}
		
		$xa = '
			<div class="row">
				<div class="col-md-9 ">
					<div class="form-group">
						<label>Parent Menu<br><font style="color: red;font-style: italic;font-size: 10px;">*) Jika membuat Menu Utama, maka pilihan Parent Menu harus dikosongkan.</font></label>
							<select class="form-control custom-control" id="id_parentmenu" name="id_parentmenu"">
								<option value="">-- Pilih --</option>';
						
								$sql = "SELECT * FROM `stm_menuv2` WHERE level = '1'"; 
								$query = $ci->db->query($sql);
								foreach ($query->result() as $list){
									if($list->id == $parent){
										$xa .= '<option value="'.$list->id.'" selected>'.$list->menu.'</option>';
									}else{
										$xa .= '<option value="'.$list->id.'">'.$list->menu.'</option>';
									}
								}
							
						$xa .= '</select>
					</div>
				</div>
				
				<div class="col-md-9 ">
					<div class="form-group">
						<label>Menu Level<br><font style="color: red;font-style: italic;font-size: 10px;">*) Jika yang dipilih Sub Menu, maka pilihan Parent Menu harus diisi.</font> </label>
							<select class="form-control custom-control" id="level" name="level"">';
							if($level == '1'){
								$sel1 = 'selected';
								$sel2 = '';
								$sel3 = '';
							}else if($level == '2'){
								$sel1 = '';
								$sel2 = 'selected';
								$sel3 = '';
							}else{
								$sel1 = '';
								$sel2 = '';
								$sel3 = 'selected';
							}
							$xa.='
								<option value="0" '.$sel3.'>-- Pilih --</option>
								<option value="1" '.$sel1.'>Menu Utama</option>
								<option value="2" '.$sel2.'>Sub Menu</option>';
							
						$xa .= '</select>
					</div>
				</div>
				
				<div class="col-md-9 ">
					<div class="form-group">
						<label>Menu Name</label>
						<input type="text" placeholder="" id="menu" name="menu" class="form-control" value="'.$menu.'" required>
					</div>
				</div>
				
				<div class="col-md-9 ">
					<div class="form-group">
						<label>Link<br><font style="color: red;font-style: italic;font-size: 10px;">*) Jika membuat Menu Utama dengan Sub Menu, maka inputan Link dikosongkan, jika membuat Menu Utama tanpa Sub Menu atau hanya membuat Sub Menu maka input Link harus diisi.</font></label>
						<input type="text" placeholder = "" id="linka" name="linka" class="form-control" value="'.$linka.'">
					</div>
				</div>
				
				<div class="col-md-9 ">
					<div class="form-group">
						<label>Icon<br><font style="color: red;font-style: italic;font-size: 10px;">*) Jika membuat Menu Utama maka inputan Icon harus diisi, jika Sub Menu Kosongkan.</font></label>
						<input type="text" placeholder = "fa fa icon" id="icon" name="icon" class="form-control" value="'.$icon.'">
					</div>
				</div>
				
				 <div class="col-md-9 ">
					<div class="form-group">
						<label>Urutan<br><font style="color: red;font-style: italic;font-size: 10px;">*) Urutan Menu, isi dengan angka.</font></label>
						<input type="text" placeholder="" id="urutan" name="urutan" class="form-control" value="'.$urutan.'">
						<input type="hidden" placeholder="" id="id" name="id" class="form-control" value="'.$id.'">
					</div>
				</div>
				
			</div>
			
		';
		
		
		echo $xa;
	
	}
	
}
