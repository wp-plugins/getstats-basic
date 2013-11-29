<?php
	if ( ! defined( 'ABSPATH' ) || ! current_user_can( 'manage_options' ) ) exit;
	
	include_once('geoip.inc');
	$geoIp = geoip_open(realpath(dirname(__FILE__)).'/GeoIP.dat',GEOIP_STANDARD);
	
	global $wpdb;
	
	$qmgs = "";
	
	if(isset($_POST['bsc_submit_lm'])){
		$del_q = "delete from ".$wpdb->prefix."bsc_stat where vtime < DATE_SUB(NOW() , 1 MONTH)";
		
		//echo $del_q;exit;
		if($wpdb->query($del_q)){
			$qmgs = "Data successfully deleted.";
		}
	}
	elseif(isset($_POST['bsc_submit_all'])){
		$del_q = "delete from ".$wpdb->prefix."bsc_stat";
		
		//echo $del_q;exit;
		if($wpdb->query($del_q)){
			$qmgs = "Data successfully deleted.";
		}
	}
	
	$q_total = "SELECT count( * ) as total FROM ".$wpdb->prefix."bsc_stat";
	$r_total = $wpdb->get_results($q_total);
	$cntres = $r_total[0]->total;
	$num_page = ceil($cntres/30);
	
	if(isset($_GET['pg'])){
		$p = $_GET['pg'];
	}
	else
	{
		$p = 0;
	}
	if($p>1)
	{
		$l=($p*30)-30;	
		if($l >= $cntres)
		{
			$l=0;
		}									
	}
	else{
		$l=0;
	}
	
	$query = "select * from ".$wpdb->prefix."bsc_stat order by vtime desc limit $l,30";
	$res = $wpdb->get_results($query);
	//echo "<pre>";
	//print_r($res);
?>
<div class="wrap">
  <div id="icon-themes" class="icon32"> <br>
  </div>
 <?php if($qmgs):?>
  <div id="message" class="updated fade">
    <p><?php echo $qmgs;?></p>
  </div>
 <?php endif;?>
  <h2 style="margin-bottom:30px;">Visitor Stat</h2>
  <div class="tablenav top">
    <div class="tablenav-pages"> 
    	<span class="displaying-num">total <?php echo $r_total[0]->total;?> visitor</span> 
        <span class="pagination-links"> 
        	<a href="?page=gsbsc-stat&pg=1">«</a> 
            <a href="?page=gsbsc-stat&pg=<?php if(isset($_GET['pg']) && $_GET['pg'] > 1){$pg_p = $_GET['pg'] - 1;echo $pg_p;}else{echo 1;}?>">‹</a> 
            <span class="paging-input">
      			<span class="total-pages"><?php if(isset($_GET['pg'])){echo $_GET['pg'];}else{echo 1;}?></span>
      				of <span class="total-pages"><?php echo $num_page;?></span> 
            </span> 
            <a href="?page=gsbsc-stat&pg=<?php if(isset($_GET['pg'])){if($_GET['pg'] >= $num_page){echo $num_page;}else{$pg_n = $_GET['pg'] + 1;echo $pg_n;}}else{if($num_page > 1){echo 2;}else{echo 1;}}?>">›</a> 
            <a href="?page=gsbsc-stat&pg=<?php echo $num_page;?>">»</a>
       </span> 
    </div>
  </div>
  <table class="wp-list-table widefat fixed posts" cellspacing="0">
    <thead>
      <tr>
      	<th>Country</th>
        <th>IP</th>
        <th>User Agent</th>
        <th>Page</th>
        <th>Referrer</th>
        <th>Count</th>
        <th>Last visit</th>
      </tr>
    </thead>
    <?php foreach($res as $stat){?>
    <tr valign="top">
      <td><strong><?php echo geoip_country_name_by_addr($geoIp, $stat->ip);?></strong></td>
      <td><?php echo $stat->ip;?></td>
      <td><?php echo $stat->ua;?></td>
      <td><?php echo $stat->page;?></td>
      <td><?php echo $stat->referrer;?></td>
      <td><?php echo $stat->vcount;?></td>
      <td><?php echo date('d M, Y H:i:s',strtotime($stat->vtime));?></td>
    </tr>
    <?php }?>
  </table>
  <p style="margin:15px;">
  <form action="" method="post" onsubmit="return false" id="del-frm">
  	<input type="submit" name="bsc_submit_lm" value="Delete Last month data" class="button-primary del-data" />
    <span style="margin-left:20px;"><input type="submit" name="bsc_submit_all" value="Delete All data" class="button-primary del-data" /></span>
  </form>
  </p>
</div>
<script>
	jQuery(document).ready(function($) {
        $(".del-data").click(function(){
			var cnfAck = confirm("Do you realy want to Delete those data ?");
			if(cnfAck == true){
				$("#del-frm").removeAttr('onsubmit');
				$("#del-frm").submit();
			}
		});
    });
</script>