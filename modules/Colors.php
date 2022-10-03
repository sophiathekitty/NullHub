<?php
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
    $pallets = ServerRequests::LoadHubJSON("/api/colors/?pallet=1");
    foreach($pallets['pallet'] as $pallet => $colors){
        if(is_array($colors)) {
            foreach($colors as $id => $color){
                if(is_array($color)){
                    if(count($color) == 2){
                        Colors::SetColor($id."_min",$color[0],$pallet);
                        Colors::SetColor($id."_max",$color[1],$pallet);
                    } else {
                        for($i = 0; $i < count($color); $i++){
                            Colors::SetColor($id."_".$i,$color[$i],$pallet);
                        }
                    }
                } else {
                    Colors::SetColor($id,$color,$pallet);
                }
            }
        } else {
            Colors::SetColor($pallet,$colors);
        }
    }
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

?>