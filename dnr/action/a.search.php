<?php
// Include your database connection code here
// For example: require_once('db_connection.php');
session_start();
//use lib\dbpdo;

//include '../../lib/dbpdo.php';
include '../../lib/database.php';
include 'mappa.php';

//$db = new dbpdo();
//$pdo = $db->getpdo();
$db = new Database();

// Check if the search query parameter is set
if (isset($_GET['charity_search']) || $_GET['charity_search'] != "") {
    $searchQuery = $_GET['charity_search'];

    // Sanitize the input to prevent SQL injection (you should use prepared statements for better security)
    $searchQuery = htmlspecialchars($searchQuery);

    // Perform a database query to search for charities based on the user's input
    //$sql = "SELECT * FROM tblorgs WHERE org_name LIKE :searchQuery";
    
    // Prepare and execute the query (you should use prepared statements for better security)
    //$stmt = $pdo->prepare($sql);
    //$stmt->execute(['searchQuery' => '%' . $searchQuery . '%']);

    $getOrgText = "SELECT c.* FROM tblclients c JOIN tblusers u ON c.client_id = u.user_id WHERE u.account_type = 'c' AND c.is_approved = '1' AND c.client_name LIKE '%$searchQuery%'";
    $list_charities = $db->query($getOrgText);
    $charities = $list_charities->fetch_all(1);


    // Fetch the results
    //$charities = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Display the search results
    if ($list_charities->num_rows > 0) {
        // Assuming you have latitude and longitude columns in your database for each charity
        //$latitude = $charities[0]['org_lat'];
        //$longitude = $charities[0]['org_lng'];
        $latitude = $charities[0]['client_lat'];
        $longitude = $charities[0]['client_lng'];

        if ($latitude != null || $longitude != null) {
            // JavaScript code to update the map's view
            echo "<script>
                window.location.href = `../maps.php?lat=$latitude&lng=$longitude`;
            </script>";

        } else {
            //$address = mappa::geocodeAddress($charities[0]['org_address']);
            $address = mappa::geocodeAddress($charities[0]['client_address']);
            $latitude = $address['lat'];
            $longitude = $address['lng'];

            echo "<script>
                window.location.href = `../maps.php?lat=$latitude&lng=$longitude`;
            </script>";

        }

        unset($_GET['charity_search']);
    } else {
        $_SESSION['status'] = "Search Failed";
        $_SESSION['status_text'] = "No charities found matching your search.";
        $_SESSION['status_code'] = "error";
        header('Location: ../maps.php');
        unset($_GET['charity_search']);
    }
} else {
    $_SESSION['status'] = "Search Failed";
    $_SESSION['status_text'] = "Please enter a search query.";
    $_SESSION['status_code'] = "error";
    header('Location: ../maps.php');
}
?>
