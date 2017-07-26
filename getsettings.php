<?php
require_once(dirname(dirname(dirname(dirname(__FILE__))))."/wp-load.php");

/*Functions*/
function Ngg_oqey_makeFlashColor($csscolor){	
  $color = preg_replace('/#/i', '0x', $csscolor);
  return $color;
}

function Ngg_get_oqey_domain($url){    
  $pieces = parse_url($url);  
  $domain = isset($pieces['host']) ? $pieces['host'] : '';
  if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,7})$/i', $domain, $regs)) {
    return $regs['domain'];
  }  
  return false;
}

/*End functions*/

  if(get_option('oqey_crop_images')=="on"){ $crop       = "true"; }else{ $crop       = "false"; }
  if(get_option('oqey_HideThumbs') =="on"){ $HideThumbs = "true"; }else{ $HideThumbs = "false"; }
  if(get_option('oqey_LoopOption') =="on"){ $LoopOption = "true"; }else{ $LoopOption = "false"; }

  $maxw    = get_option('oqey_width');
  $maxh    = get_option('oqey_height');
  $r       = '';

if(get_option('oqey_limitmax')=="on"){
    
   if(get_option('oqey_max_width') !=""){  $maxw = get_option('oqey_max_width'); }
   if(get_option('oqey_max_height')!=""){  $maxh = get_option('oqey_max_height'); }
   $r .= 'MaximumWidth='.$maxw.'&MaximumHeight='.$maxh.'&';

}else{ 
    
   $r .= ''; 

}

  if(get_option('oqey_BorderOption')   =="on"){ $BorderOption    = "true"; }else{ $BorderOption    = "false"; }
  if(get_option('oqey_AutostartOption')=="on"){ $AutostartOption = "true"; }else{ $AutostartOption = "false"; }

if(get_option('oqey_CaptionsOption')=="on"){
  
  $CaptionsOption  = "true";
  $CaptionPosition = get_option('oqey_options');
  $r              .= 'CaptionsOption='.$CaptionsOption.'&CaptionPosition='.$CaptionPosition.'&';

}else{ 

  $CaptionsOption  = "false";
  $r              .= 'CaptionsOption='.$CaptionsOption.'&'; 

}

$r .= 'GalleryWidth='.get_option('oqey_width');
$r .= '&GalleryHeight='.get_option('oqey_height');
$r .= '&CropOption='.$crop;
$r .= '&ThumbWidth='.get_option('oqey_thumb_width');
$r .= '&ThumbHeight='.get_option('oqey_thumb_height');
$r .= '&TransitionTime='.get_option('oqey_effects_trans_time');
$r .= '&TransitionInterval='.get_option('oqey_pause_between_tran');
$r .= '&HideThumbs='.$HideThumbs;
$r .= '&LoopOption='.$LoopOption;
$r .= '&BorderOption='.$BorderOption;
$r .= '&BorderColor='.Ngg_oqey_makeFlashColor(get_option('oqey_border_bgcolor'));
$r .= '&BackgroundColor='.Ngg_oqey_makeFlashColor(get_option('oqey_bgcolor'));
$r .= '&AutostartOption='.$AutostartOption;
$r .= '&domain='.Ngg_get_oqey_domain(get_option('siteurl'));
$r .= '&TransitionType='.get_option('oqey_effect_transition_type');
?>