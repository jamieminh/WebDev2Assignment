
google.charts.load('current', { 'packages': ['corechart'] });

const getUnixTime = (year, month, hour) => {
    const date = new Date(year + '-' + month + '-01T' + hour + ':00:00')
    return date.getTime() / 1000
}

const monthsInUnix = (year) => {
    const months = [
        getUnixTime(year, '01', '08'),      // Jan 1st, year at 8am
        getUnixTime(year, '02', '08'),      // Feb 1st, year at 8am
        getUnixTime(year, '03', '08'),      // Mar 1st, year at 8am
        getUnixTime(year, '04', '08'),
        getUnixTime(year, '05', '08'),
        getUnixTime(year, '06', '08'),
        getUnixTime(year, '07', '08'),
        getUnixTime(year, '08', '08'),
        getUnixTime(year, '09', '08'),
        getUnixTime(year, '10', '08'),
        getUnixTime(year, '11', '08'),
        getUnixTime(year, '12', '08'),
        getUnixTime(parseInt(year) + 1, '01', '08'),    // Jan 1st, year + 1 ,at 8am
    ]

    return months
}


const getAverageNO = async (year) => {
    // initialize an empty 2d array representing months and days 12x31
    // number of days for each month doesn't matter, latter when calculating average,
    // we'll skip days that has value=0
    const results = new Array(12).fill(0).map(_ => new Array(31).fill(0))
    const h24 = 24 * 3600;            // 24 hours in unix time

    const months = monthsInUnix(year)
    // let average = []

    let xmlContent = ''

    await fetch('../data_203.xml')
        .then(async res => {
            await res.text().then(async xml => {
                xmlContent = xml
                let parser = new DOMParser()
                let xmlDOM = parser.parseFromString(xmlContent, 'application/xml')
                let recs = xmlDOM.querySelectorAll('rec');

                const getNOdaysInMonth = (month, ts, rec) => {
                    if ((ts - months[month]) % h24 === 0) {
                        const day = (ts - months[month]) / h24
                        results[month][day] += parseFloat(rec.getAttribute('no'))
                    }
                }

                recs.forEach(rec => {
                    let ts = parseInt(rec.getAttribute('ts'))
                    switch (true) {
                        case (ts >= months[0] && ts < months[1] - 1):    // Jan; minus 1 so it's just before 8am
                            getNOdaysInMonth(0, ts, rec)
                            break
                        case (ts >= months[1] && ts < months[2] - 1):    // Feb
                            getNOdaysInMonth(1, ts, rec)
                            break
                        case (ts >= months[2] && ts < months[3] - 1):    // March
                            getNOdaysInMonth(2, ts, rec)
                            break
                        case (ts >= months[3] && ts < months[4] - 1):    // April
                            getNOdaysInMonth(3, ts, rec)
                            break
                        case (ts >= months[4] && ts < months[5] - 1):    // May
                            getNOdaysInMonth(4, ts, rec)
                            break
                        case (ts >= months[5] && ts < months[6] - 1):    // June
                            getNOdaysInMonth(5, ts, rec)
                            break
                        case (ts >= months[6] && ts < months[7] - 1):    // July
                            getNOdaysInMonth(6, ts, rec)
                            break
                        case (ts >= months[7] && ts < months[8] - 1):    // August
                            getNOdaysInMonth(7, ts, rec)
                            break
                        case (ts >= months[8] && ts < months[9] - 1):    // September
                            getNOdaysInMonth(8, ts, rec)
                            break
                        case (ts >= months[9] && ts < months[10] - 1):   // October 
                            getNOdaysInMonth(9, ts, rec)
                            break
                        case (ts >= months[10] && ts < months[11] - 1):  // November
                            getNOdaysInMonth(10, ts, rec)
                            break
                        case (ts >= months[11] && ts < months[12] - 1):  // December 
                            getNOdaysInMonth(11, ts, rec)
                            break
                    }
                })
            })
        })

    const average = results.map(month => {
        let sum = 0;
        let nonZero = 0;
    
        month.forEach(day => {
            if (day !== 0) {
                sum += day;
                nonZero++;
            }
        })
    
        return nonZero ? (sum / nonZero) : 0
    })
    return average
}

const drawChart = (averages) => {
    var data = google.visualization.arrayToDataTable([
        ['Month', 'Weight'],
        [1, averages[0]],
        [2, averages[1]],
        [3, averages[2]],
        [4, averages[3]],
        [5, averages[4]],
        [6, averages[5]],
        [7, averages[6]],
        [8, averages[7]],
        [9, averages[8]],
        [10, averages[9]],
        [11, averages[10]],
        [12, averages[11]]
    ]);

    var options = {
        title: 'Month vs. Concentration comparison',
        hAxis: { title: 'Month', minValue: 1, maxValue: 12 },
        vAxis: { title: 'Concentration' },
        legend: 'none'
    };

    var chart = new google.visualization.ScatterChart(document.getElementById('chart_div'));

    chart.draw(data, options);
}

const drawChartOptions = (readings) => {
    google.charts.load('current', { 'packages': ['corechart'] });
    google.charts.setOnLoadCallback(drawChart(readings));
}

const drawScatterChart = async () => {
    const year = document.getElementById('year').value
    const readings = await getAverageNO(year)
    drawChartOptions(readings)
}


