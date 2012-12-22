<?php
require 'twilio-php/Services/Twilio.php';

class Child {
  public $name;
  public $male;
}

$norah = new Child();
$norah->name = "Norah";
$norah->male = false;

$finn = new Child();
$finn->name = "Finn";
$finn->male = true;

$kids = array($norah, $finn);

$response = new Services_Twilio_Twiml();

$child_id = isset($_REQUEST['child']) ? $_REQUEST['child'] : null;
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;

switch($action) {
  case 'menu':
    $digit = isset($_REQUEST['Digits']) ? $_REQUEST['Digits'] : null;
    $child_id = $digit - 1;
    $child = $kids[$child_id];
    $name = $child->name;
    $gather = $response->gather(array('numDigits' => 1, 'action' => "santa.php?action=choice&child=$child_id", 'method' => 'GET'));
    $gather->say("This is about $name, isn't it? Press 1 if you want to check on how $name is doing. Press 2 if you want to report an incident.");
    break;
  case 'choice':
    $digit = isset($_REQUEST['Digits']) ? $_REQUEST['Digits'] : null;
    $child = $kids[$child_id];
    $name = $child->name;
    $pronoun = $child->male ? 'his' : 'her';

    if($digit == 1) {
      // status
      $response->say("Great news! $name is on the good list! Santa's elves are loading $pronoun presents onto the sleigh right now");
      $response->hangup();
    } else if($digit == 2) {
      // incident
      $gather = $response->gather(array('numDigits' => 1, 'action' => "santa.php?action=report&child=$child_id", 'method' => 'GET'));
      $gather->say("Press 1 if $name has been good. Press 2 if $name has been naughty");
    }
    break;
  case 'report':
    $digit = isset($_REQUEST['Digits']) ? $_REQUEST['Digits'] : null;
    $child = $kids[$child_id];
    $name = $child->name;
    $pronoun = $child->male ? 'his' : 'her';

    if($digit == 1) {
      $response->say("That sounds promising. What has $name done?");
      $response->pause(array('length' => '10'));
      $response->say("That is awesome! All the elves are delighted to hear that. They're putting $pronoun name at the top of the good list");
      $response->hangup();
    } else if($digit == 2) {
      $response->say("Oh, dear. That doesn't sound good. What has $name done?");
      $response->pause(array('length' => '10'));
      $response->say("Ok, that's very disappointing. We'll pass that information on to Santa's elves. If $name doesn't start being good soon, she'll be getting reindeer poo for Christmas!");
      $response->hangup();
    }
    break;
  default:
    $gather = $response->gather(array('numDigits' => 1, 'action' => 'santa.php?action=menu', 'method' => 'GET'));
    $gather->say('Merry Christmas! Welcome to the Santa Clause emergency helpline');
    break;
}
print $response

?>