<?php
include "libky.class.php";

class ImageDownloaderBot {
	const API_BASEPATH = "https://api.telegram.org/bot";
	const API_FILEPATH = "https://api.telegram.org/file/bot";
	
	private $token;
	
	public function __construct($token) {
		$this->token = $token; // set bot token on construct
	}
	
	public function download($fileID, $targetFolder) {
		$request = array(); // create new empty request
		$request['file_id'] = $fileID;
		
		$output = $this->makeRequest("/getFile", $request);
		$extension = pathinfo($output->result->file_path)['extension']; // extract the file extension from the path
		$filename = $fileID . "." . $extension; // build the target filename
		
		$handle = fopen(self::API_FILEPATH . $this->token . "/" . $output->result->file_path, 'r'); // open connection to URL
		file_put_contents($targetFolder . $filename, $handle); // write contents to disk
		fclose($handle); // close the handle
	}
	
	private function makeRequest($method, $request) {
		$ch = curl_init(self::API_BASEPATH . $this->token . $method); // initialize connection

		curl_setopt($ch, CURLOPT_POST, 1); // switch method to POST
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($request)); // convert query array to a HTTP-encoded string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);
		curl_close($ch);

		$retobj = json_decode($response);
		return $retobj;
	}
}

$cfg = file_get_contents("config.json");
$cfg = json_decode($cfg);
if(!is_object($cfg)) throw new Exception("Could not read config.json. Does the file exist and does it contain valid JSON markup?");

$webhookIn = file_get_contents("php://input"); // get JSON input from Telegram
$sJ = json_decode($webhookIn); // decode it

// If no photo is contained, ignore
if(empty($sJ->message->photo)) die(); 

$origin = $sJ->message->chat->id;
$p = end($sJ->message->photo)->file_id;

$bot = new ImageDownloaderBot($cfg->apikey);
if(!is_dir("downloaded/" . $origin)) { // if subfolder for origin chat does not exist
	mkdir("downloaded/" . $origin) or die("Failed to create folder."); // create it
}
$bot->download($p, "downloaded/" . $origin . "/"); // download the file to the folder