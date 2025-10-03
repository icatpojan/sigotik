<!-- breadcrumb -->
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="<?php base_url();?>">Home</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>Profil</span>
        </li>
    </ul>
</div>
<!-- end breadcrumb -->
<div class="space-4"></div>
<div class="row">
    <div class="col-md-12">
        <div class="portlet box green">
            <div class="portlet-title">
                <div class="caption">Form Profile</div>
            </div>

            <div class="portlet-body form">
                <!-- BEGIN FORM-->
                <form method="post" action="" class="form-horizontal" id="form-profile">
                    <input type="hidden" name="userid" value="<?php echo $this->session->userdata('userid');?>">
                    <div class="form-body">

                        <div class="form-group">
                            <label class="col-md-3 control-label" for="user_name">Username</label>
                            <div class="col-md-4">
                                <input type="text" name="user_name" readonly="" class="form-control" value="<?php  echo $this->session->userdata('username'); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label" for="user_email">Email</label>
                            <div class="col-md-4">
                                <input type="text" name="user_email" readonly="" class="form-control" value="<?php  echo $this->session->userdata('user_email'); ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="password">Password Lama</label>
                            <div class="col-md-4">
                                <input type="password" class="form-control" name="passwordlama" value="" id="passwordlama">
                            </div>

                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="password">Password Baru</label>
                            <div class="col-md-4">
                                <input type="password" class="form-control" name="password" value="" id="pwd_val">
                                <i class="orange">Min.4 Characters</i>
                            </div>

                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label" for="password_confirmation">Ulang Password Baru</label>
                            <div class="col-md-4">
                                <input type="password" class="form-control" name="password_confirmation" id="conf_val" value="">
								<i class="orange" id="notif_val"></i>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <input type="submit" name="submit" value="Simpan" class="btn btn-success">
                            </div>
                        </div>
                    </div>
                </form>
                <!-- END FORM-->
            </div>
        </div>
    </div>
</div>

<script>
$('#conf_val').keyup(function(){
	 if ($('#conf_val').val() != $('#pwd_val').val()){
		 $('#notif_val').html("Password tidak sesuai");
		$('#notif_val').css('color', 'red');
	}
	else{
		$('#notif_val').html("Password sudah sesuai");
		$('#notif_val').css('color', 'black');
	}
});

$("#form-profile").on('submit', (function (e) {

    e.preventDefault();
    
    if ($('#conf_val').val() != $('#pwd_val').val()){
		alert("Pasword Baru tidak Sesuai");
	}
	else{
	    
    	 var data = $(this).serializeArray();
        $.ajax({
            url: "<?php echo WS_JQGRID."administration.users_controller/updateProfile"; ?>",
            type: "POST",
            data: data,
            dataType: "json",
            success: function (data) {
                alert(data.message);
                
                if(data.success){
                    $('#pwd_val').val("");
                    $('#conf_val').val("");
                    $('#passwordlama').val("");
                }
            },
            error: function (xhr, status, error) {
               // swal({title: "Error!", text: xhr.responseText, html: true, type: "error"});
            }
        });

	}
	
    
   // return false;
}));

</script>