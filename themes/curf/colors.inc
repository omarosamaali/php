<?php 
/**********************************************************************
   Theme:Curf 
   Developer:Dinesh

***********************************************************************/
function color_blend_by_opacity( $foreground, $opacity, $background=null )
{
    static $colors_rgb=array(); // stores colour values already passed through the hexdec() functions below.
    $foreground = str_replace('#','',$foreground);
    if( is_null($background) )
        $background = 'FFFFFF'; // default background.

    $pattern = '~^[a-f0-9]{6,6}$~i'; // accept only valid hexadecimal colour values.
    if( !@preg_match($pattern, $foreground)  or  !@preg_match($pattern, $background) )
    {
        trigger_error( "Invalid hexadecimal colour value(s) found", E_USER_WARNING );
        return false;
    }

    $opacity = intval( $opacity ); // validate opacity data/number.
    if( $opacity>100  || $opacity<0 )
    {
        trigger_error( "Opacity percentage error, valid numbers are between 0 - 100", E_USER_WARNING );
        return false;
    }

    if( $opacity==100 )    // $transparency == 0
        return strtoupper( $foreground );
    if( $opacity==0 )    // $transparency == 100
        return strtoupper( $background );
    // calculate $transparency value.
    $transparency = 100-$opacity;

    if( !isset($colors_rgb[$foreground]) )
    { // do this only ONCE per script, for each unique colour.
        $f = array(  'r'=>hexdec($foreground[0].$foreground[1]),
                     'g'=>hexdec($foreground[2].$foreground[3]),
                     'b'=>hexdec($foreground[4].$foreground[5])    );
        $colors_rgb[$foreground] = $f;
    }
    else
    { // if this function is used 100 times in a script, this block is run 99 times.  Efficient.
        $f = $colors_rgb[$foreground];
    }

    if( !isset($colors_rgb[$background]) )
    { // do this only ONCE per script, for each unique colour.
        $b = array(  'r'=>hexdec($background[0].$background[1]),
                     'g'=>hexdec($background[2].$background[3]),
                     'b'=>hexdec($background[4].$background[5])    );
        $colors_rgb[$background] = $b;
    }
    else
    { // if this FUNCTION is used 100 times in a SCRIPT, this block will run 99 times.  Efficient.
        $b = $colors_rgb[$background];
    }

    $add = array(    'r'=>( $b['r']-$f['r'] ) / 100,
                     'g'=>( $b['g']-$f['g'] ) / 100,
                     'b'=>( $b['b']-$f['b'] ) / 100    );

    $f['r'] += intval( $add['r'] * $transparency );
    $f['g'] += intval( $add['g'] * $transparency );
    $f['b'] += intval( $add['b'] * $transparency );

    return sprintf( '%02X%02X%02X', $f['r'], $f['g'], $f['b'] );
}
    //#31708F -green
    //#2b7a77 -green
    //#2b307a -blue
    //#C30891 -pink
    //#B5AF0E -yellow
    //#B7112B -red
    //#A609B8 -violet
if(!isset($theme_options['color_scheme'])){
    $theme_options['color_scheme'] ='#2b307a';
}
if(!isset($theme_options['theme_mode'])){
    $theme_options['theme_mode'] ='light';
}
?>
<style type="text/css">
:root{	

	--primary-color:<?php echo $theme_options['color_scheme']?>;
	--border-color:<?php echo $theme_options['color_scheme']?>;
    --hover-color:<?php echo '#'.color_blend_by_opacity($theme_options['color_scheme'],20)?>;
<?php if($theme_options['theme_mode'] =='dark'){?>
    --hover-color:#474747;
    --text-primary:#d5d5de;
    --text-secondary:#000;
    --bg-primary:#121212;
    --bg-secondary:#2e2e2e;
    --shadow-color:#fff;
<?php }else{?>
    --text-primary:#181c58;
    --text-secondary:#fff;
    --bg-primary:#fff;
    --bg-secondary:#f2f3f7;
    --shadow-color:#000;
<?php }?>
    
}

<?php if(isset($theme_options['font_api_css']) && $theme_options['font_api_css']){?>
    body{
        <?php $font_api_css =$theme_options['font_api_css'] ; 
        echo htmlspecialchars_decode($font_api_css,ENT_QUOTES);?>
    }
<?php }else{?>
    body{
       font-family: Poppins, sans-serif;
    }
<?php }?>
</style>