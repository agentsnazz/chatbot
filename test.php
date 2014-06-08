<?php

require('CommandInterpreter.php');

$interpreter = new CommandInterpreter();

$interpreter->initialize();
$interpreter->setVerbose(True);
$interpreter->loadCommands();

//$interpreter->interpret("Goodbye");
?>