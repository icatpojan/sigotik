
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery.min.js" type="text/javascript"></script>

<!-- Buat loading -->
<script src="<?php echo base_url(); ?>assets/js/jquery.blockUI.js" type="text/javascript"></script>
<!-- loading -->

<!-- BEGIN THEME GLOBAL SCRIPTS -->
<script src="<?php echo base_url(); ?>assets/global/scripts/app.min.js" type="text/javascript"></script>
<!-- END THEME GLOBAL SCRIPTS -->
<!-- BEGIN THEME LAYOUT SCRIPTS -->
<script src="<?php echo base_url(); ?>assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
<!-- END THEME LAYOUT SCRIPTS -->

<!-- NUMERIC -->
<script src="<?php echo base_url(); ?>assets/js/autoNumeric.js" type="text/javascript"></script>

<!-- BEGIN TINY -->
<script src="<?php echo base_url(); ?>assets/tinymce/js/tinymce/tinymce.min.js" type="text/javascript"></script>
<!-- END TINY -->

<script src="<?php echo base_url();?>assets/js/bootstrap.min.js"></script>
<script src="<?php echo base_url();?>assets/js/jquery.dataTables.js"></script>

<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>


<script type="text/javascript">
    $(document).ready(function () {
        // Ajax setup csrf token.
        var csfrData = {};
        csfrData['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
        $.ajaxSetup({
            data: csfrData,
            cache: false
        });
   });

    $(document).ajaxStart(function () {
        $(document).ajaxStart($.blockUI({
            message:  'Loading...',
            css: {
                border: 'none',
                padding: '5px',
                backgroundColor: '#000',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity: .6,
                color: '#fff'
            }

        })).ajaxStop($.unblockUI);
    });

    function loadContentWithParams(id, params) {
        $.ajax({
            url: "<?php echo base_url().'panel/load_content/'; ?>" + id,
            type: "POST",
            data: params,
            success: function (data) {
                $( "#main-content" ).html( data );
            },
            error: function (xhr, status, error) {
                swal({title: "Error!", text: xhr.responseText, html: true, type: "error"});
            }
        });
        return;
    }

    $(".nav-item").on('click', function(){
        var nav = $(this).attr('data-source');

        if(!nav){

        }else{
            $(".nav-item").removeClass("active");

            $(this).addClass("active");
            $(this).parent("ul").parent("li").addClass("active");

            loadContentWithParams(nav,{});
        }

    });


    $("#my-profile").click(function(event){
        event.stopPropagation();
        $(".nav-item").removeClass("active");
        loadContentWithParams('profile.profile_form',{
			
		});
    });
	
	$("#notifpesan").click(function(event){
		// alert(0);
        event.stopPropagation();
        $(".nav-item").removeClass("active");
        loadContentWithParams('notif.display_notif',{
			
		});
    });

    jQuery.fn.center = function () {

        if(this.width() > $(window).width()) {
            this.css("width", $(window).width()-40);
        }
        this.css("top",($(window).height() - this.height() ) / 2+$(window).scrollTop() + "px");
        this.css("left",( $(window).width() - this.width() ) / 2+$(window).scrollLeft() + "px");

        return this;
    }
    
    function addCommas(nStr){
		nStr += '';
		x = nStr.split('.');
		x1 = x[0];
		x2 = x.length > 1 ? '.' + x[1] : '';
		var rgx = /(\d+)(\d{3})/;
		while (rgx.test(x1)) {
			x1 = x1.replace(rgx, '$1' + ',' + '$2');
		}
		return x1 + x2;
	}
	
	function numericFilter(txb) {
	   txb.value = txb.value.replace(/[^\0-9]/ig, "");
	}
	
	
	/* function getnotif(){
		$.ajax({
            url: "<?php echo base_url().'panel/ceknotif'; ?>",
            type: "POST",
			global : false,
			dataType : 'json',
            success: function (data) {
                var number = data.hasil;
				if(number == 0){
					$('#notif').removeClass('notification');
					$('#vali').empty();
					// getdatanotif();
				}else{
					$('#notif').addClass('notification');           
					$('#vali').empty();
					$('#vali').append(number);
					// getdatanotif();
				}
            }
        });
	}
	
	function getdatanotif(){
		$.ajax({
            url: "<?php echo base_url().'panel/datanotif'; ?>",
            type: "POST",
			global : false,
			dataType : 'json',
            success: function (data) {
					$('#notifpesan').empty();
					$('#notifpesan').append(data.hasil);
            }
        });
	} */
	
	// setInterval(getnotif, 3000);




</script>
