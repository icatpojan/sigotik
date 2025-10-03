<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lap extends CI_Controller {
	
	public function index(){
		parent::__construct();
		
		$this->load->helper('form');
		
	}  
	
	public function getDataTable($tahun){
		
		$ci = & get_instance();
		$sSearch	=  $this->input->post('sSearch');
		$iDisplayStart = $this->input->post('iDisplayStart');
		$iDisplayLength = $this->input->post('iDisplayLength');
		
		$cari = "";
 
		if($sSearch != NULL){
			$sSearch = strtoupper($sSearch);
			$cari .= " AND (nama LIKE '%".$sSearch."%' OR code LIKE '%".$sSearch."%')";
		}
		$prbhanke = $ci->db->query("SELECT MAX(perubahan_ke) AS maks FROM bbm_anggaran WHERE statusanggaran = '1' AND periode = '".$tahun."'")->row_array();
		$sql = "SELECT m_upt.code, m_upt.nama, anggaran FROM `bbm_anggaran` 
				JOIN m_upt ON m_upt.`code` = bbm_anggaran.m_upt_code
				WHERE statusanggaran = '1' AND periode = '".$tahun."' AND perubahan_ke = '".$prbhanke['maks']."'  ".$cari; 
		
		$query = $ci->db->query($sql." LIMIT ".$iDisplayLength." OFFSET ".$iDisplayStart." ");
		$query2 = $ci->db->query($sql);
		$total = $query2->num_rows();
		
		
		if($total > 0){	
			$i = $iDisplayStart;
			foreach ($query->result() as $list){
				$i++;
				$aaData[] = array( 
					
					$i,
					$list->code,
					$list->nama,
					$list->anggaran
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
	
	function showdtotal($tahun){
	    $ci = & get_instance();
		$code = $ci->session->userdata('m_upt_code');
		$prbhankeawal = $ci->db->query("SELECT if(MAX(perubahan_ke)-1<=0,0,MAX(perubahan_ke)-1) AS maks FROM bbm_anggaran WHERE periode = '".$tahun."'")->row_array();
		//$prbhanke     = $ci->db->query("SELECT MAX(perubahan_ke) AS maks FROM bbm_anggaran WHERE statusanggaran = '1' AND periode = '".$tahun."'")->row_array();
		$prbhanke     = $ci->db->query("SELECT MAX(perubahan_ke) AS maks FROM bbm_anggaran WHERE periode = '".$tahun."'")->row_array();
		if ($prbhankeawal['maks']==$prbhanke['maks']) {
			$sql = $ci->db->query("SELECT m_upt.code, m_upt.nama, anggaran FROM `bbm_anggaran` 
					JOIN m_upt ON m_upt.`code` = bbm_anggaran.m_upt_code
					WHERE statusanggaran = '1' AND periode = '".$tahun."' AND perubahan_ke = '".$prbhanke['maks']."' "); 
		} else {
			$ss = "SELECT code, nama,anggaran_awal,anggaran,anggaran-anggaran_awal AS perubahan
								  FROM
								  (SELECT m_upt.code, m_upt.nama,SUM(IF(perubahan_ke='".$prbhankeawal['maks']."',anggaran,0)) AS anggaran_awal,
								   SUM(IF(perubahan_ke='".$prbhanke['maks']."',anggaran,0)) AS anggaran FROM `bbm_anggaran` JOIN m_upt ON m_upt.`code` = bbm_anggaran.m_upt_code ";
			if($code==0) {
			$ss .=	"WHERE ";
			}else {
			$ss .=	"WHERE m_upt.code = ".$code." and ";
			}
			$ss .= "statusanggaran = '1' AND periode = '".$tahun."' GROUP BY CODE ORDER BY CODE) anggaran ";
			$sql = $ci->db->query($ss);
		}	
		$jml = $sql->num_rows();
		if ($prbhankeawal['maks']==$prbhanke['maks']) {
			$x = '
				<h2 style="text-align: center;color: black;">LAPORAN ANGGARAN BBM</h2>
				<h2 style="text-align: center;color: black;">PERIODE TAHUN '.date('Y').'</h2>
				<table id="data_tables" class="table3 table3-striped table3-bordered table3-hover" >
					<thead>
						<tr>
							<th>No</th>
							<th>Kode UPT</th>
							<th>Nama UPT</th>
							<th>Anggaran</th>
						</tr>
					</thead>
					<tbody>
					';
			if($jml > 0){
				$i=1;
				$total = 0;
				foreach($sql->result() AS $list){
				$x.='
							<tr>
								<td>'.$i.'</td>
								<td>'.$list->code.'</td>
								<td>'.$list->nama.'</td>
								<td style="text-align:right;">Rp. '.number_format($list->anggaran,0,',','.').'</td>
							</tr>
				';
				$i++;
				$total+=$list->anggaran;
				}
				$x.='
							<tr>
								<td colspan="3" style="text-align:right;">GRAND TOTAL</td>
								<td style="text-align:right;">Rp. '.number_format($total,0,',','.').'</td>
							</tr>
				';
			}else{
				$x.='
					<tr>
						<td colspan="4" style="text-align: center;">- Data Kosong -</td>
					</tr>
				';
			}
			$x.='
					</tbody>
				</table>
			';
		} else {
			$x = '
				<h2 style="text-align: center;color: black;">LAPORAN ANGGARAN BBM</h2>
				<h2 style="text-align: center;color: black;">PERIODE TAHUN '.date('Y').'</h2>
				<table id="data_tables" class="table3 table3-striped table3-bordered table3-hover" >
					<thead>
						<tr>
							<th>No</th>
							<th>Kode UPT</th>
							<th>Nama UPT</th>
							<th>Anggaran perubahan ke '.$prbhankeawal["maks"].'</th>
							<th>Anggaran Perubahan ke '.$prbhanke["maks"].'</th>
							<th>Perubahan</th>
						</tr>
					</thead>
					<tbody>
					';
			if($jml > 0){
				$i=1;
				$total = 0;
				$totalawal = 0;
				$rubah = 0;
				foreach($sql->result() AS $list){
				$x.='
							<tr>
								<td>'.$i.'</td>
								<td>'.$list->code.'</td>
								<td>'.$list->nama.'</td>
								<td style="text-align:right;">Rp. '.number_format($list->anggaran_awal,0,',','.').'</td>
								<td style="text-align:right;">Rp. '.number_format($list->anggaran,0,',','.').'</td>
								<td style="text-align:right;">Rp. '.number_format($list->perubahan,0,',','.').'</td>								
							</tr>
				';
				$i++;
				$totalawal+=$list->anggaran_awal;
				$total+=$list->anggaran;
				$rubah+=$list->perubahan;				}
				$x.='
							<tr>
								<td colspan="3" style="text-align:right;">GRAND TOTAL</td>
								<td style="text-align:right;">Rp. '.number_format($totalawal,0,',','.').'</td>
								<td style="text-align:right;">Rp. '.number_format($total,0,',','.').'</td>
								<td style="text-align:right;">Rp. '.number_format($rubah,0,',','.').'</td>
							</tr>
				';
			}else{
				$x.='
					<tr>
						<td colspan="4" style="text-align: center;">- Data Kosong -</td>
					</tr>
				';
			}
			$x.='
					</tbody>
				</table>
			';	
		}	
		echo $x;
	}
	
	function showdtotalpake($tgl_awal, $tgl_akhir){
		$ci = & get_instance();
		$code = $ci->session->userdata('m_upt_code');
		if($tgl_awal == '0' && $tgl_akhir == '0'){
			$priodeaw = '-';
			$priodeak = '-';
			$tahun = '0';
		}else{
		    $tgl_awal = date('yy-m-d', strtotime($tgl_awal));
		    $tgl_akhir = date('yy-m-d', strtotime($tgl_akhir));
			$priodeaw = strtoupper(indo_date($tgl_awal));
			$priodeak = strtoupper(indo_date($tgl_akhir));
			$tahun = date('Y',strtotime($tgl_awal));
		}
		$prbhanke = $ci->db->query("SELECT MAX(perubahan_ke) AS maks FROM bbm_anggaran WHERE periode = '".$tahun."'")->row_array();
		
		$ss = "SELECT m_upt.code, m_upt.nama, if(statusanggaran = '1',anggaran,0) as anggaran,
								(
									SELECT IF(SUM(total) IS NULL or statustagihan <> '1', '0', SUM(total)) FROM bbm_tagihan 
									WHERE m_upt_code = bbm_anggaran.m_upt_code AND tanggal_invoice BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."'
									
								) AS tagihan

								FROM `bbm_anggaran` 
								LEFT JOIN m_upt ON m_upt.`code` = bbm_anggaran.m_upt_code ";
		if($code==0) {
			$ss .=	"WHERE ";
		}else {
			$ss .=	"WHERE m_upt.code = ".$code." and ";
		}
		$ss .= "periode = '".$tahun."' AND perubahan_ke = '".$prbhanke['maks']."' ";		
		
		$sql = $ci->db->query($ss);
		$jum = $sql->num_rows();
		$x = '
			<h2 style="text-align: center;color: black;">LAPORAN PENGGUNAAN ANGGARAN DAN TAGIHAN BBM</h2>
			<h2 style="text-align: center;color: black;">PERIODE : '.$priodeaw.' SAMPAI DENGAN '.$priodeak.'</h2>
			<table id="data_tables" class="table3 table3-striped table3-bordered table3-hover" >
				<thead>
					<tr>
						<th>No</th>
						<th>Kode UPT</th>
						<th>Nama UPT</th>
						<th>Total Anggaran</th>
						<th>Total Tagihan</th>
						<th>Sisa Anggaran</th>
						<th>Persentase</th>
					</tr>
				</thead>
				<tbody>
				';
		if($jum > 0){
			$i=1;
			$totalang = 0;
			$totaltag = 0;
			$totalsan = 0;
			$sisa_anggaran = 0;
			$totpersn = 0;
			foreach($sql->result() AS $list){
				$sisa_anggaran = $list->anggaran - $list->tagihan;
				if($list->tagihan<=0 or $list->anggaran<=0){
					$persentase = 0;
				} else {
					$persentase = ($list->tagihan / $list->anggaran) * 100;
				}	
			$x.='
						<tr>
							<td>'.$i.'</td>
							<td>'.$list->code.'</td>
							<td>'.$list->nama.'</td>
							<td style="text-align:right;">Rp. '.number_format($list->anggaran,0,',','.').'</td>
							<td style="text-align:right;">Rp. '.number_format($list->tagihan,0,',','.').'</td>
							<td style="text-align:right;">Rp. '.number_format($sisa_anggaran,0,',','.').'</td>
							<td style="text-align:right;">'.number_format($persentase,2,',','.').' %</td>
						</tr>
			';
			$i++;
			$totalang +=$list->anggaran;
			$totaltag +=$list->tagihan;
			$totalsan +=$sisa_anggaran;
			if($totaltag<=0 or $totalang<=0){
				$totpersn = 0;
			} else {	
				$totpersn = ($totaltag / $totalang) * 100;
			}
			}
			$x.='
						<tr>
							<td colspan="3" style="text-align:right;">GRAND TOTAL</td>
							<td style="text-align:right;">Rp. '.number_format($totalang,0,',','.').'</td>
							<td style="text-align:right;">Rp. '.number_format($totaltag,0,',','.').'</td>
							<td style="text-align:right;">Rp. '.number_format($totalsan,0,',','.').'</td>
							<td style="text-align:right;">'.number_format($totpersn,2,',','.').' %</td>
						</tr>
			';
		}else{
			$x.='
						<tr>
							<td colspan="6" style="text-align: center;">- Data Kosong -</td>
						</tr>
			';
		}
		$x.='
					</tbody>
				</table>
		';
		
		echo $x;
	}
	
	function getupt(){
		$ci = & get_instance();
		$code = $ci->session->userdata('m_upt_code');
		
		$sql = $ci->db->query("SELECT * FROM m_upt WHERE code = '".$code."'")->row_array();
		$x='';
		$x.='
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<label>Kode UPT :</label>
						<output placeholder="" id="kode_upt" name="kode_upt" class="form-control">'.$code.'</output>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<label>Nama UPT :</label>
						<output placeholder="" id="nama_upt" name="nama_upt" class="form-control">'.$sql['nama'].'</output>
					</div>
				</div>
			</div>
		';
		if($code == '000'){
		$x.='
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<label>Pilih UPT :</label>
						<select class="form-control custom-control" id="m_upt_code" name="m_upt_code">';
						$sql = $ci->db->query("SELECT * FROM m_upt ORDER BY nama");
						foreach($sql->result() AS $data){
		$x.='
							<option value="'.$data->code.'">'.$data->nama.'</option>';	
									
						}
		$x.='
					</select>
					</div>
				</div>
			</div>
			';
		}else{
		$x.='';
		}
		
		echo $x;
		
	}
	
	function showPenggunaanUPT($tgl_awal, $tgl_akhir,$codes){
		$ci = & get_instance();
		if($tgl_awal == '0' && $tgl_akhir == '0'){
			$priodeaw = '-';
			$priodeak = '-';
			$tahun = '0';
		}else{
		    $tgl_awal = date('Y-m-d', strtotime($tgl_awal));
		    $tgl_akhir = date('Y-m-d', strtotime($tgl_akhir));
			$priodeaw = strtoupper(indo_date($tgl_awal));
			$priodeak = strtoupper(indo_date($tgl_akhir));
			$tahun = date('Y',strtotime($tgl_awal));
		}
		
		$m_code = $ci->session->userdata('m_upt_code');
		if($m_code == '000'){
			$code = $codes;
		}else{
			$code = $m_code;
		}
		
		$prbhanke = $ci->db->query("SELECT MAX(perubahan_ke) AS maks, anggaran FROM bbm_anggaran WHERE m_upt_code = '".$code."' AND periode = '".$tahun."'")->row_array();
		/*$sql = $ci->db->query("SELECT tanggal_invoice, no_invoice, total,
								(
									SELECT IF(COUNT(anggaran) = '0','0',anggaran) FROM bbm_anggaran WHERE m_upt_code = '".$code."' AND periode = '".$tahun."' AND statusanggaran = '1' AND perubahan_ke = '".$prbhanke['maks']."'
								) AS anggaran,
								(
									SELECT IF(SUM(total) IS NULL,'0',SUM(total)) FROM bbm_tagihan WHERE tanggal_invoice BETWEEN DATE_FORMAT('".$tgl_awal."','%Y-01-01') AND DATE_ADD('".$tgl_awal."',INTERVAL -1 DAY) AND statustagihan = '1'
								) AS sisa_angaran
								FROM `bbm_tagihan`
								WHERE tanggal_invoice BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."' AND statustagihan = '1'
								"); */

		$sql = $ci->db->query("SELECT STR_TO_DATE(tanggal_input,'%Y-%m-%d') AS tanggal_input,'' AS no_tagihan,IF(COUNT(anggaran) = 0 OR statusanggaran <> '1','0',anggaran) AS bbm_anggaran,0 AS bbm_tagihan
								FROM bbm_anggaran WHERE m_upt_code = '".$code."' AND periode = '".$tahun."' AND perubahan_ke = '".$prbhanke['maks']."'
								UNION
								SELECT tanggal_invoice AS tanggal_input,no_invoice AS no_tagihan,0 AS bbm_anggaran,total AS bbm_tagihan FROM bbm_tagihan
								WHERE m_upt_code = '".$code."' AND statustagihan = 1 AND tanggal_invoice BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."' order by tanggal_input ");
								
		$jum = $sql->num_rows();
			// <h2 style="text-align: center;color: black;"></h2>
		$x = '
			<h2 style="text-align: center;color: black;">PERIODE : '.$priodeaw.' SAMPAI DENGAN '.$priodeak.'</h2>
			<table id="data_tables" class="table3 table3-striped table3-bordered table3-hover" >
				<thead>
					<tr>
						<th>No</th>
						<th>Tanggal</th>
						<th>No Tagihan</th>
						<th>Total Anggaran</th>
						<th>Tagihan</th>
						<th>Sisa Anggaran</th>
					</tr>
				</thead>
				<tbody>
				';
		if($jum > 0){
			$i=1;
			$totalang = 0;
			$totaltag = 0;
			$totalsan = 0;
			$sisa_anggaran = 0;
			$angg = 0;
			foreach($sql->result() AS $list){
					$angg = $list->bbm_anggaran - $list->bbm_tagihan;
					$sisa_anggaran = $sisa_anggaran + $angg;
			$x.='
						<tr>
							<td>'.$i.'</td>
							<td>'.indo_date($list->tanggal_input).'</td>
							<td>'.$list->no_tagihan.'</td>
							<td style="text-align:right;">Rp. '.number_format($list->bbm_anggaran,0,',','.').'</td>
							<td style="text-align:right;">Rp. '.number_format($list->bbm_tagihan,0,',','.').'</td>
							<td style="text-align:right;">Rp. '.number_format($sisa_anggaran,0,',','.').'</td>
						</tr>
			';
			$i++;
			//$angg -= $list->total;
			$totalang +=$list->bbm_anggaran;
			$totaltag +=$list->bbm_tagihan;
			$totalsan +=$sisa_anggaran;
			}
			$x.='
						<tr>
							<td colspan="3" style="text-align:right;">GRAND TOTAL</td>
							<td style="text-align:right;">Rp. '.number_format($totalang,0,',','.').'</td>
							<td style="text-align:right;">Rp. '.number_format($totaltag,0,',','.').'</td>
							<td style="text-align:right;"></td>
						</tr>
			';
		}else{
			$x.='
						<tr>
							<td colspan="6" style="text-align: center;">- Data Kosong -</td>
						</tr>
			';
		}
		$x.='
					</tbody>
				</table>
		';
		
		echo $x;
	}
	
	/*function showdtagihanupt($tahun,$codes){
		$ci = & get_instance();
		
		$m_code = $ci->session->userdata('m_upt_code');
		if($m_code == '000'){
			$code = $codes;
		}else{
			$code = $m_code;
		}
		
		$sql = $ci->db->query("SELECT * FROM `bbm_tagihan`
								WHERE m_upt_code = '".$code."' AND YEAR(tanggal_invoice) = '".$tahun."' AND tanggal_sppd IS NOT NULL ");
		$jum = $sql->num_rows();
		$x = '
			<h2 style="text-align: center;color: black;">LAPORAN REALISASI BBM</h2>
			<h2 style="text-align: center;color: black;">PERIODE TAHUN '.date('Y').'</h2>
			<table id="data_tables" class="table3 table3-striped table3-bordered table3-hover" >
				<thead>
					<tr>
						<th>No</th>
						<th>Bulan</th>
						<th>Tanggal Tagihan</th>
						<th>Nomor Tagihan</th>
						<th>BBM (Ltr)</th>
						<th>Nominal</th>
						<th>Harga Rata2</th>
						<th>Tgl SP2D</th>
					</tr>
				</thead>
				<tbody>
				';
		if($jum > 0){
			$i=1;
		$total = 0;
		foreach($sql->result() AS $list){
			$rata = $list->total/$list->quantity;
			$x.='
						<tr>
							<td>'.$i.'</td>
							<td>'.get_bulan(date('m', strtotime($list->tanggal_invoice))).'</td>
							<td>'.indo_date($list->tanggal_invoice).'</td>
							<td>'.$list->no_invoice.'</td>
							<td>'.number_format($list->quantity,0,',','.').'</td>
							<td>Rp. '.number_format($list->total,0,',','.').'</td>
							<td>Rp. '.number_format($rata,2,',','.').'</td>
							<td>'.indo_date($list->tanggal_sppd).'</td>
						</tr>
			';
			$i++;
			}
		}else{
			$x.='
						<tr>
							<td colspan="8" style="text-align: center;">- Data Kosong -</td>
						</tr>
			';
		}
		
		$x.='
					
				</tbody>
			</table>
		';
		
		echo $x;
	} */
	
	function showdtagihanupt($tahun,$codes){
		$ci = & get_instance();
		
		$m_code = $ci->session->userdata('m_upt_code');
		if($m_code == '000'){
			$code = $codes;
		}else{
			$code = $m_code;
		}
		
		//$sql = $ci->db->query("SELECT * FROM `bbm_tagihan`
		//						WHERE m_upt_code = '".$code."' AND YEAR(tanggal_invoice) = '".$tahun."' AND tanggal_sppd IS NOT NULL ");

		$sql = $ci->db->query("SELECT tanggal_invoice,quantity,total,rata,IF(grouptgl=1,'01-10',IF(grouptgl=2,'11-20',IF(grouptgl=3,CONCAT('21-',DAY(LAST_DAY(tanggal_invoice))),''))) AS periode,tanggal_sppd 
					    FROM
						(SELECT tanggal_invoice,SUM(quantity) AS quantity,SUM(total) AS total,SUM(total)/SUM(quantity) AS rata,tanggal_invoice AS periode,tanggal_sppd ,
						IF(CEILING(DAY(tanggal_invoice)/10)>3,3,CEILING(DAY(tanggal_invoice)/10)) AS grouptgl
						FROM bbm_tagihan WHERE statustagihan = 1 and m_upt_code = '".$code."' AND YEAR(tanggal_invoice) = '".$tahun."' AND tanggal_sppd IS NOT NULL 
						GROUP BY MONTH(tanggal_invoice),grouptgl) csql ");
								
		$jum = $sql->num_rows();
		$x = '
			<h2 style="text-align: center;color: black;">REALISASI TAGIHAN BBM KAPAL PENGAWAS</h2>
			<h2 style="text-align: center;color: black;">DIREKTORAT PEMANTAUAN DAN ARMADA</h2>
			<h2 style="text-align: center;color: black;">TAHUN ANGGARAN '.date('Y').'</h2>
			<table id="data_tables" class="table3 table3-striped table3-bordered table3-hover" >
				<thead>
					<tr>
						<th style="text-align:center; vertical-align: middle " rowspan = "2">No</th>
						<th style="text-align:center; vertical-align: middle "rowspan = "2">Bulan</th>
						<th style="text-align:center" colspan = "3">Rincian Tagihan</th>
						<th style="text-align:center; vertical-align: middle " rowspan = "2"> Harga Rata - Rata</th>
						<th style="text-align:center;  vertical-align: middle" rowspan = "2">PERIODE</th>						
						<th style="text-align:center;  vertical-align: middle" rowspan = "2">Tgl SP2D</th>
					</tr>
					<tr>
						<th style="text-align:center" >Tagihan</th>
						<th style="text-align:center" >BBM (Ltr)</th>
						<th style="text-align:center" >Nominal</th>
					</tr>
				</thead>
				<tbody>
				';
		if($jum > 0){
			$i=1;
		$totalquantity = 0;
		$totalharga = 0;
		foreach($sql->result() AS $list){
			$rata = $list->total/$list->quantity;
			$totalquantity += $list->quantity;
			$totalharga    += $list->total;
			if ($list->tanggal_sppd == '0000-00-00') {
				$mydate = '';				
			} else {
				$mydate = indo_date($list->tanggal_sppd);
			}

			$x.='
						<tr>
							<td>'.$i.'</td>
							<td>'.get_bulan(date('m', strtotime($list->tanggal_invoice))).'</td>
							<td> Tagihan '.$i.'</td>
							<td style="text-align:right">'.number_format($list->quantity,0,',','.').'</td>
							<td style="text-align:right">Rp. '.number_format($list->total,0,',','.').'</td>
							<td style="text-align:right">Rp. '.number_format($rata,2,',','.').'</td>
							<td>'.$list->periode.' '.get_bulan(date('m', strtotime($list->tanggal_invoice))).'
							<td>'.$mydate.'</td>
						</tr>
			';
			$i++;
			}
			$rata2 = $totalharga/$totalquantity;
			$x.='
						<tr>
							<td colspan = "3">REALISASI (s.d '.indo_date($list->tanggal_invoice).'</td>
							<td style="text-align:right">'.number_format($totalquantity,0,',','.').'</td>
							<td style="text-align:right">Rp. '.number_format($totalharga,0,',','.').'</td>
							<td style="text-align:right">Rp. '.number_format($rata2,2,',','.').'</td>
						</tr>';
			
		}else{
			$x.='
						<tr>
							<td colspan="8" style="text-align: center;">- Data Kosong -</td>
						</tr>
			';
		}
		
		$x.='
					
				</tbody>
			</table>
		';
		
		echo $x;
	}
	
	function showdtotalpakaibbm($tgl_awal,$tgl_akhir,$codes,$codekpl){
		$ci = & get_instance();
		$tgl_awal = date('Y-m-d', strtotime($tgl_awal));
		$tgl_akhir = date('Y-m-d', strtotime($tgl_akhir));
		$tahun = date('Y',strtotime($tgl_awal));
		$kond ="";
		if($codes != '000' or $codes != '0'){
		    $kond ="AND m_kapal.m_upt_code = '".$codes."'";
		}
		if($codekpl != '0' or $codekpl != '000') {
		    $kond .=" AND m_kapal.m_kapal_id = '".$codekpl."' ";			
		}
		//$prbhanke = $ci->db->query("SELECT MAX(perubahan_ke) AS maks FROM bbm_anggaran WHERE statusanggaran = '1' AND periode = '".$tahun."'")->row_array();
		$sql = $ci->db->query("SELECT kapal_code,nama_kapal,SUM(penerimaan) AS penerimaan,SUM(penggunaan) AS penggunaan
		FROM m_kapal,
		(SELECT kapal_code,tanggal_surat,a.nomor_surat,SUM(volume_isi) AS penerimaan,0 AS penggunaan
		FROM bbm_kapaltrans a,bbm_transdetail b WHERE status_ba = '5' AND a.nomor_surat=b.nomor_surat AND tanggal_surat >= '".$tgl_awal."' 
		AND tanggal_surat <= '".$tgl_akhir."' GROUP BY nomor_surat UNION 
		SELECT kapal_code,tanggal_surat,nomor_surat,0 AS penerimaan,volume_sisa AS  penggunaan
		FROM bbm_kapaltrans  WHERE status_ba = '3' AND tanggal_surat >= '".$tgl_awal."' AND tanggal_surat <= '".$tgl_akhir."') trans 
		WHERE m_kapal.code_kapal=trans.kapal_code ".$kond." GROUP BY kapal_code;");
		
		$jml = $sql->num_rows();
		$x = '
			<h2 style="text-align: center;color: black;">LAPORAN TOTAL PENGGUNAAN DAN PENERIMAAN BBM</h2>
			<h2 style="text-align: center;color: black;">PERIODE : '.strtoupper(indo_date($tgl_awal)).' SAMPAI DENGAN '.strtoupper(indo_date($tgl_akhir)).'</h2>
			<table id="data_tables" class="table3 table3-striped table3-bordered table3-hover" >
				<thead>
					<tr>
						<th>No</th>
						<th>Kode Kapal</th>
						<th>Nama Kapal</th>
						<th>Total Penerimaan</th>
						<th>Total Penggunaan</th>
					</tr>
				</thead>
				<tbody>
				';
		if($jml > 0){
		    $i=1;
		$totalang = 0;
		$totaltag = 0;
		$totalsan = 0;
		$sisa_anggaran = 0;
		foreach($sql->result() AS $list){
    		$x.='
    					<tr>
    						<td>'.$i.'</td>
    						<td>'.$list->kapal_code.'</td>
    						<td>'.$list->nama_kapal.'</td>
    						<td style="text-align:right;">'.number_format($list->penerimaan,0,',','.') .' Liter</td>
    						<td style="text-align:right;">'.number_format($list->penggunaan,0,',','.') .' Liter</td>
    					</tr>
    		';
    		$i++;
    		$totalang +=$list->penerimaan;
    		$totaltag +=$list->penggunaan;
    		$totalsan +=$sisa_anggaran;
    		}
    		$x.='
    					<tr>
    						<td colspan="3" style="text-align:right;">GRAND TOTAL</td>
    						<td style="text-align:right;">'.number_format($totalang,0,',','.') .' Liter</td>
    						<td style="text-align:right;">'.number_format($totaltag,0,',','.') .' Liter</td>
    					</tr>
    		';
		}else{
		    $x.='
		        <tr>
		            <td colspan="6" style="text-align: center;">- Data Kosong -</td>
		        </tr>
		    ';
		}
		
		$x.='
		    	</tbody>
    		</table>
		';
		
		
		echo $x;
	}
	
	function showdtotalpakaibbm2($tgl_awal, $tgl_akhir,$codes,$codekpl){
		$ci = & get_instance();
		$tgl_awal = date('yy-m-d', strtotime($tgl_awal));
		$tgl_akhir = date('yy-m-d', strtotime($tgl_akhir));
		$tahun = date('Y',strtotime($tgl_awal));
		//$prbhanke = $ci->db->query("SELECT MAX(perubahan_ke) AS maks FROM bbm_anggaran WHERE statusanggaran = '1' AND periode = '".$tahun."'")->row_array();

		$kond ="";
		if($codes != '000' or $codes != '0'){
		    $kond ="AND m_kapal.m_upt_code = '".$codes."'";
		}
		if($codekpl != '0' or $codekpl != '000') {
		    $kond .=" AND m_kapal.m_kapal_id = '".$codekpl."' ";			
		}

		
		$sql = $ci->db->query("SELECT tanggal_surat,nomor_surat,kapal_code,nama_kapal,penerimaan ,penggunaan
		FROM m_kapal,
		(SELECT kapal_code,tanggal_surat,a.nomor_surat,SUM(volume_isi) AS penerimaan,0 AS penggunaan 
		FROM bbm_kapaltrans a,bbm_transdetail b WHERE status_ba = '5' AND a.nomor_surat=b.nomor_surat AND tanggal_surat >= '".$tgl_awal."' AND tanggal_surat <= '".$tgl_akhir."'
		GROUP BY nomor_surat
		UNION
		SELECT kapal_code,tanggal_surat,nomor_surat,0 AS penerimaan,volume_sisa AS  penggunaan
		FROM bbm_kapaltrans  WHERE status_ba = '3' AND tanggal_surat >= '".$tgl_awal."' AND tanggal_surat <= '".$tgl_akhir."') trans 
		WHERE m_kapal.code_kapal=trans.kapal_code ".$kond." ORDER BY tanggal_surat,nomor_surat,kapal_code;");
		$jml = $sql->num_rows();
		$x = '
			<h2 style="text-align: center;color: black;">LAPORAN DETAIL PENGGUNAAN DAN PENERIMAAN BBM</h2>
			<h2 style="text-align: center;color: black;">PERIODE : '.strtoupper(indo_date($tgl_awal)).' SAMPAI DENGAN '.strtoupper(indo_date($tgl_akhir)).'</h2>
			<table id="data_tables" class="table3 table3-striped table3-bordered table3-hover" >
				<thead>
					<tr>
						<th>No</th>
						<th>Tanggal BA</th>
						<th>Nomor Surat</th>
						<th>Kode Kapal</th>
						<th>Nama Kapal</th>
						<th>Total Penerimaan</th>
						<th>Total Penggunaan</th>
					</tr>
				</thead>
				<tbody>
				';
		if($jml > 0){
		    $i=1;
		$totalang = 0;
		$totaltag = 0;
		$totalsan = 0;
		$sisa_anggaran = 0;
		foreach($sql->result() AS $list){
    		$x.='
    					<tr>
    						<td>'.$i.'</td>
    						<td>'.$list->tanggal_surat.'</td>
    						<td>'.$list->nomor_surat.'</td>
    						<td>'.$list->kapal_code.'</td>
    						<td>'.$list->nama_kapal.'</td>
    						<td style="text-align:right;">'.number_format($list->penerimaan,0,',','.').' Liter</td>
    						<td style="text-align:right;">'.number_format($list->penggunaan,0,',','.').' Liter</td>
    					</tr>
    		';
    		$i++;
    		$totalang +=$list->penerimaan;
    		$totaltag +=$list->penggunaan;
    		$totalsan +=$sisa_anggaran;
    		}
    		$x.='
    					<tr>
    						<td colspan="5" style="text-align:right;">GRAND TOTAL</td>
    						<td style="text-align:right;">'.number_format($totalang,0,',','.').' Liter</td>
    						<td style="text-align:right;">'.number_format($totaltag,0,',','.').' Liter</td>
    					</tr>
    		';
		}else{
		    $x.='
		        <tr>
		            <td colspan="6" style="text-align: center;">- Data Kosong -</td>
		        </tr>
		    ';
		}
		
		$x.='
		    	</tbody>
    		</table>
		';
		
		
		echo $x;
	}

	function showdtotalpakaibbm3($tgl_awal, $tgl_akhir,$m_kapal_id,$m_kapal_nama){
		$ci = & get_instance();
		$tgl_awal = date('yy-m-d', strtotime($tgl_awal));
		$tgl_akhir = date('yy-m-d', strtotime($tgl_akhir));
		$tahun = date('Y',strtotime($tgl_awal));
		$prbhanke = $ci->db->query("SELECT nama_kapal FROM m_kapal WHERE m_kapal_id = '".$m_kapal_id."'");
		foreach($prbhanke->result() AS $list2){
			$nama_kapal = $list2->nama_kapal;
		}	
		$sql = $ci->db->query("SELECT tanggal_surat,nomor_surat,kapal_code,nama_kapal,penerimaan ,penggunaan
		FROM m_kapal,
		(SELECT kapal_code,tanggal_surat,a.nomor_surat,SUM(volume_isi) AS penerimaan,0 AS penggunaan 
		FROM bbm_kapaltrans a,bbm_transdetail b WHERE status_ba = '5' AND a.nomor_surat=b.nomor_surat AND tanggal_surat >= '".$tgl_awal."' AND tanggal_surat <= '".$tgl_akhir."'
		GROUP BY nomor_surat
		UNION
		SELECT kapal_code,tanggal_surat,nomor_surat,0 AS penerimaan,volume_sisa AS  penggunaan
		FROM bbm_kapaltrans  WHERE status_ba = '3' AND tanggal_surat >= '".$tgl_awal."' AND tanggal_surat <= '".$tgl_akhir."') trans 
		WHERE m_kapal.code_kapal=trans.kapal_code and m_kapal_id = '".$m_kapal_id."'ORDER BY tanggal_surat,nomor_surat,kapal_code;");
		$jml = $sql->num_rows();
		$x = '
			<h2 style="text-align: center;color: black;">LAPORAN DETAIL PENGGUNAAN DAN PENERIMAAN BBM</h2>
			<h2 style="text-align: center;color: black;">NAMA KAPAL : '.strtoupper($nama_kapal).'</h2>
			<h2 style="text-align: center;color: black;">PERIODE : '.strtoupper(indo_date($tgl_awal)).' SAMPAI DENGAN '.strtoupper(indo_date($tgl_akhir)).'</h2>
			<table id="data_tables" class="table3 table3-striped table3-bordered table3-hover" >
				<thead>
					<tr>
						<th>No</th>
						<th>Tanggal BA</th>
						<th>Nomor Surat</th>
						<th>Total Penerimaan</th>
						<th>Total Penggunaan</th>
					</tr>
				</thead>
				<tbody>
				';
		if($jml > 0){
		    $i=1;
		$totalang = 0;
		$totaltag = 0;
		$totalsan = 0;
		$sisa_anggaran = 0;
		foreach($sql->result() AS $list){
    		$x.='
    					<tr>
    						<td>'.$i.'</td>
    						<td>'.$list->tanggal_surat.'</td>
    						<td>'.$list->nomor_surat.'</td>
    						<td style="text-align:right;">'.number_format($list->penerimaan,0,',','.').' Liter</td>
    						<td style="text-align:right;">'.number_format($list->penggunaan,0,',','.').' Liter</td>
    					</tr>
    		';
    		$i++;
    		$totalang +=$list->penerimaan;
    		$totaltag +=$list->penggunaan;
    		$totalsan +=$sisa_anggaran;
    		}
    		$x.='
    					<tr>
    						<td colspan="3" style="text-align:right;">GRAND TOTAL</td>
    						<td style="text-align:right;">'.number_format($totalang,0,',','.').' Liter</td>
    						<td style="text-align:right;">'.number_format($totaltag,0,',','.').' Liter</td>
    					</tr>
    		';
		}else{
		    $x.='
		        <tr>
		            <td colspan="6" style="text-align: center;">- Data Kosong -</td>
		        </tr>
		    ';
		}
		
		$x.='
		    	</tbody>
    		</table>
		';
		
		
		echo $x;
	}
	
}
