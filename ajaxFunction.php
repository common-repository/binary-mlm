<?php 
 
if ( defined('WP_CONFIG_ONLY') ) return; 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$g_criteria = ""; 
$g_criteria1 = ""; 
$g_criteria2 = ""; 
$g_criteria3 = "";
 
if(isset($_REQUEST['do'])) {
	$g_criteria1 = trim(filter_var($_REQUEST['do'], FILTER_SANITIZE_STRING));
}

if(isset($_REQUEST['event'])) {
	$g_criteria2 = trim(filter_var($_REQUEST['event'], FILTER_SANITIZE_STRING));
}


switch($g_criteria1)
{
	
	case "statuschange": 
		updatePaymentStatus();		
	break;
	
}


function updatePaymentStatus()
{
	global $wpdb;
	if(isset($_REQUEST['userId']) && isset($_REQUEST['status']))
	{
		$table_prefix = mlm_core_get_table_prefix();
				
		$sql = $wpdb->prepare( "UPDATE 
								 
				      SET 
					     payment_status = '".filter_var($_REQUEST['status'], FILTER_SANITIZE_STRING)."'
				      WHERE 
					     user_id = '".filter_var($_REQUEST['userId'], FILTER_SANITIZE_STRING)."',{$table_prefix}mlm_users  ");
			
		$rs = $wpdb->query($sql);
		if(!$rs){
			echo "<span class='error' style='color:red'>Updating Fail</span>";
		} 		 
		 
	}
	
}



?>