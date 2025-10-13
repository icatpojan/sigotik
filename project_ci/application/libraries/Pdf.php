<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
require_once dirname(__FILE__) . '/tcpdf/tcpdf.php';
 
class pdf extends TCPDF
{
    function __construct()
    {
        parent::__construct();
    }
}
//require(APPPATH.'config/tcpdf_ci'.EXT);
//require_once dirname($tcpdf['base_directory'].'tcpdf.php';

?>