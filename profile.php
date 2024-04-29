<?php
session_start();
include 'connect.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function fetchUserData($conn, $user_id) {
    $stmt_get_user = $conn->prepare("SELECT * FROM user WHERE user_id = ?");
    $stmt_get_user->bind_param('i', $user_id);
    $stmt_get_user->execute();
    $result_get_user = $stmt_get_user->get_result();
    return $result_get_user->fetch_assoc();
}

function updateUser($conn, $user_id, $name, $email, $contact_no, $nid) {
    $stmt_check_existence = $conn->prepare("SELECT * FROM user WHERE (email = ? OR contact_no = ? OR nid = ?) AND user_id != ?");
    $stmt_check_existence->bind_param('sssi', $email, $contact_no, $nid, $user_id);
    $stmt_check_existence->execute();
    $result_check_existence = $stmt_check_existence->get_result();

    if ($result_check_existence->num_rows > 0) {
        return "Email, contact number, or NID already exists.";
    } else {
        $stmt_update_user = $conn->prepare("UPDATE user SET name = ?, email = ?, contact_no = ?, nid = ? WHERE user_id = ?");
        $stmt_update_user->bind_param('ssssi', $name, $email, $contact_no, $nid, $user_id);
        $stmt_update_user->execute();

        if ($stmt_update_user->affected_rows > 0) {
            return "Profile updated successfully.";
        } else {
            return "Failed to update profile.";
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $conn = connectToDatabase();
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact_no = $_POST['contact_no'];
    $nid = $_POST['nid'];

    $success = updateUser($conn, $user_id, $name, $email, $contact_no, $nid);
}

$user = null;
if (isLoggedIn()) {
    $user_id = $_SESSION['user_id'];
    $conn = connectToDatabase();
    $user = fetchUserData($conn, $user_id);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body>
<?php include 'title.php'; ?>
<div class="max-w-lg mx-auto mt-10">
    <?php if (isLoggedIn()) { ?>
        <div class="bg-gradient-to-r from-blue-400 to-green-400 p-6 rounded-lg shadow-md">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Name:</label>
                <div class="flex items-center">
                    <span id="nameField"><?php echo $user['name']; ?></span>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                <div class="flex items-center">
                    <span id="emailField"><?php echo $user['email']; ?></span>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Contact No:</label>
                <div class="flex items-center">
                    <span id="contactNoField"><?php echo $user['contact_no']; ?></span>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">NID:</label>
                <div class="flex items-center">
                    <span id="nidField"><?php echo $user['nid']; ?></span>
                </div>
            </div>
        </div>
        <div class="flex justify-center mb-4">
            <button class="bg-blue-500 flex-grow hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-4" onclick="editProfile()">Edit Profile</button>
        </div>

        <form action="" method="post" id="updateProfileForm" class="shadow-md rounded px-8 pt-6 pb-8 mb-4 hidden bg-gradient-to-r from-blue-400 to-green-400">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Name</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name" name="name" type="text" placeholder="Name" value="<?php echo $user['name']; ?>">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email" name="email" type="email" placeholder="Email" value="<?php echo $user['email']; ?>">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="contact_no">Contact No</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="contact_no" name="contact_no" type="text" placeholder="Contact No" value="<?php echo $user['contact_no']; ?>">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="nid">NID</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="nid" name="nid" type="text" placeholder="NID" value="<?php echo $user['nid']; ?>">
            </div>
            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
            <div class="flex items-center justify-between">
                <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit" name="update_profile">Update</button>
                <button class="font-bold py-2 px-4 rounded bg-red-500 hover:bg-red-700 focus:outline-none focus:shadow-outline" type="button" onclick="cancelUpdate()">Cancel</button>
            </div>
        </form>
        <?php if (isset($success)) { ?>
            <div id="message" class="mt-4 <?php echo strpos($success, 'successfully') !== false ? 'bg-green-500' : 'bg-red-500'; ?> text-white font-bold py-2 px-4 rounded">
                <?php echo $success; ?>
            </div>
            <script>
                setTimeout(function() {
                    document.getElementById('message').style.display = 'none';
                }, 5000);
            </script>
        <?php } ?>
    <?php } ?>
</div>

<script>
    function editProfile() {
        document.getElementById('updateProfileForm').style.display = 'block';
        document.getElementById('nameField').style.display = 'none';
        document.getElementById('emailField').style.display = 'none';
        document.getElementById('contactNoField').style.display = 'none';
        document.getElementById('nidField').style.display = 'none';
    }

    function cancelUpdate() {
        document.getElementById('updateProfileForm').style.display = 'none';
        document.getElementById('nameField').style.display = 'block';
        document.getElementById('emailField').style.display = 'block';
        document.getElementById('contactNoField').style.display = 'block';
        document.getElementById('nidField').style.display = 'block';
    }
</script>
</body>

</html>
