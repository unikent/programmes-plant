<?php
/**
 *
 *
 */
class ResizeThumbnails_Task
{

	/**
	 * Regenerate the thumbnails from the original images
	 *
	 * @param array  $arguments The arguments sent to the command. Optional maxWidth
	 */
	public function run($arguments = array())
	{
		if (sizeof($arguments) === 0) {
			$maxWidth =(int)Config::get('images.thumbnail_max_width');
		} else {
			$maxWidth = (int)$arguments[0];
		}
		if ($maxWidth < 50) {
			echo "\n Error: width cannot be less than 50. \n";
			die();
		}
		echo "Resizing images to maximum width of $maxWidth\n\n";
		$images = Image::get();

		foreach (Image::get() as $image) {
			$path = Config::get('images.image_directory', path('storage').'images');
			$src = $path . '/' . $image->id . '.jpg';
			$dest = $path . '/' . $image->id . '_thumb.jpg';
			echo "Resizing {$image->id} - {$image->name}...";
			if ($this->make_thumb($path.'/'.$image->id.'.jpg', $path.'/'.$image->id.'_thumb.jpg', $maxWidth)) {
				echo "SUCCESS!\n";
			} else {
				echo "FAILED\n";
			}
			flush();
		}
	}

	protected function make_thumb($src, $dest, $desired_width)
	{
		$result = false;
		/* read the source image */
		$source_image = imagecreatefromjpeg($src);
		$width = imagesx($source_image);
		$height = imagesy($source_image);

		// Get thumb sizeing
		list($desired_height, $desired_width) = Image::getThumbSize($desired_width, $width, $height);

		if ($source_image) {
			/* create a new, "virtual" image */
			$virtual_image = imagecreatetruecolor($desired_width, $desired_height);
			if ($virtual_image) {
				/* copy source image at a resized size */
				if (imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height)) {
					/* create the physical thumbnail image to its destination */
					$result = imagejpeg($virtual_image, $dest);
				}
				imagedestroy($virtual_image);
			}
			imagedestroy($source_image);
		}
		return $result;
	}
}
