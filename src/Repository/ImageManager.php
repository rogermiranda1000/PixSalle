<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use Ramsey\Uuid\Uuid;
use Salle\PixSalle\Repository\ImageRepository;

final class ImageManager implements ImageRepository {
	private string $base_path;

	public function __construct(string $base_path) {
		$this->base_path = $base_path;
	}

	public function getPhoto(string $uuid, string $extension): string {
		$path = $this->base_path . $uuid . '.' . $extension;
		$type = pathinfo($path, PATHINFO_EXTENSION);
		$data = file_get_contents($path);
		return 'data:image/' . $type . ';base64,' . base64_encode($data);
	}

    public function savePhoto($photo): string {
		// get extension
        $fileName = $photo->getClientFilename();
        $fileInfo = pathinfo($fileName);
        $format = $fileInfo['extension'];

        $uuid = Uuid::uuid4()->toString();
        $photo->moveTo($this->base_path . $uuid . '.' . $format);
		return $uuid;
	}

	public function getPhotoSize(string $uuid, string $extension): array {
		return getimagesize($this->base_path . $uuid . '.' . $extension);
	}

	
    public function removePhoto(string $uuid, string $extension): void {
		unlink($this->base_path . $uuid . '.' . $extension);
	}
}