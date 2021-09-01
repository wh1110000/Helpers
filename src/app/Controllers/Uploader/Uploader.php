<?php

namespace wh1110000\helpers\Controllers\Uploader;

use Illuminate\Support\Facades\Storage;

/**
 * Class Uploader
 * @package Workhouse\Cms\Helpers
 */

class Uploader {

	/**
	 * @var
	 */

	private $model;

	/**
	 * @var bool
	 */

	private $hashedName = true;

	/**
	 * @var bool
	 */

	private $saveAsDraft = false;

	/**
	 * @var bool
	 */

	private $savedAsDraft = false;

	/**
	 * @var string
	 */

	private $mediaFolder = 'img/';

	/**
	 * @var string
	 */

	private $draftFolder = 'storage/_uploads';

	/**
	 * @var string
	 */

	private $disk;

	/**
	 * @var bool
	 */

	private $subfolder;

	/**
	 * @var
	 */

	public $fileName;

	/**
	 * @var
	 */

	private $file;
	/**
	 * @var
	 */

	private $destinationPath;

	/**
	 * Uploader constructor.
	 */

	public function __construct() {

		$this->disk('public');
	}

	/**
	 * @return $this
	 */

	public function saveAsDraft(){

		$this->saveAsDraft = true;

		return $this;
	}

	/**
	 * @param bool $hashedName
	 *
	 * @return $this
	 */

	public function hashedName($hashedName = true){

		$this->hashedName = $hashedName;

		return $this;
	}

	/**
	 * @param $disk
	 *
	 * @return $this
	 */

	public function disk($disk){

		if(in_array($disk, array('local', 'public', 's3'))){

			$this->disk = Storage::disk($disk);
		}

		return $this;
	}

	/**
	 * @param $object
	 *
	 * @return $this
	 */

	public function model($object){

		$this->model = $object;

		return $this;
	}

	/**
	 * @param bool $subfolder
	 *
	 * @return $this
	 */

	public function subfolder($subfolder = true){

		$this->subfolder = $subfolder;

		return $this;
	}

	/**
	 * @param $file
	 *
	 * @return $this
	 */

	public function setFile($file){

		$this->file = $file;

		return $this;
	}

	/**
	 * @return mixed
	 */

	public function getFilename(){

		return $this->fileName;
	}

	/**
	 * @return mixed
	 */

	public function getSize(){

		if($this->getFile()){

			return $this->getFile()->getSize();
		}
	}

	/**
	 * @return mixed
	 */

	public function getMime(){

		if($this->getFile()){

			return $this->getFile()->getClientMimeType();
		}
	}

	/**
	 * @return mixed
	 */

	public function getDimensions($type){

		if($this->getFile()) {

			$dimensions = getimagesize( $this->getFile() );

			if(is_array($dimensions) && isset($dimensions[1])){

				switch ($type){

					case 'width':

						$result = $dimensions[0];

						break;


					case 'height':

						$result = $dimensions[1];

						break;
				}
			}
		}

		return $result ?? 0;
	}

	/**
	 * @return mixed
	 */

	public function getWidth(){

		return $this->getDimensions('width');
	}

	/**
	 * @return mixed
	 */

	public function getHeight(){

		return $this->getDimensions('height');
	}

	/**
	 * @return mixed
	 */

	public function getFile(){

		return $this->file;
	}

	/**
	 *
	 */

	public function prepareUpload() {

		if ( $this->saveAsDraft ) {

			$this->destinationPath = $this->draftFolder . '/';

		} else {

			$this->destinationPath = $this->destinationPath . $this->mediaFolder . '/';

			if ( is_bool( $this->subfolder ) && optional( $this->model )->getId() ) {

				$this->destinationPath = $this->destinationPath . $this->model->id . '/';

			} else {

				$this->destinationPath = $this->destinationPath . $this->subfolder . '/';
			}
		}

		if ( ! $this->disk->exists( $this->destinationPath ) ) {

			$this->disk->makeDirectory( $this->destinationPath );
		}

		if ( ! $this->savedAsDraft ) {

			$this->fileName = $this->hashedName ? uniqid() . time() . '.' . $this->file->getClientOriginalExtension() : $this->file->getClientOriginalName();
		}
	}

	/**
	 * @return $this|bool|string
	 */


	public function upload(){

		$this->prepareUpload();

		try {

			if($this->savedAsDraft){

				if($this->disk->exists($this->draftFolder . '/' . $this->fileName)){

					$this->disk->copy($this->draftFolder . '/' . $this->fileName, $this->destinationPath.$this->fileName);
				}

			} else {

				if(in_array($this->file->getClientOriginalExtension(), ['doc','docx','odt','pdf', 'svg', 'txt', 'csv'])){


					$this->disk->putFileAs($this->destinationPath, $this->file, $this->fileName);


				} else {

					$image = \Image::make($this->file->getRealPath());

					$this->disk->put($this->destinationPath.$this->fileName, $image->encode());
				}
			}

			if($this->saveAsDraft){

				$this->saveAsDraft = false;

				$this->savedAsDraft = true;

				return $this;

			} else {

				return $this;
			}

		} catch(\Exception $e){

			return false;
		}
	}
}