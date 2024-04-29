<?php
session_start();
include 'connect.php';
$conn = connectToDatabase();

$stmt_get_places = $conn->prepare("SELECT * FROM place");
$stmt_get_places->execute();
$result_get_places = $stmt_get_places->get_result();
$places = $result_get_places->fetch_all(MYSQLI_ASSOC);

$error = '';
$searched = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $source = $_POST['source'];
    $destination = $_POST['destination'];
    $date = $_POST['date'];

    if ($source == $destination) {
        $error = 'Source and Destination cannot be same';

    }
    else{
        $stmt_get_trains = $conn->prepare("SELECT trip.trip_id, trip.train_id, train.train_name FROM trip INNER JOIN train ON trip.train_id = train.train_id WHERE DATE(trip_date) = ?");
        $stmt_get_trains->bind_param('s', $date);
        $stmt_get_trains->execute();
        $result_get_trains = $stmt_get_trains->get_result();
        $trains_info = $result_get_trains->fetch_all(MYSQLI_ASSOC);

        $trains = [];
        foreach ($trains_info as $train_info) {
            $train_id = $train_info['train_id'];
            $train_name = $train_info['train_name'];
            $trip_id = $train_info['trip_id'];
            $stmt_get_routes = $conn->prepare("SELECT route.route_id, route.start_place_id, route.end_place_id FROM route INNER JOIN train_route ON route.route_id = train_route.route_id WHERE train_route.train_id = ?");
            $stmt_get_routes->bind_param('i', $train_id);
            $stmt_get_routes->execute();
            $result_get_routes = $stmt_get_routes->get_result();
            $routes = $result_get_routes->fetch_all(MYSQLI_ASSOC);

            $source_found = false;
            $destination_found = false;
            foreach ($routes as $route) {
                if ($route['start_place_id'] == $source) {
                    $source_found = true;
                }
                if ($route['end_place_id'] == $destination) {
                    $destination_found = true;
                }
            }

            if ($source_found && $destination_found) {
                $trains[] = [
                    'trip_id' => $trip_id,
                    'train_id' => $train_id,
                    'train_name' => $train_name
                ];
            }
        }
        $searched = true;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLOTH TRAINS</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
<?php include 'title.php'; ?>

<div class="fixed left-0 top-20 bottom-0 bg-gray-200 w-64 p-4">
    <h3 class="text-lg font-semibold mb-4">Search Trains</h3>
    <form method="post">
        <div class="mb-4">
            <label for="source" class="block mb-1">Source Place</label>
            <select id="source" name="source" class="w-full border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300" required>
                <option value="">Select Source</option>
                <?php foreach ($places as $place) { ?>
                    <option value="<?php echo $place['place_id']; ?>"><?php echo $place['place_name']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="mb-4">
            <label for="destination" class="block mb-1">Destination Place</label>
            <select id="destination" name="destination" class="w-full border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300" required>
                <option value="">Select Destination</option>
                <?php foreach ($places as $place) { ?>
                    <option value="<?php echo $place['place_id']; ?>"><?php echo $place['place_name']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="mb-4">
            <label for="date" class="block mb-1">Pick a Date</label>
            <input type="date" id="date" name="date" class="w-full border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300" min="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d', strtotime('+10 days')); ?>" required>
        </div>
        <button type="submit" class="w-full bg-blue-500 text-white font-semibold py-2 px-4 rounded hover:bg-blue-600 transition duration-300">Search</button>
    </form>
</div>


<div class="ml-64 p-4 flex justify-center items-center h-full flex-col">
    <?php if (!empty($error)) : ?>
        <p class="bg-red-500 text-white py-2 px-4 rounded shadow-md"><?php echo $error; ?></p>
    <?php elseif (isset($trains) && !empty($trains)) : ?>
        <?php foreach ($trains as $train) : ?>
            <div class="bg-gradient-to-br w-96 from-pink-300 to-purple-400 rounded-lg p-8 flex flex-col justify-between mb-8">
                <div>
                    <h3 class="text-3xl font-semibold text-center text-black mb-4"><?php echo $train['train_name']; ?></h3>
                </div>
                <div class="flex justify-center">
                    <a href="/admin/Ticket/buy_ticket.php?trip_id=<?php echo $train['trip_id']; ?>&source=<?php echo $source; ?>&destination=<?php echo $destination; ?>" class="bg-blue-500 text-white font-semibold py-3 px-8 rounded-lg w-64 text-center hover:bg-blue-600 transition duration-300">Buy Ticket</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php elseif ($searched) : ?>
        <p class="bg-yellow-300 text-yellow-800 py-2 px-4 rounded shadow-md">No trains found for the selected route and date.</p>
    <?php endif; ?>
</div>



</body>

</html>
