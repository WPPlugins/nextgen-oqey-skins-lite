<?php
require_once(dirname(dirname(dirname(dirname(__FILE__))))."/wp-load.php");
global $wpdb;

$oqey_music = $wpdb->prefix . "oqey_music";
$r          = '';

if( isset($_POST['galleryid']) ){
    
    header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
    $r  .= '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'."\n";
    $r  .= '<songs>'."\n";
    $mus = $wpdb->get_row( "SELECT * FROM $oqey_music WHERE status !=2 ORDER BY id ASC LIMIT 0,1" );
    $r  .= '<song path="'.urlencode(trim($mus->link)).'" artist="" title="'.urlencode(trim($mus->title)).'"></song>'."\n";
    $r  .= '</songs>'."\n";
    
    echo $r;

}else{
    
    die("Access denied. Security check failed! What are you trying to do? It`s not working like that.");
    
}
?>