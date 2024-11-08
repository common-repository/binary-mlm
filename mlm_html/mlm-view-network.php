<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class BinaryTree 
{
	//$clients array contain the nodes
	public $clients=array();
	
	//$add_page_id variable take the wordpress pageID for the network registration
	public $add_page_id; 
	
	//$view_page_id take the wordpress pageID where the netwok to open
	public $view_page_id;
	
	//$counter varibale take the how many level you want to shows the network
	public $counter = 5;
	
	//constructor
	function __construct()
	{
		$this->add_page_id = get_post_id('mlm_registration_page');
		$this->view_page_id = get_post_id('mlm_network_page');
	}
	//getUsername() function take the key and return the username of the user's key
	function getUsername($key)
	{
		$table_prefix = mlm_core_get_table_prefix();
		global $wpdb;
		$sql = "SELECT username 
				FROM {$table_prefix}mlm_users
				WHERE user_key = '".$key."'
				AND banned = '0'";
		$sql = $wpdb->get_row($sql,ARRAY_A);
		$row = $sql;
		return $row['username'];
	}
	
	//addLeftLeg() function build the Left leg registration node of the network
	function addLeftLeg($key, $add_page_id)
	{
		$username = $this->getUsername($key);
		$str = "[{v:'".$key."ADD', 
				f:'<a href=\"?page_id=".$add_page_id."&k=".$key."&l=0\">ADD</a><br><span class=\"leg\">Left leg</span>'}, 
				'".$username."', 
				''],";
		return $str;
	}
	
	//addRightLeg() function build the Right leg registration node of the network
	function addRightLeg($key, $add_page_id)
	{
		$username = $this->getUsername($key);
		$str = "[{v:'".$key."ADD2', 
				f:'<a href=\"?page_id=".$add_page_id."&k=".$key."&l=1\">ADD</a><br><span class=\"leg\">Right leg</span>'}, 
				'".$username."', 
				''],";
		return $str;
	}
	
	//checkPaymentStatus() function check the node user is paid or not
	function checkPaymentStatus($key, $payment, $view_page_id)
	{
		$paymentStr='<br><a href="?page_id='.$view_page_id.'&k='.$key.'">View</a>';
		if($payment==1)
		{
			$paymentStr.='<br><span class=\"paid\">PAID</span>';
		}
		return $paymentStr;
	}
	
	//buildRootNetwork() function take the key and build the root node of the network
	function buildRootNetwork($key)
	{
		$level = array();
		$username = $this->getUsername($key);
		$myclients[]="[{v:'".$username."', f:'".$username."<br><span class=\"owner\">Owner</span>'}, '', 'The owner'],";
		$this->clients[] = $myclients;
		$level[] = $key;
		return $level;
	}
	
	//buildLevelByLevelNetwork() function build the 1st and more level network
	function buildLevelByLevelNetwork($key, $add_page_id, $view_page_id, $counter, $level)
	{
		$table_prefix = mlm_core_get_table_prefix();
		global $wpdb;
		$level1 = array();
		for($i=0; $i<$counter; $i++)
		{
			$myclients = array();
			if($level[$i]!='add' && $level[$i]!='')
			{
				$sql = "SELECT username, payment_status, user_key, leg 
						FROM {$table_prefix}mlm_users 
						WHERE parent_key = '".$level[$i]."' 
						AND banned='0' 
						ORDER BY leg DESC";
				$sql = $wpdb->get_results($sql,ARRAY_A);
				$num = $wpdb->num_rows;
				// no child case
				if(!$num)
				{
					$myclients[]= $this->addLeftLeg($level[$i], $add_page_id);
					$myclients[]= $this->addRightLeg($level[$i], $add_page_id);
					$level1[] = 'add';
					$level1[] = 'add';
				}
				//if child exist
				else if($num > 0)
				{
					$username = $this->getUsername($level[$i]);
					
					foreach($sql as $row)
					{
						//check user paid or not
						$payment = $this->checkPaymentStatus($row['user_key'], $row['payment_status'], $view_page_id);
						//if only one child exist
						if($num == 1)
						{
							//if right leg child exist
							if($row['leg']==1)
							{
								$myclients[] = $this->addLeftLeg($level[$i], $add_page_id);
								$myclients[] = "[{v:'".$row['username']."',f:'".$row['username'].$payment."'}, '".$username."', ''],";
								$level1[] = 'add';
								$level1[] = $row['user_key'];
							}
							else  // if left leg child exist
							{
								$myclients[] = "[{v:'".$row['username']."',f:'".$row['username'].$payment."'}, '".$username."', ''],";
								$myclients[] = $this->addRightLeg($level[$i], $add_page_id);
								$level1[] = $row['user_key'];
								$level1[] = 'add';
							}
						}
						else  //both child exist left and right leg
						{
							$myclients[] = "[{v:'".$row['username']."',f:'".$row['username'].$payment."'}, '".$username."', ''],";
							$level1[] = $row['user_key'];
						}
					} //end while loop
				}
				$this->clients[] = $myclients;
			} // end most outer if statement
		} //end for loop
		return $level1;
	}
	
	function network()
	{
		global $current_user,$wpdb;
		get_currentuserinfo();
		$username2 = $current_user->user_login;
		$table_prefix = mlm_core_get_table_prefix();
		$res12 = $wpdb->get_row("SELECT * FROM {$table_prefix}mlm_users WHERE username = '".$username2."'",ARRAY_A);
		if($_POST['search'])
		{
$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
			$username = sanitize_text_field( $_POST['username']);
			$select = $wpdb->get_row("SELECT user_key FROM {$table_prefix}mlm_users WHERE username = '".$username."'",ARRAY_A);
			
			if(!empty($select))
			{
				$result = $select;
				$left = $wpdb->get_var("SELECT pkey FROM {$table_prefix}mlm_leftleg 
									 WHERE ukey = '".$result['user_key']."' AND pkey = '".$res12['user_key']."'");
				$leftnum = $left;
				if(!empty($leftnum))
					$key = $result['user_key'];
				else
				{
					$right = $wpdb->get_var("SELECT pkey FROM {$table_prefix}mlm_rightleg 
										  WHERE ukey = '".$result['user_key']."' AND pkey = '".$res12['user_key']."'");
					$rightnum = $right;
					if(!empty($rightnum))
						$key = $result['user_key'];
					else
					{
					?>
		<p style="margin:0 auto;padding:0px;font-family:Arial, Helvetica, sans-serif;font-size:20px; text-align:center; font-weight:bold; padding:25px 0px 15px 0px;color:grey;">You can't authorized to access searched user's genealogy.</p>
		<div class="button-wrap" style="height:14px; margin-left:20px;">
<div class="red button">
<a href="javascript:history.back(1)">Back</a>
</div>
</div>
		<?php
						exit;
					}
				}
			}
			else
			{?>
		<p style="margin:0 auto;padding:0px;font-family:Arial, Helvetica, sans-serif;font-size:20px; text-align:center; font-weight:bold; padding:25px 0px 15px 0px;color:grey;">You have searched a wrong username.</p>
		<div class="button-wrap" style="height:14px; margin-left:20px;">
<div class="red button">
<a href="javascript:history.back(1)">Back</a>
</div>
</div>
		<?php
				exit;
			}	
		}
		else
		{
			if($_GET['k']!='')
				$key = filter_var($_GET['k'], FILTER_SANITIZE_STRING);
			else
				$key = get_current_user_key();
		}
/*
		$browser = getBrowser();
		if($browser=="error")
		{?>
		<p style="margin:0 auto;padding:0px;font-family:Arial, Helvetica, sans-serif;font-size:20px; text-align:center; font-weight:bold; padding:25px 0px 15px 0px;color:grey;">For best results use only <a href="http://www.apple.com/safari/download/" target="_blank">Safari</a> / <a href="https://www.google.com/chrome" target="_blank">Chrome</a>.</p>
		<?php
			exit;
		}*/
		if(!checkKey($key))
		{?>
		<p style="margin:0 auto;padding:0px;font-family:Arial, Helvetica, sans-serif;font-size:20px; text-align:center; font-weight:bold; padding:25px 0px 15px 0px;color:grey;">There has been an error while generating this tree. <br>Please contact the system admin at <?php echo  get_settings('admin_email') ?> to report this problem.</p>
		<?php
			exit;
		}

	/*********************************************** Root node ******************************************/
		$level = $this->buildRootNetwork($key);
	/*********************************************** First level ******************************************/
		$level = $this->buildLevelByLevelNetwork($key, $this->add_page_id, $this->view_page_id, 1, $level);
	/*********************************************** 2 and more level's ******************************************/
		if($this->counter>=2)
		{
			$j = 1;
			for($i = 2; $i<= $this->counter; $i++)
			{
				$j = $j * 2;
				$level = $this->buildLevelByLevelNetwork($key, $this->add_page_id, $this->view_page_id, $j, $level);
			}
		}
		return $this->clients;
	}//end function
}// end class
function viewBinaryNetwork()
{
	$obj = new BinaryTree();
	$var = $obj->network();
	global $current_user,$wpdb;
	get_currentuserinfo();
	$username1 = $current_user->user_login;
	$table_prefix = mlm_core_get_table_prefix();
	$res = $wpdb->get_row("SELECT user_key FROM {$table_prefix}mlm_users WHERE username = '".$username1."'",ARRAY_A);
?>
	<style type="text/css">
		span.owner
		{
			color:#339966; 
			font-style:italic;
		}
		span.paid
		{
			color: #669966!important; 
			/*background-color:#770000; */
			font-style:normal;
		}
		span.leg
		{
			color:red; 
			font-style:italic;
		}
	</style>
    <script type='text/javascript' src='//www.google.com/jsapi'></script>
    <script type='text/javascript'>
      google.load('visualization', '1', {packages:['orgchart']});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Name');
        data.addColumn('string', 'Manager');
        data.addColumn('string', 'ToolTip');
        data.addRows([<? for($i=0;$i<count($var);$i++){for($j=0;$j<2;$j++)echo $var[$i][$j];}?> ['', null,'']]);
        var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
        chart.draw(data, {allowHtml:true});}
</script>
<script type="text/javascript" language="javascript">
	function searchUser()
	{
		var user = document.getElementById("username").value;
		if(user=="")
		{
			alert("Please enter username then searched.");
			document.getElementById("username").focus();
			return false;
		}
	}
</script>
 		<table border="0" cellspacing="0" cellpadding="0" >
			<tr>
  				<td align="center">	
					<form action="<?php bloginfo('url'); ?>/?page_id=<?php echo $obj->view_page_id?>&k=<?php echo $res['user_key'];?>" method="post">
						<input type="submit" value="YOU">
					</form>
				</td>
			<td align="center">
				<form name="usersearch" id="usersearch" action="" method="post" onSubmit="return searchUser();">
					<input type="text" name="username" id="username"> <input type="submit" name="search" value="Search">
				</form>
			</td>
		</tr>               
		</table>
		<div style="margin:0 auto;padding:0px;clear:both; width:100%!important;" align="center">
    <div id='chart_div'></div>
</div>
<?php
}
?>