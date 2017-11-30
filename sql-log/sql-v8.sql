ALTER TABLE `mantenimientos` ADD COLUMN `creado`  datetime NOT NULL AFTER `id_mantenimientos`;
UPDATE `mantenimientos` AS m SET m.creado = (SELECT MIN(mh.fecha) FROM mantenimhistoricos AS mh WHERE m.id_mantenimientos = mh.id_mantenimientos);

ALTER TABLE `mantenimhistoricos` ADD COLUMN `creado`  datetime NOT NULL AFTER `id_mantenimientos`;
UPDATE `mantenimhistoricos` AS mh SET mh.creado = (SELECT m.creado FROM mantenimientos AS m WHERE m.id_mantenimientos = mh.id_mantenimientos);
