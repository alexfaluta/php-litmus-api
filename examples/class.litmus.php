<?php
	class Litmus {
		protected $subdomain, $username, $password;
		
		public function __construct($subdomain, $username, $password) {
			$this->subdomain = $subdomain;
			$this->username  = $username;
			$this->password  = $password;
		}
		
		private function get_request($url) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://' . $this->subdomain . '.litmus.com/' . $url);
			curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->password);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml', 'Accept: application/xml'));
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			$result = curl_exec($ch);
			
			return $result;
		}
		
		private function post_request($url, $post_data) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://' . $this->subdomain . '.litmus.com/' . $url);
			curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->password);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml', 'Accept: application/xml', 'Content-length: ' . strlen($post_data)));
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			$result = curl_exec($ch);
			
			return $result;
		}
		
		//Test Set Methods
		//Returns details of a single test.
		public function tests() {
			return $this->get_request('tests.xml');
		}
		
		//Returns details of a single test.
		public function tests_show($id) {
			return $this->get_request('tests/' . $id . '.xml');
		}
		
		//Updates a test in your account. This is used for publishing results publicly or changing a test's title.
		public function tests_update($id, $name, $public_sharing) {
			//Prepare request
			$post_data = '
				<?xml version="1.0"?>
				<test_set>
					<public_sharing>' . $public_sharing . '</public_sharing>
					<name>' . $name . '</name>
				</test_set>
			';
			
			//Return result
			return $this->post_request('tests/' . $id . '.xml', $post_data);
		}
		
		//Test Set Version Methods
		//Returns details of a single version for a particular test.
		public function versions_show($id, $version) {
			return $this->get_request('tests/' . $id . '/versions/' . $version . '.xml');
		}
		
		//Creating a new version of a web page test will re-test the same URL immediately.
		public function versions_create($id) {
			return $this->post_request('tests/' . $id . '/versions.xml', '');
		}
		
		//To reduce the strain on the Litmus servers, and to reduce the bandwidth you use to check for test completion, there is a special poll method that can be used.
		public function versions_poll($id, $version) {
			return $this->get_request('tests/' . $id . '/versions/' . $version . '/poll.xml');
		}
		
		//????Result Methods
		//?Used to retrieve details of a single result, useful when used in conjunction with the versions/poll method while waiting for individual results to complete.
		public function results_show($id, $version, $result_id) {
			return $this->get_request('tests/' . $id . '/versions/' . $version . '/results/' . $result_id . '.xml');
		}
		
		//This method is used to update the properties of a result.
		public function results_update($id, $version, $result_id) {
			return $this->get_request('tests/' . $id . '/versions/' . $version . '/results/' . $result_id . '.xml');
		}
		
		//Page Methods
		//Creates a new web page test in your account.
		public function pages_create() {
			//Prepare request
			$clients = '';
			return $this->post_request('pages.xml', $post_data)
		}
		
		//Email Methods
	}	
?>