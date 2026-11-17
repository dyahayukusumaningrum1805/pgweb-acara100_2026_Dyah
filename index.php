<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sebaran Lokasi Biopori</title>
    
    <!-- 1. Panggil CSS Leaflet (Dari Lampiran) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.5.1/dist/leaflet.css">
    
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 6px 10px;
        }
        
        /* Ukuran wadah peta */
        #map {
            height: 450px;
            width: 100%;
            margin-top: 20px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>

<?php
// === KODE PHP UTAMA KAMU ===
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "biopori";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM `sebaran biopori`";
$result = $conn->query($sql);

echo "<h1>Sebaran Lokasi Biopori</h1>";

echo "<a href='input/index.html'>Tambahkan</a>";

$locations = array();

if ($result->num_rows > 0) {
    echo "<table border='1px'><tr>
            <th>Kecamatan</th>
            <th>Nama Bank</th>
            <th>Alamat</th>
            <th>Jumlah</th>
            <th>Latitude</th>
            <th>Longitude</th>
            <th colspan='2'>Aksi</th>
        </tr>";

    while($row = $result->fetch_assoc()) {
        echo "<tr><td>".$row["kecamatan"]."</td>".
                "<td>".$row["nama bank"]."</td>".
                "<td>".$row["alamat"]."</td>".
                "<td align='right'>".$row["jumlah"]."</td>".
                "<td align='right'>".$row["latitude"]."</td>".
                "<td align='right'>".$row["longitude"]."</td>".
                "<td><a href=delete.php?id=".$row["id"].">hapus</a></td>".
                "<td><a href=edit/index.php?id=".$row["id"].">edit</a></td>".
            "</tr>";
            
        if (!empty($row["latitude"]) && !empty($row["longitude"])) {
            $locations[] = $row;
        }
    }
    echo "</table>";
} else {
    echo "0 results";
}

$conn->close();
?>

    <!-- Wadah Peta -->
    <h2>Peta Sebaran Lokasi</h2>
    <div id="map"></div>

    <!-- Panggil JS Leaflet (Dari Lampiran) -->
    <script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js"></script>

    <script>
        /* Initial Map */
        var map = L.map('map').setView([-7.78044, 110.355], 13);

        /* Tile Basemap 1: Esri Imagery */
        var basemap1 = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri'
        });

        /* Tile Basemap 2: Google Street */
        var basemap2 = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: 'Map data &copy; Google'
        });

        basemap2.addTo(map);

        var overlayMaps = {};
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
        /* LOOPING DATA DARI MYSQL DENGAN TOOLTIP HOVER */
        <?php foreach ($locations as $loc): ?>
            var lat = parseFloat("<?php echo $loc['latitude']; ?>");
            var lng = parseFloat("<?php echo $loc['longitude']; ?>");
            
            if (!isNaN(lat) && !isNaN(lng)) {
                var marker = L.marker([lat, lng]).addTo(map);
                
                // MENGGUNAKAN bindTooltip (Muncul otomatis saat kursor mengarah/hover ke marker)
                marker.bindTooltip(
                    "<b><?php echo addslashes($loc['nama bank']); ?></b><br>" +
                    "Kecamatan: <?php echo addslashes($loc['kecamatan']); ?><br>" +
                    "Alamat: <?php echo addslashes($loc['alamat']); ?><br>" +
                    "Jumlah: <?php echo $loc['jumlah']; ?>",
                    {
                        direction: 'top', // Posisi tooltip di atas marker
                        sticky: true     // Tooltip mengikuti gerak kursor mouse
                    }
                );

                overlayMaps["<?php echo addslashes($loc['nama bank']); ?>"] = marker;
            }
        <?php endforeach; ?>

        /* Control Layer & Scale Bar dari Lampiran */
        var baseMaps = {
            "Google Street": basemap2,
            "Esri Imagery": basemap1
        };

        L.control.layers(baseMaps, overlayMaps, {collapsed: false}).addTo(map);

        L.control.scale({
            maxWidth: 150, 
            position: 'bottomright'
        }).addTo(map);
    </script>

</body>
</html>