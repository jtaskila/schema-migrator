ALTER TABLE migrations 
ADD COLUMN timestamp bigint UNSIGNED
AFTER path;
