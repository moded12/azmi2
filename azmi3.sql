-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 29, 2025 at 03:19 PM
-- Server version: 10.5.27-MariaDB
-- PHP Version: 8.4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `azmi3`
--

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

CREATE TABLE `attachments` (
  `id` int(11) NOT NULL,
  `thread_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `name`) VALUES
(1, 'الصف الأول'),
(2, 'الصف الثاني'),
(3, 'الصف الثالث'),
(4, 'الصف الرابع'),
(5, 'الصف الخامس'),
(6, 'الصف السادس'),
(7, 'الصف السابع'),
(8, 'الصف الثامن'),
(9, 'الصف التاسع'),
(10, 'الصف العاشر'),
(11, 'الصف الحادي عشر'),
(12, 'الصف الثاني عشر'),
(13, 'السياحة في عمان'),
(14, 'الجامعات في عمان');

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `semester_id`, `name`) VALUES
(12, 42, '4444'),
(13, 79, 'وحدة 1 تربية صف 2 ف1'),
(14, 85, 'سياحة');

-- --------------------------------------------------------

--
-- Table structure for table `materials`
--

CREATE TABLE `materials` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `materials`
--

INSERT INTO `materials` (`id`, `class_id`, `name`) VALUES
(27, 1, 'لغة عربية'),
(28, 1, 'تربية إسلامية'),
(29, 1, 'لغة انجليزية'),
(30, 1, 'علـــــوم'),
(31, 1, 'رياضيات'),
(32, 1, 'د. اجتماعية'),
(33, 1, 'مهارات حياتية'),
(34, 1, 'تقنية المعلومات'),
(35, 1, 'الهوية والمواطنة'),
(36, 1, 'التربية البدنية والصحية'),
(37, 1, 'الفنون البصرية'),
(38, 1, 'الفنون الموسيقية'),
(39, 1, 'xxxx'),
(40, 2, 'تربية إسلامية'),
(41, 2, 'لغة انجليزية'),
(42, 2, 'علـــــوم'),
(43, 2, 'رياضيات'),
(44, 2, 'د. اجتماعية'),
(45, 2, 'مهارات حياتية'),
(46, 2, 'تقنية المعلومات'),
(47, 2, 'الهوية والمواطنة'),
(48, 2, 'التربية البدنية والصحية'),
(49, 2, 'الفنون البصرية'),
(50, 2, 'الفنون الموسيقية'),
(51, 13, 'الأماكن السياحية');

--
-- Triggers `materials`
--
DELIMITER $$
CREATE TRIGGER `add_semesters_after_material_insert` AFTER INSERT ON `materials` FOR EACH ROW BEGIN
  INSERT INTO semesters (name, material_id) VALUES ('الفصل الأول', NEW.id), ('الفصل الثاني', NEW.id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `semesters`
--

CREATE TABLE `semesters` (
  `id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `semesters`
--

INSERT INTO `semesters` (`id`, `material_id`, `name`) VALUES
(29, 39, 'الفصل الأول'),
(30, 39, 'الفصل الثاني'),
(33, 27, 'الفصل الأول'),
(34, 28, 'الفصل الأول'),
(35, 29, 'الفصل الأول'),
(36, 30, 'الفصل الأول'),
(37, 31, 'الفصل الأول'),
(38, 32, 'الفصل الأول'),
(39, 33, 'الفصل الأول'),
(40, 34, 'الفصل الأول'),
(41, 35, 'الفصل الأول'),
(42, 36, 'الفصل الأول'),
(43, 37, 'الفصل الأول'),
(44, 38, 'الفصل الأول'),
(48, 27, 'الفصل الثاني'),
(49, 28, 'الفصل الثاني'),
(50, 29, 'الفصل الثاني'),
(51, 30, 'الفصل الثاني'),
(52, 31, 'الفصل الثاني'),
(53, 32, 'الفصل الثاني'),
(54, 33, 'الفصل الثاني'),
(55, 34, 'الفصل الثاني'),
(56, 35, 'الفصل الثاني'),
(57, 36, 'الفصل الثاني'),
(58, 37, 'الفصل الثاني'),
(59, 38, 'الفصل الثاني'),
(63, 40, 'الفصل الأول'),
(64, 40, 'الفصل الثاني'),
(65, 41, 'الفصل الأول'),
(66, 41, 'الفصل الثاني'),
(67, 42, 'الفصل الأول'),
(68, 42, 'الفصل الثاني'),
(69, 43, 'الفصل الأول'),
(70, 43, 'الفصل الثاني'),
(71, 44, 'الفصل الأول'),
(72, 44, 'الفصل الثاني'),
(73, 45, 'الفصل الأول'),
(74, 45, 'الفصل الثاني'),
(75, 46, 'الفصل الأول'),
(76, 46, 'الفصل الثاني'),
(77, 47, 'الفصل الأول'),
(78, 47, 'الفصل الثاني'),
(79, 48, 'الفصل الأول'),
(80, 48, 'الفصل الثاني'),
(81, 49, 'الفصل الأول'),
(82, 49, 'الفصل الثاني'),
(83, 50, 'الفصل الأول'),
(84, 50, 'الفصل الثاني'),
(85, 51, 'الفصل الأول'),
(86, 51, 'الفصل الثاني'),
(87, 51, 'الفصل الأول'),
(88, 51, 'الفصل الثاني');

-- --------------------------------------------------------

--
-- Table structure for table `threads`
--

CREATE TABLE `threads` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `content_type` varchar(20) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `threads`
--

INSERT INTO `threads` (`id`, `group_id`, `title`, `description`, `thumbnail`, `created_at`, `content_type`, `file_path`) VALUES
(18, 12, '4444', '444444', NULL, '2025-06-29 12:41:59', 'pdf', NULL),
(19, 12, '5555', '555', NULL, '2025-06-29 12:51:44', 'url', NULL),
(20, 13, 'تربية حلول', 'كتاب الطالب', NULL, '2025-06-29 14:11:38', 'url', NULL),
(21, 14, 'السياحة', 'سياحة', NULL, '2025-06-29 14:32:32', 'image', NULL),
(22, 14, 'جبل الأخضر', 'يُعد جبل الأخضر واحدًا من أعلى قمم جبال الحجر في عُمان...', NULL, '2025-06-29 14:36:50', NULL, NULL),
(23, 14, 'وادي شاب', 'يُعتبر وادي شاب من أجمل الأوْدِيَة العمانية بطبيعته الخلابة...', NULL, '2025-06-29 14:36:50', NULL, NULL),
(24, 14, 'وادي بني خالد', 'يشتهر وادي بني خالد ببركه الكبيرة المحاطة...', NULL, '2025-06-29 14:36:50', NULL, NULL),
(25, 14, 'وادي دربات', 'يقع وادي دربات في محافظة ظفار، ويتميز بشلالاته المتتالية...', NULL, '2025-06-29 14:36:50', NULL, NULL),
(26, 14, 'قلعة نزوى', 'تُعد قلعة نزوى واحدة من أشهر القلاع التاريخية في عُمان...', NULL, '2025-06-29 14:36:50', NULL, NULL),
(27, 14, 'سوق نزوى', 'يقام سوق نزوى التقليدي كل جمعة ويشتهر ببيع المنتجات المحلية...', NULL, '2025-06-29 14:36:50', NULL, NULL),
(28, 14, 'قلعة بهلاء', 'أدرجت قلعة بهلاء ضمن قائمة التراث العالمي لليونسكو...', NULL, '2025-06-29 14:36:50', NULL, NULL),
(29, 14, 'صلالة خلال موسم الخريف', 'تشتهر محافظة ظفار بمناظرها الخضراء خلال موسم الخريف...', NULL, '2025-06-29 14:36:50', NULL, NULL),
(30, 14, 'محمية رأس الجنز للسلحفاة', 'تُعد محمية رأس الجنز من أهم مواقع تعشيش السلاحف...', NULL, '2025-06-29 14:36:50', NULL, NULL),
(31, 14, 'مسقط القديمة وسوق مطرح', 'تمثل مسقط القديمة قلب العاصمة التاريخي…', NULL, '2025-06-29 14:36:50', NULL, NULL),
(32, 14, 'رمال الربع الخالي ?️', 'تقع صحراء الربع الخالي في جنوب شرق عُمان وتمتد على مساحة شاسعة من الرمال الذهبية.\r\nتُعرف بالكثبان الرملية الضخمة التي ترتفع أحيانًا لأكثر من 100 متر.\r\nتوفر تجربة التخييم في الصحراء مشاهدة سماوية نقية مليئة بالنجوم.\r\nيمكن تنظيم رحلات السفاري بسيارات الدفع الرباعي لاستكشاف المناطق المنعزلة.\r\nينصح بزيارة الصحراء في أشهر الخريف للتمتع بطقس معتدل.', 'https://www.shneler.com/azmi3/admin/uploads/siahah/10.jpg', '2025-06-29 14:40:51', NULL, NULL),
(33, 14, 'وادي تيبي ?️', 'يعد وادي تيبي من أجمل أودية عُمان بطبيعته الخضراء وطبيعة الصخور الملونة.\r\nيمتد الوادي بين القرى الجبلية ويُتيح مسارات مشي آمنة وسط أشجار النخيل.\r\nتتجمع البرك الطبيعية بين الصخور لتوفر مواقع سباحة منعشة.\r\nيمكنك الاستمتاع بنزهة عائلية على ضفاف الجدول الصافي.\r\nينصح بارتداء أحذية مائيّة لتتمكن من عبور الجداول بسهولة.', 'https://www.shneler.com/azmi3/admin/uploads/siahah/9.jpg', '2025-06-29 14:40:51', NULL, NULL),
(34, 14, 'حفرة بِمَّة ?', 'تقع حفرة بِمَّة بالقرب من شاطئ فَنَاء في محافظة مسقط.\r\nتتميز بجدران صخرية تغمرها مياه عذبة صافية يصل عمقها إلى أكثر من 20 مترًا.\r\nتُعد موقعًا مثاليًا للسباحة والغوص السطحي وسط الماء الفيروزي.\r\nتُحيط بها أشجار النخيل والحشائش الشاطئية، ما يمنحها جمالًا خاصًا.\r\nتوفر المنطقة مظلات طبيعية يمكن الجلوس تحتها والاستمتاع بالمنظر.', 'https://www.shneler.com/azmi3/admin/uploads/siahah/8.jpg', '2025-06-29 14:40:51', NULL, NULL),
(35, 14, 'مغارة الهوتة ?', 'تُعدّ مغارة الهوتة واحدة من أطول كهوف عُمان بطول يتجاوز 3 كيلومترات.\r\nتضم ممرات صخرية وتشكيلات جيرية رائعة تكوّنت عبر آلاف السنين.\r\nيُخصّص ممر داخلي آمن للزوار مع إضاءة خافتة تُبرز الطبيعة الجيولوجية.\r\nيمكنك التجول داخل جزء منها مصحوبًا بمرشدين مختصين.\r\nتقع المغارة بجوار بحيرة صغيرة تُنتج أسماكًا سليمة للطعام.', 'https://www.shneler.com/azmi3/admin/uploads/siahah/7.jpg', '2025-06-29 14:40:51', NULL, NULL),
(36, 14, 'قرية مسفاة العبريين ?', 'تقع قرية مسفاة العبريين على سفوح جبال الحجر الغربي بالقرب من نخل.\r\nتشتهر بمنازلها الطينية القديمة والطرق المرصوفة بالحجارة التقليدية.\r\nيمكن المشي عبر الأزقة الضيقة والتمتع بمشاهدة مشتلّات النخيل والحدائق.\r\nتوفر القرية إطلالات بانورامية على الوديان العميقة المحيطة.\r\nتتوفّر فيها بيوت ضيافة صغيرة لتجربة الحياة الريفية الأصيلة.', 'https://www.shneler.com/azmi3/admin/uploads/siahah/6.jpg', '2025-06-29 14:40:51', NULL, NULL),
(37, 14, 'مدينة صور العتيقة ?️', 'تشتهر مدينة صور بتاريخها البحري العريق وورش بناء السفن الخشبية (الدوارق).\r\nتضم ميناءً قديمًا ما زال يعمل ويقدّم تجربة مشاهدة قوارب الصيد التقليدية.\r\nيمكن التجول في سوق السمك التقليدي وشراء أجود أنواع الأسماك الطازجة.\r\nتزور القاهرة القديمة (القلعة) المطلة على الخليج العربي.\r\nينصح برحلة بحرية قصيرة لمشاهدة الدلافين ومواقع السلاحف البحرية.', 'https://www.shneler.com/azmi3/admin/uploads/siahah/5.jpg', '2025-06-29 14:40:51', NULL, NULL),
(38, 14, 'شاطئ الخوير ?', 'يمتاز شاطئ الخوير برماله الناعمة ومياهه الفيروزية الصافية.\r\nيُحيط به غابات المانجروف التي تضفي عليه جمالًا طبيعيًا وتنوعًا بيئيًا.\r\nيمكنك ركوب الزوارق الصغيرة أو التجديف في القنوات المائية الهادئة.\r\nيوفر الشاطئ مناطق مخصصة للتخييم والشواء العائلي.\r\nينصح بزيارة الموقع في الصباح الباكر للاستمتاع بهدوء المكان.', 'https://www.shneler.com/azmi3/admin/uploads/siahah/4.jpg', '2025-06-29 14:40:51', NULL, NULL),
(39, 14, 'مضيق مسندم ⛴️', 'يشتهر مضيق مسندم بمناظره الخلابة من خليجاته العميقة وجزرها الصغيرة.\r\nيمكنك الانطلاق في رحلة بحرية بالقارب لرؤية المروج المرجانية والحياة البحرية.\r\nتشاهد في الطريق قوافل الجبال المموجة التي تنحدر نحو مياه الخليج.\r\nتُعرف المنطقة أيضًا باسم “النرويج العربية” لجمالها البحري.\r\nتتوفر رحلات غوص بسيط لمشاهدة الشعاب المرجانية الملونة.', 'https://www.shneler.com/azmi3/admin/uploads/siahah/3.jpg', '2025-06-29 14:40:51', NULL, NULL),
(40, 14, 'قلعة الرستاق ?', 'تقع قلعة الرستاق في قلب ولاية الرستاق التاريخية بمحافظة جنوب الباطنة.\r\nبُنيت في القرن الرابع عشر وتتميز بأبراجها العالية وجدرانها السميكة.\r\nتضم داخلها متحفًا صغيرًا يعرض أدوات الحرب القديمة والزي التقليدي.\r\nيمكنك الصعود إلى أحد الأبراج لمشاهدة منظر المدينة والواحات المجاورة.\r\nتنظم في الموقع فعاليات ثقافية وتراثية تعرض الحرف العُمانية الأصيلة.', 'https://www.shneler.com/azmi3/admin/uploads/siahah/2.jpg', '2025-06-29 14:40:51', NULL, NULL),
(41, 14, 'الجزر الديمانيات ?️', 'تتكوّن محمية الجزر الديمانيات من تسع جزر صغيرة قبالة ساحل مسقط.\r\nتُعرف بمياهها النقية وشعابها المرجانية وحياة الأسماك الاستوائية.\r\nيمكنك الغوص السطحي ومراقبة السلاحف البحرية وأسماك القرش الصغيرة.\r\nتوفر المنطقة مواقع جميلة للتخييم البحري ومراقبة الطيور المهاجرة.\r\nتُدار المحمية بإشراف وزارة البيئة وتخضع لرقابة صارمة للحفاظ على التنوع البيولوجي.', 'https://www.shneler.com/azmi3/admin/uploads/siahah/1.jpg', '2025-06-29 14:40:51', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `thread_files`
--

CREATE TABLE `thread_files` (
  `id` int(11) NOT NULL,
  `thread_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `thread_files`
--

INSERT INTO `thread_files` (`id`, `thread_id`, `file_path`) VALUES
(9, 18, 'uploads/68611877c4744_01.jpg'),
(10, 18, 'uploads/68611877c6cd9_02.jpg'),
(11, 19, 'https://oman99.com/w/1/ar1/f1/t1/'),
(12, 20, 'https://oman99.com/s/2/BD2/BD2F1.htm');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `thread_id` (`thread_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `semester_id` (`semester_id`);

--
-- Indexes for table `materials`
--
ALTER TABLE `materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `semesters`
--
ALTER TABLE `semesters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `material_id` (`material_id`);

--
-- Indexes for table `threads`
--
ALTER TABLE `threads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `thread_files`
--
ALTER TABLE `thread_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `thread_id` (`thread_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `materials`
--
ALTER TABLE `materials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `semesters`
--
ALTER TABLE `semesters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `threads`
--
ALTER TABLE `threads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `thread_files`
--
ALTER TABLE `thread_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attachments`
--
ALTER TABLE `attachments`
  ADD CONSTRAINT `attachments_ibfk_1` FOREIGN KEY (`thread_id`) REFERENCES `threads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `materials`
--
ALTER TABLE `materials`
  ADD CONSTRAINT `materials_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `semesters`
--
ALTER TABLE `semesters`
  ADD CONSTRAINT `semesters_ibfk_1` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `threads`
--
ALTER TABLE `threads`
  ADD CONSTRAINT `threads_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `thread_files`
--
ALTER TABLE `thread_files`
  ADD CONSTRAINT `thread_files_ibfk_1` FOREIGN KEY (`thread_id`) REFERENCES `threads` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
