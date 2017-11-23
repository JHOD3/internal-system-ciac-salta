
DROP TABLE IF EXISTS `mantenimhistoricos`;
CREATE TABLE `mantenimhistoricos` (
  `id_mantenimhistoricos` int(11) NOT NULL,
  `id_mantenimientos` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `id_sectores` int(11) NOT NULL,
  `solicitador` varchar(100) NOT NULL,
  `tarea` varchar(100) NOT NULL,
  `especialista` varchar(100) NOT NULL,
  `observaciones` varchar(200) NOT NULL,
  `estado` tinyint(1) NOT NULL,
  `id_mantenimientos_estados` int(11) NOT NULL,
  `id_usuarios` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `mantenimhistoricos`
--

INSERT INTO `mantenimhistoricos` (`id_mantenimhistoricos`, `id_mantenimientos`, `fecha`, `id_sectores`, `solicitador`, `tarea`, `especialista`, `observaciones`, `estado`, `id_mantenimientos_estados`, `id_usuarios`) VALUES
(1, 50, '2017-11-23 11:53:55', 1, 'DRA PORTUGAL', 'AIRE ACOND. TIRA OLOR ', 'ARNEDO', '', 1, 4, 10),
(2, 47, '2017-11-23 11:54:45', 1, 'DR CARLOS GIL', 'RETOQUE DE PINTURA Y TECHO', 'ARQUITECTA', '', 1, 4, 10),
(3, 48, '2017-11-23 11:54:59', 1, 'DR. ABRAHAM RAFAEL', 'AIRE ACONDIC', 'JOSE ARNEDO', '', 1, 6, 10),
(4, 49, '2017-11-23 11:55:17', 1, 'KAREN', 'PICAPORTE Y CERRADURA', 'MOISES CABRERA', '', 1, 4, 10),
(5, 45, '2017-11-23 11:55:27', 1, 'RUIZ ALVAREZ', 'CAMBIAR EL PICAPORTE', 'MOISES CABRERA', '', 1, 6, 10),
(6, 46, '2017-11-23 11:55:33', 1, 'DR TABOADA / ALVERO', 'AIRE ACONDIC', 'JOSE ARNEDO', '', 1, 6, 10),
(8, 44, '2017-11-23 11:55:55', 1, 'AD', 'PUERTA DE INGRESO AJUSTAR', 'CASEROS VIDRIOS 431-9369', '', 1, 4, 10),
(9, 42, '2017-11-23 11:56:01', 1, 'DR.LEO', 'ARMAR MESADA PLEGABLE DONDE ESTA EL ANAFE', 'DAN', '', 1, 4, 10),
(10, 43, '2017-11-23 11:56:08', 1, 'J SUAINA', 'CAMBIAR DE LUGAR PLANTA (FUENTE)', 'JAVIER JARDINERO', '', 1, 6, 10),
(11, 41, '2017-11-23 11:56:16', 1, 'SECRETARIAS', 'SILLA DE RECPCION', 'JUAREZ', '', 1, 6, 10),
(12, 39, '2017-11-23 11:56:23', 1, 'JAVIER', 'REUBICACION DE LAS PLANTAS', 'JAVIER', '', 1, 6, 10),
(13, 40, '2017-11-23 11:56:30', 1, 'JAVIER', 'LUCES DE EMERGENCIA DE LOS CONSULTORIOS 8-10', '', '', 1, 4, 10),
(14, 38, '2017-11-23 11:56:36', 1, '', 'PUERTA VIDRIO SIN FRENO', 'CASEROS VIDRIOS', '', 1, 6, 10),
(15, 37, '2017-11-23 11:56:44', 1, 'DR. LEONARDO SUAINA', 'BACHA TRANCADA', 'WALTER154474084', '', 1, 6, 10),
(16, 36, '2017-11-23 11:56:50', 1, '', 'BA?O PUBLICO AL FRENTE DEL LABORATORIO', 'WALTER154474084', '', 1, 6, 10),
(17, 34, '2017-11-23 11:56:57', 1, '', 'CAMBIO DE LUCES RX', 'MARCELO', '', 1, 4, 10),
(18, 35, '2017-11-23 11:57:02', 1, '', 'BOMBA DE FUENTE', 'MARCELO154530670', '', 1, 4, 10),
(19, 33, '2017-11-23 11:57:11', 1, 'DRA. SEGURA', 'AIRE ACONDIC', 'JOSE ARNEDO', '', 1, 6, 10),
(20, 32, '2017-11-23 11:57:20', 1, 'DR TABOADA', 'ARREGLO DE CERRADURA ', 'MOISES', '', 1, 6, 10),
(21, 31, '2017-11-23 11:57:26', 1, '', 'CONTROL REMOTO DR HJEALA', 'DR. HJEALA', '', 1, 6, 10),
(22, 28, '2017-11-23 11:57:34', 1, '', 'TRANSFORMADOR LED BLANCA ', 'MARCELO', '', 1, 6, 10),
(23, 29, '2017-11-23 11:57:41', 1, 'JAVIER', 'VIVERO LAS MARGARITAS', 'JAVIER', '', 1, 6, 10),
(24, 30, '2017-11-23 11:57:48', 1, 'GABRIELA', 'BA?O TRANCADO EN PLANTA BAJA', 'WALTER', '', 1, 6, 10),
(25, 22, '2017-11-23 11:57:54', 1, '', 'CAMBIO DE LAMPARAS', 'MARCELO', '', 1, 6, 10),
(26, 27, '2017-11-23 11:58:00', 1, '', 'SE REALIZO MANTENIMIENTO DEL ASCENSOR', 'VIOBAL', '', 1, 6, 10),
(27, 24, '2017-11-23 11:58:17', 1, 'DRA CRESPO', 'CAMBIAR DE LUGAR EL PERCHERO DEL CONSULTORIO', '', '', 1, 7, 10),
(28, 23, '2017-11-23 11:58:24', 1, '', 'CAMBIO DE AUTOMATICO DEL TANQUE (ARRIBA)', 'MARCELO', '', 1, 6, 10),
(29, 25, '2017-11-23 11:58:30', 1, '', 'REPARACION DE DICROICA DE LA FUENTE', 'MARCELO', '', 1, 6, 10),
(30, 26, '2017-11-23 11:58:36', 1, '', 'REPARACION DE BOMBA DE LA FUENTE', 'MARCELO', '', 1, 6, 10),
(31, 19, '2017-11-23 11:58:41', 1, 'ULIVARRI', 'SILLAS PARA BOXES', 'JUAREZ', '', 1, 6, 10),
(32, 20, '2017-11-23 11:58:47', 1, '', 'AIRES SPLIT EN FUNCIONAMIENTO', '', '', 1, 6, 10),
(33, 21, '2017-11-23 11:58:53', 1, '', 'SILLONES REPARAR INCLINADO', '', '', 1, 4, 10),
(34, 18, '2017-11-23 11:58:59', 1, '', 'SE REALIZO MANTENIMIENTO DEL ASCENSOR', 'VIOBAL', '', 1, 6, 10),
(35, 17, '2017-11-23 11:59:06', 1, 'SUAINA LEONARDO', 'REPARACI?N DE ANAFE', 'WALTER', '', 1, 6, 10),
(36, 15, '2017-11-23 11:59:21', 1, 'DR. ULIVARRI', 'REPONER SILLAS DE ACOMPA?ANTES', '', 'ENTREGADAS SILLAS DE HD', 1, 1, 10),
(37, 16, '2017-11-23 11:59:41', 1, 'BLANCA', 'REPARAR ENCHUFES', 'MARCELO', '', 1, 6, 10),
(38, 14, '2017-11-23 11:59:48', 1, 'DRA. CRUZ VARELA', 'CORTOCIRCUITO  ENCHUFES COSTADO DE LAS CAMILLAS', 'MARCELO', '', 1, 8, 10),
(39, 13, '2017-11-23 11:59:57', 1, 'DRA. CRUZ VARELA', 'ARREGLAR CABLEADO DE EQUIPO DE COLPO ', 'ING J. MARTINEZ ', '', 1, 8, 10),
(40, 10, '2017-11-23 12:00:06', 1, 'GABRIELA', 'TRANCADO Y CLAUSURADO', 'AGUAS DEL NORTE', 'RECL. N? 2432050', 1, 1, 10),
(41, 11, '2017-11-23 12:00:14', 1, 'SUAINA JAVIER', 'REPARAR PARED DE LA VENTANA Y PINTAR', 'ARQUITECTA ', '', 1, 4, 10),
(42, 12, '2017-11-23 12:00:20', 1, 'DR. OVEJERO', 'TAPIZADO DE SILLA (TAPICERO)', 'SR ARIAS 4254808 - 3835400696', '', 1, 6, 10),
(43, 9, '2017-11-23 12:00:26', 1, 'ESTELA', 'TRANCADO Y CLAUSURADO', 'WALTER', '', 1, 6, 10),
(44, 8, '2017-11-23 12:00:34', 1, 'GABRIELA', 'DESTRANCAR C?MARAS (A. DEL NORTE) 12:00HS', 'AGUAS DEL NORTE', '', 1, 6, 10),
(45, 6, '2017-11-23 12:00:56', 1, 'SUAINA JAVIER', 'REPARAR MARCO DE LA PUERTA DE MAMO', 'ARQUITECTA', '', 1, 4, 10),
(46, 7, '2017-11-23 12:01:03', 1, 'SUAINA JAVIER', 'LUCES DE EMERGENCIA DE LOS CONSULTORIOS 8-10', 'ARQUITECTA', '', 1, 4, 10),
(47, 5, '2017-11-23 12:01:10', 1, 'GABRIELA', 'DESTRANCAR C?MARAS (A. DEL NORTE) HS 14:00', 'AGUAS DEL NORTE', 'RECL. N? 2425629', 1, 1, 10),
(48, 3, '2017-11-23 12:01:16', 1, 'SUAINA JAVIER', 'ADJUNTAR MUEBLES EN UNA SOLA LINEA', 'DOMINGO DAN', '', 1, 6, 10),
(49, 4, '2017-11-23 12:01:23', 1, 'ESTELA', 'PERDIDA EN EL INODORO', 'WALTER', '', 1, 6, 10),
(50, 2, '2017-11-23 12:01:34', 1, 'DRA. CRUZ VARELA', 'CORTOCIRCUITO  ENCHUFES COSTADO DE LAS CAMILLAS', 'YURKINA', '', 1, 9, 10),
(51, 1, '2017-11-23 12:01:42', 1, 'DRA. CRUZ VARELA', 'CAMBIO DE FOCO', 'JULIA COSTILLA', 'FOCO QUEMADO', 1, 1, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mantenimientos`
--

DROP TABLE IF EXISTS `mantenimientos`;
CREATE TABLE `mantenimientos` (
  `id_mantenimientos` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `id_sectores` int(11) NOT NULL,
  `solicitador` varchar(100) NOT NULL,
  `tarea` varchar(100) NOT NULL,
  `especialista` varchar(100) NOT NULL,
  `observaciones` varchar(200) NOT NULL,
  `estado` tinyint(1) NOT NULL,
  `id_mantenimientos_estados` int(11) NOT NULL,
  `id_usuarios` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `mantenimientos`
--

INSERT INTO `mantenimientos` (`id_mantenimientos`, `fecha`, `id_sectores`, `solicitador`, `tarea`, `especialista`, `observaciones`, `estado`, `id_mantenimientos_estados`, `id_usuarios`) VALUES
(1, '2017-11-23 12:01:42', 1, 'DRA. CRUZ VARELA', 'CAMBIO DE FOCO', 'JULIA COSTILLA', 'FOCO QUEMADO', 1, 1, 10),
(2, '2017-11-23 12:01:34', 1, 'DRA. CRUZ VARELA', 'CORTOCIRCUITO  ENCHUFES COSTADO DE LAS CAMILLAS', 'YURKINA', '', 1, 9, 10),
(3, '2017-11-23 12:01:16', 1, 'SUAINA JAVIER', 'ADJUNTAR MUEBLES EN UNA SOLA LINEA', 'DOMINGO DAN', '', 1, 6, 10),
(4, '2017-11-23 12:01:23', 1, 'ESTELA', 'PERDIDA EN EL INODORO', 'WALTER', '', 1, 6, 10),
(5, '2017-11-23 12:01:10', 1, 'GABRIELA', 'DESTRANCAR C?MARAS (A. DEL NORTE) HS 14:00', 'AGUAS DEL NORTE', 'RECL. N? 2425629', 1, 1, 10),
(6, '2017-11-23 12:00:56', 1, 'SUAINA JAVIER', 'REPARAR MARCO DE LA PUERTA DE MAMO', 'ARQUITECTA', '', 1, 4, 10),
(7, '2017-11-23 12:01:03', 1, 'SUAINA JAVIER', 'LUCES DE EMERGENCIA DE LOS CONSULTORIOS 8-10', 'ARQUITECTA', '', 1, 4, 10),
(8, '2017-11-23 12:00:34', 1, 'GABRIELA', 'DESTRANCAR C?MARAS (A. DEL NORTE) 12:00HS', 'AGUAS DEL NORTE', '', 1, 6, 10),
(9, '2017-11-23 12:00:26', 1, 'ESTELA', 'TRANCADO Y CLAUSURADO', 'WALTER', '', 1, 6, 10),
(10, '2017-11-23 12:00:06', 1, 'GABRIELA', 'TRANCADO Y CLAUSURADO', 'AGUAS DEL NORTE', 'RECL. N? 2432050', 1, 1, 10),
(11, '2017-11-23 12:00:14', 1, 'SUAINA JAVIER', 'REPARAR PARED DE LA VENTANA Y PINTAR', 'ARQUITECTA ', '', 1, 4, 10),
(12, '2017-11-23 12:00:20', 1, 'DR. OVEJERO', 'TAPIZADO DE SILLA (TAPICERO)', 'SR ARIAS 4254808 - 3835400696', '', 1, 6, 10),
(13, '2017-11-23 11:59:57', 1, 'DRA. CRUZ VARELA', 'ARREGLAR CABLEADO DE EQUIPO DE COLPO ', 'ING J. MARTINEZ ', '', 1, 8, 10),
(14, '2017-11-23 11:59:48', 1, 'DRA. CRUZ VARELA', 'CORTOCIRCUITO  ENCHUFES COSTADO DE LAS CAMILLAS', 'MARCELO', '', 1, 8, 10),
(15, '2017-11-23 11:59:21', 1, 'DR. ULIVARRI', 'REPONER SILLAS DE ACOMPA?ANTES', '', 'ENTREGADAS SILLAS DE HD', 1, 1, 10),
(16, '2017-11-23 11:59:41', 1, 'BLANCA', 'REPARAR ENCHUFES', 'MARCELO', '', 1, 6, 10),
(17, '2017-11-23 11:59:06', 1, 'SUAINA LEONARDO', 'REPARACI?N DE ANAFE', 'WALTER', '', 1, 6, 10),
(18, '2017-11-23 11:58:59', 1, '', 'SE REALIZO MANTENIMIENTO DEL ASCENSOR', 'VIOBAL', '', 1, 6, 10),
(19, '2017-11-23 11:58:41', 1, 'ULIVARRI', 'SILLAS PARA BOXES', 'JUAREZ', '', 1, 6, 10),
(20, '2017-11-23 11:58:47', 1, '', 'AIRES SPLIT EN FUNCIONAMIENTO', '', '', 1, 6, 10),
(21, '2017-11-23 11:58:53', 1, '', 'SILLONES REPARAR INCLINADO', '', '', 1, 4, 10),
(22, '2017-11-23 11:57:54', 1, '', 'CAMBIO DE LAMPARAS', 'MARCELO', '', 1, 6, 10),
(23, '2017-11-23 11:58:24', 1, '', 'CAMBIO DE AUTOMATICO DEL TANQUE (ARRIBA)', 'MARCELO', '', 1, 6, 10),
(24, '2017-11-23 11:58:17', 1, 'DRA CRESPO', 'CAMBIAR DE LUGAR EL PERCHERO DEL CONSULTORIO', '', '', 1, 7, 10),
(25, '2017-11-23 11:58:30', 1, '', 'REPARACION DE DICROICA DE LA FUENTE', 'MARCELO', '', 1, 6, 10),
(26, '2017-11-23 11:58:36', 1, '', 'REPARACION DE BOMBA DE LA FUENTE', 'MARCELO', '', 1, 6, 10),
(27, '2017-11-23 11:58:00', 1, '', 'SE REALIZO MANTENIMIENTO DEL ASCENSOR', 'VIOBAL', '', 1, 6, 10),
(28, '2017-11-23 11:57:34', 1, '', 'TRANSFORMADOR LED BLANCA ', 'MARCELO', '', 1, 6, 10),
(29, '2017-11-23 11:57:41', 1, 'JAVIER', 'VIVERO LAS MARGARITAS', 'JAVIER', '', 1, 6, 10),
(30, '2017-11-23 11:57:48', 1, 'GABRIELA', 'BA?O TRANCADO EN PLANTA BAJA', 'WALTER', '', 1, 6, 10),
(31, '2017-11-23 11:57:26', 1, '', 'CONTROL REMOTO DR HJEALA', 'DR. HJEALA', '', 1, 6, 10),
(32, '2017-11-23 11:57:20', 1, 'DR TABOADA', 'ARREGLO DE CERRADURA ', 'MOISES', '', 1, 6, 10),
(33, '2017-11-23 11:57:11', 1, 'DRA. SEGURA', 'AIRE ACONDIC', 'JOSE ARNEDO', '', 1, 6, 10),
(34, '2017-11-23 11:56:57', 1, '', 'CAMBIO DE LUCES RX', 'MARCELO', '', 1, 4, 10),
(35, '2017-11-23 11:57:02', 1, '', 'BOMBA DE FUENTE', 'MARCELO154530670', '', 1, 4, 10),
(36, '2017-11-23 11:56:50', 1, '', 'BA?O PUBLICO AL FRENTE DEL LABORATORIO', 'WALTER154474084', '', 1, 6, 10),
(37, '2017-11-23 11:56:44', 1, 'DR. LEONARDO SUAINA', 'BACHA TRANCADA', 'WALTER154474084', '', 1, 6, 10),
(38, '2017-11-23 11:56:36', 1, '', 'PUERTA VIDRIO SIN FRENO', 'CASEROS VIDRIOS', '', 1, 6, 10),
(39, '2017-11-23 11:56:23', 1, 'JAVIER', 'REUBICACION DE LAS PLANTAS', 'JAVIER', '', 1, 6, 10),
(40, '2017-11-23 11:56:30', 1, 'JAVIER', 'LUCES DE EMERGENCIA DE LOS CONSULTORIOS 8-10', '', '', 1, 4, 10),
(41, '2017-11-23 11:56:16', 1, 'SECRETARIAS', 'SILLA DE RECPCION', 'JUAREZ', '', 1, 6, 10),
(42, '2017-11-23 11:56:01', 1, 'DR.LEO', 'ARMAR MESADA PLEGABLE DONDE ESTA EL ANAFE', 'DAN', '', 1, 4, 10),
(43, '2017-11-23 11:56:08', 1, 'J SUAINA', 'CAMBIAR DE LUGAR PLANTA (FUENTE)', 'JAVIER JARDINERO', '', 1, 6, 10),
(44, '2017-11-23 11:55:55', 1, 'AD', 'PUERTA DE INGRESO AJUSTAR', 'CASEROS VIDRIOS 431-9369', '', 1, 4, 10),
(45, '2017-11-23 11:55:27', 1, 'RUIZ ALVAREZ', 'CAMBIAR EL PICAPORTE', 'MOISES CABRERA', '', 1, 6, 10),
(46, '2017-11-23 11:55:34', 1, 'DR TABOADA / ALVERO', 'AIRE ACONDIC', 'JOSE ARNEDO', '', 1, 6, 10),
(47, '2017-11-23 11:54:45', 1, 'DR CARLOS GIL', 'RETOQUE DE PINTURA Y TECHO', 'ARQUITECTA', '', 1, 4, 10),
(48, '2017-11-23 11:54:59', 1, 'DR. ABRAHAM RAFAEL', 'AIRE ACONDIC', 'JOSE ARNEDO', '', 1, 6, 10),
(49, '2017-11-23 11:55:17', 1, 'KAREN', 'PICAPORTE Y CERRADURA', 'MOISES CABRERA', '', 1, 4, 10),
(50, '2017-11-23 11:53:55', 1, 'DRA PORTUGAL', 'AIRE ACOND. TIRA OLOR ', 'ARNEDO', '', 1, 4, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mantenimientos_estados`
--

DROP TABLE IF EXISTS `mantenimientos_estados`;
CREATE TABLE `mantenimientos_estados` (
  `id_mantenimientos_estados` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `mantenimientos_estados`
--

INSERT INTO `mantenimientos_estados` (`id_mantenimientos_estados`, `nombre`) VALUES
(1, 'Solicitado'),
(2, 'Vino'),
(3, 'No vino'),
(4, 'En proceso'),
(5, 'No arreglado'),
(6, 'Solucionado'),
(7, 'Pendiente'),
(8, 'Descartado'),
(9, 'Cancelado');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `mantenimhistoricos`
--
ALTER TABLE `mantenimhistoricos`
  ADD PRIMARY KEY (`id_mantenimhistoricos`);

--
-- Indices de la tabla `mantenimientos`
--
ALTER TABLE `mantenimientos`
  ADD PRIMARY KEY (`id_mantenimientos`);

--
-- Indices de la tabla `mantenimientos_estados`
--
ALTER TABLE `mantenimientos_estados`
  ADD PRIMARY KEY (`id_mantenimientos_estados`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `mantenimhistoricos`
--
ALTER TABLE `mantenimhistoricos`
  MODIFY `id_mantenimhistoricos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;
--
-- AUTO_INCREMENT de la tabla `mantenimientos`
--
ALTER TABLE `mantenimientos`
  MODIFY `id_mantenimientos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;
--
-- AUTO_INCREMENT de la tabla `mantenimientos_estados`
--
ALTER TABLE `mantenimientos_estados`
  MODIFY `id_mantenimientos_estados` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;