<?php

class Colors extends clsModel {
    public $table_name = "Colors";
    public $fields = [
        [
            'Field'=>"id",
            'Type'=>"varchar(20)",
            'Null'=>"NO",
            'Key'=>"PRI",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"color",
            'Type'=>"varchar(7)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"pallet",
            'Type'=>"varchar(10)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"general",
            'Extra'=>""
        ]
    ];

    public static $colors;
    public static function GetInstance(){
        if(is_null(Colors::$colors)) Colors::$colors = new Colors();
        return Colors::$colors;
    }
    public static function GetColor($id,$pallet = "general",$default = null){
        $colors = Colors::GetInstance();
        $color = $colors->LoadColor($id);
        if(is_null($color) && !is_null($default)){
            $colors->Save(['id'=>$id,'color'=>$default,'pallet'=>$pallet]);
            return $default;
        }
        return $color['color'];
    }

    public function SaveColor($id,$color,$pallet = "general"){
        return $this->Save(['id'=>$id,'color'=>$color,'pallet'=>$color],['id'=>$id]);
    }
    public function LoadColor($id){
        return $this->LoadById($id);
    }
    public function LoadPallet($pallet){
        return $this->LoadAllWhere(['pallet'=>$pallet]);
    }
    public function PalletsList(){
        $rows = clsDB::$db_g->select("SELECT DISTINCT `pallet` FROM `".$this->table_name."`");
        $pallets = [];
        foreach($rows as $row){
            $pallets[] = $row['pallet'];
        }
        return $pallets;
    }
}
if(defined('VALIDATE_TABLES')){
    $color_validate = new Colors();
    clsModel::$models[] = $color_validate;
    function ValidateColors(){
        global $root_path;
        Colors::GetColor("background","general","#2f4f4f");
        Colors::GetColor("text","general","#cad7e4");
        Colors::GetColor("link","general","#d3d3d3");
        Colors::GetColor("null","general","#98ed8a");
        Colors::GetColor("highlight","general","#ff8c00");

        Colors::GetColor("Sun","calendar","#6ca9c9");
        Colors::GetColor("Mon","calendar","#6ac46a");
        Colors::GetColor("Tue","calendar","#d1c173");
        Colors::GetColor("Wed","calendar","#d879b1");
        Colors::GetColor("Thu","calendar","#947bd8");
        Colors::GetColor("Fri","calendar","#82e587");
        Colors::GetColor("Sat","calendar","#ed8787");

        Colors::GetColor("month_1","calendar","#9933cc");
        Colors::GetColor("month_2","calendar","#990000");
        Colors::GetColor("month_3","calendar","#990099");
        Colors::GetColor("month_4","calendar","#009900");
        Colors::GetColor("month_5","calendar","#0099ff");
        Colors::GetColor("month_6","calendar","#669900");
        Colors::GetColor("month_7","calendar","#ff6600");
        Colors::GetColor("month_8","calendar","#ff3300");
        Colors::GetColor("month_9","calendar","#cc3399");
        Colors::GetColor("month_10","calendar","#ff6600");
        Colors::GetColor("month_11","calendar","#cc6633");
        Colors::GetColor("month_12","calendar","#9999ff");

        // find colors to validate in plugins
        $plugins = FindPlugins($root_path."plugins/");
        foreach($plugins as $plugin){
            if(is_file($plugin."colors.php")){
                require_once($plugin."colors.php");
            }
        }
    }
}
?>