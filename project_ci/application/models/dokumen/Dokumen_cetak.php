<?php

/**
 * Users Model
 *
 */
class Dokumen_cetak extends Abstract_model {	
	
    function __construct() {        
		
		
		parent::__construct();
		
		$this->load->library('pdf');
	}
	
	public function cetak_ba_sisa_sblm_pengisian($nomor_suratx, $filename) {
	   
		$nomor_surat = str_replace('_','/',$nomor_suratx);
		
		$q_result2 = "SELECT kapal_code,nama_kapal,nomor_surat,REPLACE(jam_surat,':','.') AS jam_surat,tanggal_surat, volume_sisa,
		TRIM(SUBSTRING(f_formattanggal(tanggal_surat),1,254)) AS ttanggal,
		nama_nahkoda,nip_nahkoda,nama_kkm,nip_kkm,nama_staf_pagkalan,nip_staf,nama_kapal,nama_upt,
		alamat1,alamat2,alamat3,kota,zona_waktu,lok_surat,jabatan_staf_pangkalan, an_staf, an_nakhoda, an_kkm
		FROM
		(SELECT a.*,b.nama_kapal,m_upt_code,nama AS nama_upt,
   		  IFNULL(alamat1,'') AS alamat1,IFNULL(alamat2,'') AS alamat2,ifnull(alamat3,'') AS alamat3, ifnull(kota,'') AS kota, a.zona_waktu_surat AS zona_waktu, a.lokasi_surat AS lok_surat
		  FROM bbm_kapaltrans a 
		  LEFT JOIN m_kapal b ON a.kapal_code = b.code_kapal LEFT JOIN m_upt c ON b.m_upt_code = c.code  
		  WHERE nomor_surat = '".$nomor_surat."') bbm_kapaltrans";
		 
		$coba = $this->db->query($q_result2);
		
		foreach($coba->result_array() AS $list){
			$nama_kapal 	= $list['nama_kapal'];
			$nomor_surat 	= $list['nomor_surat'];
			$jam_surat 		= $list['jam_surat'];
			$tanggal_surat 	= $list['tanggal_surat'];
			$volume_sisa 	= $list['volume_sisa'];
			$ttanggal 		= $list['ttanggal'];
			$nama_nahkoda 	= $list['nama_nahkoda'];
			$nip_nahkoda 	= $list['nip_nahkoda'];
			$nama_kkm 		= $list['nama_kkm'];
			$nip_kkm 		= $list['nip_kkm'];
			$nama_staf_pagkalan = $list['nama_staf_pagkalan'];
			$nip_staf 		= $list['nip_staf'];
			$nama_upt		= $list['nama_upt'];
			$alamat1		= $list['alamat1'];
			$alamat2		= $list['alamat2'];
			$alamat3		= $list['alamat3'];
			$lok_surat		= $list['lok_surat'];
			$zona_waktu		= $list['zona_waktu'];
			$jabatan_staf_pangkalan		= $list['jabatan_staf_pangkalan'];
		} 
		
		if($list['an_staf'] == 1){
			$an_staf = 'An. ';
		}else{
			$an_staf = '';
		}
		
		if($list['an_nakhoda'] == 1){
			$an_nakhoda = 'An. ';
		}else{
			$an_nakhoda = '';
		}
		
		if($list['an_kkm'] == 1){
			$an_kkm = 'An. ';
		}else{
			$an_kkm = '';
		}
		
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setPrintFooter(false);
        $pdf->setPrintHeader(false);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->AddPage('P','A4');
        $pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetFont('');
        
        
        $tbl = '<style type="text/css">
				hr.new5 {
					  border: 20px solid green;
					  border-radius: 5px;
					}
			</style>';
		// <-- Header
        $tbl .= '
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;" border ="0">
							<tr>
                            	
								<td width="17%" align="center" ><img align="center" width="120" height="120" src="'.$_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/assets/img/kkp3.png" border="0" /></td>
								<td width="82%" align="center" >
									<font size="15"><b>KEMENTERIAN KELAUTAN DAN PERIKANAN</b></font><br>
									<font size="17">DIREKTORAT JENDERAL PENGAWASAN</font><br>
									<font size="17">SUMBER DAYA KELAUTAN DAN PERIKANAN</font><br>
									<font size="12"><b><i>'.strtoupper($nama_upt).'</b></i></font><br>
									<font size="10">'.$alamat1.'</font><br>
									<font size="10">'.$alamat2.'</font><br>
									<font size="10">'.$alamat3.'</font>
								</td>
							</tr>
				</table> ';
			

		$style = array('width' => 1.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 58, 200, 58, $style);
		$style2 = array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 60, 200, 60, $style2);
		// Header -->
		
		
		// <-- ISI 
		
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">
                
							<tr>
								<td width="100%" align="center" ><font size="12"><b><u>BERITA ACARA SISA BBM SEBELUM PENGISIAN</b></u></font></td>
							</tr>
							<tr>
								<td width="100%" align="center" ><b>Nomor : '.$nomor_surat.'</b><br></td>
					
							</tr>
							
							<tr>
								<td width="100%" align="justify" >
								Pada hari ini '.$ttanggal.' pukul '.$jam_surat.' '.$zona_waktu.', bertempat di '.strtoupper($lok_surat).', kami yang bertanda tangan
								dibawah ini : </td>
							</tr>
							<tr>
								<td></td>
							</tr>
							
							<tr>
								<td width="20%" align="justify" >Nama/Jabatan</td>
								<td width="2%" align="center" >:</td>
								<td width="3%" align="center" >1.</td>
								<td width="auto" align="justify" >'.$nama_nahkoda.' / Nakhoda Kapal Pengawas '.$nama_kapal.'</td>
							</tr>
							<tr>
								<td width="20%" align="justify" ></td>
								<td width="2%" align="center" >:</td>
								<td width="3%" align="center" >2.</td>
								<td width="auto" align="justify" >'.$nama_kkm.' / KKM Kapal Pengawas '.$nama_kapal.'</td>
							</tr>
							
							<tr>
								<td></td>
							</tr>
							
							<tr>
								<td width="20%" align="justify" >Alamat</td>
								<td width="2%" align="center" >:</td>
								<td width="auto" align="justify" >'.$alamat1.'</td>
							</tr>
							
							<tr>
								<td></td>
							</tr>
							
							<tr>
								<td width="100%" align="justify" >
								Menyatakan bahwa telah melakukan pengukuran sisa bbm sebelum pengisian dengan rincian sebagai berikut :</td>
							</tr>
				</table>';
		
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="1">
							
							<tr>
								<td width="40%" align="center" >Sisa BBM Sebelum Pengisian</td>
								<td width="3%" align="center" >=</td>
								<td width="40%" align="center" >'.number_format($volume_sisa).'</td>
								<td width="auto" align="center" >Liter</td>
							</tr>
				</table>';
				
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">
							
							<tr>
								<td width="100%" align="justify" >
												Demikian Berita Acara Sisa BBM Sebelum Pengisian ini dibuat dengan sebenar – benarnya untuk dapat dipergunakan sebagaimana mestinya.</td>
							</tr>
				</table>';
		
		
		
		// ISI -->  
		
		
		// <-- Fotter
		$tbl .= '<br><br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif; font-size:10px" border ="0">
							<tr>
								<td width="40%" align="center" >
									<b>'.$an_nakhoda.' Nakhoda KP. '.$nama_kapal.'</b><br><br><br><br><br>
						
									<b><u>'.$nama_nahkoda.'</u></b><br>
									<b>NIP. '.$nip_nahkoda.'</b>
								</td>
								<td width="20%" align="center" ></td>
								<td width="40%" align="center" >
									<b>'.$an_kkm.' KKM KP. '.$nama_kapal.'</b><br><br><br><br><br>
						
									<b><u>'.$nama_kkm.'</u></b><br>
									<b>NIP. '.$nip_kkm.'</b>
								</td>
							</tr>
							<tr>
								<td width="30%" align="center"></td>
								<td width="40%" align="center" >
									<b><br><br>Menyaksikan:</b><br>
									<b>'.$an_staf.' '.$jabatan_staf_pangkalan.'</b><br><br><br><br><br>
						
									<b><u>'.$nama_staf_pagkalan.'</u></b><br>
									<b>NIP. '.$nip_staf.'</b>
								</td>
								
								<td width="30%" align="center" ></td>
							</tr>
				</table> ';
		// Fotter -->
		
		
		$pdf->writeHTML($tbl, true, false, true, false, '');
        $pdf->Output($_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/dokumen/cetakan_ba/'.$filename.'.pdf', 'F');

	}
	
	public function cetak_ba_sblm_pelayaran($nomor_suratx, $filename) {
    	
		$nomor_surat = str_replace('_','/',$nomor_suratx);
		
		$q_result2 = "SELECT kapal_code,nama_kapal,nomor_surat,REPLACE(jam_surat,':','.') AS jam_surat,tanggal_surat, volume_sisa,
		TRIM(SUBSTRING(f_formattanggal(tanggal_surat),1,254)) AS ttanggal,
		TRIM(SUBSTRING(f_formattanggal(tanggal_surat),1,254)) AS ttanggal,nama_nahkoda,nip_nahkoda,nama_kkm,nip_kkm,nama_staf_pagkalan,nip_staf,nama_kapal,nama_upt,
		alamat1,alamat2,alamat3,kota, zona_waktu, lok_surat, jabatan_staf_pangkalan, an_staf, an_nakhoda, an_kkm
		FROM
		(SELECT a.*,b.nama_kapal,m_upt_code,nama AS nama_upt,
   		  IFNULL(alamat1,'') AS alamat1,IFNULL(alamat2,'') AS alamat2,ifnull(alamat3,'') AS alamat3, ifnull(kota,'') AS kota, a.zona_waktu_surat AS zona_waktu, a.lokasi_surat AS lok_surat 
		  FROM bbm_kapaltrans a 
		  LEFT JOIN m_kapal b ON a.kapal_code = b.code_kapal LEFT JOIN m_upt c ON b.m_upt_code = c.code  
		  WHERE nomor_surat = '".$nomor_surat."') bbm_kapaltrans";
		 
		// var_dump($q_result2);die();
		$coba = $this->db->query($q_result2);
		
		foreach($coba->result_array() AS $list){
			$nama_kapal 	= $list['nama_kapal'];
			$nomor_surat 	= $list['nomor_surat'];
			$jam_surat 		= $list['jam_surat'];
			$tanggal_surat 	= $list['tanggal_surat'];
			$volume_sisa 	= $list['volume_sisa'];
			$ttanggal 		= $list['ttanggal'];
			$nama_nahkoda 	= $list['nama_nahkoda'];
			$nip_nahkoda 	= $list['nip_nahkoda'];
			$nama_kkm 		= $list['nama_kkm'];
			$nip_kkm 		= $list['nip_kkm'];
			$nama_staf_pagkalan = $list['nama_staf_pagkalan'];
			$nip_staf 		= $list['nip_staf'];
			$nama_upt		= $list['nama_upt'];
			$alamat1		= $list['alamat1'];
			$alamat2		= $list['alamat2'];
			$alamat3		= $list['alamat3'];
			$lok_surat		    = $list['lok_surat'];
			$zona_waktu		    = $list['zona_waktu'];
			$jabatan_staf_pangkalan		    = $list['jabatan_staf_pangkalan'];
		} 
		
		if($list['an_staf'] == 1){
			$an_staf = 'An. ';
		}else{
			$an_staf = '';
		}
		
		if($list['an_nakhoda'] == 1){
			$an_nakhoda = 'An. ';
		}else{
			$an_nakhoda = '';
		}
		
		if($list['an_kkm'] == 1){
			$an_kkm = 'An. ';
		}else{
			$an_kkm = '';
		}
		
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setPrintFooter(false);
        $pdf->setPrintHeader(false);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->AddPage('P','A4');
        $pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetFont('');
        
        
        $tbl = '<style type="text/css">
				hr.new5 {
					  border: 20px solid green;
					  border-radius: 5px;
					}
			</style>';
		// <-- Header
        $tbl .= '
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;" border ="0">
							<tr>
								<td width="17%" align="center" ><img align="center" width="120" height="120" src="'.$_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/assets/img/kkp3.png" border="0" /></td>
								<td width="82%" align="center" >
									<font size="15"><b>KEMENTERIAN KELAUTAN DAN PERIKANAN</b></font><br>
									<font size="17">DIREKTORAT JENDERAL PENGAWASAN</font><br>
									<font size="17">SUMBER DAYA KELAUTAN DAN PERIKANAN</font><br>
									<font size="12"><b><i>'.strtoupper($nama_upt).'</b></i></font><br>
									<font size="10">'.$alamat1.'</font><br>
									<font size="10">'.$alamat2.'</font><br>
									<font size="10">'.$alamat3.'</font>
								</td>
							</tr>
				</table> ';
			

		$style = array('width' => 1.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 58, 200, 58, $style);
		$style2 = array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 60, 200, 60, $style2);
		// Header -->
		
		
		// <-- ISI 
		
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">
                
                            <tr>
							   
								<td width="100%" align="center" ><font size="12"><b><u>BERITA ACARA SISA BBM SEBELUM PELAYARAN</b></u></font></td>
								
							</tr>
							<tr>
								<td width="100%" align="center" ><b>Nomor : '.$nomor_surat.'</b><br></td>
					
							</tr>
							<tr>
								<td width="100%" align="justify" >
								Pada hari ini '.$ttanggal.' pukul '.$jam_surat.' '.$zona_waktu.', bertempat di '.strtoupper($lok_surat).', kami yang bertanda tangan
								dibawah ini : </td>
							</tr>
							<tr>
								<td></td>
							</tr>
							
							<tr>
								<td width="20%" align="justify" >Nama/Jabatan</td>
								<td width="2%" align="center" >:</td>
								<td width="3%" align="center" >1.</td>
								<td width="auto" align="justify" >'.$nama_nahkoda.' / Nakhoda Kapal Pengawas '.$nama_kapal.'</td>
							</tr>
							<tr>
								<td width="20%" align="justify" ></td>
								<td width="2%" align="center" >:</td>
								<td width="3%" align="center" >2.</td>
								<td width="auto" align="justify" >'.$nama_kkm.' / KKM Kapal Pengawas '.$nama_kapal.'</td>
							</tr>
							
							<tr>
								<td></td>
							</tr>
							
							<tr>
								<td width="20%" align="justify" >Alamat</td>
								<td width="2%" align="center" >:</td>
								<td width="auto" align="justify" >'.$alamat1.'</td>
							</tr>
							
							<tr>
								<td></td>
							</tr>
							
							<tr>
								<td width="100%" align="justify" >
								Menyatakan bahwa telah melakukan pengukuran sisa BBM sebelum pelayaran dengan rincian sebagai berikut :</td>
							</tr>
				</table>';
		
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="1">
							
							<tr>
								<td width="40%" align="center" >Sisa BBM Sebelum Pelayaran </td>
								<td width="3%" align="center" >=</td>
								<td width="40%" align="center" >'.number_format($volume_sisa).'</td>
								<td width="auto" align="center" >Liter</td>
							</tr>
				</table>';
				
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">
							
							<tr>
								<td width="100%" align="justify" >
												Demikian Berita Acara Sisa BBM Sebelum Pelayaran ini dibuat dengan sebenar – benarnya untuk dapat dipergunakan sebagaimana mestinya.</td>
							</tr>
				</table>';
		
		
		
		// ISI -->  
		
		
		// <-- Fotter
		$tbl .= '<br><br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif; font-size:10px" border ="0">
							<tr>
								<td width="40%" align="center" >
									<b>'.$an_nakhoda.' Nakhoda KP. '.$nama_kapal.'</b><br><br><br><br><br>
						
									<b><u>'.$nama_nahkoda.'</u></b><br>
									<b>NIP. '.$nip_nahkoda.'</b>
								</td>
								<td width="20%" align="center" ></td>
								<td width="40%" align="center" >
									<b>'.$an_kkm.' KKM KP. '.$nama_kapal.'</b><br><br><br><br><br>
						
									<b><u>'.$nama_kkm.'</u></b><br>
									<b>NIP. '.$nip_kkm.'</b>
								</td>
							</tr>
							<tr>
								<td width="30%" align="center"></td>
								<td width="40%" align="center" >
									<b><br><br>Menyaksikan:</b><br>
									<b>'.$an_staf.' '.$jabatan_staf_pangkalan.'</b><br><br><br><br><br>
						
									<b><u>'.$nama_staf_pagkalan.'</u></b><br>
									<b>NIP. '.$nip_staf.'</b>
								</td>
								
								<td width="30%" align="center" ></td>
							</tr>
				</table> ';
		// Fotter -->
		
		
		$pdf->writeHTML($tbl, true, false, true, false, '');
        $pdf->Output($_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/dokumen/cetakan_ba/'.$filename.'.pdf', 'F');

	}
	
	public function cetak_ba_ssdah_pelayaran($nomor_suratx, $filename) {
	    
		$nomor_surat = str_replace('_','/',$nomor_suratx);
		
		$q_result2 = "SELECT kapal_code,nama_kapal,nomor_surat,REPLACE(jam_surat,':','.') AS jam_surat,tanggal_surat, volume_sisa,
		
		TRIM(SUBSTRING(f_formattanggal(tanggal_surat),1,254)) AS ttanggal,nama_nahkoda,nip_nahkoda,nama_kkm,nip_kkm,nama_staf_pagkalan,nip_staf,nama_kapal,nama_upt,
		alamat1,alamat2,alamat3,kota,zona_waktu, lok_surat, jabatan_staf_pangkalan, an_staf, an_nakhoda, an_kkm
		FROM
		(SELECT a.*,b.nama_kapal,m_upt_code,nama AS nama_upt,
   		  IFNULL(alamat1,'') AS alamat1,IFNULL(alamat2,'') AS alamat2,ifnull(alamat3,'') AS alamat3, ifnull(kota,'') AS kota, a.zona_waktu_surat AS zona_waktu , a.lokasi_surat AS lok_surat 
		  FROM bbm_kapaltrans a 
		  LEFT JOIN m_kapal b ON a.kapal_code = b.code_kapal LEFT JOIN m_upt c ON b.m_upt_code = c.code  
		  WHERE nomor_surat = '".$nomor_surat."') bbm_kapaltrans";
		 
		// var_dump($q_result2);die();
		$coba = $this->db->query($q_result2);
		
		foreach($coba->result_array() AS $list){
			$nama_kapal 	= $list['nama_kapal'];
			$nomor_surat 	= $list['nomor_surat'];
			$jam_surat 		= $list['jam_surat'];
			$tanggal_surat 	= $list['tanggal_surat'];
			$volume_sisa 	= $list['volume_sisa'];
			$ttanggal 		= $list['ttanggal'];
			$nama_nahkoda 	= $list['nama_nahkoda'];
			$nip_nahkoda 	= $list['nip_nahkoda'];
			$nama_kkm 		= $list['nama_kkm'];
			$nip_kkm 		= $list['nip_kkm'];
			$nama_staf_pagkalan = $list['nama_staf_pagkalan'];
			$nip_staf 		= $list['nip_staf'];
			$nama_upt		= $list['nama_upt'];
			$alamat1		= $list['alamat1'];
			$alamat2		= $list['alamat2'];
			$alamat3		= $list['alamat3'];
			$lok_surat   		= $list['lok_surat'];
			$zona_waktu		= $list['zona_waktu'];
			$jabatan_staf_pangkalan		= $list['jabatan_staf_pangkalan'];
		} 
		
		if($list['an_staf'] == 1){
			$an_staf = 'An. ';
		}else{
			$an_staf = '';
		}
		
		if($list['an_nakhoda'] == 1){
			$an_nakhoda = 'An. ';
		}else{
			$an_nakhoda = '';
		}
		
		if($list['an_kkm'] == 1){
			$an_kkm = 'An. ';
		}else{
			$an_kkm = '';
		}
		
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setPrintFooter(false);
        $pdf->setPrintHeader(false);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->AddPage('P','A4');
        $pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetFont('');
        
        
        $tbl = '<style type="text/css">
				hr.new5 {
					  border: 20px solid green;
					  border-radius: 5px;
					}
			</style>';
		// <-- Header
        $tbl .= '
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;" border ="0">
							<tr>
								<td width="17%" align="center" ><img align="center" width="120" height="120" src="'.$_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/assets/img/kkp3.png" border="0" /></td>
								<td width="82%" align="center" >
									<font size="15"><b>KEMENTERIAN KELAUTAN DAN PERIKANAN</b></font><br>
									<font size="17">DIREKTORAT JENDERAL PENGAWASAN</font><br>
									<font size="17">SUMBER DAYA KELAUTAN DAN PERIKANAN</font><br>
									<font size="12"><b><i>'.strtoupper($nama_upt).'</b></i></font><br>
									<font size="10">'.$alamat1.'</font><br>
									<font size="10">'.$alamat2.'</font><br>
									<font size="10">'.$alamat3.'</font>
								</td>
							</tr>
				</table> ';
			

		$style = array('width' => 1.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 58, 200, 58, $style);
		$style2 = array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 60, 200, 60, $style2);
		// Header -->
		
		
		// <-- ISI 
		
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">
                
                            <tr>
							    <td width="100%" align="center" ><font size="12"><b><u>BERITA ACARA SISA BBM SESUDAH PELAYARAN</b></u></font></td>
								
							</tr>
							<tr>
							    <td width="100%" align="center" ><b>Nomor : '.$nomor_surat.'</b><br></td>
				
							</tr>
							<tr>
								<td width="100%" align="justify" >
								Pada hari ini '.$ttanggal.' pukul '.$jam_surat.' '.$zona_waktu.', bertempat di '.strtoupper($lok_surat).', kami yang bertanda tangan
								dibawah ini : </td>
							</tr>
							<tr>
								<td></td>
							</tr>
							
							<tr>
								<td width="20%" align="justify" >Nama/Jabatan</td>
								<td width="2%" align="center" >:</td>
								<td width="3%" align="center" >1.</td>
								<td width="auto" align="justify" >'.$nama_nahkoda.' / Nakhoda Kapal Pengawas '.$nama_kapal.'</td>
							</tr>
							<tr>
								<td width="20%" align="justify" ></td>
								<td width="2%" align="center" >:</td>
								<td width="3%" align="center" >2.</td>
								<td width="auto" align="justify" >'.$nama_kkm.' / KKM Kapal Pengawas '.$nama_kapal.'</td>
							</tr>
							
							<tr>
								<td></td>
							</tr>
							
							<tr>
								<td width="20%" align="justify" >Alamat</td>
								<td width="2%" align="center" >:</td>
								<td width="auto" align="justify" >'.$alamat1.'</td>
							</tr>
							
							<tr>
								<td></td>
							</tr>
							
							<tr>
								<td width="100%" align="justify" >
								Menyatakan bahwa telah melakukan pengukuran sisa BBM sesudah pelayaran dengan rincian sebagai berikut :</td>
							</tr>
				</table>';
		
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="1">
							
							<tr>
								<td width="40%" align="center" >Sisa BBM Sesudah Pelayaran </td>
								<td width="3%" align="center" >=</td>
								<td width="40%" align="center" >'.number_format($volume_sisa).'</td>
								<td width="auto" align="center" >Liter</td>
							</tr>
				</table>';
				
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">
							
							<tr>
								<td width="100%" align="justify" >
												Demikian Berita Acara Sisa BBM Sesudah Pelayaran ini dibuat dengan sebenar – benarnya untuk dapat dipergunakan sebagaimana mestinya.</td>
							</tr>
				</table>';
		
		
		
		// ISI -->  
		
		
		// <-- Fotter
		$tbl .= '<br><br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif; font-size:10px" border ="0">
							<tr>
								<td width="40%" align="center" >
									<b>'.$an_nakhoda.' Nakhoda KP. '.$nama_kapal.'</b><br><br><br><br><br>
						
									<b><u>'.$nama_nahkoda.'</u></b><br>
									<b>NIP. '.$nip_nahkoda.'</b>
								</td>
								<td width="20%" align="center" ></td>
								<td width="40%" align="center" >
									<b>'.$an_kkm.' KKM KP. '.$nama_kapal.'</b><br><br><br><br><br>
						
									<b><u>'.$nama_kkm.'</u></b><br>
									<b>NIP. '.$nip_kkm.'</b>
								</td>
							</tr>
							<tr>
								<td width="30%" align="center"></td>
								<td width="40%" align="center" >
									<b><br><br>Menyaksikan:</b><br>
									<b>'.$an_staf.' '.$jabatan_staf_pangkalan.'</b><br><br><br><br><br>
						
