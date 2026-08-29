<?php
	$sekarang = "32.73.10.1006";
	// Get API URL
	$api_url = "https://api.bmkg.go.id/publik/prakiraan-cuaca?adm4=".$sekarang;
	$response_body = @file_get_contents($api_url);

	// Check if fail
	if ($response_body === false) {
	    die("ERROR: Gagal mengambil data.");
	}

	// Decode String JSON
	$data = json_decode($response_body, true);

	if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
	    die(
	        "ERROR: Data bukan format JSON yang valid. " .
	            htmlspecialchars(json_last_error_msg())
	    );
	}

	// Set header
	header("Content-Type: text/html; charset=utf-8");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prakiraan Cuaca BMKG</title>
    <style>
        img { width: 20px; height: 20px; vertical-align: middle; margin-left: 5px; }
    </style>
</head>
<body>

<h1>Prakiraan Cuaca BMKG</h1>

<?php
// Location
if (isset($data["lokasi"]["desa"]) && isset($data["lokasi"]["kecamatan"])) {
    echo "Desa/Kelurahan: " .
        htmlspecialchars($data["lokasi"]["desa"]);
    echo "<br/>";
    echo "Kecamatan: " .
        htmlspecialchars($data["lokasi"]["kecamatan"] ?? "N/A") .
        "<br/>";
    echo "Kota/Kabupaten: " .
        htmlspecialchars($data["lokasi"]["kotkab"] ?? "N/A") .
        "<br>";    
} else {
    echo "Lokasi Tidak Ditemukan";
}

// Weather forecast data
echo "<br/>";
echo "Detail Prakiraan Cuaca hari ini:";
if (isset($data["data"][0]["cuaca"]) && is_array($data["data"][0]["cuaca"])) {
    foreach ($data["data"][0]["cuaca"] as $index_hari => $prakiraan_harian) {
    	if ($index_hari !=0){
    		return;
    	}

        echo "<ul>";
        if (is_array($prakiraan_harian)) {
            foreach ($prakiraan_harian as $prakiraan) {
                $waktu_lokal = isset($prakiraan["local_datetime"])
                    ? htmlspecialchars($prakiraan["local_datetime"])
                    : "N/A";
                $deskripsi = isset($prakiraan["weather_desc"])
                    ? htmlspecialchars($prakiraan["weather_desc"])
                    : "N/A";
                $alt_text = isset($prakiraan["weather_desc"])
                    ? htmlspecialchars(
                        $prakiraan["weather_desc"],
                        ENT_QUOTES,
                        "UTF-8",
                    )
                    : "Ikon Cuaca";
                $raw_img_url = isset($prakiraan["image"])
                    ? $prakiraan["image"]
                    : "";
                $img_url_processed = "";

                if (!empty($raw_img_url)) {
                    $img_url_processed = str_replace(" ", "%20", $raw_img_url);
                }

                echo "<li>";
                echo "<strong>Jam:</strong> " . $waktu_lokal . " | ";
                echo "<strong>Cuaca:</strong> " . $deskripsi . " ";
                if (
                    $img_url_processed &&
                    filter_var($img_url_processed, FILTER_VALIDATE_URL)
                ) {
                    echo '<img src="' .
                        $img_url_processed .
                        '" alt="' .
                        $alt_text .
                        '" title="' .
                        $alt_text .
                        '"> | ';
                }
            }
        } else {
            echo "<li>Data tidak valid.</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<p>Struktur data prakiraan cuaca tidak ditemukan.</p>";
}

// Debugging $data
	/*
	echo "<pre>";
	print_r($data);
	echo "</pre>";
	*/
?>
</body>
</html>
