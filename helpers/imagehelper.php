<?php
class ImageHelper
{
	static function DisplaySerialisedImages($ImageArray, $Templater)
	{
		$ImageFound = false;
		$ImageArray = unserialize($ImageArray);

		if (!empty($ImageArray))
		{
			$Templater->BeginTag('div', array('style' => 'text-align: center;'));
				$Templater->BeginTag('div', array('class' => 'bss-slides num1', 'tabindex' => '1'));
					foreach ($ImageArray as $ImageValue)
					{
						if (!empty($ImageValue))
						{
							$ImageFound = true;
							$Templater->BeginTag('figure');
								$Templater->BeginTag('img', array('class' => 'boundedbox screenshot', 'src' => $ImageValue), true);
							$Templater->EndLastTag();
						}
					}
				$Templater->EndLastTag();
			$Templater->EndLastTag();
		}

		return $ImageFound;
	}
	
	static function GetDominantColorAndTextColour($ImageLocation, &$DominantRGB, &$TextColour)
	{
		$Image;
		switch ((new finfo(FILEINFO_MIME_TYPE))->buffer(file_get_contents($ImageLocation)))
		{
			case 'image/jpeg':
			case 'image/jpg': $Image = imagecreatefromjpeg($ImageLocation); break;
			case 'image/png': $Image = imagecreatefrompng($ImageLocation); break;
			case 'image/gif': $Image = imagecreatefromgif($ImageLocation); break;

			default:
			{
				$DominantRGB = '#FFFFFF';
				$TextColour = '#000000';
				return;
			}
		}

		$ImageWidth = imagesx($Image);
		$ImageHeight = imagesy($Image);
		$RGBCounter = array();

		for ($X = 0; $X < $ImageWidth; $X += $ImageWidth)
		{
			for ($Y = 0; $Y < $ImageHeight; $Y += $ImageHeight)
			{
				$Color = ImageColorAt($Image, $X, $Y);
				if (isset($RGBCounter[$Color]))
				{
					$RGBCounter[$Color]++;
				}
				else
				{
					$RGBCounter[$Color] = 0;
				}
			}
		}

		$DominantRGB = '#' . dechex(array_search(max($RGBCounter), $RGBCounter));
		$DominantRGB = str_pad($DominantRGB, 7, '0', STR_PAD_RIGHT);
		if (
			(
				pow(hexdec(substr($DominantRGB, 0, 2)) / 255, 2.2) * 0.2126 +
				pow(hexdec(substr($DominantRGB, 2, 4)) / 255, 2.2)* 0.7152 +
				pow(hexdec(substr($DominantRGB, 4, 6)) / 255, 2.2) * 0.0722
			) > 130000
		)
		{
			$TextColour = '#000000';
		}
		else
		{
			$TextColour = '#FFFFFF';
		}
	}
}
?>