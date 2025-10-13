<!-- breadcrumb -->
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="<?php base_url();?>">Home</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>Notifikasi</span>
        </li>
    </ul>
</div>
<!-- end breadcrumb -->
<div class="row">
    <ul id="tabs">

      <li><a id="tab1" onClick="getTab1()">Notifikasi Masuk</a></li>
      <li><a id="tab2">Isi Notifikasi</a></li>

	</ul>
	<div class="container2" id="tab1C">
		<table id="data_tables" class="table5 table5-striped table5-bordered table5-hover" >
			<thead>
				<tr>
					<th>No</th>
					<th>Kepada</th>
					<th>Subject</th>
					<th>Pesan</th>
					<th>Tanggal</th>
					<th>Aksi</th>
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
	<div class="container2" id="tab2C">
		<form id="dataSeri">
			<div id="isiData"></div>
		</form>	
	</div>
</div>

<script>
$(document).ready(function() {
	$('#tabs li a:not(:first)').addClass('inactive');
	$('.container2').hide();
	$('.container2:first').show();
		
	$('#tabs li a').click(function(){
		var t = $(this).attr('id');
		if($(this).hasClass('inactive')){
			$('#tabs li a').addClass('inactive');           
			$(this).removeClass('inactive');
			
			$('.container2').hide();
			$('#'+ t + 'C').fadeIn('slow');
		}
	});
	
	$('#data_tables').dataTable({
		'bServerSide'    : true,
		'bAutoWidth'     : false,
		'scrollX'		 : true,
		'sPaginationType': 'full_numbers',
		'sAjaxSource'    : '<?php echo base_url()?>index.php/notif/notif/getDataTable',
		
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
});

function refreshData(){
	var x = $('#data_tables').dataTable();
	x.fnDestroy();
	$('#data_tables').dataTable({
			'bServerSide'    : true,
			'bAutoWidth'     : false,
			'scrollX'		 : true,
			'sPaginationType': 'full_numbers',
			'sAjaxSource'    : '<?php echo base_url()?>index.php/notif/notif/getDataTable',
			
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
	refreshData();
	$('#tab2').hide();
}

function lihat(id){
	$('#tab2').show();
	$('#isiData').load('<?php echo base_url()?>index.php/notif/notif/shownotif/'+id);
	$('#tab2').click();
}

</script>