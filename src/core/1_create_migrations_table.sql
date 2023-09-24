CREATE TABLE IF NOT EXISTS migrations (
    id int NOT NULL AUTO_INCREMENT,
    path text NOT NULL,
    timestamp bigint unsigned NOT NULL,
    PRIMARY KEY(id)
);