									<b><u>'.$nama_staf_pagkalan.'</u></b><br>
									<b>NIP. '.$nip_staf.'</b>
								</td>
								
								<td width="30%" align="center" ></td>
							</tr>
				</table> ';
		// Fotter -->
		
		
		$pdf->writeHTML($tbl, true, false, true, false, '');
        $pdf->Output($_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/dokumen/cetakan_ba/'.$filename.'.pdf', 'F');

	}
	
	public function cetak_ba_pemeriksa_sarana($nomor_suratx, $filename) {
	    
	    header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Content-Type: application/xml; charset=utf-8");
		
		$nomor_surat = str_replace('_','/',$nomor_suratx);
		
		$q_result2 = "SELECT kapal_code,nama_kapal,nomor_surat,REPLACE(jam_surat,':','.') AS jam_surat,tanggal_surat,
		TRIM(SUBSTRING(f_formattanggal(tanggal_surat),1,254)) AS ttanggal, jenis_tranport,CASE 
        WHEN jenis_tranport = '1' THEN 'Mobil/<strike>Kapal/Pengisian Langsung</strike>'
        WHEN jenis_tranport = '2' THEN '<strike>Mobil</strike>/Kapal/<strike>Pengisian Langsung</strike>'
        ELSE '<strike>Mobil/Kapal</strike>/Pengisian Langsung'
    	END AS jenis_tranport_dtl,
		IF(status_segel=1,'baik/<strike>rusak</strike>','<strike>baik</strike>/rusak') AS status_segel,IF(status_flowmeter=1,'baik/<strike>rusak</strike>','<strike>baik</strike>/rusak') AS status_flowmeter,penyedia,
		IF(kesimpulan=1,'dilakukan','ditunda sampai dengan tersedianya sarana pengganti.') AS kesimpulan,
		nama_nahkoda,nip_nahkoda,nama_kkm,nip_kkm,nama_staf_pagkalan,nip_staf,nama_kapal,nama_upt,
		alamat1,alamat2,alamat3,kota,zona_waktu, lok_surat, jabatan_staf_pangkalan, an_staf, an_nakhoda, an_kkm
		FROM
		(SELECT a.*,b.nama_kapal,m_upt_code,nama AS nama_upt,
   		  IFNULL(alamat1,'') AS alamat1,IFNULL(alamat2,'') AS alamat2,ifnull(alamat3,'') AS alamat3,ifnull(kota,'') AS kota, a.zona_waktu_surat AS zona_waktu , a.lokasi_surat AS lok_surat 
		  FROM bbm_kapaltrans a 
		  LEFT JOIN m_kapal b ON a.kapal_code = b.code_kapal LEFT JOIN m_upt c ON b.m_upt_code = c.code  
		  WHERE nomor_surat = '".$nomor_surat."' AND status_ba = '4') bbm_kapaltrans;";
		$coba = $this->db->query($q_result2);
		
		foreach($coba->result_array() AS $list){
			$nama_kapal 	= $list['nama_kapal'];
			$nomor_surat 	= $list['nomor_surat'];
			$jam_surat 		= $list['jam_surat'];
			$tanggal_surat 	= $list['tanggal_surat'];
			$status_segel	= $list['status_segel'];
			$status_flowmeter = $list['status_flowmeter'];
			$penyedia		= $list['penyedia'];
			$kesimpulan		= $list['kesimpulan'];
			$ttanggal 		= $list['ttanggal'];
			$nama_nahkoda 	= $list['nama_nahkoda'];
			$nip_nahkoda 	= $list['nip_nahkoda'];
			$nama_kkm 		= $list['nama_kkm'];
			$nip_kkm 		= $list['nip_kkm'];
			$nama_staf_pagkalan = $list['nama_staf_pagkalan'];
			$nip_staf 		= $list['nip_staf'];
			$nama_upt		= $list['nama_upt'];
			$alamat1		= $list['alamat1'];
			$alamat2		= $list['alamat2'];
			$alamat3		= $list['alamat3'];
			$lok_surat		    = $list['lok_surat'];
			$zona_waktu		= $list['zona_waktu'];
			$jabatan_staf_pangkalan		= $list['jabatan_staf_pangkalan'];
			$jenis_tranport		= $list['jenis_tranport'];
			$jenis_tranport_dtl = $list['jenis_tranport_dtl'];
		} 
		
		if($list['an_staf'] == 1){
			$an_staf = 'An. ';
		}else{
			$an_staf = '';
		}
		
		if($list['an_nakhoda'] == 1){
			$an_nakhoda = 'An. ';
		}else{
			$an_nakhoda = '';
		}
		
		if($list['an_kkm'] == 1){
			$an_kkm = 'An. ';
		}else{
			$an_kkm = '';
		}
		
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setPrintFooter(false);
        $pdf->setPrintHeader(false);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->AddPage('P','A4');
        $pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetFont('');
        
        
        $tbl = '<style type="text/css">
				hr.new5 {
					  border: 20px solid green;
					  border-radius: 5px;
					}
			</style>';
		// <-- Header
        $tbl .= '
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;" border ="0">
							<tr>
								<td width="17%" align="center" ><img align="center" width="120" height="120" src="'.$_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/assets/img/kkp3.png" border="0" /></td>
								<td width="82%" align="center" >
									<font size="15"><b>KEMENTERIAN KELAUTAN DAN PERIKANAN</b></font><br>
									<font size="17">DIREKTORAT JENDERAL PENGAWASAN</font><br>
									<font size="17">SUMBER DAYA KELAUTAN DAN PERIKANAN</font><br>
									<font size="12"><b><i>'.strtoupper($nama_upt).'</b></i></font><br>
									<font size="10">'.$alamat1.'</font><br>
									<font size="10">'.$alamat2.'</font><br>
									<font size="10">'.$alamat3.'</font>
								</td>
							</tr>
				</table> ';
			

		$style = array('width' => 1.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 58, 200, 58, $style);
		$style2 = array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 60, 200, 60, $style2);
		// Header -->
		
		
		// <-- ISI 
		
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">
                            
                             <tr>
							    <td width="100%" align="center" ><font size="12"><b><u>BERITA ACARA PEMERIKSAAN SARANA PENGISIAN BBM</b></u></font></td>
							
							</tr>
							<tr>
							    <td width="100%" align="center" ><b>Nomor : '.$nomor_surat.'</b><br></td>
				
							</tr>
							<tr>
								<td width="100%" align="justify" >
								Pada hari ini '.$ttanggal.' pukul '.$jam_surat.' '.$zona_waktu.', bertempat di '.strtoupper($lok_surat).', kami yang bertanda tangan
								dibawah ini : </td>
							</tr>
							<tr>
								<td></td>
							</tr>
							
							<tr>
								<td width="20%" align="justify" >Nama/Jabatan</td>
								<td width="2%" align="center" >:</td>
								<td width="3%" align="center" >1.</td>
								<td width="auto" align="justify" >'.$nama_nahkoda.' / Nakhoda Kapal Pengawas '.$nama_kapal.'</td>
							</tr>
							<tr>
								<td width="20%" align="justify" ></td>
								<td width="2%" align="center" >:</td>
								<td width="3%" align="center" >2.</td>
								<td width="auto" align="justify" >'.$nama_kkm.' / KKM Kapal Pengawas '.$nama_kapal.'</td>
							</tr>
							
							<tr>
								<td></td>
							</tr>
							
							<tr>
								<td width="20%" align="justify" >Alamat</td>
								<td width="2%" align="center" >:</td>
								<td width="auto" align="justify" >'.$alamat1.'</td>
							</tr>
							
							<tr>
								<td></td>
							</tr>
							
							<tr>
								<td width="100%" align="justify" >
								Menyatakan bahwa telah melakukan pemeriksaan sarana pengisian dan volume BBM dengan menggunakan '.$jenis_tranport_dtl.' milik '.$penyedia.',
			sebelum pengisian dilakukan dengan rincian pemeriksaan sebagai berikut :</td>
							</tr>
				</table>';
				
	    // var_dump($jenis_tranport);die();
	    
	    
	    
	    
		if($jenis_tranport == 1){
		    
		    $tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">
							
							<tr>
								<td width="8%" align="center" ></td>
								<td width="3%" align="center" >1.</td>
								<td width="auto" align="justify" >&nbsp; Segel tutup tangki dalam kondisi '.$status_segel.'.</td>
							</tr>
							<tr>
								<td width="8%" align="center" ></td>
								<td width="3%" align="center" >2.</td>
								<td width="auto" align="justify" >&nbsp;<strike> Flowmeter dalam kondisi '.$status_flowmeter.'.</strike></td>
							</tr>
							<tr>
								<td width="8%" align="center" ></td>
								
							</tr>
							<tr>
								<td width="12%" align="justify" >Kesimpulan</td>
								<td width="3%" align="center" >:</td>
								<td width="auto" align="justify" >Pengisian dapat '.$kesimpulan.'</td>
							</tr>
							
				</table>';
		    
		}else if($jenis_tranport == 2){
		    
		    $tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">
							
							<tr>
								<td width="8%" align="center" ></td>
								<td width="3%" align="center" >1.</td>
								<td width="auto" align="justify" >&nbsp; <strike> Segel tutup tangki dalam kondisi '.$status_segel.'.</strike></td>
							</tr>
							<tr>
								<td width="8%" align="center" ></td>
								<td width="3%" align="center" >2.</td>
								<td width="auto" align="justify" >&nbsp; Flowmeter dalam kondisi '.$status_flowmeter.'.</td>
							</tr>
							<tr>
								<td width="8%" align="center" ></td>
								
							</tr>
							<tr>
								<td width="12%" align="justify" >Kesimpulan</td>
								<td width="3%" align="center" >:</td>
								<td width="auto" align="justify" >Pengisian dapat '.$kesimpulan.'</td>
							</tr>
							
				</table>';
		    
		}else{
		    
		    $tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">
							
							<tr>
								<td width="8%" align="center" ></td>
								<td width="3%" align="center" >1.</td>
								<td width="auto" align="justify" >&nbsp;<strike> Segel tutup tangki dalam kondisi '.$status_segel.'.</strike></td>
							</tr>
							<tr>
								<td width="8%" align="center" ></td>
								<td width="3%" align="center" >2.</td>
								<td width="auto" align="justify" >&nbsp;Flowmeter dalam kondisi '.$status_flowmeter.'.</td>
							</tr>
							<tr>
								<td width="8%" align="center" ></td>
								
							</tr>
							<tr>
								<td width="12%" align="justify" >Kesimpulan</td>
								<td width="3%" align="center" >:</td>
								<td width="auto" align="justify" >Pengisian dapat '.$kesimpulan.'</td>
							</tr>
							
				</table>';
		    
		    
		}
				
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">
							
							<tr>
								<td width="100%" align="justify" >
												Demikian Berita Acara Pemeriksaan Sarana Pengisian ini dibuat dengan
			sebenar – benarnya untuk dapat dipergunakan sebagaimana mestinya.</td>
							</tr>
				</table>';
		
		
		
		// ISI -->  
		
		
		// <-- Fotter
		$tbl .= '<br></br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif; font-size:10px" border ="0">
							<tr>
								<td width="40%" align="center" >
									<b>'.$an_kkm.' KKM KP. '.$nama_kapal.'</b><br><br><br><br><br>
						
									<b><u>'.$nama_kkm.'</u></b><br>
									<b>NIP. '.$nip_kkm.'<br></b>
								</td>
								<td width="20%" align="center" >
								
								</td>
								<td width="40%" align="center" >
									<b>Penyedia/Pengirim BBM</b>
                                    <br><b> '.$penyedia.' </b><br><br><br><br>
						
								    ________________________
								</td>
							</tr>
							
							<tr>
								<td width="40%" align="center">
									<b><br><br><br>'.$an_nakhoda.' Nakhoda KP. '.$nama_kapal.'</b><br><br><br><br><br>
						
									<b><u>'.$nama_nahkoda.'</u></b><br>
									<b>NIP. '.$nip_nahkoda.'</b> 
								</td>
									
								<td width="20%" align="center" >
									<b>Menyaksikan:</b>
									
								</td>
								
								<td width="40%" align="center" >
									<b><br><br><br>'.$an_staf.' '.$jabatan_staf_pangkalan.'</b><br><br><br><br><br>
						
									<b><u>'.$nama_staf_pagkalan.'</u></b><br>
									<b>NIP. '.$nip_staf.'</b>
								</td>
							</tr>
				</table> ';
		// Fotter -->
		
		
		$pdf->writeHTML($tbl, true, false, true, false, '');
        $pdf->Output($_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/dokumen/cetakan_ba/'.$filename.'.pdf', 'F');

	}

	public function cetak_ba_penggunaan_bbm($nomor_suratx, $filename) {
		
		$nomor_surat = str_replace('_','/',$nomor_suratx);
		
		$q_result2 = "SELECT kapal_code,nama_kapal,nomor_surat,REPLACE(jam_surat,':','.') AS jam_surat,
		volume_sisa, tanggal_surat, volume_sebelum, TRIM(SUBSTRING(f_formattanggal(tanggal_surat),1,254)) AS ttanggal,
		volume_pengisian, volume_pemakaian,tanggal_sebelum,tanggal_pengisian,keterangan_jenis_bbm,
		nama_nahkoda,nip_nahkoda,nama_kkm,nip_kkm,nama_staf_pagkalan,nip_staf,nama_kapal,nama_upt,
		alamat1,alamat2,alamat3,kota, zona_waktu, lok_surat, jabatan_staf_pangkalan, an_staf, an_nakhoda, an_kkm
		FROM
		(SELECT a.*,b.nama_kapal,m_upt_code,nama AS nama_upt,
   		  IFNULL(alamat1,'') AS alamat1,IFNULL(alamat2,'') AS alamat2,IFNULL(alamat3,'') AS alamat3, ifnull(kota,'') AS kota, a.zona_waktu_surat AS zona_waktu, a.lokasi_surat AS lok_surat 
		  FROM bbm_kapaltrans a 
		  LEFT JOIN m_kapal b ON a.kapal_code = b.code_kapal LEFT JOIN m_upt c ON b.m_upt_code = c.code  
		  WHERE nomor_surat = '".$nomor_surat."' and status_ba = '3') bbm_kapaltrans;";
		$coba = $this->db->query($q_result2);
		
		foreach($coba->result_array() AS $list){
			//$kapal_code 	= $list['kapal_code'];
			$nama_kapal 	= $list['nama_kapal'];
			$tanggal_surat 	= $list['tanggal_surat'];
			$nomor_surat 	= $list['nomor_surat'];
			$jam_surat 		= $list['jam_surat'];
			$tanggal_sebelum 	= $list['tanggal_sebelum'];
			$tanggal_pengisian 	= $list['tanggal_pengisian'];
			$volume_sisa 	= $list['volume_sisa'];
			$volume_sebelum	= $list['volume_sebelum'];
			$volume_pengisian 	= $list['volume_pengisian'];
			$volume_pemakaian 	= $list['volume_pemakaian'];
			$ttanggal 		= $list['ttanggal'];
			$nama_nahkoda 	= $list['nama_nahkoda'];
			$nip_nahkoda 	= $list['nip_nahkoda'];
			$nama_kkm 		= $list['nama_kkm'];
			$nip_kkm 		= $list['nip_kkm'];
			$nama_staf_pagkalan = $list['nama_staf_pagkalan'];
			$nip_staf 		= $list['nip_staf'];
			$nama_upt		= $list['nama_upt'];
			$alamat1		= $list['alamat1'];
			$alamat2		= $list['alamat2'];
			$alamat3		= $list['alamat3'];
			$lok_surat		    = $list['lok_surat'];
			$zona_waktu		= $list['zona_waktu'];
			$keterangan_jenis_bbm = $list['keterangan_jenis_bbm'];
			$jabatan_staf_pangkalan = $list['jabatan_staf_pangkalan'];
		} 
		
		if($list['an_staf'] == 1){
			$an_staf = 'An. ';
		}else{
			$an_staf = '';
		}
		
		if($list['an_nakhoda'] == 1){
			$an_nakhoda = 'An. ';
		}else{
			$an_nakhoda = '';
		}
		
		if($list['an_kkm'] == 1){
			$an_kkm = 'An. ';
		}else{
			$an_kkm = '';
		}
		
		if($tanggal_pengisian == '1970-01-01' || $tanggal_pengisian == '0000-00-00'){
		   $tgl_pengisian =  '';
		}else{
		    $tgl_pengisian =  $this->indo_date($tanggal_pengisian);
		}
		
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setPrintFooter(false);
        $pdf->setPrintHeader(false);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->AddPage('P','A4');
        $pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetFont('');
        
        
        $tbl = '<style type="text/css">
				hr.new5 {
					  border: 20px solid green;
					  border-radius: 5px;
					}
			</style>';
		// <-- Header
        $tbl .= '
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;" border ="0">
							<tr>
								<td width="17%" align="center" ><img align="center" width="120" height="120" src="'.$_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/assets/img/kkp3.png" border="0" /></td>
								<td width="82%" align="center" >
									<font size="15"><b>KEMENTERIAN KELAUTAN DAN PERIKANAN</b></font><br>
									<font size="17">DIREKTORAT JENDERAL PENGAWASAN</font><br>
									<font size="17">SUMBER DAYA KELAUTAN DAN PERIKANAN</font><br>
									<font size="12"><b><i>'.strtoupper($nama_upt).'</b></i></font><br>
									<font size="10">'.$alamat1.'</font><br>
									<font size="10">'.$alamat2.'</font><br>
									<font size="10">'.$alamat3.'</font>
								</td>
							</tr>
				</table> ';
			

		$style = array('width' => 1.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 58, 200, 58, $style);
		$style2 = array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 60, 200, 60, $style2);
		// Header -->
		
		
		// <-- ISI 
		
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">
                            
                            <tr>
							    <td width="100%" align="center" ><font size="12"><b><u>BERITA ACARA PENGGUNAAN BBM</b></u></font></td>
							
							</tr>
							<tr>
							    <td width="100%" align="center" ><b>Nomor : '.$nomor_surat.'</b><br></td>
							</tr>
							<tr>
								<td width="100%" align="justify" >
								Pada hari ini '.$ttanggal.' pukul '.$jam_surat.' '.$zona_waktu.', bertempat di '.strtoupper($lok_surat).', kami yang bertanda tangan
								dibawah ini : </td>
							</tr>
							<tr>
								<td></td>
							</tr>
							
							<tr>
								<td width="20%" align="justify" >Nama/Jabatan</td>
								<td width="2%" align="center" >:</td>
								<td width="3%" align="center" >1.</td>
								<td width="auto" align="justify" >'.$nama_nahkoda.' / Nakhoda Kapal Pengawas '.$nama_kapal.'</td>
							</tr>
							<tr>
								<td width="20%" align="justify" ></td>
								<td width="2%" align="center" >:</td>
								<td width="3%" align="center" >2.</td>
								<td width="auto" align="justify" >'.$nama_kkm.' / KKM Kapal Pengawas '.$nama_kapal.'</td>
							</tr>
							
							<tr>
								<td></td>
							</tr>
							
							<tr>
								<td width="20%" align="justify" >Alamat</td>
								<td width="2%" align="center" >:</td>
								<td width="auto" align="justify" >'.$alamat1.'</td>
							</tr>
							
							<tr>
								<td></td>
							</tr>
							
							<tr>
								<td width="100%" align="justify" >
								Menyatakan bahwa telah menggunakan BBM '.$keterangan_jenis_bbm.' dengan rincian sebagai berikut :</td>
							</tr>
				</table>';
		
		if($volume_pengisian == 0){
		    
		     $dtlVolPengisian = '';
		    
		}else{
		    
		    $dtlVolPengisian = ', tanggal '.$this->indo_date($tanggal_pengisian).' ';
		   
		    
		}
		
		if($volume_pemakaian == 0){
		    
		    $dtlVolPemakaian = '';
		    
		}else{
		   
		    $dtlVolPemakaian = ', tanggal '.$this->indo_date($tanggal_surat).' ';
		}
		
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="1">
							
							<tr>
								<td width="69%" align="justify">&nbsp; Volume tangki pengukuran sebelumnya, tanggal '.$this->indo_date($tanggal_sebelum).'</td>
								<td width="3%" align="center" >=</td>
								<td width="17%" align="right" >'.number_format($volume_sebelum).'&nbsp;&nbsp;&nbsp;</td>
								<td width="auto" align="justify" >&nbsp;Liter</td>
							</tr>
							<tr>
								<td width="69%" align="justify">&nbsp; Pengisian '.$dtlVolPengisian.'</td>
								<td width="3%" align="center" >=</td>
								<td width="17%" align="right" >'.number_format($volume_pengisian).'&nbsp;&nbsp;&nbsp;</td>
								<td width="auto" align="justify" >&nbsp;Liter</td>
							</tr>
							<tr>
								<td width="69%" align="justify">&nbsp; Volume tangki pengukuran saat ini '.$dtlVolPemakaian.'</td>
								<td width="3%" align="center" >=</td>
								<td width="17%" align="right" >'.number_format($volume_sisa).'&nbsp;&nbsp;&nbsp;</td>
								<td width="auto" align="justify" >&nbsp;Liter</td>
							</tr>
							<tr>
								<td width="69%" align="justify">&nbsp; Jumlah Penggunaan</td>
								<td width="3%" align="center" >=</td>
								<td width="17%" align="right" >'.number_format($volume_pemakaian).'&nbsp;&nbsp;&nbsp;</td>
								<td width="auto" align="justify" >&nbsp;Liter</td>
							</tr>
				</table>';
				
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">
							
							<tr>
								<td width="100%" align="justify" >
												Demikian Berita Acara Pengunaan BBM ini dibuat dengan sebenar – benarnya untuk dapat dipergunakan sebagaimana mestinya.</td>
							</tr>
				</table>';
		
		
		
		// ISI -->  
		
		
		// <-- Fotter
		$tbl .= '<br><br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif; font-size:10px" border ="0">
							<tr>
								<td width="40%" align="center" >
									<b>'.$an_nakhoda.' Nakhoda KP. '.$nama_kapal.'</b><br><br><br><br><br>
						
									<b><u>'.$nama_nahkoda.'</u></b><br>
									<b>NIP. '.$nip_nahkoda.'</b>
								</td>
								<td width="20%" align="center" ></td>
								<td width="40%" align="center" >
									<b>'.$an_kkm.' KKM KP. '.$nama_kapal.'</b><br><br><br><br><br>
						
									<b><u>'.$nama_kkm.'</u></b><br>
									<b>NIP. '.$nip_kkm.'</b>
								</td>
							</tr>
							<tr>
								<td width="30%" align="center"></td>
								<td width="40%" align="center" >
									<b><br><br>Menyaksikan:</b><br>
									<b>'.$an_staf.' '.$jabatan_staf_pangkalan.'</b><br><br><br><br><br>
						
									<b><u>'.$nama_staf_pagkalan.'</u></b><br>
									<b>NIP. '.$nip_staf.'</b>
								</td>
								
								<td width="30%" align="center" ></td>
							</tr>
				</table> ';
		// Fotter -->
		
		
		$pdf->writeHTML($tbl, true, false, true, false, '');
        $pdf->Output($_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/dokumen/cetakan_ba/'.$filename.'.pdf', 'F');


	}
	
