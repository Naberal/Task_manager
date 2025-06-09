<?php
declare(strict_types=1);

namespace App\Infrastructure\DB;

use App\Domain\Entities\Task;
use App\Domain\Service\TaskRepository;
use App\Domain\VO\TaskId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function findBy(
        array  $criteria,
        ?array $orderBy = null,
        ?int   $limit = null,
        ?int   $offset = null
    ): array
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
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

    public function getById(TaskId $id): ?Task
    {
        return $this->find($id->id);
    }

    public function update(Task $task): void
    {
        $this->getEntityManager()->flush();
    }
}
