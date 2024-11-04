<?php
// widget.php
header("Content-Type: application/javascript");
$color = isset($_GET['color']) ? htmlspecialchars($_GET['color']) : '#f9f9f9';
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : 'Hello, World!';

// You could add any PHP logic here. In this case, we will generate the current date and time.
$date = date("Y-m-d H:i:s");

// JavaScript code to inject the widget into the page
echo <<<EOT
(function() {
    // Create the widget container
    var widgetContainer = document.createElement('div');
    widgetContainer.style.padding = '10px';
    widgetContainer.style.border = '1px solid #ccc';
    widgetContainer.style.backgroundColor = '$color'; // Use the color parameter
    widgetContainer.style.width = '200px';
    widgetContainer.style.textAlign = 'center';

    // Add the date and time
    var dateElement = document.createElement('p');
    dateElement.textContent = 'Current Date & Time: $date';
    dateElement.style.fontSize = '16px';
    dateElement.style.margin = '0';
    widgetContainer.appendChild(dateElement);

    // Add the message
    var messageElement = document.createElement('p');
    messageElement.textContent = '$message'; // Add the message
    messageElement.style.fontSize = '14px';
    messageElement.style.margin = '5px 0 0 0'; // Adjust margin
    widgetContainer.appendChild(messageElement);

    // Append the widget to a specific element in the card
    const targetCard = document.querySelector('.widget-card:last-child .widget-content');
    targetCard.innerHTML = ''; // Clear loading message
    targetCard.appendChild(widgetContainer);
})();
EOT;
?>
