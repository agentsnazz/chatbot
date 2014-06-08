<?php

require('CommandInterpreter.php');

$interpreter = new CommandInterpreter();

$interpreter->initialize();
$interpreter->setVerbose(True);
$interpreter->loadCommands();

print("\n");
print("Tester: Greeting\n");
print("Robot: ".$interpreter->interpret("Greeting")[0]);
print("\n");
print("Tester: Fish\n");
print("Robot: ".$interpreter->interpret("Fish")[0]);
print("\n");
print("Tester: Goodbye\n");
print("Robot: ".$interpreter->interpret("Goodbye")[0]);
?>