<?php
include_once("../connect/connect.php");
include_once("../config/config.php");

$recption_id = $_SESSION['recipit_id']['New'];
$query = "select recipit_id,cash_gift,gender,age,ocassion,email from ".tbl_recipient." where recipit_id = '".$recption_id."'";
$get_info = $db->get_row($query,ARRAY_A);
$cash = $get_info['cash_gift'];
$gender = $get_info['gender'];
$age = $get_info['age'];
$ocassion = $get_info['ocassion'];
$get_price = get_price($cash);
$get_data = get_category($gender,$age);
$get_occassion_data = get_ocassion($ocassion);
$category = mysql_real_escape_string(stripslashes(trim($_GET['picID'])));
 
$get_user = mysql_fetch_array(mysql_query("select userId from ".tbl_user." where email = '".$get_info['email']."'"));
$ruserId = $get_user['userId']; 
 
$query = "select * from ".tbl_product." where proid = '".$category."' and (status = 1 or status = 0) and customer_id not like '%".$_SESSION['LOGINDATA']['USERID']."%'";
$view_pro = $db->get_row($query,ARRAY_A);

/***********************GET NEXT PRODUCT********************************/
 $sql = "SELECT p.*,l.* FROM gift_product p, gift_love_it l WHERE p.proid>{$view_pro['proid']}  $get_data $get_price $get_occassion_data and (status = 1 or status = 0) and p.proid = l.proid AND l.userId = '".$ruserId."' ORDER BY p.proid LIMIT 1";
    $result = mysql_query($sql);
    if (@mysql_num_rows($result)>0) {
        $nextid = mysql_result($result,0);
    }
/***********************GET NEXT PRODUCT********************************/

/***********************GET PREVIOUS PRODUCT********************************/
 $sqls = "SELECT p.*,l.* FROM gift_product p, gift_love_it l WHERE p.proid<{$view_pro['proid']} $get_data $get_price $get_occassion_data and (status = 1 or status = 0) and p.proid = l.proid AND l.userId = '".$ruserId."' ORDER BY p.proid DESC LIMIT 1";
    $results = mysql_query($sqls);
    if (@mysql_num_rows($results)>0) {
        $previd = mysql_result($results,0);
    }	
