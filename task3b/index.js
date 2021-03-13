google.charts.load('current', { 'packages': ['corechart'] });

const getDateReadings = async (date, stationID) => {
    const aDay = 24 * 60 * 60        // 24hr in unix time

    // unix time at begining of date
    const dateBegin = new Date(date + 'T00:00:00').getTime() / 1000

    // array to store [hour, nox, no, no2] of the date
    const results = new Array(24)
    // for (let i=0; i<24; i++) {
    //     results.push([i, 0, 0, 0])
    // }

    console.log(results);

    let xmlContent = ''

    // fetch the xml file based on station id
    await fetch('../data_' + stationID + '.xml')
        .then(async res => {
            console.log('File opened Successfully');
            await res.text().then(async xml => {
                xmlContent = xml
                let parser = new DOMParser()
                let xmlDOM = parser.parseFromString(xmlContent, 'application/xml')
                let recs = xmlDOM.querySelectorAll('rec');

                recs.forEach(rec => {
                    let ts = parseInt(rec.getAttribute('ts'))
                    // if 'ts' is between dateBegin and the next 24 hours (inclusive, exclusive)
                    if (ts >= dateBegin && ts < (dateBegin + aDay)) {
                        // 3600 is an hour in unix time, if the mod resutl is 0,
                        // then the ts was measured at hour that is the result of division
                        if ((ts - dateBegin) % 3600 === 0) {
                            const hour = (ts - dateBegin) / 3600
                            const nox = rec.getAttribute('nox') || null   // return the value if it's not empty, else return null
                            const no = rec.getAttribute('no') || null
                            const no2 = rec.getAttribute('no2') || null

                            // add to results array
                            results[hour] = [hour, parseFloat(nox), parseFloat(no), parseFloat(no2)]
                        }
                    }
                })
            })
        })
        .catch(_ => console.error('Cannot open File'));


    // add an array with NaN values of pollutants to hours that dont have readings
    // the NaN points on the graph will not be denoted with a point shape (i.e circle)
    for (let i = 0; i < 24; i++) {
        if (!results[i]) {
            console.log('empty');
            results[i] = [i, NaN, NaN, NaN]
        }
    }

    return results
}

const drawChart = (readings, date, station) => {
    // add the header to the readings to draw chart
    readings.unshift(['Hour in Day', 'NOX', 'NO', 'NO2'])

    var data = google.visualization.arrayToDataTable(readings);
    console.log(readings);

    // chart options (line color, line curve style, etc)
    var options = {
        title: 'Pollutants (NOX, NO, NO2) levels on ' + date + ' measured by station ' + station,
        curveType: 'none',
        interpolateNulls: true,
        pointSize: 5,
        series: {
            0: { color: '#e2431e', pointShape: 'circle' },
            1: { color: '#6f9654', pointShape: 'circle' },
            2: { color: '#43459d', pointShape: 'circle' },
        },
        legend: { position: 'bottom' }
    };

    // draw chart at node 'chart_div'
    var chart = new google.visualization.LineChart(document.getElementById('chart_div'));

    chart.draw(data, options);

}

const drawChartOptions = (readings, date, station) => {
    google.charts.load('current', { 'packages': ['corechart'] });
    google.charts.setOnLoadCallback(drawChart(readings, date, station));
}

const testArr = [
    [1, 37.8, 34.4, 55.0],
    [2, null, 69.5, null],
    [3, null, null, null],
    [4, null, 18.8, 43.4],
    [5, null, 17.6, 12.4],
    [6, null, 13.6, null],
    [7, null, null, 34.2],
    [8, null, 29.2, 34.1],
    [9, null, 42.9, 34.1],
    [10, null, 30.9, 65.3],
    [11, null, 7.9, 4.7],
    [12, null, 8.4, null],
    [13, null, null, null],
    [14, 30.8, 6.2, 65.4]
]


const drawLineChart = async () => {
    const stationSelect = document.getElementById('station')
    const stationID = stationSelect.value           // get selected station's id
    // get station name for chart title
    const stationName = stationSelect.options[stationSelect.selectedIndex].text

    // get selected date format yyyy-mm-dd
    const date = document.getElementById('date').value

    // get readings for selected date and station
    const dateReadings = await getDateReadings(date, stationID)
    // console.log(testArr);

    // draw chart with retured redings
    drawChartOptions(dateReadings, date, stationName)
}


