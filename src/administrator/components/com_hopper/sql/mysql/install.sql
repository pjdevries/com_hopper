CREATE TABLE IF NOT EXISTS `#__hopper_projects`
(
    `id`          INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `title`       VARCHAR(255) NOT NULL,
    `alias`       VARCHAR(400) NOT NULL,
    `description` TEXT,
    `params`      TEXT,
    `created`     DATETIME DEFAULT NOW(),
    `modified`    DATETIME DEFAULT NOW(),
    `created_by`  INT          NOT NULL,
    `modified_by` INT          NOT NULL,
    INDEX `idx_title` (`title`),
    INDEX `idx_alias` (`alias`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  DEFAULT COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__hopper_releases`
(
    `id`          INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `project_id`  INT UNSIGNED NOT NULL,
    `version`     VARCHAR(24)  NOT NULL,
    `note`        VARCHAR(255),
    `params`      TEXT,
    `created`     DATETIME DEFAULT NOW(),
    `modified`    DATETIME DEFAULT NOW(),
    `created_by`  INT          NOT NULL,
    `modified_by` INT          NOT NULL,
    INDEX `idx_project_id` (`project_id`),
    INDEX `idx_version` (`version`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  DEFAULT COLLATE = utf8mb4_unicode_ci;