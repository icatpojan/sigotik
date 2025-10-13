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
<div class="row">
	<div class="col-md-12">
	    <?php
	      if($this->session->userdata('conf_group_id') == 3){
	           	echo '<button type="button" class="btn btn-info mb-xl" onClick="getTab2(0)">TAMBAH<i class="fa fa-plus"></i></button>';
	      }else{
	            echo '<button type="button" class="btn btn-info mb-xl" onClick="getTab2(0)" disabled>TAMBAH<i class="fa fa-plus"></i></button>';
	      }
		
		?>
	</div>
</div>

<div class="row">
	
	<ul id="tabs">

      <li><a id="tab1" onClick="getTab1()">Data</a></li>
      <li><a id="tab2">Form BA Pemeriksaan sarana pengisian</a></li>
      <li><a id="tab3">Form Upload</a></li>
      <li><a id="tab4">Form Edt BA Pemeriksaan sarana pengisian</a></li>
      <li><a id="tab5">Data <?php echo $menu1?></a></li>
	</ul>
	 
  <div class="container" id="tab1C">
	<table id="data_tables" class="table3 table3-striped table3-bordered table3-hover" >
		<thead>
			<tr>
				<th width="4%">No</th>
				<th>Nomor Surat</th>
				<th>Tanggal Surat</th>
				<th>Cetak Ulang</th>
				<th>Aksi</th>
				<th>View Dok</th>
        		<th>Release</th>

			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="6" align="center">
					<img src="<?php echo base_url();?>assets/img/loadingAnimation.gif">
				</td>
			</tr>
		</tbody>
	</table>
  </div>
  
  
  
	<div class="container" id="tab2C">
		<form id="dataSeri">
		    <div id="dataCreate"></div>
			<br>
			<button type="button" class="btn btn-warning" onClick="createDok()">Buat Dokumen</button>
	
		</form>
	</div>     

	<div class="container" id="tab3C">
		<form id="dataUpld"  enctype="multipart/form-data">
			<div id="dataUlang"></div>
			<br>
			<button type="button" class="btn btn-success mb-xl" onClick="simpanUp()">SIMPAN</button>
		</form>
   </div>
    
     <div class="container" id="tab4C">
		<form id="dataEdit">
			<div id="dataRubah"></div>
			<br>
			<button type="button" class="btn btn-success mb-xl" onClick="editDok()">SIMPAN</button>
		</form>
   </div>
   
   <div class="container" id="tab5C">
        <div id="dataLihat"></div>
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
	
//	getVal();
	
	$('#data_tables').dataTable({
		'bServerSide'    : true,
		'bAutoWidth'     : false,
		'scrollX'		 : true,
		'sPaginationType': 'full_numbers',
		'sAjaxSource'    : '<?php echo base_url()?>index.php/monitoring/ba_pemeriksaan_sarana_pengisian/getDataTable',
		
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
	$('#tab5').hide();
});

function getTab1(){
	refreshData();
	$('#tab2').hide();
	$('#tab3').hide();
	$('#tab4').hide();
	$('#tab5').hide();
}

function getTab2(){
	
	$('#tab2').show();
	$('#dataCreate').load('<?php echo base_url()?>index.php/monitoring/ba_pemeriksaan_sarana_pengisian/getDataForm');
	$('#tab2').click();
	$('#tab3').hide();
	$('#tab4').hide();
	$('#tab5').hide();
}

function getTab3(trans_id){
	// alert(nomor_surat);
	$('#tab3').show();
	$('#dataUlang').load('<?php echo base_url()?>index.php/monitoring/ba_pemeriksaan_sarana_pengisian/getDataUpload/'+trans_id);
	$('#tab3').click();
}

function getTab4(trans_id){
	$('#tab4').show();
	$('#dataRubah').load('<?php echo base_url()?>index.php/monitoring/ba_pemeriksaan_sarana_pengisian/getDataEdit/'+trans_id);
	$('#tab4').click();
}

