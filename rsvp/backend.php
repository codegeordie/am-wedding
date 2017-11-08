<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>RSVP Interface</title>
	<link href="https://fonts.googleapis.com/css?family=Quicksand|Sacramento|Raleway:200,300" rel="stylesheet">
	<link rel="stylesheet" href="backendstyles.css">
</head>
<body>
	
<?PHP
require 'dbconnect.php';

$database = "bixandmud_guestlist";

$mysqli = new mysqli( DB_SERVER, DB_USER, DB_PASS, $database );

$result = $mysqli->query("SELECT * FROM guestlist");

$attending = 0;
$notattending = 0;
$notresponded = 0;
$meal1 = 0;
$meal2 = 0;
$meal3 = 0;

	while ($row = $result->fetch_assoc()) {
		if ($row['attending'] == 1) {
			$attending++;
			${'meal' . $row["meal"]}++;

			echo '
			<div class="wrapper yes respond' . $row["responded"] . '">
				<p class="entry">' . $row["firstname"] . '</p>
				<p class="entry">' . $row["lastname"] . '</p>
				<p class="entry">' . $row["meal"] . '</p>
			</div>
			';


		} else if ($row['attending'] == 0 && $row['responded'] >= 1) {
			$notattending++;

			echo '
			<div class="wrapper no respond' . $row["responded"] . '">
				<p class="entry">' . $row["firstname"] . '</p>
				<p class="entry">' . $row["lastname"] . '</p>
			</div>
			';

		} else {
			$notresponded++;

			echo '
			<div class="wrapper lazy">
				<p class="entry">' . $row["firstname"] . '</p>
				<p class="entry">' . $row["lastname"] . '</p>
			</div>
			';

		}
		
	}//end while

echo '
	<div class="wrapper total">
		<p class="entry">Yes: ' . $attending . '</p>
		<p class="entry">No: ' . $notattending . '</p>
		<p class="entry">Radio Silence: ' . $notresponded . '</p>
		<p class="entry">Beef: ' . $meal1 . '</p>
		<p class="entry">Fish: ' . $meal2 . '</p>
		<p class="entry">Green things: ' . $meal3 . '</p>
	</div>
';

$mysqli->close();

?>


</body>
</html>