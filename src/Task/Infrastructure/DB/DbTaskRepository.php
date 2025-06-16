<?php
declare(strict_types=1);

namespace App\Task\Infrastructure\DB;

use App\Task\Domain\Entities\Task;
use App\Task\Domain\Service\TaskRepository;
use App\Task\Domain\VO\OwnerId;
use App\Task\Domain\VO\Priority;
use App\Task\Domain\VO\Status;
use App\Task\Domain\VO\TaskId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\LockMode;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;
use RuntimeException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class DbTaskRepository extends ServiceEntityRepository implements TaskRepository
{
    public function __construct(ManagerRegistry $registry)
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
        OwnerId $ownerId,
        ?string $searchTerm = null,
        array   $statuses = [],
        array   $priorities = [],
        array   $sortBy = []
    ): array {
        $orderBy = [];
        foreach ($sortBy as $field => $dir) {
            $orderBy[] = "$field $dir";
        }
        $resultSetMappingBuilder = new ResultSetMappingBuilder($this->getEntityManager());
        $resultSetMappingBuilder->addRootEntityFromClassMetadata(Task::class, 't');
        $query = $this->getEntityManager()->createNativeQuery(
            $this->buildSqlQueryLoadBy($searchTerm, $statuses, $priorities, $orderBy),
            $resultSetMappingBuilder
        );
        $query->setParameter('owner_id', $ownerId->id);
        $query->setParameter('query', "+" . $searchTerm . "*");
        $query->setParameter('statuses', array_map(fn(Status $s) => $s->value, $statuses), ArrayParameterType::STRING);
        $query->setParameter('priorities', array_map(fn(Priority $p) => $p->value, $priorities), ArrayParameterType::INTEGER);


        return $query->getResult();
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

    /**
     * @param string|null $searchTerm
     * @param array $statuses
     * @param array $priorities
     * @param array $orderBy
     * @return string
     */
    private function buildSqlQueryLoadBy(?string $searchTerm, array $statuses, array $priorities, array $orderBy): string
    {
        $sql = "SELECT * FROM tasks WHERE owner_id = :owner_id";
        $conditions = [];

        if (!empty($searchTerm)) {
            $conditions[] = "MATCH(title, description) AGAINST (:query IN NATURAL LANGUAGE MODE)";
        }
        if (!empty($statuses)) {
            $conditions[] = "status IN (:statuses)";
        }
        if (!empty($priorities)) {
            $conditions[] = "priority IN (:priorities)";
        }

        if (!empty($conditions)) {
            $sql .= " AND " . implode(" AND ", $conditions);
        }

        if (!empty($orderBy)) {
            $sql .= " ORDER BY " . implode(', ', $orderBy);
        }
        return $sql;
    }
}