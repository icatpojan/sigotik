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
      <li><a id="tab2">Form <?php echo $menu1?></a></li>
      <li><a id="tab3">Form Upload</a></li>
       <li><a id="tab4">Form Edit <?php echo $menu1?></a></li>
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
		
			<div class="row">
			
				 <div class="col-md-7">
					<div class="form-group">
						<label>Kapal</label>
						<select class="form-control custom-control" id="m_kapal_id" name="m_kapal_id" onChange="getVal()">
							<?php
								$ci = & get_instance();
								
								$sql = "SELECT * FROM m_kapal WHERE m_kapal_id IN (SELECT m_kapal_id FROM sys_user_kapal WHERE conf_user_id = '".$this->session->userdata('userid')."' ) "; 
								$query = $ci->db->query($sql);
								foreach ($query->result() as $list){
									echo '<option value="'.$list->m_kapal_id.'">'.$list->nama_kapal.'</option>';
								}
							?>
						</select>
					</div>
				</div>
				
				<div class="col-md-7 ">
					<div class="form-group">
						<label>KODE KAPAL</label>
						<input type="text" name="code_kapal" id="code_kapal" readonly="" class="form-control" value="">
					</div>
				</div>
				
				<div class="col-md-6 ">
					<div class="form-group">
						<label>LOKASI UPT</label>
						<textarea name="alamat1" id="alamat1" readonly="" rows="3" cols="50" class="form-control"/>
					</div>
				</div>
				
				<div class="col-md-4 ">
					<div class="form-group">
						<label>LOKASI KAPAL</label>
						<textarea id="lokasi_surat" name="lokasi_surat" rows="3" cols="50" class="form-control"/>
					</div>
				</div>
				
				<div class="col-md-7 ">
					<div class="form-group">
						<label>NOMOR BA</label>
						<input type="text" name="nomor_surat" id="nomor_surat" class="form-control" value="">
					</div>
				</div>	
				
			</div>	
			
			<div class="row">
				  <div class="col-md-3 ">
					<label>TANGGAL</label>
					<input type="text" name="tanggal_surat" id="tanggal_surat" class="form-control datepicker" value="">
				 </div>
				 
				 <div class="col-md-3 ">
					<label>JAM </label>
					<input type="time" name="jam_surat" id="jam_surat" class="form-control" >
				 </div>
				 
				<div class="col-md-3"> 
					<label>Zona Waktu Surat</label>
					<select class="form-control custom-control" id="zona_waktu_surat" name="zona_waktu_surat">
						<option value="WIB">WIB</option>
						<option value="WITA">WITA</option>
						<option value="WIT">WIT</option>
					</select>
				 </div>
			</div>
			
			<div class="row">
				<br>
				
				<div class="col-md-7 ">
					<div class="form-group">
						<label>SISA BBM SESUDAH PELAYARAN</label>
						<input type="text" name="volume_sisa" name="volume_sisa" class="form-control angka" value="" onkeypress="validate(event)">
					</div>
				</div>
				
				
			</div>
			
			<div class="row">
			    
			    <div class="col-md-8">
					<label>PEJABAT/STAF UPT &nbsp;&nbsp;| &nbsp;&nbsp;<input type="checkbox" class="custom-control-input" id="an_staf" name="an_staf" value="1" > An. </label>
					<input type="text" placeholder="" id="jabatan_staf_pangkalan" name="jabatan_staf_pangkalan" class="form-control" value="">
				 </div>
			</div>
			<div class="row">    
				<div class="col-md-5 ">
					<label>NAMA PEJABAT/STAF UPT </label>
					<input type="text" placeholder="" id="nama_petugas" name="nama_petugas" class="form-control" value="">
				 </div>
				 
				 <div class="col-md-4 ">
					<label>NIP </label>
						<input type="text" placeholder="" id="nip_petugas" name="nip_petugas" class="form-control" value="">
				</div>
			</div>
			<div class="row">	 
				 <div class="col-md-5 ">
					<label>NAMA NAKHODA &nbsp;&nbsp;| &nbsp;&nbsp;<input type="checkbox" class="custom-control-input" id="an_nakhoda" name="an_nakhoda" value="1" > An.  </label>
					<input type="text" placeholder="" id="nama_nakoda" name="nama_nakoda" class="form-control" value="">
				 </div>
				 
				 <div class="col-md-4 ">
					<label>NIP </label>
						<input type="text" placeholder="" id="nip_nakoda" name="nip_nakoda" class="form-control" value="">
				 </div>
			 </div>
			<div class="row">	 	 
				 <div class="col-md-5 ">
					<label>NAMA KKM &nbsp;&nbsp;| &nbsp;&nbsp;<input type="checkbox" class="custom-control-input" id="an_kkm" name="an_kkm" value="1" > An. </label>
					<input type="text" placeholder="" id="nama_kkm" name="nama_kkm" class="form-control" value="">
				 </div>
				 
				 <div class="col-md-4 ">
					<label>NIP </label>
						<input type="text" placeholder="" id="nip_kkm" name="nip_kkm" class="form-control" value="">
				 </div>
			
			</div>
			
			<div class="row">
				<br>
				<div class="col-md-4">
					<label></label>
						<button type="button" class="btn btn-warning" onClick="createDok()">Buat Dokumen</button>
				 </div>
			</div>			
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
	
	getVal();
	
	$('#data_tables').dataTable({
		'bServerSide'    : true,
		'bAutoWidth'     : false,
		'scrollX'		 : true,
		'sPaginationType': 'full_numbers',
		'sAjaxSource'    : '<?php echo base_url()?>index.php/monitoring/ba_ssdah_pelayaran/getDataTable',
		
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
	$('#tab2').click();
	$('#tab3').hide();
	$('#tab4').hide();
	$('#tab5').hide();
	
}

function getTab3(trans_id){
	// alert(nomor_surat);
	$('#tab3').show();
	$('#dataUlang').load('<?php echo base_url()?>index.php/monitoring/ba_ssdah_pelayaran/getDataUpload/'+trans_id);
	$('#tab3').click();
}

function getTab4(trans_id){
	$('#tab4').show();
	$('#dataRubah').load('<?php echo base_url()?>index.php/monitoring/ba_ssdah_pelayaran/getDataEdit/'+trans_id);
	$('#tab4').click();
}

function getTab5(trans_id){
	$('#tab5').show();
	$('#dataLihat').load('<?php echo base_url()?>index.php/monitoring/ba_ssdah_pelayaran/getDataLihat/'+trans_id);
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
			'sAjaxSource'    : '<?php echo base_url()?>index.php/monitoring/ba_ssdah_pelayaran/getDataTable',
			
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

	var x = $('#dataSeri').serializeArray();
	
	var nomor_surat = $('#nomor_surat').val();
	
	if(nomor_surat == ''){
	    
	    alert("Nomor Surat Tidak Boleh Kosong");
	    
	}else{
	
	    $.ajax({
    		type : 'POST',
    		url: '<?php echo WS_JQGRID."monitoring.ba_ssdah_pelayaran/create"; ?>',
    		data : x,
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

	var x = $('#dataEdit').serializeArray();
		
	$.ajax({
		type : 'POST',
		url: '<?php echo WS_JQGRID."monitoring.ba_ssdah_pelayaran/edit"; ?>',
		data : x,
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

function RemoveRougeChar(convertString){
    
    
    if(convertString.substring(0,1) == ","){
        
        return convertString.substring(1, convertString.length)            
        
    }
    return convertString;
    
}

$(function() {
       $('.datepicker').datepicker({
            format: 'dd-mm-yyyy'
        });
    });
</script>