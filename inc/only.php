<?php
 	$recption_id = $_SESSION['recipit_id']['New'];
 	$get_recp = get_recp_info($recption_id);
 	$cash = $get_recp['cash_gift'];
	$gender = $get_recp['gender'];
	$age = $get_recp['age'];
	$ocassion = $get_recp['ocassion'];
	$get_price = get_price($cash);
	$get_data = get_category($gender,$age);
	$get_occassion_data = get_ocassion($ocassion);
 
 
 
		$chk_users = mysql_query("select userId,email from ".tbl_user." where email = '".$get_recp['email']."'");
		if(mysql_num_rows($chk_users) > 0) {
		$get_userid = mysql_fetch_array($chk_users);
		$uId = $get_userid['userId'];
		$owndata = "and own_id not like '%".$uId."%'";
		$hidedata = "and hide_id not like '%".$uId."%'";
		$lovedata = "and love_id like '%".$uId."%'";
		$query_udemo = "UNION select * from ".tbl_product." where (status = 1 or status = 0) $get_price $owndata $hidedata and hide_id not like '%".$_SESSION['LOGINDATA']['USERID']."%' $lovedata ORDER BY FIND_IN_SET('".$uId."',love_id) DESC";
		}
		
		

$query_demo = "select * from ".tbl_product." where (status = 1 or status = 0) $get_data $get_price $get_occassion_data $owndata $hidedata and hide_id not like '%".$_SESSION['LOGINDATA']['USERID']."%'";



 $select_products =  $query_demo." ".$query_udemo;
 
		$view_pro = $db->get_row($select_products,ARRAY_A);
		$get_pro = $db->get_results($select_products,ARRAY_A);
		
		/***********************GET NEXT PRODUCT********************************/
		$nxtsql = "SELECT proid FROM ".tbl_product." WHERE proid>{$view_pro['proid']}  $get_data $get_price $get_occassion_data and (status = 1 or status = 0) $owndata $hidedata and hide_id not like '%".$_SESSION['LOGINDATA']['USERID']."%'";
 if(mysql_num_rows($chk_users) > 0) {
 $nxtsqls = "UNION SELECT proid FROM ".tbl_product." WHERE proid>{$view_pro['proid']} $get_price and (status = 1 or status = 0) $owndata $hidedata and hide_id not like '%".$_SESSION['LOGINDATA']['USERID']."%' $lovedata ORDER BY proid LIMIT 1";
 }
 $next_pro =  $nxtsql." ".$nxtsqls;
 
    $result = mysql_query($next_pro);
    if (@mysql_num_rows($result)>0) {
        $nextid = mysql_result($result,0);
    }
		/***********************GET NEXT PRODUCT********************************/
		
		/***********************GET PREVIOUS PRODUCT********************************/
		$prevsql = "SELECT proid FROM ".tbl_product." WHERE proid<{$view_pro['proid']} $get_data $get_price $get_occassion_data and (status = 1 or status = 0) $owndata $hidedata and hide_id not like '%".$_SESSION['LOGINDATA']['USERID']."%'";
if(mysql_num_rows($chk_users) > 0) {
$prevsqls = "UNION SELECT proid FROM ".tbl_product." WHERE proid<{$view_pro['proid']} $get_price and (status = 1 or status = 0) $owndata $hidedata and hide_id not like '%".$_SESSION['LOGINDATA']['USERID']."%' $lovedata ORDER BY proid DESC LIMIT 1";
}
$prev_pro =  $prevsql." ".$prevsqls;

    $results = mysql_query($prev_pro);
    if (@mysql_num_rows($results)>0) {
        $previd = mysql_result($results,0);
    }
		/***********************GET PREVIOUS PRODUCT********************************/	
		
	
