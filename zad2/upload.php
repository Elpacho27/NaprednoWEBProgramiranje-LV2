<?php
session_start();

//dohvaćanje naziva prenesene datoteke
$filename = $_FILES['file']['name'];

//dostavljanje lokacije za spremanje
$location = "uploads/" . $filename;
$fileType = pathinfo($location, PATHINFO_EXTENSION);
$validExtensions = array("jpg", "jpeg", "png", "pdf");

//drovjera je li format datoteke dozvoljen
if (!in_array(strtolower($fileType), $validExtensions)) {
    echo "<p>Neispravan format datoteke ($fileType).</p>";
    die();
}

//učitavanje sadržaja datoteke
$content = file_get_contents($_FILES['file']['tmp_name']);

//postavljanje ključa za enkripciju
$encryptionKey = md5('kljuc za enkripciju');
$cipher = "AES-128-CTR";
$ivLength = openssl_cipher_iv_length($cipher);
$options = 0;

//generiranje inicijalizacijskog vektora (IV)
$encryptionIV = random_bytes($ivLength);

//enkripcija sadržaja
$encryptedData = openssl_encrypt($content, $cipher, $encryptionKey, $options, $encryptionIV);

//kodiranje podataka u base64
$encodedData = base64_encode($encryptedData);
$_SESSION['iv'] = $encryptionIV;

//dobivanje naziva datoteke bez ekstenzije
$filenameWithoutExt = substr($filename, 0, strpos($filename, "."));

//provjera i kreiranje direktorija ako ne postoji
if (!is_dir("uploads/")) {
    if (!mkdir("uploads/", 0777, true)) {
        die("<p>Neuspjelo kreiranje direktorija uploads.</p>");
    }
}

//spremanje enkriptiranih podataka u datoteku
$serverFilename = "uploads/${filenameWithoutExt}.$fileType.txt";
file_put_contents($serverFilename, $encodedData);

//potvrda uspješnog prijenosa
echo "Datoteka je uspješno prenesena.";
