<?php
require 'form.php';

$date = $date;
$id = $sttId;

// the chosen date at 00:00:00 in unix time
$dateBegin = DateTime::createFromFormat("Y-m-d H:i:s", "{$date} 00:00:00", new DateTimeZone('GMT'))->getTimestamp();
$h24 = 24 * 3600;                       // 24 hours in unix time
$reader = new XMLReader();

if (!$reader->open("../data_" . $id . ".xml")) {
    die("Failed to open file");
}

$results = array_fill(0, 24, 0);

// loop through recs
while ($reader->read()) {
    if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'rec') {
        // $node = simplexml_import_dom($doc->importNode($reader->expand(), true));
        $ts = intval($reader->getAttribute('ts'));

        // if 'ts' is between dateBegin and the next 24 hours (inclusive, exclusive)
        if ($ts >= $dateBegin && $ts < ($dateBegin + $h24)) {
            // 3600 is an hour in unix time, if the mod resutl is 0,
            // then the ts was measured at the hour that is the result of division
            if (($ts - $dateBegin) % 3600 == 0) {
                $hour = ($ts - $dateBegin) / 3600;      // e.g. if $hour = 3, record is measured at 3am
                $nox = $reader->getAttribute('nox');    // get pollutants
                $no2 = $reader->getAttribute('no2');
                $no = $reader->getAttribute('no');

                // add float value if there's reading, else 'NaN' 
                $results[$hour] = [$hour, floatval($nox) ?: 'NaN', floatval($no2) ?: 'NaN', floatval($no) ?: 'NaN'];
            }
        }
    }
}

// if there are no readings for ant hour, replace them with 'NaN' values 
foreach ($results as $index => $hour) {
    if ($hour == 0) {
        $results[$index] = [$hour, 'NaN', 'NaN', 'NaN'];
    }
}

$reader->close();

?>

<script type="text/javascript">
    google.charts.load('current', {
        'packages': ['corechart']
    });
    let results = JSON.parse('<?php echo json_encode($results); ?>');
    let date = '<?php echo $date; ?>';
    let sttId = '<?php echo $sttId; ?>';

    console.log(results);


    // setTimeOut for google libraries to load
    setTimeout(() => {
        if (google.visualization != undefined) {
            google.charts.setOnLoadCallback(drawChart());
        }
    }, 400)


    const drawChart = () => {
        // add the header for x and y axis
        results.forEach(hour => {
            for (let i=1; i<=3; i++) {
                if (hour[i] === 'NaN')
                    hour[i] = NaN
            }
        })

        results.unshift(['Hour', 'NOX', 'NO2', 'NO']);

        let data = google.visualization.arrayToDataTable(results);

        let options = {
            title: 'Pollutants levels on ' + date + ' measured by station ' + sttId + ', measured in µg/m³',
            curveType: 'none',
            hAxis: {
                title: 'Hour',
                minValue: 0,
                maxValue: 23,
                gridlines: {count: 12}
            },
            vAxis: {
                title: 'Concentration (µg/m³)',
            },
            interpolateNulls: true,
            pointSize: 5,
            series: {
                0: {
                    color: '#e2431e',
                    pointShape: 'circle'
                },
                1: {
                    color: '#6f9654',
                    pointShape: 'circle'
                },
                2: {
                    color: '#43459d',
                    pointShape: 'circle'
                },
            },
            legend: {
                position: 'bottom'
            }
        };

        let chart = new google.visualization.LineChart(document.getElementById('chart_div'));

        chart.draw(data, options);
    }
</script>