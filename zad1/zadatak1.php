<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DB Backup</title>
</head>
<body>
    <?php
    
    //funkcija za dohvaćanje imena stupca
    $getColumnName = function ($value) {
        return $value->name;
    };

    //naziv baze podataka
    $databaseName = "radovi";
    $directory = "backup/$databaseName";

    //provjera i stvaranje direktorija ako ne postoji
    if (!is_dir($directory)) {
        if (!mkdir($directory, 0777, true)) {
            die("<p>Neuspjelo stvaranje direktorija za sigurnosnu kopiju.</p></body></html>");
        }
    }

    //vrijeme za dodavanje u naziv datoteke
    $currentTime = time();
    
    //povezivanje s bazom podataka
    $connection = mysqli_connect("localhost", "root", "", $databaseName)
    or die("<p>Neuspjelo povezivanje s bazom podataka '$databaseName'.</p></body></html>");
    
    $files = [];
    $result = mysqli_query($connection, "SHOW TABLES");
    
    if (mysqli_num_rows($result) > 0) {
        echo "<p>Početak sigurnosne kopije baze podataka '$databaseName'.</p>";
        
        while (list($table) = mysqli_fetch_array($result, MYSQLI_NUM)) {
            $query = "SELECT * FROM $table";
            $columns = array_map($getColumnName, $connection->query($query)->fetch_fields());
            $tableResult = mysqli_query($connection, $query);
            
            if (mysqli_num_rows($tableResult) > 0) {
                $fileName = "{$table}_{$currentTime}";
                $filePath = "$directory/$fileName.txt";
                
                //otvaranje datoteke za zapis
                if ($fp = fopen($filePath, "w")) {
                    $files[] = $fileName;
                    
                    while ($row = mysqli_fetch_array($tableResult, MYSQLI_NUM)) {
                        $rowText = "INSERT INTO $table (" . implode(", ", $columns) . ") VALUES ('" . implode("', '", $row) . "');\n";
                        fwrite($fp, $rowText);
                    }
                    fclose($fp);
                    
                    echo "<p>Tablica '$table' uspješno pohranjena.</p>";

                    //sažimanje datoteke
                    $gzipPath = "$directory/{$fileName}.sql.gz";
                    if ($fp = gzopen($gzipPath, 'w9')) {
                        $content = file_get_contents($filePath);
                        gzwrite($fp, $content);
                        unlink($filePath); //brisanje originalne datoteke nakon sažimanja
                        gzclose($fp);
                        echo "<p>Tablica '$table' uspješno sažeta.</p>";
                    } else {
                        echo "<p>Pogreška pri sažimanju tablice '$table'.</p>";
                    }
                } else {
                    echo "<p>Neuspjelo otvaranje datoteke '$filePath' za zapis.</p>";
                    break;
                }
            }
        }
    } else {
        echo "<p>Baza podataka '$databaseName' ne sadrži tablice za sigurnosnu kopiju.</p>";
    }
    ?>
</body>
</html>
