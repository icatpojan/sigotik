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

      <li><a id="tab1" onClick="getTab1()">Laporan Transaksi Perubahan Anggaran UPT</a></li>

	</ul>
	<div class="container2" id="tab1C">
	<div class="row">
		<div class="col-md-3">
			<div class="form-group">
				<label>Periode </label>
					<select class="form-control custom-control" id="periode" name="periode">
					<?php
						$ci = & get_instance();
						$sql = $ci->db->query("SELECT * FROM bbm_anggaran GROUP BY periode");
						foreach($sql->result() AS $data){
								echo '<option value="'.$data->periode.'">'.$data->periode.'</option>';	
						}
					?>
					</select>
			</div>
		</div>
	</div>
	<div id="upts"></div>
	<div class="row">
		<div class="col-md-2">
			<div class="form-group">
				<button type="button" class="btn btn-info mb-sm" onClick="previews()">PREVIEW</button> 
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
	</br>
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
	upt();
});

function upt(){
	$('#upts').load('<?php echo base_url()?>index.php/lap/lap/getupt');
}


function previews(){
			var periode = $('#periode').val();
			if(($('#m_upt_code').length) > 0){
				var code = ''+$('#m_upt_code').val();
			}else{
				var code = '0';
			}
			$('#isiData').load('<?php echo base_url()?>index.php/lap/lap/showdinternal/'+periode+'/'+code);
		}

function toprint(){
	var periode = $('#periode').val();
	if(($('#m_upt_code').length) > 0){
		var code = ''+$('#m_upt_code').val();
	}else{
		var code = '0';
	}
	
	var xx = '<?php echo base_url()?>'+'index.php/lap/lap_cetak/showdinternal/'+periode+'/'+code;
	var x = window.open(xx,'_blank');
    x.focus();
}

function toexcel(){
	var periode = $('#periode').val();
	if(($('#m_upt_code').length) > 0){
		var code = ''+$('#m_upt_code').val();
	}else{
		var code = '0';
	}
	
	var xx = '<?php echo base_url()?>'+'index.php/lap/lap_excel/showdinternal/'+periode+'/'+code;
	var x = window.open(xx,'_blank');
    x.focus();
}		
		
</script>