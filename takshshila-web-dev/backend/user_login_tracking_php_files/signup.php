<?php
include("db.php");

echo "REQUEST METHOD: " . $_SERVER["REQUEST_METHOD"] . "<br>";


if ($_SERVER["REQUEST_METHOD"] === "POST") {


    if (
        isset($_POST["username"]) &&
        isset($_POST["password"]) &&
        isset($_POST["firstName"]) &&
        isset($_POST["email"]) &&
        isset($_POST["phone"])
    ) {

        // Get and combine user data
        $name = $_POST["firstName"] . " " . $_POST["lastName"];
        $email = $_POST["email"];
        $phone = $_POST["phone"];
        $username = $_POST["username"];
        $password = $_POST["password"];
        $confirmPassword = $_POST["confirmPassword"];

        // Optional: check password match
        if ($password !== $confirmPassword) {
            echo "Passwords do not match.";
            exit;
        }

        // Step 1: Check if username already exists
        $check_sql = "SELECT Sno FROM logindata WHERE username = ?";
        $check_stmt = $conn->prepare($check_sql);

        if (!$check_stmt) {
            die("Prepare failed: " . $conn->error);
        }


        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            echo "Username already exists. Please choose another.";
        } else {
            // Step 2: Insert into both tables
             $hash_password = password_hash($password, PASSWORD_DEFAULT);

            $insert_sql1 = "INSERT INTO personalinfo (name, EMAILid, PHONEno) VALUES (?, ?, ?)";
            $insert_sql2 = "INSERT INTO logindata (username, password) VALUES (?, ?)";

            $insert_stmt1 = $conn->prepare($insert_sql1);
            $insert_stmt2 = $conn->prepare($insert_sql2);


            if ($insert_stmt1 && $insert_stmt2) {
                // Use "sss" instead of "ssi" if phone is stored as a string
                $insert_stmt1->bind_param("sss", $name, $email, $phone);
                $insert_stmt2->bind_param("ss", $username, $hash_password);

                $success1 = $insert_stmt1->execute();
                $success2 = $insert_stmt2->execute();

                if ($success1 && $success2) {
                    echo "User registered successfully!";
                } else {
                    echo "Something went wrong while saving your data.";
                }

                $insert_stmt1->close();
                $insert_stmt2->close();
            } else {
                echo "Could not prepare insert statements.";
            }
        }

        $check_stmt->close();
    } else {
        echo "All fields are required.";
    }
}
?>
