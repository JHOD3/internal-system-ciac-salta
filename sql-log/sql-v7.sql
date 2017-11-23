CREATE TABLE `mantenimientos` (
  `id_mantenimientos` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `id_sectores` int(11) NOT NULL,
  `solicitador` varchar(100) NOT NULL,
  `tarea` varchar(100) NOT NULL,
  `especialista` varchar(100) NOT NULL,
  `observaciones` varchar(200) NOT NULL,
  `estado` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE `mantenimientos`
  ADD PRIMARY KEY (`id_mantenimientos`);
ALTER TABLE `mantenimientos`
  MODIFY `id_mantenimientos` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `mantenimientos_estados` (
    `id_mantenimientos_estados` INT NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(100) NOT NULL,
    PRIMARY KEY (`id_mantenimientos_estados`)
) ENGINE = MyISAM;

INSERT INTO `mantenimientos_estados` (`nombre`) VALUES
    ('Solicitado'),
    ('Vino'),
    ('No vino'),
    ('En proceso'),
    ('No arreglado'),
    ('Solucionado')
;

ALTER TABLE `mantenimientos` MODIFY COLUMN `fecha`  datetime NOT NULL AFTER `id_mantenimientos`;

CREATE TABLE `mantenimhistoricos` (
  `id_mantenimhistoricos` int(11) NOT NULL AUTO_INCREMENT,
  `id_mantenimientos` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `id_sectores` int(11) NOT NULL,
  `solicitador` varchar(100) NOT NULL,
  `tarea` varchar(100) NOT NULL,
  `especialista` varchar(100) NOT NULL,
  `observaciones` varchar(200) NOT NULL,
  `estado` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_mantenimhistoricos`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

ALTER TABLE `mantenimientos` ADD COLUMN `id_mantenimientos_estados`  integer(11) NOT NULL AFTER `estado`;
ALTER TABLE `mantenimhistoricos` ADD COLUMN `id_mantenimientos_estados`  integer(11) NOT NULL AFTER `estado`;

ALTER TABLE `mantenimientos` ADD COLUMN `id_usuarios`  int(11) NOT NULL AFTER `id_mantenimientos_estados`;
ALTER TABLE `mantenimhistoricos` ADD COLUMN `id_usuarios`  int(11) NOT NULL AFTER `id_mantenimientos_estados`;