	public function cetak_ba_akhir_bulan($nomor_suratx, $filename) {
		
		$nomor_surat = str_replace('_','/',$nomor_suratx);
			
		$q_result2 = "SELECT kapal_code,nama_kapal,nomor_surat,REPLACE(jam_surat,':','.') AS jam_surat,
		volume_sisa, tanggal_surat, volume_sebelum, TRIM(SUBSTRING(f_formattanggal(tanggal_surat),1,254)) AS ttanggal,
		volume_pengisian, volume_pemakaian,tanggal_sebelum,tanggal_pengisian,
		nama_nahkoda,nip_nahkoda,nama_kkm,nip_kkm,nama_staf_pagkalan,nip_staf,nama_kapal,nama_upt,
		alamat1,alamat2,alamat3,kota, zona_waktu,lok_surat, jabatan_staf_pangkalan, an_staf, an_nakhoda, an_kkm
		FROM
		(SELECT a.*,b.nama_kapal,m_upt_code,nama AS nama_upt,
   		  IFNULL(alamat1,'') AS alamat1,IFNULL(alamat2,'') AS alamat2,IFNULL(alamat3,'') AS alamat3, IFNULL(kota,'') AS kota, a.zona_waktu_surat AS zona_waktu, a.lokasi_surat AS lok_surat 
		  FROM bbm_kapaltrans a 
		  LEFT JOIN m_kapal b ON a.kapal_code = b.code_kapal LEFT JOIN m_upt c ON b.m_upt_code = c.code  
		  WHERE nomor_surat = '".$nomor_surat."' and status_ba = '1') bbm_kapaltrans;";
		$coba = $this->db->query($q_result2);
		
		foreach($coba->result_array() AS $list){
			//$kapal_code 	= $list['kapal_code'];
			$nama_kapal 	= $list['nama_kapal'];
			$tanggal_surat 	= $list['tanggal_surat'];
			$nomor_surat 	= $list['nomor_surat'];
			$jam_surat 		= $list['jam_surat'];
			$tanggal_sebelum 	= $list['tanggal_sebelum'];
			$tanggal_pengisian 	= $list['tanggal_pengisian'];
			$volume_sisa 	= $list['volume_sisa'];
			$volume_sebelum	= $list['volume_sebelum'];
			$volume_pengisian 	= $list['volume_pengisian'];
			$volume_pemakaian 	= $list['volume_pemakaian'];
			$ttanggal 		= $list['ttanggal'];
			$nama_nahkoda 	= $list['nama_nahkoda'];
			$nip_nahkoda 	= $list['nip_nahkoda'];
			$nama_kkm 		= $list['nama_kkm'];
			$nip_kkm 		= $list['nip_kkm'];
			$nama_staf_pagkalan = $list['nama_staf_pagkalan'];
			$nip_staf 		= $list['nip_staf'];
			$nama_upt		= $list['nama_upt'];
			$alamat1		= $list['alamat1'];
			$alamat2		= $list['alamat2'];
			$alamat3		= $list['alamat3'];
			$lok_surat		    = $list['lok_surat'];
			$zona_waktu		= $list['zona_waktu'];
			$jabatan_staf_pangkalan		= $list['jabatan_staf_pangkalan'];
		} 
		
		if($list['an_staf'] == 1){
			$an_staf = 'An. ';
		}else{
			$an_staf = '';
		}
		
		if($list['an_nakhoda'] == 1){
			$an_nakhoda = 'An. ';
		}else{
			$an_nakhoda = '';
		}
		
		if($list['an_kkm'] == 1){
			$an_kkm = 'An. ';
		}else{
			$an_kkm = '';
		}
		
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setPrintFooter(false);
        $pdf->setPrintHeader(false);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->AddPage('P','A4');
        $pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetFont('');
        
        
        $tbl = '<style type="text/css">
				hr.new5 {
					  border: 20px solid green;
					  border-radius: 5px;
					}
			</style>';
		// <-- Header
        $tbl .= '
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;" border ="0">
							<tr>
								<td width="17%" align="center" ><img align="center" width="120" height="120" src="'.$_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/assets/img/kkp3.png" border="0" /></td>
								<td width="82%" align="center" >
									<font size="15"><b>KEMENTERIAN KELAUTAN DAN PERIKANAN</b></font><br>
									<font size="17">DIREKTORAT JENDERAL PENGAWASAN</font><br>
									<font size="17">SUMBER DAYA KELAUTAN DAN PERIKANAN</font><br>
									<font size="12"><b><i>'.strtoupper($nama_upt).'</b></i></font><br>
									<font size="10">'.$alamat1.'</font><br>
									<font size="10">'.$alamat2.'</font><br>
									<font size="10">'.$alamat3.'</font>
								</td>
							</tr>
				</table> ';
			

		$style = array('width' => 1.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 58, 200, 58, $style);
		$style2 = array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 60, 200, 60, $style2);
		// Header -->
		
		
		// <-- ISI 
		
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">
                
                            <tr>
							    
								<td width="100%" align="center" ><font size="12"><b><u>BERITA ACARA AKHIR BULAN</b></u></font></td>
								
							</tr>
							<tr>
							    <td width="100%" align="center" ><b>Nomor : '.$nomor_surat.'</b><br></td>
							</tr>
							<tr>
								<td width="100%" align="justify" >
								Pada hari ini '.$ttanggal.' pukul '.$jam_surat.' '.$zona_waktu.', bertempat di '.strtoupper($lok_surat).', kami yang bertanda tangan
								dibawah ini : </td>
							</tr>
							<tr>
								<td></td>
							</tr>
							
							<tr>
								<td width="20%" align="justify" >Nama/Jabatan</td>
								<td width="2%" align="center" >:</td>
								<td width="3%" align="center" >1.</td>
								<td width="auto" align="justify" >'.$nama_nahkoda.' / Nakhoda Kapal Pengawas '.$nama_kapal.'</td>
							</tr>
							<tr>
								<td width="20%" align="justify" ></td>
								<td width="2%" align="center" >:</td>
								<td width="3%" align="center" >2.</td>
								<td width="auto" align="justify" >'.$nama_kkm.' / KKM Kapal Pengawas '.$nama_kapal.'</td>
							</tr>
							
							<tr>
								<td></td>
							</tr>
							
							<tr>
								<td width="20%" align="justify" >Alamat</td>
								<td width="2%" align="center" >:</td>
								<td width="auto" align="justify" >'.$alamat1.'</td>
							</tr>
							
							<tr>
								<td></td>
							</tr>
							
							<tr>
								<td width="100%" align="justify" >
								Menyatakan bahwa telah melakukan pengukuran sisa bbm akhir bulan dengan rincian sebagai berikut :</td>
							</tr>
				</table>';
		
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="1">
							
							<tr>
								<td width="73%" align="justify">&nbsp; Sisa BBM Bulan Sebelumnya, tanggal '.$this->indo_date($tanggal_sebelum).'</td>
								<td width="3%" align="center" >=</td>
								<td width="17%" align="right" >'.number_format($volume_sebelum).'&nbsp;&nbsp;&nbsp;</td>
								<td width="auto" align="justify" >&nbsp;Liter</td>
							</tr>
							<tr>
								<td width="73%" align="justify">&nbsp; Jumlah Pengisian</td>
								<td width="3%" align="center" >=</td>
								<td width="17%" align="right" >'.number_format($volume_pengisian).'&nbsp;&nbsp;&nbsp;</td>
								<td width="auto" align="justify" >&nbsp;Liter</td>
							</tr>
							<tr>
								<td width="73%" align="justify">&nbsp; Jumlah Pemakaian</td>
								<td width="3%" align="center" >=</td>
								<td width="17%" align="right" >'.number_format($volume_pemakaian).'&nbsp;&nbsp;&nbsp;</td>
								<td width="auto" align="justify" >&nbsp;Liter</td>
							</tr>
							<tr>
								<td width="73%" align="justify">&nbsp; Volume Akhir Bulan</td>
								<td width="3%" align="center" >=</td>
								<td width="17%" align="right" >'.number_format($volume_sisa).'&nbsp;&nbsp;&nbsp;</td>
								<td width="auto" align="justify" >&nbsp;Liter</td>
							</tr>
				</table>';
				
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">
							
							<tr>
								<td width="100%" align="justify" >
												Demikian Berita Acara Akhir Bulan ini dibuat dengan sebenar – benarnya untuk dapat dipergunakan sebagaimana mestinya.</td>
							</tr>
				</table>';
		
		
		
		// ISI -->  
		
		
		// <-- Fotter
		$tbl .= '<br><br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif; font-size:10px" border ="0">
							<tr>
								<td width="40%" align="center" >
									<b>'.$an_nakhoda.' Nakhoda KP. '.$nama_kapal.'</b><br><br><br><br><br>
						
									<b><u>'.$nama_nahkoda.'</u></b><br>
									<b>NIP. '.$nip_nahkoda.'</b>
								</td>
								<td width="20%" align="center" ></td>
								<td width="40%" align="center" >
									<b>'.$an_kkm.' KKM KP. '.$nama_kapal.'</b><br><br><br><br><br>
						
									<b><u>'.$nama_kkm.'</u></b><br>
									<b>NIP. '.$nip_kkm.'</b>
								</td>
							</tr>
							<tr>
								<td width="30%" align="center"></td>
								<td width="40%" align="center" >
									<b><br><br>Menyaksikan:</b><br>
									<b>'.$an_staf.' '.$jabatan_staf_pangkalan.'</b><br><br><br><br><br>
						
									<b><u>'.$nama_staf_pagkalan.'</u></b><br>
									<b>NIP. '.$nip_staf.'</b>
								</td>
								
								<td width="30%" align="center" ></td>
							</tr>
				</table> ';
		// Fotter -->
		
		
		$pdf->writeHTML($tbl, true, false, true, false, '');
        $pdf->Output($_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/dokumen/cetakan_ba/'.$filename.'.pdf', 'F');

	}
	
	public function cetak_ba_pemenerimaan_bbm($nomor_suratx, $filename) {
	    
	    header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
		
		$nomor_surat = str_replace('_','/',$nomor_suratx);
		
		$q_result2 = "SELECT kapal_code,nama_kapal,nomor_surat,REPLACE(jam_surat,':','.') AS jam_surat,tanggal_surat,
							TRIM(SUBSTRING(f_formattanggal(tanggal_surat),1,254)) AS ttanggal,keterangan_jenis_bbm,
							  penyedia, nama_nahkoda,nip_nahkoda,nama_kkm,nip_kkm,nama_staf_pagkalan,nip_staf,nama_kapal,nama_upt,
							  alamat1,alamat2,alamat3,kota, zona_waktu,jabatan_staf_pangkalan,no_so,lokasi_surat, an_staf, an_nakhoda, an_kkm
					  FROM
					(SELECT a.*,b.nama_kapal,m_upt_code,nama AS nama_upt,
					  IFNULL(alamat1,'') AS alamat1,IFNULL(alamat2,'') AS alamat2,IFNULL(alamat3,'') AS alamat3,IFNULL(kota,'') AS kota,  a.zona_waktu_surat AS zona_waktu
					  FROM bbm_kapaltrans a 
					  LEFT JOIN m_kapal b ON a.kapal_code = b.code_kapal LEFT JOIN m_upt c ON b.m_upt_code = c.code 
					  WHERE nomor_surat = '".$nomor_surat."' AND status_ba = '5') bbm_kapaltrans;";
		$coba = $this->db->query($q_result2);
		
		foreach($coba->result_array() AS $list){
			
			$nama_kapal 	= $list['nama_kapal'];
			$nomor_surat 	= $list['nomor_surat'];
			$jam_surat 		= $list['jam_surat'];
			$tanggal_surat 	= $list['tanggal_surat'];
			$penyedia		= $list['penyedia'];
			$keterangan_jenis_bbm= $list['keterangan_jenis_bbm'];
			$ttanggal 		= $list['ttanggal'];
			$nama_nahkoda 	= $list['nama_nahkoda'];
			$nip_nahkoda 	= $list['nip_nahkoda'];
			$nama_kkm 		= $list['nama_kkm'];
			$nip_kkm 		= $list['nip_kkm'];
			$nama_staf_pagkalan = $list['nama_staf_pagkalan'];
			$nip_staf 		= $list['nip_staf'];
			$nama_upt		= $list['nama_upt'];
			$alamat1		= $list['alamat1'];
			$alamat2		= $list['alamat2'];
			$alamat3		= $list['alamat3'];
			$kota		    = $list['lokasi_surat'];
			$no_so		    = $list['no_so'];
			$zona_waktu		= $list['zona_waktu'];
			$jabatan_staf_pangkalan		= $list['jabatan_staf_pangkalan'];
		}
		
		if($list['an_staf'] == 1){
			$an_staf = 'An. ';
		}else{
			$an_staf = '';
		}
		
		if($list['an_nakhoda'] == 1){
			$an_nakhoda = 'An. ';
		}else{
			$an_nakhoda = '';
		}
		
		if($list['an_kkm'] == 1){
			$an_kkm = 'An. ';
		}else{
			$an_kkm = '';
		}
		
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setPrintFooter(false);
        $pdf->setPrintHeader(false);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->AddPage('P','A4');
        $pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetFont('');
        
        
        $tbl = '<style type="text/css">
				hr.new5 {
					  border: 20px solid green;
					  border-radius: 5px;
					}
			</style>';
		// <-- Header
        $tbl .= '
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;" border ="0">
							<tr>
								<td width="17%" align="center" ><img align="center" width="120" height="120" src="'.$_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/assets/img/kkp3.png" border="0" /></td>
								<td width="82%" align="center" >
									<font size="15"><b>KEMENTERIAN KELAUTAN DAN PERIKANAN</b></font><br>
									<font size="17">DIREKTORAT JENDERAL PENGAWASAN</font><br>
									<font size="17">SUMBER DAYA KELAUTAN DAN PERIKANAN</font><br>
									<font size="12"><b><i>'.strtoupper($nama_upt).'</b></i></font><br>
									<font size="10">'.$alamat1.'</font><br>
									<font size="10">'.$alamat2.'</font><br>
									<font size="10">'.$alamat3.'</font>
								</td>
							</tr>
				</table> ';
			

		$style = array('width' => 1.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 58, 200, 58, $style);
		$style2 = array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 60, 200, 60, $style2);
		// Header -->
		
		$sql2    = "SELECT COUNT(*) AS con FROM `bbm_transdetail` WHERE nomor_surat = '".$nomor_surat."' "; 
		$query2  = $this->db->query($sql2);
		
		foreach ($query2->result() as $list2){
			
			$con = $list2->con;
		
		}
		
		if($con == 8 || $con == 9 || $con == 10){
		    
		    $px = '11px';
		}else{
		    $px = '12px';
		}
		
		// <-- ISI 
		
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:'.$px.'" border ="0">
                            
                            <tr>
							    <td width="100%" align="center" ><font size="12"><b><u>BERITA ACARA PENERIMAAN BBM</b></u></font></td>
								
							</tr>
							<tr>
							    <td width="100%" align="center" ><b>Nomor : '.$nomor_surat.'</b><br></td>
				
							</tr>
							<tr>
								<td width="100%" align="justify" >
								Pada hari ini '.$ttanggal.' pukul '.$jam_surat.' '.$zona_waktu.', bertempat di '.strtoupper($kota).', kami yang bertanda tangan
								dibawah ini : </td>
							</tr>
							<tr>
								<td></td>
							</tr>
							<tr>
								<td width="20%" align="justify" >Nama/Jabatan</td>
								<td width="2%" align="center" >:</td>
								<td width="3%" align="center" >1.</td>
								<td width="auto" align="justify" >'.$nama_nahkoda.' / Nakhoda Kapal Pengawas '.$nama_kapal.'</td>
							</tr>
							<tr>
								<td width="20%" align="justify" ></td>
								<td width="2%" align="center" >:</td>
								<td width="3%" align="center" >2.</td>
								<td width="auto" align="justify" >'.$nama_kkm.' / KKM Kapal Pengawas '.$nama_kapal.'</td>
							</tr>
							
							<tr>
								<td></td>
							</tr>
							
							<tr>
								<td width="20%" align="justify" >Alamat</td>
								<td width="2%" align="center" >:</td>
								<td width="auto" align="justify" >'.$alamat1.'</td>
							</tr>
							
							<tr>
								<td></td>
							</tr>
							
							<tr>
								<td width="100%" align="justify" >
								Menyatakan bahwa telah menerima hasil pekerjaan pengadaan BBM '.$keterangan_jenis_bbm.' dari penyedia '.$penyedia.' :</td>
							</tr>
				</table>';
		
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:'.$px.'" border ="1">
						<tr>
							<td width="5%" align="center"><b>No</b></td>
							<td align="center"><b>Transportasi</b></td>
							<td align="center"><b>Nomor SO</b></td>
							<td align="center"><b>Nomor DO</b></td>
							<td align="center"><b>Volume (Liter)</b></td>
							<td width="auto" align="center"><b>Keterangan</b></td>

						</tr>';
						$i   = 1;
						$jml = 0;
						$sql = "SELECT * FROM `bbm_transdetail` WHERE nomor_surat = '".$nomor_surat."' "; 
						$query = $this->db->query($sql);
						
						foreach ($query->result() as $list){
							
							$tbl .= '<tr>
										<td width="5%" align="center">'.$i++.'</td>
										<td align="center">'.$list->transportasi.'</td>
										<td align="center">'.$no_so.'</td>
										<td align="center">'.$list->no_do.'</td>
										<td align="center">'.number_format($list->volume_isi).' Liter</td>
										<td width="auto" align="center">'.$list->keterangan.'</td>
							</tr>';
							
							$jml += $list->volume_isi;
						}
						
						$tbl .= '<tr>
										<td colspan="4" align="center">JUMLAH</td>
										<td align="center">'.number_format($jml).' Liter</td>
										<td width="auto" align="center"></td>
							</tr>';
						
			$tbl .= ' </table>';
				
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:'.$px.'" border ="0">
							
							<tr>
								<td width="100%" align="justify" >
												Demikian Berita Acara Penerimaan BBM ini dibuat dengan sebenar – benarnya untuk dapat dipergunakan sebagaimana mestinya.</td>
							</tr>
				</table>';
		
		
		
		// ISI -->  
		
		
		// <-- Fotter
		$tbl .= '<br></br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif; font-size:10px" border ="0">
							<tr>
								<td width="40%" align="center" >
									<b>'.$an_kkm.' KKM KP. '.$nama_kapal.'</b><br><br><br><br><br>
						
									<b><u>'.$nama_kkm.'</u></b><br>
									<b>NIP. '.$nip_kkm.'<br></b>
								</td>
								<td width="20%" align="center" >
								
								</td>
								<td width="40%" align="center" >
									<b>Penyedia/Pengirim BBM</b>
                                    <br><b>'.$penyedia.'</b><br><br><br><br>
						
								    ________________________
								</td>
							</tr>
							
							<tr>
								<td width="40%" align="center">
									<b><br><br><br>'.$an_nakhoda.' Nakhoda KP. '.$nama_kapal.'</b><br><br><br><br><br>
						
									<b><u>'.$nama_nahkoda.'</u></b><br>
									<b>NIP. '.$nip_nahkoda.'</b> 
								</td>
									
								<td width="20%" align="center" >
									<b>Menyaksikan:</b>
									
								</td>
								
								<td width="40%" align="center" >
									<b><br><br><br>'.$an_staf.' '.$jabatan_staf_pangkalan.'</b><br><br><br><br><br>
						
									<b><u>'.$nama_staf_pagkalan.'</u></b><br>
									<b>NIP. '.$nip_staf.'</b>
								</td>
							</tr>
				</table> ';
		// Fotter -->
		
		
		$pdf->writeHTML($tbl, true, false, true, false, '');
        $pdf->Output($_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/dokumen/cetakan_ba/'.$filename.'.pdf', 'F');

	}

	public function cetak_ba_penitipan_bbm($nomor_suratx, $filename) {
	    
	    header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
		
		$nomor_surat = str_replace('_','/',$nomor_suratx);
		
	    $q_result2 = "SELECT kapal_code,nama_kapal,nomor_surat,REPLACE(jam_surat,':','.') AS jam_surat,tanggal_surat, volume_sisa,
		TRIM(SUBSTRING(f_formattanggal(tanggal_surat),1,254)) AS ttanggal,
		nama_nahkoda,nip_nahkoda,nama_kkm,nip_kkm,nama_staf_pagkalan,nip_staf,nama_kapal,nama_upt,
		alamat1,alamat2,alamat3,kota,zona_waktu,lok_surat,jabatan_staf_pangkalan,penyedia_penitip,nama_penitip,alamat_penitip,jabatan_penitip,penggunaan,lokasi_surat, an_staf, an_nakhoda, an_kkm
		FROM
		(SELECT a.*,b.nama_kapal,m_upt_code,nama AS nama_upt,
   		  IFNULL(alamat1,'') AS alamat1,IFNULL(alamat2,'') AS alamat2,ifnull(alamat3,'') AS alamat3, ifnull(kota,'') AS kota, a.zona_waktu_surat AS zona_waktu, a.lokasi_surat AS lok_surat
		  FROM bbm_kapaltrans a 
		  LEFT JOIN m_kapal b ON a.kapal_code = b.code_kapal LEFT JOIN m_upt c ON b.m_upt_code = c.code  
		  WHERE nomor_surat = '".$nomor_surat."') bbm_kapaltrans";
		 
		$coba = $this->db->query($q_result2);
		
		foreach($coba->result_array() AS $list){
			$nama_kapal 	= $list['nama_kapal'];
			$nomor_surat 	= $list['nomor_surat'];
			$jam_surat 		= $list['jam_surat'];
			$tanggal_surat 	= $list['tanggal_surat'];
			$volume_sisa 	= $list['volume_sisa'];
			$ttanggal 		= $list['ttanggal'];
			$nama_nahkoda 	= $list['nama_nahkoda'];
			$nip_nahkoda 	= $list['nip_nahkoda'];
			$nama_kkm 		= $list['nama_kkm'];
			$nip_kkm 		= $list['nip_kkm'];
			$nama_staf_pagkalan = $list['nama_staf_pagkalan'];
			$nip_staf 		= $list['nip_staf'];
			$nama_upt		= $list['nama_upt'];
			$alamat1		= $list['alamat1'];
			$alamat2		= $list['alamat2'];
			$alamat3		= $list['alamat3'];
			$lok_surat		= $list['lok_surat'];
			$zona_waktu		= $list['zona_waktu'];
			$jabatan_staf_pangkalan		= $list['jabatan_staf_pangkalan'];
			$penyedia_penitip		= $list['penyedia_penitip'];
			$nama_penitip		= $list['nama_penitip'];
			$jabatan_penitip		= $list['jabatan_penitip'];
			$alamat_penitip		= $list['alamat_penitip'];
			$penggunaan		= $list['penggunaan'];
			$kota		    = $list['lokasi_surat'];
		} 
		
	    if($list['an_staf'] == 1){
			$an_staf = 'An. ';
		}else{
			$an_staf = '';
		}
		
		if($list['an_nakhoda'] == 1){
			$an_nakhoda = 'An. ';
		}else{
			$an_nakhoda = '';
		}
		
		if($list['an_kkm'] == 1){
			$an_kkm = 'An. ';
		}else{
			$an_kkm = '';
		}
		
		
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setPrintFooter(false);
        $pdf->setPrintHeader(false);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->AddPage('P','A4');
        $pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetFont('');
        
        
        $tbl = '<style type="text/css">
				hr.new5 {
					  border: 20px solid green;
					  border-radius: 5px;
					}
			</style>';
		// <-- Header
        $tbl .= '
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;" border ="0">
							<tr>
								<td width="17%" align="center" ><img align="center" width="120" height="120" src="'.$_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/assets/img/kkp3.png" border="0" /></td>
								<td width="82%" align="center" >
									<font size="15"><b>KEMENTERIAN KELAUTAN DAN PERIKANAN</b></font><br>
									<font size="17">DIREKTORAT JENDERAL PENGAWASAN</font><br>
									<font size="17">SUMBER DAYA KELAUTAN DAN PERIKANAN</font><br>
									<font size="12"><b><i>'.strtoupper($nama_upt).'</b></i></font><br>
									<font size="10">'.$alamat1.'</font><br>
									<font size="10">'.$alamat2.'</font><br>
									<font size="10">'.$alamat3.'</font>
								</td>
							</tr>
				</table> ';
			

		$style = array('width' => 1.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 58, 200, 58, $style);
		$style2 = array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 60, 200, 60, $style2);
		// Header -->
		
		// <-- ISI 
		
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:11px" border ="0">
                            
                            <tr>
							    <td width="100%" align="center" ><font size="12"><b><u>BERITA ACARA PENITIPAN BBM</b></u></font></td>
								
							</tr> 
							<tr>
							    <td width="100%" align="center" ><b>Nomor : '.$nomor_surat.'</b><br></td>
				
							</tr>
							<tr>
								<td width="100%" align="justify" >
								Pada hari ini '.$ttanggal.' pukul '.$jam_surat.' '.$zona_waktu.', bertempat di '.strtoupper($kota).', kami yang bertanda tangan
								dibawah ini : </td>
							</tr>
							<tr>
								<td></td>
							</tr>
							<tr>
								<td width="20%" align="justify" >Nama/Jabatan</td>
								<td width="2%" align="center" >:</td>
								<td width="3%" align="center" >1.</td>
								<td width="auto" align="justify" >'.$nama_nahkoda.' / Nakhoda Kapal Pengawas '.$nama_kapal.'</td>
							</tr>
							<tr>
								<td width="20%" align="justify" ></td>
								<td width="2%" align="center" >:</td>
								<td width="3%" align="center" >2.</td>
								<td width="auto" align="justify" >'.$nama_kkm.' / KKM Kapal Pengawas '.$nama_kapal.'</td>
							</tr>
							
