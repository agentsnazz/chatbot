<?php

require('CommandInterpreter.php');

$interpreter = new CommandInterpreter();

$interpreter->initialize();
$interpreter->setVerbose(False);
$interpreter->setMood("mean");
$interpreter->loadCommands();

while(True) {
    print("Tester: ");
    $input = trim(fgets(STDIN));
    print("Robot: ".$interpreter->interpret($input)[0]."\n");
}
?>