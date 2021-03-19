<?php

$st = microtime(true);
$stations = [
    '203', '206', '188', '209', '213', '215',
    '228', '270', '271', '375', '395', '452',
    '447', '459', '463', '481', '500', '501'
];

// loop through each station file
foreach ($stations as $stationNum) {
    $file = fopen("data_$stationNum.csv", "r") or die("Unable to open data_$stationNum.csv!");
    fgets($file);                                   // skip header line

    $xml = new XMLWriter();
    $xml->openUri("data_" . $stationNum . '.xml');
    $xml->startDocument('1.0', 'UTF-8');
    $xml->setIndent(true);

    $firstRec = fgets($file);                       // get the first DATA line to get geocode and name
    $stationInfo = explode(',', $firstRec);         // turn the data string into an array

    // if there are no header (e.g. station 481), go and get name and geocode from the original csv file
    if (count($stationInfo) == 1) {                 
        $id = $stationNum;
        [$name, $geocode] = getNameGeo($stationNum);
    } 
    // else get id, name, and geocode (note: trim the newline at the end)
    else {
        $id = $stationInfo[0];
        $name = $stationInfo[14];
        $geocode = $stationInfo[15] . ',' . trim($stationInfo[16]);
    }

    $xml->startElement('station');                  // open <station > tag
    $xml->writeAttribute('id', $id);                // write the attributes
    $xml->writeAttribute('name', $name);
    $xml->writeAttribute('geocode', $geocode);

    rewind($file);                                  // move pointer back to start of file
    fgets($file);                                   // skip first header line


    // loop though data lines 
    while ($rec = fgets($file)) {
        $recArr = explode(",", $rec);

        // ignore empty records or records that dont have nox and no2 readings
        if (count($recArr) == 1 || ($recArr[2] == '' & $recArr[3] == ''))
            continue;

        $xml->startElement('rec');                  // open <rec>
        $xml->writeAttribute('ts', $recArr[1]);     // add ts and pollutants attribute
        $xml->writeAttribute('nox', $recArr[2]);
        $xml->writeAttribute('no', $recArr[4]);
        $xml->writeAttribute('no2', $recArr[3]);
        $xml->endElement();                         // close </rec>
    }

    $xml->fullEndElement();                         // close </station> tag
    $xml->flush();
    fclose($file);

    removeLastNewLine($stationNum);                 // remove the last new line
}


function getNameGeo($stationID) {
    $in_file = fopen("air-quality-data-2004-2019.csv", "r") or die("Unable to open file!");
    while ($data = fgets($in_file)) {
        $arr = explode(";", $data);                 // explode the line into array

        if ($arr[4] == $stationID) {                // if a line with station Id is found
            $name = $arr[17];                       // set $name and geocode
            $geocode = $arr[18];
            break;
        }
    }

    fclose($in_file);

    return [$name, $geocode];
}

function removeLastNewLine($stationID) {
    $file = fopen('data_' . $stationID . '.xml', 'r+') or die("can't open: $php_errormsg");
    fseek($file, -2, SEEK_END);             // place the pointer just before the > of the station tag
    fwrite($file, '> ');                    // overwrite 2 bytes with a '>' and a space 
    fclose($file);    
}

echo microtime(true) - $st;
