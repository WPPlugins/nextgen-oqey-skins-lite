<?php
require_once(dirname(dirname(dirname(dirname(__FILE__))))."/wp-load.php");
global $wpdb;  

if(isset($_POST['gal_id'])){

   $oqey_galls  = $wpdb->prefix . "ngg_gallery";
   $oqey_images = $wpdb->prefix . "ngg_pictures";
   $r           = '';
   $bgimage     = '';

   $data    = explode("-", $_POST['gal_id']);
   $id      = absint( $data[0] );
   $s       = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $oqey_galls WHERE gid = %d ", $id ) );
   $nggpath = trim($s->path);

   $bg      = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $oqey_images WHERE galleryid = %d ORDER BY sortorder ASC LIMIT 0,1", $id ) );
   $bgimage = get_option('siteurl').'/'.$nggpath.'/'.trim($bg->filename); //splash image
   
   $imgs    = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $oqey_images WHERE galleryid = %d ORDER BY sortorder ASC", $id ) );         
   $gthmb   = get_option('siteurl').'/'.$nggpath.'/thumbs/thumbs_';
   $gimg    = get_option('siteurl').'/'.$nggpath.'/';   

   header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
   $r .= '<?xml version="1.0" encoding="UTF-8"?>'."\n";
   $r .= '<oqeygallery bgpath="'.$bgimage.'" galtitle="'.urlencode($s->title).'" path="" imgPath="">'."\n"; 
    
   foreach($imgs as $i) { 

       $r .= '<item>'."\n";
       $r .= '<thumb file="'.$gthmb.trim($i->filename).'" alt="'.urlencode(trim($i->alttext)).'" comments="'.urlencode(trim($i->description)).'" link=""/>'."\n";
       $r .= '<image file="'.$gimg.trim($i->filename).'" alt="'.urlencode(trim($i->alttext)).'" comments="'.urlencode(trim($i->description)).'" link="">'."\n";    
       $r .= '</image>'."\n";
       $r .= '</item>'."\n";
       
 }

   $r .= '</oqeygallery>'."\n";
   
   echo $r;
  
}else{ 
    
   die("Access denied. Security check failed! What are you trying to do? It`s not working like that. ");
    
}
?>