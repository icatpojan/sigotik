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
		<button type="button" class="btn btn-info mb-xl" onClick="getTab2(0)">TAMBAH<i class="fa fa-plus"></i></button> 
	</div>
</div>

<div class="row">
	
	<ul id="tabs">

      <li><a id="tab1" onClick="getTab1()">Data Portal Berita</a></li>
      <li><a id="tab2">Form Portal Berita</a></li>
      <li><a id="tab3">Upload Image</a></li>

	</ul>
	
  <div class="container" id="tab1C">
	<table id="data_table" class="table4 table4-striped table4-bordered table4-hover">
			<thead>
				<tr>
					<th width="4%">No</th>
					<th>Img</th>
					<th>Judul</th>
					<th>Berita</th>
					<th>Penerbit</th>
					<th>Tanggal Terbit</th>
					<th>Status</th>
					<th>Aksi</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="7" align="center">
						<img src="<?php echo base_url();?>assets/img/loadingAnimation.gif">
					</td>
				</tr>
			</tbody>
		</table>
  </div>
  
  
  
	<div class="container" id="tab2C">
		<form id="dataSeri" enctype="multipart/form-data">
			<div id="isiData"></div>
				<button type="button" class="btn btn-success mb-xl" onClick="simpan()">SIMPAN</button>
				<button type="button" class="btn btn-danger mb-xl" onClick="reset()">RESET</button> 
		</form>
		<br>
	</div>
  
	<div class="container" id="tab3C">
		<form id="dataSeri3">
			<div id="isiData3"></div>
				<button type="button" class="btn btn-success mb-xl" onClick="simpan3()">SIMPAN</button>
				<button type="button" class="btn btn-danger mb-xl" onClick="reset()">RESET</button> 
		</form>
		<br>
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
	
	
	$('#data_table').dataTable({
			'bServerSide'    : true,
			'bAutoWidth'     : false,
			'scrollX'		 : true,
			'sPaginationType': 'full_numbers',
			'sAjaxSource'    : '<?php echo base_url()?>index.php/portal/portal/getDataTable',
			
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
				'sAjaxSource'    : '<?php echo base_url()?>index.php/portal/portal/getDataTable',
				
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
	tinymce.remove('#news');
	$('#tab3').hide();
	
});

function refreshData(){
	var x = $('#data_table').dataTable();
	x.fnDestroy();
	$('#data_table').dataTable({
			'bServerSide'    : true,
			'bAutoWidth'     : false,
			'scrollX'		 : true,
			'sPaginationType': 'full_numbers',
			'sAjaxSource'    : '<?php echo base_url()?>index.php/portal/portal/getDataTable',
			
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
	tinymce.remove('#news');
	$('#tab3').hide();
}

function getTab2(id){
	$('#tab2').show();
	$('#isiData').load('<?php echo base_url()?>index.php/portal/portal/getDataForm/'+id);
	$('#tab2').click();
}

function getTab3(id){
	$('#tab3').show();
	$('#isiData3').load('<?php echo base_url()?>index.php/portal/portal/getDataUpload/'+id);
	$('#tab3').click();
}

function simpan(){
	var content = tinymce.get("news").getContent();
	$('#news').val(content);
	var x = $('#dataSeri').serializeArray();
		
	$.ajax({
		type : 'POST',
		url: '<?php echo WS_JQGRID."portal.portal/saveup"; ?>',
		data : x,
		dataType : 'json',
		success : function(data){
			alert(data.message);
			$('#tab1').click();
			refreshData();
			
		}
	});
}

function simpan3(){
	var data = new FormData();
	var form_data = $('#dataSeri3').serializeArray();
	var id = $('#id').val();
	var file_data = $('input[name="images"]')[0].files;
	for (var i = 0; i < file_data.length; i++) {
		data.append("images[]", file_data[i]);
	}
	
	data.append("id", id);
	
	$.ajax({
		type : 'POST',
		url: '<?php echo WS_JQGRID."portal.portal/upload"; ?>',
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
		url: '<?php echo WS_JQGRID."portal.portal/destroy"; ?>',
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