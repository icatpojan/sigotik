<?php
/**
 * bphtb_registration
 * class model for table bds_bphtb_registration 
 *
 * @since 23-10-2012 12:07:20
 * @author hilman farid
 */
class Inbox_message extends Abstract_model{
    /* Table name */
    public $table = 't_message_inbox';
    /* Alias for table */
    public $alias = '';
    /* List of table fields */
    public $fields = array();
                           
                           
    /* Display fields */
    public $displayFields = array();
    /* Details table */
    public $details = array();
    /* Primary key */
    public $pkey = '';
    /* References */    
    public $refs = array();
    
    /* select from clause for getAll and countAll */
    public $selectClause = "inbox.*,to_char(inbox.creation_date, 'yyyy-mm-dd') AS creation_date,to_char(inbox.creation_date, 'HH24:MI:SS PM') as creation_time, 
                                to_char(inbox.update_date, 'yyyy-mm-dd') AS update_date,mtype.message_type";

    public $fromClause = " t_message_inbox inbox
                         left join sikp.p_message_type mtype on mtype.p_message_type_id = inbox.p_message_type_id";

    function __construct($t_cust_account_id = ''){
        if (!empty($t_cust_account_id)){
            $this->fromClause = sprintf($this->fromClause, "and a.t_cust_account_id = ".$t_cust_account_id);
        }else{
            $this->fromClause = sprintf($this->fromClause, 'and a.t_cust_account_id = -999');
        }

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