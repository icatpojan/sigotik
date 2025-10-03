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

		<li><a id="tab1" onClick="getTab1()">Data Anggaran Internal</a></li>
		<li><a id="tab2">Form Tambah Anggaran Internal</a></li>
		<li><a id="tab3">Form Edit Anggaran Internal</a></li>

	</ul>
	<div class="container" id="tab1C">
		<table id="data_tables" class="table3 table3-striped table3-bordered table3-hover" >
			<thead>
				<tr>
					<th>No</th>
					<th>Nama UPT</th>
					<th>Tgl Transaksi</th>
					<th>Nominal Perubahan</th>
					<th>Nomor Surat</th>
					<th>Keterangan</th>
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
		'sAjaxSource'    : '<?php echo base_url()?>index.php/angta/anggaran/getDataTableAngaranInternal',
		
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
		'sAjaxSource'    : '<?php echo base_url()?>index.php/angta/anggaran/getDataTableAngaranInternal',
		
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

function resetfield(){
	$("input[type='text']").val('');
	$("input[type='date']").val('');
}

function getTab1(){
	refreshData();
	$('#tab2').hide();
	$('#tab3').hide();
}

function getTab2(id){
	$('#tab2').show();
	$('#isiData2').load('<?php echo base_url()?>index.php/angta/anggaran/frmdataAnggaranInternal/'+id);
	$('#tab2').click();
}

function getTab3(id){
	$('#tab3').show();
	$('#isiData3').load('<?php echo base_url()?>index.php/angta/anggaran/frmdataAnggaranInternal/'+id);
	$('#tab3').click();
}



function simpan(){
	
	var x = $('#dataSeri2').serializeArray();
	
	$.ajax({
		type : 'POST',
		url: '<?php echo WS_JQGRID."angta.anggaran/createInternal"; ?>',
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
	
	var x = $('#dataSeri3').serializeArray();
	
	$.ajax({
		type : 'POST',
		url: '<?php echo WS_JQGRID."angta.anggaran/updateInternal"; ?>',
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
		'id' : id
	};
	
	var del = confirm("Apakah Kamu Yakin Untuk Hapus Data ini?");
	if (del){
		
	   $.ajax({
		type : 'POST',
		url: '<?php echo WS_JQGRID."angta.anggaran/destroyInternal"; ?>',
		data : datForm,
		dataType : 'json',
		success : function(data){
				alert(data.message);
				refreshData();
			}
		});
		
	}
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
  
  $(".angka").keyup(function(event){
      // skip for arrow keys
      if(event.which >= 37 && event.which <= 40){
          event.preventDefault();
      }
      var $this = $(this);
      var num = $this.val().replace(/,/gi, "").split("").reverse().join("");
      
      var num2 = RemoveRougeChar(num.replace(/(.{3})/g,"$1,").split("").reverse().join(""));
      
      console.log(num2);
      
      
      // the following line has been simplified. Revision history contains original.
      $this.val(num2);
  });
  
}

function getValZ(){
	
	
	var a = 0;
	document.getElementById('sisa_pagu').value = a;
	
	document.getElementById('sisa_pagu').value = ReplaceNumberWithCommas(parseFloat(document.getElementById('anggaran').value.replace(/,/g ,''))+parseFloat(document.getElementById('nominal_awal').value.replace(/,/g ,'')) + parseFloat(document.getElementById('nominal_rubah').value.replace(/,/g ,'')));
	
}

function ReplaceNumberWithCommas(yourNumber) {

    var n= yourNumber.toString().split(".");
    n[0] = n[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    return n.join(".");
}

function RemoveRougeChar(convertString){
    
    
    if(convertString.substring(0,1) == ","){
        
        return convertString.substring(1, convertString.length)            
        
    }
    return convertString;
    
}

</script>