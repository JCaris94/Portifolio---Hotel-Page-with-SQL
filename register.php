<?php
// Change this to your connection info.
$DATABASE_HOST = 'localhost:3306';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'phplogin';

// Try and connect using the info above.
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    // If there is an error with the connection, stop the script and display the error.
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

// Now we check if the data was submitted, isset() function will check if the data exists.
if (!isset($_POST['username'], $_POST['password'], $_POST['email'])) {
    // Could not get the data that should have been sent.
    exit('Please complete the registration form!');
}

// Make sure the submitted registration values are not empty.
if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email'])) {
    // One or more values are empty.
    exit('Please complete the registration form');
}

// Validate email.
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    exit('Email is not valid!');
}

// Validate username (alphanumeric).
if (preg_match('/^[a-zA-Z0-9]+$/', $_POST['username']) == 0) {
    exit('Username is not valid!');
}

// Validate password length.
if (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 5) {
    exit('Password must be between 5 and 20 characters long!');
}

// We need to check if the account with that username exists.
if ($stmt = $con->prepare('SELECT id, activation_code FROM accounts WHERE username = ?')) {
    // Bind parameters (s = string).
    $stmt->bind_param('s', $_POST['username']);
    $stmt->execute();
    $stmt->store_result();
    // Store the result so we can check if the account exists in the database.
    if ($stmt->num_rows > 0) {
        // Bind the result to variables.
        $stmt->bind_result($id, $activation_code);
        $stmt->fetch();

        // Check if the account is activated.
        if ($activation_code == 'activated') {
            // Account is activated.
            // Display home page or a welcome message.
            echo 'Your account is already activated. Welcome!';
        } else {
            // Account is not activated.
            // Redirect user or display an error message.
            exit('Your account is not activated. Please check your email to activate your account.');
        }
    } else {
        // Username doesn't exist, insert new account.
        if ($stmt = $con->prepare('INSERT INTO accounts (username, password, email, activation_code) VALUES (?, ?, ?, ?)')) {
            // Hash the password and create a unique activation code.
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $uniqid = uniqid();
            $stmt->bind_param('ssss', $_POST['username'], $password, $_POST['email'], $uniqid);
            $stmt->execute();
            // Send activation email.
            $from = 'noreply@yourdomain.com';
            $subject = 'Account Activation Required';
            $headers = 'From: ' . $from . "\r\n" . 
                       'Reply-To: ' . $from . "\r\n" . 
                       'X-Mailer: PHP/' . phpversion() . "\r\n" . 
                       'MIME-Version: 1.0' . "\r\n" . 
                       'Content-Type: text/html; charset=UTF-8' . "\r\n";
            $activate_link = 'http://yourdomain.com/phplogin/activate.php?email=' . $_POST['email'] . '&code=' . $uniqid;
            $message = '<p>Please click the following link to activate your account: <a href="' . $activate_link . '">' . $activate_link . '</a></p>';
            mail($_POST['email'], $subject, $message, $headers);
            echo 'Please check your email to activate your account!';
        } else {
            // Something is wrong with the SQL statement, so you must check to make sure your accounts table exists with all four fields.
            exit('Could not prepare statement!');
        }
    }
    $stmt->close();
} else {
    // Something is wrong with the SQL statement, so you must check to make sure your accounts table exists.
    exit('Could not prepare statement!');
}

$con->close();
?>
