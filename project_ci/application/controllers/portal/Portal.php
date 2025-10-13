<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Portal extends CI_Controller {
	
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
			$cari .= " AND (news_title LIKE '%".$sSearch."%' OR author LIKE '%".$sSearch."%')";
		}
		// $id = $this->session->userdata('userid');
		$sql = "SELECT *,substring(news,1,250) as news1, date_create AS date_creates
				FROM port_news WHERE 1=1 ".$cari." ORDER BY date_create DESC "; 
		
		$query = $ci->db->query($sql." LIMIT ".$iDisplayLength." OFFSET ".$iDisplayStart." ");
		$query2 = $ci->db->query($sql);
		$total = $query2->num_rows();
		
		
		if($total > 0){	
			$i = $iDisplayStart;
			foreach ($query->result() as $list){
				if($list->post == '0'){
					$sts = 'Tidak Terbit';
				}else{
					$sts = 'Terbit';
				}
				
				$btn = '<button type="button" class="btn btn-info" onClick="getTab3('.$list->id.')" title="UPLOAD" ><i class="fa fa-cloud-upload"></i></button>';
				$img = '<img src="'.base_url().'/images/'.$list->img.'" alt="" width="80px"><br><br>';
				$i++;
				$aaData[] = array( 
					
					$i,
					$img.$btn,
					$list->news_title,
					$list->news1,
					$list->author,
					$list->date_creates,
					$sts,
					'
					<button type="button" class="btn btn-warning mb-xl" onClick="getTab2('.$list->id.')" title="EDIT" ><i class="fa fa-edit"></i></button><br><br>
					<button type="button" class="btn btn-danger mb-xl" onClick="hapus('.$list->id.')" title="HAPUS"><i class="fa fa-times"></i></button>
					',
					
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
		$sql = $ci->db->query("SELECT * FROM port_news WHERE id = '".$id."'"); 
		
		if($sql->num_rows > 0){
			$dt = $sql->row();
			$news_title = $dt->news_title;
			$news = $dt->news;
			$author = $dt->author;
			$post = $dt->post;
		}else{
			$news_title		= '';
			$news			= '';
			$author			= '';
			$post			= '';
		}
		
		$xa = '';
		$xa .= "
			<script>
			$(document).ready(function(){
				// tinymce.EditorManager.execCommand('mceAddControl',true, 'news');
				tinymce.init({
					selector: '#news',
					height: 300,
					plugins: [
					  'advlist autolink lists charmap print preview hr anchor pagebreak spellchecker',
					  'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
					  'table emoticons template paste help'
					],
					toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | ' +
					  'bullist numlist outdent indent | link image | print preview media fullpage | ' +
					  'forecolor backcolor emoticons | help',
					menubar: 'favs file edit view insert format tools table help'
				});
			});
			</script>
		";
		$xa .= '
			<div class="row">
				<div class="col-md-9 ">
					<div class="form-group">
						<label>Judul Berita</label>
						<input type="text" placeholder="" id="news_title" name="news_title" class="form-control" value= "'.$news_title.'" required>
					</div>
				</div>
				
				<div class="col-md-9 ">
					<div class="form-group">
						<label>Isi Berita</label>
						<textarea class="form-control" id="news" name="news">'.$news.'</textarea>
					</div>
				</div>
				
				<div class="col-md-9 ">
					<div class="form-group">
						<label>Status</label>
						<select class="form-control custom-control" id="post" name="post"">
							';
							if($post == '0'){
								$sel1 = 'selected';
								$sel2 = '';
							}else if($post == 1){
								$sel1 = '';
								$sel2 = 'selected';
							}else{
								$sel1 = '';
								$sel2 = '';
							}
							$xa.='
							<option value="-">- PILIH -</option>
							<option value="1" '.$sel2.'>Terbitkan</option>
							<option value="0" '.$sel1.'>Sembunyikan</option>
						</select>
					</div>
				</div>
				<div class="col-md-9 ">
					<div class="form-group">
						<label>Kategori Berita</label>
						<select class="form-control custom-control" id="kategori_id" name="kategori_id"">
							';
							$sel1 = '';
							$sel2 = '';
							$sel3 = '';
							$sel4 = '';
							$sel5 = '';
							$sel6 = '';
							if($post == '1'){
								$sel1 = 'selected';
							}else if($post == '2'){
								$sel2 = '';
							}else if($post == '3'){
								$sel3 = 'selected';
							}else if($post == '4'){
								$sel4 = 'selected';
							}else if($post == '5'){
								$sel5 = 'selected';
							}else if($post == '6'){
								$sel6 = 'selected';
							}else{
								$sel1 = '';
								$sel2 = '';
								$sel3 = '';
								$sel4 = '';
								$sel5 = '';
								$sel6 = '';
							}
							$xa.='
							<option value="-">- PILIH -</option>
							<option value="1" '.$sel1.'>Pengumuman</option>
							<option value="2" '.$sel2.'>Agenda</option>
							<option value="3" '.$sel3.'>Artikel</option>
							<option value="4" '.$sel4.'>Pelayanan</option>
							<option value="5" '.$sel5.'>Berita penting</option>
							<option value="6" '.$sel6.'>HoAX & Penipuan</option>
						</select>
					</div>
				</div>
				<input type="hidden" placeholder="" id="id" name="id" class="form-control" value="'.$id.'">
			</div>
			
		';
		
		
		echo $xa;
	}
	
	function getDataUpload($id){
		$ca = '';
		$ca .= '
		<div class="row">
			 	 <div class="col-md-9 ">
					<div class="form-group">
						<label></label>
						<input type="file" name="images" class="form-control" />
					</div>
				</div>
				
				<input type="hidden" placeholder="" id="id" name="id" class="form-control" value="'.$id.'">
			</div>';
		
		echo $ca;
	
	}
}
