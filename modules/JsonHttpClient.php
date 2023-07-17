<?php
/**
 * handle JSON http requests
 */
class JsonHttpClient
{
    /**
     * get some json data
     * @param string $url the url to load
     * @param array $headers [optional] any special headers you want to send
     * @param array $data [optional] the data quarry 
     * @return array the json data
     */
    public static function get($url, $headers = [],$data = null)
    {
        //if(!is_null($data)) return JsonHttpClient::request("post",$url,$headers,$data);
        return JsonHttpClient::request("get",$url,$headers,$data);
    }
    /**
     * get some json data
     * @param string $url the url to load
     * @param array $headers [optional] any special headers you want to send
     * @return array the json data
     */
    public static function options($url, $headers = [])
    {
        return JsonHttpClient::request("options",$url,$headers);
    }
    /**
     * post some json data
     * @param string $url the url to post to
     * @param array $data the data array to send
     * @param array $headers [optional] any special headers you want to send
     * @return array|string the json response data
     */
    public static function post($url, $data, $headers = [])
    {
        return JsonHttpClient::request("post",$url,$headers,$data);
    }
    /**
     * put some json data
     * @param string $url the url to put to
     * @param array $data the data array to send
     * @param array $headers [optional] any special headers you want to send
     * @return array|string the json response data
     */
    public static function put($url, $data, $headers = [])
    {
        return JsonHttpClient::request("put",$url,$headers,$data);
    }
    /**
     * delete some json data
     * @param string $url the url to delete from
     * @param array $data the data array to send
     * @param array $headers [optional] any special headers you want to send
     * @return array|string the json response data
     */
    public static function delete($url, $data, $headers = [])
    {
        return JsonHttpClient::request("delete",$url,$headers,$data);
    }
    /**
     * patch some json data
     * @param string $url the url to delete from
     * @param array $data the data array to send
     * @param array $headers [optional] any special headers you want to send
     * @return array|string the json response data
     */
    public static function patch($url, $data, $headers = [])
    {
        return JsonHttpClient::request("patch",$url,$headers,$data);
    }
    /**
     * sync some json data
     * @param string $url the url to sync with
     * @param array $data the data array to sync
     * @param array $headers [optional] any special headers you want to send
     * @return array|string the json response data
     */
    public static function sync($url, $data, $headers = [])
    {
        return JsonHttpClient::request("sync",$url,$headers,$data);
    }
    /**
     * do a request
     * @param string $method the request method to use (get, post, put, delete, patch)
     * @param string $url the url for the request
     * @param array $data the data array to send
     * @param array $headers [optional] any special headers you want to send
     * @return array|string the json response data
     */
    public static function request($method,$url,$headers = [],$data = null){
        if (!is_null($data) && !in_array('Content-Type: application/json', $headers)) $headers[] = 'Content-Type: application/json';
        // setup curl request
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if(strtoupper($method) != "GET"){ // not a get request
            // is post?
            if(strtoupper($method) == "POST") curl_setopt($ch, CURLOPT_POST, true);
            else curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method)); // custom method
        } else {
            // is get... but what if sending post data?
            if(!is_null($data)) curl_setopt($ch, CURLOPT_POST, true);
        }
        if(!is_null($data)) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // do curl request
        $response = curl_exec($ch);
        curl_close($ch);
        // return the json
        $json = json_decode($response, true);
        if(is_null($json)) return $response;
        return $json;
    }
}
?>