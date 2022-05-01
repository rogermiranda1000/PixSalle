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

    public function savePhoto(string $photo, string $extension): string {
        $uuid = Uuid::uuid4()->toString();
        $photo->moveTo($this->base_path . $uuid . '.' . $extension);
		return $uuid;
	}
}