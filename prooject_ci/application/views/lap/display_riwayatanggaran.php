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

      <li><a id="tab1" onClick="getTab1()">Laporan Penggunaan Anggaran dan Tagihan BBM</a></li>

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
	$('#isiData').load('<?php echo base_url()?>index.php/lap/lap/showdtotalpake/'+tgl_awal+'/'+tgl_akhir);
}
function toprint(){
	var tgl_awal = $('#tgl_awal').val();
			if(tgl_awal == ''){
				tgl_awal = 0;
			}
			var tgl_akhir = $('#tgl_akhir').val();
			if(tgl_akhir == ''){
				tgl_akhir = 0;
			}
			
			if(($('#m_upt_code').length) > 0){
				var code = ''+$('#m_upt_code').val();
			}else{
				var code = '0';
			}
	
	var xx = '<?php echo base_url()?>'+'index.php/lap/lap_cetak/showdtotalpake/'+tgl_awal+'/'+tgl_akhir;
	var x = window.open(xx,'_blank');
    x.focus();
}
function toexcel(){
	var tgl_awal = $('#tgl_awal').val();
			if(tgl_awal == ''){
				tgl_awal = 0;
			}
			var tgl_akhir = $('#tgl_akhir').val();
			if(tgl_akhir == ''){
				tgl_akhir = 0;
			}
			
			if(($('#m_upt_code').length) > 0){
				var code = ''+$('#m_upt_code').val();
			}else{
				var code = '0';
			}
	
	var xx = '<?php echo base_url()?>'+'index.php/lap/lap_excel/showdtotalpake/'+tgl_awal+'/'+tgl_akhir;
	var x = window.open(xx,'_blank');
    x.focus();
}
</script>