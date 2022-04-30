<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use Salle\PixSalle\Repository\ImageRepository;

final class ImageManager implements ImageRepository {
	private string $base_path;

	public function __construct(string $base_path) {
		$this->base_path = $base_path;
	}

	public function getPhoto(string $uuid, string $path): string {
		$path = $this->base_path . $uuid . '.' . $path;
		$type = pathinfo($path, PATHINFO_EXTENSION);
		$data = file_get_contents($path);
		return 'data:image/' . $type . ';base64,' . base64_encode($data);
	}

    public function savePhoto(string $photo): string {
		return ""; // TODO return saved UUID
	}
}