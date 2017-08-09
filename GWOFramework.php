<?php
 class GWO{

	var $text;
	function __construct(){            
		$this->text=new GWOTexts();	
	}

 	
 	function printPagePostOptionArea(){
	    global $post;
	    $post_id = $post;
	    if (is_object($post_id))
	    	$post_id = $post_id->ID;
	    $gwoTest= new GWOTest();	    
	    $gwoTest->LoadFromId($post_id);

		$this->text->getPagePostOptionArea($gwoTest->gwo_enabled,
										   $gwoTest->is_AB_experiment,	
										   $gwoTest->is_original_page, 						   
										   $gwoTest->gwo_account_id,
										   $gwoTest->testid,
   										   $gwoTest->test_title,  										   
										   $gwoTest->is_goal,
										   $gwoTest->uses_linkgoal,
										   $extras);
 	}
 	function addGWOTest($id){
 		$csgwo_edit = $_POST["csgwo_edit"];
		$nonce = $_POST['nonce-gwo-edit'];
		
 		if (isset($csgwo_edit) && !empty($csgwo_edit) && wp_verify_nonce($nonce, 'edit-gwo-nonce')) {
 			$gwoTest=new GWOTest();
 			$gwoTest->post_id=$id;
 			$gwoTest->LoadFromFormPost();
 			$gwoTest->Save();
		}
 	}

 	function printHeader(){ 
		global $wp_query;
		$post = $wp_query->get_queried_object();
		$gwo_enable=(bool)get_post_meta($post->ID, '_csgwo_enable',true);
		if($gwo_enable){
			$gwo_goal=(bool)get_post_meta($post->ID, '_csgwo_goalpage',true);					
			$gwo_testid=get_post_meta($post->ID,'_csgwo_testid',true);
			$gwo_account_id=get_post_meta($post->ID,'_csgwo_accountid',true);						
			$gwo_is_ab = (bool)get_post_meta($post->ID,'_csgwo_is_AB_experiment',true);
			$gwo_linkgoal = (bool)get_post_meta($post->ID,'_csgwo_goallink',true);
			if($gwo_is_ab){
				$gwo_is_original = (bool)get_post_meta($post->ID,'_csgwo_is_original_page',true);
				if($gwo_is_original)
					$this->text->getABControlScript($gwo_account_id,$gwo_testid);
				else
					$this->text->getTrackingScript($gwo_account_id,$gwo_testid);
			}else if(!$gwo_goal)
				$this->text->getControlScript($gwo_account_id,$gwo_testid);
			if($gwo_linkgoal)
				$this->text->getConversionLinkScript($gwo_account_id,$gwo_testid);
			else if($gwo_goal)
				$this->text->getConversionScript($gwo_account_id,$gwo_testid);
		}
	}
/*	
 	function printFooter(){ 
		global $wp_query;
		$post = $wp_query->get_queried_object();
		$gwo_enable=(bool)get_post_meta($post->ID, '_csgwo_enable', true);
	
		if($gwo_enable){
			$gwo_testid=get_post_meta($post->ID,'_csgwo_testid',true);	
			$gwo_goal=(bool)get_post_meta($post->ID, '_csgwo_goalpage', true);	
			$gwo_linkgoal = (bool)get_post_meta($post->ID,'_csgwo_goallink',true);
			$gwo_account_id=get_post_meta($post->ID,'_csgwo_accountid',true);
			
			if(!$gwo_goal){
				$this->text->getTrackingScript($gwo_account_id,$gwo_testid);
				if($gwo_linkgoal)
					$this->text->getConversionLinkScript($gwo_account_id,$gwo_testid);
			}
			else if($gwo_goal){
				$this->text->getConversionScript($gwo_account_id,$gwo_testid);
			}
		}
	}*/
	
	function create_gwo_sections($content){
		$content= preg_replace("/\[section (.*?)\]/is","<script>utmx_section($1)</script>",$content);
		return str_replace("[/section]","</noscript>",$content);
	}

	function remove_gwo_sections($content){
		$content= preg_replace("/\[section (.*?)\]/is","",$content);
		return str_replace("[/section]","",$content);
	}
 }

 class GWOTest{
 	var $testid;
	var $gwo_enabled;
	var $is_AB_experiment;	
	var $is_goal;
	var $test_title;
	var $uses_linkgoal;
	var $post_id;
	var $gwo_account_id;
	var $is_original_page;
	function LoadFromFormPost(){		
	    $this->gwo_enabled=	$_POST['csgwo_enable'];
	    $this->is_AB_experiment=$_POST['csgwo_is_AB_experiment'];
	    $this->is_original_page=$_POST['csgwo_is_original_page'];
	    $this->testid=		$_POST['csgwo_testid'];
	    $this->gwo_account_id=$_POST['csgwo_accountid'];  	
	    $this->test_title=	$_POST['csgwo_title'];
	    $this->uses_linkgoal=$_POST['csgwo_useslinkgoal'];
	    $pagetype=	$_POST['csgwo_pagetype'];
	    if($pagetype=="goal")
	    	$this->is_goal=	true;
	    else
	    	$this->is_goal=	false;
	    $trackingcode=		$_POST['csgwo_trackingcode'];
	    if(isset($trackingcode) && !empty($trackingcode)){
			$this->ExtractTestAndAccountId($trackingcode);
	    }
	}
	function LoadFromId($post_id){
		$this->post_id=$post_id;
		$this->testid = 		get_post_meta($post_id, '_csgwo_testid', true);
		$this->gwo_account_id = get_post_meta($post_id, '_csgwo_accountid', true);
		$this->is_AB_experiment=get_post_meta($post_id, '_csgwo_is_AB_experiment',true);
		$this->is_original_page=get_post_meta($post_id,'_csgwo_is_original_page',true);				
	    $this->gwo_enabled = 	get_post_meta($post_id, '_csgwo_enable', true);
		$this->is_goal = 		get_post_meta($post_id, '_csgwo_goalpage', true);
		$this->test_title = 	get_post_meta($post_id, '_csgwo_title', true);
		$this->uses_linkgoal =	get_post_meta($post_id, '_csgwo_goallink',true);
	}
 	function Save(){
 		update_post_meta($this->post_id,'_csgwo_enable',$this->gwo_enabled);
		update_post_meta($this->post_id,'_csgwo_testid',$this->testid);
		update_post_meta($this->post_id,'_csgwo_accountid',$this->gwo_account_id);
	    update_post_meta($this->post_id,'_csgwo_goalpage',$this->is_goal);	    		    	
	    update_post_meta($this->post_id,'_csgwo_title',$this->test_title);
	    update_post_meta($this->post_id,'_csgwo_goallink',$this->uses_linkgoal);
		update_post_meta($this->post_id,'_csgwo_is_AB_experiment',$this->is_AB_experiment);
		update_post_meta($this->post_id,'_csgwo_is_original_page',$this->is_original_page);
 	}
 	function ExtractTestAndAccountId($trackingcode){
		$trackingcode=strip_tags($trackingcode);
		$trackingcode=str_replace('._','',$trackingcode);
		$trackingcode=stripslashes($trackingcode);
		preg_match("/\['\bgwosetAccount\b\'\,\ \'(\w\w-\d+-\d\d)\'\]\);/misx",$trackingcode,$matches);
		$this->gwo_account_id=$matches[1];
		preg_match("/\['\bgwotrackPageview\b\'\,\ \'\/(\d+)\/\btest\b\'\]\);/misx",$trackingcode,$matches);
		$this->testid=$matches[1];
	}
 }
 
 class GWOTexts{

 	function getPagePostOptionArea($gwo_enable,$is_AB_experiment,$is_original_page,$accountid,$testid,$gwo_title,$gwo_goalpage,$gwo_goallink,$extras){	?>
 			<?php echo $extras; ?>
				<a target="_blank" href="http://andreasnurbo.com/gwo-plugin"><?php _e('Plugin supportpage', 'cs_gwo_plugin') ?></a>
				<input value="csgwo_edit" type="hidden" name="csgwo_edit" id="csgwo_edit" />
				<input type="hidden" name="nonce-gwo-edit" value="<?php echo wp_create_nonce('edit-gwo-nonce') ?>" />
				<table class="form-table" style="margin-bottom:40px;width:340px;">
					<tr>
						<th scope="row" style="vertical-align:top;width:50px;">
							<?php _e('Enable GWO:', 'cs_gwo_package')?>
						</th>
						<td>
							<input type="checkbox" name="csgwo_enable" <?php if ($gwo_enable) echo "checked=\"1\""; ?>/>
						</td>
					</tr>
					<tr>
						<th scope="row" style="vertical-align:top;width:50px;">
							<?php _e('Is AB Experiment:', 'cs_gwo_package')?>
						</th>
						<td>
							<input type="checkbox" name="csgwo_is_AB_experiment" <?php if ($is_AB_experiment) echo "checked=\"1\""; ?>  onclick="jQuery('#originalpage').toggle()"/>
						</td>
					</tr>					
					<tr id="originalpage" style="display:none">
						<th scope="row" style="vertical-align:top;width:50px;">
							<?php _e('Is original page:', 'cs_gwo_package')?>
						</th>
						<td>
							<input type="checkbox" name="csgwo_is_original_page" <?php if ($is_original_page) echo "checked=\"1\""; ?>/>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php _e('Extract account/test id from tracking code:', 'cs_gwo_plugin') ?>
						</th>
						<td>
							<input type="text" name="csgwo_trackingcode" size="15" />
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php _e('Test id:', 'cs_gwo_plugin') ?>
						</th>
						<td>
							<input type="text" name="csgwo_testid" value="<?php echo $testid ?>" size="15" />
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php _e('GWO Account id:', 'cs_gwo_plugin') ?>
						</th>
						<td>
							<input type="text" name="csgwo_accountid" size="15"  value="<?php echo $accountid; ?>" />
						</td>
					</tr>
					<tr>
						<th scope="row" style="vertical-align:top;">
							<?php _e('This is a testpage:', 'cs_gwo_plugin')?>
						</th>
						<td>
							<input type="radio" name="csgwo_pagetype" value="landingpage" <?php if (!$gwo_goalpage) echo "checked"; ?> onclick="jQuery('#linkgoal').show()" />
						</td>
					</tr>
					<tr>
						<th scope="row" style="vertical-align:top;">
							<?php _e('This is a goalpage:', 'cs_gwo_plugin')?>
						</th>
						<td>
							<input type="radio" name="csgwo_pagetype" value="goal" <?php if ($gwo_goalpage) echo "checked"; ?> onclick="jQuery('#linkgoal').hide()" />
						</td>
					</tr>
					<tr id="linkgoal" style="display:none">
						<th scope="row" style="vertical-align:top;">
							<?php _e('Page uses link click as conversion(goal):', 'cs_gwo_plugin')?>
						</th>
						<td>
							<input type="checkbox" name="csgwo_useslinkgoal" <?php if ($gwo_goallink) echo "checked=\"1\""; ?>/>
						</td>
					</tr>
				</table>
		<?php if(!$gwo_goalpage){?>
		<script type="text/javascript">
		jQuery('#linkgoal').show();
		</script>
		<?php }?>
		<?php if($is_AB_experiment){?>
		<script type="text/javascript">
		jQuery('#originalpage').show();
		</script>
		<?php }?>		
							<?php
	}
	function getABControlScript($accountid,$testid){?>
<!-- Google Website Optimizer Control Script -->
<script>
function utmx_section(){}function utmx(){}
(function(){var k='<?php echo $testid?>',d=document,l=d.location,c=d.cookie;function f(n){
if(c){var i=c.indexOf(n+'=');if(i>-1){var j=c.indexOf(';',i);return escape(c.substring(i+n.
length+1,j<0?c.length:j))}}}var x=f('__utmx'),xx=f('__utmxx'),h=l.hash;
d.write('<sc'+'ript src="'+
'http'+(l.protocol=='https:'?'s://ssl':'://www')+'.google-analytics.com'
+'/siteopt.js?v=1&utmxkey='+k+'&utmx='+(x?x:'')+'&utmxx='+(xx?xx:'')+'&utmxtime='
+new Date().valueOf()+(h?'&utmxhash='+escape(h.substr(1)):'')+
'" type="text/javascript" charset="utf-8"></sc'+'ript>')})();
</script><script>utmx("url",'A/B');</script>
<!-- End of Google Website Optimizer Control Script -->
<!-- Google Website Optimizer Tracking Script -->
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['gwo._setAccount', '<?php echo $accountid?>']);
  _gaq.push(['gwo._trackPageview', '/<?php echo $testid?>/test']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
<!-- End of Google Website Optimizer Tracking Script -->	
	<?php
	}/*
	function getABControlScript($gwo_testid){?>
			<!-- Google Website Optimizer Control Script -->
		<script>
		function utmx_section(){}function utmx(){}
		(function(){var k='<?php echo $gwo_testid ?>',d=document,l=d.location,c=d.cookie;function f(n){
		if(c){var i=c.indexOf(n+'=');if(i>-1){var j=c.indexOf(';',i);return escape(c.substring(i+n.length+1,j<0?c.length:j))
		}}}var x=f('__utmx'),xx=f('__utmxx'),h=l.hash;
		d.write('<sc'+'ript src="'+
		'http'+(l.protocol=='https:'?'s://ssl':'://www')+'.google-analytics.com'
		+'/siteopt.js?v=1&utmxkey='+k+'&utmx='+(x?x:'')+'&utmxx='+(xx?xx:'')+'&utmxtime='
		+new Date().valueOf()+(h?'&utmxhash='+escape(h.substr(1)):'')+
		'" type="text/javascript" charset="utf-8"></sc'+'ript>')})();
		</script><script>utmx("url",'A/B');</script>
		<!-- End of Google Website Optimizer Control Script -->	
	
	<?php }*/
	function getControlScript($account_id,$testid){?>
<!-- Google Website Optimizer Control Script -->
<script>
function utmx_section(){}function utmx(){}
(function(){var k='<?php echo $testid?>',d=document,l=d.location,c=d.cookie;function f(n){
if(c){var i=c.indexOf(n+'=');if(i>-1){var j=c.indexOf(';',i);return escape(c.substring(i+n.
length+1,j<0?c.length:j))}}}var x=f('__utmx'),xx=f('__utmxx'),h=l.hash;
d.write('<sc'+'ript src="'+
'http'+(l.protocol=='https:'?'s://ssl':'://www')+'.google-analytics.com'
+'/siteopt.js?v=1&utmxkey='+k+'&utmx='+(x?x:'')+'&utmxx='+(xx?xx:'')+'&utmxtime='
+new Date().valueOf()+(h?'&utmxhash='+escape(h.substr(1)):'')+
'" type="text/javascript" charset="utf-8"></sc'+'ript>')})();
</script>
<!-- End of Google Website Optimizer Control Script -->
<!-- Google Website Optimizer Tracking Script -->
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['gwo._setAccount', '<?php echo $account_id?>']);
  _gaq.push(['gwo._trackPageview', '/<?php echo $testid?>/test']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
<!-- End of Google Website Optimizer Tracking Script -->
<?php	
	}/*
	function getControlScript($gwo_testid){?>
	
	
		<script type="text/javascript">
		function utmx_section(){}function utmx(){}
		(function(){var k='<?php echo $gwo_testid ?>',d=document,l=d.location,c=d.cookie;function f(n){
		if(c){var i=c.indexOf(n+'=');if(i>-1){var j=c.indexOf(';',i);
		return escape(c.substring(i+n.length+1,j<0?c.length:j))}}}
		var x=f('__utmx'),xx=f('__utmxx'),h=l.hash;
		d.write('<sc'+'ript src="'+
		'http'+(l.protocol=='https:'?'s://ssl':'://www')+'.google-analytics.com'
		+'/siteopt.js?v=1&utmxkey='+k+'&utmx='+(x?x:'')+'&utmxx='+(xx?xx:'')+'&utmxtime='
		+new Date().valueOf()+(h?'&utmxhash='+escape(h.substr(1)):'')+
		'" type="text/javascript" charset="utf-8"></sc'+'ript>')})();
		</script>
		<?php 
	}*/
	function getTrackingScript($accountid,$testid){?>
<!-- Google Website Optimizer Tracking Script -->
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['gwo._setAccount', '<?php echo $accountid ?>']);
  _gaq.push(['gwo._trackPageview', '/<?php echo $testid ?>/test']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
<!-- End of Google Website Optimizer Tracking Script -->
	<?php
	}/*
	function getTrackingScript($account_id,$gwo_testid){?>
		<script type="text/javascript">
		if(typeof(_gat)!='object')document.write('<sc'+'ript src="http'+
		(document.location.protocol=='https:'?'s://ssl':'://www')+
		'.google-analytics.com/ga.js"></sc'+'ript>')
		</script>
		<script type="text/javascript">
		try {
		var gwoTracker=_gat._getTracker("<?php echo $account_id ?>");
		gwoTracker._trackPageview("/<?php echo $gwo_testid ?>/test");
		}catch(err){}
		</script><?php 
	}*/
	
	function getConversionScript($accountid,$testid){?>
<!-- Google Website Optimizer Tracking Script -->
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['gwo._setAccount', '<?php echo $accountid?>']);
  _gaq.push(['gwo._trackPageview', '/<?php echo $testid?>/goal']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
<!-- End of Google Website Optimizer Tracking Script -->
<?php	
	}/*
	function getConversionScript($account_id,$gwo_testid){?>
		<script type="text/javascript">
		if(typeof(_gat)!='object')document.write('<sc'+'ript src="http'+
		(document.location.protocol=='https:'?'s://ssl':'://www')+
		'.google-analytics.com/ga.js"></sc'+'ript>')
		</script>
		<script type="text/javascript">
		try {
		var gwoTracker=_gat._getTracker("<?php echo $account_id ?>");
		gwoTracker._trackPageview("/<?php echo $gwo_testid ?>/goal");
		}catch(err){}</script><?php 
	}*/
	function getConversionLinkScript($accountid,$testid){?>
		<!-- Google Website Optimizer Tracking Script -->	
		<script type="text/javascript">	
		var _gaq = _gaq || [];
		_gaq.push(['gwo._setAccount', '<?php echo $accountid?>']);		
		function ConversionCount(that) {	
			try {
				_gaq.push(['gwo._trackPageview', '/<?php echo $testid?>/goal']);
				setTimeout('document.location = "' + that.href + '"', 100);
			}catch(err){} 
			return true;
		}
		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();		
		</script>
		<!-- End of Google Website Optimizer Tracking Script -->
<?php 
	}	/*
	function getConversionLinkScript($account_id,$gwo_testid){?>
		<script type="text/javascript">
		function ConversionCount() {
			var gwoTracker=_gat._getTracker("<?php echo $account_id ?>");
		    gwoTracker._trackPageview("/<?php echo $gwo_testid; ?>/goal");
		    return true;
		}	
		</script><?php 
	}*/
}