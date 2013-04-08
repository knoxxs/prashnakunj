<?php
require_once __DIR__.'/user.php';
require_once __DIR__.'/review.php';
require_once __DIR__.'/Answer.php';
require_once __DIR__.'/base.php';

class Reviewer extends Review{

	protected $reviewerId, $timeStamp, $answerAssociated;

	public function __construct($QID, $suggestionUserName=null, $suggestionTimeStamp=null, $reviewerId, $timeStamp){
		parent::__construct();
		$this->reviewerId = $reviewerId;
		$this->timeStamp = $timeStamp;

		$db->query("SELECT string FROM Answer WHERE QID='$this->QID' AND reviewerId='$this->reviewerId' AND timeStamp='$this->timeStamp'");
		$records = $db->fetch_assoc_all();
		$this->answerAssociated = new Answer($this->QID, $records['string'], $this->timeStamp, $this->reviewerId);
	}
}