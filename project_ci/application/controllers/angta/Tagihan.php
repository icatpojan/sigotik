<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tagihan extends CI_Controller {
	
	public function index(){
		parent::__construct();
		
		$this->load->helper('form');
		
	}  
	
	
	#Entry Tagihan
	public function getDataTable(){
		
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
		if($code != '000'){
		    $kond = "AND m_upt_code = '".$code."'";
		}
		$sql = "SELECT * FROM bbm_tagihan 
		        JOIN m_upt ON m_upt.code = bbm_tagihan.m_upt_code
		        WHERE 1=1 ".$kond." ".$cari." ORDER BY tanggal_invoice DESC"; 
		
		$query = $ci->db->query($sql." LIMIT ".$iDisplayLength." OFFSET ".$iDisplayStart." ");
		$query2 = $ci->db->query($sql);
		$total = $query2->num_rows();
		
		
		if($total > 0){	
			$i = $iDisplayStart;
			foreach ($query->result() as $list){
				$app = '';
				
					if($list->statustagihan == '0'){
						$app = 'Entry Data';
						$btn = '<button style="padding: 5px;font-size: 15px;height: 100%;" type="button" class="btn btn-warning mb-xl" onClick="getTab3('.$list->tagihan_id.')" title="Edit"><i class="fa fa-pencil-square-o"></i></button>
								<button style="padding: 5px;font-size: 15px;height: 100%;" type="button" class="btn btn-danger mb-xl" onClick="hapus('.$list->tagihan_id.')" title="Hapus"><i class="fa fa-trash"></i></button>
						';
					}else if($list->statustagihan == '2'){
					    $app = 'Pengajuan Dibatalkan';
					    $btn = '<button style="padding: 5px;font-size: 15px;height: 100%;" type="button" class="btn btn-warning mb-xl" onClick="getTab3('.$list->tagihan_id.')" title="Edit"><i class="fa fa-pencil-square-o"></i></button>
								<button style="padding: 5px;font-size: 15px;height: 100%;" type="button" class="btn btn-danger mb-xl" onClick="hapus('.$list->tagihan_id.')" title="Hapus"><i class="fa fa-trash"></i></button>
						';
					}else{
						$app = 'Pengajuan Disetujui';
						$btn = '<font color="gray">- Disabled -</font>';
					}
				$i++;
				$aaData[] = array( 	
					$i,
					$list->m_upt_code,
					$list->nama,
					indo_date($list->tanggal_invoice),
					$list->no_tagihan,
					$list->penyedia,
					number_format($list->quantity,0,',','.'),
					number_format($list->total,0,'.',','),
					$app,
					$btn
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
	
	function frmdataTagihan($id){
		$ci = & get_instance();
		$no_tagihans 	= no_tagihan();
		$no_tagihan		= $no_tagihans;
		$xa='';
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
						<output placeholder="" id="alamat_upt" name="alamat_upt" class="form-control">'.(($getupt['alamat1'] != '') ? $getupt['alamat1'] : $getupt['alamat2']).'</output>
					</div>
				</div>
				<div class="col-md-10">
					<div class="form-group">
						<label>NOMOR SO<br><font color="red" size="1px">*) Jika Nomor SO > 1 pakai koma spasi(, ). Contoh : 4013285342, 40130443704, 4013157191, 4013202417, ...dst</font></label>
						<input type="text" placeholder="" id="no_so" name="no_so" class="form-control" value="">
					</div>
					<div class="form-group">
						<button type="button" class="btn-sm btn-info" onClick="caridata(1)">CARI DATA BY NO SO <i class="fa fa-search"></i></button>
					</div>
				</div>
			</div>
			<div id="fieldso_1"></div>
			<div class="row">
				<div class="col-md-5">
					<div class="form-group">
						<label>NOMOR TAGIHAN</label>
						<input type="text" placeholder="" id="no_tagihan" name="no_tagihan" class="form-control" value="'.$no_tagihan.'" readonly />
					</div>
				</div>
                <div class="col-md-5">
					<div class="form-group">
						<label>NOMOR SPT</label>
						<input type="text" placeholder="" id="no_spt" name="no_spt" class="form-control" value="" />
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>TAGIHAN KE</label>
						<input type="text" placeholder="" id="tagihanke" name="tagihanke" class="form-control" value="" />
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>TANGGAL TAGIHAN</label>
						<input type="text" placeholder="" id="tgl_invoice" name="tgl_invoice" class="form-control datepicker"  value="">
						
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>PENYEDIA</label>
						<input type="text" placeholder="" id="penyedia" name="penyedia" class="form-control" value="" />
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>QUANTITY</label>
						<input type="text" placeholder="" id="quantity" name="quantity" class="form-control" value="" data-an-default="" readonly />
						<input type="hidden" placeholder="" id="real_quantity" name="real_quantity" class="form-control" value="" />
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>HARGA TOTAL</label>
						<input type="text" placeholder="" id="harga" name="harga" class="form-control"  value="" data-an-default="" readonly />
						<input type="hidden" placeholder="" id="real_harga" name="real_harga" class="form-control" value="" />
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>HARGA PER LITER</label>
						<output placeholder="" id="hargaperliter" name="hargaperliter" class="form-control"></output>
						<input type="hidden" placeholder="" id="real_hargaperliter" name="real_hargaperliter" class="form-control" value="" />
					</div>
				</div>
				
				<div class="col-md-10 ">
					<div class="form-group">
						<label>UPLOAD FILE</label>
						<input type="file" name="images" class="form-control" />
					</div>
				</div>
			</div>
				
		';
		
		$xa.="
		<script>
			$('#tgl_invoice').datepicker({
				format: 'dd-mm-yyyy'
			});
		</script>
		";
		
		echo $xa;
	}
	
	function frmdataTagihanEdit($id){
		$ci = & get_instance();
		$xa='';
		$dtnya = $ci->db->query("SELECT * FROM bbm_tagihan WHERE tagihan_id = '".$id."'")->row_array();
		$tanggal_invoice	= date('d-m-yy', strtotime($dtnya['tanggal_invoice']));
		$tagihanke			= $dtnya['tagihanke'];
		$no_tagihan			= $dtnya['no_tagihan'];
    	$no_spt				= $dtnya['no_spt'];
		$penyedia			= $dtnya['penyedia'];
		$quantity			= $dtnya['quantity'];
		$harga				= $dtnya['total'];
		$hargaperliter		= $dtnya['hargaperliter'];
		$file				= $dtnya['file'];
		$xa .= "
			<script>
				$('#quantity1').autoNumeric('init', {aSep: ',', aDec: '.', mDec: '0'});
				$('#harga1').autoNumeric('init', {aSep: ',', aDec: '.', mDec: '0'});
				$('#hargaperliter1').autoNumeric('init', {aSep: ',', aDec: '.', mDec: '0'});
			</script>
		";
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
				<div class="col-md-10">
					<div class="form-group">
						<label>DETAIL SO</label>
					</div>
				</div>
			</div>
			<div id="fieldso_2" class="row">
			'.$this->getdtSOAda($id).'
			</div>
			<div class="row">
				<div class="col-md-5">
					<div class="form-group">
						<label>NOMOR TAGIHAN</label>
						<input type="text" placeholder="" id="no_tagihan" name="no_tagihan" class="form-control" value="'.$no_tagihan.'" readonly />
					</div>
				</div>
                <div class="col-md-5">
					<div class="form-group">
						<label>NOMOR SPT</label>
						<input type="text" placeholder="" id="no_spt" name="no_spt" class="form-control" value="'.$no_spt.'" />
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>TAGIHAN KE</label>
						<input type="text" placeholder="" id="tagihanke" name="tagihanke" class="form-control" value="'.$tagihanke.'" />
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>TANGGAL TAGIHAN</label>
						<input type="text" placeholder="" id="tgl_invoice" name="tgl_invoice" class="form-control datepicker"  value="'.$tanggal_invoice.'">
						
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>PENYEDIA</label>
						<input type="text" placeholder="" id="penyedia" name="penyedia" class="form-control" value="'.$penyedia.'" />
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>QUANTITY</label>
						<input type="text" placeholder="" id="quantity1" name="quantity1" class="form-control" value="'.$quantity.'" data-an-default="'.$quantity.'" readonly />
						<input type="hidden" placeholder="" id="real_quantity1" name="real_quantity1" class="form-control" value="'.$quantity.'" />
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>HARGA TOTAL</label>
						<input type="text" placeholder="" id="harga1" name="harga1" class="form-control"  value="'.$harga.'" data-an-default="'.$harga.'" readonly />
						<input type="hidden" placeholder="" id="real_harga1" name="real_harga1" class="form-control" value="'.$harga.'" />
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>HARGA PER LITER</label>
						<output placeholder="" id="hargaperliter1" name="hargaperliter1" class="form-control">'.$hargaperliter.'</output>
						<input type="hidden" placeholder="" id="real_hargaperliter1" name="real_hargaperliter1" class="form-control" value="'.$hargaperliter.'" />
					</div>
				</div>
				
				<div class="col-md-10 ">
					<div class="form-group">
						<label>UPLOAD FILE</label>
						';
						if($file != null){
							$ext = explode('.',$file);
							if(in_array($ext[1],array('jpg','png','jpeg'))){
								$xa.='<br><img src="'.base_url().'images/'.$file.'" width="80px"/> <br><br>';
							}else{
								$xa.='<br><a href="'.base_url().'images/'.$file.'" target="_blank">Lihat Dokumen</a> <br><br>';
							}
						}else{
							$xa.='';
						}
						$xa.='
						<input type="file" name="images" class="form-control" />
					</div>
				</div>
			</div>
				
		';
		
		$xa.="
		<script>
			$('#tgl_invoice').datepicker({
				format: 'dd-mm-yyyy'
			});
		</script>
		";
		
		echo $xa;
	}
	
	public function getdtSO($id){
		$no_so = str_replace("x","', '",$id);
		$ci = & get_instance();
		$sql = $ci->db->query("SELECT * FROM bbm_transdetail WHERE no_so IN ('".$no_so."') AND status_bayar = '0'");
		$jmdata = $sql->num_rows();
		$xa = '';
		$xa .= "
			<script>
			$(document).ready(function(){
				$('.x').autoNumeric('init', {aSep: ',', aDec: '.', mDec: '0'});
				$('.y').autoNumeric('init', {aSep: '.', aDec: ',', mDec: '0'});
				$('#check_alls').on('click',function(){
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
						$('#check_alls').prop('checked',true);
					}else{
						$('#check_alls').prop('checked',false);
					}
					
				});
				
				
			});
			
			$(document).on('change', '.form-control', function(){
				var totharga = 0;
				";
				foreach($sql->result() AS $listo){
		$xa.="
				$('#detail_harga_".$listo->bbm_transdetail_id."').each(function(){
						$(this).autoNumeric('init', {aSep: ',', aDec: '.', mDec: '0'});
						var hargas = $(this).val();
						var harga = hargas.replace(/,/g,'');
						if($.isNumeric(harga)){
							totharga += parseFloat(harga);
						}
						// console.log(totharga);
					});";
				}
		$xa.=		
				"
				$('#real_harga').val(totharga);
				totharga = addCommas(totharga);
				$('#harga').val(totharga);
				
				var kuantitis = $('#quantity').val();
				var kuantiti = kuantitis.replace(/,/g,'');
				var hargas = $('#harga').val();
				var harga = hargas.replace(/,/g,'');
				if($.isNumeric(harga)){
					hargaperliter = parseFloat(harga) / parseFloat(kuantiti);
				}
				$('#real_hargaperliter').val(hargaperliter);
				hargaperliter = addCommas(hargaperliter);
				$('#hargaperliter').val(hargaperliter);
			});
			
			
			</script>
		";
		$dbl = '';
		if($jmdata == 0){
			$dbl = 'disabled';
		}
		$xa.= '
			<div class="row">
				<div class="col-md-10">
					<div class="custom-control custom-checkbox" style="border-bottom: 1px dotted gray;">
						<input type="checkbox" id="check_alls" class="masterkliker" '.$dbl.'>
						<label class="custom-control-label" for="check_alls">CHECK ALL DATA SO</label>
					</div>
				</div>
			</div>
			<div class="row" style="margin-top: 10px;">
			<div class="col-md-10">
				<table id="data_tables" class="table2 table2-striped table2-bordered table2-hover" >
				<thead>
					<tr>
						<th></td>
						<th width="10%">Nomor Surat</th>
						<th>Transportasi</th>
						<th>Nomor SO</th>
						<th>Nomor DO</th>
						<th>Volume</th>
						<th>Harga</th>
						<th>No Invoice</th>
					</tr>
				<thead>
				<tbody>
		';
		
		if($jmdata > 0){
		$x = 0;
		foreach($sql->result() as $list){
			
		$xa.='
					<tr>
						<td>
							<input type="checkbox" class="custom-control-input" id="transdetaildt_['.$list->bbm_transdetail_id.']" name="transdetaildt_['.$list->bbm_transdetail_id.']" value="'.floatval($list->volume_isi).'">
							<input type="hidden" id="transdetaildt_id_[]" name="transdetaildt_id_[]" value="'.$list->bbm_transdetail_id.'">
						</td>
						<td><label class="custom-control-label" for="transdetaildt_['.$list->bbm_transdetail_id.']">'.$list->nomor_surat.'</label></td>
						<td><label class="custom-control-label" for="transdetaildt_['.$list->bbm_transdetail_id.']">'.$list->transportasi.'</label></td>
						<td><label class="custom-control-label" for="transdetaildt_['.$list->bbm_transdetail_id.']">'.$list->no_so.'</label></td>
						<td><label class="custom-control-label" for="transdetaildt_['.$list->bbm_transdetail_id.']">'.$list->no_do.'</label></td>
						<td><label class="custom-control-label y" for="transdetaildt_['.$list->bbm_transdetail_id.']">'.floatval($list->volume_isi).'</label></td>
						<td><input type="text" name="detail_harga_'.$list->bbm_transdetail_id.'" id="detail_harga_'.$list->bbm_transdetail_id.'" class="form-control x c'.$x.'" style="border:none;background-color:transparent;" value="" placeholder="Isi hanya angka" onKeyUp="numericFilter(this);" disabled/></td>
						<td><input type="text" name="no_invoice_'.$list->bbm_transdetail_id.'" id="no_invoice_'.$list->bbm_transdetail_id.'" class="form-control" style="border:none;background-color:transparent;" value="" /></td>
					</tr>
		';
		$x++;
		}
		
		}else{
		$xa.='
					<tr>
						<td colspan="8" style="text-align: center;">-- Data Tidak Ditemukan --</td>
					</tr>
		';
		}
		$xa.='
				</tbody>
				</table>
			</div>
			</div>
		';
		$xa.="
		<script>
			$(document).on('click', '.custom-control-input', function(){
				var voltot = 0;
				var cd = 0;
					$('.custom-control-input:checked').each(function(){
					    $('.c'+cd).attr('disabled',false);
						cd++;
						var vol = $(this).val();
						if($.isNumeric(vol)){
							voltot = parseFloat(voltot) + parseFloat(vol);
						}
					});
					quantity = addCommas(voltot);
					$('#quantity').val(quantity);
					$('#real_quantity').val(voltot);
					$('#harga').val(0);
					$('#real_harga').val('');
					$('#hargaperliter').val('');
					$('#real_hargaperliter').val(0);
			});
			
			$(document).on('click', '.masterkliker', function(){
				var voltot = 0;
				var cd = 0;
					$('.custom-control-input').each(function(){
					    $('.c'+cd).attr('disabled',false);
						cd++;
						var vol = $(this).val();
						if($.isNumeric(vol)){
							voltot = parseFloat(voltot) + parseFloat(vol);
						}
					});
					quantity = addCommas(voltot);
					$('#quantity').val(quantity);
					$('#real_quantity').val(voltot);
					$('#harga').val(0);
					$('#real_harga').val('');
					$('#hargaperliter').val('');
					$('#real_hargaperliter').val(0);
			});
		</script>
		";
		echo $xa;
	}
	
	public function getdtSOAda($id){
		$ci = & get_instance();
		$dtnya = $ci->db->query("SELECT * FROM bbm_tagihan WHERE tagihan_id = '".$id."'")->row_array();
		$sql = $ci->db->query("SELECT * FROM bbm_transdetail WHERE no_tagihan = '".$dtnya['no_tagihan']."' AND (no_tagihan !='' OR no_tagihan != null)");
		$jmdata = $sql->num_rows();
		$xa = '';
		$xa .= "
			<script>
			$('.x').autoNumeric('init', {aSep: ',', aDec: '.', mDec: '0'});
			$('.y').autoNumeric('init', {aSep: '.', aDec: ',', mDec: '0'});
			// $('.custom-control-input2').prop('checked',true);
			$(document).on('change', '.form-control', function(){
				var totharga = 0;
				";
				foreach($sql->result() AS $listo){
		$xa.="
				$('#detail_hargas_".$listo->bbm_transdetail_id."').each(function(){
						$(this).autoNumeric('init', {aSep: ',', aDec: '.', mDec: '0'});
						var hargas = $(this).val();
						var harga = hargas.replace(/,/g,'');
						if($.isNumeric(harga)){
							totharga += parseFloat(harga);
						}
						console.log(totharga);
					});";
				}
		$xa.=		
				"
				$('#real_harga1').val(totharga);
				totharga = addCommas(totharga);
				$('#harga1').val(totharga);
				
				var kuantitis1 = $('#quantity1').val();
				var kuantiti1 = kuantitis1.replace(/,/g,'');
				var hargas1 = $('#harga1').val();
				var harga1 = hargas1.replace(/,/g,'');
				var hargaperliter1 = 0;
				if($.isNumeric(harga1)){
					hargaperliter1 = parseFloat(harga1) / parseFloat(kuantiti1);
					console.log(harga1);
				}
				$('#real_hargaperliter1').val(hargaperliter1);
				hargaperliter1 = addCommas(hargaperliter1);
				$('#hargaperliter1').val(hargaperliter1);
			});
			
			
			</script>
		";
		$dbl = '';
		if($jmdata == 0){
			$dbl = 'disabled';
		}
		$xa.= '
			
			<div class="col-md-10">
				<table id="data_tables" class="table2 table2-striped table2-bordered table2-hover" >
				<thead>
					<tr>
						<th></td>
						<th width="10%">Nomor Surat</th>
						<th>Transportasi</th>
						<th>Nomor SO</th>
						<th>Nomor DO</th>
						<th>Volume</th>
						<th>Harga</th>
						<th>No Invoice</th>
					</tr>
				<thead>
				<tbody>
		';
		
		if($jmdata > 0){
		$x = 0;
		foreach($sql->result() as $list){
			
							//<input type="checkbox" class="custom-control-input2" id="transdetaildts_['.$list->bbm_transdetail_id.']" name="transdetaildts_['.$list->bbm_transdetail_id.']" value="'.floatval($list->volume_isi).'">
		$xa.='
					<tr>
						<td>
							<input type="hidden" id="transdetaildt_ids_[]" name="transdetaildt_ids_[]" value="'.$list->bbm_transdetail_id.'">
						</td>
						<td><label class="custom-control-label" for="transdetaildts_['.$list->bbm_transdetail_id.']">'.$list->nomor_surat.'</label></td>
						<td><label class="custom-control-label" for="transdetaildts_['.$list->bbm_transdetail_id.']">'.$list->transportasi.'</label></td>
						<td><label class="custom-control-label" for="transdetaildts_['.$list->bbm_transdetail_id.']">'.$list->no_so.'</label></td>
						<td><label class="custom-control-label" for="transdetaildts_['.$list->bbm_transdetail_id.']">'.$list->no_do.'</label></td>
						<td><label class="custom-control-label y" for="transdetaildts_['.$list->bbm_transdetail_id.']">'.floatval($list->volume_isi).'</label></td>
						<td><input type="text" name="detail_hargas_'.$list->bbm_transdetail_id.'" id="detail_hargas_'.$list->bbm_transdetail_id.'" class="form-control x" style="border:none;background-color:transparent;" value="'.$list->harga_total.'" placeholder="Isi hanya angka" onKeyUp="numericFilter(this);" /></td>
						<td><input type="text" name="no_invoices_'.$list->bbm_transdetail_id.'" id="no_invoices_'.$list->bbm_transdetail_id.'" class="form-control" style="border:none;background-color:transparent;" value="'.$list->no_invoice.'" /></td>
					</tr>
		';
		$x++;
		}
		
		}else{
		$xa.='
					<tr>
						<td colspan="8" style="text-align: center;">-- Data Tidak Ditemukan --</td>
					</tr>
		';
		}
		$xa.='
				</tbody>
				</table>
			</div>
		';
		$xa.="
		<script>
			$(document).on('click', '.custom-control-input2', function(){
				var voltot = 0;
					$('.custom-control-input2:checked').each(function(){
						var vol = $(this).val();
						if($.isNumeric(vol)){
							voltot = parseFloat(voltot) + parseFloat(vol);
						}
					});
					quantity = addCommas(voltot);
					$('#quantity1').val(quantity);
					$('#real_quantity1').val(voltot);
					// $('#harga1').val(0);
					// $('#real_harga1').val('');
					// $('#hargaperliter1').val('');
					// $('#real_hargaperliter1').val(0);
			});
		</script>
		";
		return $xa;
	}
	
	
	
	#Approval Tagihan
	public function getDataTableApprov(){
		
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
		//$kond = "";
		//if($code != '000'){
		//    $kond = "AND m_upt_code = '".$code."'";
		//}
		//$sql = "SELECT * FROM bbm_tagihan WHERE statustagihan = '0' ".$kond." ".$cari." ORDER BY tanggal_input DESC ";
		$sql = "SELECT * FROM bbm_tagihan WHERE statustagihan IN ('0','2') AND m_upt_code = '".$code."' ".$cari." ORDER BY tanggal_invoice DESC "; 
		
		$query = $ci->db->query($sql." LIMIT ".$iDisplayLength." OFFSET ".$iDisplayStart." ");
		$query2 = $ci->db->query($sql);
		$total = $query2->num_rows();
		
		
		if($total > 0){	
			$i = $iDisplayStart;
			foreach ($query->result() as $list){
				
				$i++;
				$aaData[] = array( 	
					$i,
					$list->m_upt_code,
					indo_date($list->tanggal_invoice),
					$list->no_tagihan,
					$list->penyedia,
					number_format($list->quantity,0,',','.'),
					'Rp. '.number_format($list->total,0,'.',','),
					$list->user_input.'<br>'.$list->tanggal_input,
					'
					<button style="padding: 5px;font-size: 15px;height: 100%;" type="button" class="btn btn-second mb-xl" onClick="getTab2('.$list->tagihan_id.')" title="Preview"><i class="fa fa-eye"></i></button>
					<button style="padding: 5px;font-size: 15px;height: 100%;" type="button" class="btn btn-info mb-xl" onClick="approve('.$list->tagihan_id.')" title="Approve"><i class="fa fa-gavel"></i></button>
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
	
	function vwdataTagihan($id){
		$ci = & get_instance();
		$xa='';
		$dtnya = $ci->db->query("SELECT * FROM bbm_tagihan WHERE tagihan_id = '".$id."'")->row_array();
		$tanggal_invoice	= indo_date($dtnya['tanggal_invoice']);
		$no_invoice			= $dtnya['no_tagihan'];
		$penyedia			= $dtnya['penyedia'];
		$quantity			= $dtnya['quantity'];
		$harga				= $dtnya['total'];
		$hargaperliter		= $dtnya['hargaperliter'];
		
		$xa .= "
			<script>
				$('#quantity').autoNumeric('init', {aSep: '.', aDec: ',', mDec: '0'});
				$('#harga').autoNumeric('init', {aSep: ',', aDec: '.', mDec: '0'});
				$('#hargaperliter').autoNumeric('init', {aSep: ',', aDec: '.', mDec: '0'});
			</script>
		";
		
		// $xa='';
		$code_upt = $ci->session->userdata('m_upt_code');
		$getupt = $ci->db->query("SELECT * FROM m_upt WHERE code = '".$code_upt."'")->row_array();
		$xa.='
			<div class="row">
				<div class="col-md-10">
					<div class="form-group">
						<label>KODE UPT</label>
						<output id="kode_upt" name="kode_upt" class="form-control">'.$code_upt.'</output>
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
				<div class="col-md-5">
					<div class="form-group">
						<label>NOMOR TAGIHAN</label>
						<output placeholder="" id="no_invoice" name="no_invoice" class="form-control">'.$no_invoice.'</output>
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>TANGGAL TAGIHAN</label>
						<output placeholder="" id="tgl_invoice" name="tgl_invoice" class="form-control">'.$tanggal_invoice.'</output>
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>PENYEDIA</label>
						<output placeholder="" id="penyedia" name="penyedia" class="form-control">'.$penyedia.'</output>
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>QUANTITY</label>
						<output placeholder="" id="quantity" name="quantity" class="form-control">'.$quantity.'</output>
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>HARGA TOTAL</label>
						<output placeholder="" id="harga" name="harga" class="form-control">'.$harga.'</output>
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>HARGA PER LITER</label>
						<output placeholder="" id="hargaperliter" name="hargaperliter" class="form-control">'.$hargaperliter.'</output>
					</div>
				</div>
			</div>
				
		';
		
		
		echo $xa;
	}
	
	
	#Pembatalan Tagihan
	public function getDataTableReject(){
		
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
		//$kond = "";
		//if($code != '000'){
		//    $kond = "AND m_upt_code = '".$code."'";
		//}
		//$sql = "SELECT * FROM bbm_tagihan WHERE statustagihan = '1' ".$kond." ".$cari." ORDER BY tanggal_input DESC ";
		$sql = "SELECT * FROM bbm_tagihan WHERE statustagihan = '1' ".$cari." ORDER BY tanggal_input DESC "; 
		
		$query = $ci->db->query($sql." LIMIT ".$iDisplayLength." OFFSET ".$iDisplayStart." ");
		$query2 = $ci->db->query($sql);
		$total = $query2->num_rows();
		
		
		if($total > 0){	
			$i = $iDisplayStart;
			foreach ($query->result() as $list){
				
				$i++;
				$aaData[] = array( 	
					$i,
					$list->m_upt_code,
					indo_date($list->tanggal_invoice),
					$list->no_tagihan,
					$list->penyedia,
					number_format($list->quantity,0,',','.'),
					'Rp. '.number_format($list->total,0,'.',','),
					$list->user_input.'<br>'.$list->tanggal_input,
					'
					<button style="padding: 5px;font-size: 15px;height: 100%;" type="button" class="btn btn-second mb-xl" onClick="getTab2('.$list->tagihan_id.')" title="Preview"><i class="fa fa-eye"></i></button>
					<button style="padding: 5px;font-size: 15px;height: 100%;" type="button" class="btn btn-danger mb-xl" onClick="batal('.$list->tagihan_id.')" title="Batal"><i class="fa fa-gavel"></i></button>
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
	
	
	#Insert Tanggal SPPD
	public function getDataTableSppd(){
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
		//$kond = "";
		//if($code != '000'){
		//   $kond = "AND m_upt_code = '".$code."'";
		//}
		//$sql = "SELECT * FROM bbm_tagihan WHERE statustagihan = '1' ".$kond." ".$cari." ORDER BY tanggal_input DESC "; 
		$sql = "SELECT * FROM bbm_tagihan WHERE statustagihan = '1' AND m_upt_code = '".$code."' ".$cari." ORDER BY tanggal_input DESC "; 
		
		$query = $ci->db->query($sql." LIMIT ".$iDisplayLength." OFFSET ".$iDisplayStart." ");
		$query2 = $ci->db->query($sql);
		$total = $query2->num_rows();
		
		
		if($total > 0){	
			$i = $iDisplayStart;
			foreach ($query->result() as $list){
				if($list->file_sppd != null){
					$prev = '<a href="'.base_url().'dokumen/dokumen_sppd/'.$list->file_sppd.'" target="_blank">Lihat Berkas</a>';
				}else{
					$prev = 'Berkas Kosong';
				}
				$i++;
				$aaData[] = array( 	
					$i,
					$list->m_upt_code,
					indo_date($list->tanggal_invoice),
					$list->no_invoice,
					$list->penyedia,
					number_format($list->quantity,0,',','.'),
					'Rp. '.number_format($list->total,0,'.',','),
					$list->user_input.'<br>'.$list->tanggal_input,
                	$prev,
					'
                    <button style="padding: 5px;font-size: 15px;height: 100%;" type="button" class="btn btn-success mb-xl" onClick="getTab4('.$list->tagihan_id.')" title="Upload"><i class="fa fa-upload"></i></button>
					<button style="padding: 5px;font-size: 15px;height: 100%;" type="button" class="btn btn-second mb-xl" onClick="getTab2('.$list->tagihan_id.')" title="Preview"><i class="fa fa-eye"></i></button>
					<button style="padding: 5px;font-size: 15px;height: 100%;" type="button" class="btn btn-warning mb-xl" onClick="getTab3('.$list->tagihan_id.')" title="Update Tanggal SPPD"><i class="fa fa-pencil"></i></button>
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

	function getDataUpload($tagihan_id){
		
		$ca = '';
		$ca .= '
		<div class="row">
			 
			
				 <div class="col-md-9 ">
					<div class="form-group">
						<label></label>
						<input type="file" name="my_images" class="form-control" />
					</div>
				</div>
				
				<input type="hidden" placeholder="" id="tagihan_id" name="tagihan_id" class="form-control" value="'.$tagihan_id.'">
			</div>';
		
		echo $ca;
	
	}
	
	function frmdataTagihansppd($id){
		$ci = & get_instance();
		$xa='';
		$dtnya = $ci->db->query("SELECT * FROM bbm_tagihan WHERE tagihan_id = '".$id."'")->row_array();
		$tanggal_invoice	= $dtnya['tanggal_invoice'];
		$no_invoice			= $dtnya['no_tagihan'];
		$penyedia			= $dtnya['penyedia'];
		$quantity			= $dtnya['quantity'];
		$harga				= $dtnya['total'];
		$hargaperliter		= $dtnya['hargaperliter'];
		$tgl_sppd			= $dtnya['tanggal_sppd'];
		$xa .= "
			<script>
				$('#quantity').autoNumeric('init', {aSep: '.', aDec: ',', mDec: '0'});
				$('#harga').autoNumeric('init', {aSep: ',', aDec: '.', mDec: '0'});
				$('#hargaperliter').autoNumeric('init', {aSep: ',', aDec: '.', mDec: '0'});
			</script>
		";
		
		// $xa='';
		$code_upt = $ci->session->userdata('m_upt_code');
		$getupt = $ci->db->query("SELECT * FROM m_upt WHERE code = '".$code_upt."'")->row_array();
		$xa.='
			<div class="row">
				<div class="col-md-10">
					<div class="form-group">
						<label>KODE UPT</label>
						<output id="kode_upt" name="kode_upt" class="form-control">'.$code_upt.'</output>
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
				<div class="col-md-5">
					<div class="form-group">
						<label>NOMOR TAGIHAN</label>
						<output placeholder="" id="no_invoice" name="no_invoice" class="form-control">'.$no_invoice.'</output>
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>TANGGAL TAGIHAN</label>
						<output placeholder="" id="tgl_invoice" name="tgl_invoice" class="form-control">'.$tanggal_invoice.'</output>
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>PENYEDIA</label>
						<output placeholder="" id="penyedia" name="penyedia" class="form-control">'.$penyedia.'</output>
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>QUANTITY</label>
						<output placeholder="" id="quantity" name="quantity" class="form-control">'.$quantity.'</output>
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>HARGA TOTAL</label>
						<output placeholder="" id="harga" name="harga" class="form-control">'.$harga.'</output>
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>HARGA PER LITER</label>
						<output placeholder="" id="hargaperliter" name="hargaperliter" class="form-control">'.$hargaperliter.'</output>
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>TANGGAL SPPD</label>
						<input type="text" placeholder="" id="tgl_sppd" name="tgl_sppd" class="form-control" style="line-height: 25px;"  value="'.$tgl_sppd.'">
					</div>
				</div>
			</div>
				
		';
		
		$xa.="
		<script>
			$('#tgl_sppd').datepicker({
				format: 'dd-mm-yyyy'
			});
		</script>
		";
		
		
		echo $xa;
	}
	
	function vwdataTagihansppd($id){
		$ci = & get_instance();
		$xa='';
		$dtnya = $ci->db->query("SELECT * FROM bbm_tagihan WHERE tagihan_id = '".$id."'")->row_array();
		$tanggal_invoice	= indo_date($dtnya['tanggal_invoice']);
		$no_invoice			= $dtnya['no_tagihan'];
		$penyedia			= $dtnya['penyedia'];
		$quantity			= $dtnya['quantity'];
		$harga				= $dtnya['total'];
		$hargaperliter		= $dtnya['hargaperliter'];
		if($dtnya['tanggal_sppd'] == '0000-00-00'){
			$tgl_sppd			= '-';
		}else{
			$tgl_sppd			= indo_date($dtnya['tanggal_sppd']);
		}
		$xa .= "
			<script>
				$('#quantity').autoNumeric('init', {aSep: '.', aDec: ',', mDec: '0'});
				$('#harga').autoNumeric('init', {aSep: ',', aDec: '.', mDec: '0'});
				$('#hargaperliter').autoNumeric('init', {aSep: ',', aDec: '.', mDec: '0'});
			</script>
		";
		
		// $xa='';
		$code_upt = $ci->session->userdata('m_upt_code');
		$getupt = $ci->db->query("SELECT * FROM m_upt WHERE code = '".$code_upt."'")->row_array();
		$xa.='
			<div class="row">
				<div class="col-md-10">
					<div class="form-group">
						<label>KODE UPT</label>
						<output id="kode_upt" name="kode_upt" class="form-control">'.$code_upt.'</output>
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
				<div class="col-md-5">
					<div class="form-group">
						<label>NOMOR TAGIHAN</label>
						<output placeholder="" id="no_invoice" name="no_invoice" class="form-control">'.$no_invoice.'</output>
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>TANGGAL TAGIHAN</label>
						<output placeholder="" id="tgl_invoice" name="tgl_invoice" class="form-control">'.$tanggal_invoice.'</output>
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>PENYEDIA</label>
						<output placeholder="" id="penyedia" name="penyedia" class="form-control">'.$penyedia.'</output>
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>QUANTITY</label>
						<output placeholder="" id="quantity" name="quantity" class="form-control">'.$quantity.'</output>
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>HARGA TOTAL</label>
						<output placeholder="" id="harga" name="harga" class="form-control">'.$harga.'</output>
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>HARGA PER LITER</label>
						<output placeholder="" id="hargaperliter" name="hargaperliter" class="form-control">'.$hargaperliter.'</output>
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label>TANGGAL SPPD</label>
						<output placeholder="" id="tgl_sppd" name="tgl_sppd" class="form-control">'.$tgl_sppd.'</output>
					</div>
				</div>
			</div>
				
		';
		
		
		echo $xa;
	}
}
