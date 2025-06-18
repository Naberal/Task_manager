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
        $sql = "SELECT * FROM tasks WHERE owner_id = :owner_id";
        $sql .= $this->addWhereConditions($searchTerm, $statuses, $priorities);
        $sql .= $this->addOrderBy($sortBy);

        $resultSetMappingBuilder = new ResultSetMappingBuilder($this->getEntityManager());
        $resultSetMappingBuilder->addRootEntityFromClassMetadata(Task::class, 't');
        $query = $this->getEntityManager()->createNativeQuery($sql, $resultSetMappingBuilder);
        $query->setParameter('owner_id', $ownerId->id);
        $query->setParameter('searchTerm', "+" . $searchTerm . "*");
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

    private function addOrderBy(array $sortBy): string
    {
        $orderBy = [];
        foreach ($sortBy as $field => $dir) {
            $orderBy[] = match ($field) {
                'priority' => "priority $dir",
                'createdAt' => "created_at $dir",
                'completedAt' => "completed_at $dir",
            };
        }

        if (!empty($orderBy)) {
            return " ORDER BY " . implode(", ", $orderBy);
        }
        return '';
    }

    /**
     * @param string|null $searchTerm
     * @param array $statuses
     * @param array $priorities
     * @return string
     */
    private function addWhereConditions(?string $searchTerm, array $statuses, array $priorities): string
    {
        $conditions = [];
        if (!empty($searchTerm)) {
            $conditions[] = "MATCH(title, description) AGAINST (:searchTerm IN NATURAL LANGUAGE MODE)";
        }
        if (!empty($statuses)) {
            $conditions[] = "status IN (:statuses)";
        }
        if (!empty($priorities)) {
            $conditions[] = "priority IN (:priorities)";
        }

        if (!empty($conditions)) {
            return " AND " . implode(" AND ", $conditions);
        }

        return "";
    }
}