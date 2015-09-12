<?php

class DateTimeView {


	public function show() {
		date_default_timezone_set('Europe/Stockholm');

		$weekdayString = date('l');
		$dateString = date('jS \of F Y');
		$timeString = date('H:i:s');

		return '<p>' . $weekdayString . ", the " . $dateString . ", The time is " . $timeString . '</p>';
	}
}