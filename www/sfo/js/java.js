	// Clear empty form
	function clickclear(thisfield, defaulttext) {
		if (thisfield.value == defaulttext) {
		thisfield.value = "";
		}
	}	

	// Refill empty form
	function clickrecall(thisfield, defaulttext) {
		if (thisfield.value == "") {
		thisfield.value = defaulttext;
		}
	}