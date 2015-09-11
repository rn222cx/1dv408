<?php

class DateTimeView {


	public function show() {

		$weekdayString = date('l');
		$dateString = date('jS \of F Y');
		$timeString = date('H:i:s');

		return '<p>' . $weekdayString . ", the " . $dateString . ", The time is " . $timeString . '</p>';
	}
}