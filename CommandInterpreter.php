<?php
/*
 * Command Interpreter
 * 
 * Interprets commands
 */

class CommandInterpreter {

    private $commandList = array();
    private $verbose = True;
    private $mood = "friently";

    function initialize() {
        $this->log("Initialized");
    }

    function loadCommands() {
        $dir = new DirectoryIterator('commands');
        foreach ($dir as $fileinfo) {
            if (!$fileinfo->isDot()) {
                $json = file_get_contents("commands/".$fileinfo->getFilename());
                $command = json_decode($json);
                $this->commandList[$command->name] = $command;
               $this->log("Loaded Command: ".$command->name);
            }
        }
    }

    // interprets a command
    // returns an array: [ text to say, action to perform]
    function interpret($input) {
        $command = $this->commandList[$input];
        if(isset($command)) {
            $say = "";
            if(isset($command->speech)) {
                if($this->mood == "friendly") {$sayOptions = $command->speech->friendly;}
                elseif($this->mood == "mean") {$sayOptions = $command->speech->mean;}
                else {$sayOptions = $command->speech->default;}
                $say = $sayOptions[mt_rand(0, count($sayOptions) -1)];
            }
            return [$say, $command->action];
        } else {
            return ["ERROR: Command not recognized.", null];
        }
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