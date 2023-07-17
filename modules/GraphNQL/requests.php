<?php
/**
 * client for sending queries.
 */
class GraphClient {
    /**
     * send a get query to the hub
     * @param array $query the query array
     */
    public static function GetFromHub($query){
        return JsonHttpClient::post(GraphClient::GraphURL(Servers::GetHub()),$query);
    }
    /**
     * send a put query to the hub
     * @param array $query the query array
     */
    public static function PutToHub($query){
        return JsonHttpClient::put(GraphClient::GraphURL(Servers::GetHub()),$query);
    }
    /**
     * send a delete query to the hub
     * @param array $query the query array
     */
    public static function DeleteOnHub($query){
        return JsonHttpClient::delete(GraphClient::GraphURL(Servers::GetHub()),$query);
    }
    /**
     * send a sync query to the hub
     * @param array $query the query array
     */
    public static function SyncWithHub($query){
        return JsonHttpClient::sync(GraphClient::GraphURL(Servers::GetHub()),$query);
    }
    /**
     * send a get query to a server
     * @param array $server the server data array
     * @param array $query the query array
     */
    public static function Get($server,$query){
        return JsonHttpClient::post(GraphClient::GraphURL($server),$query);
    }
    /**
     * send a put query to a server
     * @param array $server the server data array
     * @param array $query the query array
     */
    public static function Put($server,$query){
        return JsonHttpClient::put(GraphClient::GraphURL($server),$query);
    }
    /**
     * send a delete query to a server
     * @param array $server the server data array
     * @param array $query the query array
     */
    public static function Delete($server,$query){
        return JsonHttpClient::delete(GraphClient::GraphURL($server),$query);
    }
    /**
     * send a sync query to a server
     * @param array $server the server data array
     * @param array $query the query array
     */
    public static function Sync($server,$query){
        return JsonHttpClient::sync(GraphClient::GraphURL($server),$query);
    }
    /**
     * get the graph url for a server
     * @param array $server the server data array
     * @param array $query the query array
     */
    private static function GraphURL($server){
        return "http://".$server['url']."/GraphNQL/";
    }
}
?>