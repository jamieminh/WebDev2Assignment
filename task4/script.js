// Load the Visualization API and the corechart package.
google.charts.load('current', { 'packages': ['corechart'] });

let stations = {
    188: ["AURN Bristol Centre", 51.4572041156, -2.58564914143],    // 188
    203: ["Brislington Depot", 51.4417471802, -2.55995583224],     // 203
    206: ["Rupert Street", 51.4554331987, -2.59626237324],     // 206
    209: ["IKEA M32", 51.4752847609, -2.56207998299],     // 209
    213: ["Old Market", 51.4560189999, -2.58348949026],     // 213
    215: ["Parson Street School", 51.432675707, -2.60495665673],      // 215
    228: ["Temple Meads Station", 51.4488837041, -2.58447776241],     // 228
    270: ["Wells Road", 51.4278638883, -2.56374153315],     // 270
    271: ["Trailer Portway P&R", 51.4899934596, -2.68877856929],     // 271
    375: ["Newfoundland Road Police Station", 51.4606738207, -2.58225341824],     // 375
    395: ["Shiner's Garage", 51.4577930324, -2.56271419977],     // 395
    447: ["Bath Road", 51.4425372726, -2.57137536073],     // 447
    452: ["AURN St Pauls", 51.4628294172, -2.58454081635],     // 452
    459: ["Cheltenham Road \ Station Road", 51.4689385901, -2.5927241667],      // 459
    463: ["Fishponds Road", 51.4780449714, -2.53523027459],     // 463
    481: ["CREATE Centre Roof", 51.447213417, -2.62247405516],      // 481
    500: ["Temple Way", 51.4579497129, -2.58398909033],     // 500
    501: ["Colston Avenue", 51.4552693825, -2.59664882861]      // 501
}

// array to hold year-pollutant combination so next time user select it, no need to wait
// {no_2015: {188: [[monthlyAvgs], totalCount], 203: [[..],..], ...}, no2_2016: {....} }
// totalCount is the number of records collected, used to determine the radius of concentration circle
let visited = {}
let map;
let markers = []
let circles = []
let xmlContent = ''
let year = '2015'
let pollutant = 'no'

// > 100, 200, 300, 400, 500, 600, 700
const dangerColors = ['#00bf43', '#79ba00', '#bbc402', '#c99a00', '#c26800', '#bf2a00', '#c20064']

async function initMap() {
    map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 51.4622796, lng: -2.6031459 },    // Bristol Uni as center
        zoom: 12.5,
    });

    setMarkers()

    // add event listerner to relooad markers when user choose year and pollutant
    document.getElementById('reloadMap').addEventListener('click', reloadMarkers);
}

const setMarkers = () => {
    const thisVisited = visited[pollutant + '_' + year]
    // if this year-pollutant combination has been visited, use the stored data to set markers
    if (thisVisited) {
        // visited: {id: [[monthlyAvg], totalCount], ...}  
        Object.keys(thisVisited).forEach(sttID => {
            const sttInfo = thisVisited[sttID]
            addAMarker(sttID, sttInfo[0], sttInfo[1])       // id, monthlyAvg, totalCount
        })

        // stop spinner and update Map Info box
        document.getElementById('map-info').innerText = 'Average ' + pollutant.toUpperCase() + ' concentration in the year ' + year
        document.getElementById('loader').style.display = 'none'
    }
    else {
        document.getElementById('loader').style.display = 'block'       // start the spinner
        const aVisit = []
        Object.keys(stations).forEach(async (sttID) => {
            const yearUnix = new Date(year).getTime() / 1000            // get selected year and next year in unix time
            const nextYearUnix = new Date(year + 1).getTime() / 1000
            let [monthlyAvgs, count] = await getAverageMonthly(sttID, yearUnix, nextYearUnix)
            addAMarker(sttID, monthlyAvgs, count)
            aVisit[sttID] = [monthlyAvgs, count]
        })

        // add this year-pollutant combination to the visited
        setTimeout(() => visited[pollutant + '_' + year] = aVisit, 1000)

        // stop spinner and update Map Info box
        setTimeout(() => {
            document.getElementById('map-info').innerText = 'Average ' + pollutant.toUpperCase() + ' concentration in the year ' + year
            document.getElementById('loader').style.display = 'none'
        }, 5000)
    }
}

