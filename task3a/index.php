<?php
require 'form.php';

$year = intval($year);
$id = $sttId;

$reader = new XMLReader();
$sums = [];
$results = array_fill(0, 12, 'NaN');    // a 12-item array initialized with NaN values
$h24 = 24 * 3600;                       // 24 hours in unix time


// array contains start of 13 months at 8am in timestamp (Jan -> Dec of $year, and Jan of next year)
$monthsUnix = [];
foreach (range(1, 13) as $m) {
    array_push($monthsUnix, getTimestamp($year, $m));
}


if (!$reader->open("../data_" . $id . ".xml")) {
    die("Failed to open file");
}
$sums = array_fill(0, 12, 0);
$counts = array_fill(0, 12, 0);

// loop through recs
while ($reader->read()) {
    if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'rec') {
        // $node = simplexml_import_dom($doc->importNode($reader->expand(), true));
        $ts = intval($reader->getAttribute('ts'));

        foreach (range(0, 11) as $m) {
            if ($ts >= $monthsUnix[$m] && $ts < $monthsUnix[$m + 1]) {
                // if 'ts' is divisible by 'h24', it is at 8am of some day in the month
                if (($ts - $monthsUnix[$m]) % $h24 === 0) {
                    $sums[$m] += intval($reader->getAttribute('no'));
                    $counts[$m]++;
                    break;
                }
            }
        }
    }
}

$reader->close();

$averages = [];
// calculate averages
foreach ($sums as $index => $sum) {
    if ($counts[$index] != 0)
        array_push($averages, number_format($sum / $counts[$index], 2));
    else
        array_push($averages, 'NaN');
}

function getTimestamp($year, $month)
{

    $d = DateTime::createFromFormat("d-m-Y H:i:s", "01-{$month}-{$year} 08:00:00", new DateTimeZone('GMT'));
    return $d->getTimestamp();
}

?>

<script type="text/javascript">
    google.charts.load('current', {
        'packages': ['corechart']
    });
    let averages = JSON.parse('<?php echo json_encode($averages); ?>');
    let year = '<?php echo $year; ?>';
    let sttId = '<?php echo $sttId; ?>';

    // setTimeOut for google libraries to load
    setTimeout(() => {
        if (google.visualization != undefined) {
            google.charts.setOnLoadCallback(drawChart());
        }
    }, 400)


    const drawChart = () => {
        // map averages to array of array contains [month, average] then add a header
        let chartData = averages.map((avg, index) => [index + 1, (avg === "NaN") ? NaN : parseFloat(avg)]);
        chartData.unshift(['Month', 'Average Concentration (µg/m³)']);

        let data = google.visualization.arrayToDataTable(chartData);

        var options = {
            title: 'Monthly average NO concentration in the year ' + year + ' measured by station ' + sttId + ', measured in µg/m³',
            hAxis: {
                title: 'Month',
            },
            vAxis: {
                title: 'Concentration (µg/m³)',
            },
            legend: 'none'
        };

        var chart = new google.visualization.ScatterChart(document.getElementById('chart_div'));

        chart.draw(data, options);
    }
</script>