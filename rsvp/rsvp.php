<?PHP
require 'dbconnect.php';

$database = "bixandmud_guestlist";

$mysqli = new mysqli( DB_SERVER, DB_USER, DB_PASS, $database );

$v0 = [];
$v1 = [];
$v2 = [];
$v3 = [];
$v4 = [];
$v5 = [];
$v6 = [];

if (isset($_POST['search-submit'])) {

	$firstname = $_POST["first-name"];
	$lastname = $_POST["last-name"];

if ($firstname == "SecretFirst" && $lastname == "SecretLast") {  //secret user for couple to access rsvp response page
	include('php-include.html');
	echo '<div class="form-wrapper">
			<a class="rsvp-button" target="_blank" href="./backend.php">Guest Responses</a>
		</div>
		</body>
		</html>';
} else {


	$result = $mysqli->query("SELECT * FROM guestlist WHERE pair = (
					SELECT pair FROM guestlist WHERE 
					LOWER(firstname) = LOWER('$firstname') 
					AND LOWER(lastname) = LOWER('$lastname'))");

		if ($result->num_rows) {
			$i = 1;
			while ($row = $result->fetch_assoc()) {
				global $v0, $v1, $v2, $v3, $v4, $v5, $v6;
				if (strtolower($row['firstname']) == strtolower($firstname) && strtolower($row['lastname']) == strtolower($lastname)) {
					$v0 = $row;
				} else {
					${"v" . $i} = $row;
	   				$i++;
	   			}
			}//endwhile

			if ($v0['responded'] > 0) {
				$respondedecho = '<p class="note">Hi ' . $v0['firstname'] . ', you already rsvp' . "'" . 'd, are you sure you want to change it?</p>';
			} else {
				$respondedecho = '<p class="greeting">Hi ' . $v0['firstname'] . '!</p>';
			}

			include('php-include.html');
			echo '<div class="form-wrapper">' . $respondedecho . '
					
					<form id="rsvp-confirm-form" class="rsvp-form" action="rsvp.php" method="POST" target="rsvp-iframe">
						<p>Are you able to attend the wedding?</p>
						<div class="radio-wrapper">
					        <input class="radio" type="radio" id="yes-attending" name="guest[' . $v0['id'] . ']" value="yes" required>
					        <label for="yes-attending">Yes, absolutely!</label>
					  	</div>
					  	<div class="radio-wrapper">
					  		<input class="radio" type="radio" id="no-attending" name="guest[' . $v0['id'] . ']" value="no" required>
					  		<label for="no-attending">No, unfortunately.</label>
					    </div>
					    <p>If so, what meal would you like?</p>
					    <div class="radio-wrapper">
					        <input class="radio" type="radio" id="meat" name="meal[' . $v0['id'] . ']" value="1">
					        <label for="meat">Beef brisket</label>
					  	</div>
					  	<div class="radio-wrapper">
					  		<input class="radio" type="radio" id="fish" name="meal[' . $v0['id'] . ']" value="2">
					  		<label for="fish">Seared merluza</label>
					    </div>
					    <div class="radio-wrapper">
					  		<input class="radio" type="radio" id="veg" name="meal[' . $v0['id'] . ']" value="3">
					  		<label for="veg">Zucchini cakes</label>
					    </div>
					    <div>
		    				<button class="rsvp-button" type="submit" value="' . $v1['id'] . '" name="rsvp-submit">Confirm</button>
		    			</div>
					</form>
				</div>		
			</body>
			</html>';

		} else {//if no results found
			
			include('php-include.html');
			echo '<div class="form-wrapper">
					<p class="greeting">Sorry, your name doesn' . "'" . 't show up!<br>Try using the exact name on the invitation.</p>
					<a class="rsvp-button" href="rsvp-system.html">Try Again!</a>
				</div>
				</body>
				</html>';

		}//endif results

}//endif backend escape
}//endif searching