							<tr>
								<td width="20%" align="justify" >Alamat</td>
								<td width="2%" align="center" >:</td>
								<td width="auto" align="justify" >'.$alamat1.'</td>
							</tr>
							
							<tr>
								<td></td>
							</tr>
							
							<tr>
								<td width="100%" align="justify" >Selanjutnya disebut sebagai Pihak I selaku Penitip BBM, dan </td>
							</tr>
							
							<tr>
								<td></td>
							</tr>
							
							<tr>
								<td width="20%" align="justify" >Nama/Jabatan</td>
								<td width="2%" align="center" >:</td>
								<td width="3%" align="center" >1.</td>
								<td width="auto" align="justify" >'.$nama_penitip.' / '.$jabatan_penitip.' '.$penyedia_penitip.'</td>
							</tr>
						    <tr>
								<td width="20%" align="justify" >Alamat</td>
								<td width="2%" align="center" >:</td>
								<td width="auto" align="justify" >'.$alamat_penitip.'</td>
							</tr>
							
							<tr>
								<td></td>
							</tr>
							
							<tr>
								<td width="100%" align="justify" >Selanjutnya disebut sebagai Pihak II selaku Penitip BBM, dan </td>
							</tr>
							
							<tr>
								<td></td>
							</tr>
							
							<tr>
						    	<td width="100%" align="justify" >
								Menyatakan bahwa    : </td>
							</tr>
							
							<tr>
						    	<td width="2%" align="justify" ></td>
								<td width="4%" align="center" >1.</td>
								<td width="auto" align="justify" >Pihak I melakukan penitipan BBM sejumlah <b>'.number_format($penggunaan).'</b> Liter dalam kondisi baik kepada Pihak II.</td>
							</tr>
							<tr>
						    	<td width="2%" align="justify" ></td>
								<td width="4%" align="center" >2.</td>
								<td width="auto" align="justify" >Pihak II bersedia menerima penitipan BBM tersebut dalam rangka pengamanan BBM saat '.$nama_kapal.' melakukan perbaikan.</td>
							</tr>
							<tr>
						    	<td width="2%" align="justify" ></td>
								<td width="4%" align="center" >3.</td>
								<td width="auto" align="justify" >Pada saat kegiatan perbaikan selesai, Pihak II bersedia mengembalikan BBM yang dititipkan kepada Pihak I dalam keadaan utuh sebagaimanan semula.</td>
							</tr>
							
							<tr>
								<td></td>
							</tr>
							
							<tr>
								<td width="100%" align="justify" >
								Demikian Berita Acara Penitipan BBM ini dibuat dengan sebenar-benarnya untuk dapat dipergunakan sebagaimana mestinya.</td>
							</tr>
				</table>';
				
		
		
		// ISI -->  
		
		
		// <-- Fotter
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif; font-size:10px" border ="0">
							<tr>
								<td width="40%" align="center" >
								    <b>Pihak I (Pertama) </b><br>
									<b>'.$an_nakhoda.' Nakhoda KP. '.$nama_kapal.'</b><br><br><br><br><br>
						
									<b><u>'.$nama_nahkoda.'</u></b><br>
									<b>NIP. '.$nip_nahkoda.'</b>
								</td>
								<td width="20%" align="center" ></td>
								<td width="40%" align="center" >
									<b>Pihak II (Kedua) </b><br>
									<b>'.$jabatan_penitip.' '.$penyedia_penitip.'</b><br><br><br><br><br> 
						
									<b><u>'.$nama_penitip.'</u></b><br>
								</td>
							</tr>
							<tr>
								<td width="30%" align="center"></td>
								<td width="40%" align="center" >
									<b><br><br>Menyaksikan:</b><br>
									<b>'.$an_staf.' '.$jabatan_staf_pangkalan.'</b><br><br><br><br><br>
						
									<b><u>'.$nama_staf_pagkalan.'</u></b><br>
									<b>NIP. '.$nip_staf.'</b>
								</td>
								
								<td width="30%" align="center" ></td>
							</tr>
				</table> ';
		// Fotter -->
		
		
		$pdf->writeHTML($tbl, true, false, true, false, '');
        $pdf->Output($_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/dokumen/cetakan_ba/'.$filename.'.pdf', 'F');

	}

	public function cetak_ba_pengembalian_bbm($nomor_suratx, $filename) {

        header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
		
		$nomor_surat = str_replace('_','/',$nomor_suratx);
		
	    $q_result2 = "SELECT kapal_code,nama_kapal,nomor_surat,REPLACE(jam_surat,':','.') AS jam_surat,tanggal_surat, volume_sisa,
		TRIM(SUBSTRING(f_formattanggal(tanggal_surat),1,254)) AS ttanggal,
		nama_nahkoda,nip_nahkoda,nama_kkm,nip_kkm,nama_staf_pagkalan,nip_staf,nama_kapal,nama_upt,
		alamat1,alamat2,alamat3,kota,zona_waktu,lok_surat,jabatan_staf_pangkalan,penyedia_penitip,nama_penitip,alamat_penitip,jabatan_penitip,penggunaan,alamat_penyedia_penitip,keterangan_jenis_bbm, 
		link_modul_ba, an_staf, an_nakhoda, an_kkm
		FROM
		(SELECT a.*,b.nama_kapal,m_upt_code,nama AS nama_upt,
   		  IFNULL(alamat1,'') AS alamat1,IFNULL(alamat2,'') AS alamat2,ifnull(alamat3,'') AS alamat3, ifnull(kota,'') AS kota, a.zona_waktu_surat AS zona_waktu, a.lokasi_surat AS lok_surat
		  FROM bbm_kapaltrans a 
		  LEFT JOIN m_kapal b ON a.kapal_code = b.code_kapal LEFT JOIN m_upt c ON b.m_upt_code = c.code  
		  WHERE nomor_surat = '".$nomor_surat."') bbm_kapaltrans";
		 
		$coba = $this->db->query($q_result2);
		
		foreach($coba->result_array() AS $list){
			$nama_kapal 	= $list['nama_kapal'];
			$nomor_surat 	= $list['nomor_surat'];
			$jam_surat 		= $list['jam_surat'];
			$tanggal_surat 	= $list['tanggal_surat'];
			$volume_sisa 	= $list['volume_sisa'];
			$ttanggal 		= $list['ttanggal'];
			$nama_nahkoda 	= $list['nama_nahkoda'];
			$nip_nahkoda 	= $list['nip_nahkoda'];
			$nama_kkm 		= $list['nama_kkm'];
			$nip_kkm 		= $list['nip_kkm'];
			$nama_staf_pagkalan = $list['nama_staf_pagkalan'];
			$nip_staf 		= $list['nip_staf'];
			$nama_upt		= $list['nama_upt'];
			$alamat1		= $list['alamat1'];
			$alamat2		= $list['alamat2'];
			$alamat3		= $list['alamat3'];
			$lok_surat		= $list['lok_surat'];
			$zona_waktu		= $list['zona_waktu'];
			$jabatan_staf_pangkalan		= $list['jabatan_staf_pangkalan'];
			$penyedia_penitip		= $list['penyedia_penitip'];
			$nama_penitip		= $list['nama_penitip'];
			$jabatan_penitip		= $list['jabatan_penitip'];
			$alamat_penitip		= $list['alamat_penitip'];
			$penggunaan		= $list['penggunaan'];
			$alamat_penyedia_penitip	= $list['alamat_penyedia_penitip'];
			$keterangan_jenis_bbm		= $list['keterangan_jenis_bbm'];
			$link_modul_ba		= $list['link_modul_ba'];
		} 
		
	    if($list['an_staf'] == 1){
			$an_staf = 'An. ';
		}else{
			$an_staf = '';
		}
		
		if($list['an_nakhoda'] == 1){
			$an_nakhoda = 'An. ';
		}else{
			$an_nakhoda = '';
		}
		
		if($list['an_kkm'] == 1){
			$an_kkm = 'An. ';
		}else{
			$an_kkm = '';
		}
		
	    $sql2    = "SELECT nomor_surat as no_penitip, tanggal_surat AS tgl_penitip FROM `bbm_kapaltrans` WHERE nomor_surat = '".$link_modul_ba."' "; 
	    
		$query2  = $this->db->query($sql2);
		
		foreach ($query2->result_array() as $list2){
			
			$no_penitip	    = $list2['no_penitip'];
            $tgl_penitip	= $list2['tgl_penitip'];
		    
		}
		//var_dump($no_penitip);die();
		//$no_penitip	= "asdasd";
         //   $tgl_penitip	= "asssss";
		
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setPrintFooter(false);
        $pdf->setPrintHeader(false);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->AddPage('P','A4');
        $pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetFont('');
        
        
        $tbl = '<style type="text/css">
				hr.new5 {
					  border: 20px solid green;
					  border-radius: 5px;
					}
			</style>';
		// <-- Header
        $tbl .= '
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;" border ="0">
							<tr>
								<td width="17%" align="center" ><img align="center" width="120" height="120" src="'.$_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/assets/img/kkp3.png" border="0" /></td>
								<td width="82%" align="center" >
									<font size="15"><b>KEMENTERIAN KELAUTAN DAN PERIKANAN</b></font><br>
									<font size="17">DIREKTORAT JENDERAL PENGAWASAN</font><br>
									<font size="17">SUMBER DAYA KELAUTAN DAN PERIKANAN</font><br>
									<font size="12"><b><i>'.strtoupper($nama_upt).'</b></i></font><br>
									<font size="10">'.$alamat1.'</font><br>
									<font size="10">'.$alamat2.'</font><br>
									<font size="10">'.$alamat3.'</font>
								</td>
							</tr>
				</table> ';
			

		$style = array('width' => 1.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 58, 200, 58, $style);
		$style2 = array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 60, 200, 60, $style2);
		// Header -->
		
		// <-- ISI 
		
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:11px" border ="0">
                            
                            <tr>
							    <td width="100%" align="center" ><font size="12"><b><u>BERITA ACARA PENGEMBALIAN BBM</b></u></font></td>
								
							</tr> 
							<tr>
							    <td width="100%" align="center" ><b>Nomor : '.$nomor_surat.'</b><br></td>
				
							</tr>
							<tr>
								<td width="100%" align="justify" >Yang bertanda tangan di bawah ini :</td>
							</tr>
							
							<tr>
								<td></td>
							</tr>
							<tr>
								<td width="20%" align="justify" >Nama/Jabatan</td>
								<td width="2%" align="center" >:</td>
								<td width="3%" align="center" >1.</td>
								<td width="auto" align="justify" >'.$nama_nahkoda.' / Nakhoda Kapal Pengawas '.$nama_kapal.'</td>
							</tr>
							<tr>
								<td width="20%" align="justify" ></td>
								<td width="2%" align="center" >:</td>
								<td width="3%" align="center" >2.</td>
								<td width="auto" align="justify" >'.$nama_kkm.' / KKM Kapal Pengawas '.$nama_kapal.'</td>
							</tr>
							
							<tr>
								<td width="20%" align="justify" >Alamat</td>
								<td width="2%" align="center" >:</td>
								<td width="auto" align="justify" >'.$alamat1.'</td>
							</tr>
							<tr>
								<td width="100%" align="justify" >Selanjutnya disebut sebagai <b>Pihak Pertama</b></td>
							</tr>
							
							<tr>
								<td></td>
							</tr>
							
							<tr>
								<td width="20%" align="justify" >Nama/Jabatan</td>
								<td width="2%" align="center" >:</td>
								<td width="3%" align="center" >1.</td>
								<td width="auto" align="justify" >'.$nama_penitip.' / '.$jabatan_penitip.'</td>
							</tr>
						    <tr>
								<td width="20%" align="justify" >Alamat</td>
								<td width="2%" align="center" >:</td>
								<td width="auto" align="justify" >'.$alamat_penitip.'</td>
							</tr>
							<tr>
								<td width="100%" align="justify" >Selanjutnya disebut sebagai <b>Pihak Kedua</b></td>
							</tr>
							
							<tr>
								<td></td>
							</tr>
							<tr>
								<td width="100%" align="justify" >
								Pada hari ini '.$ttanggal.' pukul '.$jam_surat.' '.$zona_waktu.', bertempat di Galanagan Kapal '.$penyedia_penitip.' ('.$alamat_penyedia_penitip.'), 
								telah dilakukan Pengembalian BBM '.$keterangan_jenis_bbm.' dari pihak kedua ke pihak Pertama sebanyak <b>'.number_format($penggunaan).'</b> Liter berdasarkan Berita Acara Penitipan BBM Nomor '.$no_penitip.' tanggal '.$this->indo_date($tgl_penitip).'.
								</td> 
							</tr>
						        
								<tr>
								<td></td>
							</tr>
							<tr>
								<td width="100%" align="justify" >
								Demikian Berita Acara Pengembalian BBM ini dibuat dengan sebenar-benarnya untuk dapat dipergunakan sebagaimana mestinya.</td>
							</tr>
				</table>';
				
		
		
		// ISI -->  
		
		
		// <-- Fotter
		$tbl .= '<br></br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif; font-size:10px" border ="0">
                            <tr>
								<td align="center" >
									<b>Pihak Pertama</b>
									
								</td>
							</tr>
							<tr>
								<td width="40%" align="center" >
									<b>'.$an_kkm.' KKM KP. '.strtoupper($nama_kapal).'</b><br><br><br><br><br>
						
									<b><u>'.strtoupper($nama_kkm).'</u></b><br>
									<b>NIP. '.$nip_kkm.'<br></b>
								</td>
								<td width="20%" align="center" >
								
								</td>
								<td width="40%" align="center" >
								    <b>'.$an_nakhoda.' Nakhoda '.strtoupper($nama_kapal).'</b><br><br><br><br><br>
						
									<b><u>'.strtoupper($nama_nahkoda).'</u></b><br>
									<b>NIP. '.$nip_nahkoda.'</b> 
								</td>
							</tr>
							
							<tr>
								<td width="40%" align="center">
									
									<br><br><br><b>Pihak Kedua</b><br>
						            <b>'.$jabatan_penitip.'</b><br>
						            <b>'.$penyedia_penitip.'</b><br><br><br><br><br> 
						            
						            
								    <b><u>'.$nama_penitip.'</u></b><br>
								</td>
									
								<td width="20%" align="center" >
									<b>Menyaksikan</b>
									
								</td>
								
								<td width="40%" align="center" >
								    <b><br><br><br>Saksi</b><br>
									<b>'.$an_staf.' '.$jabatan_staf_pangkalan.'</b><br><br><br><br><br>
						
									<b><u>'.strtoupper($nama_staf_pagkalan).'</u></b><br>
									<b>NIP. '.$nip_staf.'</b>
								</td>
							</tr>
				</table> ';
		// Fotter -->
		
		
		$pdf->writeHTML($tbl, true, false, true, false, '');
        $pdf->Output($_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/dokumen/cetakan_ba/'.$filename.'.pdf', 'F');

    }
	
	public function cetak_ba_peminjaaman_bbm($nomor_suratx, $filename) {
    	
		$nomor_surat = str_replace('_','/',$nomor_suratx);
		
		$q_result2 = "SELECT kapal_code,nama_kapal,nomor_surat,REPLACE(jam_surat,':','.') AS jam_surat,tanggal_surat,
		TRIM(SUBSTRING(f_formattanggal(tanggal_surat),1,254)) AS ttanggal,volume_pemakaian,volume_sebelum,volume_sisa,nomer_persetujuan,tgl_persetujuan,deskripsi_persetujuan,
		TRIM(SUBSTRING(f_formattanggal(tanggal_surat),1,254)) AS ttanggal,nama_nahkoda,nip_nahkoda,nama_kkm,nip_kkm,nama_staf_pagkalan,nip_staf,nama_kapal,nama_upt,
		alamat1,alamat2,alamat3,kota, zona_waktu, lok_surat, jabatan_staf_pangkalan, an_staf, an_nakhoda, an_kkm,nip_nahkoda_temp,an_nakhoda_temp,an_kkm_temp,nama_kkm_temp,nip_kkm_temp,
		pangkat_nahkoda_temp,keterangan_jenis_bbm, nama_kapal_p,pangkat_nahkoda,nama_nahkoda_temp,pangkat_nahkoda_temp,sebab_temp,lokasi_surat,volume_sebelum
		FROM
		(SELECT e.deskripsi_persetujuan, a.*,b.nama_kapal,d.nama_kapal as nama_kapal_p, b.m_upt_code,nama AS nama_upt,
   		  IFNULL(alamat1,'') AS alamat1,IFNULL(alamat2,'') AS alamat2,ifnull(alamat3,'') AS alamat3, ifnull(kota,'') AS kota, a.zona_waktu_surat AS zona_waktu, a.lokasi_surat AS lok_surat 
		  FROM bbm_kapaltrans a 
		  LEFT JOIN m_kapal b ON a.kapal_code = b.code_kapal 
		  LEFT JOIN m_upt c ON b.m_upt_code = c.code  
		  LEFT JOIN m_kapal d ON a.kapal_code_temp = d.code_kapal 
		  LEFT JOIN m_persetujuan e ON e.id = a.m_persetujuan_id
		  WHERE nomor_surat = '".$nomor_surat."') bbm_kapaltrans";
		// var_dump($list);die(); 
		$coba = $this->db->query($q_result2);
		
		foreach($coba->result_array() AS $list){
			
			$nama_kapal 	= $list['nama_kapal'];
			$nama_kapal_p 	= $list['nama_kapal_p'];
			$nomor_surat 	= $list['nomor_surat'];
			$jam_surat 		= $list['jam_surat'];
			$tanggal_surat 	= $list['tanggal_surat'];
			$volume_sebelum 	= $list['volume_sebelum'];
			$volume_pemakaian 	= $list['volume_pemakaian'];
			$volume_sisa 	= $list['volume_sisa'];
			$ttanggal 		= $list['ttanggal'];
			$nama_nahkoda 	= $list['nama_nahkoda'];
			$pangkat_nahkoda 	= $list['pangkat_nahkoda'];
			$nama_nahkoda_peminjam 	= $list['nama_nahkoda_temp'];
			$pangkat_nahkoda_peminjam 	= $list['pangkat_nahkoda_temp'];
			$nip_nahkoda 	= $list['nip_nahkoda'];
			$nip_nahkoda_peminjam 	= $list['nip_nahkoda_temp'];
			$nama_kkm 		= $list['nama_kkm'];
			$nama_kkm_peminjam 		= $list['nama_kkm_temp'];
			$nip_kkm 		= $list['nip_kkm'];
			$nip_kkm_peminjam 		= $list['nip_kkm_temp'];
			$nama_staf_pagkalan = $list['nama_staf_pagkalan'];
			$nip_staf 		= $list['nip_staf'];
			$nama_upt		= $list['nama_upt'];
			$alamat1		= $list['alamat1'];
			$alamat2		= $list['alamat2'];
			$alamat3		= $list['alamat3'];
			$lok_surat		    = $list['lok_surat'];
			$zona_waktu		    = $list['zona_waktu'];
			$jabatan_staf_pangkalan		    = $list['jabatan_staf_pangkalan'];
			$keterangan_jenis_bbm		    = $list['keterangan_jenis_bbm'];
			$sebab_peminjaman		    = $list['sebab_temp'];
			$lokasi_surat		    = $list['lokasi_surat'];
			$nomer_persetujuan		    = $list['nomer_persetujuan'];
			$tgl_persetujuan		    = $list['tgl_persetujuan'];
			$deskripsi_persetujuan		    = $list['deskripsi_persetujuan'];
		} 
		
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setPrintFooter(false);
        $pdf->setPrintHeader(false);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->AddPage('P','A4');
        $pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetFont('');
        
        
        $tbl = '<style type="text/css">
				hr.new5 {
					  border: 20px solid green;
					  border-radius: 5px;
					}
			</style>';
		// <-- Header
        $tbl .= '
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;" border ="0">
							<tr>
								<td width="15%" align="center" ><img align="center" width="120" height="120" src="'.$_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/assets/img/kkp3.png" border="0" /></td>
								<td width="85%" align="center" >
									<font size="15"><b>KEMENTERIAN KELAUTAN DAN PERIKANAN</b></font><br>
									<font size="17">DIREKTORAT JENDERAL PENGAWASAN</font><br>
									<font size="17">SUMBER DAYA KELAUTAN DAN PERIKANAN</font><br>
									<font size="12"><b><i>'.strtoupper($nama_upt).'</b></i></font><br>
									<font size="10">'.$alamat1.'</font><br>
									<font size="10">'.$alamat2.'</font><br>
									<font size="10">'.$alamat3.'</font>
								</td>
							</tr>
				</table> ';
			

		$style = array('width' => 1.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 58, 200, 58, $style);
		$style2 = array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 60, 200, 60, $style2);
		// Header -->
		
		
		// <-- ISI 
		if($list['an_staf'] == 1){
			$an_staf = 'An. ';
		}else{
			$an_staf = '';
		}
		
		if($list['an_nakhoda'] == 1){
			$an_nakhoda = 'An. ';
		}else{
			$an_nakhoda = '';
		}
		
		if($list['an_kkm'] == 1){
			$an_kkm = 'An. ';
		}else{
			$an_kkm = '';
		}
		
		if($list['an_nakhoda_temp'] == 1){
			$an_nakhoda_temp = 'An. ';
		}else{
			$an_nakhoda_temp = '';
		}
		
		if($list['an_kkm_temp'] == 1){
			$an_kkm_temp = 'An. ';
		}else{
			$an_kkm_temp = '';
		}
		
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">
                
                            <tr>
							   
								<td width="100%" align="center" ><font size="12"><b><u>BERITA ACARA PEMINJAMAN BBM</b></u></font></td>
								
							</tr>
							<tr>
								<td width="100%" align="center" ><b>Nomor : '.$nomor_surat.'</b><br></td>
					
							</tr>
							<tr>
								<td width="100%" align="justify" >Yang bertanda tangan di bawah ini :</td>
							</tr>
							<tr>
								<td width="4%" align="justify" >1.</td>
								<td width="20%" align="justify" >Nama</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$nama_nahkoda_peminjam.'</td>
								
							</tr>
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="20%" align="justify" >Pangkat/Gol</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$pangkat_nahkoda_peminjam.'</td>
								
							</tr>
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="20%" align="justify" >Jabatan</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$an_nakhoda_temp .'Nakhoda KP '.$nama_kapal_p.'</td>
								
							</tr>
							
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="80%" align="justify" >Selanjutnya disebut sebagai <b>Pihak I selaku peminjam</b></td>
							</tr>
							<tr>
								<td width="4%" align="justify" >2.</td>
								<td width="20%" align="justify" >Nama</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$nama_nahkoda.'</td>
								
							</tr>
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="20%" align="justify" >Pangkat/Gol</td>
								<td width="3%" align="justify" >:</td>
								<td width="30%" align="justify" >'.$pangkat_nahkoda.'</td>
								
							</tr>
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="20%" align="justify" >Jabatan</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$an_nakhoda .'Nakhoda KP '.$nama_kapal.'</td>
								
							</tr>
							
							<tr>
								<td width="4%" align="justify"></td>
								<td width="80%" align="justify" >Selanjutnya disebut sebagai <b>Pihak II selaku pemberi pinjaman</b></td>
								
							</tr>
						</table>';
			
							
			$tbl .= '<table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">	
			                <tr>
			                    <td></td>
			                </tr>
							<tr>
							    
							    
								<td width="100%" align="justify">Pada hari ini '.$ttanggal.' pukul  '.$jam_surat.' '.$zona_waktu.' bertempat di '.$lokasi_surat.' berdasarkan Surat Persetujuan dari '.$deskripsi_persetujuan.'
								Nomor '.$nomer_persetujuan.' tanggal '.$this->indo_date($tgl_persetujuan).', telah dilakukan peminjaman BBM '.$keterangan_jenis_bbm.' dari PIHAK II ke PIHAK I sebanyak <b>'.number_format($volume_pemakaian).'</b> liter. Adapun peminjaman BBM ini di karenakan <b>'.$sebab_peminjaman.'</b>
								
