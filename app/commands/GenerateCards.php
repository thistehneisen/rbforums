<?php

set_time_limit(0);

Class GenerateCards {
    private $cli = null;
	public $path = null;
	public $tempFilePath = null;
	public $template = null;
	public $gd = null;
	public $cardW = 560;
	public $cardX = 662;
	public $cardY = 960;

    public function __construct(Console $cliObject) {
        $this->cli = $cliObject;
	    $this->path = BASE_PATH.'uploads/to-print/';
	    if(!file_exists($this->path)) {
		    mkdir($this->path, 0777);
	    }

	    $this->tempFilePath = $this->path.'temp/';
	    if(!file_exists($this->tempFilePath)) {
		    mkdir($this->tempFilePath, 0777);
	    }

	    $this->template = BASE_PATH.'assets/img/card-template.jpg';

	    $this->gd = new Gdhelper();

	    switch($this->cli->getCommand()) {
		    case "failed":
				$this->generateFailed();
			    break;
		    default:
			    $card = $this->cli->getParameter(0);
			    if($card) {
				    $this->generateCard($card);
			    } else {
				    $this->generateAllCards();
			    }
			    break;
	    }

    }

	public function generateFailed() {
		$this->cli->info("Populating card data");
		$cards = (new Cards())->where('failed', 1)->get()->result();
		$rows = [];
		$customPath = BASE_PATH.'uploads/to-print/failed/';
		if(!$cards->isEmpty()) {
			$this->cli->info("Generating ".count($cards->items()));
			foreach ($cards as $c) {
				$this->generateCard($c, $customPath);
				$rows[] = [
					'ID' => $c->id,
					'Sūtītājs' => $c->name,
					'Saņēmējs' => $c->name_to,
					'Iela, māja, dzīvoklis' => $c->street,
					'Pilsēta, novads, pagasts' => $c->city,
					'Pasta indekss' => $c->postal_code
				];
			}
		}
		$this->genCSV($rows, $customPath);
		$this->cli->info('ALL DONE');
	}

    public function generateAllCards() {
    	$this->cli->info("Populating card data");
	    $cards = (new Cards())->where('status >=', 0)->get()->result();
	    $rows = [];
	    if(!$cards->isEmpty()) {
	    	$this->cli->info("Generating ".count($cards->items()));
	    	foreach ($cards as $c) {
				$this->generateCard($c);
			    $rows[] = [
			    	'ID' => $c->id,
				    'Sūtītājs' => $c->name,
				    'Saņēmējs' => $c->name_to,
				    'Iela, māja, dzīvoklis' => $c->street,
				    'Pilsēta, novads, pagasts' => $c->city,
				    'Pasta indekss' => $c->postal_code
			    ];
		    }
	    }
	    $this->genCSV($rows);
	    $this->cli->info('ALL DONE');
    }

    public function genCSV(array $rows, $customPath = null) {
    	if(!is_null($customPath)) {
		    $path = $customPath;
	    } else {
		    $path = $this->path;
	    }
	    if(!file_exists($path)) {
	    	mkdir($path, 0777, true);
	    }

    	$file = $path.date('d.m.Y-G-i-s').'-export.csv';
	    $this->cli->info('Generating CSV file: '.$file);
	    array2csvFile($file, $rows, ';');
    }

	public function generateCard($id, $customPath = null) {
		if(!is_numeric($id) && $id) {
			$card = $id;
		} else {
			$card = (new Cards())->find($id);
		}
		if($card) {
			$this->cli->info("Generating card ".$card->id." ...");
			$asset = (new Assets())->find($card->img_id);
			if(!$asset) {
				$this->cli->error("Asset for card Nr. ".$card->id." not found");
			} else {
				$raw = BASE_PATH.'uploads/raw-images/'.$asset->local_file;
				//generate little picture
				$this->gd->width = $this->cardW;
				$this->gd->height = $this->cardW;
				$this->gd->source = $raw;
				$this->gd->destination = $this->tempFilePath.sprintf('%04d', $card->id).'.'.$asset->ext;

				if($asset->width == $asset->height && $asset->offset_x == 0 & $asset->offset_y == 0) {
					@$this->gd->resizeCrop();
				} else {
					@$this->gd->resizeProportionally();
					$this->gd->source = $this->gd->destination;

					$offsetX = ((abs($asset->offset_x) * 100 / Assets::$ratioW) / 100) * $this->cardW;
					$offsetY = ((abs($asset->offset_y) * 100 / Assets::$ratioW) / 100) * $this->cardW;

					$this->gd->crop($offsetX, $offsetY);

				}

				$exif = @exif_read_data($raw);
				if($exif) {
					$orientation = arrayGet($exif, 'Orientation');
					if(!is_null($orientation)) {
						$this->gd->source = $this->gd->destination;
						$this->gd->rotateByEXIFOrientation($orientation);
					}
				}

				if(!is_null($customPath)) {
					$imgPath = $customPath;
				} else {
					$imgPath = $this->path;
				}

				if(!file_exists($imgPath)) {
					mkdir($imgPath, 0777, true);
				}

				//put new image on card
				$this->gd->watermark = $this->tempFilePath.sprintf('%04d', $card->id).'.'.$asset->ext;
				$offset = $offset = round(($this->cardX-$this->cardW)/2);
				$this->gd->horizontal = $offset;
				$this->gd->vertical = $offset;
				$this->gd->source = $this->template;
				$this->gd->destination = $imgPath.sprintf('%04d', $card->id).'.jpg';
				$this->gd->setWatermark();
				unlink($this->gd->watermark);

				//set text
				$im = imagecreatefromjpeg($this->gd->destination);
				$textColor = imagecolorallocate($im, 0, 39, 63);
				$customStart = $this->cli->getAttribute('top');
				$offsetX = 750;
				if($customStart) {
					$offsetX = (int)$customStart;
				}
				$fontSize = 58;
				$lineHeight = 60;
				putenv('GDFONTPATH=' . BASE_PATH.'assets/fonts');

				$font = 'studioscripttt.ttf';

				$boundings = imagettfbbox($fontSize, 0, $font, $card->name);
				$w = abs($boundings[4]-$boundings[0]);
				if($w > $this->cardW) {
					//dalam
					$text = explode(' ', $card->name);
					$tmpText = $text[0];
					foreach ($text as $k => $word) {
						if($k != 0) {
							$tmpText .= ' '.$word;
						}
						$boundings = imagettfbbox($fontSize, 0, $font, $tmpText);
						$oldW = $w;
						$w = abs($boundings[4]-$boundings[0]);
						if($w > $this->cardW) {
							$tmpText = str_replace(' '.$word, '', $tmpText);
							$offset = $offset = round(($this->cardX-$this->cardW)/2) + round(($this->cardW - $oldW) /2);
							imagettftext($im, $fontSize, 0, $offset, $offsetX, $textColor, $font, $tmpText);
							$offsetX += $lineHeight;
							$tmpText = $word;
						}
					}
					$boundings = imagettfbbox($fontSize, 0, $font, $tmpText);
					$w = abs($boundings[4]-$boundings[0]);
					$offset = $offset = round(($this->cardX - $this->cardW)/2) + round(($this->cardW - $w) /2);
					imagettftext($im, $fontSize, 0, $offset, $offsetX, $textColor, $font, $tmpText);
				} else {
					$offsetX = 760;
					$offset = $offset = round(($this->cardX-$this->cardW)/2) + round(($this->cardW - $w) /2);
					imagettftext($im, $fontSize, 0, $offset, $offsetX, $textColor, $font, $card->name);
				}
				imagejpeg($im, $this->gd->destination, 100);
				imagedestroy($im);
			}
		} else {
			$this->cli->error("Image not found: $id");
		}
	}

}