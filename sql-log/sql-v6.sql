CREATE TABLE `agendas` (
    `id_agendas` INT NOT NULL AUTO_INCREMENT ,
    `nombre` VARCHAR(100) NOT NULL ,
    `apellido` VARCHAR(100) NOT NULL ,
    `rubro` VARCHAR(100) NOT NULL ,
    `celular` VARCHAR(100) NOT NULL ,
    `telefono` VARCHAR(100) NOT NULL ,
    `direccion` VARCHAR(100) NOT NULL ,
    `tipo` INT NOT NULL , PRIMARY KEY (`id_agendas`)
) ENGINE = MyISAM;

ALTER TABLE `agendas` ADD `estado` TINYINT(1) NOT NULL AFTER `tipo`;

ALTER TABLE `agendas` CHANGE `tipo` `id_agendas_tipos` INT(11) NOT NULL;

CREATE TABLE `agendas_tipos` (
    `id_agendas_tipos` INT NOT NULL AUTO_INCREMENT ,
    `nombre` VARCHAR(100) NOT NULL ,
    PRIMARY KEY (`id_agendas_tipos`)
) ENGINE = MyISAM;

INSERT INTO `agendas_tipos` (`id_agendas_tipos`, `nombre`) VALUES (NULL, 'Proveedor'), (NULL, 'Médico Externo'), (NULL, 'Personal de Mantenimiento');
