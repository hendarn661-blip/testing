<?php
// Mengambil protokol (http atau https)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

// Mengambil nama domain atau host (misal: localhost atau ://domain.com)
$domain = $_SERVER['HTTP_HOST'];

// Mengambil folder tempat projek Anda berada
// Jika projek berada di root domain, ini akan menghasilkan "/"
$project_folder = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

// Menggabungkan semuanya menjadi base_url yang dinamis
$base_url = $protocol . $domain . $project_folder;

// Definisikan sebagai konstanta agar bisa diakses di mana saja
define('BASE_URL', $base_url);
?>
