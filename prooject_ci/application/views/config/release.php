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
</div>

<div class="row">
	
	<ul id="tabs">

      <li><a id="tab1" onClick="getTab1()">Data</a></li>

	</ul>
	 
  <div class="container" id="tab1C">
	<table id="data_tables" class="table3 table3-striped table3-bordered table3-hover" >
		<thead>
			<tr>
				<th width="4%">No</th>
				<th>Nomor Surat</th>
				<th>Tanggal Surat</th>
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
		'sAjaxSource'    : '<?php echo base_url()?>index.php/config/release/getDataTable',
		
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
	
	
});




function refreshData(){
	var x = $('#data_tables').dataTable();
	x.fnDestroy();
	$('#data_tables').dataTable({
			'bServerSide'    : true,
			'bAutoWidth'     : false,
			'scrollX'		 : true,
			'sPaginationType': 'full_numbers',
			'sAjaxSource'    : '<?php echo base_url()?>index.php/config/release/getDataTable',
			
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