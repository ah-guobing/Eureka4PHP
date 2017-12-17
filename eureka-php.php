<?php
/**
 * Project: Eureka4PHP
 * File: eureka-php.php
 * User: guobingbing
 * Time: 2017/12/15 16:50
 * Desc: PHP版的Eureka客户端
 */


class EurekaApi
{
    private $eurekaServer;
    private $instanceName;
    private $instanceDomain;
    private $instanceIp;
    private $instancePort;
    private $instanceHomepageUrl;
    private $instanceStatusUrl;
    private $instanceHealthCheckUrl;

    public function __construct($config)
    {
        if (!$config['eureka_server'] || !$config['instance_name'] || !$config['instance_domain'] || !$config['instance_ip'] || !$config['instance_port']) {
            throw new Exception(”请检查以下参数是否设置：eureka_server、instance_name、instance_domain、instance_ip、instance_port”);
        }
        $this->eurekaServer = $config['eureka_server'];
        $this->instanceName = $config['instance_name'];
        $this->instanceDomain = $config['instance_domain'];
        $this->instanceIp = $config['instance_ip'];
        $this->instancePort = $config['instance_port'];
        $this->instanceHomepageUrl = $config['instance_homepage_url'];
        $this->instanceStatusUrl = $config['instance_status_url'];
        $this->instanceHealthCheckUrl = $config['instance_health_check_url'];
    }

    private function curl($url, $method, $header = null, $data = null)
    {
        $handle = curl_init();
        if ($header) {
            curl_setopt($handle, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_HEADER, 0);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

        switch (strtoupper($method)) {
            case 'GET':
                break;
            case 'POST':
                curl_setopt($handle, CURLOPT_POST, true);
                curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'POST');
                break;
            case 'PUT':
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
                break;
            case 'DELETE':
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }

        $res = curl_exec($handle);
        $errMsg = curl_error($handle);
        if ($errMsg) {
            throw new Exception('请求发生错误，出错信息为：' . $errMsg);
        }
        curl_close($handle);
        return $res;
    }

    public function register()
    {
        $sendData = array(
            'instance' => array(
                "instanceId" => $this->instanceIp . ':' . $this->instanceName . ':' . $this->instancePort,
                //实例注册到eureka服务端的唯一的实例ID
                "app" => $this->instanceName,//在eureka服务上注册的应用程序的名字，默认为unknow
                "appGroupName" => null,//在eureka服务上注册的应用程序组的名字，默认为unknow
                "ipAddr" => $this->instanceIp,//实例的ip地址
                "sid" => "na",
                'port' => array(
                    "@enabled" => true,
                    "$" => $this->instancePort
                ),
                "securePort" => array(
                    "@enabled" => false,
                    "$" => 443
                ),
                "homePageUrl" => $this->instanceHomepageUrl,//实例的相关主页URL路径，然后构造出主机名，安全端口等，默认为/
                "statusPageUrl" => $this->instanceStatusUrl,//实例绝对状态页的URL路径，为其他服务提供信息时来找到这个实例的状态的路径，默认为null
                "healthCheckUrl" => $this->instanceHealthCheckUrl,
                "secureHealthCheckUrl" => null,//实例的相对健康检查URL路径，默认为/health
                "vipAddress" => $this->instanceName,
                "secureVipAddress" => $this->instanceName,
                "countryId" => 1,
                "dataCenterInfo" => array(
                    "@class" => 'com.netflix.appinfo.InstanceInfo$DefaultDataCenterInfo',
                    "name" => "MyOwn"
                ),
                "hostName" => $this->instanceDomain,
                "status" => "UP",
                "overriddenstatus" => "UNKNOWN",
                "leaseInfo" => array(
                    "renewalIntervalInSecs" => 30,// eureka客户需要多长时间发送心跳给eureka服务器，表明它仍然活着,默认为30秒
                    "durationInSecs" => 90,//Eureka服务器在接收到实例的最后一次发出的心跳后，需要等待多久才可以将此实例删除，默认为90秒
                    "registrationTimestamp" => 0,
                    "lastRenewalTimestamp" => 0,
                    "renewalTimestamp" => 0,
                    "evictionTimestamp" => 0,
                    "serviceUpTimestamp" => 0
                ),
                "isCoordinatingDiscoveryServer" => false,
                "metadata" => array(
                    "@class" => 'java.util.Collections$EmptyMap'
                ),
                "lastUpdatedTimestamp" => 1513329362712,
                "lastDirtyTimestamp" => 1513329362712,
                "actionType" => "ADDED"
            )
        );
        $data = json_encode($sendData);
        $header = array(
            "Content-type: application/json;charset=utf-8",
            "Accept: application/json",
            'Connection: Keep-Alive',
            "Content-Length: " . strlen($data)
        );
        $this->curl($this->eurekaServer . 'apps/' . $this->instanceName, 'POST', $header, $data);
    }

    public function canceller()
    {
        $this->curl($this->eurekaServer . 'apps/' . $this->instanceName . '/' . $this->instanceIp . ':' . $this->instanceName . ':'
            . $this->instancePort, 'DELETE');
    }

    public function heartbeat()
    {
        $header = array(
            "Content-type: application/json;charset=utf-8",
            "Accept: application/json",
            'Connection: Keep-Alive'
        );
        return $this->curl($this->eurekaServer . 'apps/' . $this->instanceName . '/' . $this->instanceIp . ':' .
            $this->instanceName . ':'
            . $this->instancePort . '?status=UP', 'PUT', $header);
    }
}

/**
 * 调用演示
 */
error_reporting(E_ALL & ~E_NOTICE);
$config = [
    'eureka_server' => 'http://192.168.144.1:8761/eureka/',//Eureka服务地址
    'instance_name' => 'eureka4php',//实例名
    'instance_domain' => 'youdomain.com',//实例域名，若服务没有绑定域名，则填写IP
    'instance_ip' => '192.168.144.155',//实例IP
    'instance_port' => '80',//实例端口号
    'instance_homepage_url' => 'http://youdomain.com/',//实例主页，以/结尾
    'instance_status_url' => 'http://youdomain.com?ac=info',//实例状态页
    'instance_health_check_url' => 'http://youdomain.com?ac=health'//实例健康检查页
];
$ac = $_GET['ac'];
$EurekaApi = new EurekaApi($config);
if ($ac == 'reg') {
    $EurekaApi->register();
} else if ($ac == 'heartbeat') {
    $EurekaApi->heartbeat();
} else if ($ac == 'unreg') {
    $EurekaApi->canceller();
} else {
    echo '接收参数：' . $ac;
}


