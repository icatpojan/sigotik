<?php
	
function jsonDecode($data) {

	if (empty($data)) return array();

    $items = json_decode($data, true);

    if ($items == NULL){
        throw new Exception('JSON items could not be decoded');
    }

    return $items;
}

function isValidEmail($email){ 
    return filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match('/@.+\./', $email);
}

function html_spaces($number=1) {
    $result = "";
    for($i = 1; $i <= $number; $i++) {
        $result .= "&nbsp;";
    }
    return $result;
}

function indo_date($tgl){
	$tgl_s = date('j',strtotime($tgl));
	$bln_s = get_bulan(date('n',strtotime($tgl)));
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

function romawi($n){
	if($n==1){
		$hasil = 'I';
	}else if($n==2){
		$hasil = 'II';
	}else if($n==3){
		$hasil = 'III';
	}else if($n==4){
		$hasil = 'IV';
	}else if($n==5){
		$hasil = 'V';
	}else if($n==6){
		$hasil = 'VI';
	}else if($n==7){
		$hasil = 'VII';
	}else if($n==8){
		$hasil = 'VIII';
	}else if($n==9){
		$hasil = 'IX';
	}else if($n==10){
		$hasil = 'X';
	}else if($n==11){
		$hasil = 'XI';
	}else if($n==12){
		$hasil = 'XII';
	}
	return $hasil;
}

function nosurat($kdkpl,$kdupt){
	$ci = & get_instance();
	#1. PW.430
	#2. KW.330
	#3. T
	#4. KW.340
	#5. KW.310
	
	#ORIGIN : 001/ORCA.03.3/PSDKP.3/KW340/I/2019
	$urutan = 0;
	$sql = $ci->db->query("SELECT MAX(LEFT(nomor_surat,3)) AS maks FROM bbm_kapaltrans WHERE kapal_code = '".$kdkpl."'")->row_array();
	$urutan = $sql['maks'] + 1;
	$urut 	= $urutan;
	$z = strlen($urut);
	$x = 3-$z;
	for($i=$x;$i>0;$i--){
		$urut = '0'.$urut;
	}
	$kdupet = intval($kdupt);
	$bln = romawi(date('m'));
	$no_sur = ''.$urut.'/'.$kdkpl.'.'.$kdupet.'/PSDKP.'.$kdupet.'/PW340/'.$bln.'/'.date('Y');
	
	return $no_sur;
}

function excel($data, $name){
	$x = $data;
	header("Content-type: application/vnd-ms-excel");
	header("Content-Disposition: attachment; filename=".$name.".xls");
	return $x;
}

function no_tagihan(){
	$ci = & get_instance();
	$userid = $ci->session->userdata('userid');
	
	#ORIGIN : 20/07/00001
	$urutan = 0;
	$sql = $ci->db->query("SELECT MAX(RIGHT(no_tagihan, 5)) AS maks FROM bbm_tagihan WHERE LEFT(RIGHT(no_tagihan,11),2) = RIGHT(YEAR(NOW()),2)")->row_array();
	$urutan = $sql['maks'] + 1;
	$urut 	= $urutan;
	$z = strlen($urut);
	$x = 5-$z;
	for($i=$x;$i>0;$i--){
		$urut = '0'.$urut;
	}
	
	$bln = date('m');
	$no_tagian = $userid.'.'.date('y').'.'.$bln.'.'.$urut;
	
	return $no_tagian;
}


?>