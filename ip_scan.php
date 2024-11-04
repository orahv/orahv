<?php
// ip_scan.php
header("Content-Type: application/json");

$network = isset($_GET['network']) ? $_GET['network'] : '192.168.1';
$start = isset($_GET['start']) ? intval($_GET['start']) : 1;
$end = isset($_GET['end']) ? intval($_GET['end']) : 10;

$availableDevices = [];

// Function to check if an IP address is active and get the hostname
function checkIpAvailability($ip) {
    $pingResult = exec("ping -n 1 -w 100 $ip", $output, $status);
    if ($status === 0) {
        $hostname = gethostbyaddr($ip);
        return [
            'ip' => $ip,
            'hostname' => $hostname ?: 'Unknown'
        ];
    }
    return false;
}

// Scan the IP range and collect available devices
for ($i = $start; $i <= $end; $i++) {
    $ip = $network . '.' . $i;
    $device = checkIpAvailability($ip);
    if ($device) {
        $availableDevices[] = $device;
    }
}

// Output the list of available devices
echo json_encode(['devices' => $availableDevices]);
