<?php
extract($_POST);

if (isset($drawChart)) {
    $year = $_POST['year'];
    $sttId = $_POST['station'];
} else {
    $year = 2015;
    $sttId = 188;
}

function selected($isYear, $val){
    global $year, $sttId;
    if ($isYear)
        return ($year == $val) ? print("selected") : "";
    else
        return ($sttId == $val) ? print("selected") : "";
}

?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Task 3a: Scatter Chart Visualization</title>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

</head>

<body style="font-family: Roboto;">
    <form method="POST" action="<?php print $_SERVER['PHP_SELF']; ?>" style="text-align: center;">
        <label for="year">Select Year</label>
        <select name="year" id="year">
            <option value="2015" <?php selected(true, 2015); ?>>2015</option>
            <option value="2016" <?php selected(true, 2016); ?>>2016</option>
            <option value="2017" <?php selected(true, 2017); ?>>2017</option>
            <option value="2018" <?php selected(true, 2018); ?>>2018</option>
            <option value="2019" <?php selected(true, 2019); ?>>2019</option>
        </select>

        <label for="station" style="margin-left: 20px">Select Station</label>
        <select name="station" id="station">
            <option value="188" <?php selected(false, 188); ?>>188 - AURN Bristol Centre</option>
            <option value="203" <?php selected(false, 203); ?>>203 - Brislington Depot</option>
            <option value="206" <?php selected(false, 206); ?>>206 - Rupert Street</option>
            <option value="209" <?php selected(false, 209); ?>>209 - IKEA M32</option>
            <option value="213" <?php selected(false, 213); ?>>213 - Old Market</option>
            <option value="215" <?php selected(false, 215); ?>>215 - Parson Street School</option>
            <option value="228" <?php selected(false, 228); ?>>228 - Temple Meads Station</option>
            <option value="270" <?php selected(false, 270); ?>>270 - Wells Road</option>
            <option value="271" <?php selected(false, 271); ?>>271 - Trailer Portway P&R</option>
            <option value="375" <?php selected(false, 375); ?>>375 - Newfoundland Road Police Station</option>
            <option value="395" <?php selected(false, 395); ?>>395 - Shiner's Garage</option>
            <option value="452" <?php selected(false, 452); ?>>452 - AURN St Pauls</option>
            <option value="447" <?php selected(false, 447); ?>>447 - Bath Road</option>
            <option value="459" <?php selected(false, 459); ?>>459 - Cheltenham Road \ Station Road</option>
            <option value="463" <?php selected(false, 463); ?>>463 - Fishponds Road</option>
            <option value="481" <?php selected(false, 481); ?>>481 - CREATE Centre Roof'</option>
            <option value="500" <?php selected(false, 500); ?>>500 - Temple Way</option>
            <option value="501" <?php selected(false, 501); ?>>501 - Colston Avenue</option>
        </select>

        <button name="drawChart" style="margin-left: 20px" >Submit</button>
    </form>
    <div id="chart_div" style="width: 800px; height: 500px; margin: 0 auto;"></div>

    <div style="text-align: center;">
        <strong><em style="color: #cf2323"><small>*Empty graph means there was no reading.</small></em></strong>
    </div>
</body>

</html>