const addAMarker = (sttID, monthlyAvgs, count) => {
    const sttAvg = parseFloat((monthlyAvgs.reduce((a, b) => a + b, 0) / 12).toFixed(2))
    const [sttName, lat, long] = stations[sttID]
    console.log(sttID, sttAvg, monthlyAvgs);

    // create a new marker
    const marker = new google.maps.Marker({
        position: { lat: lat, lng: long },
        title: sttID + ': ' + sttName + ' - Average: ' + sttAvg || 'No Data',
        label: { text: sttID, color: 'white', fontSize: "9px" },
        animation: google.maps.Animation.DROP,
        map: map
    })

    // add onclick listener to marker 
    google.maps.event.addListener(marker, 'click', () => {
        $(".modal-title").text(sttID + ': ' + sttName + ' - Average: ' + sttAvg || 'No Data');
        $("#chartModal").modal('show');
        // setTimeOut for 'chart_div' to load before drawing the chart, else y axis will lose value scale
        setTimeout(() => google.charts.setOnLoadCallback(drawColumnChart(monthlyAvgs)), 150)
    })

    // circle indicate level of concentration 
    const circle = new google.maps.Circle({
        map: map,
        strokeWeight: 0,
        fillColor: getColor(sttAvg),        // color depends on the average value
        fillOpacity: 0.45,
        center: { lat: lat, lng: long },
        radius: 0.1 * count,                // radius is proportional to amount of data collected
    })

    circles.push(circle)                    // push to arrays to later reload markers
    markers.push(marker)
}

// feature: reload Markers on button click - source code: http://jsfiddle.net/upsidown/p646xmcr/
const reloadMarkers = () => {
    document.getElementById('loader').style.display = 'block'       // display loader
    year = document.getElementById('year').value;                   // get user selected year and pollutant
    pollutant = document.getElementById('pollutant').value

    markers.forEach(m => m.setMap(null))
    circles.forEach(c => c.setMap(null))
    markers = []
    circles = []
    setMarkers()
}
// end source

const drawColumnChart = (monthlyAvgs) => {
    // add 'month' column and header row
    const columnsData = monthlyAvgs.map((avg, index) => [index + 1, avg, getColor(avg)])
    columnsData.unshift(['Month', 'Concentration (µg/m³)', { role: 'style' }])

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
        'title': 'Monthly average ' + pollutant.toUpperCase() + ' concentration measured by station, in µg/m³',
        hAxis: { title: 'Month', gridlines: { count: 6 } },
        vAxis: { title: 'Concentration (µg/m³)', gridlines: { count: 6 } },
        'width': 600,
        'height': 300,
        legend: { position: 'none' },
        fontName: 'Roboto'
    };

    // Instantiate and draw chart, passing in options.
    let chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
    chart.draw(view, options);
}

const getAverageMonthly = async (sttID) => {
    const nextYear = getUnixTime(parseInt(year) + 1, 1)
    const yearUnix = getUnixTime(year, 1)

    // 2d array to store monthly [sum, count]
    let sumCount = Array(12).fill(0).map(_ => Array(2).fill(0))
    // fetch the xml file based on station id
    await fetch('../data_' + sttID + '.xml')
        .then(async res => {
            await res.text().then(async xml => {
                xmlContent = xml
                let parser = new DOMParser()
                let xmlDOM = parser.parseFromString(xmlContent, 'application/xml')
                let recs = xmlDOM.querySelectorAll('rec');

                // loop through records
                recs.forEach(rec => {
                    let ts = parseInt(rec.getAttribute('ts'))
                    if (ts >= yearUnix && ts < nextYear) {
                        const reading = rec.getAttribute(pollutant)
                        // if reading is not empty string
                        if (reading) {
                            for (let i = 1; i <= 12; i++) {     // loop through 12 months                            
                                if (i !== 12 && ts >= getUnixTime(year, i) && ts < getUnixTime(year, i + 1)
                                    || i === 12 && ts >= getUnixTime(year, i) && ts < getUnixTime(year + 1, 1)) {
                                    sumCount[i - 1][0] += parseFloat(reading)
                                    sumCount[i - 1][1]++
                                    break
                                }
                            }
                        }
                    }
                })
            })
        })
        .catch(_ => console.error(err));

    const monthlyAvg = sumCount.map(month => month[1] ? parseFloat((month[0] / month[1]).toFixed(2)) : 0)
    const totalCount = sumCount.reduce((a, b) => a + b[1], 0)
    return [monthlyAvg, totalCount]
}

const getUnixTime = (year, month) => {
    const date = new Date(year.toString() + '-' + month + '-01')
    return date.getTime() / 1000
}

const getColor = (reading) => {
    return (dangerColors[Math.floor(reading / 100)])
}
