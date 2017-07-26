<?php
// NextGen Oqey Skins Lite
// Copyright (c) 2013 oqeysites.com
// This is an add-on for WordPress
// http://wordpress.org/
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
// *****************************************************************

/*
Plugin Name: NextGen Oqey Skins Lite
Version: 0.3
Description: NextGen Oqey Skins Lite is an add-on for oQey Gallery plugin that allow to use oQey skin for NextGen gallery.
Author: oqeysites.com
Author URI: http://oqeysites.com/
*/
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'nggoqeyskinslite.php' == basename($_SERVER['SCRIPT_FILENAME'])) die ('Please do not load this page directly. Thanks!');

//global $oqeycounter;

if (!defined('OQEY_ABSPATH')) {

    define('OQEY_ABSPATH', str_replace('\\', '/', ABSPATH) ); //oqey path

}

require_once(OQEY_ABSPATH . 'wp-admin/includes/plugin.php');

function oqey_gallery_required(){
   
      echo '<div class="error fade" style="background-color:#E36464;">
            <p>'.__( 'NextGen Oqey Skins Lite requires latest version of oQey Gallery plugin activated. Please install and activate this plugin.', 'oqey-gallery' ).'
            </p></div>';
}

if(!is_plugin_active('oqey-gallery/oqeygallery.php')){
   add_action( 'admin_notices', 'oqey_gallery_required');
}

/*Functions*/
function oqey_getBFolder($id){ $folder=""; if($id==0 || $id==1){ $folder = "";}else{ $folder=$id."/"; } return $folder; }

function NgggetUserNow($userAgent) {
    $crawlers = 'Google|msnbot|Rambler|Yahoo|AbachoBOT|accoona|' .
    'AcioRobot|ASPSeek|CocoCrawler|Dumbot|FAST-WebCrawler|' .
    'GeonaBot|Gigabot|Lycos|MSRBOT|Scooter|AltaVista|IDBot|eStyle|Scrubby|yandex|facebook';
    $isCrawler = (preg_match("/$crawlers/i", $userAgent) > 0);
    return $isCrawler;
}
/**/

function read_next_tags_and_process_them($content) {
            
$tags = 'slideshow';
remove_shortcode( $tags );

$tagn = 'nggallery';
remove_shortcode( $tagn );

add_shortcode( 'slideshow', 'NgAddoQeyGallery' );
add_shortcode( 'nggallery', 'NgAddoQeyGallery' );

return $content;

}

add_filter('the_content', 'read_next_tags_and_process_them', 1);

