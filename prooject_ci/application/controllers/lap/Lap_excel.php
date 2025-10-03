<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lap_excel extends CI_Controller {
	
	public function index(){
		parent::__construct();
		
		$this->load->helper('form');
		
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
					WHERE statusanggaran = '1' AND periode = '".$tahun."' AND perubahan_ke = '".$prbhanke['maks']."' GROUP BY code "); 
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
			<style>
			p.small {
			  line-height: 0.1;
			}

			p.big {
			  line-height: 0.4;
			}

			hr.style2 {
				border-top: 3px double #8c8b8b;
			}
			@media print {
			input.noPrint { display: none; }
			}
			@page
					{
						size: auto;   /* auto is the initial value */
						margin: 0mm;  /* this affects the margin in the printer settings */
					}
			</style>
			<table cellpadding=0 cellspacing=0 width="100%" >
				<tr>
					<td width="20%">
						<img align="center" width="120" height="120" src="'.base_url().'assets/img/kkp3.png" alt="test alt attribute" border="0" />		
					</td>
					<td><b>
						<p class = "small" align=center style="text-align:center"> <span style="font-size:14.0pt;color:#0000E1">LAPORAN ANGGARAN BBM</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">DIREKTORAT PEMANTAUAN DAN ARMADA</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">TAHUN ANGGARAN '.date('Y').'</span></p>
						<p class = "small" align=right style="text-align:right"> <span lang=SV style="color:#0000E1">Dalam Rupiah</span></p>
						</b>
					</td>
				</tr>
				<tr>
				</tr>
			</table>
			<br/>
			<hr style = "border-top: 3px double #8c8b8b;" />
			<table id="data_tables" border=1 cellspacing=0 cellpadding=5 width = "100%"" >	
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
								<td style="text-align:right;">'.number_format($list->anggaran,0).'</td>
							</tr>
				';
				$i++;
				$total+=$list->anggaran;
				}
				$x.='
							<tr>
								<td colspan="3" style="text-align:right;">GRAND TOTAL</td>
								<td style="text-align:right;">'.number_format($total,0).'</td>
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
			<style>
			p.small {
			  line-height: 0.1;
			}

			p.big {
			  line-height: 0.4;
			}

			hr.style2 {
				border-top: 3px double #8c8b8b;
			}
			@media print {
			input.noPrint { display: none; }
			}
			@page
					{
						size: auto;   /* auto is the initial value */
						margin: 0mm;  /* this affects the margin in the printer settings */
					}
			</style>
			<table cellpadding=0 cellspacing=0 width="100%" >
				<tr>
					<td width="20%">
						<img align="center" width="120" height="120" src="'.base_url().'assets/img/kkp3.png" alt="test alt attribute" border="0" />		
					</td>
					<td><b>
						<p class = "small" align=center style="text-align:center"> <span style="font-size:14.0pt;color:#0000E1">LAPORAN ANGGARAN BBM</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">DIREKTORAT PEMANTAUAN DAN ARMADA</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">TAHUN ANGGARAN '.date('Y').'</span></p>
						<p class = "small" align=right style="text-align:right">   <span lang=SV style="color:#0000E1">Dalam Rupiah</span></p>
						</b>
					</td>
				</tr>
				<tr>
				</tr>
			</table>
			<br/>
			<hr style = "border-top: 3px double #8c8b8b;" />
			<table id="data_tables" border=1 cellspacing=0 cellpadding=5 width = "100%"" >
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
								<td style="text-align:right;">'.number_format($list->anggaran_awal,0).'</td>
								<td style="text-align:right;">'.number_format($list->anggaran,0).'</td>
								<td style="text-align:right;">'.number_format($list->perubahan,0).'</td>								
							</tr>
				';
				$i++;
				$totalawal+=$list->anggaran_awal;
				$total+=$list->anggaran;
				$rubah+=$list->perubahan;				}
				$x.='
							<tr>
								<td colspan="3" style="text-align:right;">GRAND TOTAL</td>
								<td style="text-align:right;">'.number_format($totalawal,0).'</td>
								<td style="text-align:right;">'.number_format($total,0).'</td>
								<td style="text-align:right;">'.number_format($rubah,0).'</td>
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
				
			<script>
				window.close();
			</script>
			';	
		}	
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=export-ang1.xls");

		echo $x;	}
	
	function samples($code, $tahun, $tgl_awal, $tgl_akhir){
    	$ci = & get_instance();
		if($code==0){
    		$kcode = "";
		}else{
		    $kcode = "AND code = '".$code."'";
		}
    	$uptss = $ci->db->query("SELECT * FROM m_upt WHERE 1=1 ".$kcode." ORDER BY m_upt_id");
		$jumz = $uptss->num_rows();
		$aa = 0;
		$sanggarans = 0;
		$stagihans = 0;
		foreach($uptss->result() as $uptssls){
    		$prbhanketabel = $ci->db->query("SELECT MAX(perubahan_ke) AS maks FROM bbm_anggaran WHERE m_upt_code = '".$uptssls->code."' AND periode = '".$tahun."'")->row_array();
    		$qtabel = $ci->db->query("
     		SELECT m_upt.code, m_upt.nama,anggaran,
        		(
            		SELECT SUM(tagihan) FROM ( SELECT STR_TO_DATE(tanggal_input,'%Y-%m-%d') AS tanggal_input, 0 AS tagihan
            		FROM bbm_anggaran WHERE m_upt_code = '".$uptssls->code."' AND periode = '".$tahun."' AND perubahan_ke = '".$prbhanketabel['maks']."'
            		UNION ALL
            		SELECT tanggal_invoice AS tanggal_input,total AS tagihan FROM bbm_tagihan
            		WHERE m_upt_code = '".$uptssls->code."' AND statustagihan = 1 AND tanggal_invoice BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."' ) as tagihan
        		) AS tagihan

        		FROM `bbm_anggaran` 
        		JOIN m_upt ON m_upt.code = bbm_anggaran.m_upt_code
        		WHERE m_upt_code = '".$uptssls->code."' AND statusanggaran = '1' AND periode = '".$tahun."' AND perubahan_ke = '".$prbhanketabel['maks']."'")->row_array();



        		if($uptssls->code == ''){
            		$kond = "";
        		}else{
            		$kond = "AND m_upt_code = '".$qtabel['code']."' ";
        		}
        		$sqla2 = $ci->db->query("SELECT * FROM bbm_anggaran_upt WHERE YEAR(tanggal_trans) = '".$tahun."' ".$kond."");
        		$jm2 = $sqla2->num_rows();
        		if($jm2 > 0){
            		$anggarans2 = 0;
            		$anggarans2 = $qtabel['anggaran'];
            		foreach($sqla2->result() as $lsty2){
                		$anggarans2 += $lsty2->nominal;
            		}

        		}else{
            		$anggarans2 = $qtabel['anggaran'];
        		}

        		$sanggarans += $anggarans2;
        		$stagihans += $qtabel['tagihan'];

    		}
    	$data = array();
    	$data['anggaran'] = $sanggarans;
    	$data['tagihan'] = $stagihans;
    	return $data;
    }

	function showdtotalpake($tgl_awal, $tgl_akhir){
		$ci = & get_instance();
		$code = $ci->session->userdata('m_upt_code');
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
		/*$prbhanke = $ci->db->query("SELECT MAX(perubahan_ke) AS maks FROM bbm_anggaran WHERE periode = '".$tahun."'")->row_array();
		
		$ss = "SELECT m_upt.code, m_upt.nama, if(statusanggaran = '1',anggaran,0) as anggaran,
								(
									SELECT IF(SUM(total) IS NULL, '0', SUM(total)) FROM bbm_tagihan 
									WHERE m_upt_code = bbm_anggaran.m_upt_code AND statustagihan = '1'  AND tanggal_invoice BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."'
									
								) AS tagihan

								FROM `bbm_anggaran` 
								LEFT JOIN m_upt ON m_upt.`code` = bbm_anggaran.m_upt_code ";
		if($code==0) {
			$ss .=	"WHERE ";
		}else {
			$ss .=	"WHERE m_upt.code = ".$code." and ";
		}
		$ss .= "periode = '".$tahun."' AND perubahan_ke = '".$prbhanke['maks']."' ";		*/
		
		if($code==0) {
			$upt =	" ";
		}else {
			$upt =	"AND code = ".$code."  ";
		}
		$ss = "SELECT * FROM m_upt WHERE 1=1 ".$upt." ";
		//var_dump($ss);die;
		$sql = $ci->db->query($ss);
		$jum = $sql->num_rows();
		$x = '
			<h2 style="text-align: center;color: black;">LAPORAN PENGGUNAAN ANGGARAN DAN TAGIHAN BBM</h2>
			<h2 style="text-align: center;color: black;">PERIODE : '.$priodeaw.' SAMPAI DENGAN '.$priodeak.'</h2>
			<table id="data_tables" class="table3 table3-striped table3-bordered table3-hover" >';
		$x = '
			<style>
			p.small {
			  line-height: 0.1;
			}

			p.big {
			  line-height: 0.4;
			}

			hr.style2 {
				border-top: 3px double #8c8b8b;
			}
			@media print {
			input.noPrint { display: none; }
			}
			@page
					{
						size: auto;   /* auto is the initial value */
						margin: 0mm;  /* this affects the margin in the printer settings */
					}
			</style>
			<table cellpadding=0 cellspacing=0 width="100%" >
				<tr>
					<td width="20%">
						<img align="center" width="120" height="120" src="'.base_url().'assets/img/kkp3.png" alt="test alt attribute" border="0" />		
					</td>
					<td><b>
						<p class = "small" align=center style="text-align:center"> <span style="font-size:14.0pt;color:#0000E1">LAPORAN PENGGUNAAN ANGGARAN DAN TAGIHAN BBM</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">DIREKTORAT PEMANTAUAN DAN ARMADA</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">PERIODE : '.$priodeaw.' SAMPAI DENGAN '.$priodeak.'</span></p>
						</b>
					</td>
				</tr>
				<tr>
				</tr>
			</table>
			<br/>
			<hr style = "border-top: 3px double #8c8b8b;" />
			<table id="data_tables" border=1 cellspacing=0 cellpadding=5 width = "100%"" >	
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
			foreach($sql->result() AS $uptssls){
            	$prbhanketabel = $ci->db->query("SELECT MAX(perubahan_ke) AS maks FROM bbm_anggaran WHERE m_upt_code = '".$uptssls->code."' AND periode = '".$tahun."'")->row_array();
				$qtabel = $ci->db->query("
      						 SELECT m_upt.code, m_upt.nama,anggaran,
               					(
                       				SELECT SUM(tagihan) FROM ( SELECT STR_TO_DATE(tanggal_input,'%Y-%m-%d') AS tanggal_input, 0 AS tagihan
             						FROM bbm_anggaran WHERE m_upt_code = '".$uptssls->code."' AND periode = '".$tahun."' AND perubahan_ke = '".$prbhanketabel['maks']."'
									UNION ALL
                                    SELECT tanggal_invoice AS tanggal_input, total AS tagihan FROM bbm_tagihan
                                    WHERE m_upt_code = '".$uptssls->code."' AND statustagihan = 1 AND tanggal_invoice BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."' ) as tagihan
                                	) AS tagihan

                             FROM `bbm_anggaran` 
                             JOIN m_upt ON m_upt.code = bbm_anggaran.m_upt_code
                             WHERE m_upt_code = '".$uptssls->code."' AND statusanggaran = '1' AND periode = '".$tahun."' AND perubahan_ke = '".$prbhanketabel['maks']."'")->row_array();
                                        	
                                        	
                                        	
                             if($uptssls->code == ''){
								$kond = "";
							}else{
								$kond = "AND m_upt_code = '".$qtabel['code']."' ";
							}
							$sqla2 = $ci->db->query("SELECT * FROM bbm_anggaran_upt WHERE YEAR(tanggal_trans) = '".$tahun."' ".$kond."");
							$jm2 = $sqla2->num_rows();
							if($jm2 > 0){
                            	$anggarans2 = 0;
                            	$anggarans2 = $qtabel['anggaran'];
								foreach($sqla2->result() as $lsty2){
									$anggarans2 += $lsty2->nominal;
								}
                                       	
							}else{
								$anggarans2 = $qtabel['anggaran'];
							}
           					$sisa_anggaran = $anggarans2 - $qtabel['tagihan'];
                          	if($qtabel['tagihan']<=0 or $anggarans2<=0){
								$persentase = 0;
                            } else {	
								$persentase = ($qtabel['tagihan'] / $anggarans2) * 100;
							}
            
            				
           					if($uptssls->code == 000){
                            	$utama = $this->samples($uptssls->code, $tahun, $tgl_awal, $tgl_akhir);
                            	$sanggarans = $utama['anggaran'];
                            	$stagihans = $utama['tagihan'];
                            	$anggarans2 = $sanggarans;
                            	$tagihan = $stagihans;
                            	$sisa_anggaran = $sanggarans - $tagihan;
                            	if($tagihan<=0 or $anggarans2<=0){
									$persentase = 0;
								} else {	
									$persentase = ($tagihan / $anggarans2) * 100;
								}
							}else{
                            	$anggarans2 = $anggarans2;
                                $tagihan = $qtabel['tagihan'];
                                $sisa_anggaran = $anggarans2 - $qtabel['tagihan'];
                                if($qtabel['tagihan']<=0 or $anggarans2<=0){
									$persentase = 0;
								} else {	
									$persentase = ($qtabel['tagihan'] / $anggarans2) * 100;
								}
							}
			$x.='
						<tr>
							<td>'.$i.'</td>
							<td>'.$qtabel['code'].'</td>
							<td>'.$qtabel['nama'].'</td>
							<td style="text-align:right;">'.number_format($anggarans2,0).'</td>
							<td style="text-align:right;">'.number_format($tagihan,0).'</td>
							<td style="text-align:right;">'.number_format($sisa_anggaran,0).'</td>
							<td style="text-align:right;">'.number_format($persentase,2,',','.').' %</td>
						</tr>
			';
			$i++;
			$totalang +=$anggarans2;
			$totaltag +=$tagihan;
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
							<td style="text-align:right;">'.number_format($totalang,0).'</td>
							<td style="text-align:right;">'.number_format($totaltag,0).'</td>
							<td style="text-align:right;">'.number_format($totalsan,0).'</td>
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
			<script>
				window.close();
			</script>
		
		';
		
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=export-ang2.xls");

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
								UNION ALL
								SELECT tanggal_invoice AS tanggal_input,no_invoice AS no_tagihan,0 AS bbm_anggaran,total AS bbm_tagihan FROM bbm_tagihan
								WHERE m_upt_code = '".$code."' AND statustagihan = 1 AND tanggal_invoice BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."' order by tanggal_input ");	
    
		/*$sql = $ci->db->query("SELECT STR_TO_DATE(tanggal_input,'%Y-%m-%d') AS tanggal_input,'' AS no_tagihan,IF(COUNT(anggaran) = 0 OR statusanggaran <> '1','0',anggaran) AS bbm_anggaran,0 AS bbm_tagihan,
								(SELECT upper(nama) as nama FROM m_upt WHERE CODE = '".$code."') AS nama
								FROM bbm_anggaran WHERE m_upt_code = '".$code."' AND periode = '".$tahun."' AND perubahan_ke = '".$prbhanke['maks']."'
								UNION
								SELECT tanggal_invoice AS tanggal_input,no_invoice AS no_tagihan,0 AS bbm_anggaran,total AS bbm_tagihan ,
								(SELECT  upper(nama) as nama FROM m_upt WHERE CODE = '".$code."') AS nama FROM bbm_tagihan
								WHERE m_upt_code = '".$code."' AND statustagihan = 1 AND tanggal_invoice BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."' order by tanggal_input ");*/
								
		$jum = $sql->num_rows();
			// <h2 style="text-align: center;color: black;"></h2>
		$namaupt= 'aaaaa';	
		if($jum > 0){
			foreach($sql->result() AS $list2){
				$namaupt = $list2->nama;
			}
		}	

		$x = '
			<style>
			p.small {
			  line-height: 0.1;
			}

			p.big {
			  line-height: 0.4;
			}

			hr.style2 {
				border-top: 3px double #8c8b8b;
			}
			@media print {
			input.noPrint { display: none; }
			}
			@page
					{
						size: auto;   /* auto is the initial value */
						margin: 0mm;  /* this affects the margin in the printer settings */
					}
			</style>
			<table cellpadding=0 cellspacing=0 width="100%" >
				<tr>
					<td width="20%">
						<img align="center" width="120" height="120" src="'.base_url().'assets/img/kkp3.png" alt="test alt attribute" border="0" />		
					</td>
					<td><b>
						<p class = "small" align=center style="text-align:center"> <span style="font-size:14.0pt;color:#0000E1">LAPORAN ANGGARAN DAN REALISASI UPT</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">'.$namaupt.'</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">PERIODE : '.$priodeaw.' SAMPAI DENGAN '.$priodeak.'</span></p>
						</b>
					</td>
				</tr>
				<tr>
				</tr>
			</table>
			<br/>
			<hr style = "border-top: 3px double #8c8b8b;" />
			<table id="data_tables" border=1 cellspacing=0 cellpadding=5 width = "100%"" >	
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
							<td style="text-align:right;">'.number_format($list->bbm_anggaran,0).'</td>
							<td style="text-align:right;">'.number_format($list->bbm_tagihan,0).'</td>
							<td style="text-align:right;">'.number_format($sisa_anggaran,0).'</td>
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
							<td style="text-align:right;">'.number_format($totalang,0).'</td>
							<td style="text-align:right;">'.number_format($totaltag,0).'</td>
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
				
			<script>
				window.close();
			</script>
		';
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=export-ang3.xls");		
		echo $x;
	}

	function showdtagihanupt($tahun,$codes){
		$ci = & get_instance();
		
		$m_code = $ci->session->userdata('m_upt_code');
		if($m_code == '000'){
			$code = $codes;
		}else{
			$code = $m_code;
		}
		

		/*$sql = $ci->db->query("SELECT tanggal_invoice,quantity,total,rata,IF(grouptgl=1,'01-10',IF(grouptgl=2,'11-20',IF(grouptgl=3,CONCAT('21-',DAY(LAST_DAY(tanggal_invoice))),''))) AS periode,tanggal_sppd 
					    FROM
						(SELECT tanggal_invoice,SUM(quantity) AS quantity,SUM(total) AS total,SUM(total)/SUM(quantity) AS rata,tanggal_invoice AS periode,tanggal_sppd ,
						IF(CEILING(DAY(tanggal_invoice)/10)>3,3,CEILING(DAY(tanggal_invoice)/10)) AS grouptgl
						FROM bbm_tagihan WHERE statustagihan = 1 and m_upt_code = '".$code."' AND YEAR(tanggal_invoice) = '".$tahun."' AND tanggal_sppd IS NOT NULL 
						GROUP BY MONTH(tanggal_invoice),grouptgl) csql ");
		*/				
						
		$sql = $ci->db->query("SELECT tanggal_invoice,tagihanke,quantity,total,total/quantity AS rata,tanggal_invoice AS periode,tanggal_sppd ,
						IF(CEILING(DAY(tanggal_invoice)/10)>3,3,CEILING(DAY(tanggal_invoice)/10)) AS grouptgl
						FROM bbm_tagihan WHERE statustagihan = 1 AND m_upt_code = '".$code."' AND YEAR(tanggal_invoice) = '".$tahun."' AND tanggal_sppd IS NOT NULL
						ORDER BY tanggal_invoice");
								
		$jum = $sql->num_rows();
		$x = '
			<style>
			p.small {
			  line-height: 0.1;
			}

			p.big {
			  line-height: 0.4;
			}

			hr.style2 {
				border-top: 3px double #8c8b8b;
			}
			@media print {
			input.noPrint { display: none; }
			}
			@page
					{
						size: auto;   /* auto is the initial value */
						margin: 0mm;  /* this affects the margin in the printer settings */
					}
			</style>
			<table cellpadding=0 cellspacing=0 width="100%" >
				<tr>
					<td width="20%">
						<img align="center" width="120" height="120" src="'.base_url().'assets/img/kkp3.png" alt="test alt attribute" border="0" />		
					</td>
					<td><b>
						<p class = "small" align=center style="text-align:center"> <span style="font-size:14.0pt;color:#0000E1">REALISASI TAGIHAN BBM KAPAL PENGAWAS</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">DIREKTORAT PEMANTAUAN DAN ARMADA</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">TAHUN ANGGARAN '.date('Y').'</span></p>
						</b>
					</td>
				</tr>
				<tr>
				</tr>
			</table>
			<br/>
			<hr style = "border-top: 3px double #8c8b8b;" />
			<table id="data_tables" border=1 cellspacing=0 cellpadding=5 width = "100%"" >
				<thead>
					<tr>
						<th style="text-align:center; vertical-align: middle " rowspan = "2">No</th>
						<th style="text-align:center; vertical-align: middle "rowspan = "2">Bulan</th>
						<th style="text-align:center" colspan = "3">Rincian Tagihan</th>
						<th style="text-align:center; vertical-align: middle " rowspan = "2"> Harga Rata - Rata (Rp.)</th>
						<th style="text-align:center;  vertical-align: middle" rowspan = "2">PERIODE</th>						
						<th style="text-align:center;  vertical-align: middle" rowspan = "2">Tgl SP2D</th>
					</tr>
					<tr>
						<th style="text-align:center" >Tagihan</th>
						<th style="text-align:center" >BBM (Ltr)</th>
						<th style="text-align:center" >Nominal (Rp.)</th>
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
				$mydate2 = '';				
			} else {
				$mydate = indo_date($list->tanggal_sppd);
				$mydate2 = indo_date($list->tanggal_invoice);
			}

			$x.='
						<tr>
							<td>'.$i.'</td>
							<td>'.get_bulan(date('m', strtotime($list->tanggal_invoice))).'</td>
							<td>'.$list->tagihanke.'</td>
							<td style="text-align:right">'.number_format($list->quantity,0).'</td>
							<td style="text-align:right">'.number_format($list->total,0).'</td>
							<td style="text-align:right">'.number_format($rata,2).'</td>
							<td>'.$mydate2.'
							<td>'.$mydate.'</td>
						</tr>
			';
			$i++;
			}
			$rata2 = $totalharga/$totalquantity;
			$x.='</tbody>
				 <tfoot>
						<tr>
							<td colspan = "3">REALISASI (s.d '.indo_date($list->tanggal_invoice).'</td>
							<td style="text-align:right">'.number_format($totalquantity,0).'</td>
							<td style="text-align:right">'.number_format($totalharga,0).'</td>
							<td style="text-align:right">'.number_format($rata2,2).'</td>
							<td></td>
							<td></td>
						</tr>
				 </tfoot>
				 ';
			
		}else{
			$x.='
						<tr>
							<td colspan="8" style="text-align: center;">- Data Kosong -</td>
						</tr>
			';
		}
		
		$x.='
			</table>
			
			<script>
				window.close();
			</script>
		';
		
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=export-ang4.xls");
		echo $x;
	}
	
	function showdtagihanuptRinci($x){
		$ci = & get_instance();
		
		
		
						
		$sql = $ci->db->query("SELECT TRIM(REPLACE(REPLACE(REPLACE(REPLACE(`no_tagihan`,' ',''),'\t',''),'\n',''),'\r','')) AS no_tagihan, YEAR(tanggal_invoice) AS tahun   FROM `bbm_tagihan` WHERE tagihan_id = ".$x." ");
		foreach($sql->result() AS $list){
			
			
		}
		
		$q = $ci->db->query(" 
				SELECT a.*, b.kapal_code FROM `bbm_transdetail` a
				LEFT JOIN bbm_kapaltrans b ON a.nomor_surat = b.nomor_surat
				WHERE TRIM(REPLACE(REPLACE(REPLACE(REPLACE(a.no_tagihan,' ',''),'\t',''),'\n',''),'\r','')) = '".$list->no_tagihan."';
		");
		
		// var_dump($list->no_tagihan);die();
		
		$x = '
			<style>
			p.small {
			  line-height: 0.1;
			}

			p.big {
			  line-height: 0.4;
			}

			hr.style2 {
				border-top: 3px double #8c8b8b;
			}
			@media print {
			input.noPrint { display: none; }
			}
			@page
					{
						size: auto;   /* auto is the initial value */
						margin: 0mm;  /* this affects the margin in the printer settings */
					}
			</style>
			<table cellpadding=0 cellspacing=0 width="100%" >
				<tr>
					<td width="20%">
						<img align="center" width="120" height="120" src="'.base_url().'assets/img/kkp3.png" alt="test alt attribute" border="0" />		
					</td>
					<td><b>
						<p class = "small" align=center style="text-align:center"> <span style="font-size:14.0pt;color:#0000E1">REALISASI TAGIHAN BBM KAPAL PENGAWAS</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">DIREKTORAT PEMANTAUAN DAN ARMADA</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">TAHUN ANGGARAN '.$list->tahun.'</span></p>
						</b>
					</td>
				</tr>
				<tr>
				</tr>
			</table>
			<br/>
			<hr style = "border-top: 3px double #8c8b8b;" />
			<table id="data_tables" border=1 cellspacing=0 cellpadding=5 width = "100%"" >
				<thead>
					<tr>
						<th style="text-align:center; vertical-align: middle ">No</th>
						<th style="text-align:center; vertical-align: middle ">Nama</th>
						<th style="text-align:center; vertical-align: middle ">No SO</th>
						<th style="text-align:center; vertical-align: middle ">No Do</th>
						<th style="text-align:center; vertical-align: middle ">Liter</th>
						<th style="text-align:center; vertical-align: middle ">Harga total</th>
						
					</tr>
				</thead>
				<tbody>';
		$i=1;
		$totalharga = 0;
		$volumeisi = 0;
		foreach($q->result() AS $r){
			$totalharga    += $r->harga_total;
			$volumeisi    += $r->volume_isi;
			
			$x.='
				<tr>
					<td>'.$i.'</td>
					<td>'.$r->kapal_code.'</td>
					<td>'.$r->no_so.'</td>
					<td>'.$r->no_do.'</td>
					<td>'.$r->volume_isi.'</td>
					<td style="text-align:right">'.number_format($r->harga_total,0).'</td>
				</tr> ';
			$i++;
		}

			$x.='</tbody>
				 <tfoot>
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td >'.number_format($volumeisi,2).'</td>
							<td >'.number_format($totalharga,2).'</td>
						</tr>
				 </tfoot>';
			
		
		$x.='
			</table>
			
			<script>
				window.close();
			</script>
		';
		
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=export-rinci.xls");
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
		    $jnamaupt = $ci->db->query("SELECT nama FROM m_upt WHERE CODE = '".$codes."'")->row_array();
		    $judul_namaupt = $jnamaupt['nama'];
		} else {
		   $judul_namaupt = '';
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
		SELECT kapal_code,tanggal_surat,nomor_surat,0 AS penerimaan,volume_pemakaian AS  penggunaan
		FROM bbm_kapaltrans  WHERE status_ba = '3' AND tanggal_surat >= '".$tgl_awal."' AND tanggal_surat <= '".$tgl_akhir."') trans 
		WHERE m_kapal.code_kapal=trans.kapal_code ".$kond." GROUP BY kapal_code;");
		
		$jml = $sql->num_rows();
		$x = '
			<p class = "small" align=center style="text-align:center"> <span style="font-size:14.0pt;color:#0000E1">LAPORAN TOTAL PENGGUNAAN DAN PENERIMAAN BBM</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">DIREKTORAT PEMANTAUAN DAN ARMADA</span></p>';
                		if($judul_namaupt != '') {
                		    $x .= '<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">'.$judul_namaupt.'</span></p>';
                		}
                		$x .= '
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">PERIODE : '.strtoupper(indo_date($tgl_awal)).' SAMPAI DENGAN '.strtoupper(indo_date($tgl_akhir)).'</span></p>
						<p class = "small" align=center style="text-align:right"> <span lang=SV style="color:#0000E1">(dalam satuan liter)</span></p>

			<table id="data_tables" class="table3 table3-striped table3-bordered table3-hover" >';
		
		$x = '
		<style>
			p.small {
			  line-height: 0.1;
			}

			p.big {
			  line-height: 0.4;
			}

			hr.style2 {
				border-top: 3px double #8c8b8b;
			}
			@media print {
			input.noPrint { display: none; }
			}
			@page
					{
						size: auto;   /* auto is the initial value */
						margin: 0mm;  /* this affects the margin in the printer settings */
					}
			</style>
		<table cellpadding=0 cellspacing=0 width="100%" >
				<tr>
					<td width="20%">
						<img align="center" width="120" height="120" src="'.base_url().'assets/img/kkp3.png" alt="test alt attribute" border="0" />		
					</td>
					<td><b>
						<p class = "small" align=center style="text-align:center"> <span style="font-size:14.0pt;color:#0000E1">LAPORAN TOTAL PENGGUNAAN DAN PENERIMAAN BBM</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">DIREKTORAT PEMANTAUAN DAN ARMADA</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">PERIODE : '.strtoupper(indo_date($tgl_awal)).' SAMPAI DENGAN '.strtoupper(indo_date($tgl_akhir)).'</span></p>
						<p class = "small" align=center style="text-align:right"> <span lang=SV style="color:#0000E1">(dalam satuan liter)</span></p>
						</b>
					</td>
				</tr>
				<tr>
				</tr>
			</table>
			<br/>
			<hr style = "border-top: 3px double #8c8b8b;" />
			<table id="data_tables2" border=1 cellspacing=0 cellpadding=5 width = "100%"" >
				<thead>
					<tr>
						<th>No</th>
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
    						<td>'.$list->nama_kapal.'</td>
    						<td style="text-align:right;">'.number_format($list->penerimaan,0) .' </td>
    						<td style="text-align:right;">'.number_format($list->penggunaan,0) .' </td>
    					</tr>
    		';
    		$i++;
    		$totalang +=$list->penerimaan;
    		$totaltag +=$list->penggunaan;
    		$totalsan +=$sisa_anggaran;
    		}
    		$x.='
    					<tr>
    						<td colspan="2" style="text-align:right;">GRAND TOTAL</td>
    						<td style="text-align:right;">'.number_format($totalang,0) .' </td>
    						<td style="text-align:right;">'.number_format($totaltag,0) .' </td>
    					</tr>
    		';
		}else{
		    $x.='
		        <tr>
		            <td colspan="5" style="text-align: center;">- Data Kosong -</td>
		        </tr>
		    ';
		}
		
		$x.='
		    	</tbody>
    		</table>
		
		<script>
				window.close();
		</script>
		';
		
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=export-bbm.xls");		
		echo $x;
	}
	
	function showdtotalpakaibbm2($tgl_awal, $tgl_akhir,$codes,$codekpl){
		$ci = & get_instance();
		$tgl_awal = date('Y-m-d', strtotime($tgl_awal));
		$tgl_akhir = date('Y-m-d', strtotime($tgl_akhir));
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
		SELECT kapal_code,tanggal_surat,nomor_surat,0 AS penerimaan,volume_pemakaian AS  penggunaan
		FROM bbm_kapaltrans  WHERE status_ba = '3' AND tanggal_surat >= '".$tgl_awal."' AND tanggal_surat <= '".$tgl_akhir."') trans 
		WHERE m_kapal.code_kapal=trans.kapal_code ".$kond." ORDER BY tanggal_surat,nomor_surat,kapal_code;");
		$jml = $sql->num_rows();
		$x = '
		<style>
			p.small {
			  line-height: 0.1;
			}

			p.big {
			  line-height: 0.4;
			}

			hr.style2 {
				border-top: 3px double #8c8b8b;
			}
			@media print {
			input.noPrint { display: none; }
			}
			@page
					{
						size: auto;   /* auto is the initial value */
						margin: 0mm;  /* this affects the margin in the printer settings */
					}
			</style>
		<table cellpadding=0 cellspacing=0 width="100%" >
				<tr>
					<td width="20%">
						<img align="center" width="120" height="120" src="'.base_url().'assets/img/kkp3.png" alt="test alt attribute" border="0" />		
					</td>
					<td><b>
						<p class = "small" align=center style="text-align:center"> <span style="font-size:14.0pt;color:#0000E1">LAPORAN DETAIL PENERIMAAN DAN PENGGUNAAN BBM</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">DIREKTORAT PEMANTAUAN DAN ARMADA</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">PERIODE : '.strtoupper(indo_date($tgl_awal)).' SAMPAI DENGAN '.strtoupper(indo_date($tgl_akhir)).'</span></p>
						</b>
					</td>
				</tr>
				<tr>
				</tr>
			</table>
			<br/>
			<hr style = "border-top: 3px double #8c8b8b;" />
			<table id="data_tables2" border=1 cellspacing=0 cellpadding=5 width = "100%"" >
				<thead>
					<tr>
						<th>No</th>
						<th>Tanggal BA</th>
						<th>Nomor BA</th>
						<th>Nama Kapal</th>
						<th>Total Penerimaan</br>(liter)</th>
						<th>Total Penggunaan</br>(liter)</th>
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
    						<td>'.$list->nama_kapal.'</td>
    						<td style="text-align:right;">'.number_format($list->penerimaan,0).' </td>
    						<td style="text-align:right;">'.number_format($list->penggunaan,0).' </td>
    					</tr>
    		';
    		$i++;
    		$totalang +=$list->penerimaan;
    		$totaltag +=$list->penggunaan;
    		$totalsan +=$sisa_anggaran;
    		}
    		$x.='
    					<tr>
    						<td colspan="4" style="text-align:right;">GRAND TOTAL</td>
    						<td style="text-align:right;">'.number_format($totalang,0).' </td>
    						<td style="text-align:right;">'.number_format($totaltag,0).' </td>
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
    		
    		<script>
				window.close();
			</script>
		';
		
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=export-bbm2.xls");

		echo $x;
	}
	
	function showdtotalpakaibbm3($tgl_awal, $tgl_akhir,$m_kapal_id,$m_kapal_nama){
		$ci = & get_instance();
		$tgl_awal = date('Y-m-d', strtotime($tgl_awal));
		$tgl_akhir = date('Y-m-d', strtotime($tgl_akhir));
		$tahun = date('Y',strtotime($tgl_awal));
		$prbhanke = $ci->db->query("SELECT nama_kapal FROM m_kapal WHERE m_kapal_id = '".$m_kapal_id."'");
		foreach($prbhanke->result() AS $list2){
			$nama_kapal = $list2->nama_kapal;
		}	
		$sql = $ci->db->query("SELECT   IF(status_ba=1,'BA Akhir Bulan',
										IF(status_ba=2,'BA Sisa Sebelum Pengisian',
										IF(status_ba=3,'BA Penggunaan BBM',
										IF(status_ba=4,'BA Pemeriksaan Sarana Pengisian',
										IF(status_ba=5,'BA Penerimaan BBM',
										IF(status_ba=6,'BA Sebelum Pelayaran',
										IF(status_ba=7,'BA Sesudah Pelayaran',
										IF(status_ba=8,'BA Penitipan BBM',
										IF(status_ba=9,'BA Pengembalian BBM',
										IF(status_ba=10,'BA Peminjaman BBM',
										IF(status_ba=11,'BA Penerimaan Pinjaman BBM',
										IF(status_ba=12,'BA Pengembalian Pinjamaan BBM',
										IF(status_ba=13,'BA Penerimaan Pengembalian BBM',
										IF(status_ba=14,'BA Pemberian Hibah BBM Antar Kapal Pengawas',
										IF(status_ba=15,'BA Penerimaan Hibah BBM Antar Kapal Pengawas',
										IF(status_ba=16,'BA Penerimaan Hibah BBM Insatansi Lain',
										IF(status_ba=17,'BA Penerimaan Hibah BBM',
										IF(status_ba=18,'BA Pemberian Hibah BBM Instansi Lain',
				''))))))))))))))))))	AS nama_ba,nomor_surat,tanggal_surat,CONCAT(jam_surat,' ',zona_waktu_surat) AS jam,lokasi_surat,
				volume_sebelum,volume_pengisian,volume_pemakaian,volume_sisa 
				FROM bbm_kapaltrans a,m_kapal b WHERE status_ba<>1 and a.kapal_code = b.code_kapal AND m_kapal_id = '".$m_kapal_id."' AND a.tanggal_surat BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."' ORDER BY tanggal_surat,nomor_surat,jam;");
		$jml = $sql->num_rows();
		$x = '
			<h2 style="text-align: center;color: black;"></h2>
			<h2 style="text-align: center;color: black;"></h2>
			<h2 style="text-align: center;color: black;"></h2>
			<h2 style="text-align: right;color: black;"><small> (Dalam satuan liter) </small></h2>

			<table id="data_tables" class="table3 table3-striped table3-bordered table3-hover" >';
		
		$x = '
		<style>
			p.small {
			  line-height: 0.1;
			}

			p.big {
			  line-height: 0.4;
			}

			hr.style2 {
				border-top: 3px double #8c8b8b;
			}
			@media print {
			input.noPrint { display: none; }
			}
			@page
					{
						size: auto;   /* auto is the initial value */
						margin: 0mm;  /* this affects the margin in the printer settings */
					}
			</style>
		<table cellpadding=0 cellspacing=0 width="100%" >
				<tr>
					<td width="20%">
						<img align="center" width="120" height="120" src="'.base_url().'assets/img/kkp3.png" alt="test alt attribute" border="0" />		
					</td>
					<td><b>
						<p class = "small" align=center style="text-align:center"> <span style="font-size:14.0pt;color:#0000E1">HISTORY PENERIMAAN DAN PENGGUNAAN BBM</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">NAMA KAPAL : '.strtoupper($nama_kapal).'</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">PERIODE : '.strtoupper(indo_date($tgl_awal)).' SAMPAI DENGAN '.strtoupper(indo_date($tgl_akhir)).'</span></p>
						<p class = "small" align=center style="text-align:right"> <span lang=SV style="color:#0000E1">(dalam satuan liter)</span></p>						
						</b>
					</td>
				</tr>
				<tr>
				</tr>
			</table>
			<br/>
			<hr style = "border-top: 3px double #8c8b8b;" />
			<table id="data_tables2" border=1 cellspacing=0 cellpadding=5 width = "100%"" >
				<thead>
					<tr>
						<th style="text-align: center;color: black;">No</th>
						<th >Nama BA</th>
						<th style="text-align: center;color: black;">Nomor BA</th>
						<th style="text-align: center;color: black;">Tanggal BA</th>
						<th style="text-align: center;color: black;">Jam BA</th>
						<th style="text-align: center;color: black;">Lokasi Surat</th>
						<th style="text-align: center;color: black;">BBM Sebelum </br>Pengisian</th>
						<th style="text-align: center;color: black;">BBM </br>Pengisian</th>
						<th style="text-align: center;color: black;">BBM </br>Pemakaian</th>
						<th style="text-align: center;color: black;">Sisa </br>BBM</th>
					</tr>
				</thead>
				<tbody>
				';
		if($jml > 0){
		    $i=1;
		$totalbbm1 = 0;
		$totalbbm2 = 0;
		$totalbbm3 = 0;
		$totalbbm4 = 0;
		$sisa_anggaran = 0;
		foreach($sql->result() AS $list){
    		$x.='
    					<tr>
    						<td>'.$i.'</td>
    						<td>'.$list->nama_ba.'</td>
    						<td>'.$list->nomor_surat.'</td>
    						<td>'.$list->tanggal_surat.'</td>
    						<td>'.$list->jam.'</td>
    						<td>'.$list->lokasi_surat.'</td>
    						<td style="text-align:right;">'.number_format($list->volume_sebelum,0).' </td>
    						<td style="text-align:right;">'.number_format($list->volume_pengisian,0).' </td>
    						<td style="text-align:right;">'.number_format($list->volume_pemakaian,0).' </td>
    						<td style="text-align:right;">'.number_format($list->volume_sisa,0).' </td>
    					</tr>
    		';
    		$i++;
    		$totalbbm1 +=$list->volume_sebelum;
    		$totalbbm2 +=$list->volume_pengisian;
			$totalbbm3 +=$list->volume_pemakaian;
    		$totalbbm4 +=$list->volume_sisa;			
    		}
    		/*$x.='
    					<tr>
    						<td colspan="6" style="text-align:right;">GRAND TOTAL</td>
    						<td style="text-align:right;"> </td>
    						<td style="text-align:right;">'.number_format($totalbbm2,0).' </td>
    						<td style="text-align:right;">'.number_format($totalbbm3,0).' </td>
    						<td style="text-align:right;"> </td>
    					</tr>
    		';*/
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
			<script>
				window.close();
			</script>
		';
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=export-bbm3.xls");
		
		echo $x;
	}

	function showdtotalpakaibbm4($tgl_awal, $tgl_akhir,$m_kapal_id,$m_kapal_nama){
		$ci = & get_instance();
		$tgl_awal = date('Y-m-d', strtotime($tgl_awal));
		$tgl_akhir = date('Y-m-d', strtotime($tgl_akhir));
		$tahun = date('Y',strtotime($tgl_awal));
		$prbhanke = $ci->db->query("SELECT nama_kapal FROM m_kapal WHERE m_kapal_id = '".$m_kapal_id."'");
		foreach($prbhanke->result() AS $list2){
			$nama_kapal = $list2->nama_kapal;
		}	
		$sql = $ci->db->query("SELECT IF(status_ba=1,'BA Akhir Bulan',IF(status_ba=2,'BA Sisa Sebelum Pengisian',IF(status_ba=3,'BA Penggunaan BBM',
				IF(status_ba=4,'BA Pemeriksaan Sarana Pengisian',IF(status_ba=5,'BA Penerimaan BBM',IF(status_ba=6,'BA Sebelum Pelayaran',
				IF(status_ba=7,'BA Sesudah Pelayaran','')))))))	AS nama_ba,nomor_surat,tanggal_surat,CONCAT(jam_surat,' ',zona_waktu_surat) AS jam,lokasi_surat,
				volume_sebelum,volume_pengisian,volume_pemakaian,volume_sisa 
				FROM bbm_kapaltrans a,m_kapal b 
				WHERE status_ba=1 and a.kapal_code = b.code_kapal AND m_kapal_id = '".$m_kapal_id."' AND tanggal_surat >= '".$tgl_awal."' AND tanggal_surat <= '".$tgl_akhir."' ORDER BY tanggal_surat,jam;");
		$jml = $sql->num_rows();
		$x = '
			<h2 style="text-align: center;color: black;"></h2>
			<h2 style="text-align: center;color: black;"></h2>
			<h2 style="text-align: center;color: black;"></h2>
			<h2 style="text-align: right;color: black;"><small> (Dalam satuan liter) </small></h2>
			<table id="data_tables" class="table3 table3-striped table3-bordered table3-hover" >';
		
		$x = '
		<style>
			p.small {
			  line-height: 0.1;
			}

			p.big {
			  line-height: 0.4;
			}

			hr.style2 {
				border-top: 3px double #8c8b8b;
			}
			@media print {
			input.noPrint { display: none; }
			}
			@page
					{
						size: auto;   /* auto is the initial value */
						margin: 0mm;  /* this affects the margin in the printer settings */
					}
			</style>
		<table cellpadding=0 cellspacing=0 width="100%" >
				<tr>
					<td width="20%">
						<img align="center" width="120" height="120" src="'.base_url().'assets/img/kkp3.png" alt="test alt attribute" border="0" />		
					</td>
					<td><b>
						<p class = "small" align=center style="text-align:center"> <span style="font-size:14.0pt;color:#0000E1">LAPORAN DETAIL AKHIR BULAN BBM</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">NAMA KAPAL : '.strtoupper($nama_kapal).'</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">PERIODE : '.strtoupper(indo_date($tgl_awal)).' SAMPAI DENGAN '.strtoupper(indo_date($tgl_akhir)).'</span></p>
						<p class = "small" align=center style="text-align:right"> <span lang=SV style="color:#0000E1">(dalam satuan liter)</span></p>						
						</b>
					</td>
				</tr>
				<tr>
				</tr>
			</table>
			<br/>
			<hr style = "border-top: 3px double #8c8b8b;" />
			<table id="data_tables2" border=1 cellspacing=0 cellpadding=5 width = "100%"" >
				<thead>
					<tr>
						<th style="text-align: center;color: black;">No</th>
						<th >Nama BA</th>
						<th style="text-align: center;color: black;">Nomor Surat</th>
						<th style="text-align: center;color: black;">Tanggal BA</th>
						<th style="text-align: center;color: black;">Jam BA</th>
						<th style="text-align: center;color: black;">Lokasi Surat</th>
						<th style="text-align: center;color: black;">BBM Sebelum </br>Pengisian</th>
						<th style="text-align: center;color: black;">BBM </br>Pengisian</th>
						<th style="text-align: center;color: black;">BBM </br>Pemakaian</th>
						<th style="text-align: center;color: black;">Sisa </br>BBM</th>
					</tr>
				</thead>
				<tbody>
				';
		if($jml > 0){
		    $i=1;
		$totalbbm1 = 0;
		$totalbbm2 = 0;
		$totalbbm3 = 0;
		$totalbbm4 = 0;
		$sisa_anggaran = 0;
		foreach($sql->result() AS $list){
    		$x.='
    					<tr>
    						<td>'.$i.'</td>
    						<td>'.$list->nama_ba.'</td>
    						<td>'.$list->nomor_surat.'</td>
    						<td>'.$list->tanggal_surat.'</td>
    						<td>'.$list->jam.'</td>
    						<td>'.$list->lokasi_surat.'</td>
    						<td style="text-align:right;">'.number_format($list->volume_sebelum,0).'</td>
    						<td style="text-align:right;">'.number_format($list->volume_pengisian,0).' </td>
    						<td style="text-align:right;">'.number_format($list->volume_pemakaian,0).' </td>
    						<td style="text-align:right;">'.number_format($list->volume_sisa,0).' </td>
    					</tr>
    		';
    		$i++;
    		$totalbbm1 +=$list->volume_sebelum;
    		$totalbbm2 +=$list->volume_pengisian;
			$totalbbm3 +=$list->volume_pemakaian;
    		$totalbbm4 +=$list->volume_sisa;			
    		}
    		$x.='
    					<tr>
    						<td colspan="6" style="text-align:right;">GRAND TOTAL</td>
    						<td style="text-align:right;"> </td>
    						<td style="text-align:right;">'.number_format($totalbbm2,0).' </td>
    						<td style="text-align:right;">'.number_format($totalbbm3,0).' </td>
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
			
			<script>
				window.close();
			</script>
		';
		
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=export-bbm4.xls");

		echo $x;
	}
	
	function showdlappenerimaanbbm($tgl_awal,$tgl_akhir,$codes,$codekpl){
		$ci = & get_instance();
		$tgl_awal = date('Y-m-d', strtotime($tgl_awal));
		$tgl_akhir = date('Y-m-d', strtotime($tgl_akhir));
		$tahun = date('Y',strtotime($tgl_awal));
		$kond ="";
		if($codes != '000' or $codes != '0'){
		    $kond ="AND m_kapal.m_upt_code = '".$codes."'";
		    $jnamaupt = $ci->db->query("SELECT nama FROM m_upt WHERE CODE = '".$codes."'")->row_array();
		    $judul_namaupt = $jnamaupt['nama'];
		} else {
		   $judul_namaupt = '';
		}
		if($codekpl != '0' or $codekpl != '000') {
		    $kond .=" AND m_kapal.m_kapal_id = '".$codekpl."' ";	
		    $jnamakapal = $ci->db->query("SELECT nama_kapal FROM m_kapal WHERE m_kapal_id = '".$codekpl."'")->row_array();
		    $judul_namakapal = $jnamakapal['nama_kapal'];
		}
		//$prbhanke = $ci->db->query("SELECT MAX(perubahan_ke) AS maks FROM bbm_anggaran WHERE statusanggaran = '1' AND periode = '".$tahun."'")->row_array();
		
		$sql = $ci->db->query("SELECT kapal_code,' ' AS patroli,tanggal_surat,a.nomor_surat,lokasi_surat,a.no_so,no_do,volume_isi,jml_do 
		FROM bbm_transdetail a,(SELECT bbm_kapaltrans.nomor_surat,kapal_code,' ' AS patroli,tanggal_surat,lokasi_surat,COUNT(no_do) AS jml_do,status_ba 
		FROM bbm_kapaltrans,bbm_transdetail 
		WHERE bbm_kapaltrans.nomor_surat=bbm_transdetail.nomor_surat AND status_ba = 5 AND volume_isi > 0 GROUP BY nomor_surat) b,m_kapal
		WHERE a.nomor_surat=b.nomor_surat 
		AND m_kapal.code_kapal = b.kapal_code
		AND status_ba = 5 AND volume_isi > 0 
		AND tanggal_surat >= '".$tgl_awal."' AND tanggal_surat <= '".$tgl_akhir."' ".$kond."
		ORDER BY kapal_code,tanggal_surat,nomor_surat;");
		//ORDER BY kapal_code,nomor_surat,tanggal_surat;");
		
		if($codekpl == 0){
			   $judul_namakapal = 'Semua Kapal';
		}
		$jml = $sql->num_rows();
		$x = '
			<style>
			p.small {
			  line-height: 0.1;
			}

			p.big {
			  line-height: 0.4;
			}

			hr.style2 {
				border-top: 3px double #8c8b8b;
			}
			@media print {
			input.noPrint { display: none; }
			}
			@page
					{
						size: auto;   /* auto is the initial value */
						margin: 0mm;  /* this affects the margin in the printer settings */
					}
			</style>
			<table cellpadding=0 cellspacing=0 width="100%" >
				<tr>
					<td width="20%">
						<img align="center" width="120" height="120" src="'.base_url().'assets/img/kkp3.png" alt="test alt attribute" border="0" />		
					</td>
					<td><b>
						<p class = "small" align=center style="text-align:center"> <span style="font-size:14.0pt;color:#0000E1">LAPORAN PENERIMAAN BBM</span></p>';
		                if($judul_namaupt != '') {
		                    $x .= '<p class = "small" align=center style="text-align:center"> <span style="font-size:14.0pt;color:#0000E1">'.$judul_namaupt.'</span></p>';
		                }
		                $x .= '
		                
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">PERIODE : '.strtoupper(indo_date($tgl_awal)).' SAMPAI DENGAN '.strtoupper(indo_date($tgl_akhir)).'</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">NAMA KAPAL : '.$judul_namakapal.'</span></p>
						<p class = "small" align=center style="text-align:right"> <span lang=SV style="color:#0000E1">(dalam satuan liter)</span></p>						
						</b>
					</td>
				</tr>
				<tr>
				</tr>
			</table>
			<br/>
			<hr style = "border-top: 3px double #8c8b8b;" />
			<table id="data_tables2" border=1 cellspacing=0 cellpadding=5 width = "100%"" >
				<thead>
					<tr>
						<th style="text-align: center;">No</th>
						<th style="text-align: center;">Kode Kapal</th>
						<th style="text-align: center;">Patroli</th>
						<th style="text-align: center;">Tanggal BA</th>
						<th style="text-align: center;">Nomor BA</th>
						<th style="text-align: center;">Lokasi</th>
						<th style="text-align: center;">Nomor SO</th>
						<th style="text-align: center;">Nomor DO</th>
						<th style="text-align: center;">Volume Isi
							</br> (Liter)
						</th>
					</tr>
				</thead>
				<tbody>
				';
		if($jml > 0){
		    $i=0;
			$ii = 1;

			$totalang = 0;
			$totaltag = 0;
			$totalsan = 0;
			$sisa_anggaran = 0;
			$xc = 1;
			$xno = '';
			foreach($sql->result() AS $list){
			
			if($xc == 1){$xno = $list->nomor_surat;}
			$xc++;
    		if($xno <> $list->nomor_surat){
					$i=0;
					$ii++;
					$xno = $list->nomor_surat;
			}
			$i++;
			
    		$x.='		<tr>';
			
			if($i==1){
				$x.=	   '<td rowspan="'.$list->jml_do.'">'.$ii.'
							</td>';
				$x.=	   '<td rowspan="'.$list->jml_do.'">'.$list->kapal_code.'</td>
    						<td rowspan="'.$list->jml_do.'">'.$xno.'</td>
    						<td rowspan="'.$list->jml_do.'">'.$list->tanggal_surat.'</td>
    						<td rowspan="'.$list->jml_do.'">'.$list->nomor_surat.'</td>
    						<td rowspan="'.$list->jml_do.'">'.$list->lokasi_surat.'</td>
    						<td rowspan="'.$list->jml_do.'">'.$list->no_so.'</td>';							
			}else{
				//$x.=	   '<td colspan = "7">
				//			</td>';
			}		
			$x.=		   '<td>'.$list->no_do.'</td>
    						<td style="text-align:right;">'.number_format($list->volume_isi,0) .' </td>
    					</tr>
    		';

    		$totalang +=$list->volume_isi;
    		}
    		$x.='
    					<tr>
    						<td colspan="8" style="text-align:right;">GRAND TOTAL</td>
    						<td style="text-align:right;">'.number_format($totalang,0) .' </td>
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
			
			<script>
				window.close();
			</script>
		';
		
		
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=export-bbm5.xls");

		echo $x;	
	    
	}
	
	function showdlapverifikasi($tgl_awal, $tgl_akhir,$codes,$codes2){
		$ci = & get_instance();
		$tgl_awal = date('Y-m-d', strtotime($tgl_awal));
		$tgl_akhir = date('Y-m-d', strtotime($tgl_akhir));
		$tahun = date('Y',strtotime($tgl_awal));
		$kond ="";
    	$m_upt = $ci->db->query("SELECT * FROM m_upt WHERE code = '".$codes."'")->row_array();
		if($codes != '000' or $codes != '0'){
		   $kond ="AND d.m_upt_code like '%".$codes."%'  ";
		}
		
		if($codes2 != ''){
		    $kond .=" AND replace(d.no_tagihan,'/','') = '".$codes2."'  ";
		}
			
		$sql = $ci->db->query("SELECT b.peruntukan,no_spt,nama_kapal,'PATROLI' AS untuk,a.no_invoice,tanggal_invoice,lokasi_surat,a.no_so,no_do,
		volume_isi,harga_total,tanggal_sebelum,volume_sebelum,tanggal_sebelum AS tanggal_pemakaian,volume_pemakaian,
		tanggal_sebelum AS tanggal_pemeriksaan,volume_sisa,IF(status_segel=1,'BAIK','RUSAK') AS status_segel,tanggal_surat,volume_isi,
		(SELECT COUNT(*) FROM bbm_transdetail WHERE no_tagihan = d.no_tagihan AND no_so = a.no_so GROUP BY no_tagihan,no_so) AS jml_do,d.no_tagihan
		FROM bbm_tagihan d
		LEFT JOIN bbm_transdetail a ON d.no_tagihan = a.no_tagihan
		LEFT JOIN bbm_kapaltrans b ON a.nomor_surat = b.nomor_surat
		LEFT JOIN m_kapal c ON b.kapal_code = c.code_kapal 
		WHERE statustagihan = 1 AND d.no_tagihan <> '' AND tanggal_surat >= '".$tgl_awal."' 
		and status_ba = 5 AND tanggal_surat <= '".$tgl_akhir."' ".$kond. "
		ORDER BY no_tagihan,no_invoice,tanggal_invoice;");
		$dtsek = $sql->row_array();
		$jml = $sql->num_rows();
		$x = '
			<style>
			p.small {
			  line-height: 0.1;
			}

			p.big {
			  line-height: 0.4;
			}

			hr.style2 {
				border-top: 3px double #8c8b8b;
			}
			@media print {
			input.noPrint { display: none; }
			}
			@page
					{
						size: auto;   /* auto is the initial value */
						margin: 0mm;  /* this affects the margin in the printer settings */
					}
			</style>
			<table cellpadding=0 cellspacing=0 width="100%" >
				<tr>
					<td width="20%">
						<img align="center" width="120" height="120" src="'.base_url().'assets/img/kkp3.png" alt="test alt attribute" border="0" />		
					</td>
					<td><b>
						<p class = "small" align=center style="text-align:center"> <span style="font-size:14.0pt;color:#0000E1">LAPORAN VERIFIKASI REALISASI</span></p>
						<p class = "small" align=center style="text-align:center"> <span style="font-size:14.0pt;color:#0000E1">'.strtoupper($m_upt['nama']).'</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">TANGGAL PENAGIHAN : '.strtoupper(indo_date($dtsek['tanggal_invoice'])).'</span></p>
						</b>
					</td>
				</tr>
				<tr>
				</tr>
			</table>
			<br/>
			<hr style = "border-top: 3px double #8c8b8b;" />
			<table id="data_tables2" border=1 cellspacing=0 cellpadding=5 width = "100%"" >
				<thead>
					<tr>
						<th rowspan = "2" style="text-align: center;vertical-align: middle;">No</th>
						<th rowspan = "2" style="text-align: center;vertical-align: middle;">Nama Kapal</th>
						<th rowspan = "2" style="text-align: center;vertical-align: middle;">Peruntukan</th>
						<th rowspan = "2" style="text-align: center;vertical-align: middle;">No. SPT</th>
						<th colspan = "6" style="text-align: center;vertical-align: middle;">Nota Pengambilan</th>
					</tr>
					<tr>
						<th style="text-align: center;vertical-align: middle;">Tanggal Invoice</th>
						<th style="text-align: center;vertical-align: middle;">Depot</th>
						<th style="text-align: center;vertical-align: middle;">Nomor SO</th>
						<th style="text-align: center;vertical-align: middle;">Nomor DO</th>
						<th style="text-align: center;vertical-align: middle;">Volume Isi
							</br> (Liter)
						</th>
						<th style="text-align: center;vertical-align: middle;">Tagihan
							</br> (Rp.)
						</th>
					</tr>
				</thead>
				<tbody>
				';
		if($jml > 0){
		    $i=0;
			$ii = 1;

			$totalang = 0;
			$totaltag = 0;
			$totalsan = 0;
			$sisa_anggaran = 0;
			$xc = 1;
			$xno = '';
			foreach($sql->result() AS $list){
			
			if($xc == 1){$xno = $list->no_tagihan;}
			$xc++;
    		if($xno <> $list->no_tagihan){
					$i=0;
					$ii++;
					$xno = $list->no_tagihan;
			}
			$i++;
			
    		$x.='		<tr>';
			
				$x.=	   '<td >'.$ii.'</td>';
				$x.=	   '<td >'.$list->nama_kapal.'</td>
    						<td >'.$list->peruntukan.'</td>
    						<td >'.$list->no_spt.'</td>';														
			$x.=		   '
    						<td>'.$list->tanggal_invoice.'</td>
    						<td>'.$list->lokasi_surat.'</td>
							<td>'.$list->no_so.'</td>
							<td>'.$list->no_do.'</td>
    						<td style="text-align:right;">'.number_format($list->volume_isi,0) .'</td>
    						<td style="text-align:right;">'.number_format($list->harga_total,0) .'</td>';
					
				
    		$x.=	   '</tr> ';

    		$totalang +=$list->volume_isi;
            $totaltag +=$list->harga_total; 
    		}
    		$x.='
    					<tr>
    						<td colspan="8" style="text-align:right;">GRAND TOTAL</td>
    						<td style="text-align:right;">'.number_format($totalang,0) .' </td>
                            <td style="text-align:right;">'.number_format($totaltag,0,',','.') .' </td>
    					</tr>
    		';
		}else{
		    $x.='
		        <tr>
		            <td colspan="10" style="text-align: center;vertical-align: middle;">- Data Kosong -</td>
		        </tr>
		    ';
		}
		$x.='
					</tbody>
				</table>
				
			<script>
				window.close();
			</script>
		';
		
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=export-verifikasi.xls");

		echo $x;	
	}
	
	function showdlapverifikasi2($tgl_awal,$tgl_akhir,$codes,$codes2){
		$ci = & get_instance();
		$tgl_awal = date('Y-m-d', strtotime($tgl_awal));
		$tgl_akhir = date('Y-m-d', strtotime($tgl_akhir));
		$tahun = date('Y',strtotime($tgl_awal));
		$kond ="";
    	$m_upt = $ci->db->query("SELECT * FROM m_upt WHERE code = '".$codes."'")->row_array();
		if($codes != '000' or $codes != '0'){
		   $kond ="AND d.m_upt_code like '%".$codes."%'  ";
		}
		
		if($codes2 != ''){
		    $kond .=" AND replace(d.no_tagihan,'/','') = '".$codes2."'  ";
		}
			
		$sql = $ci->db->query("
		SELECT nama_kapal,no_invoice,tanggal_invoice,lokasi_surat,volume_isi,harga_total,harga_total*10/100 AS pbbkb,harga_total+(harga_total*10/100) AS total_harga
		FROM
		(SELECT nama_kapal,'PATROLI' AS untuk,a.no_invoice,tanggal_invoice,lokasi_surat,a.no_so,no_do,
		volume_isi,harga_total,tanggal_sebelum,volume_sebelum,tanggal_sebelum AS tanggal_pemakaian,volume_pemakaian,
		tanggal_sebelum AS tanggal_pemeriksaan,volume_sisa,IF(status_segel=1,'BAIK','RUSAK') AS status_segel,tanggal_surat,
		(SELECT COUNT(*) FROM bbm_transdetail WHERE no_tagihan = d.no_tagihan AND no_so = a.no_so GROUP BY no_tagihan,no_so) AS jml_do,d.no_tagihan
		FROM bbm_tagihan d
		LEFT JOIN bbm_transdetail a ON d.no_tagihan = a.no_tagihan
		LEFT JOIN bbm_kapaltrans b ON a.nomor_surat = b.nomor_surat
		LEFT JOIN m_kapal c ON b.kapal_code = c.code_kapal 
		WHERE statustagihan = 1 AND d.no_tagihan <> '' AND tanggal_surat >= '".$tgl_awal."' 
		and status_ba = 5 AND tanggal_surat <= '".$tgl_akhir."' ".$kond. "
		ORDER BY no_tagihan,no_invoice,tanggal_invoice) art");
		
		$jml = $sql->num_rows();
		$x = '
			<style>
			p.small {
			  line-height: 0.1;
			}

			p.big {
			  line-height: 0.4;
			}

			hr.style2 {
				border-top: 3px double #8c8b8b;
			}
			@media print {
			input.noPrint { display: none; }
			}
			@page
					{
						size: auto;   /* auto is the initial value */
						margin: 0mm;  /* this affects the margin in the printer settings */
					}
			</style>
			<table cellpadding=0 cellspacing=0 width="100%" >
				<tr>
					<td width="20%">
						<img align="center" width="120" height="120" src="'.base_url().'assets/img/kkp3.png" alt="test alt attribute" border="0" />		
					</td>
					<td><b>
						<p class = "small" align=center style="text-align:center"> <span style="font-size:14.0pt;color:#0000E1">Berita Acara Pembayaran Tagihan</span></p>
						<p class = "small" align=center style="text-align:center"> <span style="font-size:14.0pt;color:#0000E1">'.$m_upt['nama'].'</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">Tanggal Periode : '.indo_date($tgl_awal).' sampai dengan '.indo_date($tgl_akhir).'</span></p>
						</b>
					</td>
				</tr>
				<tr>
				</tr>
			</table>
			<br/>
			<hr style = "border-top: 3px double #8c8b8b;" />
			<table id="data_tables2" border=1 cellspacing=0 cellpadding=5 width = "100%"" >
				<thead>
					<tr>
						<th  style="text-align: center;vertical-align: middle;">NO</th>
						<th  style="text-align: center;vertical-align: middle;">NO.INVOICE</th>
						<th  style="text-align: center;vertical-align: middle;">DEPOT</th>
                        <th  style="text-align: center;vertical-align: middle;">KAPAL</th>
						<th  style="text-align: center;vertical-align: middle;">VOLUME (liter)</th>
						<th  style="text-align: center;vertical-align: middle;">HARGA (Rp.)</th>
						<th  style="text-align: center;vertical-align: middle;">TOTAL (Rp.)</th>
					</tr>
				</thead>
				<tbody>
				';
		if($jml > 0){
		    $i=0;
			$ii = 1;

			$totalisi = 0;
			$totalharga1 = 0;
			$totalharga2 = 0;
			$totalharga3 = 0;
			
			foreach($sql->result() AS $list){
			
			$i++;
    		    $x.='		<tr>';
			
				$x.=	   '<td >'.$i.'</td>';
				$x.=	   '<td >'.$list->no_invoice.'</td>
    						<td >'.$list->lokasi_surat.'</td>														
                            <td >'.$list->nama_kapal.'</td>
    						<td style="text-align:right;">'.number_format($list->volume_isi,0,',','.') .'</td>
    						<td style="text-align:right;">'.number_format($list->harga_total,0,',','.') .'</td>
    						<td style="text-align:right;">'.number_format($list->harga_total,0,',','.') .'</td>';
    		$totalharga1 += $list->harga_total;
			$totalisi 	 += $list->volume_isi;
			$totalharga2 += $list->pbbkb;
			$totalharga3 += $list->total_harga;			
    		}
    		$x.='
    					<tr>
							<td colspan = "4">GRAND TOTAL</td>
    						<td style="text-align:right;">'.number_format($totalisi,0,',','.') .'</td>
    						<td style="text-align:right;">'.number_format($totalharga1,0,',','.') .'</td>
    						<td style="text-align:right;">'.number_format($totalharga1,0,',','.') .'</td>
    					</tr>
    		';
		}else{
		    $x.='
		        <tr>
		            <td colspan="8" style="text-align: center;vertical-align: middle;">- Data Kosong -</td>
		        </tr>
		    ';
		}
		
		$x.='
		    	</tbody>
    		</table>
			
			<script>
				window.close();
			</script>
		';
		
	    header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=export-verifikasi2.xls");	
		echo $x;
	}
	
	function showdinternal($tahun,$codes){
		$ci = & get_instance();
		
		$m_code = $ci->session->userdata('m_upt_code');
		if($m_code == '000'){
			$code = $codes;
		}else{
			$code = $m_code;
		}
		
					
						
		$sql = $ci->db->query("SELECT tanggal_invoice,tagihanke,quantity,total,total/quantity AS rata,tanggal_invoice AS periode,tanggal_sppd ,
						IF(CEILING(DAY(tanggal_invoice)/10)>3,3,CEILING(DAY(tanggal_invoice)/10)) AS grouptgl
						FROM bbm_tagihan WHERE statustagihan = 1 AND m_upt_code = '".$code."' AND YEAR(tanggal_invoice) = '".$tahun."' AND tanggal_sppd IS NOT NULL
						ORDER BY tanggal_invoice");
						
		$sql = $ci->db->query("SELECT '' AS nomor_surat,STR_TO_DATE(tanggal_input,'%Y-%m-%d') AS tanggal_trans,anggaran AS nominal,keterangan FROM bbm_anggaran WHERE periode = '".$tahun."' AND perubahan_ke = (SELECT MAX(perubahan_ke) FROM bbm_anggaran WHERE periode = '".$tahun."') 
								AND m_upt_code = '".$code."'
								UNION 
								SELECT nomor_surat,tanggal_trans,nominal*-1,keterangan FROM bbm_anggaran_upt WHERE YEAR(tanggal_trans) = '".$tahun."' AND m_upt_code = '".$code."'
								ORDER BY tanggal_trans");
								
		$jum = $sql->num_rows();
		$x = '

		<style>
			p.small {
			  line-height: 0.1;
			}

			p.big {
			  line-height: 0.4;
			}

			hr.style2 {
				border-top: 3px double #8c8b8b;
			}
			@media print {
			input.noPrint { display: none; }
			}
			@page
					{
						size: auto;   /* auto is the initial value */
						margin: 0mm;  /* this affects the margin in the printer settings */
					}
			</style>
			<table cellpadding=0 cellspacing=0 width="100%" >
				<tr>
					<td width="20%">
						<img align="center" width="120" height="120" src="'.base_url().'assets/img/kkp3.png" alt="test alt attribute" border="0" />		
					</td>
					<td><b>
						<p class = "small" align=center style="text-align:center"> <span style="font-size:14.0pt;color:#0000E1">LAPORAN TRANSAKSI PERUBAHAN ANGGARAN UPT</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">TAHUN ANGGARAN '.date('Y').'</span></p>
						</b>
					</td>
				</tr>
				<tr>
				</tr>
			</table>
			<br/>
			<hr style = "border-top: 3px double #8c8b8b;" />
			<table id="data_tables2" border=1 cellspacing=0 cellpadding=5 width = "100%"" >
				<thead>
					<tr>
						<th style="text-align:center; vertical-align: middle " >No</th>
						<th style="text-align:center; vertical-align: middle " >Nomor Surat</th>
						<th style="text-align:center" vertical-align: middle " >Tanggal</th>
						<th style="text-align:center; vertical-align: middle " >Nominal (Rp.)</th>
						<th style="text-align:center;  vertical-align: middle" >Keterangan</th>						
					</tr>
				</thead>
				<tbody>
				';
		if($jum > 0){
			$i=1;
		$totalquantity = 0;
		$totalharga = 0;
		foreach($sql->result() AS $list){
			$totalharga    += $list->nominal;
			if ($list->tanggal_trans == '0000-00-00') {
				$mydate = '';				
			} else {
				$mydate = indo_date($list->tanggal_trans);
			}

			$x.='
						<tr>
							<td>'.$i.'</td>
							<td>'.$list->nomor_surat.'</td>
							<td>'.$mydate.'</td>
							<td style="text-align:right">'.number_format($list->nominal,0).'</td>
							<td>'.$list->keterangan.'</td>
						</tr>
			';
			$i++;
			}
			$x.='</tbody>
				 <tfoot>
						<tr>
							<td colspan = "3">Total</td>
							<td style="text-align:right">'.number_format($totalharga,0).'</td>
							<td></td>
						</tr>
				 </tfoot>
				 ';
			
		}else{
			$x.='
						<tr>
							<td colspan="8" style="text-align: center;">- Data Kosong -</td>
						</tr>
			';
		}
		
		$x.='
					
				
			</table>
			<script>
				window.close();
			</script>			
		';

		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=perubahan_internal.xls");		
		echo $x;
	}
	
	function showpenitipanbbm($tgl_awal,$tgl_akhir,$codes){
		$ci = & get_instance();
		$tgl_awal = date('Y-m-d', strtotime($tgl_awal));
		$tgl_akhir = date('Y-m-d', strtotime($tgl_akhir));
		$kond ="";
		// if($codes != '000' or $codes != '0'){
		    // $kond ="AND m_kapal.m_upt_code = '".$codes."'";
		// }
		$sql = $ci->db->query("SELECT 
								 IF(status_ba=1,'BA Akhir Bulan',IF(status_ba=2,'BA Sisa Sebelum Pengisian',IF(status_ba=3,'BA Penggunaan BBM',
								 IF(status_ba=4,'BA Pemeriksaan Sarana Pengisian',IF(status_ba=5,'BA Penerimaan BBM',IF(status_ba=6,'BA Sebelum Pelayaran',
								 IF(status_ba=7,'BA Sesudah Pelayaran',IF(status_ba=8,'BA Penitipan BBM',''))))))))	AS nama_ba,
								 nomor_surat,
								 tanggal_surat,
								 jam_surat,
								 zona_waktu_surat,
								 lokasi_surat,
								 volume_sebelum,
								 volume_pengisian,
								 volume_pemakaian,
								 volume_sisa,
								 jabatan_staf_pangkalan,
								 nama_staf_pagkalan,
								 nip_staf,
								 nama_nahkoda,
								 nip_nahkoda,
								 nama_kkm,
								 nip_kkm,
								 tanggalinput,
								 link_modul_ba, 
								 penggunaan,
								 nama_penitip,
								 alamat_penitip,
								 alamat_penyedia_penitip
								 
							FROM `bbm_kapaltrans` WHERE status_ba = 8 AND tanggal_surat BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."' ".$kond." ");
		
		$jml = $sql->num_rows();
		$x = '
			<style>
				p.small {
				  line-height: 0.1;
				}

				p.big {
				  line-height: 0.4;
				}

				hr.style2 {
					border-top: 3px double #8c8b8b;
				}
				@media print {
				input.noPrint { display: none; }
				}
				@page
						{
							size: auto;   /* auto is the initial value */
							margin: 0mm;  /* this affects the margin in the printer settings */
						}
			</style>
			<h2 style="text-align: center;color: black;">LAPORAN PENITIPAN BBM</h2>
			<h2 style="text-align: center;color: black;">PERIODE : '.strtoupper(indo_date($tgl_awal)).' SAMPAI DENGAN '.strtoupper(indo_date($tgl_akhir)).'</h2>
			<h2 style="text-align: right;color: black;"><small> (Dalam satuan liter) </small></h2>

			<table id="data_tables" class="table3 table3-striped table3-bordered table3-hover" >
				<thead>
					<tr>
						<th>No</th>
						<th>Nama BA</th>
						<th>Nomor BA</th>
						<th>Tanggal BA</th>
						<th>Jam BA</th>
						<th>Lokasi Surat</th>
						<th>Nama Penitip</th>
						<th>Alamat Penitip</th>
						<th>Sisa BBM</th>
						<th>Penitipan BBM</th>
					</tr>
				</thead>
				<tbody>
				';
		if($jml > 0){
		    $i=1;
		$totalpeng = 0;
		$totalssa = 0;
		foreach($sql->result() AS $list){
    		$x.='
    					<tr>
    						<td>'.$i.'</td>
    						<td>'.$list->nama_ba.'</td>
    						<td>'.$list->nomor_surat.'</td>
    						<td>'.$list->tanggal_surat.'</td>
    						<td>'.$list->jam_surat.' '.$list->zona_waktu_surat.'</td>
    						<td>'.$list->lokasi_surat.'</td>
    						<td>'.$list->nama_penitip.'</td>
    						<td>'.$list->alamat_penitip.' </td>
    						<td style="text-align:right;">'.number_format($list->volume_sisa,0) .' </td>
    						<td style="text-align:right;">'.number_format($list->penggunaan,0).'</td>
    					</tr>
    		';
    		$i++;
			$totalpeng += $list->penggunaan;
			$totalssa += $list->volume_sisa;
    		}
    		$x.='
    					<tr>
    						<td colspan="8" style="text-align:right;">GRAND TOTAL</td>
    						<td style="text-align:right;">'.number_format($totalpeng,0) .' </td>
    						<td style="text-align:right;">'.number_format($totalssa,0) .' </td>
    					</tr>
    		';
		}else{
		    $x.='
		        <tr>
		            <td colspan="10" style="text-align: center;">- Data Kosong -</td>
		        </tr>
		    ';
		}
		
		$x.='
		    	</tbody>
    		</table>
			<script>
				
				window.close();
			</script>
		';
		
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=export-penitpanbbm.xls");
		echo $x;
		
	}
	
	function showpengembalianbbm($tgl_awal,$tgl_akhir,$codes){
		$ci = & get_instance();
		$tgl_awal = date('Y-m-d', strtotime($tgl_awal));
		$tgl_akhir = date('Y-m-d', strtotime($tgl_akhir));
		$kond ="";
		// if($codes != '000' or $codes != '0'){
		    // $kond ="AND m_kapal.m_upt_code = '".$codes."'";
		// }
		$sql = $ci->db->query("SELECT 
								 IF(status_ba=1,'BA Akhir Bulan',IF(status_ba=2,'BA Sisa Sebelum Pengisian',IF(status_ba=3,'BA Penggunaan BBM',
								 IF(status_ba=4,'BA Pemeriksaan Sarana Pengisian',IF(status_ba=5,'BA Penerimaan BBM',IF(status_ba=6,'BA Sebelum Pelayaran',
								 IF(status_ba=7,'BA Sesudah Pelayaran',IF(status_ba=8,'BA Penitipan BBM',IF(status_ba=9,'BA Pengembalian BBM','')))))))))	AS nama_ba,
								 nomor_surat,
								 tanggal_surat,
								 jam_surat,
								 zona_waktu_surat,
								 lokasi_surat,
								 volume_sebelum,
								 volume_pengisian,
								 volume_pemakaian,
								 volume_sisa,
								 jabatan_staf_pangkalan,
								 nama_staf_pagkalan,
								 nip_staf,
								 nama_nahkoda,
								 nip_nahkoda,
								 nama_kkm,
								 nip_kkm,
								 tanggalinput,
								 link_modul_ba, 
								 penggunaan,
								 nama_penitip,
								 alamat_penitip,
								 alamat_penyedia_penitip
								 
							FROM `bbm_kapaltrans` WHERE status_ba = 9 AND tanggal_surat BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."' ".$kond." ");
		
		$jml = $sql->num_rows();
		$x = '
			<style>
				p.small {
				  line-height: 0.1;
				}

				p.big {
				  line-height: 0.4;
				}

				hr.style2 {
					border-top: 3px double #8c8b8b;
				}
				@media print {
				input.noPrint { display: none; }
				}
				@page
						{
							size: auto;   /* auto is the initial value */
							margin: 0mm;  /* this affects the margin in the printer settings */
						}
			</style>
			<h2 style="text-align: center;color: black;">LAPORAN PENGEMBALIAN BBM</h2>
			<h2 style="text-align: center;color: black;">PERIODE : '.strtoupper(indo_date($tgl_awal)).' SAMPAI DENGAN '.strtoupper(indo_date($tgl_akhir)).'</h2>
			<h2 style="text-align: right;color: black;"><small> (Dalam satuan liter) </small></h2>

			<table id="data_tables" class="table3 table3-striped table3-bordered table3-hover" >
				<thead>
					<tr>
						<th>No</th>
						<th>Nama BA</th>
						<th>Nomor BA</th>
						<th>Tanggal BA</th>
						<th>Jam BA</th>
						<th>Lokasi Surat</th>
						<th>Nama Penitip</th>
						<th>Alamat Penitip</th>
						<th>Alamat Penyedia Penitipan</th>
						<th>Penitipan BBM</th>
					</tr>
				</thead>
				<tbody>
				';
		if($jml > 0){
		$i=1;
		$totalpeng = 0;
		foreach($sql->result() AS $list){
    		$x.='
    					<tr>
    						<td>'.$i.'</td>
    						<td>'.$list->nama_ba.'</td>
    						<td>'.$list->nomor_surat.'</td>
    						<td>'.$list->tanggal_surat.'</td>
    						<td>'.$list->jam_surat.' '.$list->zona_waktu_surat.'</td>
    						<td>'.$list->lokasi_surat.'</td>
    						<td>'.$list->nama_penitip.'</td>
    						<td>'.$list->alamat_penitip.' </td>
    						<td>'.$list->alamat_penyedia_penitip.' </td>
    						<td style="text-align:right;">'.number_format($list->penggunaan,0).'</td>
    					</tr>
    		';
    		$i++;
    		
			$totalpeng += $list->penggunaan;
    		}
    		$x.='
    					<tr>
    						<td colspan="9" style="text-align:right;">GRAND TOTAL</td>
    						<td style="text-align:right;">'.number_format($totalpeng,0) .' </td>
    					</tr>
    		';
		}else{
		    $x.='
		        <tr>
		            <td colspan="10" style="text-align: center;">- Data Kosong -</td>
		        </tr>
		    ';
		}
		
		$x.='
		    	</tbody>
    		</table>
			<script>
				
				window.close();
			</script>
		';
		
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=export-pengembalianbbm.xls");
		echo $x;
		
	}
	
	function prevpinjamanbbm_xl($tgl_awal,$tgl_akhir,$codes,$codekpl){
		$ci = & get_instance();
		$tgl_awal = date('Y-m-d', strtotime($tgl_awal));
		$tgl_akhir = date('Y-m-d', strtotime($tgl_akhir));
		$tahun = date('Y',strtotime($tgl_awal));
		$kond ="";
        $cek = false;
		if($codes != '000' or $codes != '0'){
		    $kond ="AND m_upt_code = '".$codes."'";
		    $jnamaupt = $ci->db->query("SELECT nama FROM m_upt WHERE CODE = '".$codes."'")->row_array();
		    $judul_namaupt = $jnamaupt['nama'];
		} else {
		   $judul_namaupt = '';
		}    
		if($codekpl != '0' or $codekpl != '000') {
		    $kond .=" AND m_kapal_id = '".$codekpl."' ";	
			$cek = true;			
		}
		
		
		if($cek){
			
			
			$sql = $ci->db->query("
						SELECT kapal_code AS nama_kapal ,tanggal_surat,a.nomor_surat, volume_pemakaian, kapal_code_temp, status_temp, sebab_temp from
							(SELECT kapal_code,tanggal_surat,a.nomor_surat, volume_pemakaian, kapal_code_temp, status_temp, sebab_temp FROM bbm_kapaltrans a WHERE status_ba = '10' AND tanggal_surat >= '".$tgl_awal."' AND tanggal_surat <= '".$tgl_akhir."' GROUP BY nomor_surat) a,
							(SELECT code_kapal, nama_kapal, m_kapal_id ,m_upt_code FROM m_kapal ) b
							WHERE a.kapal_code = b.code_kapal  ".$kond."  GROUP BY nomor_surat
							
						UNION
						
						SELECT kapal_code AS nama_kapal,tanggal_surat,a.nomor_surat, volume_pemakaian, kapal_code_temp, status_temp, sebab_temp from
							(SELECT kapal_code,tanggal_surat,a.nomor_surat, volume_pemakaian, kapal_code_temp, status_temp, sebab_temp FROM bbm_kapaltrans a WHERE status_ba = '10' AND tanggal_surat >= '".$tgl_awal."' AND tanggal_surat <= '".$tgl_akhir."' GROUP BY nomor_surat) a,
							(SELECT code_kapal, nama_kapal, m_kapal_id ,m_upt_code FROM m_kapal ) b
							WHERE a.kapal_code_temp = b.code_kapal  ".$kond."  GROUP BY nomor_surat
				");
		
		}else{
			
			$sql = $ci->db->query("
						SELECT kapal_code AS nama_kapal,tanggal_surat,a.nomor_surat, volume_pemakaian, kapal_code_temp, status_temp, sebab_temp from
							(SELECT kapal_code,tanggal_surat,a.nomor_surat, volume_pemakaian, kapal_code_temp, status_temp, sebab_temp FROM bbm_kapaltrans a WHERE status_ba = '10' AND tanggal_surat >= '".$tgl_awal."' AND tanggal_surat <= '".$tgl_akhir."' GROUP BY nomor_surat) a,
							(SELECT code_kapal, nama_kapal, m_kapal_id ,m_upt_code FROM m_kapal ) b
							WHERE a.kapal_code = b.code_kapal  ".$kond."  GROUP BY nomor_surat");
		
		}
		
		$jml = $sql->num_rows();
		$x = '
			<p class = "small" align=center style="text-align:center"> <span style="font-size:14.0pt;color:#0000E1">LAPORAN PINJAMAN BBM</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">DIREKTORAT PEMANTAUAN DAN ARMADA</span></p>';
                		if($judul_namaupt != '') {
                		    $x .= '<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">'.$judul_namaupt.'</span></p>';
                		}
                		$x .= '
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">PERIODE : '.strtoupper(indo_date($tgl_awal)).' SAMPAI DENGAN '.strtoupper(indo_date($tgl_akhir)).'</span></p>

			<table id="data_tables" class="table3 table3-striped table3-bordered table3-hover" >';
		
		$x = '
		<style>
			p.small {
			  line-height: 0.1;
			}

			p.big {
			  line-height: 0.4;
			}

			hr.style2 {
				border-top: 3px double #8c8b8b;
			}
			@media print {
			input.noPrint { display: none; }
			}
			@page
					{
						size: auto;   /* auto is the initial value */
						margin: 0mm;  /* this affects the margin in the printer settings */
					}
			</style>
		<table cellpadding=0 cellspacing=0 width="100%" >
				<tr>
					<td width="20%">
						<img align="center" width="120" height="120" src="'.base_url().'assets/img/kkp3.png" alt="test alt attribute" border="0" />		
					</td>
					<td><b>
						<p class = "small" align=center style="text-align:center"> <span style="font-size:14.0pt;color:#0000E1">LAPORAN PINJAMAN BBM</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">DIREKTORAT PEMANTAUAN DAN ARMADA</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">PERIODE : '.strtoupper(indo_date($tgl_awal)).' SAMPAI DENGAN '.strtoupper(indo_date($tgl_akhir)).'</span></p>
						
						</b>
					</td>
				</tr>
				<tr>
				</tr>
			</table>
			<br/>
			<hr style = "border-top: 3px double #8c8b8b;" />
			<table id="data_tables2" border=1 cellspacing=0 cellpadding=5 width = "100%"" >
				<thead>
					<tr>
						<th>No</th>
						<th>Nama Pemberi Pinjaman</th>
						<th>Nomer Surat</th>
						<th>Tanggal Surat</th>
						<th>Volume Peminjamaan</th>
						<th>Kapal Penerima Pinajaman</th>
						<th>Sebab Pinjaman</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
				';
		if($jml > 0){
		    $i=1;
		
		foreach($sql->result() AS $list){
			
			if($list->status_temp == "0"){
			
				$status = $list->kapal_code_temp.' Belum melakukan penerimaan pinjaman';
				$bg = "#ed8a8a";
				
			}else{
				$status = $list->kapal_code_temp." Sudah melakukan penerimaan pinjaman";
				$bg = "#94ed8a";
			}
			
    		$x.='
    					<tr>
    						<td>'.$i.'</td>
    						<td>'.$list->nama_kapal.'</td>
    						<td style="text-align:center;"> '.$list->nomor_surat.'</td>
    						<td style="text-align:center;"> '.$list->tanggal_surat.'</td>
    						<td style="text-align:center;"> '.$list->volume_pemakaian.'</td>
    						<td style="text-align:center;"> '.$list->kapal_code_temp.'</td>
    						<td style="text-align:center;"> '.$list->sebab_temp.'</td>
    						<td style="text-align:center;" bgcolor="'.$bg.'" > '.$status.'</td>
    					</tr>
    		';
    		$i++;
    		
    		}
    		// $x.='
    					// <tr>
    						// <td colspan="2" style="text-align:right;">GRAND TOTAL</td>
    						// <td style="text-align:right;">'.number_format($totalang,0) .' </td>
    						// <td style="text-align:right;">'.number_format($totaltag,0) .' </td>
    					// </tr>
    		// ';
		}else{
		    $x.='
		        <tr>
		            <td colspan="5" style="text-align: center;">- Data Kosong -</td>
		        </tr>
		    ';
		}
		
		$x.='
		    	</tbody>
    		</table>
		
		<script>
				window.close();
		</script>
		';
		
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=export-peminjamaan-bbm.xls");		
		echo $x;
	}
	
	function prevpengembalianpinjamanbbm($tgl_awal,$tgl_akhir,$codes,$codekpl){
		$ci = & get_instance();
		$tgl_awal = date('Y-m-d', strtotime($tgl_awal));
		$tgl_akhir = date('Y-m-d', strtotime($tgl_akhir));
		$tahun = date('Y',strtotime($tgl_awal));
		$kond ="";
        $cek = false;
		if($codes != '000' or $codes != '0'){
		    $kond ="AND m_upt_code = '".$codes."'";
		    $jnamaupt = $ci->db->query("SELECT nama FROM m_upt WHERE CODE = '".$codes."'")->row_array();
		    $judul_namaupt = $jnamaupt['nama'];
			
		} else {
		   $judul_namaupt = '';
		}    
		
		if($codekpl != '0' or $codekpl != '000') {
		    $kond .=" AND m_kapal_id = '".$codekpl."' ";	
			$cek = true;
		}
		
		if($cek){
			
			
			$sql = $ci->db->query("
						SELECT kapal_code AS nama_kapal ,tanggal_surat,a.nomor_surat, volume_sebelum, kapal_code_temp, status_temp, sebab_temp from
							(SELECT kapal_code,tanggal_surat,a.nomor_surat, volume_sebelum, kapal_code_temp, status_temp, sebab_temp FROM bbm_kapaltrans a WHERE status_ba = '12' AND tanggal_surat >= '".$tgl_awal."' AND tanggal_surat <= '".$tgl_akhir."' GROUP BY nomor_surat) a,
							(SELECT code_kapal, nama_kapal, m_kapal_id ,m_upt_code FROM m_kapal ) b
							WHERE a.kapal_code = b.code_kapal  ".$kond."  GROUP BY nomor_surat
							
						UNION
						
						SELECT kapal_code AS nama_kapal,tanggal_surat,a.nomor_surat, volume_sebelum, kapal_code_temp, status_temp, sebab_temp from
							(SELECT kapal_code,tanggal_surat,a.nomor_surat, volume_sebelum, kapal_code_temp, status_temp, sebab_temp FROM bbm_kapaltrans a WHERE status_ba = '12' AND tanggal_surat >= '".$tgl_awal."' AND tanggal_surat <= '".$tgl_akhir."' GROUP BY nomor_surat) a,
							(SELECT code_kapal, nama_kapal, m_kapal_id ,m_upt_code FROM m_kapal ) b
							WHERE a.kapal_code_temp = b.code_kapal  ".$kond."  GROUP BY nomor_surat
				");
		
		}else{
			
			$sql = $ci->db->query("
						SELECT kapal_code AS nama_kapal,tanggal_surat,a.nomor_surat, volume_sebelum, kapal_code_temp, status_temp, sebab_temp from
							(SELECT kapal_code,tanggal_surat,a.nomor_surat, volume_sebelum, kapal_code_temp, status_temp, sebab_temp FROM bbm_kapaltrans a WHERE status_ba = '12' AND tanggal_surat >= '".$tgl_awal."' AND tanggal_surat <= '".$tgl_akhir."' GROUP BY nomor_surat) a,
							(SELECT code_kapal, nama_kapal, m_kapal_id ,m_upt_code FROM m_kapal ) b
							WHERE a.kapal_code = b.code_kapal  ".$kond."  GROUP BY nomor_surat");
		
		}
		
		$jml = $sql->num_rows();
		$x = '
			<p class = "small" align=center style="text-align:center"> <span style="font-size:14.0pt;color:#0000E1">LAPORAN PENGEMBALIAN PINJAMAN BBM</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">DIREKTORAT PEMANTAUAN DAN ARMADA</span></p>';
                		if($judul_namaupt != '') {
                		    $x .= '<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">'.$judul_namaupt.'</span></p>';
                		}
                		$x .= '
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">PERIODE : '.strtoupper(indo_date($tgl_awal)).' SAMPAI DENGAN '.strtoupper(indo_date($tgl_akhir)).'</span></p>

			<table id="data_tables" class="table3 table3-striped table3-bordered table3-hover" >';
		
		$x = '
		<style>
			p.small {
			  line-height: 0.1;
			}

			p.big {
			  line-height: 0.4;
			}

			hr.style2 {
				border-top: 3px double #8c8b8b;
			}
			@media print {
			input.noPrint { display: none; }
			}
			@page
					{
						size: auto;   /* auto is the initial value */
						margin: 0mm;  /* this affects the margin in the printer settings */
					}
			</style>
		<table cellpadding=0 cellspacing=0 width="100%" >
				<tr>
					<td width="20%">
						<img align="center" width="120" height="120" src="'.base_url().'assets/img/kkp3.png" alt="test alt attribute" border="0" />		
					</td>
					<td><b>
						<p class = "small" align=center style="text-align:center"> <span style="font-size:14.0pt;color:#0000E1">LAPORAN PENGEMBALIAN BBM</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">DIREKTORAT PEMANTAUAN DAN ARMADA</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">PERIODE : '.strtoupper(indo_date($tgl_awal)).' SAMPAI DENGAN '.strtoupper(indo_date($tgl_akhir)).'</span></p>
						
						</b>
					</td>
				</tr>
				<tr>
				</tr>
			</table>
			<br/>
			<hr style = "border-top: 3px double #8c8b8b;" />
			<table id="data_tables2" border=1 cellspacing=0 cellpadding=5 width = "100%"" >
				<thead>
					<tr>
						<th>No</th>
						<th>Kapal Penerima Pinjaman</th>
						<th>Nomer Surat</th>
						<th>Tanggal Surat</th>
						<th>Volume Peminjamaan</th>
						<th>Kapal Pemberi Pinjaman</th>
						<th>Sebab Pinjaman</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
				';
		if($jml > 0){
		    $i=1;
		
		foreach($sql->result() AS $list){
			
			if($list->status_temp == "0"){
			
				$status = $list->kapal_code_temp.' Belum melakukan pegembalian pinjaman';
				$bg = "#ed8a8a";
				
			}else{
				$status = $list->kapal_code_temp." Sudah melakukan pegembalian pinjaman";
				$bg = "#94ed8a";
			}
			
    		$x.='
					<tr>
						<td>'.$i.'</td>
						<td>'.$list->nama_kapal.'</td>
						<td style="text-align:center;"> '.$list->nomor_surat.'</td>
						<td style="text-align:center;"> '.$list->tanggal_surat.'</td>
						<td style="text-align:center;"> '.$list->volume_sebelum.'</td>
						<td style="text-align:center;"> '.$list->kapal_code_temp.'</td>
						<td style="text-align:center;"> '.$list->sebab_temp.'</td>
						<td style="text-align:center;" bgcolor="'.$bg.'" > '.$status.'</td>
			</tr>
    		';
    		$i++;
    		
    		}
    		// $x.='
    					// <tr>
    						// <td colspan="2" style="text-align:right;">GRAND TOTAL</td>
    						// <td style="text-align:right;">'.number_format($totalang,0) .' </td>
    						// <td style="text-align:right;">'.number_format($totaltag,0) .' </td>
    					// </tr>
    		// ';
		}else{
		    $x.='
		        <tr>
		            <td colspan="5" style="text-align: center;">- Data Kosong -</td>
		        </tr>
		    ';
		}
		
		$x.='
		    	</tbody>
    		</table>
		
		<script>
				window.close();
		</script>
		';
		
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=export-pengembalian-peminjamaan-bbm.xls");		
		echo $x;
	}
	
	function prevpinjamanbelumkembalibbm($tgl_awal,$tgl_akhir,$codes,$codekpl){
		$ci = & get_instance();
		$tgl_awal = date('Y-m-d', strtotime($tgl_awal));
		$tgl_akhir = date('Y-m-d', strtotime($tgl_akhir));
		$tahun = date('Y',strtotime($tgl_awal));
		$kond ="";
        $cek = false;
		if($codes != '000' or $codes != '0'){
		    $kond ="AND m_upt_code = '".$codes."'";
		    $jnamaupt = $ci->db->query("SELECT nama FROM m_upt WHERE CODE = '".$codes."'")->row_array();
		    $judul_namaupt = $jnamaupt['nama'];
		} else {
		   $judul_namaupt = '';
		}    
		if($codekpl != '0' or $codekpl != '000') {
		    $kond .=" AND m_kapal_id = '".$codekpl."' ";		
			$cek = true;				
		}
		
		if($cek){
			
			
			$sql = $ci->db->query("
						SELECT kapal_code AS nama_kapal ,tanggal_surat,a.nomor_surat, volume_sebelum, kapal_code_temp, status_temp, sebab_temp from
							(SELECT kapal_code,tanggal_surat,a.nomor_surat, volume_sebelum, kapal_code_temp, status_temp, sebab_temp FROM bbm_kapaltrans a WHERE status_ba = '10' AND tanggal_surat >= '".$tgl_awal."' AND tanggal_surat <= '".$tgl_akhir."' GROUP BY nomor_surat) a,
							(SELECT code_kapal, nama_kapal, m_kapal_id ,m_upt_code FROM m_kapal ) b
							WHERE status_temp = '1' AND a.kapal_code = b.code_kapal  ".$kond."  GROUP BY nomor_surat
							
						UNION
						
						SELECT kapal_code AS nama_kapal,tanggal_surat,a.nomor_surat, volume_sebelum, kapal_code_temp, status_temp, sebab_temp from
							(SELECT kapal_code,tanggal_surat,a.nomor_surat, volume_sebelum, kapal_code_temp, status_temp, sebab_temp FROM bbm_kapaltrans a WHERE status_ba = '10' AND tanggal_surat >= '".$tgl_awal."' AND tanggal_surat <= '".$tgl_akhir."' GROUP BY nomor_surat) a,
							(SELECT code_kapal, nama_kapal, m_kapal_id ,m_upt_code FROM m_kapal ) b
							WHERE status_temp = '1' AND a.kapal_code_temp = b.code_kapal  ".$kond."  GROUP BY nomor_surat
				");
		
		}else{
			
			$sql = $ci->db->query("
						SELECT kapal_code AS nama_kapal,tanggal_surat,a.nomor_surat, volume_sebelum, kapal_code_temp, status_temp, sebab_temp from
							(SELECT kapal_code,tanggal_surat,a.nomor_surat, volume_sebelum, kapal_code_temp, status_temp, sebab_temp FROM bbm_kapaltrans a WHERE status_ba = '10' AND tanggal_surat >= '".$tgl_awal."' AND tanggal_surat <= '".$tgl_akhir."' GROUP BY nomor_surat) a,
							(SELECT code_kapal, nama_kapal, m_kapal_id ,m_upt_code FROM m_kapal ) b
							WHERE status_temp = '1' AND  a.kapal_code = b.code_kapal  ".$kond."  GROUP BY nomor_surat");
		
		}
		
		$jml = $sql->num_rows();
		$x = '
			<p class = "small" align=center style="text-align:center"> <span style="font-size:14.0pt;color:#0000E1">LAPORAN PINJAMAN BELUM KEMBALI BBM</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">DIREKTORAT PEMANTAUAN DAN ARMADA</span></p>';
                		if($judul_namaupt != '') {
                		    $x .= '<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">'.$judul_namaupt.'</span></p>';
                		}
                		$x .= '
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">PERIODE : '.strtoupper(indo_date($tgl_awal)).' SAMPAI DENGAN '.strtoupper(indo_date($tgl_akhir)).'</span></p>

			<table id="data_tables" class="table3 table3-striped table3-bordered table3-hover" >';
		
		$x = '
		<style>
			p.small {
			  line-height: 0.1;
			}

			p.big {
			  line-height: 0.4;
			}

			hr.style2 {
				border-top: 3px double #8c8b8b;
			}
			@media print {
			input.noPrint { display: none; }
			}
			@page
					{
						size: auto;   /* auto is the initial value */
						margin: 0mm;  /* this affects the margin in the printer settings */
					}
			</style>
		<table cellpadding=0 cellspacing=0 width="100%" >
				<tr>
					<td width="20%">
						<img align="center" width="120" height="120" src="'.base_url().'assets/img/kkp3.png" alt="test alt attribute" border="0" />		
					</td>
					<td><b>
						<p class = "small" align=center style="text-align:center"> <span style="font-size:14.0pt;color:#0000E1">LAPORAN PINJAMAN BELUM KEMBALI BBM</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">DIREKTORAT PEMANTAUAN DAN ARMADA</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">PERIODE : '.strtoupper(indo_date($tgl_awal)).' SAMPAI DENGAN '.strtoupper(indo_date($tgl_akhir)).'</span></p>
						
						</b>
					</td>
				</tr>
				<tr>
				</tr>
			</table>
			<br/>
			<hr style = "border-top: 3px double #8c8b8b;" />
			<table id="data_tables2" border=1 cellspacing=0 cellpadding=5 width = "100%"" >
				<thead>
					<tr>
						<th>No</th>
						<th>Nama Pemberi Pinjaman</th>
						<th>Nomer Surat</th>
						<th>Tanggal Surat</th>
						<th>Volume Peminjamaan</th>
						<th>Kapal Penerima Pinajaman</th>
						<th>Sebab Pinjaman</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
				';
		if($jml > 0){
		    $i=1;
		
		foreach($sql->result() AS $list){
			
			$status = $list->kapal_code_temp.' Belum melakukan pengembalian pinjaman';
			$bg = "#ed8a8a";
				
			
    		$x.='
    					<tr>
    						<td>'.$i.'</td>
    						<td>'.$list->nama_kapal.'</td>
    						<td style="text-align:center;"> '.$list->nomor_surat.'</td>
    						<td style="text-align:center;"> '.$list->tanggal_surat.'</td>
    						<td style="text-align:center;"> '.$list->volume_sebelum.'</td>
    						<td style="text-align:center;"> '.$list->kapal_code_temp.'</td>
    						<td style="text-align:center;"> '.$list->sebab_temp.'</td>
    						<td style="text-align:center;" bgcolor="'.$bg.'" > '.$status.'</td>
    					</tr>
    		';
    		$i++;
    		
    		}
    		// $x.='
    					// <tr>
    						// <td colspan="2" style="text-align:right;">GRAND TOTAL</td>
    						// <td style="text-align:right;">'.number_format($totalang,0) .' </td>
    						// <td style="text-align:right;">'.number_format($totaltag,0) .' </td>
    					// </tr>
    		// ';
		}else{
		    $x.='
		        <tr>
		            <td colspan="5" style="text-align: center;">- Data Kosong -</td>
		        </tr>
		    ';
		}
		
		$x.='
		    	</tbody>
    		</table>
		
		<script>
				window.close();
		</script>
		';
		
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=export-peminjamaan-belum-kembali-bbm.xls");		
		echo $x;
	}
	
	function prevhibahantarkapal_xl($tgl_awal,$tgl_akhir,$codes,$codekpl){
		$ci = & get_instance();
		$tgl_awal = date('Y-m-d', strtotime($tgl_awal));
		$tgl_akhir = date('Y-m-d', strtotime($tgl_akhir));
		$tahun = date('Y',strtotime($tgl_awal));
		$kond ="";
        $cek = false;
		if($codes != '000' or $codes != '0'){
		    $kond ="AND m_upt_code = '".$codes."'";
		    $jnamaupt = $ci->db->query("SELECT nama FROM m_upt WHERE CODE = '".$codes."'")->row_array();
		    $judul_namaupt = $jnamaupt['nama'];
		} else {
		   $judul_namaupt = '';
		}    
		if($codekpl != '0' or $codekpl != '000') {
		    $kond .=" AND m_kapal_id = '".$codekpl."' ";	
			$cek = true;			
		}
		
		
		if($cek){
			
			
			$sql = $ci->db->query("
						SELECT kapal_code AS nama_kapal ,tanggal_surat,a.nomor_surat, volume_sebelum, volume_pemakaian, kapal_code_temp, status_temp, sebab_temp from
							(SELECT kapal_code,tanggal_surat,a.nomor_surat, volume_sebelum, volume_pemakaian, kapal_code_temp, status_temp, sebab_temp FROM bbm_kapaltrans a WHERE status_ba = '14' AND tanggal_surat >= '".$tgl_awal."' AND tanggal_surat <= '".$tgl_akhir."' GROUP BY nomor_surat) a,
							(SELECT code_kapal, nama_kapal, m_kapal_id ,m_upt_code FROM m_kapal ) b
							WHERE a.kapal_code = b.code_kapal  ".$kond."  GROUP BY nomor_surat
							
						UNION
						
						SELECT kapal_code AS nama_kapal,tanggal_surat,a.nomor_surat, volume_sebelum,  volume_pemakaian, kapal_code_temp, status_temp, sebab_temp from
							(SELECT kapal_code,tanggal_surat,a.nomor_surat, volume_sebelum, volume_pemakaian, kapal_code_temp, status_temp, sebab_temp FROM bbm_kapaltrans a WHERE status_ba = '14' AND tanggal_surat >= '".$tgl_awal."' AND tanggal_surat <= '".$tgl_akhir."' GROUP BY nomor_surat) a,
							(SELECT code_kapal, nama_kapal, m_kapal_id ,m_upt_code FROM m_kapal ) b
							WHERE a.kapal_code_temp = b.code_kapal  ".$kond."  GROUP BY nomor_surat
				");
		
		}else{
			
			$sql = $ci->db->query("
						SELECT kapal_code AS nama_kapal,tanggal_surat,a.nomor_surat, volume_sebelum, volume_pemakaian, kapal_code_temp, status_temp, sebab_temp from
							(SELECT kapal_code,tanggal_surat,a.nomor_surat, volume_sebelum, volume_pemakaian, kapal_code_temp, status_temp, sebab_temp FROM bbm_kapaltrans a WHERE status_ba = '14' AND tanggal_surat >= '".$tgl_awal."' AND tanggal_surat <= '".$tgl_akhir."' GROUP BY nomor_surat) a,
							(SELECT code_kapal, nama_kapal, m_kapal_id ,m_upt_code FROM m_kapal ) b
							WHERE a.kapal_code = b.code_kapal  ".$kond."  GROUP BY nomor_surat");
		
		}
		
		$jml = $sql->num_rows();
		$x = '
			<p class = "small" align=center style="text-align:center"> <span style="font-size:14.0pt;color:#0000E1">LAPORAN HIBAH HIBAH ANATAR KAPAL</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">DIREKTORAT PEMANTAUAN DAN ARMADA</span></p>';
                		if($judul_namaupt != '') {
                		    $x .= '<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">'.$judul_namaupt.'</span></p>';
                		}
                		$x .= '
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">PERIODE : '.strtoupper(indo_date($tgl_awal)).' SAMPAI DENGAN '.strtoupper(indo_date($tgl_akhir)).'</span></p>

			<table id="data_tables" class="table3 table3-striped table3-bordered table3-hover" >';
		
		$x = '
		<style>
			p.small {
			  line-height: 0.1;
			}

			p.big {
			  line-height: 0.4;
			}

			hr.style2 {
				border-top: 3px double #8c8b8b;
			}
			@media print {
			input.noPrint { display: none; }
			}
			@page
					{
						size: auto;   /* auto is the initial value */
						margin: 0mm;  /* this affects the margin in the printer settings */
					}
			</style>
		<table cellpadding=0 cellspacing=0 width="100%" >
				<tr>
					<td width="20%">
						<img align="center" width="120" height="120" src="'.base_url().'assets/img/kkp3.png" alt="test alt attribute" border="0" />		
					</td>
					<td><b>
						<p class = "small" align=center style="text-align:center"> <span style="font-size:14.0pt;color:#0000E1">LAPORAN HIBAH ANTAR KAPAL</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">DIREKTORAT PEMANTAUAN DAN ARMADA</span></p>
						<p class = "small" align=center style="text-align:center"> <span lang=SV style="color:#0000E1">PERIODE : '.strtoupper(indo_date($tgl_awal)).' SAMPAI DENGAN '.strtoupper(indo_date($tgl_akhir)).'</span></p>
						
						</b>
					</td>
				</tr>
				<tr>
				</tr>
			</table>
			<br/>
			<hr style = "border-top: 3px double #8c8b8b;" />
			<table id="data_tables2" border=1 cellspacing=0 cellpadding=5 width = "100%"" >
				<thead>
					<tr>
						<th>No</th>
						<th>Nama Pemberi Hibah</th>
						<th>Nomer Surat</th>
						<th>Tanggal Surat</th>
						<th>Volume Hibah</th>
						<th>Kapal Penerima Hibah</th>
						<th>Sebab Hibah</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
				';
		if($jml > 0){
		    $i=1;
		
		foreach($sql->result() AS $list){
			
			if($list->status_temp == "0"){
			
				$status = $list->kapal_code_temp.' Belum melakukan penerimaan Hibah';
				$bg = "#ed8a8a";
				
			}else{
				$status = $list->kapal_code_temp." Sudah melakukan penerimaan Hibah";
				$bg = "#94ed8a";
			}
			
			
    		$x.='
    					<tr>
    						<td>'.$i.'</td>
    						<td>'.$list->nama_kapal.'</td>
    						<td style="text-align:center;"> '.$list->nomor_surat.'</td>
    						<td style="text-align:center;"> '.$list->tanggal_surat.'</td>
    						<td style="text-align:center;"> '.$list->volume_pemakaian.'</td>
    						<td style="text-align:center;"> '.$list->kapal_code_temp.'</td>
    						<td style="text-align:center;"> '.$list->sebab_temp.'</td>
    						<td style="text-align:center;" bgcolor="'.$bg.'" > '.$status.'</td>
    					</tr>
    		';
    		$i++;
    		
    		}
    		// $x.='
    					// <tr>
    						// <td colspan="2" style="text-align:right;">GRAND TOTAL</td>
    						// <td style="text-align:right;">'.number_format($totalang,0) .' </td>
    						// <td style="text-align:right;">'.number_format($totaltag,0) .' </td>
    					// </tr>
    		// ';
		}else{
		    $x.='
		        <tr>
		            <td colspan="5" style="text-align: center;">- Data Kosong -</td>
		        </tr>
		    ';
		}
		
		$x.='
		    	</tbody>
    		</table>
		
		<script>
				window.close();
		</script>
		';
		
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=export-hibah-bbm-antar-pengawas.xls");		
		echo $x;
	}

	

}	