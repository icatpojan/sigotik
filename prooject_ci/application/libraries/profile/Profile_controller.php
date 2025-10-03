<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Json library
 * @class Account_controller
 * @version 07/05/2015 12:18:00
 */
class Account_controller
{

    function excelAccountList()
    {
        $sidx = getVarClean('sidx', 'str', 'a.account_num');
        $sord = getVarClean('sord', 'str', 'asc');
        $customer_ref = getVarClean('customer_ref', 'str', '');

        try {

            $ci = &get_instance();
            $ci->load->model('account/account');
            $table = $ci->account;

            $req_param = array(
                "sort_by" => $sidx,
                "sord" => $sord,
                "limit" => null,
                "field" => null,
                "where" => null,
                "where_in" => null,
                "where_not_in" => null,
                "search" => getVarClean('_search'),
                "search_field" => getVarClean('searchField'),
                "search_operator" => getVarClean('searchOper'),
                "search_str" => getVarClean('searchString')
            );

            // Filter Table
            $req_param['where'] = array(
                "b.account_status = 'OK'",
                "c.billing_contact_seq = e.contact_seq",
                "e.address_seq = f.address_seq",
                "c.end_dat is null",
                "e.end_dat is null",
                "(a.account_num like '90%' or a.account_num like '80%')"
            );

            if (!empty($customer_ref)) {
                $req_param['where'][] = "a.customer_ref = '" . $customer_ref . "'";
            }

            $table->setJQGridParam($req_param);
            $items = $table->getAll();

            startExcel(date("dmy") . '_ACCOUNT.xls');
            echo '<html>';
            echo '<head><title>Account</title></head>';
            echo '<body>';
            echo '<table border="1">';
            echo '<tr>';
            echo '<th>No</th>';
            echo '<th>Account Num</th>';
            echo '<th>Action</th>';
            echo '<th>Account Status</th>';
            echo '<th>Currency</th>';
            echo '<th>Email</th>';
            echo '<th>NPWP</th>';
            echo '<th>Address</th>';
            echo '</tr>';
            $i = 1;
            foreach ($items as $item) {
                echo '<tr>';
                echo '<td>' . $i++ . '</td>';
                echo '<td>' . $item['account_num'] . '</td>';
                echo '<td>' . $item['action'] . '</td>';
                echo '<td>' . $item['account_name'] . '</td>';
                echo '<td>' . $item['account_status'] . '</td>';
                echo '<td>' . $item['currency_code'] . '</td>';
                echo '<td>' . $item['email'] . '</td>';
                echo '<td>' . $item['npwp'] . '</td>';
                echo '<td>' . $item['address'] . '</td>';
                echo '</tr>';
            }
            echo '</table>';
            echo '</body>';
            echo '</html>';
            exit;

        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }


    }

    function readLov()
    {

        $start = getVarClean('current', 'int', 0);
        $limit = getVarClean('rowCount', 'int', 5);

        $sort = getVarClean('sort', 'str', 'a.account_num');
        $dir = getVarClean('dir', 'str', 'asc');

        $searchPhrase = getVarClean('searchPhrase', 'str', '');
        $customer_ref = getVarClean('customer_ref', 'str', '');

        $data = array('rows' => array(), 'success' => false, 'message' => '', 'current' => $start, 'rowCount' => $limit, 'total' => 0);

        try {
            permission_check('view-account');

            $ci = &get_instance();
            $ci->load->model('account/account');
            $table = $ci->account;

            //Set default criteria. You can override this if you want
            foreach ($table->fields as $key => $field) {
                if (!empty($$key)) { // <-- Perhatikan simbol $$
                    if ($field['type'] == 'str') {
                        $table->setCriteria($table->getAlias() . $key . $table->likeOperator . " '" . $$key . "' ");
                    } else {
                        $table->setCriteria($table->getAlias() . $key . " = " . $$key);
                    }
                }
            }

            if (!empty($searchPhrase)) {
                $table->setCriteria("(upper(a.account_num) " . $table->likeOperator . " upper('%" . $searchPhrase . "%') OR upper(a.account_name) " . $table->likeOperator . " upper('%" . $searchPhrase . "%'))");
            }

//            $table->setCriteria("b.account_status = 'OK'");
            $table->setCriteria("c.billing_contact_seq = e.contact_seq");
            $table->setCriteria("e.address_seq = f.address_seq");
            $table->setCriteria("c.end_dat is null");
            $table->setCriteria("e.end_dat is null");
            $table->setCriteria("(a.account_num like '90%' or a.account_num like '80%')");

            if (!empty($customer_ref)) {
                $table->setCriteria("a.customer_ref = '" . $customer_ref . "'");
            }

            $start = ($start - 1) * $limit;
            $items = $table->getAll($start, $limit, $sort, $dir);
            $totalcount = $table->countAll();

            $data['rows'] = $items;
            $data['success'] = true;
            $data['total'] = $totalcount;

        } catch (Exception $e) {
            $data['message'] = $e->getMessage();
        }

        return $data;
    }

