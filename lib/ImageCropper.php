<?php
class Image {
	private $imgUrl ;
	private $imgInitW;
	private $imgInitH;
	private $imgW;
	private $imgH;
	private $imgY1;
	private $imgX1;
	private $cropH;
	private $cropW;
	private $angle;
	private $newimagepath;

	function __construct($imgurl = null, $imgInitW = null, $imgInitH = null, $imgW = null, $imgH = null, $imgY1 = null, $imgX1 = null, $cropH = null, $cropW = null, $angle = null, $newimagepath = null){
		$this->imgUrl = $imgurl;
		$this->imgInitW = $imgInitW;
		$this->imgInitH = $imgInitH;
		$this->imgW = $imgW;
		$this->imgH = $imgH;
		$this->imgY1 = $imgY1;
		$this->imgX1 = $imgX1;
		$this->cropH = $cropH;
		$this->cropW = $cropW;
		$this->angle = $angle;
		$this->newimagepath = $newimagepath;
	}

	protected function ValidateExtension($imgUrl){
		$what = getimagesize($imgUrl);

		switch(strtolower($what['mime']))
		{
			case 'image/png':
				$img_r = imagecreatefrompng($imgUrl);
				$source_image = imagecreatefrompng($imgUrl);
				$type = '.png';
				return array("img_r" => $img_r, "source_image"=> $source_image, "type"=>$type);
			case 'image/jpeg':
				$img_r = imagecreatefromjpeg($imgUrl);
				$source_image = imagecreatefromjpeg($imgUrl);
				$type = '.jpeg';
				return array("img_r" => $img_r, "source_image"=> $source_image, "type"=>$type);
			case 'image/gif':
				$img_r = imagecreatefromgif($imgUrl);
				$source_image = imagecreatefromgif($imgUrl);
				$type = '.gif';
				return array("img_r" => $img_r, "source_image"=> $source_image, "type"=>$type);
			default:
				throw new Exception('Exception raised while checking extension.');
				die;
				break;
		}
	}

	protected function isFolderWritable($path){
		try{
			if (!file_exists($path)) {
				echo $path;
				mkdir($path, 0777, true);
				return true ;
			}
		}catch(Exception $e){
			throw new Exception('Exception raised while checking isFolderWritable', $e->errorMessage());
			die;
		}
	}

	protected function createImage($final_image, $type){
		$outputFileName = "imageCropper". time() . $type ;
		if($type == ".png"){
			imagepng($final_image, $outputFileName, 100);
			return $outputFileName;
		}else if($type == ".jpeg"){
			imagejpeg($final_image, $outputFileName, 100);
			return $outputFileName;
		}else if($type == ".gif"){
			throw new Exception('Exception raised while creating .gif image');
			//die;
		}else{
			throw new Exception('Exception raised while creating image of undefined image type', $e->errorMessage());
			//die;
		}
	}

	public function Cropper(){
		try{
			$imgExtension = $this->ValidateExtension($this->imgUrl);

			//check new image path is writable or not ?
			$this->isFolderWritable($this->newimagepath);

			// resize the original image to size of editor
			$resizedImage = imagecreatetruecolor($this->imgW, $this->imgH);
			imagecopyresampled($resizedImage, $imgExtension['source_image'], 0, 0, 0, 0, $this->imgW, $this->imgH, $this->imgInitW, $this->imgInitH);

			// rotate the rezized image
			$rotated_image = imagerotate($resizedImage, -$angle, 0);

			// find new width & height of rotated image
			$rotated_width = imagesx($rotated_image);
			$rotated_height = imagesy($rotated_image);

			// diff between rotated & original sizes
			$dx = $rotated_width - $this->imgW;
			$dy = $rotated_height - $this->imgH;

			// crop rotated image to fit into original rezized rectangle
			$cropped_rotated_image = imagecreatetruecolor($this->cropW, $this->cropH);
			imagecolortransparent($cropped_rotated_image, imagecolorallocate($cropped_rotated_image, 0, 0, 0));
			imagecopyresampled($cropped_rotated_image, $rotated_image, 0, 0, $dx / 2, $dy / 2, $this->imgW, $this->imgH, $this->imgW, $this->imgH);

			// crop image into selected area
			$final_image = imagecreatetruecolor($this->cropW, $this->cropH);
			print_r($final_image);
			imagecolortransparent($final_image, imagecolorallocate($final_image, 0, 0, 0));
			imagecopyresampled($final_image, $cropped_rotated_image, 0, 0, $this->imgX1, $this->imgY1, $this->cropW, $this->cropH, $this->cropW, $this->cropH);

			// finally output image
			return $this->createImage($final_image, $imgExtension['type']);
		}catch(Exception $e){
			throw new Exception('Exception raised while creating resizeable Image', $e->errorMessage());
			die;
		}
	}
	
}

?>
