<?php
header("Content-Type: application/json; charset=UTF-8");

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(204);
    exit;
}

define("DATA_FILE", __DIR__ . "/data.json");

function read_workouts() {
    if (!file_exists(DATA_FILE)) {
        return [];
    }
    $json = file_get_contents(DATA_FILE);
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

function write_workouts($workouts) {
    $fp = fopen(DATA_FILE, "c+");
    if ($fp && flock($fp, LOCK_EX)) {
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, json_encode(array_values($workouts), JSON_PRETTY_PRINT));
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
        return true;
    }
    return false;
}

function next_id($workouts) {
    $maxId = 0;
    foreach ($workouts as $w) {
        if ($w["id"] > $maxId) $maxId = $w["id"];
    }
    return $maxId + 1;
}

function send_json($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

function get_request_body() {
    $body = json_decode(file_get_contents("php://input"), true);
    return is_array($body) ? $body : [];
}

$method = $_SERVER["REQUEST_METHOD"];
$workouts = read_workouts();
$id = isset($_GET["id"]) ? (int) $_GET["id"] : null;

switch ($method) {

    case "GET":
        if ($id !== null) {
            $match = array_values(array_filter($workouts, fn($w) => $w["id"] === $id));
            if (empty($match)) {
                send_json(["error" => "Workout not found"], 404);
            }
            send_json($match[0]);
        }
        send_json($workouts);
        break;

    case "POST":
        $body = get_request_body();

        if (empty($body["name"]) || empty($body["category"])) {
            send_json(["error" => "Fields 'name' and 'category' are required"], 400);
        }

        $newWorkout = [
            "id"        => next_id($workouts),
            "name"      => $body["name"],
            "category"  => $body["category"],
            "date"      => $body["date"] ?? date("Y-m-d"),
            "duration"  => isset($body["duration"]) ? (int) $body["duration"] : 0,
            "calories"  => isset($body["calories"]) ? (int) $body["calories"] : 0,
            "status"    => $body["status"] ?? "Scheduled",
            "intensity" => $body["intensity"] ?? "Medium"
        ];

        $workouts[] = $newWorkout;

        if (!write_workouts($workouts)) {
            send_json(["error" => "Failed to save workout"], 500);
        }
        send_json($newWorkout, 201);
        break;

    case "PUT":
        if ($id === null) {
            send_json(["error" => "A workout 'id' is required"], 400);
        }

        $body = get_request_body();
        $found = false;

        foreach ($workouts as &$w) {
            if ($w["id"] === $id) {
                $w = array_merge($w, $body);
                $w["id"] = $id;
                $found = true;
                break;
            }
        }
        unset($w);

        if (!$found) {
            send_json(["error" => "Workout not found"], 404);
        }
        if (!write_workouts($workouts)) {
            send_json(["error" => "Failed to update workout"], 500);
        }

        $updated = array_values(array_filter($workouts, fn($w) => $w["id"] === $id))[0];
        send_json($updated);
        break;

    case "DELETE":
        if ($id === null) {
            send_json(["error" => "A workout 'id' is required"], 400);
        }

        $originalCount = count($workouts);
        $workouts = array_values(array_filter($workouts, fn($w) => $w["id"] !== $id));

        if (count($workouts) === $originalCount) {
            send_json(["error" => "Workout not found"], 404);
        }
        if (!write_workouts($workouts)) {
            send_json(["error" => "Failed to delete workout"], 500);
        }
        send_json(["message" => "Workout $id deleted"]);
        break;

    default:
        send_json(["error" => "Method not allowed"], 405);
}
