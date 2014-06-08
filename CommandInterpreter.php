<?php
/*
 * Command Interpreter
 * 
 * Interprets commands
 */

class CommandInterpreter {

    private $commandList = array();
    private $verbose = True;

    function initialize() {
        $this->log("Initialized");
    }

    function loadCommands() {
        $dir = new DirectoryIterator('commands');
        foreach ($dir as $fileinfo) {
            print("\n\n\nArray Size: ".sizeof($this->commandList));
            if (!$fileinfo->isDot()) {
                $json = file_get_contents("commands/".$fileinfo->getFilename());
                $command = json_decode($json);
                $this->commandList[$command->name] = $command;
               $this->log("Loaded Command: ".$command->name);
            }
        }

        var_dump($this->commandList);
        print("\n\n");
        print("\n\n\nArray Size: ".sizeof($this->commandList));
    }

    function interpret($command) {

    }

    function setVerbose($verb) {
        if (isset($verb) && is_bool($verb)) {
            $this->verbose = $verb;
        } else {
            $this->verbose = True;
        }
    }

    function log($string) {
        if($this->verbose) {print("[CommandInterpreter] ".$string."\n");}
    }

}

?>