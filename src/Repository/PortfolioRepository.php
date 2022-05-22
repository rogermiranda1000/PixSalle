<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use Salle\PixSalle\Model\Portfolio;

interface PortfolioRepository {

    public function createPortfolio(Portfolio $portfolio): void;
    public function getPortfolioByUserId(int $user_id);

}
