<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

	public function index() {

        if($this->session->userdata('logged_in')) {
            //go to default page
            redirect(base_url().'panel');
        }

        $data = array();
        $data['login_url'] = base_url()."auth/login";
        $data['error'] = '';

		$this->load->view('auth/login', $data);
	}

    public function login() {
        $username = $this->security->xss_clean($this->input->post('username'));
        $password = $this->security->xss_clean($this->input->post('password'));
		if(empty($username) or empty($password)) {
			//User Pass Kosong
            $data = array();
			$data['login_url'] = base_url()."auth/login";
			$data['error'] = 'Username / Password Kosong';

			$this->load->view('auth/login', $data);
        }else{
			// Jika Username Pass SALAH 
			// $sql = "SELECT * FROM stm_user WH";
			if(isset($username) && isset($password)){
				$userid = $username;
				$userid = str_replace(' ', '', $username);
					$cek = $this->db->query("SELECT * FROM conf_user WHERE (username = '".$username."' OR email = '".$username."') AND is_active = '1'");
					if($cek->num_rows() >0){
						$row = $cek->row();
						if($row->password == md5($password)){
							$userdata = array(
								'userid'         	=> $row->conf_user_id,
								'username'         	=> $row->username,
								'user_email'        => $row->email,
								'user_realname'     => $row->nama_lengkap,
								'logged_in'         => true,
								'conf_group_id'     => $row->conf_group_id,
								'm_upt_code'    	=> $row->m_upt_code,
								'login_time'    	=> date('Y-m-d H:i:s')
							
							 );
							
							$this->session->set_userdata($userdata);
							redirect(base_url().'panel');
						}else{
							$data = array();
							$data['login_url'] = base_url()."auth/login";
							$data['error'] = 'Password Anda Salah';

							$this->load->view('auth/login', $data);
						}
					}else{
						$data = array();
						$data['login_url'] = base_url()."auth/login";
						$data['error'] = 'Username Tidak Terdaftar';

						$this->load->view('auth/login', $data);
					}
			}
		}
    }

    public function logout() {

        
        $this->session->sess_destroy();
        redirect(base_url().'auth');
		
    }
}
