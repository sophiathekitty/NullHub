<?php
/**
 * a generic hourly chart generator. this should be extended to handle loading the hourly data and combined into a final chart. (unless clsModel gets an some better hourly loading options)
 */
class HourlyChart {
    /**
     * calculate the ranges for numeric fields in a chart (will skip non numeric fields if included... why include those? developer proofing....)
     * @param array $chart an array containing an hourly chart
     * @param clsModel $model the model related to the chart
     * @param array $fields a list of fields to generate ranges for
     */
    public static function Ranges($chart,$model){
        return HourlyChart::Range($chart,$model->DataFields());

    }
    /**
     * calculate the ranges for numeric fields in a chart (will skip non numeric fields if included... why include those? developer proofing....)
     * @param array $chart an array containing an hourly chart
     * @param array $fields a list of fields to generate ranges for
     */
    public static function Range($chart,$fields){
        $ranges = [];
        foreach($fields as $field){
            if(is_numeric($chart[0][$field]))
            $ranges[$field] = ['min'=>10000,'max'=>0];
        }
        
        for($i = 0; $i < 24; $i++){
            foreach($fields as $field){
                if(is_numeric($chart[$i][$field])){
                    if($ranges[$field]['min'] > $chart[$i][$field.'_min']) $ranges[$field]['min'] = $chart[$i][$field.'_min'];
                    if($ranges[$field]['max'] < $chart[$i][$field.'_max']) $ranges[$field]['max'] = $chart[$i][$field.'_max'];        
                }
            }
        }
        return $ranges;

    }
    /**
     * calculate the hourly averages for an hour. for numeric fields the average value and min/max range will be found. for string fields the most common occurrence will be found.
     * @param array $data an array of data for the hour
     * @param int $hour the hour being averaged (0-23)
     * @param array $fields a list of fields to average
     * @return array|null the averages for an hour (single hour item in hourly chart) returns null if no data sent
     */
    public function HourlyAverages($data,$hour,$fields){
        // setup averages array
        if($hour < 10){
            $hour = "0".$hour;
        }
        $averages = $this->Averages($data,$fields);
        $averages['hour'] = (string)$hour;
        return $averages;
    }
    /**
     * calculate the hourly averages for a set of data. for numeric fields the average value and min/max range will be found. for string fields the most common occurrence will be found.
     * @param array $data an array of data to be averaged
     * @param array $fields a list of fields to average
     * @return array|null the averages for an hour (single hour item in hourly chart) returns null if no data sent
     */
    public function Averages($data,$fields){
        if(count($data) < 1) return null; // return if there's no data to process
        $averages = [      
            "count" => count($data)
        ];
        foreach($fields as $field){
            if(is_numeric($data[0][$field])){
                // numeric average and range
                $averages[$field."_min"] = 100000;
                $averages[$field."_max"] = 0;
                $averages[$field] = 0;
            } else {
                // figure out which string is most common
                $averages[$field] = [];
            }
        }
        // main data processing
        foreach($data as $h){
            foreach($fields as $field){
                if(is_numeric($h[$field])){
                    // add up value for average and push out min and max values
                    if($h[$field] < $averages[$field."_min"]) $averages[$field."_min"] = (double)$h[$field];
                    if($h[$field] > $averages[$field."_max"]) $averages[$field."_max"] = (double)$h[$field];
                    $averages[$field] += $h[$field];    
                } else {
                    // see if string has occurred before and add to counter if it has
                    if($averages[$field][$h[$field]]){
                        $averages[$field][$h[$field]]++;
                    } else {
                        $averages[$field][$h[$field]] = 1;
                    }
                }
            }    
        }
        // finalize the averages
        foreach($fields as $field){
            if(is_numeric($averages[$field])){
                // actually calculate the average value
                $averages[$field] = round($averages[$field]/$averages['count'],2);
            } else {
                // find the string with the most occurances and store it
                $count = 0;
                $string = "";
                foreach($averages[$field] as $key => $value){
                    if($value > $count){
                        $string = $key;
                        $count = $value;
                    }
                }
                $averages[$field] = $string;
            }
        }
        return $averages;
    }
    /**
     * merge two hourly charts into one (average the hourly data together and figure out the min and max)
     * @param array $dataA the first hourly chart
     * @param array $dataB the second hourly chart
     * @param float $percent the percent of the first field to use. $dataA * $percent + $dataB * (1-$percent)
     * @param array $fields a list of fields to average
     * @return array the average of the two charts
     */
    public function Merge($dataA,$dataB,$fields,$percent=0.5){
        Debug::Log("HourlyChart::Merge",$dataA,$dataB,$fields,$percent);
        $data = [];
        for($h = 0; $h < 24; $h++){
            $data[$h] = ['hour'=>"$h"];
            if($h < 10) $data[$h] = ['hour'=>"0$h"];
            foreach($fields as $field){
                if(isset($dataA[$h][$field]) && isset($dataB[$h][$field]) && is_numeric($dataA[$h][$field]) && is_numeric($dataB[$h][$field])){
                    Debug::Log("HourlyChart::Merge","merge lists");
                    $data[$h][$field] = round(MergeFloats($dataA[$h][$field],$dataB[$h][$field],$percent),2);
                    //$data[$h][$field] = round((($dataA[$h][$field]*$percent)+($dataB[$h][$field]*(1-$percent))),2);
                    //$data[$h][$field] = round(($dataA[$h][$field]+$dataB[$h][$field])/2,2);
                    if($dataA[$h][$field."_max"] > $dataB[$h][$field."_max"]) $data[$h][$field."_max"] = $dataA[$h][$field."_max"];
                    else $data[$h][$field."_max"] = $dataB[$h][$field."_max"];
                    if($dataA[$h][$field."_min"] < $dataB[$h][$field."_min"]) $data[$h][$field."_min"] = $dataA[$h][$field."_min"];
                    else $data[$h][$field."_min"] = $dataB[$h][$field."_min"];
                } else if(isset($dataA[$h][$field]) && is_numeric($dataA[$h][$field])){
                    Debug::Log("HourlyChart::Merge","Use data A");
                    $data[$h][$field] = $dataA[$h][$field];
                    $data[$h][$field."_max"] = $dataA[$h][$field."_max"];
                    $data[$h][$field."_min"] = $dataA[$h][$field."_min"];
                } else if(isset($dataB[$h][$field]) && is_numeric($dataB[$h][$field])){
                    Debug::Log("HourlyChart::Merge","Use data B");
                    $data[$h][$field] = $dataA[$h][$field];
                    $data[$h][$field."_max"] = $dataB[$h][$field."_max"];
                    $data[$h][$field."_min"] = $dataB[$h][$field."_min"];
                }
            }
        }
        return $data;
    }
}
?>