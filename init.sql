CREATE DATABASE IF NOT EXISTS task_manager;
USE task_manager;
CREATE TABLE IF NOT EXISTS users
(
    id      CHAR(8) PRIMARY KEY,
    api_key CHAR(16) NOT NULL UNIQUE
) ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS tasks
(
    id           CHAR(4) PRIMARY KEY,
    owner_id     CHAR(8)      NOT NULL,
    title        VARCHAR(100) NOT NULL,
    description  TEXT         NOT NULL,
    priority     INT          NOT NULL CHECK (priority BETWEEN 1 AND 5),
    status       VARCHAR(10)  NOT NULL,
    created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    completed_at DATETIME     NULL,
    epic_task_id CHAR(4)      NULL,
    CONSTRAINT fk_epic_task FOREIGN KEY (epic_task_id) REFERENCES tasks (id) ON DELETE SET NULL
        ON UPDATE CASCADE,
    CONSTRAINT fk_owner FOREIGN KEY (owner_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT chk_dates CHECK (completed_at IS NULL OR completed_at >= created_at),
    FULLTEXT search (title, description)
) ENGINE = InnoDB;
