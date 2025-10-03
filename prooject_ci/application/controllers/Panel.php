<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Panel extends CI_Controller {

	public function index() {
		if(!$this->session->userdata('logged_in')) {
            
            redirect(base_url().'auth');
        }

        $this->load->view('panel');
	}

    function load_content($id) {
			$data = $this->getmenusub($id);
			
        try {
            $file_exist = true;
            check_login();
            $id = str_replace('.php','',$id);
            $file = explode(".", $id);
            $url_file = "";
            if(count($file) > 1) {
                if(strtolower(substr($file[1],-4)) != ".php")
                    $file[1] .= ".php";
                if(file_exists(APPPATH."views/".$file[0].'/'.$file[1])) {
					$data2 = array();
					$data2['menu1'] = $data['menu1'];
					$data2['menu2'] = $data['menu2'];
                    $this->load->view($file[0].'/'.$file[1], $data2);
                }else {
                    $file_exist = false;
                }

                $url_file = APPPATH."views/".$file[0].'/'.$file[1];
            }else {
                if(strtolower(substr($id,-4)) != ".php")
                    $id .= ".php";

                if(file_exists(APPPATH."views/".$id)) {
					$data2 = array();
					$data2['menu1'] = $data['menu1'];
					$data2['menu2'] = $data['menu2'];
                    $this->load->view($id, $data2);
                }else {
                    $file_exist = false;
                }

                $url_file = APPPATH."views/".$id;
            }

            if(!$file_exist) {
                $this->load->view("error_404_panel.php");
            }

        }catch(Exception $e) {
            echo "
                <script>
					location.reload();
                </script>
            ";
            exit;
                    // swal({
                      // title: 'Session Timeout',
                      // text: '".$e->getMessage()."',
                      // html: true,
                      // type: 'error'
                    // });
        }
    }
	
	function getmenusub($param){
		$ci = & get_instance();
		$sql = $ci->db->query("SELECT * FROM stm_menuv2 WHERE linka = '".$param."'");
		$menu2 = '';
		$menu1 = '';
		if($sql->num_rows() > 0){
			$dtmenu = $sql->row_array();
			if($dtmenu['id_parentmenu'] != NULL){
				$dtmenuutama = $ci->db->query("SELECT * FROM stm_menuv2 WHERE id = '".$dtmenu['id_parentmenu']."'")->row_array();
				$menu1 = $dtmenu['menu'];
				$menu2 = $dtmenuutama['menu'];
			}else{
				$menu2 = $dtmenu['menu'];
				$menu1 = $dtmenu['menu'];
			}
		}
		
		$data = array(
		'menu1' => $menu1,
		'menu2' => $menu2
		);
		return $data;
	}
	
	function ceknotif(){
		$ci = & get_instance();
		$userid = $this->session->userdata('userid');
		$sql = $ci->db->query("SELECT * FROM dat_notif WHERE conf_user_id = '".$userid."' AND `status` = '0'");
		$row = $sql->num_rows();
		echo '{"hasil": '.json_encode($row).'}';
	}
	
	function datanotif(){
		$ci = & get_instance();
		$userid = $this->session->userdata('userid');
		$sql = $ci->db->query("SELECT * FROM dat_notif WHERE conf_user_id = '".$userid."' AND `status` = '0'");
		$data = '';
		if($sql->num_rows() > 0){
			foreach($sql->result() AS $list){
				$data .= '<font>'.$list->subjek.'</font>';
			}
		}else{
			$data .= '<font style="color:#c6cac6;">- Tidak ada pesan masuk -</font>';
		}
		echo '{"hasil": '.json_encode($data).'}';
		// echo $data;
	}

}
