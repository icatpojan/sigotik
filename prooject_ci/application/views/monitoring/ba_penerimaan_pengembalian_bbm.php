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
			
				 <div class="col-md-6">
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
				
				<div class="col-md-4 ">
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
					<input type="text" name="tanggal_surat" id="tanggal_surat" class="form-control datepicker" onChange="getVolumeSounding()">
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
				 
				<div class="col-md-3 ">
					<div class="form-group">
						<label>LINK BA</label>
						    <input type="text" name="link_ba" id="link_ba" readonly="" class="form-control" >
					</div>
				</div>
				
			</div>
			
			<div class="row">
				
				<div class="col-md-4">
					<div class="form-group">
						<label>No BA Pengembalian Pinjaman</label>
						<select class="form-control custom-control" id="trans_idPeminjam" name="trans_idPeminjam" onChange="getValPeminjam(this)">
							<option value="0">--Pilih--</option>
							<?php
								$ci = & get_instance();
								
								$sql = "SELECT trans_id, nomor_surat FROM bbm_kapaltrans WHERE kapal_code_temp = (
											SELECT code_kapal FROM m_kapal WHERE m_kapal_id IN (
												SELECT m_kapal_id FROM sys_user_kapal WHERE conf_user_id = '".$this->session->userdata('userid')."' )
											) AND status_ba = 12 AND status_temp = 0";
										
								$query = $ci->db->query($sql);
								foreach ($query->result() as $list){
									echo '<option value="'.$list->trans_id.'">'.$list->nomor_surat.'</option>';
								}
							?>
						</select>
					</div>
				</div>
				
				<div class="col-md-3 ">
					<label>TANGGAL Pengembalian Pinjaman</label>
					<input type="text" name="tanggal_peminjaman" id="tanggal_peminjaman" class="form-control datepicker" readonly="">
				 </div>
				
				<input type="hidden" id="link_modulTemp" name="link_modulTemp" class="form-control">
				
				<div class="col-md-4 ">
					<div class="form-group">
						<label>KODE KAPAL Pengembalian Pinjaman</label>
						<input type="text" name="kapal_code_temp" id="kapal_code_temp" readonly="" class="form-control" value="">
					</div>
				</div>
				
				
			</div>
			
			<div class="row">
			    
			    <div class="col-md-5">
					<label>Jenis BBM</label>
					<input type="text" placeholder="" id="keterangan_jenis_bbm" name="keterangan_jenis_bbm" class="form-control" readonly="">
				 </div>
				
				<div class="col-md-4">
					<label>BBM Sebelum Pengisian</label>
					<input type="text" name="volume_sebelum" id="volume_sebelum" class="form-control angka" value="0" onkeypress="validate(event)" readonly="">
				 </div>
				 
				 
				 <div class="col-md-5">
					<label>Jumlah BBM Di Terima</label>
					<input type="text" name="volume_pengisian" id="volume_pengisian" class="form-control angka" readonly="">
				 </div>
				 
				 <div class="col-md-5">
					<label>Sisa BBM</label>
					<input type="text" name="volume_sisa" id="volume_sisa" class="form-control angka" value="0" onkeypress="validate(event)" readonly="">
				 </div><br><br>
			</div>
			<br>
			<div class="row">
			    
			   <div class="col-md-7 ">
					<div class="form-group">
						<label>Alasan Peminjaman BBM</label>
						<textarea id="sebab_temp" name="sebab_temp" rows="3" cols="50" class="form-control" readonly=""/>
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
			<br>
				<div class="col-md-4 ">
					<label>NAMA PEJABAT/STAF UPT </label>
					<input type="text" placeholder="" id="nama_petugas" name="nama_petugas" class="form-control" value="" >
				 </div>
				 
				 <div class="col-md-4 ">
					<label>NIP </label>
						<input type="text" placeholder="" id="nip_petugas" name="nip_petugas" class="form-control" value="" >
				</div>
			</div>
			
			<div class="row">
			<br>
				 <div class="col-md-4 ">
					<label>NAMA NAKHODA &nbsp;&nbsp;| &nbsp;&nbsp;<input type="checkbox" class="custom-control-input" id="an_nakhoda" name="an_nakhoda" value="1" > An.  </label>
					<input type="text" placeholder="" id="nama_nakoda" name="nama_nakoda" class="form-control" value="" >
				 </div>
				 
				 <div class="col-md-4 ">
					<label>Pangkat/Gol </label>
						<input type="text" placeholder="" id="pangkat_nahkoda" name="pangkat_nahkoda" class="form-control" value="" >
				 </div>
				 
				 <div class="col-md-4 ">
					<label>NIP </label>
						<input type="text" placeholder="" id="nip_nakoda" name="nip_nakoda" class="form-control" value="" >
				 </div>
			 </div>
			
			<div class="row">	
			<br>
				 <div class="col-md-4 ">
					<label>NAMA KKM &nbsp;&nbsp;| &nbsp;&nbsp;<input type="checkbox" class="custom-control-input" id="an_kkm" name="an_kkm" value="1"> An. </label>
					<input type="text" placeholder="" id="nama_kkm" name="nama_kkm" class="form-control" value="" >
				 </div>
				 
				 <div class="col-md-4 ">
					<label>NIP </label>
						<input type="text" placeholder="" id="nip_kkm" name="nip_kkm" class="form-control" value="">
				 </div>
			
			</div>
			
			<div class="row">
			<br>
				 <div class="col-md-4 ">
					<label>NAMA NAKHODA Pemberi Pinjaman &nbsp;&nbsp;| &nbsp;&nbsp;<input type="checkbox" class="custom-control-input" id="an_nakhoda_temp" name="an_nakhoda_temp" value="1"> An.  </label>
					<input type="text" placeholder="" id="nama_nahkoda_temp" name="nama_nahkoda_temp" class="form-control" value="" readonly="">
				 </div>
				 
				 <div class="col-md-4 ">
					<label>Pangkat/Gol </label>
						<input type="text" placeholder="" id="pangkat_nahkoda_temp" name="pangkat_nahkoda_temp" class="form-control" value="" readonly="">
				 </div>
				 
				 <div class="col-md-4 ">
					<label>NIP </label>
						<input type="text" placeholder="" id="nip_nahkoda_temp" name="nip_nahkoda_temp" class="form-control" value="" readonly="">
				 </div>
			 </div>
			
			<div class="row">	
			<br>
				 <div class="col-md-4 ">
					<label>NAMA KKM Pemberi Pinjaman &nbsp;&nbsp;| &nbsp;&nbsp;<input type="checkbox" class="custom-control-input" id="an_kkm_temp" name="an_kkm_temp" value="1"> An. </label>
					<input type="text" placeholder="" id="nama_kkm_temp" name="nama_kkm_temp" class="form-control" value="" readonly="">
				 </div>
				 
				 <div class="col-md-4 ">
					<label>NIP </label>
						<input type="text" placeholder="" id="nip_kkm_temp" name="nip_kkm_temp" class="form-control" value="" readonly="">
				 </div>
			
			</div>
			
			<div class="row">
				<br>
				<div class="col-md-4">
					<label></label>
						<button type="button" id="createdok_btn" class="btn btn-warning" onClick="createDok()">Buat Dokumen</button>
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
		'sAjaxSource'    : '<?php echo base_url()?>index.php/monitoring/ba_penerimaan_pengembalian_bbm/getDataTable',
		
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
	$('#dataUlang').load('<?php echo base_url()?>index.php/monitoring/ba_penerimaan_pengembalian_bbm/getDataUpload/'+trans_id);
	$('#tab3').click();
}

