<?php
/**
 * bphtb_registration
 * class model for table Users
 *
 * @since 23-10-2012 12:07:20
 * @author Wiliam Decosta
 */
class Users extends Abstract_model{
    /* Table name */
    public $table = 'p_app_user';
    /* Alias for table */
    public $alias = '';
    /* List of table fields */
    public $fields          = array(
                                'p_app_user_id'         => array('pkey' => true, 'type' => 'int', 'nullable' => true, 'unique' => true, 'display' => 'ID User'),
                                'app_user_name'         => array('nullable' => false, 'type' => 'str', 'unique' => true, 'display' => 'User name'),
                                'user_pwd'              => array('nullable' => true, 'type' => 'str', 'unique' => false, 'display' => 'Password'),
                                'full_name'             => array('nullable' => false, 'type' => 'str', 'unique' => false, 'display' => 'Full Name'),
                                'email_address'         => array('nullable' => false, 'type' => 'str', 'unique' => false, 'display' => 'Email'),
                            );


    /* Primary key */
    public $pkey = 'p_app_user_id';
    /* References */
    public $refs = array();

    /* select from clause for getAll and countAll */
    public $selectClause = "*";

    public $fromClause = "p_app_user";

    function __construct(){
        parent::__construct();
   	}

    /**
     * validate
     * input record validator
     */
    public function validate(){

        if ($this->actionType == 'CREATE'){
            // TODO : Write your validation for CREATE here

        }else if ($this->actionType == 'UPDATE'){
            // TODO : Write your validation for UPDATE here
        }

        return true;
    }
}
?>