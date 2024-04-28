<?php

if (!isset($_SESSION["user_id"])) {
    header("Location: /Login/login.php");
    exit();
}
if (isset($_POST["logout"])) {
    session_destroy();
    header("Location: /Login/login.php");
    exit();
}

$user_type = $_SESSION["user_type"];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body>
<div class="top-0 left-0 right-0 z-50 bg-gradient-to-br from-purple-200 via-pink-300 to-red-200 p-4 flex justify-between items-center">
    <div class="flex">
        <?php if ($user_type === "admin"): ?>
            <a href="/Admin/admin.php" class="bg-blue-500 text-white font-semibold py-2 px-4 rounded hover:bg-blue-600 transition duration-300">Admin Panel</a>
        <?php endif; ?>
        <a href="/Ticket/ticket.php" class="bg-blue-500 text-white ml-2 font-semibold py-2 px-4 rounded hover:bg-blue-600 transition duration-300">My Tickets</a>
        <a href="/profile.php" class="bg-blue-500 text-white ml-2 font-semibold py-2 px-4 rounded hover:bg-blue-600 transition duration-300">Profile</a>
    </div>
    <a href="/index.php" class="text-3xl font-semibold text-black">BD TRAIN</a>
    <form method="post">
        <button type="submit" name="logout" class="bg-red-500 text-black font-semibold py-2 px-4 rounded hover:bg-red-600 transition duration-300">Logout</button>
    </form>
</div>
</body>


</html>
