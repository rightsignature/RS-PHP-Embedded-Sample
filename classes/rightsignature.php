<?php

class RightSignature {
  public $base_url = "https://rightsignature.com";
  public $api_secure_token;
  public $debug = false;
  
  function __construct($api_secure_token) {
    $this->api_secure_token = $api_secure_token;
  }

  // 
	// Returns xml response from RightSignature's list of Documents
	// 
	// Arguments:
	// $tags (optional) - associative array of tag names and values that are associated with documents. 
	// 		ex. array('customized' => '', 'user id' => '123')
	// $query (optional) = Search term to narrow results. Should be URI encoded.
	// 		ex. 'State Street'
	// $state (optional) - Comma-joined Document states to filter results. States should be 'completed', 'pending', 'expired'.
	// 		ex. 'completed,pending'
	// $page (optional) - Page number offset. Default is 1.
	// 		ex. 1
	// $perPage (optional) - number of result per page to return.
	// 		Valid values are 10, 20, 30, 40, and 50. Default is 10.
	// 		ex. 20
	// $recipientEmail (optional) = Narrow results to documents sent by RightSignature API User to the given Recipient Email.
	// 		ex. 'a@abc.com'
	// $allDocs (optional) - Whether to show all documents from RightSignature Account. Should be 'true' or 'false'. 
	// 		Default is 'false', and this is only available if the RightSignature API User is the account owner or an account admin.
	// 		ex. 'false'
  function getDocuments($tags = NULL, $query = NULL, $page = NULL, $perPage = NULL, $recipientEmail = NULL, $allDocs = NULL) {
    $path = "/api/documents.xml";
		$params = array();
		
		// 
		// Combine arguments into URL parameters
		// 
		// Concatenate tags
		if (isset($tags)) {
			$tagArg = array();
			foreach (array_keys($tags) as $tag) {
				if (empty($tags[$tag]))
					$tagArg[] = "$tag";
				else
					$tagArg[] = "$tag:$tags[$tag]";
			}
			$params[] = "tags=" . join(',', $tagArg);
		}
		
		if (isset($query))
			$params[] = "search=" . urlencode($query);
		if (isset($state))
			$params[] = "state=" . urlencode($query);
		if (isset($page))
			$params[] = "page=" . urlencode($page);
		if (isset($perPage))
			$params[] = "per_page=" . urlencode($perPage);
		if (isset($recipientEmail))
			$params[] = "recipient_email=" . urlencode($recipientEmail);
		if (isset($allDocs))
			$params[] = "account=" . urlencode($allDocs);
		
		if (!empty($params))
			$url .= "?" . join('&', $params);
    if ($this->debug) { error_log("Getting documents at $url...\n"); }
		
    $header = Array();
    $response = $this->httpRequest($this->base_url . $path, "GET");
    return $response;
  }

  // 
	// Returns xml response from RightSignature's Document Details call
	// 
	// Arguments:
	// $guid - RightSignature Document GUID
	// 		ex. 'J1KHD2NX4KJ5S6X7S8'
  function getDocumentDetails($guid) {
    $url = $this->base_url . "/api/documents/" . $guid . ".xml";
		
    return $this->httpRequest($url, "GET");
  }

  // 
	// Returns xml response from RightSignature's list of Templates
	// 
	// Arguments:
	// $tags (optional) - associative array of tag names and values that are associated with the templates.
	// 		ex. array('customized' => '', 'user id' => '123')
	// $query (optional) = Search term to narrow results. Should be URI encoded.
	// 		ex. 'State Street'
	// $page (optional) - Page number offset. Default is 1.
	// 		ex. 1
	// $perPage (optional) - number of result per page to return.
	// 		Valid values are 10, 20, 30, 40, and 50. Default is 10.
	// 		ex. 20
  function getTemplates($tags = NULL, $query = NULL, $page = NULL, $perPage = NULL) {
    $path = "/api/templates.xml";
		$params = array();
		
		// 
		// Combine arguments into URL parameters
		// 
		// Concatenate tags
		if (isset($tags)) {
			$tagArg = array();
			foreach (array_keys($tags) as $tag) {
				if (empty($tags[$tag]))
					$tagArg[] = "$tag";
				else
					$tagArg[] = "$tag:$tags[$tag]";
			}
			$params[] = "tags=" . join(',', $tagArg);
		}
		
		if (isset($query))
			$params[] = "search=" . urlencode($query);
		if (isset($page))
			$params[] = "page=" . urlencode($page);
		if (isset($perPage))
			$params[] = "per_page=" . urlencode($perPage);
		
		if (!empty($params))
			$path .= "?" . join('&', $params);
		
    if ($this->debug) { error_log("Getting templates...\n"); }
		
    return $this->httpRequest($this->base_url . $path, "GET");
  }

