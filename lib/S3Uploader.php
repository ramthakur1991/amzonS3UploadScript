<?php
if (!class_exists('S3'))require_once('S3.php');

class S3Uploader {
	private $filePath ;
	private $accessKey;
	private $accessSecret;
	private $bucketName;

	function __construct($accessKey, $accessSecret, $bucketName){
		$this->accessKey = $accessKey;
		$this->accessSecret = $accessSecret;
		$this->bucketName = $bucketName;
	}

	public function Upload($outPutImageName, $subFolder, $uri){
		//instantiate the class
		$s3 = new S3($this->accessKey, $this->accessSecret);
		$s3->putBucket($bucket, S3::ACL_PUBLIC_READ);
		$uri = $uri . '/'. $subFolder . '/' .$outPutImageName;
		return $s3->putObjectFile($outPutImageName, $this->bucketName, $uri);
	}
}
?>