if (isset($_POST['rsvp-submit'])) {


    foreach ($_POST['guest'] as $key => $value) {
  
    	$partytype = $_POST['rsvp-submit'];
    	$guestkey = $key;
    	$responseecho = "";

    	if ($partytype) {
			$entourage = '<form id="rsvp-additional-form" class="rsvp-form" action="rsvp.php" method="POST" target="rsvp-iframe">
				    	<div>
				    		<p class="greeting smaller">You can respond for other members of your party, if you wish.</p>
				    		<a class="rsvp-button" href="rsvp-system.html">No thanks</a>
	    					<button class="rsvp-button" type="submit" value="' . $guestkey . '" name="rsvp-additional-submit">rsvp for others</button>
	    				</div>
					</form>';
		} else {
			$entourage = '';
		}

        if ($value == 'yes') {
            $attending = 1;
            $meal = ($_POST['meal'][$key]);
            $responseecho .= '<div class="form-wrapper">
					<p class="greeting">Yay, you' . "'" . 're coming!</p>
				' . $entourage . '</div>
				</body>
				</html>';
        } else if ($value == 'no') {
            $attending = 0;
            $meal = null;
            $responseecho .= '<div class="form-wrapper">
					<p class="greeting">Sorry you can' . "'" .'t make it!</p>
				' . $entourage . '</div>
				</body>
				</html>';
        }

        $mysqli->query("UPDATE guestlist SET attending = '$attending', meal = '$meal', responded = responded + 1 WHERE id = '$key'");
    }

    if ($partytype) {
    	include('php-include.html');
    	echo $responseecho;
    } else {
    	include('php-include.html');
    	echo '<div class="form-wrapper">
					<p class="greeting">Thanks for responding!</p>
				</div>
				</body>
				</html>';

    }

}//endif responding




if (isset($_POST['rsvp-additional-submit'])) {

$id = $_POST["rsvp-additional-submit"];

	$result = $mysqli->query("SELECT * FROM guestlist WHERE pair = (SELECT pair FROM guestlist WHERE id = '$id' ) AND id != '$id'");

		include('php-include.html');
		echo '<div class="form-wrapper">
				<form id="rsvp-confirm-form" class="rsvp-form" action="rsvp.php" method="POST" target="rsvp-iframe">';

		$secondaryresponseecho = "";

		while ($row = $result->fetch_assoc()) {
			if ($row['responded'] > 0) {

				$secondaryresponseecho .= '<p class="note">' . $row['firstname'] . ' has already replied.</p><br>';

			} else {

			$secondaryresponseecho .= '
					<p class="note">' . $row['firstname'] . ':</p>
					<div class="small-form-wrapper">

					<div class="radio-wrapper-small">
						<div class="radio-choice">
				        	<input class="radio" type="radio" id="yes-attending" name="guest[' . $row['id'] . ']" value="yes" required>
				        	<label for="yes-attending">Attending!</label>
				        </div>
				        <div class="radio-choice">
				  			<input class="radio" type="radio" id="no-attending" name="guest[' . $row['id'] . ']" value="no" required>
				  			<label for="no-attending">Not&nbsp;Attending.</label>
				  		</div>
				  		<div class="radio-choice">
				  		</div>
				    </div>
				    <div class="radio-wrapper-small">
				    	<div class="radio-choice">
					        <input class="radio" type="radio" id="meat" name="meal[' . $row['id'] . ']" value="1">
					        <label for="meat">Beef&nbsp;brisket</label>
						</div>
						<div class="radio-choice">
					  		<input class="radio" type="radio" id="fish" name="meal[' . $row['id'] . ']" value="2">
					  		<label for="fish">Seared&nbsp;merluza</label>
					  	</div>
					  	<div class="radio-choice">
					  		<input class="radio" type="radio" id="veg" name="meal[' . $row['id'] . ']" value="3">
					  		<label for="veg">Zucchini&nbsp;cakes</label>
					  	</div>
				    </div>
				</div>';

				}//endif double response

		}//endwhile
				echo $secondaryresponseecho;
				echo 	'<div>
		    				<button class="rsvp-button" type="submit" name="rsvp-submit">Confirm</button>
		    			</div>
					</form>
				</div>
				</body>
				</html>';

}//endif
			

$mysqli->close();


?>