	// 
	// Gets Template XML from given guid
	// 
	function getTemplateDetails($guid){
    $path = "/api/templates/$guid.xml";
    if ($this->debug) { error_log("Getting template $guid...\n"); }
		 
    return $this->httpRequest($this->base_url . $path, "GET");
	}
	
	// 
	// Prepackages 1 or more Templates so it creates a RightSignature Document from the RightSignature Templates.
	// 	Returns a guid for the new Document
	// 
	// Arguments:
	// $guids - array of RightSignature Template GUIDs
	// 		ex. a_b2asc2a_123
	// $callbackURL (Optional) - URL to callback when the Document is created. 
	// 	If none is specified, the default in RightSignature's Account settings will be used
	// 		ex. 'http://mysite/template_callback.php'
	function prepackageTemplate($guids, $callbackURL=NULL) {
		$path = "/api/templates/". join(',', $guids) . "/prepackage.xml";

		$xml = "<?xml version='1.0' encoding='UTF-8'?><template>";
		if (!empty($callbackURL))
			$xml .= "<callback_location>$callbackURL</callback_location>";
		$xml .= "</template>";
		
    $response = $this->httpRequest($this->base_url . $path, "POST", $xml);
		$new_guid = (string)(simplexml_load_string($response)->guid);
    return $new_guid;
	}
	
	// 
	// Generates a RightSignature Document from the given Template GUID and arguments
	// 
	// Arguments:
	// $guid - RightSignature's Template GUID
	// 		ex. a_b2asc2a_123
	// $subject - Email subject for document
	// $roles - associative arrays of roles for document, locked will not allow the redirected user to modify the value.
	// 		Formatted like [role_name => ['name' => Person's Name, 'email' => email, 'locked' => 'true' or 'false']].
	// 		ex. array('Employee' => array('name' => 'john smith', 'email' => 'john@example.com', 'locked' => 'false'), 
	// 							'Owner' => array('name' => 'jane smith', 'email' => 'jane@example.com', 'locked' => 'true'))
	// $merge_fields - associative array of mergefields with properties, locked will not allow the redirected user to modify the value.
	// 		Formatted like [merge_field_name => ['value' => value, 'locked' => 'true' or 'false']]
	// 		ex. array('Address' => array('value' => '123 Maple Lane', 'locked' => 'false'), 
	// 							'User ID' => array('value' => '123', 'locked' => 'true'))
	// $tags (Optional) - associative array of tag names and values to associate with template. 
	// 		ex. array('template' => '', 'user id' => '123', 'property' => '111')
	// $description (Optional) - description of document for signer to see
	// $callbackURL (Optional) - string of URL for RightSignature to POST document details to after Template gets created, viewed, and completed (all parties have signed). 
	// 		Tip: add a unique parameter in the URL to distinguish each callback, like the template_id.
	// 		NULL will use the default callback url set in the RightSignature Account settings page (https://rightsignature.com/oauth_clients).
	// 		ex. 'http://mysite/document_callback.php?template_id=123'
	// $expires_in (Optional) - integer of days to expire document, allowed values are 2, 5, 15, or 30.
	function sendTemplate($guid, $subject, $roles, $merge_fields, $tags = array(), $description = NULL, $callbackURL = NULL, $expires_in = 30){
    $path = "/api/templates.xml";
    if ($this->debug) { error_log("Sending templates...\n"); }

		// Create XML for template prefile request
		$xml = "<?xml version='1.0' encoding='UTF-8'?><template>";
		$xml .= "<guid>$guid</guid>";
		$xml .= "<subject>$subject</subject>";
		$xml .= "<action>send</action>";
		$xml .= "<expires_in>$expires_in days</expires_in>";
		if (!empty($description))
			$xml .= "<description>$description</description>";
		if (!empty($merge_fields)) {
			$xml .= "<merge_fields>";
			foreach(array_keys($merge_fields) as $mf) {
				$xml .= "<merge_field merge_field_name=\"$mf\">";
				$xml .= "<value>". $merge_fields[$mf]['value'] . "</value>";
				$xml .= "<locked>" . $merge_fields[$mf]['locked'] . "</locked>";
				$xml .= "</merge_field>";
			}
			$xml .= "</merge_fields>";
		}
		if (!empty($roles)) {
			$xml .= "<roles>";
			foreach(array_keys($roles) as $role) {
				$xml .= "<role role_name=\"$role\">";
				$xml .= "<name>" . $roles[$role]['name'] . "</name>";
				$xml .= "<email>" . $roles[$role]['email'] . "</email>";
				if (!empty($roles[$role]['locked']))
					$xml .= "<locked>" . $roles[$role]['locked'] . "</locked>";
				$xml .= "</role>";				
			}
			$xml .= "</roles>";
		}
		if (!empty($tags)) {
			$xml .= "<tags>";
			foreach (array_keys($tags) as $tag) {
				$xml .= "<tag><name>$tag</name>";				
				if (!empty($tags[$tag]))
					$xml .= "<value>$tags[$tag]</value>";
				$xml .= "</tag>";
			}
			$xml .= "</tags>";
		}
		if (!empty($callbackURL))
			$xml .= "<callback_location>$callbackURL</callback_location>";
		$xml .= "</template>";

    if ($this->debug) {
			error_log("BUILT XML \n$xml\n");
    }

		// Send request to RightSignature.com API
    $response = $this->httpRequest($this->base_url . $path, "POST", $xml);

    return $response;
	}

