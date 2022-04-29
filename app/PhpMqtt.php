<?php

namespace App;


class PhpMqtt
{
    public static $connection = null;
    public static function connect(){
        $username = getenv('MQTT_USERNAME');
        $password = getenv('MQTT_PASSWORD');
        $client_id = \Hash::make('1234567890');
        $MQTT_SERVER = getenv('MQTT_SERVER');
        $MQTT_PORT = getenv('MQTT_PORT');
        $con = new \Bluerhinos\phpMQTT($MQTT_SERVER,$MQTT_PORT,$client_id);
        if ($con->connect(true,null,$username,$password)){
            return $con;
        }else{
            echo "Time out!\n";
        }
    }

    public static function publish($channel, $data, $qos = 0){
        if(PhpMqtt::$connection === null) {
            PhpMqtt::$connection = PhpMqtt::connect();
        }
        PhpMqtt::$connection->publish($channel,$data,$qos);
    }

    public function __destruct()
    {
        PhpMqtt::$connection->close();
    }
}
