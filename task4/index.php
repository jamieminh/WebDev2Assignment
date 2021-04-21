

<?php
require 'form.php';

$pollutant = $pollutant;
$year = $year;

$ids = [188, 203, 206, 209, 213, 215, 228, 270, 271, 375, 395, 447, 452, 459, 463, 481, 500, 501];
$stations = [];
$reader = new XMLReader();

// start of this year and next year in unix time
$yearUnix = getTimestamp($year, 1);
$nextYearUnix = getTimestamp(intval($year) + 1, 1);


$monthlyAvgAllStation = [];


// get stations info
foreach ($ids as $id) {
    if (!$reader->open("../data_" . $id . ".xml")) {
        die("Failed to open file");
    }
    // array to store sum and data count [sum, count] through 12 months
    $sumCounts = array_fill(0, 12, [0, 0]);

    // loop through lines
    while ($reader->read()) {
        // get readings and data count for each month
        if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'rec') {
            $ts = intval($reader->getAttribute('ts'));
            if ($ts >= $yearUnix && $ts < $nextYearUnix) {
                $reading = $reader->getAttribute($pollutant);
                // if reading is not empty string
                if ($reading) {
                    foreach (range(1, 12) as $m) {     // loop through 12 months                            
                        if ($ts >= getTimestamp($year, $m) && $ts < getTimestamp($year, $m + 1)) {
                            $sumCounts[$m - 1][0] += floatval($reading);
                            $sumCounts[$m - 1][1] += 1;
                            break;
                        }
                    }
                }
            }
        }

        // get station info
        else if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'station') {
            $name = $reader->getAttribute('name');
            $name = str_replace("'", "", $name);
            [$lat, $long] = explode(',', $reader->getAttribute('geocode'));
            $stations[$id] = [$name, floatval($lat), floatval($long)];
        }
    }

    $monthlyAvgStation = [];
    $totalCount = 0;

    // calculate monthly average
    foreach ($sumCounts as $pair) {
        array_push($monthlyAvgStation, $pair[1] ? floatval(number_format($pair[0] / $pair[1], 2)) : 0);
        $totalCount += $pair[1];
    }

    // add monthly average of this station to the all stations array
    $monthlyAvgAllStation[$id] = [$monthlyAvgStation, $totalCount];
}

$reader->close();

function getTimestamp($year, $month)
{
    $d = DateTime::createFromFormat("d-m-Y H:i:s", "01-{$month}-{$year} 08:00:00", new DateTimeZone('GMT'));
    return $d->getTimestamp();
}


?>

<script type="text/javascript">
    let pollutant = <?php echo json_encode($pollutant); ?>;
    let year = <?php echo json_encode($year); ?>;
    let stations = <?php echo json_encode($stations); ?>;
    var monthlyAvgAllStation = <?php echo json_encode($monthlyAvgAllStation); ?>;

    // Load the Visualization API and the corechart package.
    google.charts.load('current', {
        'packages': ['corechart']
    });

    console.log(monthlyAvgAllStation);

    // each color correspond to concentration <= 100, 200, 300, 400, 500, 600, 700
    const dangerColors = ['#00bf43', '#79ba00', '#bbc402', '#c99a00', '#c26800', '#bf2a00', '#c20064']
    

    // each pin correspond to concentration <= 100, 200, 300, 400, 500, 600, over 600 and 'no data'
    const dangerLevelPins = [
        '100.png', '200.png', '300.png', '400.png', 
        '500.png', '600.png', 'over.png', 'none.png'
    ].map(pin => './assets/pin_' + pin)
        

    async function initMap() {
        map = new google.maps.Map(document.getElementById("map"), {
            center: {
                lat: 51.4622796,
                lng: -2.6031459
            }, // Bristol Uni as center
            zoom: 12.5,
        });

        setMarkers()

        document.getElementById('map-info').innerText = 'Average ' + pollutant.toUpperCase() + ' concentration in the year ' + year
    }


    const setMarkers = () => {
        Object.keys(stations).forEach(sttID => {
            const [monthlyAvgs, count] = monthlyAvgAllStation[sttID];
            const sttAvg = parseFloat((monthlyAvgs.reduce((a, b) => a + b, 0) / 12).toFixed(2))
            const [sttName, lat, long] = stations[sttID]
            // console.log(sttID, sttAvg, monthlyAvgs, count);

            var iconUrl = dangerLevelPins[7]
            if (sttAvg > 0 && sttAvg <=600) {
                iconUrl = dangerLevelPins[parseInt(Math.floor(sttAvg/100))]
            }
            else if (sttAvg > 600) {
                iconUrl = dangerLevelPins[6]
            }

            // create a new marker
            const marker = new google.maps.Marker({
                position: {
                    lat: lat,
                    lng: long
                },
                title: sttID + ': ' + sttName + ' - Average: ' + (sttAvg || 'No Data'),
                label: {
                    text: sttID,
                    color: 'white',
                    fontSize: "11px"
                },
                icon: {
                    url: iconUrl,
                    scaledSize: new google.maps.Size(30, 46), 
                },
                animation: google.maps.Animation.DROP,
                map: map
            })

            // add onclick listener to marker 
            google.maps.event.addListener(marker, 'click', () => {
                $(".modal-title").text(sttID + ': ' + sttName + ' - Average: ' + (sttAvg || 'No Data'));
                $("#chartModal").modal('show');
                // setTimeOut for 'chart_div' to load before drawing the chart, else y axis will lose value scale
                setTimeout(() => google.charts.setOnLoadCallback(drawColumnChart(monthlyAvgs, sttID)), 150)
            })


        })
    }

    const drawColumnChart = (monthlyAvgs, sttId) => {
        // add 'month' column and header row
        const columnsData = monthlyAvgs.map((avg, index) => [index + 1, avg, getColor(avg)])
        columnsData.unshift(['Month', 'Concentration (µg/m³)', {
            role: 'style'
        }])

        let data = google.visualization.arrayToDataTable(columnsData);

        // add figures on top of columns
        let view = new google.visualization.DataView(data);
        view.setColumns([0, 1, {
            calc: "stringify",
            sourceColumn: 1,
            type: "string",
            role: "annotation"
        }, 2]);

        // Set chart options
        let options = {
            'title': 'Monthly average ' + pollutant.toUpperCase() + ' concentration measured by station ' + sttId + ', in µg/m³',
            hAxis: {
                title: 'Month',
                gridlines: {
                    count: 6
                }
            },
            vAxis: {
                title: 'Concentration (µg/m³)',
                gridlines: {
                    count: 6
                }
            },
            'width': 600,
            'height': 300,
            legend: {
                position: 'none'
            },
            fontName: 'Roboto'
        };

        // Instantiate and draw chart, passing in options.
        let chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
        chart.draw(view, options);
    }

    const getColor = (reading) => {
        return (dangerColors[Math.floor(reading / 100)])
    }
</script>