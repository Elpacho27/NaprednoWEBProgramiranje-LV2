<?php
session_start();

//dohvaćanje naziva datoteke iz GET parametra
$filename = $_GET['file'];

//postavljanje ključa za dekripciju
$decryptionKey = md5('kljuc za enkripciju');
$cipher = "AES-128-CTR";
$options = 0;

//dohvaćanje inicijalizacijskog vektora iz sesije
$decryptionIV = $_SESSION['iv'];

//učitavanje šifriranog sadržaja
$encryptedFilePath = "uploads/$filename.txt";
$encryptedContent = file_get_contents($encryptedFilePath);

//dekodiranje i dekripcija podataka
$decodedContent = base64_decode($encryptedContent);
$decryptedData = openssl_decrypt($decodedContent, $cipher, $decryptionKey, $options, $decryptionIV);

//spremanje dekriptiranih podataka u privremenu datoteku
$decryptedFilePath = "uploads/$filename";
file_put_contents($decryptedFilePath, $decryptedData);

//osvježavanje informacija o datotekama
clearstatcache();

//provjera postoji li dekriptirana datoteka prije slanja korisniku
if (file_exists($decryptedFilePath)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($decryptedFilePath) . '"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($decryptedFilePath));
    
    ob_clean();
    flush();
    readfile($decryptedFilePath, true);
    
    //brisanje privremene datoteke nakon preuzimanja
    unlink($decryptedFilePath);
    
    die();
}