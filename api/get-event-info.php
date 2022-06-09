<?php
require_once __DIR__ . "/../inc/init.php";

if (is_event_started($conn)) exit(json_encode(array("state" => "Ends in", "date" => get_current_event_end_date($conn))));   //already started
if ($start_date = get_upcoming_event_start_date($conn)) exit(json_encode(array("state" => "Starts in", "date" => $start_date))); //not started yet
if (get_last_event_id($conn)) exit(json_encode(array("state" => "Ended", "date" => NULL))); //no more events
exit(json_encode(false));

?>