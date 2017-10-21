CREATE TABLE `medicosext` (
  `id_medicosext` bigint(20) NOT NULL,
  `apellidos` varchar(254) DEFAULT NULL,
  `nombres` varchar(254) DEFAULT NULL,
  `estado` bigint(20) DEFAULT NULL,
  `saludo` varchar(10) NOT NULL,
  `matricula` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

ALTER TABLE `medicosext`
  ADD PRIMARY KEY (`id_medicosext`);

ALTER TABLE `medicosext`
  MODIFY `id_medicosext` bigint(20) NOT NULL AUTO_INCREMENT;
