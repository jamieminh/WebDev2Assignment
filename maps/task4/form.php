<?php

extract($_POST);

if (isset($drawMap)) {
    $pollutant = $_POST['pollutant'];
    $year = $_POST['year'];
} else {
    $pollutant = 'nox';
    $year = '2015';
}


function selected($isYear, $val)
{
    global $year, $pollutant;
    if ($isYear)
        return ($year == $val) ? print("selected") : "";
    else
        return ($pollutant == $val) ? print("selected") : "";
}

?>


<!DOCTYPE html>
<html>

<head>
    <title>Task 4: Map Visualisation</title>
    <scrip src="https://polyfill.io/v3/polyfill.min.js?features=default"></scrip>
    <link rel="stylesheet" type="text/css" href="./style.css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Maven+Pro:wght@400;500;600&display=swap" rel="stylesheet">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
</head>

<body>
    <div id="map"></div>

    <div class="current-map-info">
        <p id="map-info"></p>
    </div>

    <!-- source: https://therichpost.com/open-bootstrap-modal-popup-google-map-marker-click/ -->
    <!-- Modal	 -->
    <div class="modal fade" id="chartModal" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <div id="chart_div"></div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- end source -->

    <div class="map-note">
        <p><em>* Select a marker to see monthly average of that station.</em></p>
    </div>


    <div class="notes">
        <div class="color-code">
            <div class="group group-none">
                <div>
                    <div class="color-0"></div>
                </div>

                <p class="none">No record</p>
            </div>

            <div class="group group-low">
                <div>
                    <div class="color-1"><small>0-100</small></div>
                    <div class="color-2"><small>101-200</small></div>
                </div>

                <p class="low">Safe</p>
            </div>

            <div class="group group-low">
                <div>
                    <div class="color-3"><small>201-300</small></div>
                    <div class="color-4"><small>301-400</small></div>
                </div>

                <p class="mod">Unsafe for sensitive groups</p>
            </div>

            <div class="group group-high">
                <div>
                    <div class="color-5"><small>401-500</small></div>
                    <div class="color-6"><small>501-600</small></div>
                </div>

                <p class="high">Unsafe</p>
            </div>

            <div class="group group-danger">
                <div>
                    <div class="color-7"><small>over 600</small></div>
                </div>

                <p class="danger">Hazardous</p>
            </div>

        </div>


        <div class="form">
            <h4>Choose a year and pollutant: </h4>

            <form method="POST" action="<?php print $_SERVER['PHP_SELF']; ?>">
                <select name="year" id="year">
                    <option value="2015" <?php selected(true, 2015); ?>>2015</option>
                    <option value="2016" <?php selected(true, 2016); ?>>2016</option>
                    <option value="2017" <?php selected(true, 2017); ?>>2017</option>
                    <option value="2018" <?php selected(true, 2018); ?>>2018</option>
                    <option value="2019" <?php selected(true, 2019); ?>>2019</option>
                </select>

                <select name="pollutant" id="pollutant">
                    <option value="no" <?php selected(false, 'no'); ?>>NO</option>
                    <option value="nox" <?php selected(false, 'nox'); ?>>NOX</option>
                    <option value="no2" <?php selected(false, 'no2'); ?>>NO2</option>
                </select>

                <button name='drawMap'>See</button>
            </form>
        </div>
    </div>



    <!-- Async script executes immediately and must be after any DOM elements used in callback. -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBfDHwRe-MPRC7fXBe5mFsh0KHvmfR8GoQ&callback=initMap&libraries=&v=weekly" async></script>
</body>

</html>