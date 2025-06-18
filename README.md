
# Task Manager

A robust task management application built with Symfony, following Domain-Driven Design (DDD) principles.

## Project Overview

This Task Manager allows users to create, edit, view, and delete tasks. It provides a clean API for task management with proper authorization and validation.

## Architecture

The application is built using Domain-Driven Design (DDD) architecture, which organizes the codebase around business domains and separates concerns into different layers:

### Domain Layer
Contains the core business logic, entities, value objects, and domain services. This layer is independent of any infrastructure or application concerns.

### Application Layer
Contains application services, DTOs (Data Transfer Objects), and interfaces that orchestrate the use cases of the application.

### Infrastructure Layer
Contains implementations of repositories, database access, external services, and other technical concerns.

## Domain Structure

The application is divided into two main domains:

### Task Domain
- **Domain**: Contains task entities, repositories, and domain services
- **Application**: Contains task-related use cases, DTOs, and services
- **Infrastructure**: Contains implementations of task repositories and other technical concerns

### User Domain
- **Domain**: Contains user entities, value objects, and domain services
- **Infrastructure**: Contains user repository implementations and authentication mechanisms

## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --pull --no-cache` to build fresh images
3. Run `docker compose up --wait` to set up and start a fresh Symfony project
4. (Optional) Run `docker compose exec php php bin/console doctrine:fixtures:load`
5. Open `https://localhost` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
6. Run `docker compose down --remove-orphans` to stop the Docker containers.

## API Endpoints

The application provides RESTful API endpoints for task management:
- Create tasks
- Edit tasks
- View tasks
- Delete tasks

All operations respect user permissions and validate relationships between entities.