	// 
	// Generates a Embedded RightSignature Document from the given Template GUID and arguments
	// 
	// Arguments:
	// $guid - RightSignature's Template GUID
	// 		ex. a_b2asc2a_123
	// $subject - Email subject for document
	// $roles - associative arrays of roles for document with only the user's name. 
	// 		The Email will be hardcoded to 'noemail@rightsignature.com', and it will be locked
	// 		Formatted like [role_name => ['name' => Person's Name]].
	// 		ex. array('Employee' => array('name' => 'john smith', 'email' => 'john@example.com', 'locked' => 'false'), 
	// 							'Owner' => array('name' => 'jane smith', 'email' => 'jane@example.com', 'locked' => 'true'))
	// $merge_fields - associative array of mergefields with properties, locked will not allow the redirected user to modify the value.
	// 		Formatted like [merge_field_name => ['value' => value, 'locked' => 'true' or 'false']]
	// 		ex. array('Address' => array('value' => '123 Maple Lane', 'locked' => 'false'), 
	// 							'User ID' => array('value' => '123', 'locked' => 'true'))
	// $tags (Optional) - associative array of tag names and values to associate with template. 
	// 		ex. array('template' => '', 'user id' => '123', 'property' => '111')
	// $description (Optional) - description of document for signer to see
	// $callbackURL (Optional) - string of URL for RightSignature to POST document details to after Template gets created, viewed, and completed (all parties have signed). 
	// 		Tip: add a unique parameter in the URL to distinguish each callback, like the template_id.
	// 		NULL will use the default callback url set in the RightSignature Account settings page (https://rightsignature.com/oauth_clients).
	// 		ex. 'http://mysite/document_callback.php?template_id=123'
	// $expires_in (Optional) - integer of days to expire document, allowed values are 2, 5, 15, or 30.
	function sendSelfSigningDocument($guid, $subject, $roles, $merge_fields, $tags = array(), $description = NULL, $callbackURL = NULL, $expires_in = NULL) {
		$locked_roles = array();
		foreach (array_keys($roles) as $role_name) {
			$locked_roles[$role_name] = array('name' => $roles[$role_name]['name'], 'email' => 'noemail@rightsignature.com', 'locked' => 'true');
		}
		
		return $this->sendTemplate($guid, $subject, $locked_roles, $merge_fields, $tags, $description, $callbackURL, $expires_in);
	}

