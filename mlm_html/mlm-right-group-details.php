<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function myRightGroupDetails()
{
	//get loged user's key
	$key = get_current_user_key();
	
	//Total Users on My left leg
	$rightLegUsers = totalRightLegUsers($key);
	
	//paid users on my left leg
	$rightLegActiveUsers = activeUsersOnRightLeg($key);
	
	//show total users on left leg
	$totalRightLegUsers = myTotalRightLegUsers($key);
	//echo "<pre>";print_r($totalRightLegUsers);exit;
?>
<script type="text/javascript" src="//www.google.com/jsapi"></script>
<script type="text/javascript">
  google.load('visualization', '1.1', {packages: ['table']});
</script>
<script type="text/javascript">
var visualization;
var data;
var options = {'showRowNumber': true};
function drawVisualization() {
  // Create and populate the data table.
  var dataAsJson =
  {cols:[
	{id:'A',label:'User Name',type:'string'},
	{id:'B',label:'User Key',type:'string'},
	{id:'C',label:'Sponsor',type:'string'},
	{id:'D',label:'Status',type:'string'}],
  rows:[
   <?php foreach( $totalRightLegUsers as $row) :  ?>
		{c:[{v:'<?php echo $row['username']?>'},{v:'<?php echo $row['user_key']?>'},{v:'<?php echo $row['sponsor_key']?>'},{v:'<?php echo $row['payment_status']?>'}]},
	<?php endforeach ;?>  
  ]};
  data = new google.visualization.DataTable(dataAsJson);
  // Set paging configuration options
  // Note: these options are changed by the UI controls in the example.
  options['page'] = 'enable';
  options['pageSize'] = 10;
  options['pagingSymbols'] = {prev: 'prev', next: 'next'};
  //options['pagingButtonsConfiguration'] = 'auto';
  //options['allowHtml'] = true;
  //data.sort({column:1, desc: false});
  // Create and draw the visualization.
  visualization = new google.visualization.Table(document.getElementById('table'));
  draw();
}
function draw() {
  visualization.draw(data, options);
}
google.setOnLoadCallback(drawVisualization);
// sets the number of pages according to the user selection.
function setNumberOfPages(value) {
  if (value) {
	options['pageSize'] = parseInt(value, 10);
	options['page'] = 'enable';
  } else {
	options['pageSize'] = null;
	options['page'] = null;  
  }
  draw();
}
// Sets custom paging symbols "Prev"/"Next"
function setCustomPagingButtons(toSet) {
  options['pagingSymbols'] = toSet ? {next: 'next', prev: 'prev'} : null;
  draw();  
}
function setPagingButtonsConfiguration(value) {
  options['pagingButtonsConfiguration'] = value;
  draw();
}
</script>
<!--va-matter-->
    <div class="va-matter">
    	<!--va-matterbox-->
    	<div class="va-matterbox">
        	<!--va-headname-->
        	<div class="va-headname">My Right Group Details</div>
            <!--/va-headname-->
			<div class="va-admin-leg-details">
            	<!--va-admin-mid-->
				<div class="paging">
				  <form action="">
					<div class="left-side">
						Display Number of Rows : &nbsp; 
					</div>
					<div class="right-side">
						<select style="font-size: 12px" onchange="setNumberOfPages(this.value)">
						  <option value="5">5</option>
						  <option selected="selected" value="10">10</option>
						  <option value="20">20</option>
						  <option  value="50">50</option>
						  <option value="100">100</option>
						  <option value="500">500</option>
						   <option value="">All</option>
						</select>
					</div>	
					</form>
					<div class="right-members">
					Total Users: <strong><?php echo  $rightLegUsers; ?></strong>&nbsp;Active Users: <strong><?php echo  $rightLegActiveUsers; ?></strong>
					</div>
					<div class="va-clear"></div>
				  </div>
				<div id="table"></div>
				<div class="va-clear"></div>
			</div>		
		</div>
	</div>	
<?php
}	
?>