<?php
// HELP FILE COMING SOON
//	v0.2
if(!class_exists(InvalidArgumentException)) {
	class InvalidArgumentException extends Exception {
	}
}

class InvalidEmailException extends InvalidArgumentException {
	private $email;
	public function getEmail() {
		return $this->email;
	}
	public function __construct($message, $code, $email = NULL) {
		$this->email = $email;
		parent::__construct($message, $code);
	}
}
/*
 * TODO: Make a UTILITY Class
 * 								To Filter Emails
 * Complete: How to check if a class exits prior to declaring it
 * TODO: Check header References, Content-Transfer-Encoding
 * TODO: Add support for Message id
 * TODO: Example.... Command Line interface for accessing mail (Ajax based)
 * TODO: Add support for Replying to an eml message or Message-id or Message Source
 * TODO: multipart/related contained multipart/alternative as its child (Research and think how to implement)
 * TODO: add support for multipart/parallel and 
 * TODO: add support for multipart/digest
 * TODO: add support for appliation/* and
 * 													Postscript, octet-stream, others 
 * TODO: message/* content type
 * TODO: add support for image/*
 * TODO: add support for video/*
 */
//TODO: Check if Function exists before calling linkSize()
include_once 'resources.inc.php';
class OOM_EMAIL_PART {
	//TODO: How to restrict initialization of this class such that only OO_BASE class can initiate it 
	private $part_content;
	private $mime_type;
	private $size;
	private $encoding;
	private $charset;
	
	public function __construct($content, $type, $size = NULL, $encoding = NULL, $charset = NULL) {
		$this->part_content = $content;
		//TODO: Validate MIME_TYPE, for not including Header Injection
		$this->mime_type = $type;
		if($size === NULL) {
		// Complete: Find accurate function to calculate and store size
			$this->size = strlen($content);
		} else {
			$this->size = $size;
		}
		$this->encoding = $encoding;
		$this->charset = $charset;
	}
	private function overrideSize() {
		//TODO: How to use friend function in PHP to Give Override rights to OO_ABSTRACT_MAIL class
	}
	public function retSize() {
		return $this->size;
	}
	public function retMime() {
		return $this->mime_type;
	}
	public function retContent() {
		return $this->part_content;
	}
	public function retEncoding() {
		return $this->encoding;
	}
	public function retCharset() {
		return $this->charset;
	}
}

class OOM_EMAIL_TYPE {
	const TEXT_PLAIN = 1;
	const TEXT_HTML = 2;
	const MULTIPART_MIXED = 3;
	const MULTIPART_ALTERNATIVE = 4;
	
	public static function validate($type) {
		if($type == self::TEXT_PLAIN || $type == self::TEXT_HTML || $type == self::MULTIPART_ALTERNATIVE || $type==self::MULTIPART_MIXED) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	public static function getStringEquivalent($type) {
		if($type == self::TEXT_PLAIN) {
			return "text/plain";
		} else if($type == self::TEXT_HTML) {
			return "text/html";
		}  else if($type == self::MULTIPART_MIXED) {
			return "multipart/mixed";
		}  else if($type == self::MULTIPART_ALTERNATIVE) {
			return "multipart/alternative";
		}
	}
}
class OOM_DEFAULTS {
	const MIME = "text/plain";
	const CHARSET = "us-ascii"; 
}
class OOM_BASE {
// Completed: Stop header injection at sender name and body
// Complete: Add advance attachment control (Remote attachment, Remote file size)			
// Complete: Modularize Send function using Private functions
// Complete: Size of the file
// Complete: Limit total attachment size.
// Complete: Mail composition
// Complete: Convert /r/n to PHP_EOL
/*
 * Complete: Convert to addPart Where everything will be added as parts Except headers
 * headers will be modified accordingly according to parts
 * Complete: E-mail type function can be text/plain. text/html, multipart/alternative, multipart/mixed
 */
	// TODO: Check all the MIME suppored in email along with multipart and text
	/*
	 * Complete: Multiple sends: Stamp header and parts, recreate header and body before sending
	 * Complete: Add Return-Path: (Delivery failed notification) and Return-Receipt-To: (Read notification)
	 * Complete: (IMP) Decide how to handle error and whether or not to use $nothrow variable
	 * 				Consider option for two classes one that throws error and other which returns -ve value 
	 * TODO: Intelligent sending, Only reproduce those parts of headers which are modified. This can be achieved by makind prepared_variable name for each variable
	 * 
	 */
	
	/*
	 * MEMBERS
	 */
	private $to;
	private $subject;
	
	// For body
	private $body;
	private $part;
	
	// For header
	private $from;
	private $cc;
	private $bcc;
	private $senderName;
	private $replyTo;
	private $xMailer;
	private $priority;
	private $emailTYPE; 
	private $returnPath; // Delivery failed notification
	private $returnReceiptTo;	//	Read notification
	private $customXmailer;

	//Utility
	private $limitTotalAttachmentSize = 512000000; // Default max size = 500 MB
	private $currentFileSize;
	
	// Prepared data
	private $prepared_header;
	private $prepared_body; 
	private $prepared_to;

	// number of modification Modification 
	private $modification_current_header;
	private $modification_current_part;
	
	private $modification_last_header;
	private $modification_last_part; 
	
	/*
	 * METHODS
	 */
	//TODO: Whats the use of Exception Code?
	// TO variable
	public function addTo($to) {			//TODO: Add support for Names
		if(is_array($to)) {
			// Check if all are correct email address
			if(!filter_var_array($to, FILTER_VALIDATE_EMAIL)) {
				throw new Exception("Invalid Email", 1);
			}
			//add all to $this->to;
			foreach($to as $key=>$val) {
				$this->to[] = $val; 
			}
		} else { // If $to is single email
			if(!filter_var($to, FILTER_VALIDATE_EMAIL)) {
				throw new Exception("Invalid Email", 2);
			};
			$this->to[] = $to;
		}
		return 0;
	}
	public function clearTo() {
		$this->to = NULL;
		return 0;
	}
	
