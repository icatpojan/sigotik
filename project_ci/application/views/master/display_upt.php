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
		<button type="button" class="btn btn-info mb-xl" onClick="getTab2()">TAMBAH<i class="fa fa-plus"></i></button> 
	</div>
</div>
<div class="row">
	
	<ul id="tabs">

      <li><a id="tab1" onClick="getTab1()">Data UPT</a></li>
      <li><a id="tab2">Form UPT</a></li>
      <li><a id="tab3">Form UPT</a></li>

	</ul>
	
  <div class="container" id="tab1C">
	<table id="data_table" class="table4 table4-striped table4-bordered table4-hover">
			<thead>
				<tr>
					<th width="4%">No</th>
					<th >Nama UPT</th>
					<th >Kode</th>
					<th >Alamat 1</th>
					<th >Alamat 2</th>
					<th >Alamat 3</th>
					<th width="14%">Aksi</th>
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
	<form id="dataSeri">
		<div id="isiData"></div>
			<button type="button" class="btn btn-success mb-xl" onClick="simpan()">SIMPAN</button>
			<button type="button" class="btn btn-danger mb-xl" onClick="reset()">RESET</button> 
	</form>
	<br>
  </div>
  
  <div class="container" id="tab3C">
	<form id="dataSeriEdit">
		<div id="isiDataEdit"></div>
			<button type="button" class="btn btn-success mb-xl" onClick="edit()">SIMPAN</button>
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
			'sAjaxSource'    : '<?php echo base_url()?>index.php/master/upt/getDataTable',
			
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
				'sAjaxSource'    : '<?php echo base_url()?>index.php/master/upt/getDataTable',
				
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
					'sAjaxSource'    : '<?php echo base_url()?>index.php/master/upt/getDataTable',
					
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
	
});

function refreshData(){
	var x = $('#data_table').dataTable();
	x.fnDestroy();
	$('#data_table').dataTable({
			'bServerSide'    : true,
			'bAutoWidth'     : false,
			'scrollX'		 : true,
			'sPaginationType': 'full_numbers',
			'sAjaxSource'    : '<?php echo base_url()?>index.php/master/upt/getDataTable',
			
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
	$('#tab2').show();
	$('#isiData').load('<?php echo base_url()?>index.php/master/upt/getDataForm');
	$('#tab2').click();
}

function simpan(){

	var x = $('#dataSeri').serializeArray();
		
	$.ajax({
		type : 'POST',
		url: '<?php echo WS_JQGRID."master.upt/create"; ?>',
		data : x,
		dataType : 'json',
		success : function(data){
				alert(data.message);
				$('#tab1').click();
				refreshData();
				
			}
		});
}

function getTab3(id){

	$('#tab3').show();
	$('#isiDataEdit').load('<?php echo base_url()?>index.php/master/upt/getDataFormEdit/'+id);
	$('#tab3').click();
	
	
}

function edit(){

	var x = $('#dataSeriEdit').serializeArray();
		
	$.ajax({
		type : 'POST',
		url: '<?php echo WS_JQGRID."master.upt/update"; ?>',
		data : x,
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
		'm_upt_id' : id
	};
	
	var del = confirm("Apakah Kamu Yakin Untuk Hapus Data ini?");
	if (del){
		
	   $.ajax({
		type : 'POST',
		url: '<?php echo WS_JQGRID."master.upt/destroy"; ?>',
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