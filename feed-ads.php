<?PHP
/*
Plugin Name: Feed Ads Plugin
Description: Places text randomly at the end of feed content and excerpts. Used to add advertising, or other messages randomly to feed entries.
Version: 1.1
Author: Keith P. Graham
Author URI: http://www.BlogsEye.com/
Requires at least: 2.8
Tested up to: 3.3


This software is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/************************************************************
* 	kpg_insertAds()
*	Gets the ads out of options and inserts one of them
*   based on a random number into the feed
*************************************************************/
function kpg_insertAds($content,$feed_type=null) {
	// get options
	echo "<!-- insert ads is firing -->";
	$options=get_option('kpg_insert_ad_options');
	if (empty($options)||!is_array($options)) return $content;
	$rline=array();
	$prob=array();
	$onead='N';
	extract($options);
	// check the array of odds to see if there is anything to do
	for ($j=0;$j<count($prob);$j++) {
		$t=stripslashes($rline[$j]);
		$r=$prob[$j];
		// $r is a number between 1 and 100 indicating how often the add will appear
		$b=rand(0, 100);
		if ($b<$r) {
			// we have a hit on this guy.
			//$t=htmlspecialchars($t);
			$content = $content.' '.$t;
			if ($onead=='Y') return $content;
		}
	}
	return $content; 
}
// add the feed filters.
add_filter('the_excerpt_rss', 'kpg_insertAds'); //depricated
add_filter('the_content_feed', 'kpg_insertAds');
add_filter('comment_text_rss', 'kpg_insertAds');


