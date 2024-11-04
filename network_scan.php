<?php
header("Content-Type: application/json");

$network = isset($_GET['network']) ? $_GET['network'] : '192.168.1';
$start = isset($_GET['start']) ? intval($_GET['start']) : 1;
$end = isset($_GET['end']) ? intval($_GET['end']) : 10;

$activeDevices = [];  // Array to store active devices with names

// Function to check if an IP address is active and get the device name
function getDeviceName($ip) {
    $pingResult = exec("ping -n 1 -w 100 $ip", $output, $status);
    if ($status === 0) {
        // Perform a reverse DNS lookup to get the hostname
        $hostname = gethostbyaddr($ip);
        return $hostname ?: $ip;  // If hostname not found, use IP as fallback
    }
    return false;
}

// Scan the IP range and record active devices
for ($i = $start; $i <= $end; $i++) {
    $ip = $network . '.' . $i;
    $deviceName = getDeviceName($ip);
    if ($deviceName) {
        $activeDevices[] = ['ip' => $ip, 'name' => $deviceName];
    }
}

// Output the list of active devices
echo json_encode([
    'activeDevices' => $activeDevices,
    'totalActive' => count($activeDevices),
    'totalInactive' => ($end - $start + 1) - count($activeDevices),
]);
