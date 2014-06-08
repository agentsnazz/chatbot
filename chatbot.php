<?php

// set your Jabber server hostname, username, and password here
$userAtServer = explode("@", $argv[0]);
define("JABBER_SERVER",$userAtServer[1]);
define("JABBER_USERNAME",$userAtServer[0]);
define("JABBER_PASSWORD",$argv[1]);

define("RUN_TIME",120);  // set a maximum run time of 60 seconds
define("CBK_FREQ",1);   // fire a callback event every second

define("VERSION","bravo");

// This class handles all events fired by the Jabber client class; you
// can optionally use individual functions instead of a class, but this
// method is a bit cleaner.
class TestMessenger {
    
    function TestMessenger(&$jab) {
        $this->jab = &$jab;
        $this->first_roster_update = true;
        
        echo "Created!\n";
        $this->countdown = 0;
    }
    
    // called when a connection to the Jabber server is established
    function handleConnected() {
        echo "Connected!\n";
        
        // now that we're connected, tell the Jabber class to login
        echo "Authenticating ...\n";
        $this->jab->login(JABBER_USERNAME,JABBER_PASSWORD);
    }
    
    // called after a login to indicate the the login was successful
    function handleAuthenticated() {
        echo "Authenticated!\n";
        
        
        echo "Fetching service list and roster ...\n";
        
        // browser for transport gateways
        $this->jab->browse();
        
        // retrieve this user's roster
        $this->jab->get_roster();
        
        // set this user's presence
        $this->jab->set_presence("","Online");
    }
    
    // called after a login to indicate that the login was NOT successful
    function handleAuthFailure($code,$error) {
        echo "Authentication failure: $error ($code)\n";
        
        // set terminated to TRUE in the Jabber class to tell it to exit
        $this->jab->terminated = true;
    }
    
    // called periodically by the Jabber class to allow us to do our own
    // processing
    function handleHeartbeat() {
        echo "Heartbeat - ";
        
        if ($this->commanded==1) {
            //Start thinking
            $this->jab->composing($this->last_msg_from,$this->last_msg_id);
            $command = strtolower($this->last_message);

            echo "Command Received:\n";
            echo "            '".$command."'\n";
            

            if (strcmp($command, "robot, make me a sandwich")==0) {
                echo "          - Command Recognized: sandwich\n";
                $this->jab->composing($this->last_msg_from,$this->last_msg_id,false);
                $this->jab->message($this->last_msg_from,"chat",NULL,"Sorry, I'm all out of mustard.");
            } elseif (strcmp($command, "robot, shut down")==0) {
                echo "          - Command Recognized: exiting\n";
                $this->jab->composing($this->last_msg_from,$this->last_msg_id,false);
                $this->jab->message($this->last_msg_from,"chat",NULL,"Goodbye!");
                exit;
            } else {
                echo "          - Command Not Recognized\n";
                $this->jab->message($this->last_msg_from,"chat",NULL,"Sorry, I don't recognize that command.");
            }
            $this->commanded = 0;
        } else {
            echo "Waiting for incoming message ...\n";
        }
    }
    
    // called when an error is received from the Jabber server
    function handleError($code,$error,$xmlns) {
        echo "Error: $error ($code)".($xmlns?" in $xmlns":"")."\n";
    }
    
    // called when a message is received from a remote contact
    function handleMessage($from,$to,$body,$subject,$thread,$id,$extended) {
        if (False) {
            echo "Incoming message!\n";
            echo "From: $from\n";
            echo "To: $to\n";
            echo "Subject: $subject\n";
            echo "Thread; $thread\n";
            echo "Body: $body\n";
            echo "ID: $id\n";
            var_dump($extended);
            echo "\n";
        }
        
        $this->last_message = $body;
        
        $this->last_msg_id = $id;
        $this->last_msg_from = $from;

        $this->commanded = 1;
    }
    
    function _contact_info($contact) {
        return sprintf("Contact %s (JID %s) has status %s and message %s\n",$contact['name'],$contact['jid'],$contact['show'],$contact['status']);
    }
    
    function handleRosterUpdate($jid) {
        foreach ($this->jab->roster as $k=>$contact) {
            echo print_r($contact, true);
        }
        
        if ($this->first_roster_update) {
            // the first roster update indicates that the entire roster has been
            // downloaded for the first time
            echo "Roster downloaded:\n";
            
            foreach ($this->jab->roster as $k=>$contact) {
                echo $this->_contact_info($contact);
            }   
            $this->first_roster_update = false;
        } else {
            // subsequent roster updates indicate changes for individual roster items
            $contact = $this->jab->roster[$jid];
            echo "Contact updated: " . $this->_contact_info($contact);
        }
    }
    
    function handleDebug($msg,$level) {
        echo "DBG: $msg\n";
    }
    
}

// include the Jabber class
require_once("libraries\class_Jabber.php");

// create an instance of the Jabber class
$display_debug_info = true;
$jab = new Jabber($display_debug_info);

// create an instance of our event handler class
$test = new TestMessenger($jab);

// set handlers for the events we wish to be notified about
$jab->set_handler("connected",$test,"handleConnected");
$jab->set_handler("authenticated",$test,"handleAuthenticated");
$jab->set_handler("authfailure",$test,"handleAuthFailure");
$jab->set_handler("heartbeat",$test,"handleHeartbeat");
$jab->set_handler("error",$test,"handleError");
$jab->set_handler("message_normal",$test,"handleMessage");
$jab->set_handler("message_chat",$test,"handleMessage");
//$jab->set_handler("debug_log",$test,"handleDebug");
$jab->set_handler("rosterupdate",$test,"handleRosterUpdate");

echo "Connecting ...\n";

// connect to the Jabber server
if (!$jab->connect(JABBER_SERVER)) {
    die("Could not connect to the Jabber server!\n");
}

// now, tell the Jabber class to begin its execution loop
$jab->execute(CBK_FREQ,RUN_TIME);

// Note that we will not reach this point (and the execute() method will not
// return) until $jab->terminated is set to TRUE.  The execute() method simply
// loops, processing data from (and to) the Jabber server, and firing events
// (which are handled by our TestMessenger class) until we tell it to terminate.
//
// This event-based model will be familiar to programmers who have worked on
// desktop applications, particularly in Win32 environments.

// disconnect from the Jabber server
$jab->disconnect();
?>