$suggest = count($_SESSION["products"]) + 1;
	?> 
	<div class="browse_cat sugg">Item #<?php echo $suggest ;?></div>
  	<div role="main" class="ui-content jqm-content jqm-content-c" id="filtered_picture">
		<?php if($previd != '') { ?>
		<a href="#" id="getPicButton_<?php echo $previd;?>" class="prod_arrow_left"></a>
		<?php } ?>
		<img src="<?php  get_image($view_pro['image_code']);?>" width="220" height="220" alt="<?php  echo substr($view_pro['pro_name'],0,50);?>" class="prod_img" />
		<?php if($nextid != '') { ?>
		<a href="#" id="getPicButton_<?php echo $nextid;?>" class="prod_arrow_left prod_arrow_right"></a>
		<?php } ?>
		<h3 class="item_name item_price">$<?php  echo $view_pro['price'];?></h3>
		<h3 class="item_name"><?php  echo substr($view_pro['pro_name'],0,50);?></h3>
		<p class="item_type"><strong>Vendor:</strong> <?php  echo $view_pro['vendor'];?>, <strong>Category:</strong> <?php  echo ucfirst($view_pro['category']).' , '.ucfirst($view_pro['sub_category']);?></p>
		<h3>More info <img src="<?php echo ru_resource;?>images/info_arrow.jpg" alt="More Info Arrow" id="more_info" /></h3>
		<div class="prod_desp">
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
		</div>
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
	</div><!-- /content -->
	<div id="product_cloud"></div>
	<div id="product_test3"></div>
		<div class="browse_cat">Gifts S'Jester is Suggesting For [Insert Recipient Name]:</div>
		<div id="myCanvasContainer">
 			<canvas width="250" height="250" id="myCanvas">
  				<p>will be replaced by something else</p>
  		</canvas>
 	 </div>
			<div id="tags">
    	<ul>
		<?php	 
		if($get_pro)
		{
		   foreach($get_pro as $product)
		   {
		?>
 				 <li><a onclick="procloud('<?php echo $product['proid']; ?>')" style="font-size: 16pt"><?php echo substr($product['pro_name'],0,20);?></a></li>
		<?php  } 
			 } 
		?>
  		</ul>
 </div>
	<?php include_once(ru_common."sjester_filter.php");?>	
<style>
.jqm-content-c{ overflow-x:visible}
</style>
<script type="text/javascript">
function procloud(id) {
	var myData = 'picID='+id;
	jQuery.ajax({
    url: "<?php echo ru;?>process/get_cloudpro.php",
	type: "GET",
    dataType:'html',
	data:myData,
    success:function(response)
    {
        $('#product_cloud').html(response);
		$('#filtered_picture').hide();
    }
    });
	
}
</script>
<script type="text/javascript">
      window.onload = function() {
        try {
          TagCanvas.Start('myCanvas','tags',{
            textColour: '#000000',
            outlineColour: '#ff00ff',
			outlineMethod: 'none',
            reverse: true,
            maxSpeed: 0.05,
			depth: 0.99,
  			weight: true,
  			weightMode: "size",
  			weightFrom: null,
			activeCursor: 'auto',
			initial : [0.1,-0.1],
			decel : 0.98,
			maxSpeed : 0.04,
			minBrightness : 0.2,
			depth : 0.92,
			pulsateTo : 0.6
          });
        } catch(e) {
          // something went wrong, hide the canvas container
          document.getElementById('myCanvasContainer').style.display = 'none';
        }
      };
    </script>
	<script type="text/javascript">
$(document).ready(function() {
$(".prod_arrow_left").live("click", function() {
	var myPictureId = $(this).attr('id');
	var getImgId =  myPictureId.split("_");
	getPicture(getImgId[1]); 
	return false;
});
});
</script>
<script type="text/javascript">
function getPicture(myPicId)
{
var myData = 'picID='+myPicId;
jQuery.ajax({
    url: "<?php echo ru;?>process/get_filterdonly.php",
	type: "GET",
    dataType:'html',
	data:myData,
    success:function(response)
    {
        $('#product_test3').html(response);
		$('#filtered_picture').hide();
		$('#product_cloud').hide();
    }
    });
}
</script>
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

$(document).ready(function () {
	$("#more_info").click(function () {
		$(".prod_desp").slideToggle('slow', function () {
			if ($(".prod_desp").css('display') === 'none') {
				$("#more_info").attr("src", "<?php echo ru_resource ?>images/info_arrow.jpg");
			} else {
				$("#more_info").attr("src", "<?php echo ru_resource ?>images/info_arrow_btm.jpg");
			}
		});
		//$(".prod_desp").slideToggle('slow');
})
})
</script>		
		