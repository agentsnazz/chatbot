<?php

class Greeting implements Action {

    function definiition() {
        return "
            Action: Greeting\n
            \n
            Simply outputs text to the console.\n
        ";
    }
    
    function action() {
        echo "Hello from the Greeting class!";;
        return "Greeting sent.";
    }
}

?>
