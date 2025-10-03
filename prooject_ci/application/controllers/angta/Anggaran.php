<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Anggaran extends CI_Controller {
	
	public function index(){
		parent::__construct();
		
		$this->load->helper('form');
		
	}  
	
	
	#Entry Data
	public function getDataTable(){
		
		$ci = & get_instance();
		$sSearch	=  $this->input->post('sSearch');
		$iDisplayStart = $this->input->post('iDisplayStart');
		$iDisplayLength = $this->input->post('iDisplayLength');
		
		$cari = "";
 
		if($sSearch != NULL){
			$sSearch = strtoupper($sSearch);
			$cari .= " AND (user_input LIKE '%".$sSearch."%' OR periode LIKE '%".$sSearch."%')";
		}
		$id = $this->session->userdata('userid');
		$sql = "SELECT periode, perubahan_ke, SUM(anggaran) as total_anggaran, statusanggaran, user_input, keterangan, tanggal_input, user_app, tanggal_app FROM bbm_anggaran WHERE 1=1 AND perubahan_ke = '0' ".$cari." GROUP BY periode ORDER BY periode DESC "; 
		
		$query = $ci->db->query($sql." LIMIT ".$iDisplayLength." OFFSET ".$iDisplayStart." ");
		$query2 = $ci->db->query($sql);
		$total = $query2->num_rows();
		
		
		if($total > 0){	
			$i = $iDisplayStart;
			foreach ($query->result() as $list){
				$app = '';
				$btn1 = '';
				$btn2 = '';
				if($list->statusanggaran == '0'){
					$app = 'Belum Di Setujui';
					$btn1 .= '<button style="padding: 5px;font-size: 15px;height: 100%;" type="button" class="btn btn-warning mb-xl" onClick="getTab4('.$list->periode.','.$list->perubahan_ke.')" title="Edit"><i class="fa fa-pencil"></i></button>';
					$btn2 .= '<button style="padding: 5px;font-size: 15px;height: 100%;" type="button" class="btn btn-danger mb-xl" onClick="hapus('.$list->periode.','.$list->perubahan_ke.')" title="Hapus"><i class="fa fa-trash"></i></button>';
				}else{
					$app = 'Sudah Di Setujui';
					$btn1 .= '';
					$btn2 .= '';
				}
				
				$getname = $ci->db->query("SELECT * FROM conf_user WHERE username = '".$list->user_input."'")->row_array();
				if(isset($getname['nama_lengkap'])){
					$nama = $getname['nama_lengkap'];
				}else{
					$nama = '-';
				}
				$i++;
				$aaData[] = array( 
					
					$i,
					$list->periode,
					'Rp. '.number_format($list->total_anggaran,2,'.',','),
					$list->keterangan,
					$nama,
					$list->tanggal_input,
					$app,
					$list->user_app,
					$list->tanggal_app,
					'
					<button style="padding: 5px;font-size: 15px;height: 100%;" type="button" class="btn btn-default mb-xl" onClick="getTab3('.$list->periode.','.$list->perubahan_ke.')" title="Lihat"><i class="fa fa-eye"></i></button>
					'.$btn1.'
					'.$btn2.'
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
	
	function adddataPeriode(){
		$ci = & get_instance();
		$xs='';
		$xs.="
			<script>
				$(document).on('change', '.form-control', function(){
					var totalAng = 0;
					";
					$sql = $ci->db->query("SELECT * FROM m_upt");
					$no=1;
					$total_anggaran = 0;
					foreach($sql->result() as $list){
		$xs.="
					$('#upt_".$list->code."').each(function(){
						$(this).autoNumeric('init', {aSep: ',', aDec: '.', mDec: '0'});
						var anggarans = $(this).val();
						var anggaran = anggarans.replace(/,/g,'');
						if($.isNumeric(anggaran)){
							totalAng += parseFloat(anggaran);
						}
						// console.log(totalAng);
					});";
					}
		$xs.="
					
					totalAng = addCommas(totalAng);
					$('#total_anggaran').val('Rp. '+totalAng+',-');
				});
			</script>
		";
		$xs.='
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<label>PERIODE</label>
						<select class="form-control custom-control" id="periode" name="periode">
							<option value="0">- Pilih -</option>
						';
							$str = 2020;
							$now = date('Y');
							for($i=$str;$i<($now+5);$i++){
							$cek = $ci->db->query("SELECT periode FROM bbm_anggaran WHERE periode = '".$i."' GROUP BY periode")->num_rows();
								if($cek > 0){
									$xs.= '<option value="'.$i.'" disabled style="color:#808080a8;">'.$i.' - Data Sudah Diinput</option>';	
								}else{
									$xs.= '<option value="'.$i.'">'.$i.'</option>';	
								}
							}
			$xs.='
						</select>
					</div>
				</div>
			</div>
			<table id="data_table" class="table5 table5-striped table5-bordered table5-hover">
				<thead>
					<tr>
						<th width="4%">No</th>
						<th>Nama UPT</th>
						<th>Anggaran</th>
					</tr>
				</thead>
				<tbody>';
					$sql = $ci->db->query("SELECT * FROM m_upt");
					$no=1;
					$total_anggaran = 0;
					foreach($sql->result() as $list){
						$xs.='
							<tr>
								<td>'.$no.'</td>
								<td>'.$list->nama.'</td>
								<td>
									<input type="text" name="upt_'.$list->code.'" id="upt_'.$list->code.'" class="form-control" style="border:none;background-color:transparent;" value="" placeholder="Isi hanya angka" onKeyUp="numericFilter(this);" />
								</td>
							</tr>';
					$no++;
					}
				$xs.='
				</tbody>
			</table>
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<label>TOTAL ANGGARAN</label>
						<output class="form-control" id="total_anggaran" name="total_anggaran">Rp. 0,-</output>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-7 ">
					<div class="form-group">
						<label>KETERANGAN</label>
						<textarea class="form-control" id="keterangan" name="keterangan"></textarea>
					</div>
				</div>
			</div>';
		
		echo $xs;
	}
	
	function viewdataPeriode($tahun,$perubahan){
		$ci = & get_instance();
		$xs='';
		$xs.='
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<label>PERIODE</label>
						<select class="form-control custom-control" id="periode" name="periode">';
							$str = 2020;
							$now = date('Y');
							for($i=$str;$i<($now+5);$i++){
								if($tahun == $i){
									$xs.= '<option value="'.$i.'" selected>'.$i.'</option>';	
								}
							}
			$xs.='
						</select>
					</div>
				</div>
			</div>
			<table id="data_table" class="table5 table5-striped table5-bordered table5-hover">
				<thead>
					<tr>
						<th width="4%">No</th>
						<th>Nama UPT</th>
						<th>Anggaran</th>
					</tr>
				</thead>
				<tbody>';
					$sql = $ci->db->query("SELECT * FROM m_upt");
					$no=1;
					$total_anggaran = 0;
					foreach($sql->result() as $list){
						$dtangta = $ci->db->query("SELECT * FROM bbm_anggaran WHERE m_upt_code = '".$list->code."' AND periode = '".$tahun."' AND perubahan_ke = '".$perubahan."'");
						$jdt = $dtangta->num_rows();
						$anggaran = $dtangta->row_array();
						$read = 'readonly="readonly"';
						$nominal = $anggaran['anggaran'];
						$total_anggaran += $nominal;
						$keterangan = $anggaran['keterangan'];
						
						$xs.='
							<tr>
								<td>'.$no.'</td>
								<td>'.$list->nama.'</td>
								<td>
									<input type="text" name="upt_'.$list->code.'" id="upt_'.$list->code.'" class="form-control" style="border:none;background-color:transparent;" value="'.number_format($nominal,0,'.',',').'" placeholder="Isi hanya angka" '.$read.' />
								</td>
							</tr>';
					$no++;
					}
				$xs.='
				</tbody>
			</table>
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<label>TOTAL ANGGARAN</label>
						<output class="form-control" id="total_anggaran" name="total_anggaran">Rp. '.number_format($total_anggaran,0,'.',',').'</output>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-7 ">
					<div class="form-group">
						<label>KETERANGAN</label>
						<textarea class="form-control" id="keterangan" name="keterangan">'.$keterangan.'</textarea>
					</div>
				</div>
			</div>';
		
		echo $xs;
	}
	
	function editdataPeriode($tahun,$perubahan){
		$ci = & get_instance();
		$xs='';
		$type = "input[type=text]";
		
		$xs.="
		<script>
			$(document).ready(function() {
				$('".$type."').autoNumeric('init', {aSep: ',', aDec: '.', mDec: '0'});
			});
		</script>
		";
		$xs.="
			<script>
				$(document).on('change', '.form-control', function(){
					var totalAng = 0;
				";
					$sql = $ci->db->query("SELECT * FROM m_upt");
					$no=1;
					$total_anggaran = 0;
					foreach($sql->result() as $list){
		$xs.="
					$('#upt_".$list->code."').each(function(){
						$(this).autoNumeric('init', {aSep: ',', aDec: '.', mDec: '0'});
						var anggarans = $(this).val();
						var anggaran = anggarans.replace(/,/g,'');
						if($.isNumeric(anggaran)){
							totalAng += parseFloat(anggaran);
						}
						// console.log(totalAng);
					});";
					}
		$xs.="
					totalAng = addCommas(totalAng);
					$('#total_anggaran').val('Rp. '+totalAng+',-');
				});
			</script>
		";
		$xs.='
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<label>PERIODE</label>
						<select class="form-control custom-control" id="periode" name="periode">';
							$str = 2020;
							$now = date('Y');
							for($i=$str;$i<($now+5);$i++){
								if($tahun == $i){
									$xs.= '<option value="'.$i.'" selected>'.$i.'</option>';	
								}
							}
			$xs.='
						</select>
					</div>
				</div>
			</div>
			<table id="data_table" class="table5 table5-striped table5-bordered table5-hover">
				<thead>
					<tr>
						<th width="4%">No</th>
						<th>Nama UPT</th>
						<th>Anggaran</th>
					</tr>
				</thead>
				<tbody>';
					$sql = $ci->db->query("SELECT * FROM m_upt");
					$no=1;
					$total_anggaran = 0;
					foreach($sql->result() as $list){
						$dtangta = $ci->db->query("SELECT * FROM bbm_anggaran WHERE m_upt_code = '".$list->code."' AND periode = '".$tahun."' AND perubahan_ke = '".$perubahan."'");
						$jdt = $dtangta->num_rows();
						$anggaran = $dtangta->row_array();
						$nominal = $anggaran['anggaran'];
						$total_anggaran += $nominal;
						$keterangan = $anggaran['keterangan'];
						
						$xs.='
							<tr>
								<td>'.$no.'</td>
								<td>'.$list->nama.'</td>
								<td>
									<input type="text" name="upt_'.$list->code.'" id="upt_'.$list->code.'" class="form-control" style="border:none;background-color:transparent;" value="'.$nominal.'" placeholder="Isi hanya angka" onKeyUp="numericFilter(this);" />
								</td>
							</tr>';
					$no++;
					}
				$xs.='
				</tbody>
			</table>
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<label>TOTAL ANGGARAN</label>
						<output class="form-control" id="total_anggaran" name="total_anggaran">Rp. '.number_format($total_anggaran,0,'.',',').'</output>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-7 ">
					<div class="form-group">
						<label>KETERANGAN</label>
						<textarea class="form-control" id="keterangan" name="keterangan">'.$keterangan.'</textarea>
					</div>
				</div>
			</div>';
		
		echo $xs;
	}
	
	
	#Perubahan Anggaran
	public function getDataTablePerubahan(){
		
		$ci = & get_instance();
		$sSearch	=  $this->input->post('sSearch');
		$iDisplayStart = $this->input->post('iDisplayStart');
		$iDisplayLength = $this->input->post('iDisplayLength');
		
		$cari = "";
 
		if($sSearch != NULL){
			$sSearch = strtoupper($sSearch);
			$cari .= " AND (user_input LIKE '%".$sSearch."%' OR periode LIKE '%".$sSearch."%')";
		}
		$id = $this->session->userdata('userid');
		$sql = "SELECT periode, SUM(anggaran) as total_anggaran, perubahan_ke, statusanggaran, user_input, keterangan, tanggal_input, user_app, tanggal_app FROM bbm_anggaran WHERE 1=1 ".$cari." GROUP BY periode, perubahan_ke ORDER BY periode, perubahan_ke ASC  "; 
		
		$query = $ci->db->query($sql." LIMIT ".$iDisplayLength." OFFSET ".$iDisplayStart." ");
		$query2 = $ci->db->query($sql);
		$total = $query2->num_rows();
		
		
		if($total > 0){	
			$i = $iDisplayStart;
			foreach ($query->result() as $list){
				$app = '';
				if($list->statusanggaran == '0'){
					$app = 'Belum Di Setujui';
				}else{
					$app = 'Sudah Di Setujui';
				}
				
				$cek = $ci->db->query("SELECT * FROM bbm_anggaran WHERE periode = '".$list->periode."'  GROUP BY periode, perubahan_ke ORDER BY periode, perubahan_ke ASC ");
				$jm = $cek->num_rows();
				if($jm > 1){
					if($list->statusanggaran == '0'){
						$btn = '<button style="padding: 5px;font-size: 15px;height: 100%;" type="button" class="btn btn-sm btn-info" onClick="pending('.$list->periode.','.$list->perubahan_ke.')" title="Pengajuan Anggaran"><i class="fa fa-cloud-upload"></i></button>&nbsp;';
						$btn2 = '<button style="padding: 5px;font-size: 15px;height: 100%;" type="button" class="btn btn-sm btn-warning" onClick="getTab4('.$list->periode.','.$list->perubahan_ke.')" title="Edit Anggaran"><i class="fa fa-pencil"></i></button>&nbsp;';
					}else{
						$cek2 = $ci->db->query("SELECT MAX(perubahan_ke) AS max FROM bbm_anggaran WHERE periode = '".$list->periode."'  GROUP BY periode")->row_array();
						if($list->statusanggaran == '1' && $cek2['max'] == $list->perubahan_ke){
							$btn = '<button style="padding: 5px;font-size: 15px;height: 100%;" type="button" class="btn btn-sm btn-info" onClick="update('.$list->periode.','.$list->perubahan_ke.')" title="Pengajuan Anggaran"><i class="fa fa-cloud-upload"></i></button>&nbsp;';
							$btn2 = '';
						}else{
							$btn = '';
							$btn2 = '';
						}
					}
				}else{
					$btn = '<button style="padding: 5px;font-size: 15px;height: 100%;" type="button" class="btn btn-sm btn-info" onClick="update('.$list->periode.','.$list->perubahan_ke.')" title="Pengajuan Anggaran"><i class="fa fa-cloud-upload"></i></button>&nbsp;';
					$btn2 = '<button style="padding: 5px;font-size: 15px;height: 100%;" type="button" class="btn btn-sm btn-warning" onClick="getTab4('.$list->periode.','.$list->perubahan_ke.')" title="Edit Anggaran"><i class="fa fa-pencil"></i></button>&nbsp;';
				}
				
				$getname = $ci->db->query("SELECT * FROM conf_user WHERE username = '".$list->user_input."'")->row_array();
				if(isset($getname['nama_lengkap'])){
					$nama = $getname['nama_lengkap'];
				}else{
					$nama = '-';
				}
				
				$i++;
				$aaData[] = array( 
					
					$i,
					$list->periode.'<br><b>Perubahan Ke - '.$list->perubahan_ke.'</b>',
					'Rp. '.number_format($list->total_anggaran,2,'.',','),
					$list->keterangan,
					$nama,
					$list->tanggal_input,
					$app,
					// $list->user_app,
					// $list->tanggal_app,
					'
					'.$btn.' '.$btn2.'
					<button style="padding: 5px;font-size: 15px;height: 100%;" type="button" class="btn btn-sm btn-default" onClick="getTab3('.$list->periode.','.$list->perubahan_ke.')" title="Lihat"><i class="fa fa-eye"></i></button>&nbsp;
					'
					// <button style="padding: 5px;font-size: 15px;" type="button" class="btn btn-sm btn-danger" onClick="hapus('.$list->periode.')" title="Hapus"><i class="fa fa-trash"></i></button>
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
	
	function updatedataPeriode($tahun,$perubahan){
		$ci = & get_instance();
		$xs='';
		$type = "input[type=text]";
		$xs.="
		<script>
			$(document).ready(function() {
				$('".$type."').autoNumeric('init', {aSep: ',', aDec: '.', mDec: '0'});
			});
		</script>
		";
		
		$xs.="
			<script>
				$(document).on('change', '.form-control', function(){
					var totalAng = 0;
					";
					$sql = $ci->db->query("SELECT * FROM m_upt");
					$no=1;
					$total_anggaran = 0;
					foreach($sql->result() as $list){
		$xs.="
					$('#upt_".$list->code."').each(function(){
						$(this).autoNumeric('init', {aSep: ',', aDec: '.', mDec: '0'});
						var anggarans = $(this).val();
						var anggaran = anggarans.replace(/,/g,'');
						if($.isNumeric(anggaran)){
							totalAng += parseFloat(anggaran);
						}
						// console.log(totalAng);
					});";
					}
		$xs.="
					totalAng = addCommas(totalAng);
					$('#total_anggaran').val('Rp. '+totalAng+',-');
				});
			</script>
		";
		$xs.='
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<label>PERIODE</label>
						<select class="form-control custom-control" id="periode" name="periode">';
							$str = 2020;
							$now = date('Y');
							for($i=$str;$i<($now+5);$i++){
								if($tahun == $i){
									$xs.= '<option value="'.$i.'" selected>'.$i.'</option>';	
								}
							}
			$xs.='
						</select>
					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-3 ">
					<div class="form-group">
						<label>PERUBAHAN KE</label>';
							$getmx = $ci->db->query("SELECT max(perubahan_ke) AS max FROM bbm_anggaran WHERE periode = '".$tahun."'")->row_array();
							$nows = $getmx['max'];
		$xs.='					
						
						<output class="form-control" id="perubahan" name="perubahan">'.$nows.'</output>
						<input type="hidden" name="perubahan_ke" id="perubahan_ke" value="'.$nows.'">
					</div>
				</div>
			</div>
			
			<table id="data_table" class="table5 table5-striped table5-bordered table5-hover">
				<thead>
					<tr>
						<th width="4%">No</th>
						<th>Nama UPT</th>
						<th>Anggaran</th>
					</tr>
				</thead>
				<tbody>';
					$sql = $ci->db->query("SELECT * FROM m_upt");
					$no=1;
					$total_anggaran = 0;
					foreach($sql->result() as $list){
						$dtangta = $ci->db->query("SELECT * FROM bbm_anggaran WHERE m_upt_code = '".$list->code."' AND periode = '".$tahun."' AND perubahan_ke = '".$perubahan."'");
						$jdt = $dtangta->num_rows();
						$anggaran = $dtangta->row_array();
						$nominal = $anggaran['anggaran'];
						$total_anggaran += $nominal;
						$keterangan = $anggaran['keterangan'];
						
						$xs.='
							<tr>
								<td>'.$no.'</td>
								<td>'.$list->nama.'</td>
								<td>
									<input type="text" name="upt_'.$list->code.'" id="upt_'.$list->code.'" class="form-control" style="border:none;background-color:transparent;" value="'.$nominal.'" placeholder="Isi hanya angka" onKeyUp="numericFilter(this);" />
								</td>
							</tr>';
					$no++;
					}
				$xs.='
				</tbody>
			</table>
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<label>TOTAL ANGGARAN</label>
						<output class="form-control" id="total_anggaran" name="total_anggaran">Rp. '.number_format($total_anggaran,0,'.',',').'</output>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-7 ">
					<div class="form-group">
						<label>KETERANGAN</label>
						<textarea class="form-control" id="keterangan" name="keterangan">'.$keterangan.'</textarea>
					</div>
				</div>
			</div>';
		
		echo $xs;
	}
	
	function previewdataPeriode($tahun,$perubahan){
		$ci = & get_instance();
		$xs='';
		$xs.='
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<label>PERIODE</label>
						<select class="form-control custom-control" id="periode" name="periode">';
							$str = 2020;
							$now = date('Y');
							for($i=$str;$i<($now+5);$i++){
								if($tahun == $i){
									$xs.= '<option value="'.$i.'" selected>'.$i.'</option>';	
								}
							}
			$xs.='
						</select>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-3 ">
					<div class="form-group">
						<label>PERUBAHAN KE</label>';
							$getmx = $ci->db->query("SELECT max(perubahan_ke) AS max FROM bbm_anggaran WHERE periode = '".$tahun."' AND perubahan_ke = '".$perubahan."'")->row_array();
							$nows = $getmx['max'];
		$xs.='					
						
						<output class="form-control" id="perubahan" name="perubahan">'.$nows.'</output>
					</div>
				</div>
			</div>
			<table id="data_table" class="table5 table5-striped table5-bordered table5-hover">
				<thead>
					<tr>
						<th width="4%">No</th>
						<th>Nama UPT</th>
						<th>Anggaran</th>
					</tr>
				</thead>
				<tbody>';
					$sql = $ci->db->query("SELECT * FROM m_upt");
					$no=1;
					$total_anggaran = 0;
					foreach($sql->result() as $list){
						$dtangta = $ci->db->query("SELECT * FROM bbm_anggaran WHERE m_upt_code = '".$list->code."' AND periode = '".$tahun."' AND perubahan_ke = '".$perubahan."'");
						$jdt = $dtangta->num_rows();
						$anggaran = $dtangta->row_array();
						$read = 'readonly="readonly"';
						$nominal = $anggaran['anggaran'];
						$total_anggaran += $nominal;
						$keterangan = $anggaran['keterangan'];
						
						$xs.='
							<tr>
								<td>'.$no.'</td>
								<td>'.$list->nama.'</td>
								<td>
									<input type="text" name="upt_'.$list->code.'" id="upt_'.$list->code.'" class="form-control" style="border:none;background-color:transparent;" value="'.number_format($nominal,0,'.',',').'" placeholder="INPUT ANGKA NOMINAL ANGGARAN" '.$read.' />
								</td>
							</tr>';
					$no++;
					}
				$xs.='
				</tbody>
			</table>
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<label>TOTAL ANGGARAN</label>
						<output class="form-control" id="total_anggaran" name="total_anggaran">Rp. '.number_format($total_anggaran,0,'.',',').'</output>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-7 ">
					<div class="form-group">
						<label>KETERANGAN</label>
						<textarea class="form-control" id="keterangan" name="keterangan" readonly="readonly">'.$keterangan.'</textarea>
					</div>
				</div>
			</div>';
		
		echo $xs;
	}
	
	
	#Approval
	public function getDataTableApprov(){
		
		$ci = & get_instance();
		$sSearch	=  $this->input->post('sSearch');
		$iDisplayStart = $this->input->post('iDisplayStart');
		$iDisplayLength = $this->input->post('iDisplayLength');
		
		$cari = "";
 
		if($sSearch != NULL){
			$sSearch = strtoupper($sSearch);
			$cari .= " AND (user_input LIKE '%".$sSearch."%' OR periode LIKE '%".$sSearch."%')";
		}
		$id = $this->session->userdata('userid');
		$sql = "SELECT periode, SUM(anggaran) as total_anggaran, perubahan_ke, statusanggaran, user_input, keterangan, tanggal_input, user_app, tanggal_app FROM bbm_anggaran WHERE 1=1 AND statusanggaran IN ('0') ".$cari." GROUP BY periode ORDER BY periode DESC "; 
		
		$query = $ci->db->query($sql." LIMIT ".$iDisplayLength." OFFSET ".$iDisplayStart." ");
		$query2 = $ci->db->query($sql);
		$total = $query2->num_rows();
		
		
		if($total > 0){	
			$i = $iDisplayStart;
			foreach ($query->result() as $list){
				$app = '';
				if($list->statusanggaran == '0'){
					$app = 'Belum Di Setujui';
				}else{
					$app = 'Sudah Di Setujui';
				}
				
				$getname = $ci->db->query("SELECT * FROM conf_user WHERE username = '".$list->user_input."'")->row_array();
				if(isset($getname['nama_lengkap'])){
					$nama = $getname['nama_lengkap'];
				}else{
					$nama = '-';
				}
				
				$i++;
				$aaData[] = array( 
					
					$i,
					$list->periode.'<br><b>Perubahan Ke - '.$list->perubahan_ke.'</b>',
					'Rp. '.number_format($list->total_anggaran,2,'.',','),
					$list->keterangan,
					$nama,
					$list->tanggal_input,
					$app,
					// $list->user_app,
					// $list->tanggal_app,
					'
					<button style="padding: 5px;font-size: 15px;height: 100%;" type="button" class="btn btn-sm btn-default" onClick="getTab2('.$list->periode.','.$list->perubahan_ke.')" title="Lihat"><i class="fa fa-eye"></i></button>&nbsp;
					<button style="padding: 5px;font-size: 15px;height: 100%;" type="button" class="btn btn-sm btn-info" onClick="update('.$list->periode.')" title="Approv Anggaran"><i class="fa fa-gavel"></i></button>&nbsp;
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
	
	function viewdataPeriodeApp($tahun,$perubahan){
		$ci = & get_instance();
		$xs='';
		$xs.='
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<label>PERIODE</label>
						<select class="form-control custom-control" id="periode" name="periode">';
							$str = 2020;
							$now = date('Y');
							for($i=$str;$i<($now+5);$i++){
								if($tahun == $i){
									$xs.= '<option value="'.$i.'" selected>'.$i.'</option>';	
								}
							}
			$xs.='
						</select>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-3 ">
					<div class="form-group">
						<label>PERUBAHAN KE</label>';
							$getmx = $ci->db->query("SELECT perubahan_ke FROM bbm_anggaran WHERE periode = '".$tahun."' AND perubahan_ke = '".$perubahan."' ")->row_array();
							$nows = $getmx['perubahan_ke'];
		$xs.='					
						
						<output class="form-control" id="perubahan" name="perubahan">'.$nows.'</output>
					</div>
				</div>
			</div>
			<table id="data_table" class="table5 table5-striped table5-bordered table5-hover">
				<thead>
					<tr>
						<th width="4%">No</th>
						<th>Nama UPT</th>
						<th>Anggaran</th>
					</tr>
				</thead>
				<tbody>';
					$sql = $ci->db->query("SELECT * FROM m_upt");
					$no=1;
					$total_anggaran = 0;
					foreach($sql->result() as $list){
						$dtangta = $ci->db->query("SELECT * FROM bbm_anggaran WHERE m_upt_code = '".$list->code."' AND periode = '".$tahun."' AND perubahan_ke = '".$perubahan."'");
						$jdt = $dtangta->num_rows();
						$anggaran = $dtangta->row_array();
						$read = 'readonly="readonly"';
						$nominal = $anggaran['anggaran'];
						$total_anggaran += $nominal;
						$keterangan = $anggaran['keterangan'];
						
						$xs.='
							<tr>
								<td>'.$no.'</td>
								<td>'.$list->nama.'</td>
								<td>
									<input type="text" name="upt_'.$list->code.'" id="upt_'.$list->code.'" class="form-control" style="border:none;background-color:transparent;" value="'.number_format($nominal,0,'.',',').'" placeholder="INPUT ANGKA NOMINAL ANGGARAN" '.$read.' />
								</td>
							</tr>';
					$no++;
					}
				$xs.='
				</tbody>
			</table>
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<label>TOTAL ANGGARAN</label>
						<output class="form-control" id="total_anggaran" name="total_anggaran">Rp. '.number_format($total_anggaran,2,'.',',').'</output>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-7 ">
					<div class="form-group">
						<label>KETERANGAN</label>
						<textarea class="form-control" id="keterangan" name="keterangan" readonly="readonly">'.$keterangan.'</textarea>
					</div>
				</div>
			</div>';
		
		echo $xs;
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
	
	/// anggaran internal
	
	public function getDataTableAngaranInternal(){
		
		$ci = & get_instance();
		$sSearch	=  $this->input->post('sSearch');
		$iDisplayStart = $this->input->post('iDisplayStart');
		$iDisplayLength = $this->input->post('iDisplayLength');
		
		$cari = "";
 
		if($sSearch != NULL){
			$sSearch = strtoupper($sSearch);
			$cari .= " AND (m_upt_code LIKE '%".$sSearch."%' OR user_input LIKE '%".$sSearch."%')";
		}
		$code = $ci->session->userdata('m_upt_code');
		$kond = "";
		//if($code != '000'){
		 //   $kond = "AND m_upt_code = '".$code."'";
    	//	}
		$sql = "SELECT bbm_anggaran_upt.*, m_upt.nama FROM bbm_anggaran_upt 
		        JOIN m_upt ON m_upt.code = bbm_anggaran_upt.m_upt_code 
		        WHERE m_upt_code = '".$code."' ".$cari."  "; 
		
		$query = $ci->db->query($sql." LIMIT ".$iDisplayLength." OFFSET ".$iDisplayStart." ");
		$query2 = $ci->db->query($sql);
		$total = $query2->num_rows();
		
		
		if($total > 0){	
			$i = $iDisplayStart;
			foreach ($query->result() as $list){
			    
				if($list->statusperubahan == '0'){
					$app = 'Belum Di Setujui';
					$btn1 = '<button style="padding: 5px;font-size: 15px;height: 100%;" type="button" class="btn btn-warning mb-xl" onClick="getTab3('.$list->anggaran_upt_id.')" title="Edit"><i class="fa fa-pencil"></i></button>';
					$btn2 = '<button style="padding: 5px;font-size: 15px;height: 100%;" type="button" class="btn btn-danger mb-xl" onClick="hapus('.$list->anggaran_upt_id.')" title="Hapus"><i class="fa fa-trash"></i></button>';
				}else if($list->statusperubahan == '2'){
					$app = 'Pengajuan Dibatalkan';
					$btn1 = '<button style="padding: 5px;font-size: 15px;height: 100%;" type="button" class="btn btn-warning mb-xl" onClick="getTab3('.$list->anggaran_upt_id.')" title="Edit"><i class="fa fa-pencil"></i></button>';
					$btn2 = '<button style="padding: 5px;font-size: 15px;height: 100%;" type="button" class="btn btn-danger mb-xl" onClick="hapus('.$list->anggaran_upt_id.')" title="Hapus"><i class="fa fa-trash"></i></button>';
				}else{
					$app = 'Sudah Di Setujui';
					$btn1 = '';
					$btn2 = '';
				}
				
				$i++;
				$aaData[] = array( 	
					$i,
					$list->nama,
					$this->indo_date($list->tanggal_trans),
					number_format($list->nominal),
			    	$list->nomor_surat,
			    	$list->keterangan,
					$app,
				    $btn1.'
					'.$btn2
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
	
	function frmdataAnggaranInternal($id){
		$ci = & get_instance();
		$xa='';
		if($id == '0'){
			$tanggal_trans	= '';
			$nominal			= 0;
			$nomor_surat			= '';
			$keterangan			= '';
		}else{
			$dtnya = $ci->db->query("SELECT * FROM bbm_anggaran_upt WHERE anggaran_upt_id = '".$id."'")->row_array();
			$tanggal_trans	= date('d-m-Y', strtotime($dtnya['tanggal_trans']));
			$nominal			= number_format($dtnya['nominal']);
			$nomor_surat		= $dtnya['nomor_surat'];
			$keterangan			= $dtnya['keterangan'];
			$xa .= "
				<script>
					
					$(document).ready(function() {
	                    
	                   
                    });
					
				</script>
			";

		}
		// $xa='';
		$code_upt = $ci->session->userdata('m_upt_code');
		$getupt = $ci->db->query("SELECT * FROM m_upt WHERE code = '".$code_upt."'")->row_array();
		
		$xa.='
			<div class="row">
				<div class="col-md-10">
					<div class="form-group">
						<label>KODE UPT</label>
						<output id="kode_upt" name="kode_upt" class="form-control">'.$code_upt.'</output>
						<input type="hidden" placeholder="" id="real_kode_upt" name="real_kode_upt" class="form-control" value="'.$code_upt.'"/>
						<input type="hidden" placeholder="" id="id" name="id" class="form-control" value="'.$id.'"/>
					</div>
				</div>
				<div class="col-md-10">
					<div class="form-group">
						<label>NAMA UPT</label>
						<output placeholder="" id="nama_upt" name="nama_upt" class="form-control">'.$getupt['nama'].'</output>
					</div>
				</div>
				<div class="col-md-10">
					<div class="form-group">
						<label>ALAMAT UPT</label>
						<output placeholder="" id="alamat_upt" name="alamat_upt" class="form-control">'.(($getupt['alamat1'] != '') ? $getupt['alamat1'] : $getupt['alamat2'])          .'</output>
					</div>
				</div>
					<div class="col-md-8">
					<div class="form-group">
						<label>TANGGAL TRANSAKSI</label>
						<input type="text" placeholder="" id="tanggal_trans" name="tanggal_trans" class="form-control datepicker" onChange ="getValNomAwal()" value="'.$tanggal_trans.'">
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>ANGGARAN</label>
						<input type="text" placeholder="" id="anggaran" name="anggaran" readonly="" class="form-control" onkeyup="getValZ()">
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>PERUBAHAN UPT</label>
						<input type="text" placeholder="" id="nominal_awal" name="nominal_awal" readonly="" class="form-control" onkeyup="getValZ()">
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>NOMINAL PERUBAHAN</label>
						<input type="text" placeholder="" id="nominal_rubah" name="nominal_rubah" class="form-control angka"  value="'.$nominal.'" onkeyup="getValZ()" ">
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>SISA PAGU</label>
						<input type="text" placeholder="" id="sisa_pagu" name="sisa_pagu" class="form-control" readonly="" ">
					</div>
				</div>
				
				<div class="col-md-5">
					<div class="form-group">
						<label>NOMOR SURAT</label>
						<input type="text" placeholder="" id="nomor_surat" name="nomor_surat" class="form-control" value="'.$nomor_surat.'"/>
					</div>
				</div>
				
				<div class="col-md-5">
					<div class="form-group">
						<label>KETERANGAN</label>
						<input type="text" placeholder="" id="keterangan" name="keterangan" class="form-control" value="'.$keterangan.'"/>
					</div>
				</div>
				
		';
		
		$xa.='
		<script>
		
	        	$(document).ready(function() {
	                
                        getValAnggaran();
                    });
                    
    			$("#tanggal_trans").datepicker({
    				format: "dd-mm-yyyy"
    			});
			
			function getValAnggaran(){
			
			        var kode_upt = $("#real_kode_upt").val();
			        
                 	$.ajax({
                		type : "POST",
                		url	: "'.base_url().'index.php/angta/anggaran/getDataAnggaran",
                		data: {kode_upt:kode_upt},
                		dataType: "json",
                		success: function (data) {	
                			$("#anggaran").val(data.anggaran);
                		}
            	});
            	
			}
			
            function getValNomAwal(){
			
			        var tanggal_trans = $("#tanggal_trans").val();
			        var kode_upt = $("#real_kode_upt").val();
			        
			         var dataForm = {
                        "tanggal_trans" : tanggal_trans,
                        "kode_upt" : kode_upt
                    }
			        
                 	$.ajax({
                		type : "POST",
                		url	: "'.base_url().'index.php/angta/anggaran/getDataNominalAwal",
                		data: dataForm,
                		dataType: "json",
                		success: function (data) {
                		
                		    if(tanggal_trans == ""){
                		        $("#nominal_awal").val(0);
                		    }else{
                		        $("#nominal_awal").val(data.nominal);
                		    }
                		   
                		    setTimeout(function(){ 	document.getElementById("sisa_pagu").value = ReplaceNumberWithCommas(parseFloat(document.getElementById("anggaran").value.replace(/,/g ,""))+parseFloat(document.getElementById("nominal_awal").value.replace(/,/g ,"")) + parseFloat(document.getElementById("nominal_rubah").value.replace(/,/g ,"")))
                         }, 450);
                        
                			
                		}
            	    });
            }
		</script>
		';
		
		echo $xa;
	}
	
	function getDataAnggaran(){
	    
	    $ci = & get_instance();
	    
		$sql = "SELECT anggaran FROM `bbm_anggaran` WHERE m_upt_code = '".$_POST['kode_upt']."' AND periode = '".date('Y')."'
                        ORDER by perubahan_ke DESC LIMIT 1"; 
		
		$query = $ci->db->query($sql);
		$total = $query->num_rows();
		$data = array();
		
		if($total>0){
		    foreach ($query->result() as $list){
			    //$cek = $ci->db->query("SELECT * FROM bbm_anggaran_upt WHERE m_upt_code = '".$_POST['kode_upt']."' AND statusperubahan = '1' AND YEAR(tanggal_trans) = '".date('Y')."' ");
				//$jm = $cek->num_rows();
				//if($jm > 0){
				//	$anggaran_awal = $list->anggaran; 
                //	foreach($cek->result() as $lsta){
                //    	$anggaran_awal += $lsta->nominal;
                //    }
                //	$data['anggaran'] = number_format($anggaran_awal);
                //}else{
					$data['anggaran'] = number_format($list->anggaran);
                //}                             
            }
		 	
		}else{
		    $data['anggaran'] = 0;
		}
		
		echo json_encode($data);
	    
	}
	
	function getDataNominalAwal(){
	    
	   
	    $thn = date("Y");
	    $tgl_awal = $thn.'-01-01';
	    
	    
	    $ci = & get_instance();
	    
	    $tanggal_trans = date("y-m-d", strtotime($_POST['tanggal_trans']));
	    
		$sql = "SELECT COALESCE(SUM(nominal),0) AS nom FROM `bbm_anggaran_upt` WHERE m_upt_code = '".$_POST['kode_upt']."' AND tanggal_trans >= '".$tgl_awal."' AND tanggal_trans < '".$tanggal_trans."' "; 
		
		$query = $ci->db->query($sql);
		$total = $query->num_rows();
		$data = array();
		
		if($total>0){
		    
		    foreach ($query->result() as $list){
			    
			    $data['nominal'] = number_format($list->nom);
	       	 }
		    
		}else{
		    $data['nominal'] = 0;
		}
		
		echo json_encode($data);
	    
	}
	
	#Aproval Internal
	public function getDataTableAngaranInternalApp(){
		
		$ci = & get_instance();
		$sSearch	=  $this->input->post('sSearch');
		$iDisplayStart = $this->input->post('iDisplayStart');
		$iDisplayLength = $this->input->post('iDisplayLength');
		
		$cari = "";
 
		if($sSearch != NULL){
			$sSearch = strtoupper($sSearch);
			$cari .= " AND (m_upt_code LIKE '%".$sSearch."%' OR user_input LIKE '%".$sSearch."%')";
		}
		$code = $ci->session->userdata('m_upt_code');
		$kond = "";
		//if($code != '000'){
		 //   $kond = "AND m_upt_code = '".$code."'";
    	//	}
		$sql = "SELECT bbm_anggaran_upt.*, m_upt.nama FROM bbm_anggaran_upt 
		        JOIN m_upt ON m_upt.code = bbm_anggaran_upt.m_upt_code 
		        WHERE statusperubahan IN ('0','2') AND m_upt_code = '".$code."' ".$cari."  "; 
		
		$query = $ci->db->query($sql." LIMIT ".$iDisplayLength." OFFSET ".$iDisplayStart." ");
		$query2 = $ci->db->query($sql);
		$total = $query2->num_rows();
		
		
		if($total > 0){	
			$i = $iDisplayStart;
			foreach ($query->result() as $list){
			    
				if($list->statusperubahan == '0'){
					$app = 'Belum Di Setujui';
					$btn1 = '<button style="padding: 5px;font-size: 15px;height: 100%;" type="button" class="btn btn-secondary mb-xl" onClick="getTab2('.$list->anggaran_upt_id.')" title="Preview"><i class="fa fa-eye"></i></button>';
					$btn2 = '<button style="padding: 5px;font-size: 15px;height: 100%;" type="button" class="btn btn-info mb-xl" onClick="getTab3('.$list->anggaran_upt_id.')" title="Approve"><i class="fa fa-cloud-upload"></i></button>';
				}else if($list->statusperubahan == '2'){
					$app = 'Pengajuan Dibatalkan';
					$btn1 = '<button style="padding: 5px;font-size: 15px;height: 100%;" type="button" class="btn btn-secondary mb-xl" onClick="getTab2('.$list->anggaran_upt_id.')" title="Preview"><i class="fa fa-eye"></i></button>';
					$btn2 = '<button style="padding: 5px;font-size: 15px;height: 100%;" type="button" class="btn btn-info mb-xl" onClick="getTab3('.$list->anggaran_upt_id.')" title="Approve"><i class="fa fa-cloud-upload"></i></button>';
				}else{
					$app = 'Sudah Di Setujui';
					$btn1 = '';
					$btn2 = '';
				}
				
				$i++;
				$aaData[] = array( 	
					$i,
					$list->nama,
					$this->indo_date($list->tanggal_trans),
					number_format($list->nominal),
			    	$list->nomor_surat,
			    	$list->keterangan,
					$app,
				    $btn1.'
					'.$btn2
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
	
	function frmdataAnggaranInternalView($id){
		$ci = & get_instance();
		$xa='';
		$dtnya = $ci->db->query("SELECT * FROM bbm_anggaran_upt WHERE anggaran_upt_id = '".$id."'")->row_array();
		$tanggal_trans	= date('d-m-Y', strtotime($dtnya['tanggal_trans']));
    	$tahun = date('Y',strtotime($dtnya['tanggal_trans']));
		$nominal			= number_format($dtnya['nominal']);
		$nomor_surat		= $dtnya['nomor_surat'];
		$keterangan			= $dtnya['keterangan'];
		$xa .= "
			<script>
				
				$(document).ready(function() {
					
				   
				});
				
			</script>
		";
		$code_upt = $ci->session->userdata('m_upt_code');
		$getupt = $ci->db->query("SELECT * FROM m_upt WHERE code = '".$code_upt."'")->row_array();
		
		$xa.='
			<div class="row">
				<div class="col-md-10">
					<div class="form-group">
						<label>KODE UPT</label>
						<output id="kode_upt" name="kode_upt" class="form-control">'.$code_upt.'</output>
						<input type="hidden" placeholder="" id="real_kode_upt" name="real_kode_upt" class="form-control" value="'.$code_upt.'"/>
						<input type="hidden" placeholder="" id="id" name="id" class="form-control" value="'.$id.'"/>
					</div>
				</div>
				<div class="col-md-10">
					<div class="form-group">
						<label>NAMA UPT</label>
						<output placeholder="" id="nama_upt" name="nama_upt" class="form-control">'.$getupt['nama'].'</output>
					</div>
				</div>
				<div class="col-md-10">
					<div class="form-group">
						<label>ALAMAT UPT</label>
						<output placeholder="" id="alamat_upt" name="alamat_upt" class="form-control">'.(($getupt['alamat1'] != '') ? $getupt['alamat1'] : $getupt['alamat2'])          .'</output>
					</div>
				</div>
					<div class="col-md-8">
					<div class="form-group">
						<label>TANGGAL TRANSAKSI</label>
						<input type="text" placeholder="" id="tanggal_trans" name="tanggal_trans" class="form-control datepicker" onChange ="getValNomAwal()" value="'.$tanggal_trans.'" disabled>
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>ANGGARAN</label>
						<input type="text" placeholder="" id="anggaran" name="anggaran" readonly="" class="form-control" onkeyup="getValZ()">
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>PERUBAHAN UPT</label>
						<input type="text" placeholder="" id="nominal_awal" name="nominal_awal" readonly="" class="form-control" onkeyup="getValZ()">
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>NOMINAL PERUBAHAN</label>
						<input type="text" placeholder="" id="nominal_rubah" name="nominal_rubah" readonly class="form-control angka"  value="'.$nominal.'" onkeyup="getValZ()" ">
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>SISA PAGU</label>
						<input type="text" placeholder="" id="sisa_pagu" name="sisa_pagu" class="form-control" readonly="" ">
					</div>
				</div>
				
				<div class="col-md-5">
					<div class="form-group">
						<label>NOMOR SURAT</label>
						<input type="text" placeholder="" id="nomor_surat" name="nomor_surat" class="form-control" value="'.$nomor_surat.'" readonly/>
					</div>
				</div>
				
				<div class="col-md-5">
					<div class="form-group">
						<label>KETERANGAN</label>
						<input type="text" placeholder="" id="keterangan" name="keterangan" class="form-control" value="'.$keterangan.'" readonly/>
					</div>
				</div>
				
		';
		
		$xa.='
		<script>
		
	        	$(document).ready(function() {
	              	 	var id_angupt = '.$id.';
						var tahun = '.$tahun.';
                        getValAnggaran2(id_angupt,tahun);
                 });
                    
    			$("#tanggal_trans").datepicker({
    				format: "dd-mm-yyyy"
    			});
			
			function getValAnggaran2(id,tahun){
			
			        var kode_upt = $("#real_kode_upt").val();
			        
                 	$.ajax({
                		type : "POST",
                		url	: "'.base_url().'index.php/angta/anggaran/getDataAnggaran_2/"+id+"/"+tahun,
                		data: {kode_upt:kode_upt},
                		dataType: "json",
                		success: function (data) {	
                			$("#anggaran").val(data.anggaran);
                		}
            	});
            	
			}
			
            function getValNomAwal(){
			
			        var tanggal_trans = $("#tanggal_trans").val();
			        var kode_upt = $("#real_kode_upt").val();
			        
			         var dataForm = {
                        "tanggal_trans" : tanggal_trans,
                        "kode_upt" : kode_upt
                    }
			        
                 	$.ajax({
                		type : "POST",
                		url	: "'.base_url().'index.php/angta/anggaran/getDataNominalAwal",
                		data: dataForm,
                		dataType: "json",
                		success: function (data) {
                		
                		    if(tanggal_trans == ""){
                		        $("#nominal_awal").val(0);
                		    }else{
                		        $("#nominal_awal").val(data.nominal);
                		    }
                		   
                		    setTimeout(function(){ 	document.getElementById("sisa_pagu").value = ReplaceNumberWithCommas(parseFloat(document.getElementById("anggaran").value.replace(/,/g ,""))+parseFloat(document.getElementById("nominal_awal").value.replace(/,/g ,"")) + parseFloat(document.getElementById("nominal_rubah").value.replace(/,/g ,"")))
                         }, 450);
                        
                			
                		}
            	    });
            }
		</script>
		';
		
		echo $xa;
	}
	
	function getDataAnggaran_2($id,$tahun){
		// var_dump($id);die;
	    
	    $ci = & get_instance();
	    
		$sql = "SELECT anggaran FROM `bbm_anggaran` WHERE m_upt_code = '".$_POST['kode_upt']."' AND periode = '".$tahun."'
                        ORDER by perubahan_ke DESC LIMIT 1"; 
		
		$query = $ci->db->query($sql);
		$total = $query->num_rows();
		$data = array();
		
		if($total>0){
		    foreach ($query->result() as $list){
			    $data['anggaran'] = number_format($list->anggaran);
                                         
            }
		 	
		}else{
		    $data['anggaran'] = 0;
		}
		
		echo json_encode($data);
	    
	}
	
	public function getDataTableAngaranInternalBat(){
		
		$ci = & get_instance();
		$sSearch	=  $this->input->post('sSearch');
		$iDisplayStart = $this->input->post('iDisplayStart');
		$iDisplayLength = $this->input->post('iDisplayLength');
		
		$cari = "";
 
		if($sSearch != NULL){
			$sSearch = strtoupper($sSearch);
			$cari .= " AND (m_upt_code LIKE '%".$sSearch."%' OR user_input LIKE '%".$sSearch."%')";
		}
		$code = $ci->session->userdata('m_upt_code');
		$kond = "";
		//if($code != '000'){
		 //   $kond = "AND m_upt_code = '".$code."'";
    	//	}
		$sql = "SELECT bbm_anggaran_upt.*, m_upt.nama FROM bbm_anggaran_upt 
		        JOIN m_upt ON m_upt.code = bbm_anggaran_upt.m_upt_code 
		        WHERE statusperubahan = '1' AND m_upt_code = '".$code."' ".$cari."  "; 
		
		$query = $ci->db->query($sql." LIMIT ".$iDisplayLength." OFFSET ".$iDisplayStart." ");
		$query2 = $ci->db->query($sql);
		$total = $query2->num_rows();
		
		
		if($total > 0){	
			$i = $iDisplayStart;
			foreach ($query->result() as $list){
			    
				if($list->statusperubahan == '1'){
					$app = 'Sudah Di Setujui';
					$btn1 = '<button style="padding: 5px;font-size: 15px;height: 100%;" type="button" class="btn btn-secondary mb-xl" onClick="getTab2('.$list->anggaran_upt_id.')" title="Preview"><i class="fa fa-eye"></i></button>';
					$btn2 = '<button style="padding: 5px;font-size: 15px;height: 100%;" type="button" class="btn btn-danger mb-xl" onClick="getTab3('.$list->anggaran_upt_id.')" title="Batalkan"><i class="fa fa-close"></i></button>';
				}else{
					$app = 'Sudah Di Setujui';
					$btn1 = '';
					$btn2 = '';
				}
				
				$i++;
				$aaData[] = array( 	
					$i,
					$list->nama,
					$this->indo_date($list->tanggal_trans),
					number_format($list->nominal),
			    	$list->nomor_surat,
			    	$list->keterangan,
					$app,
				    $btn1.'
					'.$btn2
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
	
	
	
}
