<?php
class nMapCrawler {
    private static function DoCrawlNetwork(){
        echo "do crawl? ".Settings::LoadSettingsVar('do_crawl_network','auto')."\n";
        switch(Settings::LoadSettingsVar('do_crawl_network','auto')){
            case "yes":
                echo "yes!\n";
                return true;
            case "no":
                echo "no!\n";
                return false;
        }
        echo "auto!\n";
        if(Servers::IsHub()){
            echo "no really this is the hub!\n";
            return true;
        }
        return Servers::IsHub();
    }
    public static function FindHosts(){
        echo "nMapCrawler::FindHosts()\n";
        if(!nMapCrawler::DoCrawlNetwork()) return null;
        echo "Find nMap Hosts\n";
        $ip = LocalIp();
        list($ip_a, $ip_b, $ip_c) = explode(".",$ip);
        $ip_root = "$ip_a.$ip_b.$ip_c.";
        $raw_output = shell_exec("nmap -sP $ip_root*");
        $lines = explode("\n",$raw_output);
        $hosts = array();
        foreach($lines as $line){
            if(strpos($line,$ip_root) !== false){
                $pos = strpos($line,$ip_root);
                $host = substr($line,$pos);
                if(strpos($host,")") !== false){
                    $host = substr($host,0,strpos($host,")"));
                }
                if($host != $ip_root."1"){
                    nMap::SaveHost(['ip'=>$host]);
                    array_push($hosts,nMap::LoadByIp($host));
                }
            }
        }
        return $hosts;
    }
    public static function CheckHosts(){
        if(!nMapCrawler::DoCrawlNetwork()) return nMapCrawler::CheckHub();
        echo "Check nMap Hosts\n";
        $host = nMap::LoadNext();
        if($host['type'] == "pi"){
            $host = nMapCrawler::CheckPi($host);
        } else {
            $host = nMapCrawler::CheckNew($host);
        }
        return nMap::SaveHost($host);
    }
    private static function CheckPi($host){
        $pi = GetRemoteServerInfo($host['ip']);
        if($pi){
            $host['type'] = "pi";
        }
        return $host;
    }
    private static function CheckNew($host){
        $host['type'] = "unknown";
        $host = nMapCrawler::CheckPi($host);
        if(defined('WeMoLightsPlugin')){
            // if the wemo lights plugin
            $host = WeMoSync::CheckWeMoServer($host); // speculative function call.... 
        }
        return $host;
    }
    private static function CheckHub(){
        $hub = Servers::GetHub();
        return nMapCrawler::CheckPi(['type'=>'pi','ip'=>$hub['url']]);
    }
}
function nMapHosts(){
    //global $my_ip;
    $ip = LocalIp();
    list($ip_a, $ip_b, $ip_c) = explode(".",$ip);
    $ip_root = "$ip_a.$ip_b.$ip_c.";
    echo "$ip_root\n";
    $raw_output = shell_exec("nmap -sP $ip_root*");
    $lines = explode("\n",$raw_output);
    $hosts = array();
    //$my_ip = "";
    foreach($lines as $line){
        if(strpos($line,$ip_root) !== false){
            $pos = strpos($line,$ip_root);
            $host = substr($line,$pos);
            if(strpos($host,")") !== false){
                $host = substr($host,0,strpos($host,")"));
            }
            if($host != $ip_root."1"){
                nMap::SaveHost(['ip'=>$host]);
                array_push($hosts,nMap::LoadByIp($host));
            }
        }
    }
    //print_r($hosts);
    return $hosts;
}
?>