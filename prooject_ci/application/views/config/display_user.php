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

      <li><a id="tab1" onClick="getTab1()">Data User</a></li>
      <li><a id="tab2">Form User</a></li>
      <li><a id="tab3">Form User</a></li>
      <li><a id="tab4">Set Kapal</a></li>

	</ul>
	
  <div class="container" id="tab1C">
	<table id="data_table" class="table4 table4-striped table4-bordered table4-hover">
			<thead>
				<tr>
					<th width="4%">No</th>
					<th>Username & Nama Lengkap</th>
					<th>Role & UPT</th>
					<th>Nip dan Gol</th>
					<th>Kapal</th>
					<th width="17%">Aksi</th>
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
	<form id="dataSeri">
		<div id="isiData"></div>
			<br>
			<button type="button" class="btn btn-success mb-xl" onClick="simpan()">SIMPAN</button>
			<button type="button" class="btn btn-danger mb-xl" onClick="reset()">RESET</button> 
	</form>
	<br>
  </div>
  
  <div class="container" id="tab3C">
	<form id="dataSeriEdit">
		<div id="isiDataEdit"></div>
			<button type="button" class="btn btn-success mb-xl" onClick="edit()">SIMPAN</button>
	</form>
	<br>
  </div>
  
  <div class="container" id="tab4C">
	<form id="dataSeriBox">
		<div id="isiDataChek"></div>
		<br>
			<button type="button" class="btn btn-success mb-xl" onClick="simpanBox()">SIMPAN</button>
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
			'sAjaxSource'    : '<?php echo base_url()?>index.php/config/user/getDataTable',
			
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
				'sAjaxSource'    : '<?php echo base_url()?>index.php/config/user/getDataTable',
				
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
					'sAjaxSource'    : '<?php echo base_url()?>index.php/config/user/getDataTable',
					
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
	$('#tab4').hide();
	
});

function refreshData(){
	var x = $('#data_table').dataTable();
	x.fnDestroy();
	$('#data_table').dataTable({
			'bServerSide'    : true,
			'bAutoWidth'     : false,
			'scrollX'		 : true,
			'sPaginationType': 'full_numbers',
			'sAjaxSource'    : '<?php echo base_url()?>index.php/config/user/getDataTable',
			
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
	// $('#tab1').click();
	$('#tab2').hide();
	$('#tab3').hide();
	$('#tab4').hide();
}

function getTab2(id){
	$('#tab2').show();
	$('#isiData').load('<?php echo base_url()?>index.php/config/user/getDataForm/'+id);
	
	$('#tab2').click();
		$('#tab3').hide();
}

function getTab3(id){
	$('#tab3').show();
	$('#isiDataEdit').load('<?php echo base_url()?>index.php/config/user/getDataForm/'+id);
	
	$('#tab3').click();
}


function getTab4(id){
	$('#tab4').show();
	$('#isiDataChek').load('<?php echo base_url()?>index.php/config/user/getDataCBox/'+id);
	
	$('#tab4').click();
}

function simpan(){
	
	var x = $('#dataSeri').serializeArray();
		
	$.ajax({
		type : 'POST',
		url: '<?php echo WS_JQGRID."config.user/create"; ?>',
		data : x,
		dataType : 'json',
		success : function(data){
			alert(data.message);
			$('#tab1').click();
			refreshData();
		}
	});
}

function edit(){
	
	var x = $('#dataSeriEdit').serializeArray();
		
	$.ajax({
		type : 'POST',
		url: '<?php echo WS_JQGRID."config.user/update"; ?>',
		data : x,
		dataType : 'json',
		success : function(data){
			alert(data.message);
			$('#tab1').click();
			refreshData();
		}
	});
}

function simpanBox(){
	
	var x = $('#dataSeriBox').serializeArray();
		
	$.ajax({
		type : 'POST',
		url: '<?php echo WS_JQGRID."config.user/updateBox"; ?>',
		data : x,
		dataType : 'json',
		success : function(data){
			alert(data.message);
			$('#tab1').click();
			refreshData();
		}
	});
}

function getcBox(){
	
	var upt_code = $("#m_upt_code").val();
	
	$('#dataCBox').load('<?php echo base_url()?>index.php/config/user/getDataCBox/'+upt_code);

}

function toggle(source) {
		
	var checkboxes = document.getElementsByName("m_kapal_id[]");
	  for(var i=0, n=checkboxes.length;i<n;i++) {
		checkboxes[i].checked = source.checked;
	 }
	
}



function hapus(id){
	
	var datForm = {
		'conf_user_id' : id
	};
	
	var del = confirm("Apakah Kamu Yakin Untuk Hapus Data ini?");
	if (del){
		
	   $.ajax({
		type : 'POST',
		url: '<?php echo WS_JQGRID."config.user/destroy"; ?>',
		data : datForm,
		dataType : 'json',
		success : function(data){
				alert(data.message);
				refreshData();
			}
		});
		
	}
}

function getResetPass(id){
	
	var datForm = {
		'conf_user_id' : id
	};
	
	var del = confirm("Apakah Kamu Yakin Untuk Reset Password user ini ?");
	if (del){
		
	   $.ajax({
		type : 'POST',
		url: '<?php echo WS_JQGRID."config.user/reset"; ?>',
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