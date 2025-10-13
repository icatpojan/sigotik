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

<h3 class="page-title"></h3>

<!-- end breadcrumb -->
<?php 
    $ci = & get_instance();
    $usr = $ci->session->userdata('m_upt_code');
    if($usr != '000'){
        ?>
        <div class="row">
        	<div class="col-md-12">
        		<button type="button" class="btn btn-info mb-xl" onClick="getTab2(0)">TAMBAH<i class="fa fa-plus"></i></button> 
        	</div>
        </div>
        <?php
    }
?>


<div class="row">
    <ul id="tabs">

		<li><a id="tab1" onClick="getTab1()">Data Realisasi</a></li>
		<li><a id="tab2">Form Tambah Realisasi</a></li>
		<li><a id="tab3">Form Edit Realisasi</a></li>

	</ul>
	<div class="container" id="tab1C">
		<table id="data_tables" class="table3 table3-striped table3-bordered table3-hover" >
			<thead>
				<tr>
					<th>No</th>
					<th>Kode UPT</th>
					<th>Nama UPT</th>
					<th>Tgl Tagihan</th>
					<th>No Tagihan</th>
					<th>Penyedia</th>
					<th>Quantity (Liter)</th>
					<th>Total (Rp)</th>
					<th>Status</th>
					<th>Aksi</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="11" align="center">
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
		</form>
	</div>
	
	<div class="container" id="tab3C">
		<form id="dataSeri3">
			<div id="isiData3"></div>
			<button type="button" class="btn btn-success mb-xl" onClick="simpan3()">SIMPAN</button>
			<button type="button" class="btn btn-danger mb-xl" onClick="resetfield()">RESET</button> 
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
		'sAjaxSource'    : '<?php echo base_url()?>index.php/angta/tagihan/getDataTable',
		
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
	
	
});

function refreshData(){
	var x = $('#data_tables').dataTable();
	x.fnDestroy();
	$('#data_tables').dataTable({
		'bServerSide'    : true,
		'bAutoWidth'     : false,
		'scrollX'		 : true,
		'sPaginationType': 'full_numbers',
		'sAjaxSource'    : '<?php echo base_url()?>index.php/angta/tagihan/getDataTable',
		
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

/*$(document).on('change', ".form-control", function(){
	var kuan = '';
	var hrg = '';
	// $('#quantity').autoNumeric('init', {aSep: '.', aDec: ',', mDec: '0'});
	// kuan = $('#quantity').val();
	// if(kuan != undefined){
		// var kuantiti = kuan.replace(/\./g,'');
	// }
	// $('#real_quantity').autoNumeric('init', {aSep: '.', aDec: ',', mDec: '0'});
	kuan = $('#real_quantity').val();
	if(kuan != undefined){
		var kuantiti = kuan.replace(/\./g,'');
	}
	// $('#harga').autoNumeric('init', {aSep: ',', aDec: '.', mDec: '0'});
	hrg = $('#harga').val();
	if(hrg != undefined){
		var hrg2 = hrg.replace(/,/g,'');
	}
	
	var hargaperliter = 0;
	$("#harga").each(function(){
		var hargas = $(this).val();
		var harga = hargas.replace(/,/g,'');
		console.log(harga);
		if($.isNumeric(harga)){
			hargaperliter = parseFloat(harga) / parseFloat(kuantiti);
		}
	});
	
	$('#real_quantity').val(kuantiti);
	$('#real_harga').val(hrg2);
	$('#real_hargaperliter').val(hargaperliter);
	hargaperliter = addCommas(hargaperliter);
	$('#hargaperliter').val(hargaperliter);
});*/

/*$(document).on('change', ".form-control", function(){
	var kuan1 = '';
	var hrg1 = '';
	kuan1 = $('#real_quantity1').val();
	if(kuan1 != undefined){
		var kuantiti1 = kuan1.replace(/\./g,'');
	}
	hrg1 = $('#harga1').val();
	if(hrg1 != undefined){
		var hrg21 = hrg1.replace(/,/g,'');
	}
	
	var hargaperliter1 = 0;
	$("#harga1").each(function(){
		var hargas1 = $(this).val();
		var harga1 = hargas1.replace(/,/g,'');
		if($.isNumeric(harga1)){
			hargaperliter1 = parseFloat(harga1) / parseFloat(kuantiti1);
		}
	});
	
	$('#real_quantity1').val(kuantiti1);
	$('#real_harga1').val(hrg21);
	$('#real_hargaperliter1').val(hargaperliter1);
	hargaperliter1 = addCommas(hargaperliter1);
	$('#hargaperliter1').val(hargaperliter1);
});*/

function resetfield(){
	$("input[type='text']").val('');
	$("input[type='date']").val('');
	$("#real_hargaperliter").val(0);
	$("#hargaperliter").empty();
	$("#hargaperliter").append('0');
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

function getTab2(id){
	$('#tab2').show();
	$('#isiData2').load('<?php echo base_url()?>index.php/angta/tagihan/frmdataTagihan/'+id);
	$('#tab2').click();
}

function getTab3(id){
	$('#tab3').show();
	$('#isiData3').load('<?php echo base_url()?>index.php/angta/tagihan/frmdataTagihanEdit/'+id);
	$('#tab3').click();
}

function caridata(call){
	var id = $('#no_so').val();
	var multino = id.replace(/, /g, "x");
	if(id == ''){
		id = 0;
		multino = 0;
	}
	$('#quantity').val(0);
	$('#real_quantity').val(0);
	$('#harga').val('');
	$('#real_harga').val('');
	$('#hargaperliter').val(0);
	$('#real_hargaperliter').val(0);
	$('#fieldso_'+call).load('<?php echo base_url()?>index.php/angta/tagihan/getdtSO/'+multino);
}

function simpan(){
	
	//var x = $('#dataSeri2').serializeArray();
	var data = new FormData();
	var file_data = $('input[name="images"]')[0].files;
	for (var i = 0; i < file_data.length; i++) {
		data.append("images[]", file_data[i]);
	}
	var x = $('#dataSeri2').serializeArray();
	for(var c = 0; c < x.length; c++){
		
		data.append(x[c].name, x[c].value);
	}
		
	$.ajax({
		type : 'POST',
		url: '<?php echo WS_JQGRID."angta.tagihan/create"; ?>',
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

function simpan3(){
	
	//var x = $('#dataSeri3').serializeArray();
	var data = new FormData();
	var file_data = $('input[name="images"]')[0].files;
	for (var i = 0; i < file_data.length; i++) {
		data.append("images[]", file_data[i]);
	}
	var x = $('#dataSeri3').serializeArray();
	for(var c = 0; c < x.length; c++){
		
		data.append(x[c].name, x[c].value);
	}
		
	$.ajax({
		type : 'POST',
		url: '<?php echo WS_JQGRID."angta.tagihan/update"; ?>',
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

function hapus(id){
	var datForm = {
		'id' : id
	};
	
	var del = confirm("Apakah Kamu Yakin Untuk Hapus Data ini?");
	if (del){
		
	   $.ajax({
		type : 'POST',
		url: '<?php echo WS_JQGRID."angta.tagihan/destroy"; ?>',
		data : datForm,
		dataType : 'json',
		success : function(data){
				alert(data.message);
				refreshData();
			}
		});
		
	}
}

</script>