<?php
	class Litmus {
		//Protected variables
		protected $subdomain, $username, $password;
		
		//Construct
		public function __construct($subdomain, $username, $password) {
			$this->subdomain = $subdomain;
			$this->username  = $username;
			$this->password  = $password;
		}
		
		//GET request
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
		
		//POST request
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
		
		//TEST SET METHODS
		//Returns details for all tests.
		public function tests() {
			return $this->get_request('tests.xml');
		}
		
		//Returns details of a single test using specific id.
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
		
		//TEST SET VERSION METHODS
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
		
		//RESULTS METHODS
		//Used to retrieve details of a single result, useful when used in conjunction with the versions/poll method while waiting for individual results to complete.
		public function results_show($id, $version, $result_id) {
			return $this->get_request('tests/' . $id . '/versions/' . $version . '/results/' . $result_id . '.xml');
		}
		
		//This method is used to update the properties of a result.
		public function results_update($id, $version, $result_id, $state) {
			//Create XML template
			$post_data = '
				<?xml version="1.0" encoding="UTF-8"?>
				<result>
					<check_state>' . $state . '</check_state>
				</result>
			';
			
			//Return results
			return $this->post_request('tests/' . $id . '/versions/' . $version . '/results/' . $result_id . '.xml', $post_data);
		}
		
		//Triggers a retest of just this client.
		public function results_retest($id, $version, $result_id) {
			return $this->post_request('tests/' . $id . '/versions/' . $version . '/results/' . $result_id . '/retest.xml', '');
		}
		
		//PAGE METHODS
		//Creates a new web page test in your account.
		public function pages_create($clients, $url) {
			//Prepare clients
			$clients_xml = '';
			foreach($clients as $client) {
				$clients_xml = $clients_xml . '<application><code>' . $client . '</code></application>' . "\n";
			}
			
			//Create XML template
			$post_data = '
				<?xml version="1.0"?>
				<test_set>
					<applications type="array">
						' . $clients_xml . '
					</applications>
					<save_defaults>false</save_defaults>
					<use_defaults>false</use_defaults>
					<url>' . $url . '</url>
				</test_set>
			';
			
			//Return results
			return $this->post_request('pages.xml', $post_data);
		}
		
		//Returns a list of web browsers available for testing.
		public function pages_clients() {
			//Get pages from server
			$pages_xml = $this->get_request('pages/clients.xml');
			
			//Prepare empty array
			$clients = array();
			
			//Get pages clients from XML
			$applications = new SimpleXMLElement($pages_xml);
			foreach($applications->testing_application as $application) {
				if($application->result_type == 'page') {
					array_push($clients, (string)$application->application_code);
				}
			}
			
			//Return results
			return $clients;
		}
		
		//EMAIL METHODS
		//Creates a new email test in your account.
		public function emails_create($clients, $body, $subject) {
			//Prepare clients
			$clients_xml = '';
			foreach($clients as $client) {
				$clients_xml = $clients_xml . '<application><code>' . $client . '</code></application>' . "\n";
			}
			
			//Create XML template
			$post_data = '
				<?xml version="1.0"?>
				<test_set>
					<applications type="array">
						' . $clients_xml . '
					</applications>
					<save_defaults>false</save_defaults>
					<use_defaults>false</use_defaults>
					<email_source>
						<body>' . htmlspecialchars($body) . '</body>
						<subject>' . $subject . '</subject>
					</email_source>
				</test_set>
			';
			
			//Return result
			return $this->post_request('emails.xml', $post_data);
		}
		
		//Returns a list of email clients available for testing.
		public function emails_clients() {
			//Get clients from server
			$clients_xml = $this->get_request('emails/clients.xml');
			
			//Prepare empty array
			$clients = array();
			
			//Get email clients from XML
			$applications = new SimpleXMLElement($clients_xml);
			foreach($applications->testing_application as $application) {
				if($application->result_type == 'email') {
					array_push($clients, (string)$application->application_code);
				}
			}
			
			//Return results
			return $clients;
		}
		
		//REPORT METHODS
		//Shows a list of the 20 most recent lists you've created within your Litmus account.
		public function reports_index() {
			return $this->get_request('reports.xml');
		}
		
		//Retrieves a single specified report (campaign).
		public function reports_show($id) {
			return $this->get_request('reports/' . $id . '.xml');
		}
		
		//Creates a new report if you have available credits.
		public function reports_create($name) {
			//Create XML template
			$post_data = '
				<?xml version="1.0"?>
				<report>
					<name>' . $name . '</name>
				</report>
			';
			
			//Retun results
			return $this->post_request('reports.xml', $post_data);
		}
		
		//CUSTOM METHODS
		//Select images for specific test and store into array
		public function select_tests_images($id) {
			//Return details of a single test
			$tests_show = $this->tests_show($id);
			
			//Prepare XML content
			$xml = new SimpleXMLElement($tests_show);
			
			//Create empty array
			$array = array();
			
			//Get all results
			$results = $xml->test_set_versions->test_set_version->results->result;
			foreach($results as $result) {
				//Prepare variables
				$result_id = (int)$result->id;
				$result_state = $result->state;
				$platform_name = $result->testing_application->platform_name;
				$application_long_name = $result->testing_application->application_long_name;

				//Store into array
				$array[$result_id] = array('result' => array($result_state, $platform_name, $application_long_name), 'images');

				//Get all images
				$result_images = $result->result_images->result_image;
				foreach($result_images as $result_image) {
					//Prepare variables
					$image_type = $result_image->image_type;
					$full_image = $result_image->full_image;
					$thumbnail_image = $result_image->thumbnail_image;
					
					//Store into array
					$array[$result_id]['image'][] = array($image_type, $full_image, $thumbnail_image);
				}
			}
			
			//Return result
			return $array;
		}
	}
?>