CREATE TABLE `encuestas` (
    `id_encuestas` INT NOT NULL AUTO_INCREMENT ,
    `encuesta` VARCHAR(100) NOT NULL ,
    PRIMARY KEY (`id_encuestas`)
) ENGINE = MyISAM;

INSERT INTO `encuestas` (`id_encuestas`, `encuesta`) VALUES (NULL, 'CONTANOS DÓNDE NOS CONOCISTE');

CREATE TABLE `encuestas_preguntas` (
    `id_encuetas_preguntas` INT NOT NULL AUTO_INCREMENT ,
    `pregunta` VARCHAR(100) NOT NULL ,
    `abierta` BOOLEAN NOT NULL ,
    `orden` INT NOT NULL ,
    PRIMARY KEY (`id_encuetas_preguntas`)
) ENGINE = MyISAM;

INSERT INTO `encuestas_preguntas` (`id_encuetas_preguntas`, `pregunta`, `abierta`, `orden`) VALUES (NULL, 'Ya soy paciente', '0', '1'), (NULL, 'Facebook', '0', '2');
INSERT INTO `encuestas_preguntas` (`id_encuetas_preguntas`, `pregunta`, `abierta`, `orden`) VALUES (NULL, 'Recomendado', '0', '3'), (NULL, 'Otro', '1', '4');

CREATE TABLE `encuestas_respuestas` (
    `id_encuestas_respuestas` INT NOT NULL AUTO_INCREMENT ,
    `id_turnos` INT NOT NULL ,
    `id_encuestas_preguntas` INT NOT NULL ,
    PRIMARY KEY (`id_encuestas_respuestas`)
) ENGINE = MyISAM;

CREATE TABLE `encuestas_respuestas_abiertas` (
    `id_encuestas_respuestas_abiertas` INT NOT NULL AUTO_INCREMENT ,
    `id_encuestas_respuestas` INT NOT NULL ,
    `respuesta` VARCHAR(100) NOT NULL ,
    PRIMARY KEY (`id_encuestas_respuestas_abiertas`)
) ENGINE = MyISAM;

ALTER TABLE `encuestas_preguntas` ADD `id_encuestas` INT NOT NULL AFTER `id_encuetas_preguntas`;

UPDATE `encuestas_preguntas` SET `id_encuestas` = 1;

ALTER TABLE `encuestas_preguntas` CHANGE `id_encuetas_preguntas` `id_encuestas_preguntas` INT(11) NOT NULL AUTO_INCREMENT;
