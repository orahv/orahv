<?php
header("Content-Type: application/javascript");

$network = isset($_GET['network']) ? $_GET['network'] : '192.168.1';
$start = isset($_GET['start']) ? intval($_GET['start']) : 1;
$end = isset($_GET['end']) ? intval($_GET['end']) : 20;

echo <<<EOT
(function() {
    // Select the existing widget content container
    var widgetContainer = document.querySelector('.widget-content'); // Assumes '.widget-content' exists in the HTML

    // Clear any previous content
    widgetContainer.innerHTML = '';

    // Add a title
    var titleElement = document.createElement('h3');
    titleElement.textContent = 'Network IP Scanner';
    titleElement.style.margin = '0 0 10px';
    widgetContainer.appendChild(titleElement);

    // Create input elements for network, start, and end range with styling
    var inputContainer = document.createElement('div');
    inputContainer.style.marginBottom = '10px';

    var networkInput = document.createElement('input');
    networkInput.type = 'text';
    networkInput.value = "$network";
    networkInput.placeholder = 'Network (e.g., 192.168.1)';
    networkInput.style.marginRight = '5px';
    networkInput.style.padding = '5px';
    networkInput.style.width = '140px';

    var startInput = document.createElement('input');
    startInput.type = 'number';
    startInput.value = "$start";
    startInput.placeholder = 'Start';
    startInput.style.marginRight = '5px';
    startInput.style.padding = '5px';
    startInput.style.width = '60px';

    var endInput = document.createElement('input');
    endInput.type = 'number';
    endInput.value = "$end";
    endInput.placeholder = 'End';
    endInput.style.padding = '5px';
    endInput.style.width = '60px';

    // Append input fields to the input container
    inputContainer.appendChild(networkInput);
    inputContainer.appendChild(startInput);
    inputContainer.appendChild(endInput);

    // Add the input container to the widget container
    widgetContainer.appendChild(inputContainer);

    // Add a button to initiate the scan
    var scanButton = document.createElement('button');
    scanButton.textContent = 'Scan Network';
    scanButton.style.marginTop = '5px';
    scanButton.style.padding = '6px 12px';
    scanButton.style.cursor = 'pointer';
    scanButton.onclick = function() {
        var network = networkInput.value;
        var start = parseInt(startInput.value, 10);
        var end = parseInt(endInput.value, 10);
        scanNetwork(network, start, end);
    };
    widgetContainer.appendChild(scanButton);

    // Function to scan the network and populate results
    function scanNetwork(network, start, end) {
        // Clear previous results
        widgetContainer.innerHTML = '';

        // Add title and inputs back to the widget container
        widgetContainer.appendChild(titleElement);
        widgetContainer.appendChild(inputContainer);
        widgetContainer.appendChild(scanButton);

        // Add loading message
        var loadingMessage = document.createElement('p');
        loadingMessage.textContent = 'Scanning network...';
        widgetContainer.appendChild(loadingMessage);

        // Create the table element
        var table = document.createElement('table');
        table.style.width = '100%';
        table.style.borderCollapse = 'collapse';
        table.style.marginTop = '10px';

        // Create the table header
        var headerRow = document.createElement('tr');
        var headers = ['IP Address', 'Hostname'];
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

        // Fetch data from the IP scan PHP script
        fetch("ip_scan.php?network=" + encodeURIComponent(network) + "&start=" + encodeURIComponent(start) + "&end=" + encodeURIComponent(end))
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

                        table.appendChild(row);
                    });
                } else {
                    // If no devices found, display a message
                    var noDevicesRow = document.createElement('tr');
                    var noDevicesCell = document.createElement('td');
                    noDevicesCell.textContent = 'No available devices found';
                    noDevicesCell.colSpan = 2;  // Span across two columns
                    noDevicesCell.style.textAlign = 'center';
                    noDevicesCell.style.padding = '8px';
                    noDevicesRow.appendChild(noDevicesCell);
                    table.appendChild(noDevicesRow);
                }
                widgetContainer.appendChild(table);  // Append table to widget container
            })
            .catch(error => {
                loadingMessage.textContent = 'Error scanning network.';  // Update loading message
                console.error(error);
            });
    }

    // Initial scan
    scanNetwork("$network", "$start", "$end");
})();
EOT;
