<?php
extract($_POST);

if (isset($drawChart)) {
    $sttId = $_POST['station'];
    $date = $_POST['date'];
} else {
    $sttId = 188;
    $date = '2015-01-01';
}

function selected($val)
{
    global $sttId;
    return ($sttId == $val) ? print("selected") : "";
}

?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Task 3b: Line Chart Visualization</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
</head>

<body style="font-family: Roboto;">
    <form method="POST" action="<?php print $_SERVER['PHP_SELF']; ?>" style="text-align: center;">
        <label for="station">Select Station</label>
        <select name="station" id="station">
            <option value="188" <?php selected(188); ?>>188 - AURN Bristol Centre</option>
            <option value="203" <?php selected(203); ?>>203 - Brislington Depot</option>
            <option value="206" <?php selected(206); ?>>206 - Rupert Street</option>
            <option value="209" <?php selected(209); ?>>209 - IKEA M32</option>
            <option value="213" <?php selected(213); ?>>213 - Old Market</option>
            <option value="215" <?php selected(215); ?>>215 - Parson Street School</option>
            <option value="228" <?php selected(228); ?>>228 - Temple Meads Station</option>
            <option value="270" <?php selected(270); ?>>270 - Wells Road</option>
            <option value="271" <?php selected(271); ?>>271 - Trailer Portway P&R</option>
            <option value="375" <?php selected(375); ?>>375 - Newfoundland Road Police Station</option>
            <option value="395" <?php selected(395); ?>>395 - Shiner's Garage</option>
            <option value="452" <?php selected(452); ?>>452 - AURN St Pauls</option>
            <option value="447" <?php selected(447); ?>>447 - Bath Road</option>
            <option value="459" <?php selected(459); ?>>459 - Cheltenham Road \ Station Road</option>
            <option value="463" <?php selected(463); ?>>463 - Fishponds Road</option>
            <option value="481" <?php selected(481); ?>>481 - CREATE Centre Roof'</option>
            <option value="500" <?php selected(500); ?>>500 - Temple Way</option>
            <option value="501" <?php selected(501); ?>>501 - Colston Avenue</option>
        </select>

        <label for="date" style="margin-left: 20px">Choose a date: </label>
        <input type="date" name="date" min="2015-01-01" max="2019-12-31" value=<?php print($date); ?>>

        <button name="drawChart" style="margin-left: 20px">Submit</button>
    </form>

    <div id="chart_div" style="width: 900px; height: 500px; margin: 0 auto;"></div>

    <div style="color: #bd0f0f; font-weight: 600; text-align: center;">
        <small><em>* An hour with no circle point means the station had no reading for that hour.</em></small> <br>
        <small><em>* A blank graph means the station had no reading for the date chosen.</em></small>
    </div>
</body>

</html>