    function crud()
    {

        $data = array();
        $oper = getVarClean('oper', 'str', '');
        switch ($oper) {
            case 'add' :
                permission_check('add-account');
                $data = $this->create();
                break;

            case 'edit' :
                permission_check('edit-account');
                $data = $this->update();
                break;

            case 'del' :
                permission_check('delete-account');
                $data = $this->destroy();
                break;

            default :
                permission_check('view-account');
                $data = $this->read();
                break;
        }

        return $data;
    }

    function create()
    {

        $ci = &get_instance();
        $ci->load->model('account/account');
        $table = $ci->account;

        $data = array('rows' => array(), 'page' => 1, 'records' => 0, 'total' => 1, 'success' => false, 'message' => '');

        $jsonItems = getVarClean('items', 'str', '');
        $items = jsonDecode($jsonItems);

        if (!is_array($items)) {
            $data['message'] = 'Invalid items parameter';
            return $data;
        }

        $table->actionType = 'CREATE';
        $errors = array();

        if (isset($items[0])) {
            $numItems = count($items);
            for ($i = 0; $i < $numItems; $i++) {
                try {

                    $table->db->trans_begin(); //Begin Trans

                    $table->setRecord($items[$i]);
                    $table->create();

                    $table->db->trans_commit(); //Commit Trans

                } catch (Exception $e) {

                    $table->db->trans_rollback(); //Rollback Trans
                    $errors[] = $e->getMessage();
                }
            }

            $numErrors = count($errors);
            if ($numErrors > 0) {
                $data['message'] = $numErrors . " from " . $numItems . " record(s) failed to be saved.<br/><br/><b>System Response:</b><br/>- " . implode("<br/>- ", $errors) . "";
            } else {
                $data['success'] = true;
                $data['message'] = 'Data added successfully';
            }
            $data['rows'] = $items;
        } else {

            try {
                $table->db->trans_begin(); //Begin Trans

                $table->setRecord($items);
                $table->create();

                $table->db->trans_commit(); //Commit Trans

                $data['success'] = true;
                $data['message'] = 'Data added successfully';

            } catch (Exception $e) {
                $table->db->trans_rollback(); //Rollback Trans

                $data['message'] = $e->getMessage();
                $data['rows'] = $items;
            }

        }
        return $data;

    }

    function update()
    {

        $ci = &get_instance();
        $ci->load->model('account/account');
        $table = $ci->account;

        $data = array('rows' => array(), 'page' => 1, 'records' => 0, 'total' => 1, 'success' => false, 'message' => '');

        $jsonItems = getVarClean('items', 'str', '');
        $items = jsonDecode($jsonItems);

        if (!is_array($items)) {
            $data['message'] = 'Invalid items parameter';
            return $data;
        }

        $table->actionType = 'UPDATE';

        if (isset($items[0])) {
            $errors = array();
            $numItems = count($items);
            for ($i = 0; $i < $numItems; $i++) {
                try {
                    $table->db->trans_begin(); //Begin Trans

                    $table->setRecord($items[$i]);
                    $table->update();

                    $table->db->trans_commit(); //Commit Trans

                    $items[$i] = $table->get($items[$i][$table->pkey]);
                } catch (Exception $e) {
                    $table->db->trans_rollback(); //Rollback Trans

                    $errors[] = $e->getMessage();
                }
            }

            $numErrors = count($errors);
            if ($numErrors > 0) {
                $data['message'] = $numErrors . " from " . $numItems . " record(s) failed to be saved.<br/><br/><b>System Response:</b><br/>- " . implode("<br/>- ", $errors) . "";
            } else {
                $data['success'] = true;
                $data['message'] = 'Data update successfully';
            }
            $data['rows'] = $items;
        } else {

            try {
                $table->db->trans_begin(); //Begin Trans

                $table->setRecord($items);
                $table->update();

                $table->db->trans_commit(); //Commit Trans

                $data['success'] = true;
                $data['message'] = 'Data update successfully';

                $data['rows'] = $table->get($items[$table->pkey]);
            } catch (Exception $e) {
                $table->db->trans_rollback(); //Rollback Trans

                $data['message'] = $e->getMessage();
                $data['rows'] = $items;
            }

        }
        return $data;

    }

    function destroy()
    {
        $ci = &get_instance();
        $ci->load->model('account/account');
        $table = $ci->account;

        $data = array('rows' => array(), 'page' => 1, 'records' => 0, 'total' => 1, 'success' => false, 'message' => '');

        $jsonItems = getVarClean('items', 'str', '');
        $items = jsonDecode($jsonItems);

        try {
            $table->db->trans_begin(); //Begin Trans

            $total = 0;
            if (is_array($items)) {
                foreach ($items as $key => $value) {
                    if (empty($value)) throw new Exception('Empty parameter');

                    $table->remove($value);
                    $data['rows'][] = array($table->pkey => $value);
                    $total++;
                }
            } else {
                $items = (int)$items;
                if (empty($items)) {
                    throw new Exception('Empty parameter');
                };
                // print_r($items);exit;
                $table->remove_foreign_primary($items);
                // $table->remove($items);
                $data['rows'][] = array($table->pkey => $items);
                $data['total'] = $total = 1;
            }

            $data['success'] = true;
            $data['message'] = $total . ' Data deleted successfully';

            $table->db->trans_commit(); //Commit Trans

        } catch (Exception $e) {
            $table->db->trans_rollback(); //Rollback Trans
            $data['message'] = $e->getMessage();
            $data['rows'] = array();
            $data['total'] = 0;
        }
        return $data;
    }

