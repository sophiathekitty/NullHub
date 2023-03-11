<?php
/**
 * set color on main hub
 */
function SetHubColor($id,$color,$pallet){
    Colors::SetColor($id,$color,$pallet);
    $res = ServerRequests::LoadHubJSON("/api/colors/?id=$id&color=".urlencode($color));
    Debug::Log("SetHubColor",$id,$color,$pallet,$res);
}
/**
 * color pallet generator
 * @param string $base_hex_color the starting color in hex #ff00ff
 * @param int $pallet_size the number of colors to generate
 * @param float $color_wheel_percent the percentage of the hue color wheel to use 1 is 100%
 * @return array the list of hex colors
 */
function GenerateColorPallet($base_hex_color,$pallet_size,$color_wheel_percent = 1){
    $hue_step = ($color_wheel_percent)/($pallet_size+1);
    list($h,$s,$l) = hexToHsl($base_hex_color);
    Debug::Log("GenerateColorPallet",['h'=>$h,'s'=>$s,'l'=>$l]);
    $pallet = [$base_hex_color];
    for($i = 1; $i < $pallet_size; $i++){
        $h += $hue_step;
        if($h > 1) $h -= 1;
        if($h < 0) $h += 1;
        $pallet[] = hslToHex([$h,$s,$l]);
    }
    return $pallet;
}
/**
 * gets an organized pallet of colors
 * @return array json array of color pallets
 */
function FullColorPallet(){
    $pallet = [];
    $colors = new Colors();
    $pallets = $colors->PalletsList();
    foreach($pallets as $p){
        $pallet[$p] = [];
        $pal = $colors->LoadPallet($p);
        foreach($pal as $c){
            if(strpos($c['id'],"_") > 0){
                list($key, $index) = explode("_",$c['id']);
                if($index == "max") $index = "1";
                if($index == "min") $index = "0";
                if(isset($pallet[$p][$key])){
                    $pallet[$p][$key][$index] = $c['color'];
                } else {
                    $pallet[$p][$key] = [];
                    $pallet[$p][$key][$index] = $c['color'];
                }
            } else {
                $pallet[$p][$c['id']] = $c['color'];
            }
        }
    }
    return $pallet;
}
/**
 * a parsed color pallet
 * @param string $p the name of the pallet
 * @return array a json array for a color pallet
 */
function ColorPalletStamp($p){
    $pallet = [];
    $colors = new Colors();
    $pal = $colors->LoadPallet($p);
    foreach($pal as $c){
        if(strpos($c['id'],"_") > 0){
            list($key, $index) = explode("_",$c['id']);
            if($index == "max") $index = "1";
            if($index == "min") $index = "0";
            if(isset($pallet[$key])){
                $pallet[$key][$index] = $c['color'];
            } else {
                $pallet[$key] = [];
                $pallet[$key][$index] = $c['color'];
            }
        } else {
            $pallet[$c['id']] = $c['color'];
        }
    }
    return $pallet;
}
/**
 * sync colors from the hub
 */
function SyncColorPallets(){
    if(Servers::IsHub()) return;
    Services::Start("SyncColorPallets");
    $pallets = ServerRequests::LoadHubJSON("/api/colors/?pallet=1");
    Services::Log("SyncColorPallets","pallets: ".count($pallets));
    
    foreach($pallets['pallet'] as $pallet => $colors){
        Services::Log("SyncColorPallets","pallet: $pallet");
        if(is_array($colors)) {
            foreach($colors as $id => $color){
                if(is_array($color)){
                    if(count($color) == 2){
                        Colors::SetColor($id."_min",$color[0],$pallet);
                        Colors::SetColor($id."_max",$color[1],$pallet);
                        //Services::Log("SyncColorPallets","color: ".$id."_min : ".$color[0]);
                        //Services::Log("SyncColorPallets","color: ".$id."_max : ".$color[1]);
                    } else {
                        for($i = 0; $i < count($color); $i++){
                            Colors::SetColor($id."_".$i,$color[$i],$pallet);
                            //Services::Log("SyncColorPallets","color: ".$id."_$i : ".$color[$i]);
                        }
                    }
                } else {
                    //Services::Log("SyncColorPallets","color: $id : $color");
                    Colors::SetColor($id,$color,$pallet);
                }
            }
        } else {
            Colors::SetColor($pallet,$colors);
        }
    }
    Services::Complete("SyncColorPallets");
}
/**
 * lerp between two colors
 * @param string $corA the first hex color 
 * @param string $corB the second hex color
 * @param float $lerp the percentage (0.45) between the first and second color
 */
