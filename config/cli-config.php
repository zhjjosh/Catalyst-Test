<?php
use Doctrine\DBAL\Tools\Console\ConsoleRunner;

// replace with the mechanism to retrieve DBAL connection in your app
$connection = getDBALConnection();

// You can append new commands to $commands array, if needed

return ConsoleRunner::createHelperSet($connection);