	// header variables
	public function setDeliveryFailedNotificationAddress($arg_email = NULL) {
		if(isset($arg_email)) {
			if(!filter_var($arg_email, FILTER_VALIDATE_EMAIL)) {
				throw new InvalidEmailException("Invalid email address", 1);
			}
			$this->returnPath = $arg_email;	
			$this->modificationHeader('returnPath');
		} else {
			if(isset($this->replyTo)) {
				$this->returnPath = $this->replyTo;
			} else if(isset($this->from)) {
				$this->returnPath = $this->from;
			} else {
				throw new LogicException("'Reply-to' and 'From' address are not set", 2);
			}
		}
		return 0;
	}
	public function clearDeliveryFailedNotificationAddress() {
		$this->returnPath = NULL;
		$this->modificationHeader('returnPath');
		return 0;
	}
	public function setReadNotificationAddress($arg_email = NULL) {
		if(isset($arg_email)) {
			if(!filter_var($arg_email, FILTER_VALIDATE_EMAIL)) {
				throw new InvalidEmailException("Invalid email address", 1);
			}
			$this->returnReceiptTo = $arg_email;
			$this->modificationHeader('returnReceiptTo');
		} else {
			if(isset($this->replyTo)) {
				$this->returnReceiptTo = $this->replyTo;
			} else if(isset($this->from)) {
				$this->returnReceiptTo = $this->from;
			} else {
				throw new LogicException("'Reply-to' and 'From' address are not set", 2);
			}
		}
		return 0;	
	}
	public function clearReadNotificationAddress() {
		$this->returnReceiptTo = NULL;
		$this->modificationHeader('returnReceiptTo');
		return 0;
	}
	public function addFrom($from) {
		// Validate $from
		if(!filter_var($from, FILTER_VALIDATE_EMAIL)) {
			throw new InvalidEmailException("Address not valid", 1, $from);
		}
		$this->from = $from;
		$this->modificationHeader('part');
		return 0;
	}
	public function clearFrom() {
		$this->from = NULL;
		$this->modificationHeader('from');
		return 0;
	}
	public function addSubject($sub) {
		if (!is_string($sub)) { 
			throw new InvalidArgumentException("Subject must be a string", 1);
		}
		$this->subject = $sub;
		$this->modificationHeader('subject');
		return 0;
	}
	public function clearSubject() {
		$this->subject = NULL;
		$this->modificationHeader('subject');
		return 0;
	}
	public function addCc($cc) {		//TODO: Add names
		if(is_array($cc)) {
			// Check if all are correct email address
			foreach($cc as $key=>$val) {
				if(!filter_var($val, FILTER_VALIDATE_EMAIL)) {
					throw new InvalidEmailException("invalid Email", 1, $val);
				}
			}
			//add all to $this->cc;
			foreach($cc as $key=>$val) {
				$this->cc[] = $val; // adding and incrementing count
				$this->modificationHeader('cc');
			}
		} else { // If $cc is single email
			if(!filter_var($cc, FILTER_VALIDATE_EMAIL)) {
				throw new InvalidEmailException("Invalid email", 1, $cc);
			}
			// Add it to $this->cc;
			$this->cc[] = $cc;
			$this->modificationHeader('cc');
		}
		return 0;
	}
	public function clearCc() {
		$this->cc = NULL;
		$this->modificationHeader('cc');
		return 0;
	}
	public function addBcc($bcc) {	//TODO: Add feature to add name
		if(is_array($bcc)) {
			// Check if all are correct email address
			foreach($bcc as $key=>$val) {
				if(!filter_var($val, FILTER_VALIDATE_EMAIL)) {
					throw new InvalidEmailException("invalid Email", 1, $val);
				} 
			}
			//add all to $this->bcc;
			foreach($bcc as $key=>$val) {
				$this->bcc[] = $val;
				$this->modificationHeader('bcc');
			}
		} else { // If $bcc is single email
			if(!filter_var($bcc, FILTER_VALIDATE_EMAIL)) {
				throw new InvalidEmailException("invalid Email", 1, $bcc);
			} 
			$this->bcc[] = $bcc;
			$this->modificationHeader('bcc');
		}
		return 0;
	}
	public function clearBcc() {
		$this->bcc = NULL;
		$this->modificationHeader('bcc');
		return 0;
	}
	public function addSenderName($name) {
		$name = trim($name);
		if(!(is_string($name) || !isset($name))) {
			throw new InvalidArgumentException("Name must be a string", 1);
		}
		if($name == "") {
			throw new InvalidArgumentException("Name must be a string", 1);
		}
		// To prevent header injection
		$name = filter_var($name, FILTER_SANITIZE_SPECIAL_CHARS);
		/*
		if(ctype_cntrl($name)) {
			throw new Exception("There should not be any control character in the name", 2);
		}
		*/
		$this->senderName = $name;
		$this->modificationHeader('senderName');
		return 0;
	}
	public function clearSenderName() {
		$this->senderName = NULL;
		$this->modificationHeader('senderName');
		return 0;
	}
	public function addReplyTo($reply) {
		// Validate $reply
		if(!filter_var($reply, FILTER_VALIDATE_EMAIL)) {
			throw new InvalidEmailException("First argument must be a valid email address", 1, $reply);
		}
		// add to $this->replyTo
		$this->replyTo = $reply;
		$this->modificationHeader('replyTo');
		return 0;
	}
	public function clearReplyTo() {
		$this->replyTo = NULL;
		$this->modificationHeader('replyTo');
		return 0;
	}
	public function setXmailer($xMailer = 1) {
		if($xMailer === 1 || $xMailer === 0 || $xMailer === NULL) {
			$this->xMailer = $xMailer;
			$this->modificationHeader('xMailer');
			return 0;
		} else {
				throw new InvalidArgumentException("Invalid xmailer, Expecting 0, 1 or Null", 1);
		}
	}
	public function setCustomXmailer($arg_xmailer) {
		$arg_xmailer = trim($arg_xmailer);
		if(!is_string($arg_xmailer)) {
			throw new InvalidArgumentException("Expecting String", 1);
		}
		if($arg_xmailer == "") {
			throw new InvalidArgumentException("Xmailer Cant be empty", 1);
		}
		$arg_xmailer = filter_var($arg_xmailer, FILTER_SANITIZE_SPECIAL_CHARS);
		$this->customXmailer = $arg_xmailer;
		return 0;
	}
	public function unsetXmailer() {
		$this->xMailer = NULL;
		$this->modificationHeader('xMailer');
		return 0;
	}
	/*
	public function addAlternateContent($name, $body, $mime = OOM_DEFAULTS::MIME, $charset = OOM_DEFAULTS::CHARSET) {
		if(!(is_int($name) || is_string($name))) {
			throw new Exception("Name must be integer or string", 1);
		}
		// Can only be used if emailTYPE is multipart/alternate or NULL
		if($this->emailTYPE === OOM_EMAIL_TYPE::MULTIPART_ALTERNATIVE || !isset($this->emailTYPE)) {
			$this->setEmailType(OOM_EMAIL_TYPE::MULTIPART_ALTERNATIVE);
			return $this->addPart($name, $content, $mime, NULL, NULL, $charset);
		} else {
			throw new Exception("addAlternate can only be used if emailTYPE is multipart/alternate or NULL", 1);
		}
	}
	public function addAlternateFile() {
		
	}
	*/
	public function setTotalFileSizeLimit($int_arg_sizeLimit) { 		// In bytes
		if(is_int($int_arg_sizeLimit)) {
			$this->limitTotalAttachmentSize = $int_arg_sizeLimit;
		} else {
			throw new Exception("Size must be an integer (in bytes)", 1);
		}
	}
	public function setPriority($priority = 1) {
		$exceptionCode = 0;
		if(!is_int($priority) || $priority > 5 || $priority < 1) {
			throw new InvalidArgumentException("Invalid Priority, It must be an integer and between 1 and 5", ++$exceptionCode);
		}
		$this->priority = $priority;
		$this->modificationHeader('priority');
		return 0;
	}
	public function unsetPriority() {
		$this->priority = NULL;
		$this->modificationHeader('priority');
		return 0;
	}
	public function setEmailType($type) {
		if(OOM_EMAIL_TYPE::validate($type)) {
			$this->emailTYPE = $type;
			$this->modificationHeader('emailTYPE');
			return 0;
		} else {
			throw new Exception("Invalid Type, use OOM_EMAIL_TYPE class's static members to set it" , 1);
		}
	}
	
