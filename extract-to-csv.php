<?php

// set PHP flags to run large file
@date_default_timezone_set("GMT");

ini_set('memory_limit', '512MB');
ini_set('max_execution_time', '300');
ini_set('auto_detect_line_endings', TRUE);

$st = microtime(true);


// $in_file = fopen("fragment.csv", "r") or die("Unable to open file!");
$in_file = fopen("air-quality-data-2004-2019.csv", "r") or die("Unable to open file!");

$stations = [
    '203', '206', '188', '209', '213', '215',
    '228', '270', '271', '375', '395', '452',
    '447', '459', '463', '481', '500', '501'
];


$header = 'siteID,ts,nox,no2,no,pm10,nvpm10,vpm10,nvpm2.5,pm2.5,vpm2.5,co,o3,so2,loc,lat,long';

// open files for writing
foreach ($stations as $stationNum) {
    // dynamic variable name (variable variables)
    ${'data_' . $stationNum} = fopen("data_$stationNum.csv", "w") or die("Unable to open data_$stationNum.csv!");
    fwrite(${'data_' . $stationNum}, $header);      // write the header line
}


fgets($in_file);                                    // skip first line of input (header)

while ($data = fgets($in_file)) {
    $arr = explode(";", $data);                     // split the line to make an array

    $nox = $arr[1];
    $co = $arr[11];

    if (empty($nox) && empty($co)) {                // if both nox and co readings are empty, skip
        continue;
    }

    // get lat, long from geocode
    [$lat, $long] = explode(",", $arr[18]);

    // convert to UNIX timestamp (seconds passed since Jan 1st, 1970)
    $datetime = explode("+", $arr[0])[0];           
    $datetime = str_replace("T", " ", $datetime);   // reformat the string to datetime format
    $ts = strtotime($datetime);

    // arr with all neccessary columns
    $essential = array_merge([$arr[4], $ts], array_slice($arr, 1, 3), 
                            array_slice($arr, 5, 9), [$arr[17], $lat, $long]);

    $write_str = implode(",", $essential );

    // write record into the responding file 
    fwrite(${'data_' . $arr[4]}, "\n" . $write_str);
}

foreach ($stations as $stationNum) {
    fclose(${'data_' . $stationNum});
}

echo microtime(true) - $st;


?>