								</td>
							</tr>
				</table>';
				
				
		$tbl .= '<br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">
                            <tr>
			                    <td></td>
			                </tr>
					
					<tr>
						<td width="100%" align="justify" >Demikian Berita Acara Peminjaman BBM ini dibuat dengan sebenar – benarnya untuk dapat dipergunakan sebagaimana mestinya. </td>
					</tr>
				</table>';
		// ISI -->
		
		// <-- Fotter
		
		
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif; font-size:10px" border ="0">
					<tr>	
						<td width="40%" align="center" >
							<b>Pihak I</b>
						</td>
						
						<td width="20%" align="center" ></td>
						
						<td width="40%" align="center" >
							<b>PIHAK II</b>
						</td>
					</tr>
					<tr>	
						<td width="40%" align="center" >
							<b>'.$an_nakhoda_temp.' Nakhoda KP. '.$nama_kapal_p.'</b><br><br><br><br><br>
					
							<b><u>'.$nama_nahkoda_peminjam.'</u></b><br>
							<b>NIP. '.$nip_nahkoda_peminjam.'</b><br>
						</td>
						
						<td width="20%" align="center" ></td>
						
						<td width="40%" align="center" >
							<b>'.$an_nakhoda.' Nakhoda KP. '.$nama_kapal.'</b><br><br><br><br><br>
					
							<b><u>'.$nama_nahkoda.'</u></b><br>
							<b>NIP. '.$nip_nahkoda.'</b>
						</td>
						
					</tr>
					
					<tr>
						<td width="40%" align="center" >
							<b>'.$an_kkm_temp.' KKM KP. '.$nama_kapal_p.'</b><br><br><br><br><br>
					
							<b><u>'.$nama_kkm_peminjam.'</u></b><br>
							<b>NIP. '.$nip_kkm_peminjam.'</b><br>
						</td>
						
						<td width="20%" align="center" >
							
							<b><br><br>Menyaksikan:</b><br>
							<b>'.$an_staf.' '.$jabatan_staf_pangkalan.'</b><br><br><br><br><br>
							
						</td>
						<td width="40%" align="center" >
							<b>'.$an_kkm.' KKM KP. '.$nama_kapal.'</b><br><br><br><br><br>
					
							<b><u>'.$nama_kkm.'</u></b><br>
							<b>NIP. '.$nip_kkm.'</b>
						</td>
						
					</tr>
					<tr>
						<td width="30%" align="center"></td>
						<td width="40%" align="center" >
							
					
							<b><u>'.$nama_staf_pagkalan.'</u></b><br>
							<b>NIP. '.$nip_staf.'</b>
						</td>
						
						<td width="30%" align="center" ></td>
					</tr>
				</table> ';
		// Fotter -->
		
		$pdf->writeHTML($tbl, true, false, true, false, '');
        $pdf->Output($_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/dokumen/cetakan_ba/'.$filename.'.pdf', 'F');

	}
	
	public function cetak_ba_penerimaan_pinjaman_bbm($nomor_suratx, $filename) {
    	
		$nomor_surat = str_replace('_','/',$nomor_suratx);
		
		$q_result2 = "SELECT kapal_code,nama_kapal,nomor_surat,REPLACE(jam_surat,':','.') AS jam_surat,tanggal_surat, volume_pemakaian,
		TRIM(SUBSTRING(f_formattanggal(tanggal_surat),1,254)) AS ttanggal,volume_pengisian,volume_sebelum,volume_sisa,link_modul_temp,
		TRIM(SUBSTRING(f_formattanggal(tanggal_surat),1,254)) AS ttanggal,nama_nahkoda,nip_nahkoda,nama_kkm,nip_kkm,nama_staf_pagkalan,nip_staf,nama_kapal,nama_upt,
		alamat1,alamat2,alamat3,kota, zona_waktu, lok_surat, jabatan_staf_pangkalan, an_staf, an_nakhoda, an_kkm,nip_nahkoda_temp,an_nakhoda_temp,an_kkm_temp,nama_kkm_temp,nip_kkm_temp,
		pangkat_nahkoda_temp,keterangan_jenis_bbm, nama_kapal_p,pangkat_nahkoda,nama_nahkoda_temp,pangkat_nahkoda_temp,sebab_temp,lokasi_surat
		FROM
		(SELECT a.*,b.nama_kapal,d.nama_kapal as nama_kapal_p, b.m_upt_code,nama AS nama_upt,
   		  IFNULL(alamat1,'') AS alamat1,IFNULL(alamat2,'') AS alamat2,ifnull(alamat3,'') AS alamat3, ifnull(kota,'') AS kota, a.zona_waktu_surat AS zona_waktu, a.lokasi_surat AS lok_surat 
		  FROM bbm_kapaltrans a 
		  LEFT JOIN m_kapal b ON a.kapal_code = b.code_kapal 
		  LEFT JOIN m_upt c ON b.m_upt_code = c.code  
		  LEFT JOIN m_kapal d ON a.kapal_code_temp = d.code_kapal 
		  WHERE nomor_surat = '".$nomor_surat."') bbm_kapaltrans";
		 
		$coba = $this->db->query($q_result2);
		
		foreach($coba->result_array() AS $list){
			$nama_kapal 	= $list['nama_kapal'];
			$nama_kapal_p 	= $list['nama_kapal_p'];
			$nomor_surat 	= $list['nomor_surat'];
			$jam_surat 		= $list['jam_surat'];
			$tanggal_surat 	= $list['tanggal_surat'];
			$volume_pengisian 	= $list['volume_pengisian'];
			$ttanggal 		= $list['ttanggal'];
			$nama_nahkoda 	= $list['nama_nahkoda'];
			$pangkat_nahkoda 	= $list['pangkat_nahkoda'];
			$nama_nahkoda_temp 	= $list['nama_nahkoda_temp'];
			$pangkat_nahkoda_temp 	= $list['pangkat_nahkoda_temp'];
			$nip_nahkoda 	= $list['nip_nahkoda'];
			$nip_nahkoda_temp 	= $list['nip_nahkoda_temp'];
			$nama_kkm 		= $list['nama_kkm'];
			$nama_kkm_temp 		= $list['nama_kkm_temp'];
			$nip_kkm 		= $list['nip_kkm'];
			$nip_kkm_temp 		= $list['nip_kkm_temp'];
			$nama_staf_pagkalan = $list['nama_staf_pagkalan'];
			$nip_staf 		= $list['nip_staf'];
			$nama_upt		= $list['nama_upt'];
			$alamat1		= $list['alamat1'];
			$alamat2		= $list['alamat2'];
			$alamat3		= $list['alamat3'];
			$lok_surat		    = $list['lok_surat'];
			$zona_waktu		    = $list['zona_waktu'];
			$jabatan_staf_pangkalan		    = $list['jabatan_staf_pangkalan'];
			$keterangan_jenis_bbm		    = $list['keterangan_jenis_bbm'];
			$sebab_peminjaman		    = $list['sebab_temp'];
			$lokasi_surat		    = $list['lokasi_surat'];
			$link_modul_temp		    = $list['link_modul_temp'];
			
		} 
		
		$q_result3 = "SELECT *
		  FROM bbm_kapaltrans a 
		  WHERE nomor_surat = '".$link_modul_temp."' ";
		 
		$coba3 = $this->db->query($q_result3);
		
		foreach($coba3->result_array() AS $list3){
		
			$nomor_suratx 	= $list3['nomor_surat'];
			
			$tanggal_suratx 	= $list3['tanggal_surat'];
			
			
		} 
		
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setPrintFooter(false);
        $pdf->setPrintHeader(false);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->AddPage('P','A4');
        $pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetFont('');
        
        
        $tbl = '<style type="text/css">
				hr.new5 {
					  border: 20px solid green;
					  border-radius: 5px;
					}
			</style>';
		// <-- Header
        $tbl .= '
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;" border ="0">
							<tr>
								<td width="15%" align="center" ><img align="center" width="120" height="120" src="'.$_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/assets/img/kkp3.png" border="0" /></td>
								<td width="85%" align="center" >
									<font size="15"><b>KEMENTERIAN KELAUTAN DAN PERIKANAN</b></font><br>
									<font size="17">DIREKTORAT JENDERAL PENGAWASAN</font><br>
									<font size="17">SUMBER DAYA KELAUTAN DAN PERIKANAN</font><br>
									<font size="12"><b><i>'.strtoupper($nama_upt).'</b></i></font><br>
									<font size="10">'.$alamat1.'</font><br>
									<font size="10">'.$alamat2.'</font><br>
									<font size="10">'.$alamat3.'</font>
								</td>
							</tr>
				</table> ';
			

		$style = array('width' => 1.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 58, 200, 58, $style);
		$style2 = array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 60, 200, 60, $style2);
		// Header -->
		
		
		// <-- ISI 
		if($list['an_staf'] == 1){
			$an_staf = 'An. ';
		}else{
			$an_staf = '';
		}
		
		if($list['an_nakhoda'] == 1){
			$an_nakhoda = 'An. ';
		}else{
			$an_nakhoda = '';
		}
		
		if($list['an_kkm'] == 1){
			$an_kkm = 'An. ';
		}else{
			$an_kkm = '';
		}
		
		if($list['an_nakhoda_temp'] == 1){
			$an_nakhoda_temp = 'An. ';
		}else{
			$an_nakhoda_temp = '';
		}
		
		if($list['an_kkm_temp'] == 1){
			$an_kkm_temp = 'An. ';
		}else{
			$an_kkm_temp = '';
		}
		
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">
                
                            <tr>
							   
								<td width="100%" align="center" ><font size="12"><b><u>BERITA ACARA PENERIMAAN PEMINJAMAN BBM</b></u></font></td>
								
							</tr>
							<tr>
								<td width="100%" align="center" ><b>Nomor : '.$nomor_surat.'</b><br></td>
					
							</tr>
							<tr>
								<td width="100%" align="justify" >Yang bertanda tangan di bawah ini :</td>
							</tr>
							
							<tr>
								<td width="4%" align="justify" >1.</td>
								<td width="20%" align="justify" >Nama</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$nama_nahkoda.'</td>
								
							</tr>
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="20%" align="justify" >Pangkat/Gol</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$pangkat_nahkoda.'</td>
								
							</tr>
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="20%" align="justify" >Jabatan</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$an_nakhoda .'Nakhoda KP '.$nama_kapal.'</td>
								
							</tr>
							
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="80%" align="justify" >Selanjutnya disebut sebagai <b>Pihak I selaku penerima pinjaman</b></td>
							</tr>
							<tr>
								<td width="4%" align="justify" >2.</td>
								<td width="20%" align="justify" >Nama</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$nama_nahkoda_temp.'</td>
								
							</tr>
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="20%" align="justify" >Pangkat/Gol</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$pangkat_nahkoda_temp.'</td>
								
							</tr>
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="20%" align="justify" >Jabatan</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$an_nakhoda_temp .'Nakhoda KP '.$nama_kapal_p.'</td>
								
							</tr>
							
							
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="80%" align="justify" >Selanjutnya disebut sebagai <b>Pihak II selaku pemberi pinjaman</b></td>
								
							</tr>
						</table>';
			
							
			$tbl .= '<table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">				
							<tr>
			                    <td></td>
			                </tr>
							<tr>
							
								<td width="100%" align="justify">Pada hari ini '.$ttanggal.' pukul '.$jam_surat.' '.$zona_waktu.' bertempat di '.$lokasi_surat.' berdasarkan Berita Acara Peminjaman BBM Nomor '.$nomor_suratx.' tanggal '.$this->indo_date($tanggal_suratx).', telah dilakukan penerimaan BBM '.$keterangan_jenis_bbm.' dari PIHAK II ke PIHAK I sebanyak <b>'.number_format($volume_pengisian).'</b> liter.
								
									
								</td>
							</tr>
				</table>';
				
				
		$tbl .= '<br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">
					<tr>
			                    <td></td>
			                </tr>		
					<tr>
						<td width="100%" align="justify" >Demikian Berita Acara Penerimaan Peminjaman BBM ini dibuat dengan sebenar – benarnya untuk dapat dipergunakan sebagaimana mestinya. </td>
					</tr>
				</table>';
		// ISI -->
		
		// <-- Fotter
		
		
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif; font-size:10px" border ="0">
					<tr>	
						<td width="40%" align="center" >
							<b>Pihak I</b>
						</td>
						
						<td width="20%" align="center" ></td>
						
						<td width="40%" align="center" >
							<b>PIHAK II</b>
						</td>
					</tr>
					<tr>	
						<td width="40%" align="center" >
							<b>'.$an_nakhoda.' Nakhoda KP. '.$nama_kapal.'</b><br><br><br><br><br>
					
							<b><u>'.$nama_nahkoda.'</u></b><br>
							<b>NIP. '.$nip_nahkoda.'</b>
						</td>

						
						<td width="20%" align="center" ></td>
						
						<td width="40%" align="center" >
							<b>'.$an_nakhoda_temp.' Nakhoda KP. '.$nama_kapal_p.'</b><br><br><br><br><br>
					
							<b><u>'.$nama_nahkoda_temp.'</u></b><br>
							<b>NIP. '.$nip_nahkoda_temp.'</b><br>
						</td>
						
												
					</tr>
					
					<tr>
					
						<td width="40%" align="center" >
							<b>'.$an_kkm.' KKM KP. '.$nama_kapal.'</b><br><br><br><br><br>
					
							<b><u>'.$nama_kkm.'</u></b><br>
							<b>NIP. '.$nip_kkm.'</b>
						</td>
						
						
						
						<td width="20%" align="center" >
							
							<b><br><br>Menyaksikan:</b><br>
							<b>'.$an_staf.' '.$jabatan_staf_pangkalan.'</b><br><br><br><br><br>
							
						</td>
						
						<td width="40%" align="center" >
							<b>'.$an_kkm_temp.' KKM KP. '.$nama_kapal_p.'</b><br><br><br><br><br>
					
							<b><u>'.$nama_kkm_temp.'</u></b><br>
							<b>NIP. '.$nip_kkm_temp.'</b><br>
						</td>
						
					</tr>
					<tr>
						<td width="30%" align="center"></td>
						<td width="40%" align="center" >
							
					
							<b><u>'.$nama_staf_pagkalan.'</u></b><br>
							<b>NIP. '.$nip_staf.'</b>
						</td>
						
						<td width="30%" align="center" ></td>
					</tr>
				</table> ';
		// Fotter -->
		
		$pdf->writeHTML($tbl, true, false, true, false, '');
        $pdf->Output($_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/dokumen/cetakan_ba/'.$filename.'.pdf', 'F');

	}
	
	public function cetak_ba_pengembalian_pinjaman_bbm($nomor_suratx, $filename) {
    	
		$nomor_surat = str_replace('_','/',$nomor_suratx);
		
		$q_result2 = "SELECT kapal_code,nama_kapal,nomor_surat,REPLACE(jam_surat,':','.') AS jam_surat,tanggal_surat, volume_pemakaian,volume_sebelum,link_modul_ba,
		TRIM(SUBSTRING(f_formattanggal(tanggal_surat),1,254)) AS ttanggal,volume_pemakaian,volume_sebelum,volume_sisa,link_modul_temp,
		TRIM(SUBSTRING(f_formattanggal(tanggal_surat),1,254)) AS ttanggal,nama_nahkoda,nip_nahkoda,nama_kkm,nip_kkm,nama_staf_pagkalan,nip_staf,nama_kapal,nama_upt,
		alamat1,alamat2,alamat3,kota, zona_waktu, lok_surat, jabatan_staf_pangkalan, an_staf, an_nakhoda, an_kkm,nip_nahkoda_temp,an_nakhoda_temp,an_kkm_temp,nama_kkm_temp,nip_kkm_temp,
		pangkat_nahkoda_temp,keterangan_jenis_bbm, nama_kapal_p,pangkat_nahkoda,nama_nahkoda_temp,pangkat_nahkoda_temp,sebab_temp,lokasi_surat
		FROM
		(SELECT a.*,b.nama_kapal,d.nama_kapal as nama_kapal_p, b.m_upt_code,nama AS nama_upt,
   		  IFNULL(alamat1,'') AS alamat1,IFNULL(alamat2,'') AS alamat2,ifnull(alamat3,'') AS alamat3, ifnull(kota,'') AS kota, a.zona_waktu_surat AS zona_waktu, a.lokasi_surat AS lok_surat 
		  FROM bbm_kapaltrans a 
		  LEFT JOIN m_kapal b ON a.kapal_code = b.code_kapal 
		  LEFT JOIN m_upt c ON b.m_upt_code = c.code  
		  LEFT JOIN m_kapal d ON a.kapal_code_temp = d.code_kapal 
		  WHERE nomor_surat = '".$nomor_surat."') bbm_kapaltrans";
		 
		$coba = $this->db->query($q_result2);
		
		foreach($coba->result_array() AS $list){
			$nama_kapal 	= $list['nama_kapal'];
			$nama_kapal_p 	= $list['nama_kapal_p'];
			$nomor_surat 	= $list['nomor_surat'];
			$jam_surat 		= $list['jam_surat'];
			$tanggal_surat 	= $list['tanggal_surat'];
			$volume_pemakaian 	= $list['volume_pemakaian'];
			$ttanggal 		= $list['ttanggal'];
			$nama_nahkoda 	= $list['nama_nahkoda'];
			$pangkat_nahkoda 	= $list['pangkat_nahkoda'];
			$nama_nahkoda_temp 	= $list['nama_nahkoda_temp'];
			$pangkat_nahkoda_temp 	= $list['pangkat_nahkoda_temp'];
			$nip_nahkoda 	= $list['nip_nahkoda'];
			$nip_nahkoda_temp 	= $list['nip_nahkoda_temp'];
			$nama_kkm 		= $list['nama_kkm'];
			$nama_kkm_temp 		= $list['nama_kkm_temp'];
			$nip_kkm 		= $list['nip_kkm'];
			$nip_kkm_temp 		= $list['nip_kkm_temp'];
			$nama_staf_pagkalan = $list['nama_staf_pagkalan'];
			$nip_staf 		= $list['nip_staf'];
			$nama_upt		= $list['nama_upt'];
			$alamat1		= $list['alamat1'];
			$alamat2		= $list['alamat2'];
			$alamat3		= $list['alamat3'];
			$lok_surat		    = $list['lok_surat'];
			$zona_waktu		    = $list['zona_waktu'];
			$jabatan_staf_pangkalan		    = $list['jabatan_staf_pangkalan'];
			$keterangan_jenis_bbm		    = $list['keterangan_jenis_bbm'];
			$sebab_peminjaman		    = $list['sebab_temp'];
			$lokasi_surat		    = $list['lokasi_surat'];
			$link_modul_ba		    = $list['link_modul_ba'];
			$link_modul_temp		    = $list['link_modul_temp'];
		} 
		
		$q_result3 = "SELECT *
		  FROM bbm_kapaltrans a 
		  WHERE nomor_surat = '".$link_modul_temp."' ";
		 
		$coba3 = $this->db->query($q_result3);
		
		foreach($coba3->result_array() AS $list3){
		
			$nomor_suratx 	= $list3['nomor_surat'];
			
			$tanggal_suratx 	= $list3['tanggal_surat'];
			
			
		} 
		
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setPrintFooter(false);
        $pdf->setPrintHeader(false);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->AddPage('P','A4');
        $pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetFont('');
        
        
        $tbl = '<style type="text/css">
				hr.new5 {
					  border: 20px solid green;
					  border-radius: 5px;
					}
			</style>';
		// <-- Header
        $tbl .= '
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;" border ="0">
							<tr>
								<td width="15%" align="center" ><img align="center" width="120" height="120" src="'.$_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/assets/img/kkp3.png" border="0" /></td>
								<td width="85%" align="center" >
									<font size="15"><b>KEMENTERIAN KELAUTAN DAN PERIKANAN</b></font><br>
									<font size="17">DIREKTORAT JENDERAL PENGAWASAN</font><br>
									<font size="17">SUMBER DAYA KELAUTAN DAN PERIKANAN</font><br>
									<font size="12"><b><i>'.strtoupper($nama_upt).'</b></i></font><br>
									<font size="10">'.$alamat1.'</font><br>
									<font size="10">'.$alamat2.'</font><br>
									<font size="10">'.$alamat3.'</font>
								</td>
							</tr>
				</table> ';
			

		$style = array('width' => 1.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 58, 200, 58, $style);
		$style2 = array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 60, 200, 60, $style2);
		// Header -->
		
		
		// <-- ISI 
		if($list['an_staf'] == 1){
			$an_staf = 'An. ';
		}else{
			$an_staf = '';
		}
		
		if($list['an_nakhoda'] == 1){
			$an_nakhoda = 'An. ';
		}else{
			$an_nakhoda = '';
		}
		
		if($list['an_kkm'] == 1){
			$an_kkm = 'An. ';
		}else{
			$an_kkm = '';
		}
		
		if($list['an_nakhoda_temp'] == 1){
			$an_nakhoda_temp = 'An. ';
		}else{
			$an_nakhoda_temp = '';
		}
		
		if($list['an_kkm_temp'] == 1){
			$an_kkm_temp = 'An. ';
		}else{
			$an_kkm_temp = '';
		}
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">
                
                            <tr>
							   
								<td width="100%" align="center" ><font size="12"><b><u>BERITA ACARA PENGEMBALIAN PEMINJAMAN BBM</b></u></font></td>
								
							</tr>
							<tr>
								<td width="100%" align="center" ><b>Nomor : '.$nomor_surat.'</b><br></td>
					
							</tr>
							<tr>
								<td width="100%" align="justify" >Yang bertanda tangan di bawah ini :</td>
							</tr>
							
							<tr>
								<td width="4%" align="justify" >1.</td>
								<td width="20%" align="justify" >Nama</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$nama_nahkoda.'</td>
								
							</tr>
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="20%" align="justify" >Pangkat/Gol</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$pangkat_nahkoda.'</td>
								
							</tr>
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="20%" align="justify" >Jabatan</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$an_nakhoda .'Nakhoda KP '.$nama_kapal.'</td>
								
							</tr>
							
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="80%" align="justify" >Selanjutnya disebut sebagai <b>Pihak I selaku peminjam</b></td>
							</tr>
							<tr>
								<td width="4%" align="justify" >2.</td>
								<td width="20%" align="justify" >Nama</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$nama_nahkoda_temp.'</td>
								
							</tr>
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="20%" align="justify" >Pangkat/Gol</td>
								<td width="3%" align="justify" >:</td>
								<td width="30%" align="justify" >'.$pangkat_nahkoda_temp.'</td>
								
							</tr>
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="20%" align="justify" >Jabatan</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$an_nakhoda_temp .'Nakhoda KP '.$nama_kapal_p.'</td>
								
							</tr>
							
							
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="96%" align="justify" >Selanjutnya disebut sebagai <b>Pihak II selaku pemberi pinjaman</b></td>
								
							</tr>
						</table>';
			
							
			$tbl .= '<table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">				
							<tr>
							<td></td>
							</tr>
							<tr>
							
								<td width="100%" align="justify">Pada hari ini '.$ttanggal.' pukul '.$jam_surat.' '.$zona_waktu.' bertempat di '.$lokasi_surat.' berdasarkan Berita Acara Peminjaman BBM Nomor '.$nomor_suratx.' tanggal '.$this->indo_date($tanggal_suratx).', telah dilakukan pengembalian BBM '.$keterangan_jenis_bbm.' dari PIHAK I ke PIHAK II sebanyak <b>'.number_format($volume_pemakaian).'</b> liter.
								
									
								</td>
							</tr>
				</table>';
				
				
		$tbl .= '<br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">
						<tr>
							<td></td>
							</tr>	
					<tr>
						<td width="100%" align="justify" >Demikian Berita Acara Pengembalian Peminjaman BBM ini dibuat dengan sebenar – benarnya untuk dapat dipergunakan sebagaimana mestinya. </td>
					</tr>
				</table>';
		// ISI -->
		
		// <-- Fotter
		
		
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif; font-size:10px" border ="0">
					<tr>	
						<td width="40%" align="center" >
							<b>Pihak I</b>
						</td>
						
						<td width="20%" align="center" ></td>
						
						<td width="40%" align="center" >
							<b>PIHAK II</b>
						</td>
					</tr>
					<tr>	
						<td width="40%" align="center" >
							<b>'.$an_nakhoda.' Nakhoda KP. '.$nama_kapal.'</b><br><br><br><br><br>
					
