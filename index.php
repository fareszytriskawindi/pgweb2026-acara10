<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "pertahanan";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM pertahanan_keamanan";
$result = $conn->query($sql);

$data_features = array();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ACARA 10 - Web GIS LeafletJS</title>
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.5.1/dist/leaflet.css">
    
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #faf5f7;
            color: #4a4a4a;
        }
        
        .header-banner {
            background: linear-gradient(135deg, #ffcbf2 0%, #a0c4ff 100%);
            color: #3d3a45;
            padding: 20px 25px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(255, 203, 242, 0.4);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header-banner h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            color: #434b6e;
        }
        .btn-add {
            background-color: #b9fbc0;
            color: #2d5a37;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
            box-shadow: 0 3px 8px rgba(185, 251, 192, 0.6);
            transition: all 0.3s ease;
        }
        .btn-add:hover {
            background-color: #98f5a5;
            transform: translateY(-2px);
        }

        .content-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            height: calc(100vh - 125px);
        }

        .card {
            background: #ffffff;
            border-radius: 15px;
            padding: 18px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            border: 1px solid #f0e6ed;
            display: flex;
            flex-direction: column;
        }
        .card h3 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #5c6b9c;
            border-bottom: 2px solid #f7d6e0;
            padding-bottom: 10px;
            font-size: 17px;
        }

        .table-responsive {
            overflow-y: auto;
            flex-grow: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }
        th {
            background-color: #c8e7ff;
            color: #2b4560;
            position: sticky;
            top: 0;
            padding: 12px 10px;
            text-align: left;
            font-weight: 600;
        }
        td {
            padding: 11px 10px;
            border-bottom: 1px solid #f2f5f8;
            color: #555;
        }
        tr:hover {
            background-color: #fff0f5;
        }

        .btn-action {
            padding: 5px 10px;
            border-radius: 12px;
            text-decoration: none;
            font-size: 11.5px;
            font-weight: 600;
            display: inline-block;
        }
        .btn-edit {
            background-color: #ffc6ff;
            color: #722f75;
        }
        .btn-edit:hover {
            background-color: #f7aef8;
        }
        .btn-delete {
            background-color: #ffadad;
            color: #7a2828;
        }
        .btn-delete:hover {
            background-color: #ff8585;
        }

        #map {
            width: 100%;
            height: 100%;
            border-radius: 12px;
            border: 2px solid #e7ecef;
        }

        @media (max-width: 900px) {
            .content-container {
                grid-template-columns: 1fr;
                height: auto;
            }
            #map {
                height: 420px;
            }
        }
    </style>
</head>
<body>

    <div class="header-banner">
        <h2>Web GIS Fasilitas Pertahanan & Keamanan</h2>
        <a href="input/index.html" class="btn-add">+ Input Data Baru</a>
    </div>

    <div class="content-container">
        
        <div class="card">
            <h3>Daftar Data Geospasial</h3>
            <div class="table-responsive">
                <?php if ($result && $result->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Toponim</th>
                                <th>Long</th>
                                <th>Lat</th>
                                <th>ObjectID</th>
                                <th>Objek</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <?php $data_features[] = $row; ?>
                                <tr>
                                    <td><strong style="color: #4a4e69;"><?php echo htmlspecialchars($row["toponim"]); ?></strong></td>
                                    <td><?php echo $row["longitude"]; ?></td>
                                    <td><?php echo $row["latitude"]; ?></td>
                                    <td><?php echo $row["objectid"]; ?></td>
                                    <td><?php echo htmlspecialchars($row["objek"]); ?></td>
                                    <td>
                                        <a href="edit/edit.php?id=<?php echo $row["id"]; ?>" class="btn-action btn-edit">Edit</a>
                                        <a href="delete.php?id=<?php echo $row["id"]; ?>" class="btn-action btn-delete" onclick="return confirm('Yakin ingin menghapus data ini?');">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="text-align: center; color: #aaa; margin-top: 20px;">Data tidak ditemukan.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <h3>Visualisasi Peta Interaktif</h3>
            <div id="map"></div>
        </div>

    </div>

    <script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js"></script>
    <script>
        var map = L.map('map').setView([-7.766715, 110.377460], 11);

        var basemap1 = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri'
        });

        var basemap2 = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: 'Map data &copy; Google'
        });

        basemap2.addTo(map);

        var markerGgroup = L.layerGroup();
        var dbData = <?php echo json_encode($data_features); ?>;

        dbData.forEach(function(item) {
            var lat = parseFloat(item.latitude);
            var lng = parseFloat(item.longitude);

            if (!isNaN(lat) && !isNaN(lng)) {
                var marker = L.marker([lat, lng]);
                var popupContent = "<div style='font-family: sans-serif; font-size: 12px; color: #333;'>" +
                                   "<b style='color: #5c6b9c; font-size: 13px;'>" + item.toponim + "</b><br>" +
                                   "<b>Jenis:</b> " + item.objek + "<br>" +
                                   "<b>ObjectID:</b> " + item.objectid + "<br>" +
                                   "<b>Koordinat:</b> " + lat + ", " + lng + 
                                   "</div>";
                
                marker.bindPopup(popupContent);
                markerGroup.addLayer(marker);
            }
        });

        markerGroup.addTo(map);

        L.control.layers({
            "Google Street": basemap2,
            "Esri Imagery": basemap1
        }, {
            "Fasilitas Pertahanan": markerGroup
        }, {collapsed: false}).addTo(map);

        L.control.scale({maxWidth: 150, position: 'bottomright'}).addTo(map);
    </script>
</body>
</html>
<?php $conn->close(); ?>