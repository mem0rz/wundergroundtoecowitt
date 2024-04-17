<?php
// 原始请求的参数
$params = $_GET;
var_dump($_GET);

if (empty($params)) {
    // 如果请求参数为空跳过
    return;
}

// 目标参数
$destParams = [
  'PASSKEY' => 'yourpasskey',
	'stationtype'  => 'AIRAINTECH-WS300',
	'dateutc' => $params['dateutc'],
	'tempf' => $params['tempf'],
	'humidity' => $params['humidity'],
	'baromabsin' => $params['absbaromin'],
	'baromrelin' => $params['baromin'],
	'winddir' => $params['winddir'],
	'windspeedmph' => $params['windspeedmph'],
	'windgustmph' => $params['windgustmph'],
	'solarradiation' => $params['solarradiation'],
	'uv' => $params['UV'],
	'rainratein' => $params['rainin'],
	'dailyrainin' => $params['dailyrainin'],
	'weeklyrainin' => $params['weeklyrainin'],
	'monthlyrainin' => $params['monthlyrainin'],
	'tempinf' => $params['indoortempf'],
	'pm25_ch1' => $content = file_get_contents("./phicommtoecowitt/pm25.data"),
];
//ID=IHONGK51&PASSWORD=Zfbw541h&indoortempf=66.9&indoorhumidity=36&tempf=47.9&humidity=83&temp1f=64.9&humidity1=38&dewptf=43.0&windchillf=47.9&absbaromin=30.0&baromin=29.99&windspeedmph=0.0&windgustmph=0.0&winddir=90&windspdmph_avg2m=0.0&winddir_avg2m=135&windgustmph_10m=0.0&windgustdir_10m=135&rainin=0.0&dailyrainin=0.02&weeklyrainin=0.54&monthlyrainin=0.56&solarradiation=0.0&UV=0&dateutc=2024-4-10%2012:4:2&action=updateraw&realtime=1&rtfreq=5& HTTP/1.1

//curl -d "PASSKEY=0BA6979558C5D2ADB6B20F4B23A685AF&stationtype=GW1000_V1.4.7&dateutc=2019-05-28+07:33:48&tempinf=79.7&humidityin=76&baromrelin=29.719&baromabsin=29.719&tempf=79.3&humidity=70&winddir=277&windspeedmph=0.00&windgustmph=0.00&maxdailygust=0.00&solarradiation=0.00&uv=0&rainratein=0.000&eventrainin=0.000&hourlyrainin=0.000&dailyrainin=0.000&weeklyrainin=0.000&monthlyrainin=0.059&yearlyrainin=6.803&totalrainin=6.803&temp1f=78.44&humidity1=74&temp2f=82.04&humidity2=70&temp4f=82.94&humidity4=66&soilmoisture2=0&soilmoisture3=0&soilmoisture4=8&soilmoisture5=0&wh65batt=1&wh68batt=1.50&wh40batt=1.6&wh26batt=0&batt1=0&batt2=0&batt4=0&batt6=0&Siolbatt1=0.0&Siolbatt2=1.5&Siolbatt3=1.5&Siolbatt4=1.5&Siolbatt5=1.5&Siolbatt6=0.0&Siolbatt7=0.0&Siolbatt8=0.0&pm25batt1=0&pm25batt2=0&pm25batt3=0&pm25batt4=0&Freq=433M&model=GW1000" -X POST http://192.168.2.205/data/report/

// 转发的目标地址
$url = 'http://pws.memotz.com/ecowitt/';
$destResult = httpPost($url, $destParams);
// 输出转发返回结果
echo $destResult;

// 再次原始url到homeassistant
$srcUrl = sprintf("http://192.168.0.16:8087/weatherstation/updateweatherstation.php?%s",http_build_query($params));
$srcResult = file_get_contents($srcUrl);

function httpPost(string $url, array $data): string
{
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded",
            'method' => 'POST',
            'content' => http_build_query($data),
        ],
    ];
    $context = stream_context_create($options);
    return file_get_contents($url, false, $context);
}

$logFile = './logfile.log';
$message = 'This is a log message';

// 使用 FILE_APPEND 标志来追加内容，而不是覆盖文件
// 使用 LOCK_EX 标志来防止其他人同时写入文件
file_put_contents($logFile, $destResult.PHP_EOL, FILE_APPEND | LOCK_EX);
