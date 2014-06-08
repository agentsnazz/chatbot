<?php
/*
 * Command Interpreter
 * 
 * Interprets commands
 */

class CommandInterpreter {

    private $commandList = array();
    private $verbose = False;
    private $mood = "friendly";

    function initialize() {
        $this->log("Initialized");
    }

    function loadCommands() {
        $dir = new DirectoryIterator('commands');
        foreach ($dir as $fileinfo) {
            if (!$fileinfo->isDot()) {
                $json = file_get_contents("commands/".$fileinfo->getFilename());
                $command = json_decode($json);
                $this->commandList[strtolower($command->name)] = $command;
               $this->log("Loaded Command: ".$command->name);
            }
        }
    }

    // interprets a command
    // returns an array: [ text to say, action to perform]
    function interpret($input) {
        $command = $this->commandList[strtolower($input)];
        $say = "";
        $action = null;

        if(isset($command)) {
            if(isset($command->speech)) {
                if($this->mood == "friendly") {$sayOptions = $command->speech->friendly;}
                elseif($this->mood == "mean") {$sayOptions = $command->speech->mean;}
                else {$sayOptions = $command->speech->default;}
                $say = $sayOptions[mt_rand(0, count($sayOptions) -1)];
            }

            if(isset($command->action)) {
                $action = $command->action;
            }
            
            return [$say, $action];
        } else {
            return $this->interpret("Unrecognized");
        }
    }

    function setVerbose($verb) {
        if (isset($verb) && is_bool($verb)) {
            $this->verbose = $verb;
        } else {
            $this->verbose = True;
        }
    }

    function setMood($mood) {
        $this->mood = $mood;
    }

    function log($string) {
        if($this->verbose) {print("[CommandInterpreter] ".$string."\n");}
    }

}

?>