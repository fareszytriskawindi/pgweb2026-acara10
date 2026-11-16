<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "pertahanan";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'];
$sql = "SELECT * FROM pertahanan_keamanan WHERE id = $id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Data - Acara 10</title>
    <script>
        function validateForm() {
            var objectid = document.getElementById("objectid").value;
            if (isNaN(objectid) || objectid < 1) {
                alert("Object ID harus berupa angka positif (minimal 1)!");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <h2>Form Edit Data Geospasial</h2>
    <form action="../update.php" method="POST" onsubmit="return validateForm()">
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

        <label for="toponim">Toponim:</label><br>
        <input type="text" id="toponim" name="toponim" value="<?php echo htmlspecialchars($row['toponim']); ?>" required><br><br>

        <label for="long">Longitude:</label><br>
        <input type="text" id="long" name="longitude" value="<?php echo $row['longitude']; ?>" required><br><br>

        <label for="lat">Latitude:</label><br>
        <input type="text" id="lat" name="latitude" value="<?php echo $row['latitude']; ?>" required><br><br>

        <label for="objectid">Object ID:</label><br>
        <input type="text" id="objectid" name="objectid" value="<?php echo $row['objectid']; ?>" required><br><br>

        <label for="objek">Objek:</label><br>
        <input type="text" id="objek" name="objek" value="<?php echo htmlspecialchars($row['objek']); ?>" required><br><br>

        <input type="submit" value="Update Data">
        <a href="../index.php">Batal</a>
    </form>
</body>
</html>
<?php $conn->close(); ?>