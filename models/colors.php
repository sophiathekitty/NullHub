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
            $colors->SaveColor($id,$default,$pallet);
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
    clsModel::$models[] = new Colors();
}
?>