function getTab4(trans_id){
	$('#tab4').show();
	$('#dataRubah').load('<?php echo base_url()?>index.php/monitoring/ba_penerimaan_pengembalian_bbm/getDataEdit/'+trans_id);
	$('#tab4').click();
}

function getTab5(trans_id){
	$('#tab5').show();
	$('#dataLihat').load('<?php echo base_url()?>index.php/monitoring/ba_penerimaan_pengembalian_bbm/getDataLihat/'+trans_id);
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
			'sAjaxSource'    : '<?php echo base_url()?>index.php/monitoring/ba_penerimaan_pengembalian_bbm/getDataTable',
			
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

function getValPeminjam(trans_id){
	
	var tanggal_surat = $("#tanggal_surat").val();
	
	if(tanggal_surat == ''){
		
		alert("Tanggal Surat Di Isi Terlebih Dahulu!!")
		$("#trans_idPeminjam").val(0);
		
	}else{
		
		$.ajax({
			type : "POST",
			url	: "<?php echo base_url()?>index.php/monitoring/ba_peminjaman_bbm/getDataPinjamanBbm",
			data: {trans_id:trans_id.value},
			dataType: "json",
			success: function (data) {	
				
				$("#kapal_code_temp").val(data.kapal_code);
				$("#keterangan_jenis_bbm").val(data.keterangan_jenis_bbm);
				$("#sebab_temp").val(data.sebab_temp);
				
				$("#volume_pengisian").val(format_rupiah(data.volume_pemakaian));
				$("#tanggal_peminjaman").val(data.tanggal_surat);
				
				
				$("#nama_nahkoda_temp").val(data.nama_nakoda);
				$("#pangkat_nahkoda_temp").val(data.pangkat_nahkoda);
				$("#nip_nahkoda_temp").val(data.nip_nakoda);
				
				$("#link_modulTemp").val(data.nomor_surat);
				
				$("#nama_kkm_temp").val(data.nama_kkm);
				$("#nip_kkm_temp").val(data.nip_kkm);
				
				document.getElementById("an_nakhoda_temp").checked = data.an_nakhoda;
				document.getElementById("an_kkm_temp").checked = data.an_kkm;
				
				var volume_sebelum   = parseFloat(document.getElementById('volume_sebelum').value.replace(/,/g ,'')); 
				var volume_pengisian   = parseFloat(document.getElementById('volume_pengisian').value.replace(/,/g ,''));
				
				var volume_sisa = volume_sebelum + volume_pengisian;
				
				$("#volume_sisa").val(format_rupiah(volume_sisa));
			}
		});
	}
	
	
}

function getVolumeSounding(){
    
    var tanggal_surat = $("#tanggal_surat").val();
    
    var m_kapal_id = $("#m_kapal_id").val();
    
    var dataForm = {
        'tanggal_surat' : tanggal_surat,
        'm_kapal_id' : m_kapal_id
    }
    
    $.ajax({
		type : "POST",
		url	: "<?php echo base_url()?>index.php/monitoring/ba_peminjaman_bbm/getDataBaPinjaman",
		data: dataForm,
		dataType: "json",
		success: function (data) {
		    
		    if(tanggal_surat != ''){
		        
		        if(data.jml == 0){
			        alert(data.pesan);
			        
    			    
    			    $("#createdok_btn").attr("disabled", true);
					
    			}else{
                	
					$("#link_ba").val(data.nomor_surat);
					$("#volume_sebelum").val(data.volume_sisa);
                    	
                	$("#createdok_btn").attr("disabled", false);
    			}
		         
		    }
			
			
		}
	});
    
}

function createDok(){
	
	// alert("asdasd");die();
	var x = $('#dataSeri').serializeArray();
	
	var nomor_surat = $('#nomor_surat').val();
	var link_modul = $('#link_modul').val();
	
	if(nomor_surat == ''){
	    
	    alert("Nomor Surat Tidak Boleh Kosong Nomor Surat pemberi pinjaman harus dipilih");
	    
	}else if(link_modul == 0){
		
		alert("Nomor Surat pemberi pinjaman harus dipilih");
		
	}else{
	    
	    $.ajax({
    		type : 'POST',
    		url: '<?php echo WS_JQGRID."monitoring.ba_penerimaan_pengembalian_bbm/create"; ?>',
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
		url: '<?php echo WS_JQGRID."monitoring.ba_penerimaan_pengembalian_bbm/edit"; ?>',
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

// function validate(evt) {
  // var theEvent = evt || window.event;

  // if (theEvent.type === 'paste') {
      // key = event.clipboardData.getData('text/plain');
  // } else {
      // var key = theEvent.keyCode || theEvent.which;
      // key = String.fromCharCode(key);
  // }
  // var regex = /[0-9]|\./;
  // if( !regex.test(key) ) {
    // theEvent.returnValue = false;
    // if(theEvent.preventDefault) theEvent.preventDefault();
  // }
  
  // $(".angka").keyup(function(event){
     
      // if(event.which >= 37 && event.which <= 40){
          // event.preventDefault();
      // }
      // var $this = $(this);
      // var num = $this.val().replace(/,/gi, "").split("").reverse().join("");
      
      // var num2 = RemoveRougeChar(num.replace(/(.{3})/g,"$1,").split("").reverse().join(""));
      
      // console.log(num2);
      
      
      
      // $this.val(num2);
  // });
  
// }

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

function format_rupiah(x){
	
	var	number_string = x.toString(),
		sisa 	= number_string.length % 3,
		rupiah 	= number_string.substr(0, sisa),
		ribuan 	= number_string.substr(sisa).match(/\d{3}/g);
		
	if (ribuan) {
		separator = sisa ? ',' : '';
		rupiah += separator + ribuan.join('.');
	}
	
	return rupiah;
}

$(function() {
       $('.datepicker').datepicker({
            format: 'dd-mm-yyyy'
        });
    });


</script>