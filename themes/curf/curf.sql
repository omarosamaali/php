CREATE TABLE `0_curf_options` (
  `id` int(11) NOT NULL,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `type_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `0_curf_options`
--

INSERT INTO `0_curf_options` (`id`, `name`, `type`, `type_name`, `value`) VALUES
(1, 'color_scheme', 'theme', 'curf', '#2b7a77'),
(2, 'theme_mode', 'theme', 'curf', 'light'),
(3, 'font_api_link', 'theme', 'curf', ''),
(4, 'font_api_css', 'theme', 'curf', ''),
(5, 'site_name', 'theme', 'curf', ''),
(6, 'site_url', 'theme', 'curf', ''),
(7, 'footer_hide', 'theme', 'curf', '0'),
(8, 'footer_hide_version', 'theme', 'curf', '0'),
(9, 'footer_date_time', 'theme', 'curf', '0'),
(10, 'footer_hide_servername', 'theme', 'curf', '0'),
(11, 'footer_hide_companyname', 'theme', 'curf', '0'),
(12, 'footer_hide_themename', 'theme', 'curf', '0'),
(13, 'footer_hide_username', 'theme', 'curf', '0');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `0_curf_options`
--
ALTER TABLE `0_curf_options`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `0_curf_options`
--
ALTER TABLE `0_curf_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
