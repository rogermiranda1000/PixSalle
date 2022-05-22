<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use PDO;
use Salle\PixSalle\Model\Portfolio;

final class MySQLPortfolioRepository implements PortfolioRepository
{
    private PDO $databaseConnection;

    public function __construct(PDO $database)
    {
        $this->databaseConnection = $database;
    }

    public function createPortfolio(Portfolio $portfolio) : void
    {
        $query = <<<'QUERY'
        INSERT INTO portfolios(name, user_id)
        VALUES(:name, :user_id)
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $name = $portfolio->name();
        $user_id = $portfolio->user_id();

        $statement->bindParam('name', $name, PDO::PARAM_STR);
        $statement->bindParam('user_id', $user_id, PDO::PARAM_INT);

        $statement->execute();
    }

    public function getPortfolioByUserId(int $user_id)
    {
        $query = "SELECT * FROM portfolios WHERE user_id = :user_id";

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('user_id', $user_id, PDO::PARAM_INT);

        $statement->execute();

        $count = $statement->rowCount();
        if ($count > 0) {
            $row = $statement->fetch(PDO::FETCH_OBJ);
            return $row;
        }
        return null;
    }
}
