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

      <li><a id="tab1" onClick="getTab1()">Data Role</a></li>
      <li><a id="tab2">Form Group</a></li>
      <li><a id="tab3">Edit Group</a></li>
      <li><a id="tab4">Akses Menu</a></li>

	</ul>
	
  <div class="container" id="tab1C">
	<table id="data_tables" class="table4 table4-striped table4-bordered table4-hover" >
		<thead>
			<tr>
				<th width="4%">No</th>
				<th>Group</th>
				<th width="13%">Aksi</th>
				<th width="13%">Set Akses Menu</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="4" align="center">
					<img src="<?php echo base_url();?>assets/img/loadingAnimation.gif">
				</td>
			</tr>
		</tbody>
	</table>
  </div>
  
  
  
	<div class="container" id="tab2C">
		<form id="dataSeri">
			<div id="isiData"></div>
			<br>
			<button type="button" class="btn btn-success mb-xl" onClick="simpan()">SIMPAN</button>
			<button type="button" class="btn btn-danger mb-xl" onClick="reset()">RESET</button> 	
		</form>	
	</div>

	<div class="container" id="tab3C">
		<form id="dataSeriEdit">
			<div id="isiDataEdit"></div>
				<button type="button" class="btn btn-success mb-xl" onClick="simpanedit()">SIMPAN</button>
		</form>
		<br>
	</div>
	<div class="container" id="tab4C">
		<form id="dataSeri4">
			<div id="isiData4"></div>
				<button type="button" class="btn btn-success mb-xl" onClick="simpan4()">SIMPAN</button>
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
	
	
	$('#data_tables').dataTable({
			'bServerSide'    : true,
			'bAutoWidth'     : false,
			'scrollX'		 : true,
			'sPaginationType': 'full_numbers',
			'sAjaxSource'    : '<?php echo base_url()?>index.php/config/role/getDataTable',
			
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
			'sAjaxSource'    : '<?php echo base_url()?>index.php/config/role/getDataTable',
			
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
	$('#tab4').hide();
}

function getTab2(id){

	$('#tab2').show();
	$('#isiData').load('<?php echo base_url()?>index.php/config/role/getDataForm/'+id);
	$('#tab2').click();
}

function getTab3(id){

	$('#tab3').show();
	$('#isiDataEdit').load('<?php echo base_url()?>index.php/config/role/getDataForm/'+id);
	$('#tab3').click();
}

function getTab4(id){
	// alert(0);
	$('#tab4').show();
	$('#isiData4').load('<?php echo base_url()?>index.php/config/role/setMenu/'+id);
	$('#tab4').click();
}

function simpan(){
	
	var x = $('#dataSeri').serializeArray();
		
	$.ajax({
		type : 'POST',
		url: '<?php echo WS_JQGRID."config.role/create"; ?>',
		data : x,
		dataType : 'json',
		success : function(data){
			alert(data.message);
			$('#tab1').click();
			refreshData();
			
		}
	});
}

function simpanedit(){
	
	var x = $('#dataSeriEdit').serializeArray();
		
	$.ajax({
		type : 'POST',
		url: '<?php echo WS_JQGRID."config.role/update"; ?>',
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
		url: '<?php echo WS_JQGRID."config.role/insertos"; ?>',
		data : x,
		dataType : 'json',
		success : function(data){
			alert(data.message);
			location.reload();
		}
	});
}

function hapus(id){
	
	var datForm = {
		'conf_group_id' : id
	};
	
	var del = confirm("Apakah Kamu Yakin Untuk Hapus Data ini?");
	if (del){
		
	   $.ajax({
		type : 'POST',
		url: '<?php echo WS_JQGRID."config.role/destroy"; ?>',
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