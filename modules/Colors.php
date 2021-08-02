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
?>