/************************************************************
* 	kpg_insertAds_control()
*	Options page for plugin
*************************************************************/
function kpg_insertAds_control()  {
?>

<div class="wrap">
  <h2>Feed Ads Plugin</h2>
  <?php
	$options=get_option('kpg_insert_ad_options');
	if (empty($options)||!is_array($options)) $options=array();
	$prob=array();
	$rline=array();
	$onead='N';
	extract($options);
	if (array_key_exists('kpg_insertAds_nonce',$_POST)&&wp_verify_nonce($_POST['kpg_insertAds_nonce'],'kpg_insertAds')) { 
		// need to update 
		if (array_key_exists('prob',$_POST)) {
			$prob=$_POST['prob'];
		} 
		if (array_key_exists('rline',$_POST)) {
			$rline=$_POST['rline'];
		} 
		if (array_key_exists('onead',$_POST)) {
			$onead=$_POST['onead'];
		} else {
			$onead='N';
		}
		$p=array();
		$r=array();
		$k=0;
		for ($j=0;$j<count($prob);$j++) {
			if (!empty($prob[$j])&&!empty($rline[$j])) {
				$p[$k]=$prob[$j];
				$r[$k]=stripslashes($rline[$j]);
				if ($p[$k]>100) $p[$k]=100;
				if ($p[$k]<1) $p[$k]=0;
				$k++;
				
			}
		}
		$prob=$p;
		$rline=$r;
		if (empty($onead)||$onead!='Y') $onead='N';
		$options['onead']=$onead;
		$options['prob']=$prob;
		$options['rline']=$rline;
		update_option('kpg_insert_ad_options', $options);
		?>
  <h3>Parameters Updated</h3>
  <?php

	}
   $nonce=wp_create_nonce('kpg_insertAds');

?>
  <script language="javascript" type="text/javascript">
function numerickey(event) {
	var ev=event||window.event;
	targ=ev.srcElement||ev.target;
	var unicode=ev.charCode ? ev.charCode : ev.keyCode
	if (unicode!=8 && unicode !=46){ //if the key isn't the backspace key (which we should allow)?)
		if (unicode<48||unicode>57) { //if not a number
			return false; //disable key press
		}
	}
	return true;
}
</script>
  <div style="position:relative;float:right;width:45%;background-color:ivory;border:#333333 medium groove;padding:4px;margin-left:4px;">
    <p>This plugin is free and I expect nothing in return. If you would like to support my programming, you can buy my book of short stories.</p>
    <p>Some plugin authors ask for a donation. I ask you to spend a very small amount for something that you will enjoy. eBook versions for the Kindle and other book readers start at 99&cent;. The book is much better than you might think, and it has some very good science fiction writers saying some very nice things. <br/>
      <a target="_blank" href="http://www.amazon.com/gp/product/1456336584?ie=UTF8&tag=thenewjt30page&linkCode=as2&camp=1789&creative=390957&creativeASIN=1456336584">Error Message Eyes: A Programmer's Guide to the Digital Soul</a></p>
    <p>A link on your blog to one of my personal sites would also be appreciated.</p>
    <p><a target="_blank" href="http://www.WestNyackHoney.com">West Nyack Honey</a> (I keep bees and sell the honey)<br />
      <a target="_blank" href="http://www.cthreepo.com/blog">Wandering Blog </a> (My personal Blog) <br />
      <a target="_blank" href="http://www.cthreepo.com">Resources for Science Fiction</a> (Writing Science Fiction) <br />
      <a target="_blank" href="http://www.jt30.com">The JT30 Page</a> (Amplified Blues Harmonica) <br />
      <a target="_blank" href="http://www.harpamps.com">Harp Amps</a> (Vacuum Tube Amplifiers for Blues) <br />
      <a target="_blank" href="http://www.blogseye.com">Blog&apos;s Eye</a> (PHP coding) <br />
      <a target="_blank" href="http://www.cthreepo.com/bees">Bee Progress Beekeeping Blog</a> (My adventures as a new beekeeper) </p>
  </div>
  <h4>The Feed Ads Plugin is installed and working correctly.</h4>
  <p>This plugin will insert text randomly at the tail end of rss entries. This can be used for ads, taglines, or links to other pages, blogs or websites. You may select the probability of text being added to any RSS entry, 1=1% or 1 in 100 rss entries will have this text. 100=100% which means all entries will contain the text.</p>
  <form method="post" action="">
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="kpg_insertAds_nonce" value="<?php echo $nonce;?>" />
    <table>
      <tr>
        <td valign="top">Show only one test entry per feed post</td>
        <td valign="top"><input name="onead" type="checkbox" value="Y" <?php if ($onead=='Y') echo 'checked="checked"'?> /></td>
        <td valign="top">Checking this will prevent more than one entry appearing at the end of a feed post. Since text appearing is based on the probability, it is possible that a a single feed entry can have many lines appended to it. Checking this box prevents this from happening.</td>
      </tr>
    </table>
    <p>Fill in the form fields below. There will always be extra areas to add more lines to your RSS. Probablility must be between 1 and 100. If you enter zero, your line will not be added. Please only add valid HTML code in the text area.</p>
    <p>You should always include a &lt;br/&gt; at the beginning of line. The fields are added to the end of an RSS entry with only a space, so it might look funny if you don't have a &lt;br/&gt; or &lt;hr/&gt; or at least a dash at the beginning of the added text.</p>
	<p>Please be careful to enter valid HTML. The plugin does not validate the HTML that you enter, so it is quite easy to break your RSS feed.</p>
    <p>If you are using an RSS reader built into your browser, the browser may not show any changes because it caches the last RSS feed. The plugin is working, but your browser will not show that it is working. Try clearing your cache.</p>
	<div style="clear:both"></div>
    <?php
	for ($j=0;$j<count($prob)+2;$j++) {
?>
    <fieldset style="border:thin black solid;width:80%;">
    <legend style="margin-left:20px;">Line <?php echo $j+1; ?>:</legend>
   <table width="100%"><tr>
    <td>Probability (1-100)</td>
    <td><input name="prob[]" type="text" onkeypress="return numerickey(event);" value="<?php if ($j<count($prob)) {echo $prob[$j];}?>" size="5" maxlength="4" ></td>
    </tr><tr>
    <td valign="top">Text to add:</td>
    <td><textarea name="rline[]" cols="80" rows="4"><?php if ($j<count($rline)) {echo $rline[$j];}?>
</textarea></td>
</tr></table>
    </fieldset>
    <?php
	}
?>
    <p align="center">
      <input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>
  </form>
</div>
<?php
}
function kpg_insertAds_init() {
   add_options_page('Feed Ads', 'Feed Ads', 'manage_options','feed-ads','kpg_insertAds_control');
}
  // Plugin added to Wordpress plugin architecture
	add_action('admin_menu', 'kpg_insertAds_init');	
// uninstall
function kpg_insertAds_uninstall() {
	if(!current_user_can('manage_options')) {
		die('Access Denied');
	}
	delete_option('kpg_insert_ad_options'); 
	return;
}
if ( function_exists('register_uninstall_hook') ) {
	register_uninstall_hook(__FILE__, 'kpg_insertAds_uninstall');
}

// bottom	
?>
