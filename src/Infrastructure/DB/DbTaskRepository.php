<?php
declare(strict_types=1);

namespace App\Infrastructure\DB;

use App\Domain\Entities\Task;
use App\Domain\Service\TaskRepository;
use App\Domain\VO\TaskId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;
use RuntimeException;

class DbTaskRepository extends ServiceEntityRepository implements TaskRepository
{
    public function __construct(private ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function create(Task $task): void
    {
        $this->getEntityManager()->persist($task);
        $this->getEntityManager()->flush();
    }

    public function getById(TaskId $id): ?Task
    {
        return $this->find($id->id);
    }

    public function getSubTasks(TaskId $id): array
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('t')
            ->from(Task::class, 't')
            ->where('t.epicTaskId = :id')
            ->setParameter('id', $id->id)
            ->getQuery()
            ->getResult();
    }

    public function loadBy(
        ?string $searchTerm = null,
        array   $statuses = [],
        array   $priorities = [],
        array   $sortBy = []
    ): array {
        $conn = $this->getEntityManager()->getConnection();

        $orderBy = [];
        foreach ($sortBy as $field => $dir) {
            $dir = strtoupper($dir) === 'DESC' ? 'DESC' : 'ASC';
            if (!in_array($field, ['createdAt', 'completedAt', 'priority'])) {
                continue;
            }
            $orderBy[] = "$field $dir";
        }

        $sql = "
        SELECT * FROM task
        WHERE MATCH(title, description) AGAINST (:query IN BOOLEAN MODE)
        AND status IN (:statuses)
        AND priority IN (:priorities)
        " . (!empty($orderBy) ? 'ORDER BY ' . implode(', ', $orderBy) : '');

        $stmt = $conn->prepare($sql);
        $stmt->bindValue('query' , $searchTerm);
        $stmt->bindValue('statuses' , $statuses,ArrayParameterType::STRING);
        $stmt->bindValue('priorities', $priorities,ArrayParameterType::INTEGER);
        $result = executeQuery();

        return $result->fetchAllAssociative();
    }

    public function remove(TaskId $id): void
    {
        $task = $this->getById($id);
        if ($task === null) {
            return;
        }
        $this->getEntityManager()->remove($task);
        $this->getEntityManager()->flush();
    }

    public function update(Task $task): void
    {
        $this->getEntityManager()->flush();
    }
}
