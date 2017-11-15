<?php
error_reporting(E_ALL & ~E_NOTICE);
ini_set('memory_limit', '512M');
require_once('lib/ImageCropper.php');
require_once('lib/S3Uploader.php');

//AWS S3 configurations;
if (!defined('AWS_ACCESS_KEY')) define('AWS_ACCESS_KEY', 'Amazon_access_key');
if (!defined('AWS_SECRET_KEY')) define('AWS_SECRET_KEY', 'amazon_secret_key');
if (!defined('AWS_BUCKET_NAME')) define('AWS_BUCKET_NAME', 'amazon_s3_buket_name');
if (!defined('AWS_BUCKET_KEY')) define('AWS_BUCKET_KEY', 'amazon_bucket_sub_folder_path');


$soruceimgUrl = "funny_cat.jpg"; //local Image url or any of the url from web or any where else;
$imgInitW = 1280;
$imgInitH = 720;
$imgW = 300;
$imgH = 300;
$imgY1 = 20;
$imgX1 = 20;
$cropH = 204;
$cropW = 274;
$angle = 0;
$cropedNewimagepath = "/var/www/apache/s3FileUpload";

//going to Crop image and create it locally.
$cropper = new Image($soruceimgUrl, $imgInitW, $imgInitH, $imgW, $imgH, $imgY1, $imgX1, $cropH, $cropW, $angle, $cropedNewimagepath);
$outPutImageName = $cropper->Cropper();

//going to upload cropped image to Amazon s3 server.
$S3Instance = new S3Uploader(AWS_ACCESS_KEY, AWS_SECRET_KEY, AWS_BUCKET_NAME);
$subFolderName = $cropH.'X'.$cropW;
$isUpload 	= $S3Instance->Upload($outPutImageName, $subFolderName, AWS_BUCKET_KEY);
if($isUpload) {
	die("File uploaded on Amazon S3 successfully!!!! \n");
}else {
	die("Unable to upload on Amazon S3 due to some issue!!!! \n");
}