							<b><u>'.$nama_nahkoda.'</u></b><br>
							<b>NIP. '.$nip_nahkoda.'</b>
						</td>

						
						<td width="20%" align="center" ></td>
						
						<td width="40%" align="center" >
							<b>'.$an_nakhoda_temp.' Nakhoda KP. '.$nama_kapal_p.'</b><br><br><br><br><br>
					
							<b><u>'.$nama_nahkoda_temp.'</u></b><br>
							<b>NIP. '.$nip_nahkoda_temp.'</b><br>
						</td>
						
												
					</tr>
					
					<tr>
					
						<td width="40%" align="center" >
							<b>'.$an_kkm.' KKM KP. '.$nama_kapal.'</b><br><br><br><br><br>
					
							<b><u>'.$nama_kkm.'</u></b><br>
							<b>NIP. '.$nip_kkm.'</b>
						</td>
						
						
						
						<td width="20%" align="center" >
							
							<b><br><br>Menyaksikan:</b><br>
							<b>'.$an_staf.' '.$jabatan_staf_pangkalan.'</b><br><br><br><br><br>
							
						</td>
						
						<td width="40%" align="center" >
							<b>'.$an_kkm_temp.' KKM KP. '.$nama_kapal_p.'</b><br><br><br><br><br>
					
							<b><u>'.$nama_kkm_temp.'</u></b><br>
							<b>NIP. '.$nip_kkm_temp.'</b><br>
						</td>
						
					</tr>
					<tr>
						<td width="30%" align="center"></td>
						<td width="40%" align="center" >
							
					
							<b><u>'.$nama_staf_pagkalan.'</u></b><br>
							<b>NIP. '.$nip_staf.'</b>
						</td>
						
						<td width="30%" align="center" ></td>
					</tr>
				</table> ';
		// Fotter -->
		
		$pdf->writeHTML($tbl, true, false, true, false, '');
        $pdf->Output($_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/dokumen/cetakan_ba/'.$filename.'.pdf', 'F');

	}
	
	public function cetak_ba_penerimaan_pengembalian_bbm($nomor_suratx, $filename) {
    	
		$nomor_surat = str_replace('_','/',$nomor_suratx);
		
		$q_result2 = "SELECT kapal_code,nama_kapal,nomor_surat,REPLACE(jam_surat,':','.') AS jam_surat,tanggal_surat,volume_pemakaian,
		TRIM(SUBSTRING(f_formattanggal(tanggal_surat),1,254)) AS ttanggal,volume_pengisian,volume_sebelum,volume_sisa,link_modul_temp,
		TRIM(SUBSTRING(f_formattanggal(tanggal_surat),1,254)) AS ttanggal,nama_nahkoda,nip_nahkoda,nama_kkm,nip_kkm,nama_staf_pagkalan,nip_staf,nama_kapal,nama_upt,
		alamat1,alamat2,alamat3,kota, zona_waktu, lok_surat, jabatan_staf_pangkalan, an_staf, an_nakhoda, an_kkm,nip_nahkoda_temp,an_nakhoda_temp,an_kkm_temp,nama_kkm_temp,nip_kkm_temp,
		pangkat_nahkoda_temp,keterangan_jenis_bbm, nama_kapal_p,pangkat_nahkoda,nama_nahkoda_temp,pangkat_nahkoda_temp,sebab_temp,lokasi_surat,volume_sebelum
		FROM
		(SELECT a.*,b.nama_kapal,d.nama_kapal as nama_kapal_p, b.m_upt_code,nama AS nama_upt,
   		  IFNULL(alamat1,'') AS alamat1,IFNULL(alamat2,'') AS alamat2,ifnull(alamat3,'') AS alamat3, ifnull(kota,'') AS kota, a.zona_waktu_surat AS zona_waktu, a.lokasi_surat AS lok_surat 
		  FROM bbm_kapaltrans a 
		  LEFT JOIN m_kapal b ON a.kapal_code = b.code_kapal 
		  LEFT JOIN m_upt c ON b.m_upt_code = c.code  
		  LEFT JOIN m_kapal d ON a.kapal_code_temp = d.code_kapal 
		  WHERE nomor_surat = '".$nomor_surat."') bbm_kapaltrans";
		// var_dump($q_result2);die();
		$coba = $this->db->query($q_result2);
		
		foreach($coba->result_array() AS $list){
			$nama_kapal 	= $list['nama_kapal'];
			$nama_kapal_p 	= $list['nama_kapal_p'];
			$nomor_surat 	= $list['nomor_surat'];
			$jam_surat 		= $list['jam_surat'];
			$tanggal_surat 	= $list['tanggal_surat'];
			$volume_pengisian 	= $list['volume_pengisian'];
			$ttanggal 		= $list['ttanggal'];
			$nama_nahkoda 	= $list['nama_nahkoda'];
			$pangkat_nahkoda 	= $list['pangkat_nahkoda'];
			$nama_nahkoda_peminjam 	= $list['nama_nahkoda_temp'];
			$pangkat_nahkoda_peminjam 	= $list['pangkat_nahkoda_temp'];
			$nip_nahkoda 	= $list['nip_nahkoda'];
			$nip_nahkoda_peminjam 	= $list['nip_nahkoda_temp'];
			$nama_kkm 		= $list['nama_kkm'];
			$nama_kkm_peminjam 		= $list['nama_kkm_temp'];
			$nip_kkm 		= $list['nip_kkm'];
			$nip_kkm_peminjam 		= $list['nip_kkm_temp'];
			$nama_staf_pagkalan = $list['nama_staf_pagkalan'];
			$nip_staf 		= $list['nip_staf'];
			$nama_upt		= $list['nama_upt'];
			$alamat1		= $list['alamat1'];
			$alamat2		= $list['alamat2'];
			$alamat3		= $list['alamat3'];
			$lok_surat		    = $list['lok_surat'];
			$zona_waktu		    = $list['zona_waktu'];
			$jabatan_staf_pangkalan		    = $list['jabatan_staf_pangkalan'];
			$keterangan_jenis_bbm		    = $list['keterangan_jenis_bbm'];
			$sebab_peminjaman		    = $list['sebab_temp'];
			$lokasi_surat		    = $list['lokasi_surat'];
			$link_modul_temp		    = $list['link_modul_temp'];
		} 
		
		$q_result3 = "SELECT *
		  FROM bbm_kapaltrans a 
		  WHERE nomor_surat = '".$link_modul_temp."' ";
		 
		$coba3 = $this->db->query($q_result3);
		
		foreach($coba3->result_array() AS $list3){
		
			$nomor_suratx 	= $list3['nomor_surat'];
			
			$tanggal_suratx 	= $list3['tanggal_surat'];
			
			
		} 
		
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setPrintFooter(false);
        $pdf->setPrintHeader(false);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->AddPage('P','A4');
        $pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetFont('');
        
        
        $tbl = '<style type="text/css">
				hr.new5 {
					  border: 20px solid green;
					  border-radius: 5px;
					}
			</style>';
		// <-- Header
        $tbl .= '
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;" border ="0">
							<tr>
								<td width="15%" align="center" ><img align="center" width="120" height="120" src="'.$_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/assets/img/kkp3.png" border="0" /></td>
								<td width="85%" align="center" >
									<font size="15"><b>KEMENTERIAN KELAUTAN DAN PERIKANAN</b></font><br>
									<font size="17">DIREKTORAT JENDERAL PENGAWASAN</font><br>
									<font size="17">SUMBER DAYA KELAUTAN DAN PERIKANAN</font><br>
									<font size="12"><b><i>'.strtoupper($nama_upt).'</b></i></font><br>
									<font size="10">'.$alamat1.'</font><br>
									<font size="10">'.$alamat2.'</font><br>
									<font size="10">'.$alamat3.'</font>
								</td>
							</tr>
				</table> ';
			

		$style = array('width' => 1.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 58, 200, 58, $style);
		$style2 = array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 60, 200, 60, $style2);
		// Header -->
		
		
		// <-- ISI 
		
		if($list['an_staf'] == 1){
			$an_staf = 'An. ';
		}else{
			$an_staf = '';
		}
		
		if($list['an_nakhoda'] == 1){
			$an_nakhoda = 'An. ';
		}else{
			$an_nakhoda = '';
		}
		
		if($list['an_kkm'] == 1){
			$an_kkm = 'An. ';
		}else{
			$an_kkm = '';
		}
		
		if($list['an_nakhoda_temp'] == 1){
			$an_nakhoda_temp = 'An. ';
		}else{
			$an_nakhoda_temp = '';
		}
		
		if($list['an_kkm_temp'] == 1){
			$an_kkm_temp = 'An. ';
		}else{
			$an_kkm_temp = '';
		}
		
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">
                
                            <tr>
							   
								<td width="100%" align="center" ><font size="12"><b><u>BERITA ACARA PENERIMAAN PENGEMBALIAN PEMINJAMAN BBM</b></u></font></td>
								
							</tr>
							<tr>
								<td width="100%" align="center" ><b>Nomor : '.$nomor_surat.'</b><br></td>
					
							</tr>
							<tr>
								<td width="100%" align="justify" >Yang bertanda tangan di bawah ini :</td>
							</tr>
							<tr>
								<td width="4%" align="justify" >1.</td>
								<td width="20%" align="justify" >Nama</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$nama_nahkoda.'</td>
								
							</tr>
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="20%" align="justify" >Pangkat/Gol</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$pangkat_nahkoda.'</td>
								
							</tr>
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="20%" align="justify" >Jabatan</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$an_nakhoda .'Nakhoda KP '.$nama_kapal.'</td>
								
							</tr>
							
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="96%" align="justify" >Selanjutnya disebut sebagai <b>Pihak I selaku penerima pengembalian peminjaman BBM</b></td>
							</tr>
							<tr>
								<td width="4%" align="justify" >2.</td>
								<td width="20%" align="justify" >Nama</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$nama_nahkoda_peminjam.'</td>
								
							</tr>
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="20%" align="justify" >Pangkat/Gol</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$pangkat_nahkoda_peminjam.'</td>
								
							</tr>
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="20%" align="justify" >Jabatan</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$an_nakhoda_temp .'Nakhoda KP '.$nama_kapal_p.'</td>
								
							</tr>
							
							
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="96%" align="justify" >Selanjutnya disebut sebagai <b>Pihak II selaku pemberi pengembalian peminjaman BBM</b></td>
								
							</tr>
						</table>';
			
							
			$tbl .= '<table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">				
							<tr>
			                    <td></td>
			                </tr>
							<tr>
							
								<td width="100%" align="justify">Pada hari ini '.$ttanggal.' pukul '.$jam_surat.' '.$zona_waktu.' bertempat di '.$lokasi_surat.' berdasarkan Berita Acara Pengembalian Pinjaman BBM Nomor '.$nomor_suratx.' tanggal '.$this->indo_date($tanggal_suratx).', telah dilakukan pengembalian peminjaman BBM '.$keterangan_jenis_bbm.' dari PIHAK II ke PIHAK I sebanyak <b>'.number_format($volume_pengisian).'</b> liter.
									
								</td>
							</tr>
				</table>';
				
		$tbl .= '<br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">
					<tr>
			                    <td></td>
			                </tr>		
					<tr>
						<td width="100%" align="justify" >Demikian Berita Acara Penerimaan Pengembalian Peminjaman BBM ini dibuat dengan sebenar – benarnya untuk dapat dipergunakan sebagaimana mestinya. </td>
					</tr>
				</table>';
		// ISI -->
		
		// <-- Fotter
		
		
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif; font-size:10px" border ="0">
					<tr>	
						<td width="40%" align="center" >
							<b>Pihak I</b>
						</td>
						
						<td width="20%" align="center" ></td>
						
						<td width="40%" align="center" >
							<b>PIHAK II</b>
						</td>
					</tr>
					<tr>	
						<td width="40%" align="center" >
							<b>'.$an_nakhoda_temp.' Nakhoda KP. '.$nama_kapal_p.'</b><br><br><br><br><br>
					
							<b><u>'.$nama_nahkoda_peminjam.'</u></b><br>
							<b>NIP. '.$nip_nahkoda_peminjam.'</b><br>
						</td>
						
						<td width="20%" align="center" ></td>
						
						<td width="40%" align="center" >
							<b>'.$an_nakhoda.' Nakhoda KP. '.$nama_kapal.'</b><br><br><br><br><br>
					
							<b><u>'.$nama_nahkoda.'</u></b><br>
							<b>NIP. '.$nip_nahkoda.'</b>
						</td>
						
					</tr>
					
					<tr>
						<td width="40%" align="center" >
							<b>'.$an_kkm_temp.' KKM KP. '.$nama_kapal_p.'</b><br><br><br><br><br>
					
							<b><u>'.$nama_kkm_peminjam.'</u></b><br>
							<b>NIP. '.$nip_kkm_peminjam.'</b><br>
						</td>
						
						<td width="20%" align="center" >
							
							<b><br><br>Menyaksikan:</b><br>
							<b>'.$an_staf.' '.$jabatan_staf_pangkalan.'</b><br><br><br><br><br>
							
						</td>
						<td width="40%" align="center" >
							<b>'.$an_kkm.' KKM KP. '.$nama_kapal.'</b><br><br><br><br><br>
					
							<b><u>'.$nama_kkm.'</u></b><br>
							<b>NIP. '.$nip_kkm.'</b>
						</td>
						
					</tr>
					<tr>
						<td width="30%" align="center"></td>
						<td width="40%" align="center" >
							
					
							<b><u>'.$nama_staf_pagkalan.'</u></b><br>
							<b>NIP. '.$nip_staf.'</b>
						</td>
						
						<td width="30%" align="center" ></td>
					</tr>
				</table> ';
		// Fotter -->
		
		$pdf->writeHTML($tbl, true, false, true, false, '');
        $pdf->Output($_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/dokumen/cetakan_ba/'.$filename.'.pdf', 'F');

	}
	
	public function cetak_ba_pemberi_hibah_bbm_kapal_pengawas($nomor_suratx, $filename) {
    	
		$nomor_surat = str_replace('_','/',$nomor_suratx);
		
		$q_result2 = "SELECT kapal_code,nama_kapal,nomor_surat,REPLACE(jam_surat,':','.') AS jam_surat,tanggal_surat, 
		TRIM(SUBSTRING(f_formattanggal(tanggal_surat),1,254)) AS ttanggal, volume_pemakaian,volume_sebelum,volume_sisa,nomer_persetujuan,tgl_persetujuan,deskripsi_persetujuan,
		TRIM(SUBSTRING(f_formattanggal(tanggal_surat),1,254)) AS ttanggal,nama_nahkoda,nip_nahkoda,nama_kkm,nip_kkm,nama_staf_pagkalan,nip_staf,nama_kapal,nama_upt,
		alamat1,alamat2,alamat3,kota, zona_waktu, lok_surat, jabatan_staf_pangkalan, an_staf, an_nakhoda, an_kkm,nip_nahkoda_temp,an_nakhoda_temp,an_kkm_temp,nama_kkm_temp,nip_kkm_temp,
		pangkat_nahkoda_temp,keterangan_jenis_bbm, nama_kapal_temp,pangkat_nahkoda,nama_nahkoda_temp,pangkat_nahkoda_temp,sebab_temp,lokasi_surat,volume_pemakaian
		FROM
		(SELECT e.deskripsi_persetujuan, a.*,b.nama_kapal,d.nama_kapal as nama_kapal_temp, b.m_upt_code,nama AS nama_upt,
   		  IFNULL(alamat1,'') AS alamat1,IFNULL(alamat2,'') AS alamat2,ifnull(alamat3,'') AS alamat3, ifnull(kota,'') AS kota, a.zona_waktu_surat AS zona_waktu, a.lokasi_surat AS lok_surat 
		  FROM bbm_kapaltrans a 
		  LEFT JOIN m_kapal b ON a.kapal_code = b.code_kapal 
		  LEFT JOIN m_upt c ON b.m_upt_code = c.code  
		  LEFT JOIN m_kapal d ON a.kapal_code_temp = d.code_kapal 
		  LEFT JOIN m_persetujuan e ON e.id = a.m_persetujuan_id
		  WHERE nomor_surat = '".$nomor_surat."') bbm_kapaltrans";
		 
		$coba = $this->db->query($q_result2);
		
		foreach($coba->result_array() AS $list){
			$nama_kapal 	= $list['nama_kapal'];
			$nama_kapal_temp 	= $list['nama_kapal_temp'];
			$nomor_surat 	= $list['nomor_surat'];
			$jam_surat 		= $list['jam_surat'];
			$tanggal_surat 	= $list['tanggal_surat'];
			$volume_pemakaian 	= $list['volume_pemakaian'];
			$ttanggal 		= $list['ttanggal'];
			$nama_nahkoda 	= $list['nama_nahkoda'];
			$pangkat_nahkoda 	= $list['pangkat_nahkoda'];
			$nama_nahkoda_temp 	= $list['nama_nahkoda_temp'];
			$pangkat_nahkoda_temp 	= $list['pangkat_nahkoda_temp'];
			$nip_nahkoda 	= $list['nip_nahkoda'];
			$nip_nahkoda_temp 	= $list['nip_nahkoda_temp'];
			$nama_kkm 		= $list['nama_kkm'];
			$nama_kkm_temp 		= $list['nama_kkm_temp'];
			$nip_kkm 		= $list['nip_kkm'];
			$nip_kkm_temp 		= $list['nip_kkm_temp'];
			$nama_staf_pagkalan = $list['nama_staf_pagkalan'];
			$nip_staf 		= $list['nip_staf'];
			$nama_upt		= $list['nama_upt'];
			$alamat1		= $list['alamat1'];
			$alamat2		= $list['alamat2'];
			$alamat3		= $list['alamat3'];
			$lok_surat		    = $list['lok_surat'];
			$zona_waktu		    = $list['zona_waktu'];
			$jabatan_staf_pangkalan		    = $list['jabatan_staf_pangkalan'];
			$keterangan_jenis_bbm		    = $list['keterangan_jenis_bbm'];
			$sebab_peminjaman		    = $list['sebab_temp'];
			$lokasi_surat		    = $list['lokasi_surat'];
			$nomer_persetujuan		    = $list['nomer_persetujuan'];
			$tgl_persetujuan		    = $list['tgl_persetujuan'];
			$deskripsi_persetujuan		    = $list['deskripsi_persetujuan'];
		} 
		
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setPrintFooter(false);
        $pdf->setPrintHeader(false);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->AddPage('P','A4');
        $pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetFont('');
        
        
        $tbl = '<style type="text/css">
				hr.new5 {
					  border: 20px solid green;
					  border-radius: 5px;
					}
			</style>';
		// <-- Header
        $tbl .= '
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;" border ="0">
							<tr>
								<td width="15%" align="center" ><img align="center" width="120" height="120" src="'.$_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/assets/img/kkp3.png" border="0" /></td>
								<td width="85%" align="center" >
									<font size="15"><b>KEMENTERIAN KELAUTAN DAN PERIKANAN</b></font><br>
									<font size="17">DIREKTORAT JENDERAL PENGAWASAN</font><br>
									<font size="17">SUMBER DAYA KELAUTAN DAN PERIKANAN</font><br>
									<font size="12"><b><i>'.strtoupper($nama_upt).'</b></i></font><br>
									<font size="10">'.$alamat1.'</font><br>
									<font size="10">'.$alamat2.'</font><br>
									<font size="10">'.$alamat3.'</font>
								</td>
							</tr>
				</table> ';
			

		$style = array('width' => 1.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 58, 200, 58, $style);
		$style2 = array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 60, 200, 60, $style2);
		// Header -->
		
		
		// <-- ISI 
		
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">
                
                            <tr>
							   
								<td width="100%" align="center" ><font size="12"><b><u>BERITA ACARA HIBAH BBM ANTAR KAPAL PENGAWAS PERIKANAN</b></u></font></td>
								
							</tr>
							<tr>
								<td width="100%" align="center" ><b>Nomor : '.$nomor_surat.'</b><br></td>
					
							</tr>
							<tr>
								<td width="100%" align="justify" >Yang bertanda tangan di bawah ini :</td>
							</tr>
							<tr>
								<td width="4%" align="justify" >1.</td>
								<td width="20%" align="justify" >Nama</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$nama_nahkoda_temp.'</td>
								
							</tr>
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="20%" align="justify" >Pangkat/Gol</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$pangkat_nahkoda_temp.'</td>
								
							</tr>
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="20%" align="justify" >Jabatan</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >Nakhoda KP '.$nama_kapal_temp.'</td>
								
							</tr>
							
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="80%" align="justify" >Selanjutnya disebut sebagai <b>Pihak I selaku penerima hibah BBM</b></td>
							</tr>
							<tr>
								<td width="4%" align="justify" >2.</td>
								<td width="20%" align="justify" >Nama</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$nama_nahkoda.'</td>
								
							</tr>
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="20%" align="justify" >Pangkat/Gol</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$pangkat_nahkoda.'</td>
								
							</tr>
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="20%" align="justify" >Jabatan</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >Nakhoda KP '.$nama_kapal.'</td>
								
							</tr>
							
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="80%" align="justify" >Selanjutnya disebut sebagai <b>Pihak II selaku pemberi hibah BBM</b></td>
								
							</tr>
						</table>';
			
							
			$tbl .= '<table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">	
							<tr>
								<td></td>
							</tr>
							<tr>
							
								<td width="100%" align="justify">Pada hari ini '.$ttanggal.' pukul '.$jam_surat.' '.$zona_waktu.' bertempat di '.$lokasi_surat.' berdasarkan Surat Persetujuan dari '.$deskripsi_persetujuan.'
								Nomor '.$nomer_persetujuan.' tanggal '.$this->indo_date($tgl_persetujuan).', telah dilakukan hibah BBM '.$keterangan_jenis_bbm.' dari PIHAK II ke PIHAK I sebanyak <b>'.number_format($volume_pemakaian).'</b> liter. Adapun hibah BBM ini di kerenakan <b>'.$sebab_peminjaman.' </b>
								
									
								</td>
							</tr>
				</table>';
				
				
		$tbl .= '<br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">
					<tr>
								<td></td>
							</tr>
					<tr>
						<td width="100%" align="justify" >Demikian Berita Acara Hibah BBM ini dibuat dengan sebenar – benarnya untuk dapat dijadikan sebagai bahan keterangan dan dipergunakan sebagaimana mestinya. </td>
					</tr>
				</table>';
		// ISI -->
		
		// <-- Fotter
		if($list['an_staf'] == 1){
			$an_staf = 'An. ';
		}else{
			$an_staf = '';
		}
		
		if($list['an_nakhoda'] == 1){
			$an_nakhoda = 'An. ';
		}else{
			$an_nakhoda = '';
		}
		
		if($list['an_kkm'] == 1){
			$an_kkm = 'An. ';
		}else{
			$an_kkm = '';
		}
		
		if($list['an_nakhoda_temp'] == 1){
			$an_nakhoda_temp = 'An. ';
		}else{
			$an_nakhoda_temp = '';
		}
		
		if($list['an_kkm_temp'] == 1){
			$an_kkm_temp = 'An. ';
		}else{
			$an_kkm_temp = '';
		}
		
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif; font-size:10px" border ="0">
					<tr>	
						<td width="40%" align="center" >
							<b>Pihak I</b>
						</td>
						
						<td width="20%" align="center" ></td>
						
						<td width="40%" align="center" >
							<b>PIHAK II</b>
						</td>
					</tr>
					<tr>	
						<td width="40%" align="center" >
							<b>'.$an_nakhoda_temp.' Nakhoda KP. '.$nama_kapal_temp.'</b><br><br><br><br><br>
					
							<b><u>'.$nama_nahkoda_temp.'</u></b><br>
							<b>NIP. '.$nip_nahkoda_temp.'</b><br>
						</td>
						
						<td width="20%" align="center" ></td>
						
						<td width="40%" align="center" >
							<b>'.$an_nakhoda.' Nakhoda KP. '.$nama_kapal.'</b><br><br><br><br><br>
					
							<b><u>'.$nama_nahkoda.'</u></b><br>
							<b>NIP. '.$nip_nahkoda.'</b>
						</td>
						
					</tr>
					
					<tr>
						<td width="40%" align="center" >
							<b>'.$an_kkm_temp.' KKM KP. '.$nama_kapal_temp.'</b><br><br><br><br><br>
					
							<b><u>'.$nama_kkm_temp.'</u></b><br>
							<b>NIP. '.$nip_kkm_temp.'</b><br>
						</td>
						
