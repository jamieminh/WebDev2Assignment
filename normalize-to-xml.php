<?php

$st = microtime(true);
$stations = [
    '203', '206', '188', '209', '213', '215',
    '228', '270', '271', '375', '395', '452',
    '447', '459', '463', '481', '500', '501'
];

// loop through each station file
foreach ($stations as $stationNum) {
    $file = fopen("./data_files/data_$stationNum.csv", "r") or die("Unable to open data_$stationNum.csv!");
    fgets($file);  // skip first line

    $xml = new XMLWriter();
    $xml->openUri("data_" . $stationNum . '.xml');
    $xml->startDocument('1.0', 'UTF-8');
    $xml->setIndent(true);

    $firstRec = fgets($file);
    $stationInfo = explode(',', $firstRec);
    if (count($stationInfo) == 1) {
        $id = $stationNum;
        [$name, $geocode] = getNameGeo($stationNum);
    } else {
        $id = $stationInfo[0];
        $name = $stationInfo[14];
        $geocode = $stationInfo[15] . ',' . trim($stationInfo[16]);
    }

    $xml->startElement('station');      // open <station > tag
    $xml->writeAttribute('id', $id);
    $xml->writeAttribute('name', $name);
    $xml->writeAttribute('geocode', $geocode);

    rewind($file);                      // move pointer back to start of file
    fgets($file);                       // skip first header line


    // loop though data lines (no header)
    while ($rec = fgets($file)) {
        $recArr = explode(",", $rec);

        // ignore empty records or records that dont have nox and no2 readings
        if (count($recArr) == 1 || ($recArr[2] == '' & $recArr[3] == ''))
            continue;

        $xml->startElement('rec');                  // open <rec>
        $xml->writeAttribute('ts', $recArr[1]);     // add 'ts' attribute
        $xml->writeAttribute('nox', $recArr[2]);    // add 'nox' attribute
        $xml->writeAttribute('no', $recArr[4]);     // add 'no' attribute
        $xml->writeAttribute('no2', $recArr[3]);    // add 'no2' attribute
        $xml->endElement();                         // close </rec>
    }

    $xml->fullEndElement();                             // close </station> tag
    $xml->flush();
    fclose($file);

    removeLastNewLine($stationNum);
}


function getNameGeo($stationID) {
    $in_file = fopen("air-quality-data-2004-2019.csv", "r") or die("Unable to open file!");
    while ($data = fgets($in_file)) {
        $arr = explode(";", $data);     // explode the line into array

        if ($arr[4] == $stationID) {    // if a line with station Id is found
            $name = $arr[17];           //  set $name and geocode
            $geocode = $arr[18];
            break;
        }
    }
    fclose($in_file);

    return [$name, $geocode];
}

function removeLastNewLine($stationID) {
    $file = fopen('data_' . $stationID . '.xml', 'r+') or die("can't open: $php_errormsg");
    fseek($file, -2, SEEK_END);      // place the pointer just before the > of the station tag
    fwrite($file, '> ');             // overwrite 2 bytes with a '>' and a space 
    fclose($file);    
}

echo microtime(true) - $st;