	// 
	// Generates build template token and parses out redirect-token for builder page.
	// 
	// Arguments:
	// $mergefields - array of Merge Field Names to add to Template.
	// 		ex. array('Address', 'User ID')
	// $acceptableRoles - array of Role Names that are only usable in Template.
	// 		ex. array('Owner', 'Leaser', 'Guardian')
	// $tags (Optional) - associative array of tag names and values to associate with template. 
	// 		ex. array('template' => '', 'user id' => '123', 'property' => '111')
	// $callbackURL (Optional) - string of URL for RightSignature to POST document details to after Template gets created
	// 		ex. 'http://mysite/template_callback.php'
	// $redirectURL (Optional) - string of URL for RightSignature to redirect token user after Template is created
	// 		ex. 'http://mysite/'
	// 
	function createTemplateTokenURL($mergefields, $acceptableRoles, $tags = NULL, $callbackURL = NULL, $redirectURL = NULL) {
    $path = "/api/templates/generate_build_token.xml";
		if ($this->debug) { error_log("Generating build token...\n"); }

		// Create XML for token request
		$xml = "<?xml version='1.0' encoding='UTF-8'?><template>";
		if (!empty($mergefields)) {
			$xml .= "<acceptable_merge_field_names>";
			foreach($mergefields as $mf) {
				$xml .= "<name>$mf</name>";
			}
			$xml .= "</acceptable_merge_field_names>";
		}
		if (!empty($acceptableRoles)) {
			$xml .= "<acceptable_role_names>";
			foreach($acceptableRoles as $role) {
				$xml .= "<name>$role</name>";				
			}
			$xml .= "</acceptable_role_names>";
		}
		if (!empty($tags)) {
			$xml .= "<tags>";
			foreach (array_keys($tags) as $tag) {
				$xml .= "<tag><name>$tag</name>";				
				if (!empty($tags[$tag]))
					$xml .= "<value>$tags[$tag]</value>";
				$xml .= "</tag>";
			}
			$xml .= "</tags>";
		}
		if (!empty($callbackURL))
			$xml .= "<callback_location>$callbackURL</callback_location>";
		if (!empty($redirectURL))
			$xml .= "<redirect_location>$redirectURL</redirect_location>";
		$xml .= "</template>";
		
		if ($this->debug)
			error_log("Built XML\n$xml");

    $response = $this->httpRequest($this->base_url . $path, "POST", $xml);
		$redirectToken = simplexml_load_string($response)->{'redirect-token'};
    return "$this->base_url/builder/new?rt=$redirectToken";
	}

  function addUser($uname, $email) {
    $path = "/api/users.xml";
		if ($this->debug) { error_log("Adding user...\n"); }
    
		$xml = "<?xml version='1.0' encoding='UTF-8'?><user><name>$uname</name><email>$email</email></user>";
		if ($this->debug) { error_log($xml); }
    
    $response = $this->httpRequest($this->base_url . $path, "POST", $xml);
    return $response;
  }

	// 
	// Returns an response for signer redirect-tokens, so we can have 
	// the RightSignature Signature Pad embedded on our site.
	// 
	// Arguments:
	// $guid - RightSignature's Document GUID.
	// 		ex. 'J1KHD2NX4KJ5S6X7S8'
	// $redirectURL - URL for RightSignature to callback when a signer signs the document. 
	// 		This should be hit for each signer with parameters containing the signer GUID and document GUID.
	// 		ex. 'http://127.0.0.1:8888/signer_redirect.php'
	function getSingerLinks($guid, $redirectURL){
		// Calls API and sets the redirect_location to send in after each successful signature
		$path = "/api/documents/$guid/signer_links.xml?redirect_location=" . urlencode($redirectURL);
		if ($this->debug) { error_log("Generating build token...\n"); }
		
    $response = $this->httpRequest($this->base_url . $path, "GET");
		
    return $response;
	}

  function testPost() {
    $path = "/api/test.xml";
    $xml = "<?xml version='1.0' encoding='UTF-8'?><testnode>Hello World!</testnode>";
		if ($this->debug) {
		    error_log($xml);
		}
    $response = $this->httpRequest($this->base_url . $path, "POST", $xml);
    return $response;
  }
	
  function httpRequest($url, $method, $body = NULL) {
    if (!$method) {
      $method = "GET";
    };

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		// Set the headers with Secure Token
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("api-token: $this->api_secure_token"));
	// TODO: remove this block when finished debugging
	if ($this->debug) {
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);				// Ignore SSL cert
	}
    if ($body) {
      curl_setopt($curl, CURLOPT_POST, 1);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
			// Set the headers with Secure Token
      curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: text/xml;charset=utf-8", "api-token: $this->api_secure_token"));   
    }

    $data = curl_exec($curl);
	if (curl_error($curl)) {
		error_log("CURL ERROR: " . curl_error($curl));
	}
	
    if ($this->debug) {
      error_log($data . "\n");
    }
    curl_close($curl);
    return $data; 
  }

}
