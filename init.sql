CREATE DATABASE IF NOT EXISTS task_manager;
USE task_manager;
CREATE TABLE IF NOT EXISTS tasks
(
    id           CHAR(4) PRIMARY KEY,
    title        VARCHAR(100) NOT NULL,
    description  TEXT         NOT NULL,
    priority     INT          NOT NULL CHECK (priority BETWEEN 1 AND 5),
    status       VARCHAR(10)  NOT NULL,
    created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    completed_at DATETIME     NULL,
    parent_id    CHAR(4)      NULL,
    CONSTRAINT fk_parent FOREIGN KEY (parent_id) REFERENCES tasks (id) ON DELETE SET NULL
        ON UPDATE CASCADE,
    CONSTRAINT chk_dates CHECK (completed_at IS NULL OR completed_at >= created_at),
    FULLTEXT (title, description)
) ENGINE = InnoDB;
