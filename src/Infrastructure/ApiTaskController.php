<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Domain\Entities\Task;
use App\Domain\VO\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_')]
class ApiTaskController extends AbstractController
{
    #[Route('/task', name: 'create_task', methods: ['POST'])]
    public function createTask(Task $task): Response
    {
        return $this->json($task);

    }

    #[Route('/task/{id}', name: 'delete_task', methods: ['DELETE'])]
    public function deleteTask(Id $id): Response
    {
        return new Response("Task with id $id was successfully deleted", 201);
    }

    #[Route('/myTasks', name: 'my_tasks', methods: ['GET'])]
    public function getMyTasks(): Response
    {
        return $this->json([]);
    }

    #[Route('/task/{id}/done', name: 'mark_as_done', methods: ['PUT'])]
    public function markTaskAsDone(Id $id): Response
    {
        return new Response("Task with id $id was successfully mark as done", 201);
    }

    #[Route('/task/{id}', name: 'update_task', methods: ['PUT'])]
    public function updateTask(): Response
    {
        return $this->json([]);
    }
}
