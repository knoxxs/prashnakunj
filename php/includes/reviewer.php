<?php
require_once __DIR__.'/user.php';
require_once __DIR__.'/base.php';
require_once __DIR__.'/review.php';

class Reviewer extends User{

	protected $reviewHistoryList, $toBeReviewList;

	public function __construct($userName){
		parent::__construct($userName);

		$this->fetchSubscriptionList();
		$this->reviewHistoryList = array();
		$this->toBeReviewList = array();

	}

	public function getReviewHistoryList(){
		if(count($this->reviewHistoryList) == 0){
			$this->fetchReviewHistoryList();
		}
		return $this->reviewHistoryList;
	}

	public function getReviewHistoryListArray(){
		$list = $this->getReviewHistoryList();
		$jsonList=array();
		foreach ($list as $key => $value) {
			array_push($jsonList, $value->toArray());
		}
		return $jsonList;
	}

	public function fetchReviewHistoryList($num=MORE_SIZE){
		$len = count($this->reviewHistoryList);
		//Fetching Questions
		$db = $this->getDb();
		$db->query("SELECT QID,timeStamp,userName FROM Question WHERE reviewer=? ORDER BY timeStamp DESC LIMIT " . $len . "," . $num , array($this->userName));
		$records = $db->fetch_assoc_all();
		foreach ($records as $key => $value){
			array_push($this->reviewHistoryList, new Review( $value['QID']) );
		}

		$db->query("SELECT QID,userName,timeStamp FROM Suggestion WHERE reviewerId=? ORDER BY timeStamp DESC LIMIT " . $len . "," . $num , array($this->userName));
		$records = $db->fetch_assoc_all();
		foreach ($records as $key => $value){
			array_push($this->reviewHistoryList, new Review( $value['QID'], $value['userName'], $value['timeStamp']) );
		}
		$this->result['head']['status'] = 200;
	}

	public function getToBeReviewList(){
		if(count($this->toBeReviewList) == 0){
			$this->fetchToBeReviewList();
		}
		return $this->toBeReviewList;
	}

	public function getToBeReviewListArray(){
		$list = $this->getToBeReviewList();
		$jsonList=array();
		foreach ($list as $key => $value) {
			array_push($jsonList, $value->toArray());
		}
		return $jsonList;
	}

	public function fetchToBeReviewList($num=MORE_SIZE){
		$len = count($this->toBeReviewList);

		$db = $this->getDb();
		$db->query("SELECT QID,timeStamp,userName FROM Question WHERE locked=0 AND reviewer IS NULL ORDER BY timeStamp DESC LIMIT " . $len . "," . $num , array($this->userName));
		$records = $db->fetch_assoc_all();
		foreach ($records as $key => $value){
			array_push($this->toBeReviewList, new Review( $value['QID']) );
		}
	
		$db->query("SELECT QID,userName,timeStamp FROM Suggestion WHERE locked=0 AND reviewerId IS NULL ORDER BY timeStamp DESC LIMIT " . $len . "," . $num , array($this->userName));
		$records = $db->fetch_assoc_all();
		foreach ($records as $key => $value){
			array_push($this->toBeReviewList, new Review( $value['QID'], $value['userName'], $value['timeStamp']) );
		}

		$this->result['head']['status'] = 200;
	}


	public function setLock($QID, $suggestionUserName=null, $suggestionTimeStamp=null){
		$review = new Review($QID, $suggestionUserName, $suggestionTimeStamp);
		$review->lockReview();
		$_SESSION['locked'] = serialize($review);
		return $review->result;		
	}
	public function removeLock(){
		if($this->validateVar($_SESSION['locked'])){
			$review = unserialize($_SESSION['locked']);
			
			if($review->unlockReview()){
				unset($_SESSION['locked']);
				unset($_SESSION['LAST_ACTIVITY']);
				$result = array('head' => $review->result['head'], 'body' => '');
				return true;
			}else{
				$result = $review->result;
				return false;
			}
		}else{
			$result = array('head' => array('status' => 400, 'message'=>'No lock exists'), 'body' => '') ;
			return true;
		}
	}

} 
