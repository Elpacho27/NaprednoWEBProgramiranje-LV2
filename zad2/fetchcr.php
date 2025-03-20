<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Directories</title>
</head>
<body>
    <?php
    session_start();

    // provjera txt
    $checkTxt = function ($value) {
        return (pathinfo($value, PATHINFO_EXTENSION) === 'txt');
    };

    // provjera mape
    if (!is_dir("uploads/")) {
        echo "<p>Nema dostupnih datoteka za dekripciju.</p>";
        die();
    }

    // dohvat datoteka
    $files = array_diff(scandir("uploads/"), array('..', '.'));
    $files = array_filter($files, $checkTxt);

    // prikaz datoteka
    if (count($files) === 0) {
        echo "<p>Nema dostupnih datoteka za dekripciju.</p>";
    } else {
        echo "<ul>";
        foreach ($files as $file) {
            $filenameWithoutExt = substr($file, 0, strlen($file) - 4);
            echo "<li> <a href=\"download.php?file=$filenameWithoutExt\">$filenameWithoutExt</a></li>";
        }
        echo "</ul>";
    }
    ?>
</body>
</html>