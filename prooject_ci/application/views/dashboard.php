<!-- breadcrumb -->
<script src="theme/highcharts/highcharts.js"></script>
<script src="theme/highcharts/exporting.js"></script>
<script src="theme/highcharts/export-data.js"></script>
	
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="<?php base_url();?>">Home</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>Dashboard</span>
        </li>
    </ul>
</div>
<!-- end breadcrumb -->
<?php 
		$ci = & get_instance();
		//conf_user_id
		//conf_group_id
		//
		$code = $ci->session->userdata('m_upt_code');
		$xuserid = $ci->session->userdata('userid');
		$xstatususer = $ci->session->userdata('conf_group_id');
		$tgl_akhir = date("Y-m-d");
		$tahun = date('Y',strtotime($tgl_akhir));
		$time = strtotime('01/01/'.$tahun);
		$tgl_awal = date('Y-m-d',$time);
		if($tgl_awal == '0' && $tgl_akhir == '0'){
			$priodeaw = '-';
			$priodeak = '-';
			$tahun = '0';
		}else{
			$priodeaw = strtoupper(indo_date($tgl_awal));
			$priodeak = strtoupper(indo_date($tgl_akhir));
			$tahun = date('Y',strtotime($tgl_awal));
		}
		//$prbhanke = $ci->db->query("SELECT MAX(perubahan_ke) AS maks FROM bbm_anggaran WHERE statusanggaran = '1' AND periode = '".$tahun."'")->row_array();
		$prbhanke = $ci->db->query("SELECT MAX(perubahan_ke) AS maks FROM bbm_anggaran WHERE m_upt_code = '".$code."' AND periode = '".$tahun."'")->row_array();
		$ss = "SELECT m_upt.code, m_upt.nama, anggaran,
								(
									SELECT IF(SUM(total) IS NULL, '0', SUM(total)) FROM bbm_tagihan 
									WHERE m_upt_code = bbm_anggaran.m_upt_code AND statustagihan = '1' AND tanggal_invoice BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."'
									
								) AS tagihan
								FROM `bbm_anggaran` 
								JOIN m_upt ON m_upt.`code` = bbm_anggaran.m_upt_code ";
		if($xstatususer==1 or $xstatususer==5) {
			$ss .=	"WHERE ";
		}else {
			$ss .=	"WHERE m_upt.code = ".$code." and ";
		}
		$ss .= "statusanggaran = '1' AND periode = '".$tahun."' AND perubahan_ke = '".$prbhanke['maks']."' ";		
		
		
		$sql = $ci->db->query($ss);
		$jum = $sql->num_rows();
		
		$ss =  "SELECT IFNULL(SUM(penerimaan),0) AS penerimaan,IFNULL(SUM(penggunaan),0) AS penggunaan
				FROM
				(SELECT kapal_code,tanggal_surat,a.nomor_surat,SUM(volume_isi) AS penerimaan,0 AS penggunaan
				FROM bbm_kapaltrans a,bbm_transdetail b,m_kapal c 
				WHERE status_upload = 1 AND status_ba = '5' 
				AND a.nomor_surat=b.nomor_surat AND TRIM(c.code_kapal) = TRIM(a.kapal_code)
				AND tanggal_surat >= '".$tgl_awal."' AND tanggal_surat <= '".$tgl_akhir."' ";
		if($xstatususer==1 or $xstatususer==5) {
			$ss .=	" ";
		}elseif($xstatususer==2) {			
			$ss .=	" AND m_upt_code = '".$code."' GROUP BY m_upt_code  ";
		}else {
			$ss .=	" AND kapal_code = (SELECT f.code_kapal FROM sys_user_kapal b JOIN m_kapal f ON b.m_kapal_id = f.m_kapal_id WHERE b.conf_user_id = '".$ci->session->userdata('userid')."') GROUP BY kapal_code ";
		}		
		$ss .= "UNION 
				SELECT kapal_code,tanggal_surat,nomor_surat,0 AS penerimaan,sum(volume_pemakaian) AS  penggunaan
				FROM bbm_kapaltrans a,m_kapal b WHERE status_upload = 1 AND status_ba = '3' 
				AND tanggal_surat >= '".$tgl_awal."' AND tanggal_surat <= '".$tgl_akhir."' 
				AND a.kapal_code = b.code_kapal ";
		if($xstatususer==1 or $xstatususer==5) {
			$ss .=	" ";
		}elseif($xstatususer==2) {			
			$ss .=	" AND m_upt_code = '".$code."' GROUP BY m_upt_code  ";
		}else {
			$ss .=	" AND kapal_code = (SELECT f.code_kapal FROM sys_user_kapal b JOIN m_kapal f ON b.m_kapal_id = f.m_kapal_id WHERE b.conf_user_id = '".$ci->session->userdata('userid')."') GROUP BY kapal_code ";
		}	
		$ss .= " ) trans ";
		$sql2 = $ci->db->query($ss);
		$jum2 = $sql2->num_rows();
		
		
		$prbhanke = $ci->db->query("SELECT MAX(perubahan_ke) AS maks FROM bbm_anggaran WHERE m_upt_code = '".$code."' AND periode = '".$tahun."'")->row_array();
		if($code==0) {
			$upt =	" ";
		}else {
			$upt =	"AND m_upt_code = ".$code."  ";
		}
		$ss = "select ifnull(sum(anggaran),0) as anggaran, tagihan
		FROM
		(SELECT m_upt.code, m_upt.nama,anggaran,
								
									(
                                    	SELECT SUM(bbm_tagihan) as tagihan FROM ( 		
                                        SELECT 0 AS bbm_tagihan
                                    	FROM bbm_anggaran WHERE 1=1 ".$upt." AND periode = '".$tahun."' AND perubahan_ke = '".$prbhanke['maks']."'
                                    	UNION
                                    	SELECT total AS bbm_tagihan FROM bbm_tagihan
                                    	WHERE 1=1  ".$upt." AND statustagihan = 1 AND tanggal_invoice BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."' 
                                    ) as tagihan
									
								) AS tagihan
		
								FROM `bbm_anggaran` 
								JOIN m_upt ON m_upt.code = bbm_anggaran.m_upt_code ";
		
		if($code==0) {
			$ss .=	"WHERE ";
		}else {
			$ss .=	"WHERE m_upt.code = ".$code." and ";
		}
		$ss .= "statusanggaran = '1' AND periode = '".$tahun."' AND perubahan_ke = '".$prbhanke['maks']."') jmlx ";		
		
		$sql3 = $ci->db->query($ss);
		//$totalantag = 0;
		//$totalantag1 = 0;
		//$totalanang1 = 0;
		//foreach($sql3->result() AS $listawal){
        //$totalanang1 += $listawal->anggaran;
        //$totalantag1 += $listawal->tagihan;
        //}
		//SAMPLE QUERY
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
		
?>
<h3 class="page-title"> Dashboard,<small> Selamat Datang di SIGOTIK BBM</small></h3>
<div class="row">
	<div class="alert alert-success" role="alert">
		<strong class="green"><center>Periode Pelaporan 
		<?php echo $priodeaw; ?>
		 Sampai dengan <?php echo $priodeak ?></center></strong>	
	</div>
</div>

<div class="row">
                            <div class="col-lg-3 col-xs-6">
                                <!-- small box -->
                                <div class="alert alert-success">
                                    <div class="inner">
                                        <h3>
                                            <td>
												<?php foreach($sql2->result() AS $list2): 
												      echo number_format($list2->penggunaan,0,',','.');?>
                                            </td>
                                        </h3>
                                        <p>
                                            Total Penggunaan BBM
                                            </br><small>(Liter)</small>
                                        </p>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-bag"></i>
                                    </div>
                                    <a href="#" class="small-box-footer">
                                        More info <i class="fa fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
							<div class="col-lg-3 col-xs-6">
                                <!-- small box -->
                                <div class="alert alert-info">
                                    <div class="inner">
                                        <h3>
                                            <td>
                                                <?php echo number_format($list2->penerimaan,0,',','.');?>
                                            </td>
                                        </h3>
                                        <p>
                                            Total Pengisian BBM
                                            </br><small>(Liter)</small>
                                            
                                        </p>
                                    </div>
									<?php endforeach; ?>
                                    <div class="icon">
                                        <i class="ion ion-bag"></i>
                                    </div>
                                    <a href="#" class="small-box-footer">
                                        More info <i class="fa fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
							<div class="col-lg-3 col-xs-6">
                                <!-- small box -->
                                <div class="alert alert-warning">
                                    <div class="inner">
                                        <h3>
                                            <td>
                                              <?php  
												    
															echo number_format($sanggarans,0,',','.');
														
														
														?>  
                                            </td>
                                        </h3>
                                        <p>
                                            Total Anggaran BBM
                                            </br><small>(Rupiah)</small>
                                        </p>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-bag"></i>
                                    </div>
                                    <a href="#" class="small-box-footer">
                                        More info <i class="fa fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
							<div class="col-lg-3 col-xs-6">
                                <!-- small box -->
                                <div class="alert alert-danger">
                                    <div class="inner">
                                        <h3>
                                            <td>
                                                <?php echo number_format($stagihans,0,',','.');?>
                                            </td>
                                        </h3>
                                        <p>
                                            Total Realisasi BBM
                                            </br><small>(Rupiah)</small>
                                        </p>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-bag"></i>
                                    </div>
									
                                    <a href="#" class="small-box-footer">
                                        More info <i class="fa fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
</div>	

<div class="row">

                        <!-- Left col -->
                        <section class="col-lg-12 connectedSortable"> 
							<!-- Membuat area untuk menampilkan grafik -->
							<?php
							//	$kalimat1 = '';
							//	$anggaran1 = '';
							//	$realisasi1= '';
							//	$aa=0;
							//	foreach($sql->result() AS $list){
							//		if($list->code == ''){
							//			$kond = "";
							//		}else{
							//			$kond = "AND m_upt_code = '".$list->code."' ";
							//		}
							//		$sqla1 = $ci->db->query("SELECT * FROM bbm_anggaran_upt WHERE YEAR(tanggal_trans) = '".$tahun."' ".$kond."");
							//		$jm1 = $sqla1->num_rows();
							//		$anggarans1 = $list->anggaran;
							//		if($jm1 > 0){
							//			foreach($sqla1->result() as $lsty1){
							//				$anggarans1 += $lsty1->nominal;
							//			}
							//		}else{
							//			$anggarans1 = $list->anggaran;
							//		}
							//		if($aa == 0){
							//			$koma = '';
							//		} else {
							//			$koma = ",";
							//		}	
							//		$aa++;
							//		$kalimat1   .= $koma."'".$list->nama."'";
							//		$anggaran1  .= $koma.$anggarans1;
							//		$realisasi1 .= $koma.$list->tagihan;
							//	}							
							?>
							<div id="grafik_batang" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
							<!-- Script untuk membuat grafik batang -->

                            <!-- Custom tabs (Charts with tabs)-->
                            <div class="box-body no-padding">
                                <div class="table-responsive">
                                    <!-- .table - Uses sparkline charts-->
                                    <table class="table3 table3-striped table3-bordered table3-hover">
                                        <tr>
                                            <th colspan = "5" style="text-align:right;">Dalam Rupiah</th>
										</tr>
                                        <tr>
                                            <th>Nama UPT</th>
                                            <th>Anggaran</th>
                                            <th>Realisasi</th>
                                            <th>Sisa Anggaran</th>
                                            <th>Presentase</th>
										</tr>
										<?php 
                                		if($code==0){
                                        	$kcode = "";
                                        }else{
                                        	$kcode = "AND code = '".$code."'";
                                        }
                                		$uptss = $ci->db->query("SELECT * FROM m_upt WHERE 1=1 ".$kcode." ORDER BY m_upt_id");
										$jumz = $uptss->num_rows();

                                        $kalimat21 = '';
										$anggaran21 = '';
										$realisasi21= '';
										if($jumz > 0){
                                        $aa = 0;
										$anggarans2 = 0;
        								foreach($uptss->result() as $uptssls){
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
                                       			
                                       			//if($qtabel['tagihan']<=0 or $anggarans2<=0){
												//	$persentase = 0;
												//} else {	
										    	//	$persentase = ($qtabel['tagihan'] / $anggarans2) * 100;
												//}
                                        		
                                        if($uptssls->code == 000){
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
													
                                        ?>
                                        <tr>
											<td><?php echo $qtabel['nama']; ?></td>
											<td style="text-align:right;"><?php echo 'Rp. '.number_format($anggarans2,0,',','.');?></td>
											<td style="text-align:right;"><?php echo 'Rp. '.number_format($tagihan,0,',','.');?></td>
											<td style="text-align:right;"><?php echo 'Rp. '.number_format($sisa_anggaran,0,',','.');?></td>
											<td style="text-align:right;"><?php echo number_format($persentase,2,',','.').' %';?></td>
										</tr>
                                        <?php
                                        if($aa == 0){
											$koma = '';
										} else {
											$koma = ",";
										}	
										$aa++;
										$kalimat21   .= $koma."'".$qtabel['nama']."'";
										$anggaran21  .= $koma.$anggarans2;
										$realisasi21 .= $koma.$tagihan;
        								}
                                
                                		
                                        ?>
										   
										
										<?php } else {?>
										<tr>
											<td colspan = "5" style="text-align: center"> -- Data kosong -- `</td>
										</tr>
										
										<?php } ?>
                                    </table><!-- /.table -->
                                </div>
                            </div>
						</section>
</div>	

 <script type="text/javascript">

        Highcharts.chart('grafik_batang', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Perbandingan Anggaran Dan Realisasi BBM'
            },
            subtitle: {
                text: 'Periode <?php echo $priodeaw; ?> Sampai dengan <?php echo $priodeak ?> (Dalam Rupiah)'
            },
            xAxis: {
                categories: [
                    <?php echo $kalimat21; ?>
                ],
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Nominal'
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>Rp. {point.y}</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [{
                name: 'Anggaran',
                data: [<?php echo $anggaran21; ?>]

            }, {
                name: 'Realisasi',
                data: [<?php echo $realisasi21; ?>]

            }]
        });
 </script>                               					


						