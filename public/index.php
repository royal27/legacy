<?php
// Main entry point for the application

// Set the timezone
date_default_timezone_set('Europe/Bucharest');

// For now, just a simple welcome message
echo "<h1>Bine ați venit pe noul dvs. site!</h1>";
echo "<p>Versiune PHP: " . phpversion() . "</p>";

// In the future, this file will:
// 1. Load configuration
// 2. Initialize the autoloader for classes
// 3. Handle routing to controllers
// 4. Render views/templates
?>
