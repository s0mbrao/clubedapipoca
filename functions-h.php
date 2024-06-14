<?php

//SERVER DB CONFIGURATIONS
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "cdp";



//SERVER GLOBAL VARS CONFIGURATIONS
$SERVER_DATA 	  			    = get_server_data();
$SERVER_INDEX_ANNOUNCE_TEXT 	= $SERVER_DATA[0]["INDEX_ANOUNCE_TEXT"];
$SERVER_APK_DOWNLOAD_LINK       = $SERVER_DATA[0]["APK_DOWNLOAD_LINK"];


//DATA ENCRYPTION CONFIGURATIONS
$iv = openssl_random_pseudo_bytes(16);
$DefaultEncryptionKey = "1ac";

function EncryptData($data, $key, $iv) {
    $cipher = "aes-256-cbc";
    $encrypted = openssl_encrypt($data, $cipher, $key, 0, $iv);
    return base64_encode($encrypted);
}

function DecryptData($data, $key, $iv) {
    $cipher = "aes-256-cbc";
    $decrypted = openssl_decrypt(base64_decode($data), $cipher, $key, 0, $iv);
    return $decrypted;
}


//GET USER DB DATAS
function get_user_data($_login, $_password) {
    $USER_DATA = array();
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare statement
    $sql = "SELECT 
                ID, 
                NAME,
                LOGIN,
                PASSWORD,
                ACCOUNT_LEVEL,
                CURRENT_PLAN,
                IS_MOBILE,
                IS_PC,
                REGISTERED_DATE,
                EXPIRE_DATE,
                LAST_LOGIN_DATE,
                LAST_ONLINE_DATE,
                LAST_IPV4_ADDRESS,
                LAST_PAGE,
                LAST_MOVIE,
                LAST_SHOW,
                LAST_SEASON,
                LAST_EPISODE,
                LAST_PLAN
            FROM users 
            WHERE LOGIN = ? AND PASSWORD = ?";
            
    $stmt = $conn->prepare($sql);
    
    // Bind parameters
    $stmt->bind_param("ss", $_login, $_password);

    // Execute statement
    $stmt->execute();

    // Get result
    $result = $stmt->get_result();

    // Check if any row found
    if ($result->num_rows > 0) {
        // Fetch data
        while ($row = $result->fetch_assoc()) {
            $USER_DATA[] = $row;
        }
    } else {
        // No user found with provided credentials
        $USER_DATA = null;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
    
    return $USER_DATA;
}


//GET SERVER DATA FROM DB
function get_server_data() {
    $SERVER_DATA = array(); 
    global $servername, $username, $password, $dbname;

    // Create a database connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the database connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT
        ID, 
        NAME,
        INDEX_ANOUNCE_TEXT,
		APK_DOWNLOAD_LINK,
		PROFILE_DEFAULT_ANNOUCE_TEXT,
        MOVIES_ANOUNCE_TEXT,
        SHOWS_ANNOUNCE_TEXT,
        CURRENT_ONLINE_ON_MOVIES_COUNT,
        CURRENT_ONLINE_ON_SHOWS_COUNT,
        CURRENT_ONLINE_ON_TV_COUNT,
        CURRENT_ONLINE_ALL_COUNT,
        CURRENT_ADM_ONLINE_COUNT,
        IN_MAINTENANCE,
        IS_OFFLINE,
        MAX_ONLINE,
        MAX_MOVIES_CATEGORY,
        MAX_ADM,
        ALLOW_BLACKLISTING,
        ALLOW_CHAT,
        ALLOW_COMMENTS,
        ALLOW_REPLIES,
        ALLOW_OTHER_USERS,
        ALLOW_MOVIES,
        ALLOW_SHOWS,
        ALLOW_TV,
        ALLOW_SEARCH,
        ALLOW_BADWORDS
    FROM server"; 

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $SERVER_DATA[] = $row;
        }
    } else {
        $SERVER_DATA = 0;
    }

    $conn->close();
    return $SERVER_DATA;
}


?>