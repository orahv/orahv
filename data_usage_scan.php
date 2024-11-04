<?php
// data_usage_scan.php
header("Content-Type: application/json");

$network = isset($_GET['network']) ? $_GET['network'] : '192.168.1';
$start = isset($_GET['start']) ? intval($_GET['start']) : 1;
$end = isset($_GET['end']) ? intval($_GET['end']) : 10;
$community = 'public'; // SNMP community string (default is usually 'public')
$oid = '1.3.6.1.2.1.2.2.1.10'; // Example OID for incoming traffic on a network interface

$devices = [];

// Function to get data usage via SNMP
function getDataUsage($ip, $community, $oid) {
    $usage = @snmpget($ip, $community, $oid);
    if ($usage === false) {
        // Log the error or print more details for debugging
        error_log("SNMP query failed for $ip");
        return false;
    }
    return (int) str_replace('Counter32: ', '', $usage);
}


// Scan the IP range
for ($i = $start; $i <= $end; $i++) {
    $ip = $network . '.' . $i;
    $pingResult = exec("ping -n 1 -w 100 $ip", $output, $status);
    if ($status === 0) {
        // Device is reachable
        $hostname = gethostbyaddr($ip) ?: 'Unknown';
        $dataUsage = getDataUsage($ip, $community, $oid);

        $devices[] = [
            'ip' => $ip,
            'hostname' => $hostname,
            'dataUsage' => $dataUsage !== false ? $dataUsage : 'N/A'
        ];
    }
}

// Output the device list
echo json_encode(['devices' => $devices]);