function interpolateColor($corA, $corB, $lerp)
{
    $corA = hexdec($corA);
    $corB = hexdec($corB);
    $rgbA = array(
        ($corA & 0xFF0000) >> 16,
        ($corA & 0x00FF00) >> 8,
        ($corA & 0x0000FF)
    );
    $rgbB = array(
        ($corB & 0xFF0000) >> 16,
        ($corB & 0x00FF00) >> 8,
        ($corB & 0x0000FF)
    );
    $rgbC = array_map(
        function($a,$b) use ($lerp) {return round($a+($b-$a)*$lerp);},
        $rgbA, $rgbB
    );
    return sprintf("#%02X%02X%02X",$rgbC[0],$rgbC[1],$rgbC[2]);
}

/**
 * convert hex string to hsl array
 * @source https://gist.github.com/bedeabza/10463089
 * @param string $hex the hex color string
 */
function hexToHsl($hex) {
    $hex = str_replace("#","",$hex);
    if(strlen($hex) == 8) $hex = array($hex[0].$hex[1], $hex[2].$hex[3], $hex[4].$hex[5], $hex[4].$hex[7]);
    if(strlen($hex) == 6) $hex = array($hex[0].$hex[1], $hex[2].$hex[3], $hex[4].$hex[5]);
    if(strlen($hex) == 4) $hex = array($hex[0], $hex[1], $hex[2], $hex[3]);
    if(strlen($hex) == 3) $hex = array($hex[0], $hex[1], $hex[2]);
    if(is_string($hex)) return null;
    $rgb = array_map(function($part) {
        return hexdec($part) / 255;
    }, $hex);
    $max = max($rgb);
    $min = min($rgb);
    $l = ($max + $min) / 2;
    if ($max == $min) {
        $h = $s = 0;
    } else {
        $diff = $max - $min;
        $s = $l > 0.5 ? $diff / (2 - $max - $min) : $diff / ($max + $min);
        switch($max) {
            case $rgb[0]:
                $h = ($rgb[1] - $rgb[2]) / $diff + ($rgb[1] < $rgb[2] ? 6 : 0);
                break;
            case $rgb[1]:
                $h = ($rgb[2] - $rgb[0]) / $diff + 2;
                break;
            case $rgb[2]:
                $h = ($rgb[0] - $rgb[1]) / $diff + 4;
                break;
        }
        $h /= 6;
    }
    return array($h, $s, $l);
}
/**
 * convert hsl array to hex string
 * @source https://gist.github.com/bedeabza/10463089
 * @param array $hsl the hsl array
 */
function hslToHex($hsl)
{
    // somebody said this fixes an issue with the original in the comments of the gist
    list($h, $s, $l) = $hsl;
	if ($s == 0) $s = 0.000001;
		
	$q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
	$p = 2 * $l - $q;

	$r = hue2rgb($p, $q, $h + 1/3);
	$g = hue2rgb($p, $q, $h);
	$b = hue2rgb($p, $q, $h - 1/3);

	return "#" . rgb2hex($r) . rgb2hex($g) . rgb2hex($b);
    // original?
    list($h, $s, $l) = $hsl;

    if ($s == 0) {
        $r = $g = $b = 1;
    } else {
        $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
        $p = 2 * $l - $q;

        $r = hue2rgb($p, $q, $h + 1/3);
        $g = hue2rgb($p, $q, $h);
        $b = hue2rgb($p, $q, $h - 1/3);
    }

    return rgb2hex($r) . rgb2hex($g) . rgb2hex($b);
}
/**
 * convert hue to rgb (used by hslToHex)
 * @source https://gist.github.com/bedeabza/10463089
 */
function hue2rgb($p, $q, $t) {
    if ($t < 0) $t += 1;
    if ($t > 1) $t -= 1;
    if ($t < 1/6) return $p + ($q - $p) * 6 * $t;
    if ($t < 1/2) return $q;
    if ($t < 2/3) return $p + ($q - $p) * (2/3 - $t) * 6;

    return $p;
}
/**
 * convert rgb to hex (used by hslToHex)
 * @source https://gist.github.com/bedeabza/10463089
 */
function rgb2hex($rgb) {
    return str_pad(dechex($rgb * 255), 2, '0', STR_PAD_LEFT);
}
?>