function NgAddoQeyGallery($atts){
   global $oqeycounter, $wpdb, $post;
   
   wp_enqueue_script('cycle2', WP_PLUGIN_URL . '/oqey-gallery/js/jquery.cycle2.min.js', array('jquery'),'', true);
   wp_enqueue_script('cycle2.swipe', WP_PLUGIN_URL . '/oqey-gallery/js/jquery.cycle2.swipe.min.js', array('cycle2'),'', true);
   wp_enqueue_script('oqeyjs', WP_PLUGIN_URL . '/oqey-gallery/js/oqey.js', array('jquery'),'', true);
   
   $oqey_galls  = $wpdb->prefix . "ngg_gallery";
   $oqey_images = $wpdb->prefix . "ngg_pictures";
   $oqey_skins  = $wpdb->prefix . "oqey_skins";
     
   if (is_feed()) {

     //return AddoQeyGalleryToFeed($atts);

   }else{

   $id               = absint($atts['id']);   
   //$oqey_BorderSize  = get_option('oqey_BorderSize');
   $oqey_bgcolor     = get_option('oqey_bgcolor');
   $plugin_url_qu    = site_url() . '/wp-content/plugins/nextgen-oqey-skins-lite';
   $plugin_repo_url  = site_url() . '/wp-content/oqey_gallery';
   $imgs             = '';
   $nobject          = '';
   $object           = '';
   $incolums         = '';
   $oqeyblogid       = '';
   $arrowleftright   = "";
   $arrowshtml       = "";
   $arrows           = "on";
   $wdetails         = '';
   
   $skinoptionsrecorded = "false";
   
   if(isset( $atts['width'] ) ){    
       
       $oqey_width = $atts['width']; 
       
   }else{   
       
       $wdetails   = (get_option('oqey_width_details')=='pr')?'%':'';
       $oqey_width = get_option('oqey_width').$wdetails; 
       
   }
   if(isset( $atts['height'] ) ){   $oqey_height   = $atts['height']; }else{    $oqey_height   = get_option('oqey_height'); }
   if(isset( $atts['autoplay'] ) ){ $oqey_autoplay = $atts['autoplay']; }else{  $oqey_autoplay = "false"; }
   if(isset( $atts['arrows'] ) ){   $arrows        = $atts['arrows']; }else{    $arrows        = 'on'; }
  
   $gal       = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $oqey_galls WHERE gid = %d ", $id ) );
   $nggpath   = $gal->path;
   $gal_title = urlencode($gal->title);
   
   if($gal){

      /*get default skin*/         
      $skin      = $wpdb->get_row("SELECT * FROM $oqey_skins WHERE status = '1'"); 
      $options   = "oqey_skin_options_".$skin->folder; 
      $all       = json_decode(get_option($options));
         
      if(!empty($all)){
            
            $skinoptionsrecorded = "true";
        
      }      
      
      $link = OQEY_ABSPATH . 'wp-content/oqey_gallery/skins/'.oqey_getBFolder($wpdb->blogid).$skin->folder.'/'.$skin->folder.'.swf';
      
      if(!is_file($link)){
        
         $skin    = $wpdb->get_row("SELECT * FROM $oqey_skins WHERE status != '2' LIMIT 0,1"); 
         $options = "oqey_skin_options_".$skin->folder; 
         $all     = json_decode(get_option($options));
         
         if(!empty($all)){
            
            $skinoptionsrecorded = "true";
         
         }         
      }

      
      $allimgs   = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $oqey_images WHERE galleryid = %d ORDER BY sortorder ASC", $id  ));
      $isCrawler = NgggetUserNow($_SERVER['HTTP_USER_AGENT']); // check if is a crawler
      
       
      //print_r($all);
      
      if ($isCrawler){    
           
        $imgs = "<p align='center'>".urldecode($gal_title)."</p>";
        $gimg = get_option('siteurl').'/'.trim($nggpath).'/';
         
        foreach($allimgs as $i){ 
          
             $imgs .= '<p style="margin-left:auto; margin-right:auto;display:block;text-align:center;">
                         <img src="'.$gimg.trim($i->filename).'" alt="Photo '.urldecode(trim($i->alttext)).'" style="margin:1px auto;height:auto;max-width:100%;"/>
                       </p>'; 
             
             if(get_option('oqey_show_captions_under_photos')=="on"){
                
		$comments = '';
		
		if(!empty($i->comments)){
		   $comments = ' | '.trim(urldecode($i->comments));
	        }
			       
                $imgs .= '<p class="oqey_p_comments">'.trim(urldecode($i->alt)).$comments."</p>";
                
             }
          
           }
           
        
        
        return $imgs; 
        
    }else{	
	
        $galtitle = "";
        
	if(get_option('oqey_gall_title_no')=="on"){
	   
   	    $galtitle = '<div style="margin:0 auto;width:100%;text-align:center;">'.urldecode($gal_title).'</div>';
                   
        }else{
            
            $galtitle = "";
           
        }

    if( get_option('oqey_noflash_options')=="incolums" ){
        
        $top_margin = 'margin-top:3px;';
        
    }else{
        
        $top_margin = '';
        
    }
    
    /* Custom words - set arrows ON/OFF */ 
    if($arrows=="off"){
        
        $arrowleftright = "";
        $arrowshtml     = "";
    
    }else{
        
        $arrowleftright = 'data-cycle-prev=".prevControl'.$oqeycounter.'"
                           data-cycle-next=".nextControl'.$oqeycounter.'"'; 
        $arrowshtml    .= '<span class=center><span class="prevControl prevControl'.$oqeycounter.'"></span><span class="nextControl nextControl'.$oqeycounter.'"></span></span>';
    } 
    
    if( get_option('oqey_noflash_options')=="injsarr" ){ 
        
        $optouch  = ""; 	
	
    }
	
    if( get_option('oqey_noflash_options')=="injsarrtouch" ){ 

        $optouch  = "data-cycle-swipe=true";             
    
    }
    
    if( get_option('oqey_noflash_options')=="incolums" ){  
	   
	   $incolums = "on";
           $optouch  = "off"; 
        }
        
    if(get_option('oqey_flash_gallery_true')){ 
            
            $pfv = "on"; 
                        
    }else{
            
            $oqey_skins = $wpdb->prefix . "oqey_skins";
            $r          = $wpdb->get_results( "SELECT skinid FROM $oqey_skins WHERE status !='2'");  
        
            if(empty($r)){
                $pfv = "on"; 
            }else{     
                 $pfv = "off"; 
            }
    }
    
    /*Border details*/
    $Border          = get_option('oqey_BorderOption');
    $oqeybgcss       = '';
    
    if($Border){
        
        //$oqeyBorderColor = get_option('oqey_BorderSize');
        $oqeyBorderColor = get_option('oqey_border_bgcolor');
        $oqeybgcss       = 'border:thin solid '.$oqeyBorderColor.';';
        
    }    
    /*END border*/
    
     /*Autostart details*/
    $autostart       = '';
    
    if(get_option('oqey_AutostartOption')){

        $autostart = 'data-cycle-manual-speed="'.(get_option('oqey_effects_trans_time')*1000).'"
                      data-cycle-timeout='.(get_option('oqey_pause_between_tran')*1000);
        
    }else{
        
        $autostart = 'data-cycle-manual-speed="'.(get_option('oqey_effects_trans_time')*1000).'"
                      data-cycle-timeout=0';
    }  
    /*END autostart*/
    
    /*Effect transition type Face or Slide*/
    $effecttr = '';
    if(get_option('oqey_effect_transition_type')=='slide'){

        $effecttr = 'data-cycle-fx="scrollHorz"';
        
    }
    /*END effect transition*/
    
    $nobject .= '<div class="oqeyslider" style="background:'.$oqey_bgcolor.';'.$oqeybgcss.'">';
    $nobject .= '<div class="oqey-slideshow cycle-slideshow'.$oqeycounter.'" ';
    
    if($incolums!="on"){
      
      $nobject .='data-cycle-loader=true
                  data-cycle-progressive="#slides"
                  '.$autostart.'
                  '.$optouch.'
                  '.$arrowleftright.'
                  '.$effecttr.'
                  data-cycle-slides=">div,>img"';
    }
    
    $nobject .= '>';
    $nobject .= $arrowshtml;
    $d        = 0;
    $nobject2 = "";
    
    $gimg     = get_option('siteurl').'/'.trim($nggpath).'/'; 

	
	foreach($allimgs as $i){ 
            
                  if($d<1 || get_option('oqey_noflash_options')=="incolums"){
                      $nobject .= '<div style="margin:0 auto 5px auto;"><img src="'.$gimg.trim($i->filename).'" alt="'.urldecode(trim($i->alttext)).'"/></div>'."\n";
                  }else{
                      $nobject2 .='<div><img src="'.$gimg.trim($i->filename).'" title="'.urldecode(trim($i->alttext)).'" style="max-width:100%;"/></div> --- '."\n";
                  }
                                                                 
                  if(get_option('oqey_show_captions_under_photos')=="on" && get_option('oqey_noflash_options')=="incolums" ){
                
                      $nobject .= '<p class="oqey_p_comments">'.trim($i->comments)."</p>";
                
                  }
       
                  $d++;
    }
    
    $nobject  .= '</div>';
    
    if(get_option('oqey_noflash_options')!="incolums"){
    $nobject .='<script id="slides" type="text/cycle" data-cycle-split="---">'."\n";
    $nobject .= $nobject2; 
    $nobject .='</script>'."\n";
    }
    
    $nobject .= '</div>';
    
    $backlink = "";  
    
	if(get_option("oqey_backlinks")=="on"){ 
            
            $backlink = '<div style="text-align:center;margin:0 auto;width:50%;">Created with <a href="http://oqeysites.com" target="_blank">oQey Gallery</a></div>'; 
            
        }
    
  
   $oqeyblogid = oqey_getBlogFolder($wpdb->blogid); 

   if($pfv!="on"){ // if Do not use Flash skins is active
       
       $object .= '<script type="text/javascript">'."\n";
       $object .= 'var flashvars'.$oqeycounter.' ={'."\n";
       $object .= 'autoplay:"'.$oqey_autoplay.'",'."\n";
       $object .= 'flashId:"'.$oqeycounter.'",'."\n";
       $object .= 'FKey:"'.trim($skin->comkey).'",'."\n";
       $object .= 'GalleryPath:"'.$plugin_url_qu.'",'."\n";
       $object .= 'GalleryID:"'.$id.'-'.$post->ID.'",'."\n";
       $object .= 'FirstRun:"'.trim($skin->firstrun).'"'."\n";
       $object .= '};'."\n";
       $object .= 'var params'.$oqeycounter.'     = {bgcolor:"'.$oqey_bgcolor.'", allowFullScreen:"true", wMode:"transparent"};'."\n";
       $object .= 'var attributes'.$oqeycounter.' = {id: "oqeygallery'.$oqeycounter.'"};'."\n";
       $object .= 'swfobject.embedSWF("'.$plugin_repo_url.'/skins/'.$oqeyblogid.trim($skin->folder).'/'.trim($skin->folder).'.swf", "flash_gal_'.$oqeycounter.'", "'.$oqey_width.'", "'.$oqey_height.'", "8.0.0", "", flashvars'.$oqeycounter.', params'.$oqeycounter.', attributes'.$oqeycounter.');'."\n";
       $object .= '</script>'."\n";

   }

$object .= $galtitle;
$object .= '<div id="flash_gal_'.$oqeycounter.'" style="margin: 0 auto;">'."\n";
$object .= $nobject."\n";
$object .= '</div>'."\n";
$object .= $backlink;

if($incolums!="on"){
  $object .= '<script type="text/javascript">'."\n";
  $object .= 'jQuery(document).ready(function(){'."\n";
  $object .= 'jQuery(".cycle-slideshow'.$oqeycounter.'").css("min-height", (jQuery(".cycle-slideshow'.$oqeycounter.'").width()/1.5));';
  $object .= 'jQuery(".cycle-slideshow'.$oqeycounter.' div img").css("max-height", (jQuery(".cycle-slideshow'.$oqeycounter.'").width()/1.5));';
  $object .= 'jQuery(".cycle-slideshow'.$oqeycounter.'").cycle();'."\n";
  $object .= '});'."\n";
  $object .= 'jQuery(".cycle-slideshow'.$oqeycounter.'").on("cycle-before", function( event, opts ) {'."\n";
  $object .= 'jQuery(".cycle-slideshow'.$oqeycounter.' div img").css("max-height", (jQuery(".cycle-slideshow'.$oqeycounter.'").width()/1.5));';
  $object .= '});'."\n";
  $object .= '</script>'."\n"; 
}

$oqeycounter ++;
return $object;
}
}
}
}
?>