<?php
header("Content-Type: application/javascript");

$network = isset($_GET['network']) ? $_GET['network'] : '192.168.1';
$start = isset($_GET['start']) ? intval($_GET['start']) : 1;
$end = isset($_GET['end']) ? intval($_GET['end']) : 10;

echo <<<EOT
(function() {
    // Find the container within the existing widget card
    var widgetContainer = document.querySelector('.widget-content');
    
    // Clear any existing content
    widgetContainer.innerHTML = '';

    // Add title
    var titleElement = document.createElement('h3');
    titleElement.textContent = 'Network Device Data Usage';
    titleElement.style.marginBottom = '10px';
    widgetContainer.appendChild(titleElement);

    // Add loading message
    var loadingMessage = document.createElement('p');
    loadingMessage.textContent = 'Scanning network and fetching data usage...';
    widgetContainer.appendChild(loadingMessage);

    // Create table for device info and data usage
    var table = document.createElement('table');
    table.style.width = '100%';
    table.style.borderCollapse = 'collapse';
    table.style.marginTop = '10px';

    // Table header
    var headerRow = document.createElement('tr');
    var headers = ['IP Address', 'Hostname', 'Data Usage (Bytes)'];
    headers.forEach(headerText => {
        var th = document.createElement('th');
        th.textContent = headerText;
        th.style.border = '1px solid #ddd';
        th.style.padding = '8px';
        th.style.backgroundColor = '#f2f2f2';
        th.style.textAlign = 'left';
        headerRow.appendChild(th);
    });
    table.appendChild(headerRow);

    // Fetch data from PHP script
    fetch("data_usage_scan.php?network=" + encodeURIComponent("$network") + "&start=" + encodeURIComponent("$start") + "&end=" + encodeURIComponent("$end"))
        .then(response => response.json())
        .then(data => {
            loadingMessage.style.display = 'none';  // Hide loading message
            if (data.devices && data.devices.length > 0) {
                data.devices.forEach(device => {
                    var row = document.createElement('tr');

                    var ipCell = document.createElement('td');
                    ipCell.textContent = device.ip;
                    ipCell.style.border = '1px solid #ddd';
                    ipCell.style.padding = '8px';
                    row.appendChild(ipCell);

                    var hostnameCell = document.createElement('td');
                    hostnameCell.textContent = device.hostname;
                    hostnameCell.style.border = '1px solid #ddd';
                    hostnameCell.style.padding = '8px';
                    row.appendChild(hostnameCell);

                    var usageCell = document.createElement('td');
                    usageCell.textContent = device.dataUsage !== 'N/A' ? device.dataUsage : 'Not Available';
                    usageCell.style.border = '1px solid #ddd';
                    usageCell.style.padding = '8px';
                    row.appendChild(usageCell);

                    table.appendChild(row);
                });
            } else {
                // Display a message if no devices are found
                var noDevicesRow = document.createElement('tr');
                var noDevicesCell = document.createElement('td');
                noDevicesCell.textContent = 'No available devices found';
                noDevicesCell.colSpan = 3;  // Span across three columns
                noDevicesCell.style.textAlign = 'center';
                noDevicesCell.style.padding = '8px';
                noDevicesRow.appendChild(noDevicesCell);
                table.appendChild(noDevicesRow);
            }
            widgetContainer.appendChild(table);  // Append the table to the widget content area
        })
        .catch(error => {
            loadingMessage.textContent = 'Error fetching data.';
            console.error(error);
        });
})();
EOT;
