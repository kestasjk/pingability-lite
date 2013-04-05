<?php
/*
 * Output the logs from the last week as an HTML table; used when people view the script without running a check, to give status information.
 */
if( !defined('IN_CODE') ) die();

print '<h2>Pingability-lite</h2>';

if( !($result = $db->query("SELECT Id, EventType, EventDetails, Timestamp FROM Log WHERE Timestamp > ".(time()-7*24*60*60)." ORDER BY Timestamp DESC")) )
trigger_error("Error while getting recent log entries: ".$db->lastErrorMsg());

$first=true;
print '<table>';
while( $record = $result->fetchArray(SQLITE3_ASSOC) )
{
	if( $first )
	{
		print '<tr><th>'.implode('</th><th>', array_keys($record)).'</th></tr>';
		$first = false;
	}

	$record['Timestamp'] = date(DATE_RFC822, $record['Timestamp']);
	print '<tr><td>'.implode('</td><td>', array_values($record)).'</td></tr>';
}
print '</table>';