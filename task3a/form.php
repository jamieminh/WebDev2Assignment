<?php
extract($_POST);

$hour = 0;

if (isset($drawChart)) {
    $year = $_POST['year'];
    $sttId = $_POST['station'];
    $hour = $_POST['hour'];
} else {
    $year = 2015;
    $sttId = 203;
    $hour = 0;
}

function selected($variable, $value)
{
    return ($variable == $value) ? print("selected") : "";
}



?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Task 3a: Scatter Chart Visualisation</title>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <link rel="stylesheet" href="../task3Styles.css" />

</head>

<body>

    <div id="chart_div"></div>

    <div class="footnote">
        <strong><em><small>*Empty graph means there was no reading.</small></em></strong>
    </div>

    <form method="POST" action="<?php print $_SERVER['PHP_SELF']; ?>">
        <div class="InputItem">
            <label for="year">Select Year: </label>
            <select name="year" id="year">
                <option value="2015" <?php selected($year, 2015); ?>>2015</option>
                <option value="2016" <?php selected($year, 2016); ?>>2016</option>
                <option value="2017" <?php selected($year, 2017); ?>>2017</option>
                <option value="2018" <?php selected($year, 2018); ?>>2018</option>
                <option value="2019" <?php selected($year, 2019); ?>>2019</option>
            </select>
        </div>
        <div class="InputItem">
            <label for="station">Select Station: </label>
            <select name="station" id="station">
                <option value="203" <?php selected($sttId, 203); ?>>203 - Brislington Depot</option>
                <option value="206" <?php selected($sttId, 206); ?>>206 - Rupert Street</option>
                <option value="215" <?php selected($sttId, 215); ?>>215 - Parson Street School</option>
                <option value="270" <?php selected($sttId, 270); ?>>270 - Wells Road</option>
                <option value="375" <?php selected($sttId, 375); ?>>375 - Newfoundland Road Police Station</option>
                <option value="452" <?php selected($sttId, 452); ?>>452 - AURN St Pauls</option>
                <option value="463" <?php selected($sttId, 463); ?>>463 - Fishponds Road</option>
                <option value="500" <?php selected($sttId, 500); ?>>500 - Temple Way</option>
                <option value="501" <?php selected($sttId, 501); ?>>501 - Colston Avenue</option>
            </select>
        </div>
        <div class="InputItem">
            <label for="hour">Select Hour (0 - 23): </label>
            <input type="number" name="hour" min="0" max="23" value="<?php print($hour) ?>" />
        </div>

        <div class="SubmitBtn">
            <button name="drawChart">Submit</button>
        </div>

    </form>

</body>

</html>