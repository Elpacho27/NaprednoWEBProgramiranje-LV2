<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link rel="stylesheet" href="style.css">
    <title>User Profiles</title>
</head>
<body>
<?php
    //učitavanje XML datoteke
    $xmlData = simplexml_load_file("LV2.xml");

    //funkcija za provjeru vrijednosti
    function getValueOrDefault($value) {
        return isset($value) ? $value : "";
    }

    echo "<div class='container'>
            <div class='main-body'>
                <div class='row gutters-sm'>";
    
    foreach ($xmlData->record as $user) {
        $userId = getValueOrDefault($user->id);
        $firstName = getValueOrDefault($user->ime);
        $lastName = getValueOrDefault($user->prezime);
        $email = getValueOrDefault($user->email);
        $gender = getValueOrDefault($user->spol);
        $profileImage = getValueOrDefault($user->slika);
        $biography = getValueOrDefault($user->zivotopis);

        echo "<div class='col-md-4 mb-3'>
                 <div class='card'>
                    <div class='card-body'>
                        <div class='d-flex flex-column align-items-center text-center'>
                            <img src='$profileImage' alt='Profile image' class='rounded-circle' width='150'>
                            <div class='mt-3'>
                                <h4>$firstName $lastName</h4>
                                <p class='text-secondary mb-1'>$gender</p>
                                <p class='text-muted font-size-sm'>$email</p>
                                <p class='text-muted font-size-sm'>$biography</p>
                            </div>
                        </div>
                    </div>
                </div>
              </div>";
    }
    
    echo "</div>
        </div>
    </div>";
?>
</body>
</html>
