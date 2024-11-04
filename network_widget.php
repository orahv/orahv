<?php
header("Content-Type: application/javascript");

$network = isset($_GET['network']) ? $_GET['network'] : '192.168.1';
$start = isset($_GET['start']) ? $_GET['start'] : 1;
$end = isset($_GET['end']) ? $_GET['end'] : 10;

echo <<<EOT
(function() {
    // Load Chart.js library
    var script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
    script.onload = function() {
        // Create the widget container
        var widgetContainer = document.createElement('div');
        widgetContainer.style.padding = '10px';
        widgetContainer.style.border = '1px solid #ccc';
        widgetContainer.style.backgroundColor = '#f9f9f9';
        widgetContainer.style.width = '300px';
        widgetContainer.style.textAlign = 'center';

        // Add a title
        var titleElement = document.createElement('h3');
        titleElement.textContent = 'Network Device Status';
        titleElement.style.margin = '0 0 10px';
        widgetContainer.appendChild(titleElement);

        // Add a canvas element for the pie chart
        var canvas = document.createElement('canvas');
        canvas.width = 300;
        canvas.height = 300;
        widgetContainer.appendChild(canvas);

        // Create a list element to display device names
        var deviceList = document.createElement('ul');
        deviceList.style.listStyle = 'none';
        deviceList.style.padding = '0';
        widgetContainer.appendChild(deviceList);

        // Find the correct widget content area
        const targetCard = document.querySelector('.widget-card:last-child .widget-content');
        targetCard.innerHTML = ''; // Clear loading message
        targetCard.appendChild(widgetContainer); // Append the widget container to the right place

        // Fetch data from the network scanner
        fetch("network_scan.php?network=" + encodeURIComponent("$network") + "&start=" + encodeURIComponent("$start") + "&end=" + encodeURIComponent("$end"))
            .then(response => response.json())
            .then(data => {
                // Create the pie chart
                var ctx = canvas.getContext('2d');
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: ['Active Devices', 'Inactive Devices'],
                        datasets: [{
                            data: [data.totalActive, data.totalInactive],
                            backgroundColor: ['#36a2eb', '#ff6384']
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        }
                    }
                });

                // Populate the list of active devices
                data.activeDevices.forEach(device => {
                    var listItem = document.createElement('li');
                    listItem.textContent = device.name + ' (' + device.ip + ')';
                    deviceList.appendChild(listItem);
                });
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
    };
    document.head.appendChild(script);
})();
EOT;
