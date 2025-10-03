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

      <li><a id="tab1" onClick="getTab1()">Laporan Penitipan BBM</a></li>

	</ul>
	<div class="container2" id="tab1C">
		<?php 
				$tgl1 = date("d-m-Y");
				$tahun1 = date('Y',strtotime($tgl1));
				$time1 = strtotime('01/01/'.$tahun1);
				$tgl2 = date('d-m-Y',$time1);
		?>

	<div class="row">
		<div class="col-md-3">
			<div class="form-group">
				<label>Periode Dari :</label>
				<input type="text" placeholder="" id="tgl_awal" name="tgl_awal" class="form-control datepicker" style="line-height: 25px;"  value="<?php echo $tgl2 ?>">
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<label>Sampai Dengan :</label>
				<input type="text" placeholder="" id="tgl_akhir" name="tgl_akhir" class="form-control datepicker" style="line-height: 25px;"  value="<?php echo $tgl1 ?>">
			</div>
		</div>
	</div>
	<!--<div class="row">
			
				 <div class="col-md-7">
					<div class="form-group">
						<label>Kapal</label>
						<select class="form-control custom-control" id="m_kapal_id" name="m_kapal_id" onChange="getVal()">
							<?php
								$ci = & get_instance();
								$code = $ci->session->userdata('m_upt_code');
								if($code == '000' or $code == '0'){
									$sql = "SELECT * FROM m_kapal  order by code_kapal "; 
								} else  {
									$sql = "SELECT * FROM m_kapal WHERE m_kapal_id IN (SELECT m_kapal_id FROM sys_user_kapal WHERE conf_user_id = '".$this->session->userdata('userid')."' ) "; 									
								}
								
								$query = $ci->db->query($sql);
								foreach ($query->result() as $list){
									echo '<option value="'.$list->m_kapal_id.'">'.$list->nama_kapal.'</option>';
								}
							?>
						</select>
					</div>
				</div>
	</div>	-->		
	<div class="row">
		<div class="col-md-2">
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
	var m_kapal_id = $("#m_kapal_id").val();
	$('#isiData').load('<?php echo base_url()?>index.php/lap/lap/showpenitipanbbm/'+tgl_awal+'/'+tgl_akhir+"/"+m_kapal_id);
}

function toprint(){
	var tgl_awal = $('#tgl_awal').val();
	var tgl_akhir = $('#tgl_akhir').val();
	var m_kapal_id = $("#m_kapal_id").val();
	
	var xx = '<?php echo base_url()?>'+'index.php/lap/lap_cetak/showpenitipanbbm/'+tgl_awal+'/'+tgl_akhir+"/"+m_kapal_id;
	var x = window.open(xx,'_blank');
    x.focus();
}

function toexcel(){
	var tgl_awal = $('#tgl_awal').val();
	var tgl_akhir = $('#tgl_akhir').val();
	var m_kapal_id = $("#m_kapal_id").val();
	
	var xx = '<?php echo base_url()?>'+'index.php/lap/lap_excel/showpenitipanbbm/'+tgl_awal+'/'+tgl_akhir+"/"+m_kapal_id;
	var x = window.open(xx,'_blank');
    x.focus();
}

</script>