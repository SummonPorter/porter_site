<?php
	
	class ServerResponse {
		public $responseString = '';
		public $responseCode = '';
	}
	
	class PostData {
		public $valid = true;
		public $service = '';
		public $details = '';
		public $address = '';
		public $city = '';
		public $date = '';
		public $time = '';
		public $name = '';
		public $email = '';
		public $phone = '';
	}
	
	function validPostData($data) {
		if ($data)
			return strlen($data) > 0;
		return false;
	}
	
	function parsePostData() {
		$serverPost = file_get_contents('php://input');
				
		$data = new PostData();
		if (!$serverPost)
			return null;
		
		$serverPost = json_decode($serverPost);
				
		if (validPostData($serverPost->service))
			$data->service = $serverPost->service;
		else
			$data->valid = false;
		
		if (validPostData($serverPost->details))
			$data->details = $serverPost->details;
		else
			$data->valid = false;
		
		if (validPostData($serverPost->address))
			$data->address = $serverPost->address;
		else
			$data->valid = false;
		
		if (validPostData($serverPost->city))
			$data->city = $serverPost->city;
		else
			$data->valid = false;
			
		if (validPostData($serverPost->date))
			$data->date = $serverPost->date;
		else
			$data->valid = false;
		
		if (validPostData($serverPost->time))
			$data->time = $serverPost->time;
		else
			$data->valid = false;
		
		if (validPostData($serverPost->name))
			$data->name = $serverPost->name;
		else
			$data->valid = false;
			
		if (validPostData($serverPost->email))
			$data->email = $serverPost->email;
		else
			$data->valid = false;
			
		if (validPostData($serverPost->phone))
			$data->phone = $serverPost->phone;
		else
			$data->valid = false;
		
		return $data;
	}
	
	function formatEmailHeader($data) {
		$header = 'MIME-Version: 1.0' . "\r\n";
		$header .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$header .= 'From: Pocket Porter App' . "\r\n" . 'Reply-To: ' . $data->name . '<' . $data->email . '>' . "\r\n";
		return $header;
	}
	
	function formatEmailSubject($data) {
		return $data->service;
	}
	
	function formatEmailBody($data) {
		return '<html><body><b>Service: </b>' . $data->service . "\r\n" . '<br /><b>Details: </b>' . $data->details . "\r\n" . '<br /><br /><b>Address: </b>' . $data->address . "\r\n" . '<br /><b>City :</b>' . $data->city . "\r\n" . '<br /><b>Date: </b>' . $data->date . "\r\n" . '<br /><b>Time: </b>' . $data->time . "\r\n" . '<br /><br /><b>Name: </b>' . $data->name . "\r\n" . '<br /><b>Email: </b>' . $data->email . "\r\n" . '<br /><b>Phone: </b>' . $data->phone . "\r\n" . '</body></html>';
	}
	
	$response = new ServerResponse();
	$data = parsePostData();
	
	if (!$data) {
		$response->responseCode = 0;
		$response->responseString = 'Network error.' . "\n" . 'Please try again.';
	}
	else {
		if ($data->valid) {
			$mailSent = mail('jonathan@pocketporter.co', formatEmailSubject($data), formatEmailBody($data), formatEmailHeader($data));
			
			if ($mailSent) {
				$response->responseCode = 1;
				$response->responseString = 'We have received your request.' . "\n" . 'We\'ll contact you soon.';
			} else {
				$response->responseCode = 0;
				$response->responseString = 'Internal server error.' . "\n" . 'Please try again.';
			}
		}
		else {
			$response->responseCode = 0;
			$response->responseString = 'Please ensure your information is correct.';
		}
	}
	
	header('Content-Type: application/json');
	echo json_encode($response);
?>