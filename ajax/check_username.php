<?php

if ( defined('WP_CONFIG_ONLY') ) return; 
$action = $_REQUEST['action'];
global $wpdb, $table_prefix;
if ($action == 'username') {
include("../../../../wp-config.php");
   	$q = $_GET['q'];
	 $uname = $wpdb->get_var("SELECT user_login FROM {$table_prefix}users WHERE user_login = '$q'");
      
    	if(empty($uname)){
        	_e("<span class='msg' style='color:green;'>&nbsp;"). _e('Specified username is valid!',MLM_PLUGIN_NAME)."</span>";
			
    	} else{
        	_e("<span class='errormsg' style='color:red;'>&nbsp;"). _e('Specified username is InValid!',MLM_PLUGIN_NAME)."</span>";
	}
} else if ($action == 'sponsor') {

include("../../../../wp-config.php");
    $q = $_GET['q'];
    $sname = $wpdb->get_var("SELECT username FROM {$table_prefix}mlm_users WHERE `username` = '$q'");

    if (!$sname)
        _e("<span class='errormsg'>"). _e('Sorry! The specified sponsor is not available for registration', MLM_PLUGIN_NAME)."</span>";
    //else
    //echo "<span class='msg'>Congratulations! Your sponsor is <b> ".ucwords(strtolower($sname))."</b> .</span>";
}
?>