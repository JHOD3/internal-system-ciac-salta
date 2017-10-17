ALTER TABLE `usuarios` ADD `superuser` TINYINT(1) NOT NULL DEFAULT '0' AFTER `estado`;
UPDATE `usuarios` SET superuser = 1 WHERE id_usuarios = '5';
UPDATE `usuarios` SET superuser = 2 WHERE id_usuarios = '0';
UPDATE `usuarios` SET superuser = 2 WHERE id_usuarios = '10';