function getTab5(trans_id){
	$('#tab5').show();
	$('#dataLihat').load('<?php echo base_url()?>index.php/monitoring/ba_pemeriksaan_sarana_pengisian/getDataLihat/'+trans_id);
	$('#tab5').click();
}

function refreshData(){
	var x = $('#data_tables').dataTable();
	x.fnDestroy();
	$('#data_tables').dataTable({
			'bServerSide'    : true,
			'bAutoWidth'     : false,
			'scrollX'		 : true,
			'sPaginationType': 'full_numbers',
			'sAjaxSource'    : '<?php echo base_url()?>index.php/monitoring/ba_pemeriksaan_sarana_pengisian/getDataTable',
			
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

function getVal(){
	
	var m_kapal_id = $("#m_kapal_id").val();
	
	$.ajax({
		type : "POST",
		url	: "<?php echo base_url()?>index.php/monitoring/ba_sisa_sblm_pengisian/getData",
		data: {m_kapal_id:m_kapal_id},
		dataType: "json",
		success: function (data) {	
			$("#code_kapal").val(data.code_kapal);
			$("#lokasi_surat").val(data.kota);
			$("#nama_petugas").val(data.nama_petugas);
			$("#nip_petugas").val(data.nip_petugas);
			$("#nama_nakoda").val(data.nama_nakoda);
			$("#nip_nakoda").val(data.nip_nakoda);
			$("#nama_kkm").val(data.nama_kkm);
			$("#nip_kkm").val(data.nip_kkm);
			$("#zona_waktu_surat").val(data.zona_waktu_upt);
			$("#alamat1").val(data.alamat1);
			$("#jabatan_staf_pangkalan").val(data.jabatan_petugas);
		}
	});
}

function createDok(){

	var data = new FormData();
	
	$("#status_segel").removeAttr("disabled");
    $("#status_flowmeter").removeAttr("disabled");
    
	var form_data = $('#dataSeri').serializeArray();
	
	$.each(form_data, function (key, input) {
		data.append(input.name, input.value);
	});

    jQuery.each(jQuery('input[type=file]'), function(i, value) {
        data.append('lamp_edit['+i+']', value.files[0]);
    });

	data.append('key', 'value');
	
	var nomor_surat = $('#nomor_surat').val();
	
	if(nomor_surat == ''){
	    
	    alert("Nomor Surat Tidak Boleh Kosong");
	    
	}else{
	
	    $.ajax({
    		type : 'POST',
    		url: '<?php echo WS_JQGRID."monitoring.ba_pemeriksaan_sarana_pengisian/create"; ?>',
    		data : data,
    		contentType: false,
            processData: false,
    		dataType : 'json',
    		success : function(data){
    			
    			if(data.success){
    				$('#tab1').click();
    				refreshData();
    				window.open('<?php echo base_url()?>dokumen/cetakan_ba/'+data.message+'.pdf');
    				
    				var nomor_surat = data.message;
    				setTimeout(function(){
                	$.ajax({
            			type : 'POST',
            			url: '<?php echo WS_JQGRID."cetak.cetak_ulang/delete_cetak"; ?>',
            			data : {nomor_surat:nomor_surat},
            			dataType : 'json',
            			success : function(data){
                        
            			}
            		});
                
                }, 9000);
    			}else{
    			
    				alert(data.message);
    			
    			}
    		}
    	});
	    
	}
		
	

}

function editDok(){
     
    var data = new FormData();
    
    $("#status_segel").removeAttr("disabled");
    $("#status_flowmeter").removeAttr("disabled");
    
	var form_data = $('#dataEdit').serializeArray();
	
	$.each(form_data, function (key, input) {
		data.append(input.name, input.value);
	});

    jQuery.each(jQuery('input[type=file]'), function(i, value) {
        data.append('lamp_edit['+i+']', value.files[0]);
    });

	data.append('key', 'value');
		
	$.ajax({
		type : 'POST',
		url: '<?php echo WS_JQGRID."monitoring.ba_pemeriksaan_sarana_pengisian/edit"; ?>',
		data : data,
		contentType: false,
        processData: false,
		dataType : 'json',
		success : function(data){
			
			if(data.success){
				$('#tab1').click();
				refreshData();
				window.open('<?php echo base_url()?>dokumen/cetakan_ba/'+data.message+'.pdf');
				
				var nomor_surat = data.message;
				setTimeout(function(){
                	$.ajax({
            			type : 'POST',
            			url: '<?php echo WS_JQGRID."cetak.cetak_ulang/delete_cetak"; ?>',
            			data : {nomor_surat:nomor_surat},
            			dataType : 'json',
            			success : function(data){
                        
            			}
            		});
                
                }, 9000);
			}else{
			
				alert(data.message);
			
			}
		}
	});

}


function simpanUp(){
	
	
	var data = new FormData();

//Form data
	var form_data = $('#dataUpld').serializeArray();
	var trans_id = $('#trans_id').val();
	// alert(nomor);
//Custom data
	var file_data = $('input[name="my_images"]')[0].files;
	for (var i = 0; i < file_data.length; i++) {
		data.append("my_images[]", file_data[i]);
	}
	
	data.append("trans_id", trans_id);
	
	$.ajax({
		type : 'POST',
		url: '<?php echo WS_JQGRID."administration.ba_upload/upload"; ?>',
		data : data,
		contentType: false,
        processData: false,
		dataType : 'json',
		success : function(data){
			
			// if(data.success){
				alert(data.message);
				$('#tab1').click();
				refreshData();
				// window.open('<?php echo base_url()?>dokumen/'+data.message+'.pdf');
			// }else{
			
			
			// }
		}
	});

}

function validate(evt) {
  var theEvent = evt || window.event;

  if (theEvent.type === 'paste') {
      key = event.clipboardData.getData('text/plain');
  } else {
      var key = theEvent.keyCode || theEvent.which;
      key = String.fromCharCode(key);
  }
  var regex = /[0-9]|\./;
  if( !regex.test(key) ) {
    theEvent.returnValue = false;
    if(theEvent.preventDefault) theEvent.preventDefault();
  }
}

function cetak(trans_id){
   
   $.ajax({
		type : 'POST',
		url: '<?php echo WS_JQGRID."cetak.cetak_ulang/create"; ?>',
		data : {trans_id:trans_id},
		dataType : 'json',
		success : function(data){
			
		    if(data.success){
				window.open('<?php echo base_url()?>dokumen/cetakan_ba/'+data.message+'.pdf');
				
				var nomor_surat = data.message;
				
				
				setTimeout(function(){
                	$.ajax({
            			type : 'POST',
            			url: '<?php echo WS_JQGRID."cetak.cetak_ulang/delete_cetak"; ?>',
            			data : {nomor_surat:nomor_surat},
            			dataType : 'json',
            			success : function(data){
                        
            			}
            		});
                
                }, 9000);
			}
		}
	});
    
}

function hapus(id){
	
	var datForm = {
		'trans_id' : id
	};
	
	var del = confirm("Apakah Kamu Yakin Untuk Hapus Data ini?");
	if (del){
		
	   $.ajax({
		type : 'POST',
		url: '<?php echo WS_JQGRID."monitoring.ba_sisa_sblm_pengisian/destroy"; ?>',
		data : datForm,
		dataType : 'json',
		success : function(data){
				alert(data.message);
				refreshData();
			}
		});
		
	}
}

function rel(id){
	
	var datForm = {
		'trans_id' : id
	};
	
	var del = confirm("Apakah Kamu Yakin Untuk Release Data ini?");
	if (del){
		
	   $.ajax({
		type : 'POST',
		url: '<?php echo WS_JQGRID."monitoring.ba_sisa_sblm_pengisian/realese"; ?>',
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