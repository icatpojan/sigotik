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
<!--
<h3 class="page-title"></h3>
<div class="row">
	<div class="col-md-12">
		<button type="button" class="btn btn-info mb-xl" onClick="getTab2()">TAMBAH<i class="fa fa-plus"></i></button> 
	</div>
</div>-->

<div class="row">
    <ul id="tabs">

      <li><a id="tab1" onClick="getTab1()">Data Anggaran</a></li>
      <li><a id="tab2">Form Perubahan Anggaran</a></li>
      <li><a id="tab3">Lihat Anggaran</a></li>
      <li><a id="tab4">Form Edit Anggaran</a></li>

	</ul>
	<div class="container" id="tab1C">
		<table id="data_tables" class="table3 table3-striped table3-bordered table3-hover" >
			<thead>
				<tr>
					<th>No</th>
					<th>Periode</th>
					<th>Total Anggaran</th>
					<th>Keterangan</th>
					<th>User Input</th>
					<th>Tgl Input</th>
					<th>Status Approval</th>
					<!--<th>User Approval</th>
					<th>Tgl Approval</th>-->
					<th>Aksi</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="10" align="center">
						<img src="<?php echo base_url();?>assets/img/loadingAnimation.gif">
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<div class="container" id="tab2C">
		<form id="dataSeri2">
			<div id="isiData2"></div>
			<button type="button" class="btn btn-success mb-xl" onClick="simpan()">SIMPAN</button>
			<button type="button" class="btn btn-danger mb-xl" onClick="resetfield()">RESET</button> 
			<button type="button" class="btn btn-info mb-xl" onClick="batal()">BATAL</button> 
		</form>
	</div>
	
	<div class="container" id="tab3C">
		<form id="dataSeri3">
			<div id="isiData3"></div>
		</form>
	</div>
	
	<div class="container" id="tab4C">
		<form id="dataSeri4">
			<div id="isiData4"></div>
			<button type="button" class="btn btn-success mb-xl" onClick="simpan4()">SIMPAN</button>
			<button type="button" class="btn btn-danger mb-xl" onClick="resetfield()">RESET</button> 
			<button type="button" class="btn btn-info mb-xl" onClick="batal()">BATAL</button> 
		</form>
	</div>
	
</div>

<script>
$(document).ready(function() {
	$('#tabs li a:not(:first)').addClass('inactive');
	$('.container').hide();
	$('.container:first').show();
		
	$('#tabs li a').click(function(){
		var t = $(this).attr('id');
		if($(this).hasClass('inactive')){
			$('#tabs li a').addClass('inactive');           
			$(this).removeClass('inactive');
			
			$('.container').hide();
			$('#'+ t + 'C').fadeIn('slow');
		}
	});
	
	$('#data_tables').dataTable({
		'bServerSide'    : true,
		'bAutoWidth'     : false,
		'scrollX'		 : true,
		'sPaginationType': 'full_numbers',
		'sAjaxSource'    : '<?php echo base_url()?>index.php/angta/anggaran/getDataTablePerubahan',
		
		'fnServerData': function(sSource, aoData, fnCallback){
			$.ajax({
				'dataType': 'json',
				'type'    : 'POST',
				'url'     : sSource,
				'data'    : aoData,
				'success' : fnCallback
			});
		}
	});
	
	$('#tab2').hide();
	$('#tab3').hide();
	$('#tab4').hide();
});

function refreshData(){
	var x = $('#data_tables').dataTable();
	x.fnDestroy();
	$('#data_tables').dataTable({
		'bServerSide'    : true,
		'bAutoWidth'     : false,
		'scrollX'		 : true,
		'sPaginationType': 'full_numbers',
		'sAjaxSource'    : '<?php echo base_url()?>index.php/angta/anggaran/getDataTablePerubahan',
		
		'fnServerData': function(sSource, aoData, fnCallback){
			$.ajax({
				'dataType': 'json',
				'type'    : 'POST',
				'url'     : sSource,
				'data'    : aoData,
				'success' : fnCallback
			});
		}
	});
}

// $(document).on("change", ".form-control", function(){
	// var totalAng = 0;
    // $("input[type='text']").each(function(){
		// $(this).autoNumeric('init', {aSep: ',', aDec: '.', mDec: '0'});
		// var anggarans = $(this).val();
		// var anggaran = anggarans.replace(/,/g,'');
		// if($.isNumeric(anggaran)){
			// totalAng += parseFloat(anggaran);
		// }
    // });
	// totalAng = addCommas(totalAng);
    // $("#total_anggaran").val('Rp. '+totalAng+',-');
// });

function resetfield(){
	$("input[type='text']").val('');
	$("#total_anggaran").empty();
	$("#total_anggaran").append('0');
}

function batal(){
	$('#tab1').click();
	refreshData();
}

function already(th){
	alert('Periode '+th+' Sudah Diinputkan Sebelumnya');
	$('#periode').val(0);
}

function getTab1(){
	refreshData();
	$('#tab2').hide();
	$('#tab3').hide();
}

function update(tahun,perubahan){
	$('#tab2').show();
	$('#isiData2').load('<?php echo base_url()?>index.php/angta/anggaran/updatedataPeriode/'+tahun+'/'+perubahan);
	$('#tab2').click();
}

function pending(thn,ke){
	alert('Perubahan Anggaran Ke '+ke+' Tahun '+thn+' Belum Disetujui !');
}

function getTab3(tahun,perubahan){
	$('#tab3').show();
	$('#isiData3').load('<?php echo base_url()?>index.php/angta/anggaran/previewdataPeriode/'+tahun+'/'+perubahan);
	$('#tab3').click();
}

function getTab4(tahun,perubahan){
	$('#tab4').show();
	$('#isiData4').load('<?php echo base_url()?>index.php/angta/anggaran/updatedataPeriode/'+tahun+'/'+perubahan);
	$('#tab4').click();
}

function simpan(){
	
	var x = $('#dataSeri2').serializeArray();
		
	$.ajax({
		type : 'POST',
		url: '<?php echo WS_JQGRID."angta.anggaran/update"; ?>',
		data : x,
		dataType : 'json',
		success : function(data){
			alert(data.message);
			$('#tab1').click();
			refreshData();
		}
	});
}

function simpan4(){
	
	var x = $('#dataSeri4').serializeArray();
		
	$.ajax({
		type : 'POST',
		url: '<?php echo WS_JQGRID."angta.anggaran/updteperub"; ?>',
		data : x,
		dataType : 'json',
		success : function(data){
			alert(data.message);
			$('#tab1').click();
			refreshData();
		}
	});
}

</script>