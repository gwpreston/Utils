<?php

namespace Pandar\io;

use \UnexpectedValueException;

/**
* A simple way to compress & uncompress paths.
*
* ```php
* $zip = new Zip();
* $zip->compress('./images', 5);
* $zip->uncompress('./images.gz');
*
* ```
*/
class Zip {

	/**
   * @var int ZIP_LENGTH
   */
	const ZIP_LENGTH = 4096;

	/**
	*
	*
	*/
	public function __construct() {

	}

	/**
	* Compress a given path into a zip file
	*
	* @param string $srcFileName The path to compress into a gz file.
	* @param int $level The leve of compression from 0 - 9
	* @return void
	* @throws UnexpectedValueException
	*/
	public function compress($srcFileName, $level = 9) {

		$level = is_int($level) && $level >= 0 && $level <= 9 ? $level : 9;
		$dstFileName = $srcFileName . '.gz';

		if($fp = @fopen($srcFileName, 'r')) {

			if($zp = gzopen($dstFileName, 'w' . $level)) {
				while(!feof($fp)) {
					$data = fread($fp, self::ZIP_LENGTH);
					gzwrite($zp, $data, strlen($data));
				}
				gzclose($zp);
			}
			else {
				throw new UnexpectedValueException('Could not open destination file');
			}

			fclose($fp);
		}
		else {
			throw new UnexpectedValueException('Could not open gzip file');
		}

	}

  /**
	* Compress a given path into a zip file
	*
	* @param string $srcFileName The path to the compressed file.
	* @param string $dstFileName The path to save the files that are extracted from compressed file.
	* @return void
	* @throws UnexpectedValueException
	* @todo Needs tested
	*/
	public function uncompress($srcFileName, $dstFileName) {

		# getting content of the compressed file
		if($zp = gzopen($srcFileName, 'rb')) {

			if($fp = fopen($dstFileName, 'w')) {

				while (!gzeof($zp)) {
					$data = fread($zp, self::ZIP_LENGTH);
					# writing uncompressed file
					fwrite($fp, $data, strlen($data));
				}

				fclose($fp);
			}
			else {
				throw new UnexpectedValueException('Could not open destination file');
			}

			gzclose($zp);
		}
		else {
			throw new UnexpectedValueException('Could not open gzip file');
		}

	}

}

?>
