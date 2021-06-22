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
?>