	// Body/Message/Part related
	public function addPart($part_id, $content, $type=OOM_DEFAULTS::MIME, $size = NULL, $encoding = NULL, $charset = NULL) {
		if(!(is_int($part_id) || is_string($part_id))) {
			throw new Exception("Part id must be integer or string", 1);
		}
		if(!(is_int($size) || is_null($size))) {
			throw new Exception("Size must be an integer", 2);
		}
		if($charset === "AUTO") {
			$new_charset = mb_detect_encoding($str);
			if($new_charset !== FALSE) {
				$charset = $new_charset;
			}
		}
		if($charset === "STRICT") {
			// Complete: Check Strict functionality
			$new_charset = mb_detect_encoding($str, NULL, TRUE);
			if($new_charset !== FALSE) {
				$charset = $new_charset;
			}
		}
		
		$part = new OOM_EMAIL_PART($content, $type, $size, $encoding, $charset);
		$this->part[$part_id] = $part;
		$this->modificationPart();
		return 0;
	}
	public function removePart($part_id) {
		if(!(is_int($part_id) || is_string($part_id))) {
			throw new Exception("Part id must be integer or string", 1);
		}
		$size = 0;
		if(isset($this->part[$part_id])) {
			$size = $this->part[$part_id]->retSize();
			unset($this->part[$part_id]);
			$this->modificationPart();
		}
		return $size;
	}
	public function clearPart() {
		$this->part = NULL;
		$this->modificationPart();
		return 0;
	}
	public function addBody($body, $mime=OOM_DEFAULTS::MIME, $charset = OOM_DEFAULTS::CHARSET) {
		// TODO: Validate $mime, Charset against header injection
		return $this->addPart("OO_BODY", $body, $mime, NULL, NULL, $charset);
	}
	public function clearBody() {
		return $this->removePart("OO_BODY");
	}
	public function addFiles($file) { // Only strings or array with string elements allowed.
		$return = 0;  				// Stores number of skipped vals if it does not exists
		if(is_array($file)) {
			foreach($file as $key=>$val) {
				if(is_string($val)) {
					if(file_exists($val) === true) {
						$size = filesize($val);
						$newSize = $this->currentFileSize + $size;
						if($newSize > $this->limitTotalAttachmentSize) {
							throw new Exception("Maximum allowed size increased", 3);
						}
						$content = file_get_contents($val);
						$content = chunk_split(base64_encode($content));
						//$name = basename($val);
				
						$this->addPart($val, $content, mime_content_type($file), $size, "base64");
						
						$this->currentFileSize = $newSize;
					} else {
						$return ++; // Skipped cuz it does not exists
					}
				} else {
					$return++; // Skipped cuz its not string
				}
			}
		} else if(is_string($file)) {
			if(file_exists($file) === true) {
				$size = filesize($file);
				$newSize = $this->currentFileSize + $size;
				if($newSize > $this->limitTotalAttachmentSize) {
						throw new Exception("Maximum allowed size increased", 3);
				}
				
				$content = file_get_contents($file);
				$content = chunk_split(base64_encode($content));
				//$name = basename($val);

				$this->addPart($file, $content, mime_content_type($file), $size, "base64");

				$this->currentFileSize = $newSize;
			} else {
				throw new Exception("File does not exists", 2);
			}
		} else {
			throw new Exception("Invalid input", 1);
		}
		return $return; // Returns 0 on success else return number of files skipped i.e. $return
	}
	public function removeFile($file) {
		try {
			$size = $this->removePart($file);
			$this->currentFileSize -= $size;
		} catch (Exception $e) {
				throw $e;
		}
		return 0;
	}
	public function addRemoteFile($mixed_arg_path) {			// Curl must be present
		/*
		 * When string is sent 
		 * 1 = Size of the file cant be determined from header
		 * -1 = Maximum allowed size increased (Nothorw = 1)
		 * 2 = Error occured while fetching file header, Contact Administrator if you get this error, along with test cases
		 * -2 = Invalid link 
		 * 
		 * When array is sent
		 * returns number of skipped file
		 * TODO: Return array of Skipped file 
		 */
		$return = 0;
		if(is_string($mixed_arg_path)) {
			if(filter_var($mixed_arg_path, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
				$linkPath = $mixed_arg_path;
				$size = linkSize($linkPath);
				
				if($size < 0) {
					$return = 1;	// Size cant be determined
				} else  {
					if(is_array($size)) {
						$type=$size['type'];
						$size = $size['size'];
						
						$newSize = $this->currentFileSize + $size;
						if($newSize > $this->limitTotalAttachmentSize) {
							throw new Exception("Maximum allowed size increased", 1);
						}
						
						$content = file_get_contents($linkPath);
						$content = chunk_split(base64_encode($content));
						//$name = basename($val);
						
						$this->addPart($linkPath, $content, $type, $size, "base64");
						
						$this->currentFileSize = $newSize;
					}	else {
						$content = file_get_contents($linkPath);
						$size = strlen($content);
						$content = chunk_split(base64_encode($content));
						
						//$name = basename($val);
						
						$this->addPart($linkPath, $content, NULL, $size, "base64");
						
						$this->currentFileSize = $newSize;
						
						$return = 2;
					}
				} 
			} else {
				throw new Exception("Invalid link", 2);
			}
		} else if(is_array($mixed_arg_path)) {
			foreach ($mixed_arg_path as $key=>$val) {
				if(is_string($val)) {
					if(filter_var($val, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
						$size = linkSize($val);
						if($size < 0) {
							//return -5;		// Size cant be determined or website not reachable
							$return++;
						} else if(is_integer($size)) {
							$type=$size['type'];
							$size = $size['size'];
							
							$newSize = $this->currentFileSize + $size;
							if($newSize > $this->limitTotalAttachmentSize) {
								throw new Exception("Maximum allowed size increased", 1);
							}
							$content = file_get_contents($val);
							$content = chunk_split(base64_encode($content));
							//$name = basename($val);
							
							//Complete: get MIME of the remote file from header
							$this->addPart($val, $content, $type, $size, "base64");
							
							$this->currentFileSize = $newSize;
						} else {
							$return++;
						}
					} else {
						$return++;
					}
				} else {
					$return++;
				}
			}
		} else {
			throw new Exception("Unexpected value supplied as argument", 2);
		}
		return $return;
	}
	public function removeRemoteFile($url) {
		try {
			$size = $this->removePart($url);
			$this->currentFileSize -= $size;
		} catch (Exception $e) {
			throw $e;
		}
		return 0;
	}
	
	// Modification related
	private function modificationHeader($str) {
		if(!is_string($str)) {
			//TODO: Complete me
			throw new Exception("Bug found, Please contact ooMail developers at oomail-support@lists.sourcegorge.com", -1);
		}
		if(isset($this->modification_current_header[$str])) {
			$this->modification_current_header[$str]++;
		} else {
			$this->modification_current_header[$str] = 1;
		}
	}
	private function modificationPart() {
		if(isset($this->$modification_current_part)) {
			$this->modification_current_part++;
		} else {
			$this->modification_current_part = 1;
			//echo "here";
		}
	}
	protected function setModitication() {
		$this->modification_last_header = $this->modification_current_header;
		$this->modification_last_part = $this->modification_current_part;
		return 0;
	}
	protected function ifHeaderModified() {
		if($this->modification_current_header === $this->modification_last_header) {
			return FALSE;
		} else {
			return TRUE;
		}
	}
	protected function ifPartModified() {
		if($this->modification_current_part === $this->modification_last_part) {
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
	// Prepare everything
	// TODO: Make protected
	public function prepare() {
		$uid =  md5(uniqid(time()));
		$this->prepareTo();
		$this->prepareHeader($uid);
		$this->prepareBody($uid);
	}
	protected function prepareTo() {
		// Prepare $to
		if(is_array($this->to)) {
			$to = "";
			$count = 0;
			foreach($this->to as $key=>$val) {
				if($count == 0) {
					$to = $val;
					$count++;
				} else {
					$to .= ", $val";
				}
			}
			$this->prepared_to = $to;
		} else {
			throw new Exception("TO Field can't be empty", 1);
		}
		return 0;
	}
	protected function prepareHeader($uid) {
		// If there is no change in variables return 0 directly withour rebuilding headers
		if(!$this->ifHeaderModified()) {
			return 0;
		}
		// Prepare header
		$header = "";
		if(!isset($uid)) {
			throw new Exception("UID not set", 1);
		}
		
		// Add basic header
		$header .= "MIME-Version: 1.0" . PHP_EOL;
		/*
		 * If typeEMAIL not set 
		 * 	Check if OO_BODY part is present
		 * 		Yes:	Check if any other part is present
		 * 			Yes:	multipart/mixed
		 * 			No: Same as body
		 * 		No:	Check if other part is present
		 * 			Yes:	Check number of parts
		 * 				1:	Same as part
		 * 				More: Multipart/mixed
		 * 			No:	Throw error
		 */
		$emailType = "";
			if(!isset($this->emailTYPE)) {
				$count_parts = count($this->part);
				if(isset($this->part["OO_BODY"])) {
					if($count_parts > 1) {
						$emailType = "multipart/mixed";
					} else {
						$emailType = $this->part["OO_BODY"]->retMime();
					}
				} else {
					if($count_parts > 0) {
						if($count_parts === 1) {
							foreach ($this->part as $key=>$val) {
								$emailType = $val->retMime();
							}
						} else {
							$emailType = "multipart/mixed";
						}
					} else {
						$emailType = "text/plain";
					}
				}
			} else {
				$emailType = OOM_EMAIL_TYPE::getStringEquivalent($this->emailTYPE);
			}
			
			if($count_parts === 1 || $count_parts == NULL) {
				//Complete: Character Set ?? How to get it from user?
				//$header .= "Content-Type: text/html; charset=iso-8859-1;" . PHP_EOL;
				if($count_parts === 1) {
					foreach ($this->part as $key=>$val) {
						$charset = $val->retCharset();
						if($charset) {
							$header .= "Content-Type: {$emailType}; charset={$charset}" . PHP_EOL;
						} else {
							$header .= "Content-Type: {$emailType};" . PHP_EOL;
						}
					}
				} else {
					$header .= "Content-Type: {$emailType};" . PHP_EOL;
				}
			} else if($count_parts > 1) {
				$header .= "Content-Type: {$emailType}; boundary=\"".$uid."\"" . PHP_EOL;
			}
			
			// Check if From is present
			if($this->from) {
				if($this->senderName) {
					$header .= "From: ". $this->senderName ." <" . $this->from . ">" . PHP_EOL;
				} else {
					$header .= "From: \"". $this->from . "\"" . PHP_EOL;
				}
			}
		
		// Check if $cc is present;
		if($this->cc) {
			$cc = $this->prepareCC();
			$header .= "Cc: " . $cc . "" . PHP_EOL;
		}
		// Check if $cc is present;
		if($this->bcc) {
			$bcc = $this->prepareBCC();
			$header .= "Bcc: " . $bcc . "" . PHP_EOL;
		}
		// Check if reply to is present
		if($this->replyTo != NULL) {
			$header .= "Reply-To: " . $this->replyTo . "" . PHP_EOL;
		}
		// Check if xmailer is present
		if(!is_string($this->customXmailer)) {
			if($this->xMailer === 1) {
				$header .= "X-Mailer: PHP/" . phpversion() . "" . PHP_EOL;
			}
		} else {
			$header .= "X-Mailer: {$this->customXmailer}" . PHP_EOL;
		}
		// Return-Path: (Delivery failed notification)
		if($this->returnPath!==NULL) {
			$header .= "Return-Path: " . $this->returnPath . PHP_EOL;
		}
		// Return-Receipt-To: (Read notification)
		if($this->returnReceiptTo !== NULL) {
			$header .= "Return-Receipt-To: " . $this->returnReceiptTo . PHP_EOL;
		}
		
		if(is_int($this->priority)) {
			//$header .= "Priority";
			if($this->priority === 1) {
				$header .= "X-Priority: {$this->priority}" . PHP_EOL; 
				$header .= "X-MSMail-Priority: High" . PHP_EOL; 
				$header .= "Importance: High" . PHP_EOL;
			} else if($this->priority === 2 || $this->priority === 3) {
				$header .= "X-Priority: {$this->priority}" . PHP_EOL; 
				$header .= "X-MSMail-Priority: Normal" . PHP_EOL; 
				$header .= "Importance: Normal" . PHP_EOL;
			} else if($this->priority === 4 || $this->priority === 5) {
				$header .= "X-Priority: {$this->priority}" . PHP_EOL; 
				$header .= "X-MSMail-Priority: Low" . PHP_EOL; 
				$header .= "Importance: Low" . PHP_EOL;
			}
		}
		
		$this->prepared_header = $header;
		return 0;
	}
	protected function prepareBody($uid) {
		// If there is no change in variables return 0 directly withour rebuilding headers
		if(!$this->ifPartModified()) {
			return 0;
		}
		$boundary = $uid;
		// ADDING BODY
		// Parse each part 
		/*
		 * If number of parts is 
		 * 	more than 1:	
		 * 		Use boundry
		 * 	equal to 1:
		 * 		Dont use boundry
		 * 	Less than 1 = 0 or NULL:
		 * 		Dont do anything
		 */
		
		$body = "";
		if(is_array($this->part)) {
			$count_parts = count($this->part);
		} else {
			$count_parts = 0;
		}
		
		if($count_parts > 1) {
			$body .=  "--" . $boundary . PHP_EOL;
			foreach ($this->part as $key=>$val) {
				$mime = $val->retMime();
				$name = basename($key);
				$content = $val->retContent();
				$encoding = $val->retEncoding();
				$charset = $val->retCharset();
				if($charset) {
					$body .= "Content-type: {$mime}; charset={$charset};" . PHP_EOL;
				} else {
					$body .= "Content-type: {$mime}; name=\"{$name}\"" . PHP_EOL;
				}
				if($encoding) {
					$body .= "Content-Transfer-Encoding: {$encoding}" . PHP_EOL;
				}
				$body .=  PHP_EOL;
				
				$body .= $content . PHP_EOL;
			}
			$body .= PHP_EOL;
			$body .=  "--" . $boundary . "--" . PHP_EOL;
		} else if($count_parts == 1) {
			foreach ($this->part as $key=>$val) {
				$mime = $val->retMime();
				$name = basename($key);
				$content = $val->retContent();
				$encoding = $val->retEncoding();
				$charset = $val->retCharset();
				
				if($charset) {
					$body .= "Content-type: {$mime}; charset={$charset};" . PHP_EOL;
				} else {
					$body .= "Content-type: {$mime}; name=\"{$name}\"" . PHP_EOL;
				}
				if($encoding) {
					$body .= "Content-Transfer-Encoding: {$encoding}" . PHP_EOL;
				}
				$body .=  PHP_EOL;
				
				$body .= $content . PHP_EOL;
			}
			$body .= PHP_EOL;
		}
		$this->prepared_body = $body;
		return 0;
	}
	protected function prepareCC() {
		// Prepare $cc
		// Check if $cc is present
		$cc = NULL;
		if(is_array($this->cc)) {
			$cc = "";
			$count = 0;
			foreach($this->cc as $key=>$val) {
				if($count == 0) {
					$cc = $val;
					$count++;
				} else {
					$cc .= ", $val";
				}
			}
		}
		return $cc;
	}
	protected function prepareBCC() {
		// Prepare $bcc
		// Check if $bcc is present
		$bcc = NULL;
		if(is_array($this->bcc)) {
			$bcc = "";
			$count = 0;
			foreach($this->bcc as $key=>$val) {
				if($count == 0) {
					$bcc = $val;
					$count++;
				} else {
					$bcc .= ", $val";
				}
			}
		}
		return $bcc;
	}
	// Return Prepared data
	// TODO: Make protected
	public function retPreparedBody() {
		if($this->prepared_body) {
			return $this->prepared_body;
		}else {
			throw new Exception("Message body is not prepared", 1);
		}
	}
	public function retPreparedHeader() {
		if($this->prepared_header) {
			return $this->prepared_header;
		} else {
			throw new Exception("Message header is not prepared", 1);
		}
	}
	public function retPreparedTo() {
		if($this->prepared_to) {
			return $this->prepared_to;
		} else {
			throw new Exception("TO is not prepared", 1);
		}
	}

}



class OOM_HEADER {
// Completed: Stop header injection at sender name and body
// Complete: Add advance attachment control (Remote attachment, Remote file size)			
// Complete: Modularize Send function using Private functions
// Complete: Size of the file
// Complete: Limit total attachment size.
// Complete: Mail composition
// Complete: Convert /r/n to PHP_EOL
/*
 * Complete: Convert to addPart Where everything will be added as parts Except headers
 * headers will be modified accordingly according to parts
 * Complete: E-mail type function can be text/plain. text/html, multipart/alternative, multipart/mixed
 */
	// TODO: Check all the MIME suppored in email along with multipart and text
	/*
	 * Complete: Multiple sends: Stamp header and parts, recreate header and body before sending
	 * Complete: Add Return-Path: (Delivery failed notification) and Return-Receipt-To: (Read notification)
	 * Complete: (IMP) Decide how to handle error and whether or not to use $nothrow variable
	 * 				Consider option for two classes one that throws error and other which returns -ve value 
	 * TODO: Intelligent sending, Only reproduce those parts of headers which are modified. This can be achieved by makind prepared_variable name for each variable
	 * 
	 */
	
	/*
	 * MEMBERS
	 */
	private $to;
	private $subject;
	
	// For header
	private $from;
	private $cc;
	private $bcc;
	private $senderName;
	private $replyTo;
	private $xMailer;
	private $priority;
	private $emailTYPE; 
	private $returnPath; // Delivery failed notification
	private $returnReceiptTo;	//	Read notification
	private $customXmailer;

	//Utility
	private $limitTotalAttachmentSize = 512000000; // Default max size = 500 MB
	private $currentFileSize;
	
	// Prepared data
	private $prepared_header;
	private $prepared_to;

	// number of modification Modification 
	private $modification_current_header;
	
	private $modification_last_header;
	
	/*
	 * METHODS
	 */
	//TODO: Whats the use of Exception Code?
	// TO variable
	public function addTo($to) {			//TODO: Add support for Names
		if(is_array($to)) {
			// Check if all are correct email address
			if(!filter_var_array($to, FILTER_VALIDATE_EMAIL)) {
				throw new Exception("Invalid Email", 1);
			}
			//add all to $this->to;
			foreach($to as $key=>$val) {
				$this->to[] = $val; 
			}
		} else { // If $to is single email
			if(!filter_var($to, FILTER_VALIDATE_EMAIL)) {
				throw new Exception("Invalid Email", 2);
			};
			$this->to[] = $to;
		}
		return 0;
	}
	public function clearTo() {
		$this->to = NULL;
		return 0;
	}
	
	// header variables
	public function setDeliveryFailedNotificationAddress($arg_email = NULL) {
		if(isset($arg_email)) {
			if(!filter_var($arg_email, FILTER_VALIDATE_EMAIL)) {
				throw new InvalidEmailException("Invalid email address", 1);
			}
			$this->returnPath = $arg_email;	
			$this->modificationHeader('returnPath');
		} else {
			if(isset($this->replyTo)) {
				$this->returnPath = $this->replyTo;
			} else if(isset($this->from)) {
				$this->returnPath = $this->from;
			} else {
				throw new LogicException("'Reply-to' and 'From' address are not set", 2);
			}
		}
		return 0;
	}
	public function clearDeliveryFailedNotificationAddress() {
		$this->returnPath = NULL;
		$this->modificationHeader('returnPath');
		return 0;
	}
	public function setReadNotificationAddress($arg_email = NULL) {
		if(isset($arg_email)) {
			if(!filter_var($arg_email, FILTER_VALIDATE_EMAIL)) {
				throw new InvalidEmailException("Invalid email address", 1);
			}
			$this->returnReceiptTo = $arg_email;
			$this->modificationHeader('returnReceiptTo');
		} else {
			if(isset($this->replyTo)) {
				$this->returnReceiptTo = $this->replyTo;
			} else if(isset($this->from)) {
				$this->returnReceiptTo = $this->from;
			} else {
				throw new LogicException("'Reply-to' and 'From' address are not set", 2);
			}
		}
		return 0;	
	}
	public function clearReadNotificationAddress() {
		$this->returnReceiptTo = NULL;
		$this->modificationHeader('returnReceiptTo');
		return 0;
	}
	public function addFrom($from) {
		// Validate $from
		if(!filter_var($from, FILTER_VALIDATE_EMAIL)) {
			throw new InvalidEmailException("Address not valid", 1, $from);
		}
		$this->from = $from;
		$this->modificationHeader('part');
		return 0;
	}
	public function clearFrom() {
		$this->from = NULL;
		$this->modificationHeader('from');
		return 0;
	}
	public function addSubject($sub) {
		if (!is_string($sub)) { 
			throw new InvalidArgumentException("Subject must be a string", 1);
		}
		$this->subject = $sub;
		$this->modificationHeader('subject');
		return 0;
	}
	public function clearSubject() {
		$this->subject = NULL;
		$this->modificationHeader('subject');
		return 0;
	}
	public function addCc($cc) {		//TODO: Add names
		if(is_array($cc)) {
			// Check if all are correct email address
			foreach($cc as $key=>$val) {
				if(!filter_var($val, FILTER_VALIDATE_EMAIL)) {
					throw new InvalidEmailException("invalid Email", 1, $val);
				}
			}
			//add all to $this->cc;
			foreach($cc as $key=>$val) {
				$this->cc[] = $val; // adding and incrementing count
				$this->modificationHeader('cc');
			}
		} else { // If $cc is single email
			if(!filter_var($cc, FILTER_VALIDATE_EMAIL)) {
				throw new InvalidEmailException("Invalid email", 1, $cc);
			}
			// Add it to $this->cc;
			$this->cc[] = $cc;
			$this->modificationHeader('cc');
		}
		return 0;
	}
	public function clearCc() {
		$this->cc = NULL;
		$this->modificationHeader('cc');
		return 0;
	}
	public function addBcc($bcc) {	//TODO: Add feature to add name
		if(is_array($bcc)) {
			// Check if all are correct email address
			foreach($bcc as $key=>$val) {
				if(!filter_var($val, FILTER_VALIDATE_EMAIL)) {
					throw new InvalidEmailException("invalid Email", 1, $val);
				} 
			}
			//add all to $this->bcc;
			foreach($bcc as $key=>$val) {
				$this->bcc[] = $val;
				$this->modificationHeader('bcc');
			}
		} else { // If $bcc is single email
			if(!filter_var($bcc, FILTER_VALIDATE_EMAIL)) {
				throw new InvalidEmailException("invalid Email", 1, $bcc);
			} 
			$this->bcc[] = $bcc;
			$this->modificationHeader('bcc');
		}
		return 0;
	}
	public function clearBcc() {
		$this->bcc = NULL;
		$this->modificationHeader('bcc');
		return 0;
	}
	public function addSenderName($name) {
		$name = trim($name);
		if(!(is_string($name) || !isset($name))) {
			throw new InvalidArgumentException("Name must be a string", 1);
		}
		if($name == "") {
			throw new InvalidArgumentException("Name must be a string", 1);
		}
		// To prevent header injection
		$name = filter_var($name, FILTER_SANITIZE_SPECIAL_CHARS);
		/*
		if(ctype_cntrl($name)) {
			throw new Exception("There should not be any control character in the name", 2);
		}
		*/
		$this->senderName = $name;
		$this->modificationHeader('senderName');
		return 0;
	}
	public function clearSenderName() {
		$this->senderName = NULL;
		$this->modificationHeader('senderName');
		return 0;
	}
	public function addReplyTo($reply) {
		// Validate $reply
		if(!filter_var($reply, FILTER_VALIDATE_EMAIL)) {
			throw new InvalidEmailException("First argument must be a valid email address", 1, $reply);
		}
		// add to $this->replyTo
		$this->replyTo = $reply;
		$this->modificationHeader('replyTo');
		return 0;
	}
	public function clearReplyTo() {
		$this->replyTo = NULL;
		$this->modificationHeader('replyTo');
		return 0;
	}
	public function setXmailer($xMailer = 1) {
		if($xMailer === 1 || $xMailer === 0 || $xMailer === NULL) {
			$this->xMailer = $xMailer;
			$this->modificationHeader('xMailer');
			return 0;
		} else {
				throw new InvalidArgumentException("Invalid xmailer, Expecting 0, 1 or Null", 1);
		}
	}
	public function setCustomXmailer($arg_xmailer) {
		$arg_xmailer = trim($arg_xmailer);
		if(!is_string($arg_xmailer)) {
			throw new InvalidArgumentException("Expecting String", 1);
		}
		if($arg_xmailer == "") {
			throw new InvalidArgumentException("Xmailer Cant be empty", 1);
		}
		$arg_xmailer = filter_var($arg_xmailer, FILTER_SANITIZE_SPECIAL_CHARS);
		$this->customXmailer = $arg_xmailer;
		return 0;
	}
	public function unsetXmailer() {
		$this->xMailer = NULL;
		$this->modificationHeader('xMailer');
		return 0;
	}
	/*
	public function addAlternateContent($name, $body, $mime = OOM_DEFAULTS::MIME, $charset = OOM_DEFAULTS::CHARSET) {
		if(!(is_int($name) || is_string($name))) {
			throw new Exception("Name must be integer or string", 1);
		}
		// Can only be used if emailTYPE is multipart/alternate or NULL
		if($this->emailTYPE === OOM_EMAIL_TYPE::MULTIPART_ALTERNATIVE || !isset($this->emailTYPE)) {
			$this->setEmailType(OOM_EMAIL_TYPE::MULTIPART_ALTERNATIVE);
			return $this->addPart($name, $content, $mime, NULL, NULL, $charset);
		} else {
			throw new Exception("addAlternate can only be used if emailTYPE is multipart/alternate or NULL", 1);
		}
	}
	public function addAlternateFile() {
		
	}
	*/
	public function setTotalFileSizeLimit($int_arg_sizeLimit) { 		// In bytes
		if(is_int($int_arg_sizeLimit)) {
			$this->limitTotalAttachmentSize = $int_arg_sizeLimit;
		} else {
			throw new Exception("Size must be an integer (in bytes)", 1);
		}
	}
	public function setPriority($priority = 1) {
		$exceptionCode = 0;
		if(!is_int($priority) || $priority > 5 || $priority < 1) {
			throw new InvalidArgumentException("Invalid Priority, It must be an integer and between 1 and 5", ++$exceptionCode);
		}
		$this->priority = $priority;
		$this->modificationHeader('priority');
		return 0;
	}
	public function unsetPriority() {
		$this->priority = NULL;
		$this->modificationHeader('priority');
		return 0;
	}
	public function setEmailType($type) {
		if(OOM_EMAIL_TYPE::validate($type)) {
			$this->emailTYPE = $type;
			$this->modificationHeader('emailTYPE');
			return 0;
		} else {
			throw new Exception("Invalid Type, use OOM_EMAIL_TYPE class's static members to set it" , 1);
		}
	}

	// Modification related
	private function modificationHeader($str) {
		if(!is_string($str)) {
			//TODO: Complete me
			throw new Exception("Bug found, Please contact ooMail developers at oomail-support@lists.sourcegorge.com", -1);
		}
		if(isset($this->modification_current_header[$str])) {
			$this->modification_current_header[$str]++;
		} else {
			$this->modification_current_header[$str] = 1;
		}
	}
	protected function setModitication() {
		$this->modification_last_header = $this->modification_current_header;
		return 0;
	}
	protected function ifHeaderModified() {
		if($this->modification_current_header === $this->modification_last_header) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	// Prepare everything
	public function prepare() {
		$uid =  md5(uniqid(time()));
		$this->prepareTo();
		$this->prepareHeader($uid);
		$this->prepareBody($uid);
	}
	protected function prepareTo() {
		// Prepare $to
		if(is_array($this->to)) {
			$to = "";
			$count = 0;
			foreach($this->to as $key=>$val) {
				if($count == 0) {
					$to = $val;
					$count++;
				} else {
					$to .= ", $val";
				}
			}
			$this->prepared_to = $to;
		} else {
			throw new Exception("TO Field can't be empty", 1);
		}
		return 0;
	}
	protected function prepareHeader($uid) {
		// If there is no change in variables return 0 directly withour rebuilding headers
		if(!$this->ifHeaderModified()) {
			return 0;
		}
		// Prepare header
		$header = "";
		if(!isset($uid)) {
			throw new Exception("UID not set", 1);
		}
		
		// Add basic header
		$header .= "MIME-Version: 1.0" . PHP_EOL;
		/*
		 * If typeEMAIL not set 
		 * 	Check if OO_BODY part is present
		 * 		Yes:	Check if any other part is present
		 * 			Yes:	multipart/mixed
		 * 			No: Same as body
		 * 		No:	Check if other part is present
		 * 			Yes:	Check number of parts
		 * 				1:	Same as part
		 * 				More: Multipart/mixed
		 * 			No:	Throw error
		 */
			
			
			// Check if From is present
			if($this->from) {
				if($this->senderName) {
					$header .= "From: ". $this->senderName ." <" . $this->from . ">" . PHP_EOL;
				} else {
					$header .= "From: \"". $this->from . "\"" . PHP_EOL;
				}
			}
		
		// Check if $cc is present;
		if($this->cc) {
			$cc = $this->prepareCC();
			$header .= "Cc: " . $cc . "" . PHP_EOL;
		}
		// Check if $cc is present;
		if($this->bcc) {
			$bcc = $this->prepareBCC();
			$header .= "Bcc: " . $bcc . "" . PHP_EOL;
		}
		// Check if reply to is present
		if($this->replyTo != NULL) {
			$header .= "Reply-To: " . $this->replyTo . "" . PHP_EOL;
		}
		// Check if xmailer is present
		if(!is_string($this->customXmailer)) {
			if($this->xMailer === 1) {
				$header .= "X-Mailer: PHP/" . phpversion() . "" . PHP_EOL;
			}
		} else {
			$header .= "X-Mailer: {$this->customXmailer}" . PHP_EOL;
		}
		// Return-Path: (Delivery failed notification)
		if($this->returnPath!==NULL) {
			$header .= "Return-Path: " . $this->returnPath . PHP_EOL;
		}
		// Return-Receipt-To: (Read notification)
		if($this->returnReceiptTo !== NULL) {
			$header .= "Return-Receipt-To: " . $this->returnReceiptTo . PHP_EOL;
		}
		
		if(is_int($this->priority)) {
			//$header .= "Priority";
			if($this->priority === 1) {
				$header .= "X-Priority: {$this->priority}" . PHP_EOL; 
				$header .= "X-MSMail-Priority: High" . PHP_EOL; 
				$header .= "Importance: High" . PHP_EOL;
			} else if($this->priority === 2 || $this->priority === 3) {
				$header .= "X-Priority: {$this->priority}" . PHP_EOL; 
				$header .= "X-MSMail-Priority: Normal" . PHP_EOL; 
				$header .= "Importance: Normal" . PHP_EOL;
			} else if($this->priority === 4 || $this->priority === 5) {
				$header .= "X-Priority: {$this->priority}" . PHP_EOL; 
				$header .= "X-MSMail-Priority: Low" . PHP_EOL; 
				$header .= "Importance: Low" . PHP_EOL;
			}
		}
		
		$this->prepared_header = $header;
		return 0;
	}
	protected function prepareCC() {
		// Prepare $cc
		// Check if $cc is present
		$cc = NULL;
		if(is_array($this->cc)) {
			$cc = "";
			$count = 0;
			foreach($this->cc as $key=>$val) {
				if($count == 0) {
					$cc = $val;
					$count++;
				} else {
					$cc .= ", $val";
				}
			}
		}
		return $cc;
	}
	protected function prepareBCC() {
		// Prepare $bcc
		// Check if $bcc is present
		$bcc = NULL;
		if(is_array($this->bcc)) {
			$bcc = "";
			$count = 0;
			foreach($this->bcc as $key=>$val) {
				if($count == 0) {
					$bcc = $val;
					$count++;
				} else {
					$bcc .= ", $val";
				}
			}
		}
		return $bcc;
	}
	// Return Prepared data
	// TODO: Make protected
	public function retPreparedHeader() {
		if($this->prepared_header) {
			return $this->prepared_header;
		} else {
			throw new Exception("Message header is not prepared", 1);
		}
	}
	public function retPreparedTo() {
		if($this->prepared_to) {
			return $this->prepared_to;
		} else {
			throw new Exception("TO is not prepared", 1);
		}
	}
}

//TODO: Check if HEADER can manage all the header variables
// TODO: Check number of encoding allowed
class OOM_CONST_ENCODING {
	const BASE64 = "base64";
	public function getStringEquivalent() {
		
	}
}
class OOM_SINGLE {
	//TODO: How to restrict initialization of this class such that only OO_BASE class can initiate it 
	private $content;
	private $mime;
	private $size;
	private $encoding;
	private $charset;
	
	public function __construct($content, $type, $size = NULL, $encoding = NULL, $charset = NULL) {
		$this->part_content = $content;
		//TODO: Validate MIME_TYPE, for not including Header Injection
		$this->mime_type = $type;
		if($size === NULL) {
		// Complete: Find accurate function to calculate and store size
			$this->size = strlen($content);
		} else {
			$this->size = $size;
		}
		$this->encoding = $encoding;
		$this->charset = $charset;
	}
	public function addContent($content, $mime = "text/plain", $encoding = NULL, $charset = NULL) {
		// Content is sent directly
		if(is_null($content)) {
			throw new InvalidArgumentException("Content must not be empty", 1);
		} else 	if(is_object($content) && array_search("__toString", get_class_methods(test)) === FALSE) {
			throw new InvalidArgumentException("Object sent as content does not has a string equivalent (public __toString function)", 2);
		} else if(!is_string($content)) {
			throw new InvalidArgumentException("Content is not string", 3);
		}
		$this->content = $content;
		
		//TODO: Validate MIME using Regular explression 
		$this->mime = $mime;
		
		$this->size = strlen($content);
		
		
		
		return 0;
	}
	public function addFile($path, $charset = NULL) {
		// A path to file is specified
		// Determine MIME
		// Determine size
		// Determine encode it
		// Determine charset if NULL
	}
	public function addRemoteFile($url, $mime) {
		// A url to file is specified
		// Determine MIME if NULL
		// Determine size from header, set size only using strlen()
		// Determine Encoding from header if possible, else try determining from downloaded content
		// Determine Charset from header if possible, else try determining from downloaded content
	}
	public function addAttachment() {
		// Works with both (url and path)
	}
	private function overrideSize() {
		//TODO: How to use friend function in PHP to Give Override rights to OO_ABSTRACT_MAIL class
	}
	public function retSize() {
		return $this->size;
	}
	public function retMime() {
		return $this->mime_type;
	}
	public function retContent() {
		return $this->part_content;
	}
	public function retEncoding() {
		return $this->encoding;
	}
	public function retCharset() {
		return $this->charset;
	}
}


class OOM_MULTIPLE {
	
}

class OOM_COMPLEX {
	
}

class test {
	public function what() {
		echo "Hi";
	}
	public function __toString() {
		echo "Hello";
	}
} 

//echo error_reporting(E_STRICT);

$x = new test();
//var_dump(function_exists(test::__toString));
//var_dump(get_class_methods(test));
//var_dump(array_search("x__toString", get_class_methods(test)));
/*
if(is_string($x)) {
	echo "<br />yes its string <br />";
} else {
	echo "<br /> No its not<br />";
}*/

//echo "Hi";
//phpinfo();

?>