						<td width="20%" align="center" >
							
							<b><br><br>Menyaksikan:</b><br>
							<b>'.$an_staf.' '.$jabatan_staf_pangkalan.'</b><br><br><br><br><br>
							
						</td>
						<td width="40%" align="center" >
							<b>'.$an_kkm.' KKM KP. '.$nama_kapal.'</b><br><br><br><br><br>
					
							<b><u>'.$nama_kkm.'</u></b><br>
							<b>NIP. '.$nip_kkm.'</b>
						</td>
						
					</tr>
					<tr>
						<td width="30%" align="center"></td>
						<td width="40%" align="center" >
							
					
							<b><u>'.$nama_staf_pagkalan.'</u></b><br>
							<b>NIP. '.$nip_staf.'</b>
						</td>
						
						<td width="30%" align="center" ></td>
					</tr>
				</table> ';
		// Fotter -->
		
		$pdf->writeHTML($tbl, true, false, true, false, '');
        $pdf->Output($_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/dokumen/cetakan_ba/'.$filename.'.pdf', 'F');

	}
	
	public function cetak_ba_penerima_hibah_bbm_kapal_pengawas($nomor_suratx, $filename) {
    	
		$nomor_surat = str_replace('_','/',$nomor_suratx);
		
		$q_result2 = "SELECT kapal_code,nama_kapal,nomor_surat,REPLACE(jam_surat,':','.') AS jam_surat,tanggal_surat, 
		TRIM(SUBSTRING(f_formattanggal(tanggal_surat),1,254)) AS ttanggal, volume_pengisian, volume_sebelum,volume_sisa,link_modul_temp,
		TRIM(SUBSTRING(f_formattanggal(tanggal_surat),1,254)) AS ttanggal,nama_nahkoda,nip_nahkoda,nama_kkm,nip_kkm,nama_staf_pagkalan,nip_staf,nama_kapal,nama_upt,
		alamat1,alamat2,alamat3,kota, zona_waktu, lok_surat, jabatan_staf_pangkalan, an_staf, an_nakhoda, an_kkm,nip_nahkoda_temp,an_nakhoda_temp,an_kkm_temp,nama_kkm_temp,nip_kkm_temp,
		pangkat_nahkoda_temp,keterangan_jenis_bbm, nama_kapal_temp,pangkat_nahkoda,nama_nahkoda_temp,pangkat_nahkoda_temp,sebab_temp,lokasi_surat,volume_pengisian
		FROM
		(SELECT a.*,b.nama_kapal,d.nama_kapal as nama_kapal_temp, b.m_upt_code,nama AS nama_upt,
   		  IFNULL(alamat1,'') AS alamat1,IFNULL(alamat2,'') AS alamat2,ifnull(alamat3,'') AS alamat3, ifnull(kota,'') AS kota, a.zona_waktu_surat AS zona_waktu, a.lokasi_surat AS lok_surat 
		  FROM bbm_kapaltrans a 
		  LEFT JOIN m_kapal b ON a.kapal_code = b.code_kapal 
		  LEFT JOIN m_upt c ON b.m_upt_code = c.code  
		  LEFT JOIN m_kapal d ON a.kapal_code_temp = d.code_kapal 
		  WHERE nomor_surat = '".$nomor_surat."') bbm_kapaltrans";
		 
		$coba = $this->db->query($q_result2);
		
		foreach($coba->result_array() AS $list){
			$nama_kapal 	= $list['nama_kapal'];
			$nama_kapal_temp 	= $list['nama_kapal_temp'];
			$nomor_surat 	= $list['nomor_surat'];
			$jam_surat 		= $list['jam_surat'];
			$tanggal_surat 	= $list['tanggal_surat'];
			$volume_pengisian 	= $list['volume_pengisian'];
			$ttanggal 		= $list['ttanggal'];
			$nama_nahkoda 	= $list['nama_nahkoda'];
			$pangkat_nahkoda = $list['pangkat_nahkoda'];
			$nama_nahkoda_temp 	= $list['nama_nahkoda_temp'];
			$pangkat_nahkoda_temp 	= $list['pangkat_nahkoda_temp'];
			$nip_nahkoda 	= $list['nip_nahkoda'];
			$nip_nahkoda_temp 	= $list['nip_nahkoda_temp'];
			$nama_kkm 		= $list['nama_kkm'];
			$nama_kkm_temp 		= $list['nama_kkm_temp'];
			$nip_kkm 		= $list['nip_kkm'];
			$nip_kkm_temp 		= $list['nip_kkm_temp'];
			$nama_staf_pagkalan = $list['nama_staf_pagkalan'];
			$nip_staf 		= $list['nip_staf'];
			$nama_upt		= $list['nama_upt'];
			$alamat1		= $list['alamat1'];
			$alamat2		= $list['alamat2'];
			$alamat3		= $list['alamat3'];
			$lok_surat		    = $list['lok_surat'];
			$zona_waktu		    = $list['zona_waktu'];
			$jabatan_staf_pangkalan		    = $list['jabatan_staf_pangkalan'];
			$keterangan_jenis_bbm		    = $list['keterangan_jenis_bbm'];
			$sebab_peminjaman		    = $list['sebab_temp'];
			$lokasi_surat		    = $list['lokasi_surat'];
			$link_modul_temp		    = $list['link_modul_temp'];
		} 
		
		$q_result3 = "SELECT *
		  FROM bbm_kapaltrans a 
		  WHERE nomor_surat = '".$link_modul_temp."' ";
		 
		$coba3 = $this->db->query($q_result3);
		
		foreach($coba3->result_array() AS $list3){
		
			$nomor_suratx 	= $list3['nomor_surat'];
			
			$tanggal_suratx 	= $list3['tanggal_surat'];
			
			
		} 
		
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setPrintFooter(false);
        $pdf->setPrintHeader(false);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->AddPage('P','A4');
        $pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetFont('');
        
        
        $tbl = '<style type="text/css">
				hr.new5 {
					  border: 20px solid green;
					  border-radius: 5px;
					}
			</style>';
		// <-- Header
        $tbl .= '
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;" border ="0">
							<tr>
								<td width="15%" align="center" ><img align="center" width="120" height="120" src="'.$_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/assets/img/kkp3.png" border="0" /></td>
								<td width="85%" align="center" >
									<font size="15"><b>KEMENTERIAN KELAUTAN DAN PERIKANAN</b></font><br>
									<font size="17">DIREKTORAT JENDERAL PENGAWASAN</font><br>
									<font size="17">SUMBER DAYA KELAUTAN DAN PERIKANAN</font><br>
									<font size="12"><b><i>'.strtoupper($nama_upt).'</b></i></font><br>
									<font size="10">'.$alamat1.'</font><br>
									<font size="10">'.$alamat2.'</font><br>
									<font size="10">'.$alamat3.'</font>
								</td>
							</tr>
				</table> ';
			

		$style = array('width' => 1.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 58, 200, 58, $style);
		$style2 = array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 60, 200, 60, $style2);
		// Header -->
		
		
		// <-- ISI 
		
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">
                
                            <tr>
							   
								<td width="100%" align="center" ><font size="12"><b><u>BERITA ACARA PENERIMAAN HIBAH BBM ANTAR INSTANSI LAIN</b></u></font></td>
								
							</tr>
							<tr>
								<td width="100%" align="center" ><b>Nomor : '.$nomor_surat.'</b><br></td>
					
							</tr>
							<tr>
								<td width="100%" align="justify" >Yang bertanda tangan di bawah ini :</td>
							</tr>
							<tr>
								<td width="4%" align="justify" >1.</td>
								<td width="20%" align="justify" >Nama</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$nama_nahkoda.'</td>
								
							</tr>
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="20%" align="justify" >Pangkat/Gol</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$pangkat_nahkoda.'</td>
								
							</tr>
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="20%" align="justify" >Jabatan</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >Nakhoda KP '.$nama_kapal.'</td>
								
							</tr>
							
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="80%" align="justify" >Selanjutnya disebut sebagai <b>Pihak I selaku penerimaa hibah BBM</b></td>
							</tr>
							<tr>
								<td width="4%" align="justify" >2.</td>
								<td width="20%" align="justify" >Nama</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$nama_nahkoda_temp.'</td>
								
							</tr>
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="20%" align="justify" >Pangkat/Gol</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$pangkat_nahkoda_temp.'</td>
								
							</tr>
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="20%" align="justify" >Jabatan</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >Nakhoda KP '.$nama_kapal_temp.'</td>
								
							</tr>
							
							
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="80%" align="justify" >Selanjutnya disebut sebagai <b>Pihak II selaku pemberi hibah BBM</b></td>
								
							</tr>
						</table>';
			
							
			$tbl .= '<table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">				
								<tr>
					    <td></td>
					</tr>
							<tr>
							
								<td width="100%" align="justify">Pada hari ini '.$ttanggal.' pukul  '.$jam_surat.' '.$zona_waktu.' bertempat di '.$lokasi_surat.', telah dilakukan penerimaan Hibah BBM antar Kapal Pengawas Perikanan '.$keterangan_jenis_bbm.' dari PIHAK II ke PIHAK I sebanyak <b>'.number_format($volume_pengisian).'</b> liter.
								</td>
							</tr>
				</table>';
				
				
		$tbl .= '<br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">
					<tr>
					    <td></td>
					</tr>
					<tr>
						<td width="100%" align="justify" >Demikian Berita Acara Hibah BBM ini dibuat dengan sebenar – benarnya untuk dapat dijadikan sebagai bahan keterangan dan dipergunakan sebagaimana mestinya. </td>
					</tr>
				</table>';
		// ISI -->
		
		// <-- Fotter
		if($list['an_staf'] == 1){
			$an_staf = 'An. ';
		}else{
			$an_staf = '';
		}
		
		if($list['an_nakhoda'] == 1){
			$an_nakhoda = 'An. ';
		}else{
			$an_nakhoda = '';
		}
		
		if($list['an_kkm'] == 1){
			$an_kkm = 'An. ';
		}else{
			$an_kkm = '';
		}
		
		if($list['an_nakhoda_temp'] == 1){
			$an_nakhoda_temp = 'An. ';
		}else{
			$an_nakhoda_temp = '';
		}
		
		if($list['an_kkm_temp'] == 1){
			$an_kkm_temp = 'An. ';
		}else{
			$an_kkm_temp = '';
		}
		
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif; font-size:10px" border ="0">
					<tr>	
						<td width="40%" align="center" >
							<b>Pihak I</b>
						</td>
						
						<td width="20%" align="center" ></td>
						
						<td width="40%" align="center" >
							<b>PIHAK II</b>
						</td>
					</tr>
					<tr>	
						
						
						<td width="40%" align="center" >
							<b>'.$an_nakhoda.' Nakhoda KP. '.$nama_kapal.'</b><br><br><br><br><br>
					
							<b><u>'.$nama_nahkoda.'</u></b><br>
							<b>NIP. '.$nip_nahkoda.'</b>
						</td>
						<td width="20%" align="center" ></td>
						<td width="40%" align="center" >
							<b>'.$an_nakhoda_temp.' Nakhoda KP. '.$nama_kapal_temp.'</b><br><br><br><br><br>
					
							<b><u>'.$nama_nahkoda_temp.'</u></b><br>
							<b>NIP. '.$nip_nahkoda_temp.'</b><br>
						</td>
						
						
					</tr>
					
					<tr>
						<td width="40%" align="center" >
							<b>'.$an_kkm.' KKM KP. '.$nama_kapal.'</b><br><br><br><br><br>
					
							<b><u>'.$nama_kkm.'</u></b><br>
							<b>NIP. '.$nip_kkm.'</b>
						</td>
						
						<td width="20%" align="center" >
							
							<b><br><br>Menyaksikan:</b><br>
							<b>'.$an_staf.' '.$jabatan_staf_pangkalan.'</b><br><br><br><br><br>
							
						</td>
						<td width="40%" align="center" >
							<b>'.$an_kkm_temp.' KKM KP. '.$nama_kapal_temp.'</b><br><br><br><br><br>
					
							<b><u>'.$nama_kkm_temp.'</u></b><br>
							<b>NIP. '.$nip_kkm_temp.'</b><br>
						</td>
						
						
					</tr>
					<tr>
						<td width="30%" align="center"></td>
						<td width="40%" align="center" >
							
					
							<b><u>'.$nama_staf_pagkalan.'</u></b><br>
							<b>NIP. '.$nip_staf.'</b>
						</td>
						
						<td width="30%" align="center" ></td>
					</tr>
				</table> ';
		// Fotter -->
		
		$pdf->writeHTML($tbl, true, false, true, false, '');
        $pdf->Output($_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/dokumen/cetakan_ba/'.$filename.'.pdf', 'F');

	}
	
	public function cetak_ba_pemberi_ba_hibah_bbm_instansi_lain($nomor_suratx, $filename) {
    	
		$nomor_surat = str_replace('_','/',$nomor_suratx);
		
		$q_result2 = "SELECT kapal_code,nama_kapal,nomor_surat,REPLACE(jam_surat,':','.') AS jam_surat,tanggal_surat, 
		TRIM(SUBSTRING(f_formattanggal(tanggal_surat),1,254)) AS ttanggal, volume_pemakaian,volume_sebelum,volume_sisa,nomer_persetujuan,tgl_persetujuan,deskripsi_persetujuan,
		TRIM(SUBSTRING(f_formattanggal(tanggal_surat),1,254)) AS ttanggal,nama_nahkoda,nip_nahkoda,nama_kkm,nip_kkm,nama_staf_pagkalan,nip_staf,nama_kapal,nama_upt,
		alamat1,alamat2,alamat3,kota, zona_waktu, lok_surat, jabatan_staf_pangkalan, an_staf, an_nakhoda, an_kkm,nip_nahkoda_temp,an_nakhoda_temp,an_kkm_temp,nama_kkm_temp,nip_kkm_temp,
		pangkat_nahkoda_temp,keterangan_jenis_bbm, nama_kapal_temp,pangkat_nahkoda,nama_nahkoda_temp,pangkat_nahkoda_temp,sebab_temp,lokasi_surat,volume_pemakaian,kapal_code_temp
		FROM
		(SELECT e.deskripsi_persetujuan, a.*,b.nama_kapal,d.nama_kapal as nama_kapal_temp, b.m_upt_code,nama AS nama_upt,
   		  IFNULL(alamat1,'') AS alamat1,IFNULL(alamat2,'') AS alamat2,ifnull(alamat3,'') AS alamat3, ifnull(kota,'') AS kota, a.zona_waktu_surat AS zona_waktu, a.lokasi_surat AS lok_surat 
		  FROM bbm_kapaltrans a 
		  LEFT JOIN m_kapal b ON a.kapal_code = b.code_kapal 
		  LEFT JOIN m_upt c ON b.m_upt_code = c.code
		  LEFT JOIN m_kapal d ON a.kapal_code_temp = d.code_kapal 
		  LEFT JOIN m_persetujuan e ON e.id = a.m_persetujuan_id
		  WHERE nomor_surat = '".$nomor_surat."') bbm_kapaltrans";
		 
		$coba = $this->db->query($q_result2);
		
		foreach($coba->result_array() AS $list){
			$nama_kapal 	= $list['nama_kapal'];
			$nama_kapal_temp 	= $list['kapal_code_temp'];
			$nomor_surat 	= $list['nomor_surat'];
			$jam_surat 		= $list['jam_surat'];
			$tanggal_surat 	= $list['tanggal_surat'];
			$volume_pemakaian 	= $list['volume_pemakaian'];
			$ttanggal 		= $list['ttanggal'];
			$nama_nahkoda 	= $list['nama_nahkoda'];
			$pangkat_nahkoda 	= $list['pangkat_nahkoda'];
			$nama_nahkoda_temp 	= $list['nama_nahkoda_temp'];
			$pangkat_nahkoda_temp 	= $list['pangkat_nahkoda_temp'];
			$nip_nahkoda 	= $list['nip_nahkoda'];
			$nip_nahkoda_temp 	= $list['nip_nahkoda_temp'];
			$nama_kkm 		= $list['nama_kkm'];
			$nama_kkm_temp 		= $list['nama_kkm_temp'];
			$nip_kkm 		= $list['nip_kkm'];
			$nip_kkm_temp 		= $list['nip_kkm_temp'];
			$nama_staf_pagkalan = $list['nama_staf_pagkalan'];
			$nip_staf 		= $list['nip_staf'];
			$nama_upt		= $list['nama_upt'];
			$alamat1		= $list['alamat1'];
			$alamat2		= $list['alamat2'];
			$alamat3		= $list['alamat3'];
			$lok_surat		    = $list['lok_surat'];
			$zona_waktu		    = $list['zona_waktu'];
			$jabatan_staf_pangkalan		    = $list['jabatan_staf_pangkalan'];
			$keterangan_jenis_bbm		    = $list['keterangan_jenis_bbm'];
			$sebab_peminjaman		    = $list['sebab_temp'];
			$lokasi_surat		    = $list['lokasi_surat'];
			$nomer_persetujuan		    = $list['nomer_persetujuan'];
			$tgl_persetujuan		    = $list['tgl_persetujuan'];
			$deskripsi_persetujuan		    = $list['deskripsi_persetujuan'];
		} 
		
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setPrintFooter(false);
        $pdf->setPrintHeader(false);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->AddPage('P','A4');
        $pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetFont('');
        
        
        $tbl = '<style type="text/css">
				hr.new5 {
					  border: 20px solid green;
					  border-radius: 5px;
					}
			</style>';
		// <-- Header
        $tbl .= '
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;" border ="0">
							<tr>
								<td width="15%" align="center" ><img align="center" width="120" height="120" src="'.$_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/assets/img/kkp3.png" border="0" /></td>
								<td width="85%" align="center" >
									<font size="15"><b>KEMENTERIAN KELAUTAN DAN PERIKANAN</b></font><br>
									<font size="17">DIREKTORAT JENDERAL PENGAWASAN</font><br>
									<font size="17">SUMBER DAYA KELAUTAN DAN PERIKANAN</font><br>
									<font size="12"><b><i>'.strtoupper($nama_upt).'</b></i></font><br>
									<font size="10">'.$alamat1.'</font><br>
									<font size="10">'.$alamat2.'</font><br>
									<font size="10">'.$alamat3.'</font>
								</td>
							</tr>
				</table> ';
			

		$style = array('width' => 1.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 58, 200, 58, $style);
		$style2 = array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 60, 200, 60, $style2);
		// Header -->
		
		
		// <-- ISI 
		
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">
                
                            <tr>
							   
								<td width="100%" align="center" ><font size="12"><b><u>BERITA ACARA PEMBERIAN HIBAH BBM DENGAN KAPAL INSTANSI LAIN</b></u></font></td>
								
							</tr>
							<tr>
								<td width="100%" align="center" ><b>Nomor : '.$nomor_surat.'</b><br></td>
					
							</tr>
							<tr>
								<td width="100%" align="justify" >Yang bertanda tangan di bawah ini :</td>
							</tr>
							
							<tr>
								<td width="4%" align="justify" >1.</td>
								<td width="20%" align="justify" >Nama</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$nama_nahkoda_temp.'</td>
								
							</tr>
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="20%" align="justify" >Pangkat/Gol</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$pangkat_nahkoda_temp.'</td>
								
							</tr>
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="20%" align="justify" >Jabatan</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >Nakhoda KP '.$nama_kapal_temp.'</td>
								
							</tr>
							
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="80%" align="justify" >Selanjutnya disebut sebagai <b>Pihak I selaku penerima hibah BBM</b></td>
							</tr>
							<tr>
								<td width="4%" align="justify" >2.</td>
								<td width="20%" align="justify" >Nama</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$nama_nahkoda.'</td>
								
							</tr>
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="20%" align="justify" >Pangkat/Gol</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$pangkat_nahkoda.'</td>
								
							</tr>
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="20%" align="justify" >Jabatan</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >Nakhoda KP '.$nama_kapal.'</td>
								
							</tr>
							
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="80%" align="justify" >Selanjutnya disebut sebagai <b>Pihak II selaku pemberi hibah BBM</b></td>
								
							</tr>
						</table>';
			
							
			$tbl .= '<table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">				
							<tr>
							    <td></td>
							</tr>
							
							<tr>
							
								<td width="100%" align="justify">Pada hari ini '.$ttanggal.' pukul '.$jam_surat.' '.$zona_waktu.' bertempat di '.$lokasi_surat.' berdasarkan Surat Persetujuan dari '.$deskripsi_persetujuan.'
								Nomor '.$nomer_persetujuan.' tanggal '.$this->indo_date($tgl_persetujuan).', telah dilakukan hibah BBM '.$keterangan_jenis_bbm.' dari PIHAK II ke PIHAK I sebanyak <b>'.number_format($volume_pemakaian).'</b> liter. Adapun hibah BBM ini di kerenakan <b>'.$sebab_peminjaman.'</b>.
								
									
								</td>
							</tr>
				</table>';
				
		$tbl .= '<br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">
					<tr>
							    <td></td>
							</tr>		
					<tr>
						<td width="100%" align="justify" >Demikian Berita Acara Pemberian Hibah BBM ini dibuat dengan sebenar – benarnya untuk dapat dijadikan sebagai bahan keterangan dan dipergunakan sebagaimana mestinya. </td>
					</tr>
				</table>';
		// ISI -->
		
		// <-- Fotter
		if($list['an_staf'] == 1){
			$an_staf = 'An. ';
		}else{
			$an_staf = '';
		}
		
		if($list['an_nakhoda'] == 1){
			$an_nakhoda = 'An. ';
		}else{
			$an_nakhoda = '';
		}
		
		if($list['an_kkm'] == 1){
			$an_kkm = 'An. ';
		}else{
			$an_kkm = '';
		}
		
		if($list['an_nakhoda_temp'] == 1){
			$an_nakhoda_temp = 'An. ';
		}else{
			$an_nakhoda_temp = '';
		}
		
		if($list['an_kkm_temp'] == 1){
			$an_kkm_temp = 'An. ';
		}else{
			$an_kkm_temp = '';
		}
		
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif; font-size:10px" border ="0">
					<tr>	
						<td width="40%" align="center" >
							<b>Pihak I</b>
						</td>
						
						<td width="20%" align="center" ></td>
						
						<td width="40%" align="center" >
							<b>PIHAK II</b>
						</td>
					</tr>
					<tr>	
						<td width="40%" align="center" >
							<b>'.$an_nakhoda_temp.' Nakhoda KP. '.$nama_kapal_temp.'</b><br><br><br><br><br>
					
							<b><u>'.$nama_nahkoda_temp.'</u></b><br>
							<b>NIP. '.$nip_nahkoda_temp.'</b><br>
						</td>
						
						<td width="20%" align="center" ></td>
						
						<td width="40%" align="center" >
							<b>'.$an_nakhoda.' Nakhoda KP. '.$nama_kapal.'</b><br><br><br><br><br>
					
							<b><u>'.$nama_nahkoda.'</u></b><br>
							<b>NIP. '.$nip_nahkoda.'</b>
						</td>
						
					</tr>
					
					<tr>
						<td width="40%" align="center" >
							<b>'.$an_kkm_temp.' KKM KP. '.$nama_kapal_temp.'</b><br><br><br><br><br>
					
							<b><u>'.$nama_kkm_temp.'</u></b><br>
							<b>NIP. '.$nip_kkm_temp.'</b><br>
						</td>
						
						<td width="20%" align="center" >
							
							<b><br><br>Menyaksikan:</b><br>
							<b>'.$an_staf.' '.$jabatan_staf_pangkalan.'</b><br><br><br><br><br>
							
						</td>
						<td width="40%" align="center" >
							<b>'.$an_kkm.' KKM KP. '.$nama_kapal.'</b><br><br><br><br><br>
					
							<b><u>'.$nama_kkm.'</u></b><br>
							<b>NIP. '.$nip_kkm.'</b>
						</td>
						
					</tr>
					<tr>
						<td width="30%" align="center"></td>
						<td width="40%" align="center" >
							
					
							<b><u>'.$nama_staf_pagkalan.'</u></b><br>
							<b>NIP. '.$nip_staf.'</b>
						</td>
						
						<td width="30%" align="center" ></td>
					</tr>
				</table> ';
		// Fotter -->
		
		$pdf->writeHTML($tbl, true, false, true, false, '');
        $pdf->Output($_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/dokumen/cetakan_ba/'.$filename.'.pdf', 'F');

	}
	
