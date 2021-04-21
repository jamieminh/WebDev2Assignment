<?php
require 'form.php';

$date = $date;
$sttIds = $sttIds;
$pollutant = $pollutant;

// the chosen date at 00:00:00 in unix time
$dateBegin = DateTime::createFromFormat("Y-m-d H:i:s", "{$date} 00:00:00", new DateTimeZone('GMT'))->getTimestamp();
$h24 = 24 * 3600;                       // 24 hours in unix time
$reader = new XMLReader();

// array to store array of 24 records of 6 stations
$stationsResults = [];

// open station files
foreach ($sttIds as $id) {
    if (!$reader->open("../data_" . $id . ".xml")) {
        die("Failed to open file");
    }

    // array to store 24 items initialized to -1
    $results = array_fill(0, 24, -1);


    // loop through recs
    while ($reader->read()) {
        if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'rec') {
            $ts = intval($reader->getAttribute('ts'));

            // if 'ts' is between dateBegin and the next 24 hours (0:00 -> 23:59)
            if ($ts >= $dateBegin && $ts < ($dateBegin + $h24)) {
                // 3600 is an hour in unix time, if the mod resutl is 0,
                // then the ts was measured at the hour that is the result of division
                if (($ts - $dateBegin) % 3600 == 0) {
                    $hour = ($ts - $dateBegin) / 3600;      // e.g. if $hour = 3, record is measured at 3am
                    $pollutantValue = $reader->getAttribute($pollutant);    // get pollutants

                    // add float value if there's reading, else 'NaN' 
                    $results[$hour] = floatval($pollutantValue) ?: 'NaN';
                }
            }
        }
    }

    // if there are no readings for an hour, replace them with 'NaN' values 
    foreach ($results as $index => $hour) {
        if ($hour == -1) {
            $results[$index] = 'NaN';
        }
    }

    array_push($stationsResults, [$id, $results]);

    $reader->close();
}


?>

<script type="text/javascript">
    google.charts.load('current', {
        'packages': ['corechart']
    });
    let allStationsResults = JSON.parse('<?php echo json_encode($stationsResults); ?>');
    let sttIds = JSON.parse('<?php echo json_encode($sttIds); ?>');
    let sttIdsString = sttIds.join(', ')

    // setTimeOut for google libraries to load
    setTimeout(() => {
        if (google.visualization != undefined) {
            google.charts.setOnLoadCallback(drawChart());
        }
    }, 400)


    const drawChart = () => {

        // x axes and lines (Hour, stt_1, stt_2, ..., stt_6)
        let dataColumns = [
            ['Hour', ...sttIds.map(item => item.toString())]
        ]


        // add data in the form of [hour, stt_1[hour], stt_2[hour], ..., stt_6[hour]]
        for (let i = 0; i <= 23; i++) {
            let thisHour = [i]
            allStationsResults.forEach(stt => {
                (stt[1][i] === "NaN") ? thisHour.push(NaN): thisHour.push(stt[1][i])
            })
            dataColumns.push(thisHour)
        }

        // convert US format date (yyyy-mm-dd) to dd-mmm-yyyy
        const date = new Date('<?php echo $date; ?>')
        const dateFormated = date.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
        }).replace(/ /g, '-');
        
        let data = google.visualization.arrayToDataTable(dataColumns);

        let options = {
            title: '<?php echo strtoupper($pollutant); ?> readings on ' + dateFormated +' measured by station ' +
                sttIdsString + '; measured in µg/m³',
            curveType: 'none',
            hAxis: {
                title: 'Hour',
                minValue: 0,
                maxValue: 23,
                gridlines: {
                    count: 12
                }
            },
            vAxis: {
                title: 'Concentration (µg/m³)',
            },
            interpolateNulls: true,
            pointSize: 5,
            series: {
                0: {
                    color: '#e2431e'
                },
                1: {
                    color: '#6abf30'
                },
                2: {
                    color: '#43459d'
                },
                3: {
                    color: '#e3c51b'
                },
                4: {
                    color: '#8c21d9'
                },
                5: {
                    color: '#38c0c9'
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