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
    <title>Judul</title>
</head>
<body>

<h1>Judul</h1>

<?php
// Location
if (isset($data["lokasi"]["desa"]) && isset($data["lokasi"]["kecamatan"])) {
    echo "Desa/Kelurahan: " .
        htmlspecialchars($data["lokasi"]["desa"]);
} else {
    echo "Lokasi Tidak Ditemukan";
}

?>
</body>
</html>