/***********************GET PREVIOUS PRODUCT********************************/	 
?>
  	<div role="main" class="ui-content jqm-content jqm-content-c">
		<h3 class="item_name"><?php  echo substr($view_pro['pro_name'],0,50);?></h3>
		<p class="item_type"><strong>Vendor:</strong> <?php  echo $view_pro['vendor'];?>, <strong>Category:</strong> <?php  echo ucfirst($view_pro['category']).' , '.ucfirst($view_pro['sub_category']);?></p>
		<h3 class="item_name item_price">$<?php  echo $view_pro['price'];?></h3>
		<?php if($previd != '') { ?>
		<a href="#" id="getPicButton_<?php echo $previd;?>" class="prod_arrow_left"></a>
		<?php } ?>
		<img src="<?php  get_image($view_pro['image_code']);?>" width="220" height="220" alt="<?php  echo substr($view_pro['pro_name'],0,50);?>" class="prod_img" />
		<?php if($nextid != '') { ?>
		<a href="#" id="getPicButton_<?php echo $nextid;?>" class="prod_arrow_left prod_arrow_right"></a>
		<?php } ?>
		<!--<h3>Product Description Display Here</h3>-->
		<form method="post" action="<?php echo ru;?>process/process_cart.php" data-ajax="false">
			<input type="hidden" name="proId" id="proId" value="<?php echo $view_pro['proid'];?>" />
			<input type="hidden" name="userId" id="userId" value="<?php echo $_SESSION['LOGINDATA']['USERID'];?>" />
			<input type="hidden" name="type" id="type" value="add" />
			<input type="hidden" name="qty" id="qty" value="1" />
			<button class="ui-btn ui-corner-all" id="suggest_test">Suggest</button>
		</form>
		<div class="user_love_it" style="display:none;">
			<img src="<?php echo ru_resource;?>images/user_love_it.jpg" alt="User Love It" /> <span>[ Test ] Loves it !</span>
		</div>
		<div class="ui-grid-b item-likes-type">
		<!---------------------OWN_IT------------------------------->
		<?php
			$get_own = mysql_num_rows(mysql_query("select userId from ".tbl_own." where userId = '".$_SESSION['LOGINDATA']['USERID']."' and proid = '".$view_pro['proid']."'"));
			$get_q = "SELECT count( own_it ) AS cnt FROM ".tbl_own." WHERE proid = '".$view_pro['proid']."' GROUP BY proid HAVING Count( own_it )";
			$view_q = $db->get_row($get_q,ARRAY_A);
			?>
			<div id="own_its" class="ui-block-a <?php if($get_own > 0) {?>active<?php } ?>" <?php if($get_own == 0) { ?>onclick="own_it('<?php echo $view_pro['proid'];?>','<?php echo $_SESSION['LOGINDATA']['USERID'];?>','own')" <?php } ?>><div class="ui-bar ui-bar-a">Own it<div class="own_it"></div><span><?php echo $view_q{'cnt'}; ?> People Own it</span></div></div>
			<div id="own_itbtms"></div>
			
			<!-----------------------LOVE_IT----------------------------->
			<?php
			$get_love = mysql_num_rows(mysql_query("select userId from ".tbl_love." where userId = '".$_SESSION['LOGINDATA']['USERID']."' and proid = '".$view_pro['proid']."'"));
			$get_l = "SELECT count( love_it ) AS cnt FROM ".tbl_love." WHERE proid = '".$view_pro['proid']."' GROUP BY proid HAVING Count( love_it )";
			$view_l = $db->get_row($get_l,ARRAY_A);
			?>
			<div id="love_its" class="ui-block-b <?php if($get_love > 0) {?>active<?php } ?>" <?php if($get_love == 0) { ?>onclick="love_it('<?php echo $view_pro['proid'];?>','<?php echo $_SESSION['LOGINDATA']['USERID'];?>','love')" <?php } ?>><div class="ui-bar ui-bar-a">Love it<div class="own_it love_it"></div><span><?php echo $view_l{'cnt'}; ?> People Love it</span></div></div>
			<div id="love_itbtms"></div>
			
			<!-----------------------HIDE_IT----------------------------->
			<?php
			$get_hide = mysql_num_rows(mysql_query("select userId from ".tbl_hide." where userId = '".$_SESSION['LOGINDATA']['USERID']."' and proid = '".$view_pro['proid']."'"));
			$get_h = "SELECT count( hide_it ) AS cnt FROM ".tbl_hide." WHERE proid = '".$view_pro['proid']."' GROUP BY proid HAVING Count( hide_it )";
			$view_h = $db->get_row($get_h,ARRAY_A);
			?>
			<div id="hide_its" class="ui-block-c <?php if($get_hide > 0) {?>active<?php } ?>" <?php if($get_hide == 0) { ?>onclick="hide_it('<?php echo $view_pro['proid'];?>','<?php echo $_SESSION['LOGINDATA']['USERID'];?>','hide')" <?php } ?>><div class="ui-bar ui-bar-a">Hide it<div class="own_it hide_it"></div><span><?php echo $view_h{'cnt'}; ?> People Hide it</span></div></div>
			<div id="hide_itbtms"></div>
		</div><!-- /grid-b -->
	</div>
<script type="text/javascript">
function own_it(proid,uid,type)
{
	var proId = proid;
	var userId = uid;
	var type = type;
	$.ajax({
	url: '<?php echo ru;?>process/process_product.php?proid='+proId+'&userId='+userId+'&type='+type,
	type: 'get', 
	success: function(output) {
	$('#own_its').hide();
	$('#own_itbtms').html(output);
	}
	});
}

function love_it(proid,uid,type)
{
	var proId = proid;
	var userId = uid;
	var type = type;
	$.ajax({
	url: '<?php echo ru;?>process/process_product.php?proid='+proId+'&userId='+userId+'&type='+type,
	type: 'get', 
	success: function(output) {
	$('#love_its').hide();
	$('#love_itbtms').html(output);
	}
	});
}

function hide_it(proid,uid,type)
{
	var proId = proid;
	var userId = uid;
	var type = type;
	$.ajax({
	url: '<?php echo ru;?>process/process_product.php?proid='+proId+'&userId='+userId+'&type='+type,
	type: 'get', 
	success: function(output) {
	$('#hide_its').hide();
	$('#hide_itbtms').html(output);
	}
	});
}
</script>		
		