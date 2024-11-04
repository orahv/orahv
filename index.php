<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Widget Cards</title>
    <style>
        /* General styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Button styling */
        .add-widget-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            z-index: 1000;
        }

        /* Container for widget cards */
        .widget-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            padding: 20px;
        }

        /* Individual widget card */
        .widget-card {
            flex: 1 1 300px;
            min-width: 250px;
            max-width: 100%;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            background-color: #f9f9f9;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            position: relative;
            transition: box-shadow 0.2s;
        }

        .widget-card:hover {
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.2);
        }

        /* Widget title */
        .widget-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }

        /* Widget content area */
        .widget-content {
            font-size: 14px;
            color: #555;
        }

        /* Close button styling */
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #fff;
            color: black;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
            text-align: center;
        }

        /* Modal styling */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 400px;
            overflow: hidden;
        }

        /* Button inside modal */
        .modal-content button {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
            width: 100%;
        }

        .modal-content h3 {
            margin-top: 0;
        }
    </style>
</head>
<body>
    <!-- Button to add widgets -->
    <button class="add-widget-btn" onclick="openModal()">Add Widget</button>
<br><head>WIDGETS</head>
    <!-- Modal for widget selection -->
    <div class="modal" id="widgetModal" aria-hidden="true">
        <div class="modal-content">
            <h3>Select a Widget</h3>
            <button onclick="addWidget('Welcome Widget', 'widget.php?color=blue&message=Welcome!')">Welcome Widget</button>
            <button onclick="addWidget('Network Widget', 'network_widget.php?network=192.168.1&start=1&end=20')">Network Widget</button>
            <button onclick="addWidget('IP Scan Widget', 'ip_scan_widget.php?network=192.168.1&start=1&end=20')">IP Scan Widget</button>
            <button onclick="addWidget('Data Usage Widget', 'data_usage_widget.php?network=192.168.1&start=1&end=20')">Data Usage Widget</button>
            <button onclick="closeModal()">Close</button>
        </div>
    </div>

    <!-- Container for dynamically added widgets -->
    <div class="widget-container" id="widgetContainer"></div>

    <script>
        // Function to open the widget selection modal
        function openModal() {
            document.getElementById('widgetModal').style.display = 'flex';
            document.getElementById('widgetModal').setAttribute('aria-hidden', 'false');
        }

        // Function to close the widget selection modal
        function closeModal() {
            document.getElementById('widgetModal').style.display = 'none';
            document.getElementById('widgetModal').setAttribute('aria-hidden', 'true');
        }

        // Close modal when pressing the Esc key
        window.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && document.getElementById('widgetModal').style.display === 'flex') {
                closeModal();
            }
        });

        // Function to add a widget dynamically
        function addWidget(widgetTitle, widgetSrc) {
            // Create a new div for the widget card
            const widgetCard = document.createElement('div');
            widgetCard.className = 'widget-card';

            // Add close button
            const closeButton = document.createElement('button');
            closeButton.className = 'close-btn';
            closeButton.textContent = '×';
            closeButton.onclick = () => widgetCard.remove(); // Remove card when clicked

            // Add the title of the widget
            const titleElement = document.createElement('div');
            titleElement.className = 'widget-title';
            titleElement.textContent = widgetTitle;

            // Add content area where widget script will load
            const contentElement = document.createElement('div');
            contentElement.className = 'widget-content';
            contentElement.textContent = "Loading...";

            // Fetch widget content and execute it as JavaScript
            fetch(widgetSrc)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Network response was not ok for ${widgetTitle}: ${response.statusText}`);
                    }
                    return response.text();
                })
                .then(jsContent => {
                    // Use a new Function to execute the JavaScript returned from the widget
                    new Function(jsContent)();
                })
                .catch(error => {
                    // Display an error message within the widget content area
                    contentElement.textContent = "Failed to load widget.";
                    console.error(`Error loading ${widgetTitle}:`, error);
                });

            // Append elements to widget card
            widgetCard.appendChild(closeButton); // Append close button
            widgetCard.appendChild(titleElement);
            widgetCard.appendChild(contentElement);

            // Append the widget card to the widget container
            document.getElementById('widgetContainer').appendChild(widgetCard);

            // Close the modal after adding the widget
            closeModal();
        }
    </script>
</body>
</html>
