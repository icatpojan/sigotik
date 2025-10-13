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


<div class="row">
    <ul id="tabs">

		<li><a id="tab1" onClick="getTab1()">Data Realisasi</a></li>
		<li><a id="tab2">View Data Realisasi</a></li>
		<li><a id="tab3">Input Tanggal SPPD</a></li>
        <li><a id="tab4">Upload File</a></li>

	</ul>
	<div class="container" id="tab1C">
		<table id="data_tables" class="table3 table3-striped table3-bordered table3-hover" >
			<thead>
				<tr>
					<th>No</th>
					<th>Kode UPT</th>
					<th>Tgl Tagihan</th>
					<th>No Tagihan</th>
					<th>Penyedia</th>
					<th>Quantity (Liter)</th>
					<th>Total (Rp)</th>
					<th>Status</th>
            		<th>View Upload SPPD</th>
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
			<button type="button" class="btn btn-info mb-xl" onClick="back()">KEMBALI</button> 
		</form>
	</div>
	
	<div class="container" id="tab3C">
		<form id="dataSeri3">
			<div id="isiData3"></div>
			<button type="button" class="btn btn-success mb-xl" onClick="simpan()">SIMPAN</button>
			<button type="button" class="btn btn-danger mb-xl" onClick="resetfield()">RESET</button> 
		</form>
	</div>
	<div class="container" id="tab4C">
		<form id="dataUpld"  enctype="multipart/form-data">
			<div id="dataUlang"></div>
			<br>
			<button type="button" class="btn btn-success mb-xl" onClick="simpanUp()">SIMPAN</button>
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
		'sAjaxSource'    : '<?php echo base_url()?>index.php/angta/tagihan/getDataTableSppd',
		
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
		'sAjaxSource'    : '<?php echo base_url()?>index.php/angta/tagihan/getDataTableSppd',
		
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

function back(){
	$('#tab1').click();
	refreshData();
}

function getTab1(){
	refreshData();
	$('#tab2').hide();
	$('#tab3').hide();
}

function getTab2(id){
	$('#tab2').show();
	$('#isiData2').load('<?php echo base_url()?>index.php/angta/tagihan/vwdataTagihansppd/'+id);
	$('#tab2').click();
}

function getTab3(id){
	$('#tab3').show();
	$('#isiData3').load('<?php echo base_url()?>index.php/angta/tagihan/frmdataTagihansppd/'+id);
	$('#tab3').click();
}

function getTab4(id){
	$('#tab4').show();
	$('#dataUlang').load('<?php echo base_url()?>index.php/angta/tagihan/getDataUpload/'+id);
	$('#tab4').click();
}

function simpanUp(){
	
	
	var data = new FormData();

//Form data
	var form_data = $('#dataUpld').serializeArray();
	var tagihan_id = $('#tagihan_id').val();
	// alert(nomor);
//Custom data
	var file_data = $('input[name="my_images"]')[0].files;
	for (var i = 0; i < file_data.length; i++) {
		data.append("my_images[]", file_data[i]);
	}
	
	data.append("tagihan_id", tagihan_id);
	
	$.ajax({
		type : 'POST',
		url: '<?php echo WS_JQGRID."angta.tagihan/uploadsppd"; ?>',
		data : data,
		contentType: false,
        processData: false,
		dataType : 'json',
		success : function(data){
			
			
				alert(data.message);
				$('#tab1').click();
				refreshData();
			
		}
	});

}

function simpan(){
	
	var x = $('#dataSeri3').serializeArray();
		
	$.ajax({
		type : 'POST',
		url: '<?php echo WS_JQGRID."angta.tagihan/upsppd"; ?>',
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