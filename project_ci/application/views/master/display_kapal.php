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

<div class="row">
	<div class="col-md-12">
		<?php
	        if($this->session->userdata('conf_group_id') != 3){ // Untuk ROLE KAPAL
	            	echo '<button type="button" class="btn btn-info mb-xl" onClick="getTab2(0)">TAMBAH<i class="fa fa-plus"></i></button>';
	        }
		
		?>
	</div>
</div>

<div class="row">
	
	<ul id="tabs">

     <li><a id="tab1" onClick="getTab1()">Data Kapal</a></li>
      <li><a id="tab2">Form Kapal</a></li>
      <li><a id="tab3">Form Kapal</a></li>

	</ul>
	
  <div class="container" id="tab1C">
	<table id="data_table" class="table4 table4-striped table4-bordered table4-hover">
			<thead>
				<tr>
					<th width="4%">No</th>
					<th >Nama Kapal</th>
					<th >Code</th>
					<th >Nama Nakhoda</th>
					<th >UPT</th>
					<th >Galangan Pembuatan</th>
					<th >Tahun Buat</th>
					<th width="18%">Aksi</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="9" align="center">
						<img src="<?php echo base_url();?>assets/img/loadingAnimation.gif">
					</td>
				</tr>
			</tbody>
		</table>
		<div id="viewDetail"></div>
  </div>
  
  
  
 <div class="container" id="tab2C">
	<form id="dataSeri" enctype="multipart/form-data">
		<div id="isiData"></div>
			<br>
			<button type="button" class="btn btn-success mb-xl" onClick="simpan()">SIMPAN</button>
			<button type="button" class="btn btn-danger mb-xl" onClick="reset()">RESET</button> 
	</form>
	<br>
  </div>
  
  <div class="container" id="tab3C">
	<form id="dataSeriEdit" enctype="multipart/form-data">
		<div id="isiDataEdit"></div>
			<button type="button" class="btn btn-success mb-xl" onClick="edit()">SIMPAN</button>
			<button type="button" class="btn btn-danger mb-xl" onClick="reset()">RESET</button> 
	</form>
	<br>
  </div>
         

</div>

<div id="fsModal" class="modal animated bounceIn" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
		  <!-- header -->
			<div class="modal-header">
				<h1 id="myModalLabel" class="modal-title">
					DETAIL KAPAL
				</h1>
			</div>
		  <!-- header -->
		  <!-- body -->
			<div class="modal-body">
					<div id="detkapal"></div>
			</div>
			
			<div class="modal-footer">
				<button class="btn btn-danger mb-xl" data-dismiss="modal">
				  Tutup
				</button>
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
	
	
	$('#data_table').dataTable({
			'bServerSide'    : true,
			'bAutoWidth'     : false,
			'scrollX'		 : true,
			'sPaginationType': 'full_numbers',
			'sAjaxSource'    : '<?php echo base_url()?>index.php/master/master_kapal/getDataTable',
			
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

					
		function getFilter(){
			var dt = $('#data_table').dataTable();
			dt.fnDestroy();
			$('#data_table').dataTable({
				'bServerSide'    : true,
				'bAutoWidth'     : false,
				'scrollX'		 : true,
				'sPaginationType': 'full_numbers',
				'sAjaxSource'    : '<?php echo base_url()?>index.php/master/master_kapal/getDataTable',
				
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
				
		function refreshData(){
			var x = $('#data_table').dataTable();
			x.fnDestroy();
			$('#data_table').dataTable({
					'bServerSide'    : true,
					'bAutoWidth'     : false,
					'scrollX'		 : true,
					'sPaginationType': 'full_numbers',
					'sAjaxSource'    : '<?php echo base_url()?>index.php/master/master_kapal/getDataTable',
					
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
	
	$('#tab2').hide();
	$('#tab3').hide();
	
	
	 // $("#myBtn").click(function(){
		// $("#myModal").modal();
	  // });
	
});



function refreshData(){
		var x = $('#data_table').dataTable();
		x.fnDestroy();
		$('#data_table').dataTable({
				'bServerSide'    : true,
				'bAutoWidth'     : false,
				'scrollX'		 : true,
				'sPaginationType': 'full_numbers',
				'sAjaxSource'    : '<?php echo base_url()?>index.php/master/master_kapal/getDataTable',
				
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

function getTab1(){

	$('#tab2').hide();
	$('#tab3').hide();
	
}

function getTab2(){
	
	$('#viewDetail').hide();
	
	$('#tab2').show();
	$('#tab3').hide();
	$('#dt_tab2').hide();
	$('#isiData').load('<?php echo base_url()?>index.php/master/master_kapal/getDataForm');
	$('#tab2').click();

}

function detail(id){

	// $('#viewDetail').load('<?php echo base_url()?>index.php/master/master_kapal/getDataFormEdit/'+id);
	// document.getElementById("viewDetail").focus();
	$("#fsModal").modal();
	$('#detkapal').load('<?php echo base_url()?>index.php/master/master_kapal/getDataFormDett/'+id);
	
}

function simpan(){
	
	// $("#form1").valid(); 
	var data = new FormData();

	//Form data
	var form_data = $('#dataSeri').serializeArray();
	$.each(form_data, function (key, input) {
		data.append(input.name, input.value);
	});

	//File data
	//var file_data = $('input[name="my_images"]')[0].files;
	//for (var i = 0; i < file_data.length; i++) {
//		data.append("my_images[]", file_data[i]);
//	}
	
//	var file_data2 = $('input[name="lampiran_kapal"]')[0].files;
//	for (var k = 0; k < file_data2.length; k++) {
//		data.append("lampiran_kapal[]", file_data2[i]);
//	}

    jQuery.each(jQuery('input[type=file]'), function(i, value) {
        data.append('lamp['+i+']', value.files[0]);
    });

	//Custom data
	data.append('key', 'value');
	
	$.ajax({
		type : 'POST',
		url: '<?php echo WS_JQGRID."master.master_kapal/create"; ?>',
		data : data,
		contentType: false,
        processData: false,
		dataType : 'json',
		success : function(data){
    		alert(data.message);
    		if(data.success){
    		    $('#tab1').click();
    	    	refreshData();
    		}
    	}
	});
}

function getTab3(id){
	$('#viewDetail').hide();
	
	$('#tab3').show();
	$('#isiDataEdit').load('<?php echo base_url()?>index.php/master/master_kapal/getDataFormEdit/'+id);
	$('#tab3').click();
	
	
}

function edit(){

	var data = new FormData();

	//Form data
	var form_data = $('#dataSeriEdit').serializeArray();
	$.each(form_data, function (key, input) {
		data.append(input.name, input.value);
	});

	//File data
    jQuery.each(jQuery('input[type=file]'), function(i, value) {
        data.append('lamp['+i+']', value.files[0]);
    });
	
	//Custom data
	data.append('key', 'value');
	
	$.ajax({
		type : 'POST',
		url: '<?php echo WS_JQGRID."master.master_kapal/update"; ?>',
		data : data,
		contentType: false,
        processData: false,
		dataType : 'json',
		success : function(data){
			alert(data.message);
			if(data.success){
    		    $('#tab1').click();
    	    	refreshData();
    		}
			
		}
	});

}

function hapus(id){
	
	var datForm = {
		'm_kapal_id' : id
	};
	
	var del = confirm("Apakah Kamu Yakin Untuk Hapus Data ini?");
	if (del){
		
	   $.ajax({
		type : 'POST',
		url: '<?php echo WS_JQGRID."master.master_kapal/destroy"; ?>',
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