	public function cetak_ba_penerima_hibah_bbm_instansi_lain($nomor_suratx, $filename) {
    	// var_dump("assdasd");die();
		$nomor_surat = str_replace('_','/',$nomor_suratx);
		
		$q_result2 = "SELECT kapal_code, nama_kapal, nomor_surat, REPLACE(jam_surat,':','.') AS jam_surat,tanggal_surat, 
		TRIM(SUBSTRING(f_formattanggal(tanggal_surat),1,254)) AS ttanggal, volume_pengisian, volume_pemakaian, volume_sebelum, volume_sisa, nomer_persetujuan,tgl_persetujuan,deskripsi_persetujuan,
		TRIM(SUBSTRING(f_formattanggal(tanggal_surat),1,254)) AS ttanggal,nama_nahkoda,nip_nahkoda,nama_kkm,nip_kkm,nama_staf_pagkalan,nip_staf,nama_kapal,nama_upt,
		alamat1,alamat2,alamat3,kota, zona_waktu, lok_surat, jabatan_staf_pangkalan, an_staf, an_nakhoda, an_kkm,nip_nahkoda_temp,an_nakhoda_temp,an_kkm_temp,nama_kkm_temp,nip_kkm_temp,
		pangkat_nahkoda_temp,keterangan_jenis_bbm, nama_kapal_temp,pangkat_nahkoda,nama_nahkoda_temp,pangkat_nahkoda_temp,sebab_temp,lokasi_surat,volume_pemakaian,kapal_code_temp
		FROM
		(SELECT e.deskripsi_persetujuan, a.*,b.nama_kapal,d.nama_kapal as nama_kapal_temp, b.m_upt_code,nama AS nama_upt,
   		  IFNULL(alamat1,'') AS alamat1,IFNULL(alamat2,'') AS alamat2,ifnull(alamat3,'') AS alamat3, ifnull(kota,'') AS kota, a.zona_waktu_surat AS zona_waktu, a.lokasi_surat AS lok_surat 
		  FROM bbm_kapaltrans a 
		  LEFT JOIN m_kapal b ON a.kapal_code = b.code_kapal 
		  LEFT JOIN m_upt c ON b.m_upt_code = c.code
		  LEFT JOIN m_kapal d ON a.kapal_code_temp = d.code_kapal 
		  LEFT JOIN m_persetujuan e ON e.id = a.m_persetujuan_id
		  WHERE nomor_surat = '".$nomor_surat."') bbm_kapaltrans";
		 
		$coba = $this->db->query($q_result2);
		
		foreach($coba->result_array() AS $list){
			$nama_kapal 	= $list['nama_kapal'];
			$nama_kapal_temp 	= $list['kapal_code_temp'];
			$nomor_surat 	= $list['nomor_surat'];
			$jam_surat 		= $list['jam_surat'];
			$tanggal_surat 	= $list['tanggal_surat'];
			$volume_pengisian 	= $list['volume_pengisian'];
			$ttanggal 		= $list['ttanggal'];
			$nama_nahkoda 	= $list['nama_nahkoda'];
			$pangkat_nahkoda 	= $list['pangkat_nahkoda'];
			$nama_nahkoda_temp 	= $list['nama_nahkoda_temp'];
			$pangkat_nahkoda_temp 	= $list['pangkat_nahkoda_temp'];
			$nip_nahkoda 	= $list['nip_nahkoda'];
			$nip_nahkoda_temp 	= $list['nip_nahkoda_temp'];
			$nama_kkm 		= $list['nama_kkm'];
			$nama_kkm_temp 		= $list['nama_kkm_temp'];
			$nip_kkm 		= $list['nip_kkm'];
			$nip_kkm_temp 		= $list['nip_kkm_temp'];
			$nama_staf_pagkalan = $list['nama_staf_pagkalan'];
			$nip_staf 		= $list['nip_staf'];
			$nama_upt		= $list['nama_upt'];
			$alamat1		= $list['alamat1'];
			$alamat2		= $list['alamat2'];
			$alamat3		= $list['alamat3'];
			$lok_surat		    = $list['lok_surat'];
			$zona_waktu		    = $list['zona_waktu'];
			$jabatan_staf_pangkalan		    = $list['jabatan_staf_pangkalan'];
			$keterangan_jenis_bbm		    = $list['keterangan_jenis_bbm'];
			$sebab_peminjaman		    = $list['sebab_temp'];
			$lokasi_surat		    = $list['lokasi_surat'];
			$nomer_persetujuan		    = $list['nomer_persetujuan'];
			$tgl_persetujuan		    = $list['tgl_persetujuan'];
			$deskripsi_persetujuan		    = $list['deskripsi_persetujuan'];
		} 
		
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setPrintFooter(false);
        $pdf->setPrintHeader(false);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->AddPage('P','A4');
        $pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetFont('');
        
        
        $tbl = '<style type="text/css">
				hr.new5 {
					  border: 20px solid green;
					  border-radius: 5px;
					}
			</style>';
		// <-- Header
        $tbl .= '
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;" border ="0">
							<tr>
								<td width="15%" align="center" ><img align="center" width="120" height="120" src="'.$_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/assets/img/kkp3.png" border="0" /></td>
								<td width="85%" align="center" >
									<font size="15"><b>KEMENTERIAN KELAUTAN DAN PERIKANAN</b></font><br>
									<font size="17">DIREKTORAT JENDERAL PENGAWASAN</font><br>
									<font size="17">SUMBER DAYA KELAUTAN DAN PERIKANAN</font><br>
									<font size="12"><b><i>'.strtoupper($nama_upt).'</b></i></font><br>
									<font size="10">'.$alamat1.'</font><br>
									<font size="10">'.$alamat2.'</font><br>
									<font size="10">'.$alamat3.'</font>
								</td>
							</tr>
				</table> ';
			

		$style = array('width' => 1.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 58, 200, 58, $style);
		$style2 = array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 60, 200, 60, $style2);
		// Header -->
		
		
		// <-- ISI 
		
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">
                
                            <tr>
							   
								<td width="100%" align="center" ><font size="12"><b><u>BERITA ACARA PENERIMA HIBAH BBM DENGAN KAPAL INSTANSI LAIN</b></u></font></td>
								
							</tr>
							<tr>
								<td width="100%" align="center" ><b>Nomor : '.$nomor_surat.'</b><br></td>
					
							</tr>
							<tr>
								<td width="100%" align="justify" >Yang bertanda tangan di bawah ini :</td>
							</tr>
							
							<tr>
								<td width="4%" align="justify" >1.</td>
								<td width="20%" align="justify" >Nama</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$nama_nahkoda.'</td>
								
							</tr>
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="20%" align="justify" >Pangkat/Gol</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$pangkat_nahkoda.'</td>
								
							</tr>
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="20%" align="justify" >Jabatan</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >Nakhoda KP '.$nama_kapal.'</td>
								
							</tr>
							
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="80%" align="justify" >Selanjutnya disebut sebagai <b>Pihak I selaku penerima hibah BBM</b></td>
							</tr>
							<tr>
								<td width="4%" align="justify" >2.</td>
								<td width="20%" align="justify" >Nama</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$nama_nahkoda_temp.'</td>
								
							</tr>
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="20%" align="justify" >Pangkat/Gol</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >'.$pangkat_nahkoda_temp.'</td>
								
							</tr>
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="20%" align="justify" >Jabatan</td>
								<td width="3%" align="justify" >:</td>
								<td width="50%" align="justify" >Nakhoda KP '.$nama_kapal_temp.'</td>
								
							</tr>
							
							
							<tr>
								<td width="4%" align="justify" ></td>
								<td width="80%" align="justify" >Selanjutnya disebut sebagai <b>Pihak II selaku pemberi hibah BBM</b></td>
								
							</tr>
						</table>';
			
							
			$tbl .= '<table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">				
							<tr>
							    <td></td>
							</tr>
							
							<tr>
							
								<td width="100%" align="justify">Pada hari ini '.$ttanggal.' pukul '.$jam_surat.' '.$zona_waktu.' bertempat di '.$lokasi_surat.' telah dilakukan hibah BBM '.$keterangan_jenis_bbm.' dari PIHAK II ke PIHAK I sebanyak <b>'.number_format($volume_pengisian).'</b> liter. Adapun hibah BBM ini di kerenakan <b>'.$sebab_peminjaman.'</b>.
								
									
								</td>
							</tr>
				</table>';
				
		$tbl .= '<br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">
					<tr>
							    <td></td>
							</tr>		
					<tr>
						<td width="100%" align="justify" >Demikian Berita Acara Penerimaan Hibah BBM ini dibuat dengan sebenar – benarnya untuk dapat dijadikan sebagai bahan keterangan dan dipergunakan sebagaimana mestinya. </td>
					</tr>
				</table>';
		// ISI -->
		
		// <-- Fotter
		if($list['an_staf'] == 1){
			$an_staf = 'An. ';
		}else{
			$an_staf = '';
		}
		
		if($list['an_nakhoda'] == 1){
			$an_nakhoda = 'An. ';
		}else{
			$an_nakhoda = '';
		}
		
		if($list['an_kkm'] == 1){
			$an_kkm = 'An. ';
		}else{
			$an_kkm = '';
		}
		
		if($list['an_nakhoda_temp'] == 1){
			$an_nakhoda_temp = 'An. ';
		}else{
			$an_nakhoda_temp = '';
		}
		
		if($list['an_kkm_temp'] == 1){
			$an_kkm_temp = 'An. ';
		}else{
			$an_kkm_temp = '';
		}
		
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif; font-size:10px" border ="0">
					<tr>	
						<td width="40%" align="center" >
							<b>Pihak I</b>
						</td>
						
						<td width="20%" align="center" ></td>
						
						<td width="40%" align="center" >
							<b>PIHAK II</b>
						</td>
					</tr>
					<tr>	
						<td width="40%" align="center" >
							<b>'.$an_nakhoda_temp.' Nakhoda KP. '.$nama_kapal_temp.'</b><br><br><br><br><br>
					
							<b><u>'.$nama_nahkoda_temp.'</u></b><br>
							<b>NIP. '.$nip_nahkoda_temp.'</b><br>
						</td>
						
						<td width="20%" align="center" ></td>
						
						<td width="40%" align="center" >
							<b>'.$an_nakhoda.' Nakhoda KP. '.$nama_kapal.'</b><br><br><br><br><br>
					
							<b><u>'.$nama_nahkoda.'</u></b><br>
							<b>NIP. '.$nip_nahkoda.'</b>
						</td>
						
					</tr>
					
					<tr>
						<td width="40%" align="center" >
							<b>'.$an_kkm_temp.' KKM KP. '.$nama_kapal_temp.'</b><br><br><br><br><br>
					
							<b><u>'.$nama_kkm_temp.'</u></b><br>
							<b>NIP. '.$nip_kkm_temp.'</b><br>
						</td>
						
						<td width="20%" align="center" >
							
							<b><br><br>Menyaksikan:</b><br>
							<b>'.$an_staf.' '.$jabatan_staf_pangkalan.'</b><br><br><br><br><br>
							
						</td>
						<td width="40%" align="center" >
							<b>'.$an_kkm.' KKM KP. '.$nama_kapal.'</b><br><br><br><br><br>
					
							<b><u>'.$nama_kkm.'</u></b><br>
							<b>NIP. '.$nip_kkm.'</b>
						</td>
						
					</tr>
					<tr>
						<td width="30%" align="center"></td>
						<td width="40%" align="center" >
							
					
							<b><u>'.$nama_staf_pagkalan.'</u></b><br>
							<b>NIP. '.$nip_staf.'</b>
						</td>
						
						<td width="30%" align="center" ></td>
					</tr>
				</table> ';
		// Fotter -->
		
		$pdf->writeHTML($tbl, true, false, true, false, '');
        $pdf->Output($_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/dokumen/cetakan_ba/'.$filename.'.pdf', 'F');

	}
	
	public function cetak_ba_pemberi_hibah_bbm($nomor_suratx, $filename) {
    	
		$nomor_surat = str_replace('_','/',$nomor_suratx);
		
		$q_result2 = "SELECT kapal_code,nama_kapal,nomor_surat,REPLACE(jam_surat,':','.') AS jam_surat,tanggal_surat, 
		TRIM(SUBSTRING(f_formattanggal(tanggal_surat),1,254)) AS ttanggal,
		TRIM(SUBSTRING(f_formattanggal(tanggal_surat),1,254)) AS ttanggal,nama_nahkoda,nip_nahkoda,nama_kkm,nip_kkm,nama_staf_pagkalan,nip_staf,nama_kapal,nama_upt,
		alamat1,alamat2,alamat3,kota, zona_waktu, lok_surat, jabatan_staf_pangkalan, an_staf, an_nakhoda, an_kkm,nip_nahkoda_temp,an_nakhoda_temp,an_kkm_temp,nama_kkm_temp,nip_kkm_temp,
		pangkat_nahkoda_temp,keterangan_jenis_bbm, nama_kapal_temp,pangkat_nahkoda,nama_nahkoda_temp,pangkat_nahkoda_temp,sebab_temp,lokasi_surat,volume_sisa,kapal_code_temp, no_so,penyedia,nama_penyedia, instansi_temp,alamat_instansi_temp
		FROM
		(SELECT a.*,b.nama_kapal,d.nama_kapal as nama_kapal_temp, b.m_upt_code,nama AS nama_upt,
   		  IFNULL(alamat1,'') AS alamat1,IFNULL(alamat2,'') AS alamat2,ifnull(alamat3,'') AS alamat3, ifnull(kota,'') AS kota, a.zona_waktu_surat AS zona_waktu, a.lokasi_surat AS lok_surat 
		  FROM bbm_kapaltrans a 
		  LEFT JOIN m_kapal b ON a.kapal_code = b.code_kapal 
		  LEFT JOIN m_upt c ON b.m_upt_code = c.code  
		  LEFT JOIN m_kapal d ON a.kapal_code_temp = d.code_kapal 
		  WHERE nomor_surat = '".$nomor_surat."') bbm_kapaltrans";
		 
		$coba = $this->db->query($q_result2);
		
		foreach($coba->result_array() AS $list){
			$nama_kapal 	= $list['nama_kapal'];
			$nama_kapal_temp 	= $list['kapal_code_temp'];
			$nomor_surat 	= $list['nomor_surat'];
			$jam_surat 		= $list['jam_surat'];
			$tanggal_surat 	= $list['tanggal_surat'];
			$volume_hibah 	= $list['volume_sisa'];
			$ttanggal 		= $list['ttanggal'];
			$nama_nahkoda 	= $list['nama_nahkoda'];
			$pangkat_nahkoda 	= $list['pangkat_nahkoda'];
			$nama_nahkoda_temp 	= $list['nama_nahkoda_temp'];
			$pangkat_nahkoda_temp 	= $list['pangkat_nahkoda_temp'];
			$nip_nahkoda 	= $list['nip_nahkoda'];
			$nip_nahkoda_temp 	= $list['nip_nahkoda_temp'];
			$nama_kkm 		= $list['nama_kkm'];
			$nama_kkm_temp 		= $list['nama_kkm_temp'];
			$nip_kkm 		= $list['nip_kkm'];
			$nip_kkm_temp 		= $list['nip_kkm_temp'];
			$nama_staf_pagkalan = $list['nama_staf_pagkalan'];
			$nip_staf 		= $list['nip_staf'];
			$nama_upt		= $list['nama_upt'];
			$alamat1		= $list['alamat1'];
			$alamat2		= $list['alamat2'];
			$alamat3		= $list['alamat3'];
			$lok_surat		    = $list['lok_surat'];
			$zona_waktu		    = $list['zona_waktu'];
			$jabatan_staf_pangkalan		    = $list['jabatan_staf_pangkalan'];
			$keterangan_jenis_bbm		    = $list['keterangan_jenis_bbm'];
			$sebab_peminjaman		    = $list['sebab_temp'];
			$lokasi_surat		    = $list['lokasi_surat'];
			$no_so		    = $list['no_so'];
			$penyedia		    = $list['penyedia'];
			$nama_penyedia		    = $list['nama_penyedia'];
			$instansi_temp		    = $list['instansi_temp'];
			$alamat_instansi_temp		    = $list['alamat_instansi_temp'];
		} 
		
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setPrintFooter(false);
        $pdf->setPrintHeader(false);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->AddPage('P','A4');
        $pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetFont('');
        
        $sql2    = "SELECT COUNT(*) AS con FROM `bbm_transdetail` WHERE nomor_surat = '".$nomor_surat."' "; 
		$query2  = $this->db->query($sql2);
		
		foreach ($query2->result() as $list2){
			
			$con = $list2->con;
		
		}
		
		if($con == 8 || $con == 9 || $con == 10){
		    
		    $px = '11px';
		}else{
		    $px = '12px';
		}
		
        $tbl = '<style type="text/css">
				hr.new5 {
					  border: 20px solid green;
					  border-radius: 5px;
					}
			</style>';
		// <-- Header
        $tbl .= '
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;" border ="0">
							<tr>
								<td width="15%" align="center" ><img align="center" width="120" height="120" src="'.$_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/assets/img/kkp3.png" border="0" /></td>
								<td width="85%" align="center" >
									<font size="15"><b>KEMENTERIAN KELAUTAN DAN PERIKANAN</b></font><br>
									<font size="17">DIREKTORAT JENDERAL PENGAWASAN</font><br>
									<font size="17">SUMBER DAYA KELAUTAN DAN PERIKANAN</font><br>
									<font size="12"><b><i>'.strtoupper($nama_upt).'</b></i></font><br>
									<font size="10">'.$alamat1.'</font><br>
									<font size="10">'.$alamat2.'</font><br>
									<font size="10">'.$alamat3.'</font>
								</td>
							</tr>
				</table> ';
			

		$style = array('width' => 1.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 58, 200, 58, $style);
		$style2 = array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 60, 200, 60, $style2);
		
		
		// <-- ISI 
		
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">
                
                            <tr>
							   
								<td width="100%" align="center" ><font size="12"><b><u>BERITA ACARA PENERIMAAN HIBAH BBM</b></u></font></td>
								
							</tr>
							<tr>
								<td width="100%" align="center" ><b>Nomor : '.$nomor_surat.'</b><br></td>
					
							</tr>
						
						</table>';
			
							
		$tbl .= '<table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">				
						<tr>
						
							<td width="100%" align="justify">Pada hari ini '.$ttanggal.' pukul '.$jam_surat.' '.$zona_waktu.' bertempat di '.$lokasi_surat.', kami yang bertanda tangan di bawah ini :
							
								
							</td>
						</tr>
						<tr>
						
							<td width="100%" align="justify"></td>
						</tr>
			</table>';
			
			
		$tbl .= '<table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">				
					<tr>
				
						<td width="20%" align="justify">Nama/Jabatan</td>
						<td width="1%" align="justify">:</td>
						<td width="auto" align="justify">1.'.$nama_nahkoda.' / Nakhoda Kapal Pengawas  </td>
					</tr>
					<tr>
				
						<td align="justify"></td>
						<td align="justify"></td>
						<td align="justify">2.'.$nama_kkm.' / KKM Kapal Pengawas  </td>
					</tr>
					
					<tr>
				
						<td align="justify"></td>
						<td align="justify"></td>
						<td align="justify"></td>
					</tr>
					
					<tr>
				
						<td align="justify">Alamat</td>
						<td align="justify">:</td>
						<td align="justify">'.$alamat_instansi_temp.'</td>
					</tr>
					<tr>
				
						<td align="justify"></td>
						<td align="justify"></td>
						<td align="justify"></td>
					</tr>
			</table>';
		
		$tbl .= '<table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">				
						<tr>
						
							<td width="100%" align="justify">Menyatakan bahwa telah menerima hasil pengadaan Hibah BBM '.$keterangan_jenis_bbm.' dari '.$instansi_temp.' melalui penyedia '.$penyedia.'(transportir/pengirim) sebagai berikut :
							</td>
						</tr>
						<tr>
						
							<td width="100%" align="justify"></td>
						</tr>
			</table>';
		
			$tbl .= '<br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:'.$px.'" border ="1">
						<tr>
							<td width="5%" align="center"><b>No</b></td>
							<td align="center"><b>Transportasi</b></td>
							<td align="center"><b>Nomor SO</b></td>
							<td align="center"><b>Nomor DO</b></td>
							<td align="center"><b>Volume (Liter)</b></td>
							<td width="auto" align="center"><b>Keterangan</b></td>

						</tr>';
						$i   = 1;
						$jml = 0;
						$sql = "SELECT * FROM `bbm_transdetail` WHERE nomor_surat = '".$nomor_surat."' "; 
						$query = $this->db->query($sql);
						
						foreach ($query->result() as $listdetail){
							
							$tbl .= '<tr>
										<td width="5%" align="center">'.$i++.'</td>
										<td align="center">'.$listdetail->transportasi.'</td>
										<td align="center">'.$no_so.'</td>
										<td align="center">'.$listdetail->no_do.'</td>
										<td align="center">'.number_format($listdetail->volume_isi).' Liter</td>
										<td width="auto" align="center">'.$listdetail->keterangan.'</td>
							</tr>';
							
							$jml += $listdetail->volume_isi;
						}
						
						$tbl .= '<tr>
										<td colspan="4" align="center">JUMLAH</td>
										<td align="center">'.number_format($jml).' Liter</td>
										<td width="auto" align="center"></td>
							</tr>';
						
			$tbl .= ' </table>';		
				
		$tbl .= '
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size:12px" border ="0">
							
					<tr>
						<td width="100%" align="justify" ></td>
					</tr><tr>
						<td width="100%" align="justify" >Demikian Berita Acara Penerimaan BBM ini dibuat dengan sebenar – benarnya untuk dapat dipergunakan sebagaimana mestinya. </td>
					</tr>
				</table>';
		// ISI -->
		
		// <-- Fotter
		if($list['an_staf'] == 1){
			$an_staf = 'An. ';
		}else{
			$an_staf = '';
		}
		
		if($list['an_nakhoda'] == 1){
			$an_nakhoda = 'An. ';
		}else{
			$an_nakhoda = '';
		}
		
		if($list['an_kkm'] == 1){
			$an_kkm = 'An. ';
		}else{
			$an_kkm = '';
		}
		
		if($list['an_nakhoda_temp'] == 1){
			$an_nakhoda_temp = 'An. ';
		}else{
			$an_nakhoda_temp = '';
		}
		
		if($list['an_kkm_temp'] == 1){
			$an_kkm_temp = 'An. ';
		}else{
			$an_kkm_temp = '';
		}
		
		$tbl .= '<br><br>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif; font-size:10px" border ="0">
					<tr>	
						<td width="40%" align="center" >
							<b>'.$an_kkm.' KKM KP '.$nama_kapal.'</b><br><br><br><br>
						</td>
						
						<td width="20%" align="center" ></td>
						
						<td width="40%" align="center" >
							<b>Penyedia BBM/Pengirim</b><br>
							<b>'.$penyedia.'</b>
						</td>
					</tr>
					<tr>	
						<td width="40%" align="center" >
							
					
							<b><u>'.$nama_kkm.'</u></b><br>
							<b><u>'.$nip_kkm.'</u></b><br>
						</td>
						
						<td width="20%" align="center" >
							<b></b><br>
							<b></b><br>
							<b>Menyaksikan:</b>
						</td>
						
						<td width="40%" align="center" >
						
					
							<b><u>'.$nama_penyedia.'</u></b><br>
							<b></b>
						</td>
						
					</tr>
					
					<tr>
						<td width="40%" align="center" >
							<b>'.$an_nakhoda.' Nakhoda KP. '.$nama_kapal.'</b><br><br><br><br>
					
							<b><u>'.$nama_nahkoda.'</u></b><br>
							<b>NIP. '.$nip_nahkoda.'</b><br>
						</td>
						
						<td width="20%" align="center" >
							
							
							
							
						</td>
						<td width="40%" align="center" >
							<b>'.$an_staf.' '.$jabatan_staf_pangkalan.'</b><br><br><br><br><br>
					
							<b><u>'.$nama_staf_pagkalan.'</u></b><br>
							<b>NIP. '.$nip_staf.'</b>
						</td>
						
					</tr>
				</table> ';
		// Fotter -->
		
		$pdf->writeHTML($tbl, true, false, true, false, '');
        $pdf->Output($_SERVER['DOCUMENT_ROOT'].'sigotik_bbm/dokumen/cetakan_ba/'.$filename.'.pdf', 'F');

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

/* End of file Users.php */