    function read()
    {

        $page = getVarClean('page', 'int', 1);
        $limit = getVarClean('rows', 'int', 5);
        $sidx = getVarClean('sidx', 'str', 'a.account_num');
        $sord = getVarClean('sord', 'str', 'asc');

        $data = array('rows' => array(), 'page' => 1, 'records' => 0, 'total' => 1, 'success' => false, 'message' => '');
        $customer_ref = getVarClean('customer_ref', 'str', '');

        try {

            $ci = &get_instance();
            $ci->load->model('account/account');
            $table = $ci->account;
            $req_param = array(
                "sort_by" => $sidx,
                "sord" => $sord,
                "limit" => null,
                "field" => null,
                "where" => null,
                "where_in" => null,
                "where_not_in" => null,
                "search" => $_REQUEST['_search'],
                "search_field" => isset($_REQUEST['searchField']) ? $_REQUEST['searchField'] : null,
                "search_operator" => isset($_REQUEST['searchOper']) ? $_REQUEST['searchOper'] : null,
                "search_str" => isset($_REQUEST['searchString']) ? $_REQUEST['searchString'] : null
            );

            // Filter Table
            $req_param['where'] = array(
//                "b.account_status = 'OK'",
                "c.billing_contact_seq = e.contact_seq",
                "e.address_seq = f.address_seq",
                "c.end_dat is null",
                "e.end_dat is null",
                "(a.account_num like '90%' or a.account_num like '80%')"
            );

            if (!empty($customer_ref)) {
                $req_param['where'][] = "a.customer_ref = '" . $customer_ref . "'";
            }

            $table->setJQGridParam($req_param);
            $count = $table->countAll();

            if ($count > 0) $total_pages = ceil($count / $limit);
            else $total_pages = 1;

            if ($page > $total_pages) $page = $total_pages;
            $start = $limit * $page - ($limit); // do not put $limit*($page - 1)

            $req_param['limit'] = array(
                'start' => $start,
                'end' => $limit
            );

            $table->setJQGridParam($req_param);

            if ($page == 0) $data['page'] = 1;
            else $data['page'] = $page;

            $data['total'] = $total_pages;
            $data['records'] = $count;

            $data['rows'] = $table->getAll();
            $data['success'] = true;

        } catch (Exception $e) {
            $data['message'] = $e->getMessage();
        }

        return $data;
    }

    public function getAccountAttr(){
        $page = getVarClean('page', 'int', 1);
        $limit = getVarClean('rows', 'int', 5);
        $sidx = getVarClean('sidx', 'str', 'a.attr_id');
        $sord = getVarClean('sord', 'str', 'asc');

        $data = array('rows' => array(), 'page' => 1, 'records' => 0, 'total' => 1, 'success' => false, 'message' => '');

        try {

            $ci = &get_instance();
            $ci->load->model('account/account_attr');
            $table = $ci->account_attr;
            $req_param = array(
                "sort_by" => $sidx,
                "sord" => $sord,
                "limit" => null,
                "field" => null,
                "where" => null,
                "where_in" => null,
                "where_not_in" => null,
                "search" => $_REQUEST['_search'],
                "search_field" => isset($_REQUEST['searchField']) ? $_REQUEST['searchField'] : null,
                "search_operator" => isset($_REQUEST['searchOper']) ? $_REQUEST['searchOper'] : null,
                "search_str" => isset($_REQUEST['searchString']) ? $_REQUEST['searchString'] : null
            );

            // Filter Table
            $req_param['where'] = array(
                "a.param_id = 1020",
                "b.RFEN = 'ACCOUNTATTRIBUTES'",
                "a.ATTR_NAME = b.NAME"
            );


            $table->setJQGridParam($req_param);
            $count = $table->countAll();

            if ($count > 0) $total_pages = ceil($count / $limit);
            else $total_pages = 1;

            if ($page > $total_pages) $page = $total_pages;
            $start = $limit * $page - ($limit); // do not put $limit*($page - 1)

            $req_param['limit'] = array(
                'start' => $start,
                'end' => $limit
            );

            $table->setJQGridParam($req_param);

            if ($page == 0) $data['page'] = 1;
            else $data['page'] = $page;

            $data['total'] = $total_pages;
            $data['records'] = $count;

            $data['rows'] = $table->getAll();
            $data['success'] = true;

        } catch (Exception $e) {
            $data['message'] = $e->getMessage();
        }

        return $data;
    }
}

/* End of file Groups_controller.php */