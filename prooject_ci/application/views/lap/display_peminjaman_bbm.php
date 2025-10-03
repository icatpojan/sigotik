<!-- breadcrumb -->
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="<?php base_url();?>"><?php echo $menu2;?></a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span><?php echo $menu1?></span>
        </li>
    </ul>
</div>
<!-- end breadcrumb -->
<div class="row">
    <ul id="tabs">

      <li><a id="tab1" onClick="getTab1()">Laporan Peminjaman BBM</a></li>

	</ul>
	<div class="container2" id="tab1C">
	<div class="row">
	    <?php 
				$tgl1 = date("d-m-Y");
				$tahun1 = date('Y',strtotime($tgl1));
				$time1 = strtotime('01/01/'.$tahun1);
				$tgl2 = date('d-m-Y',$time1);
		?>

		<div class="col-md-3">
			<div class="form-group">
				<label>Periode Dari :</label>
				<input type="text" placeholder="" id="tgl_awal" name="tgl_awal" class="form-control datepicker" style="line-height: 25px;"  value="<?php echo $tgl2; ?>">
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<label>Sampai Dengan :</label>
				<input type="text" placeholder="" id="tgl_akhir" name="tgl_akhir" class="form-control datepicker" style="line-height: 25px;"  value="<?php echo $tgl1; ?>">
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-3">
			<div class="form-group">
				<label>Pilih UPT :</label>
				<select class="form-control custom-control" id="m_upt_code" name="m_upt_code">
				<?php
				$ci = & get_instance();
				$code = $ci->session->userdata('m_upt_code');
				if($code == '000' or $code == '0'){
				    echo '<option value="0">- Semua -</option>';
					$sql = $ci->db->query("SELECT * FROM m_upt ORDER BY nama");
				} else {
					$sql = $ci->db->query("SELECT * FROM m_upt where code = ".$code." ORDER BY nama");					
				}	
				foreach($sql->result() AS $data){
				?>
					<option value="<?php echo $data->code?>"><?php echo $data->nama; ?></option>
				<?php
				}
				?>
			</select>
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<label>Pilih Kapal :</label>
				<select class="form-control custom-control" id="m_kapal" name="m_kapal">
				<?php
				$codeid = $ci->session->userdata('conf_group_id');
				
				if($code == '000' or $code == '0'){
				    echo '<option value="0">- Semua -</option>';
					$sql = $ci->db->query("SELECT * FROM  m_kapal ORDER BY nama_kapal");
				} else {
					if($codeid == '3') {
						
						$sql = $ci->db->query("SELECT * FROM m_kapal where m_upt_code = ".$code." and 
						m_kapal_id IN (SELECT m_kapal_id FROM sys_user_kapal 
						WHERE conf_user_id = '".$this->session->userdata('userid')."')
						ORDER BY nama_kapal ");					

					} else {
						echo '<option value="0">- Semua -</option>';
						$sql = $ci->db->query("SELECT * FROM m_kapal where m_upt_code = ".$code." ORDER BY nama_kapal");					
					}						
				}	
				foreach($sql->result() AS $data){
				?>
					<option value="<?php echo $data->m_kapal_id?>"><?php echo $data->nama_kapal; ?></option>
				<?php
				}
				?>
			</select>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-3">
			<div class="form-group">
				<button type="button" class="btn btn-info mb-sm" onClick="preview()">PREVIEW</button> 
			</div>
		</div>
		<div class="col-md-2">
			<div class="form-group">
				<button type="button" class="btn btn-info mb-sm" onClick="toprint()">PRINT</button> 
			</div>
		</div>
		<div class="col-md-2">
			<div class="form-group">
				<button type="button" class="btn btn-info mb-sm" onClick="toexcel()">EXCEL</button> 
			</div>
		</div>
		
	</div>
	<div id="isiData"></div>
	
		
	</div>
</div>

<script>
$(document).ready(function() {
	$('#tabs li a:not(:first)').addClass('inactive');
	$('.container2').hide();
	$('.container2:first').show();
		
	$('#tabs li a').click(function(){
		var t = $(this).attr('id');
		if($(this).hasClass('inactive')){
			$('#tabs li a').addClass('inactive');           
			$(this).removeClass('inactive');
			
			$('.container2').hide();
			$('#'+ t + 'C').fadeIn('slow');
		}
	});
	
	$('.datepicker').datepicker({
		format: 'dd-mm-yyyy'
	});
});

function preview(){
	var tgl_awal = $('#tgl_awal').val();
	var tgl_akhir = $('#tgl_akhir').val();
	var code = ''+$('#m_upt_code').val();
	var code_kapal = ''+$('#m_kapal').val();
	$('#isiData').load('<?php echo base_url()?>index.php/lap/lap/prevpinjamanbbm/'+tgl_awal+'/'+tgl_akhir+'/'+code+'/'+code_kapal);
}

$("#m_upt_code").change(function () {
    var m_upt_code = $(this).val();
    getKapalAll(m_upt_code);
});

function getKapalAll(m_upt_code) {
	//alert(m_upt_code);
	//alert('<?php echo base_url("lap/Globalc/getKapalAll"); ?>');

    $.ajax({
		type : "POST",
		url	: "<?php echo base_url()?>index.php/monitoring/cari_kapal/getData",
		data: {m_upt_code:m_upt_code},
		dataType: "json",
		success: function (data) {	
            if (data.data_cpa.length > 0) {
                $('#m_kapal').empty();
                $('#m_kapal').append('<option value="0">- Semua -</option>');
                for (i = 0; i < data.data_cpa.length; i++) {
                    var id = data.data_cpa[i].m_kapal_id;
                    var nama = data.data_cpa[i].nama_kapal;
                    opt = '<option value="' + id + '">' + nama + '</option>';
                    $('#m_kapal').append(opt);
                }
            }
        }
    });
}

function toprint(){
	var tgl_awal = $('#tgl_awal').val();
	var tgl_akhir = $('#tgl_akhir').val();
	var code = ''+$('#m_upt_code').val();
	var code_kapal = ''+$('#m_kapal').val();
	
	var xx = '<?php echo base_url()?>'+'index.php/lap/lap_cetak/prevpinjamanbbm_print/'+tgl_awal+'/'+tgl_akhir+'/'+code+'/'+code_kapal;
	var x = window.open(xx,'_blank');
    x.focus();
}

function toexcel(){
	var tgl_awal = $('#tgl_awal').val();
	var tgl_akhir = $('#tgl_akhir').val();
	var code = ''+$('#m_upt_code').val();
	var code_kapal = ''+$('#m_kapal').val();
	
	var xx = '<?php echo base_url()?>'+'index.php/lap/lap_excel/prevpinjamanbbm_xl/'+tgl_awal+'/'+tgl_akhir+'/'+code+'/'+code_kapal;
	var x = window.open(xx,'_blank');
    x.focus();
}
</script>