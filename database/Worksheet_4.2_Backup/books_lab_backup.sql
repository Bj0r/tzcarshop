-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 27, 2026 at 12:20 AM
-- Server version: 8.0.46-0ubuntu0.24.04.4
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `book`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `action` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Book',
  `entity_id` bigint UNSIGNED DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `http_method` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_id` int NOT NULL,
  `stock` int NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `description`, `country_id`, `stock`, `amount`, `photo`, `created_at`, `updated_at`) VALUES
(1, 'Quisquam dolorem temporibus est neque modi alias.', 'Eveniet nihil odit corporis laudantium omnis a. Ut illum eum sunt est est et. Ut vel non maxime laboriosam est necessitatibus.', 88, 548, 6635.98, 'seed-book-1.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(2, 'Est incidunt rem vero.', 'In consequatur et dolorum cum perspiciatis error. Assumenda quis blanditiis qui totam in dolorem enim dolor. Distinctio harum occaecati laudantium ut.', 99, 913, 8221.33, 'seed-book-2.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(3, 'Consequatur eius veniam eveniet quibusdam sed laboriosam.', 'Et ipsum aut repudiandae dignissimos labore. Exercitationem consequatur ea quo sint. Officiis delectus hic voluptatibus harum et consequatur non. Nostrum aliquid magni enim rerum dolor.', 17, 366, 5202.12, 'seed-book-3.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(4, 'Aut rem dolores delectus neque.', 'Earum beatae ut possimus. Laboriosam dolor id omnis. Sint odit laborum qui consequatur hic assumenda maiores.', 33, 133, 4759.66, 'seed-book-4.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(5, 'Delectus rerum sequi repellat eligendi.', 'Qui reiciendis omnis voluptatum neque non placeat. Aliquid odit ab sapiente est non labore. Molestias sit voluptates omnis sit quis quos est.', 36, 660, 1410.78, 'seed-book-5.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(6, 'Aut animi voluptatum nihil recusandae voluptates.', 'Corrupti sunt aliquam ad exercitationem consectetur autem aliquid ipsam. Officia hic quibusdam enim ut ut est. At aliquid minus praesentium sit quia.', 25, 631, 4890.59, 'seed-book-6.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(7, 'Et quis possimus nihil sint.', 'Unde vero sint nesciunt consequatur. Perspiciatis inventore iure architecto deleniti reiciendis. Deserunt est est qui sed. Magnam neque tempora est modi.', 49, 200, 1424.22, 'seed-book-7.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(8, 'Tempora porro voluptas dignissimos delectus voluptatem dolor aperiam.', 'Modi quidem et voluptas pariatur sit. Cupiditate molestiae odit unde dolorum dolor sed facere. Recusandae pariatur voluptatibus rerum totam est.', 98, 223, 6189.29, 'seed-book-8.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(9, 'Sed accusantium earum voluptas quo.', 'At qui qui amet aperiam quas omnis officia atque. Quasi sequi dolorem non reiciendis. Veritatis deserunt unde aut quos asperiores vel rerum laboriosam.', 27, 178, 6683.64, 'seed-book-9.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(10, 'Aut hic ipsam dolorem eos est labore.', 'Ea ipsa explicabo vel sit officia eaque. Voluptatum repudiandae inventore odio neque voluptate eius ea. Illo vel ipsa quod autem excepturi recusandae aliquid accusantium.', 96, 309, 6131.86, 'seed-book-10.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(11, 'Occaecati quam dolor ut ratione aut molestiae.', 'Suscipit eos ex molestias est et fuga aperiam. Quo optio autem itaque aliquam veritatis nemo.', 8, 303, 9068.78, 'seed-book-11.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(12, 'Sunt aut aperiam neque voluptatem earum in ipsum.', 'Unde placeat dolor voluptas fugiat et illum dignissimos dignissimos. Aut vero voluptate natus sunt. Id adipisci magnam sunt aliquam laboriosam incidunt.', 17, 100, 5018.57, 'seed-book-12.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(13, 'Quod consequatur aut odio beatae.', 'Non itaque esse illo nam qui sed quia. Ratione beatae nesciunt dolor veniam. Velit enim velit nobis dolore laborum. Minus et error quia saepe est quos corporis.', 46, 345, 839.27, 'seed-book-13.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(14, 'Eos porro fugit incidunt alias.', 'Quod omnis atque qui sit omnis dolorem. Sunt dolores at neque sit sequi quisquam. Deleniti dolorem aliquid omnis odit optio voluptas. Voluptatum dignissimos vel iste iure.', 53, 938, 3831.96, 'seed-book-14.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(15, 'Id est vero distinctio et quo.', 'Rerum minima et qui sint nam in et. Inventore officia et neque et voluptatem nulla aut enim.', 7, 767, 3258.43, 'seed-book-15.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(16, 'Neque nisi enim est harum alias.', 'Aut fugit quia qui animi aperiam. Dolores quis repudiandae natus aperiam enim est. Corrupti sint maxime dignissimos nostrum adipisci. Impedit nobis occaecati sit.', 51, 424, 56.33, 'seed-book-16.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(17, 'Quos enim sapiente occaecati facere non aut vitae.', 'Inventore vitae recusandae magnam voluptatem iure. In esse deleniti eos quibusdam qui quae.', 85, 869, 7053.00, 'seed-book-17.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(18, 'Nostrum et delectus voluptatum perspiciatis sed aut.', 'Suscipit id hic consequuntur et eum ex et. Voluptas aperiam modi vero ratione voluptates nesciunt molestiae. Inventore molestiae dignissimos cumque vel et sapiente alias.', 99, 984, 7666.29, 'seed-book-18.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(19, 'Earum incidunt nemo deleniti omnis consectetur.', 'Dolores distinctio nihil fugit eligendi quia esse qui. Quas placeat sunt vero ipsa. Et rerum autem culpa ipsam et et qui optio. Aut odit vitae cumque facere ipsam provident.', 3, 759, 201.89, 'seed-book-19.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(20, 'Necessitatibus maiores recusandae voluptatem aliquam facere.', 'Sint placeat vitae suscipit nesciunt. Consequatur asperiores ea non ut nihil nesciunt. Ab voluptatem expedita aut distinctio eum.', 1, 542, 3077.28, 'seed-book-20.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(21, 'Cum commodi quod ipsam ipsum fugiat consequatur non.', 'Dolore pariatur alias voluptatem quo consequatur. Assumenda quaerat similique est. Eos totam est occaecati quod aut.', 87, 726, 8581.01, 'seed-book-21.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(22, 'Fuga asperiores aut voluptatem maiores voluptas.', 'Qui sit qui veritatis molestiae explicabo animi dolorum. Et sit consectetur quam eos. Laudantium occaecati aut ex magni pariatur animi corporis.', 68, 845, 4984.39, 'seed-book-22.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(23, 'Rerum qui saepe dicta expedita nihil autem est quia.', 'Nam culpa dignissimos aut quam ut. Eum et illum incidunt quibusdam et est quisquam. Quaerat totam quod unde reprehenderit at ab corporis.', 49, 257, 8290.84, 'seed-book-23.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(24, 'Et praesentium architecto accusantium sed et qui.', 'Quos odit magnam omnis vero incidunt. Dolores non occaecati sed vitae ipsam. Occaecati veritatis harum perferendis quaerat. Et quam dolor dolor molestiae esse eum quo.', 34, 115, 1376.17, 'seed-book-24.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(25, 'Id possimus nulla sint similique accusantium.', 'Et sunt est laborum suscipit. Quia quae molestiae quia quas. Totam velit voluptatem laboriosam pariatur ipsam occaecati.', 63, 190, 1636.22, 'seed-book-25.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(26, 'Exercitationem officiis illum est et non quasi.', 'Dolor et modi minima perferendis quo odit. Assumenda aperiam soluta occaecati dolores. Dolores voluptatibus eos quis qui occaecati.', 73, 217, 982.19, 'seed-book-26.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(27, 'Tenetur sint enim quia aliquam et.', 'Eaque et fugiat tempore sit. Voluptate rerum ut occaecati quas qui. Quos sapiente alias ea vel sit laborum inventore. Et iste numquam rem quam accusantium omnis.', 11, 118, 4539.00, 'seed-book-27.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(28, 'Et et quibusdam incidunt.', 'Esse nihil cumque fuga error et culpa vitae. Animi enim vel nostrum recusandae. Nobis neque perspiciatis beatae libero sed est.', 89, 935, 6501.09, 'seed-book-28.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(29, 'Sunt officiis ipsa nostrum culpa quisquam placeat nesciunt.', 'Aliquid nesciunt placeat qui repellendus omnis. Nihil nulla est in. Necessitatibus et labore ipsum enim deleniti. Et doloremque nam pariatur quam quam saepe earum.', 38, 585, 320.84, 'seed-book-29.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(30, 'Ad facilis accusamus enim dolores non.', 'Aut dolorem ducimus ut dolores reiciendis reiciendis quam. Veritatis libero deserunt sit odio possimus. Expedita ut et repellendus saepe quis fugit quae. Magnam in est nobis voluptatem.', 52, 860, 1078.82, 'seed-book-30.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(31, 'Nulla voluptatem aut ad ea alias est.', 'Debitis at vitae consectetur nemo aut. Exercitationem placeat est et id repudiandae cum. Temporibus qui veritatis vero omnis repellendus. Corrupti accusamus inventore voluptates.', 28, 3, 2855.75, 'seed-book-31.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(32, 'Praesentium est quas magni voluptatum eveniet saepe.', 'Debitis dolorem magnam et eveniet dolor in. Quam ut consectetur fugiat incidunt dolor. Dolor reiciendis culpa fugit omnis. Aut facilis hic maiores repellat vel itaque eius.', 10, 350, 6334.45, 'seed-book-32.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(33, 'Quo porro deserunt laudantium voluptates.', 'Laboriosam officiis minus sequi. Praesentium omnis repudiandae illum atque sed qui temporibus. Cum tempora consequuntur ducimus voluptatem.', 18, 947, 6409.10, 'seed-book-33.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(34, 'In a quia odio voluptatem vel voluptatem.', 'Molestiae ipsam quisquam reprehenderit quo quo recusandae blanditiis. Veniam quo enim vitae sed. Praesentium voluptatibus ea sit perspiciatis libero.', 14, 932, 4290.14, 'seed-book-34.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(35, 'Maiores distinctio molestiae id non a.', 'Accusantium neque laborum consequatur tempore adipisci quisquam. Aut inventore expedita laudantium repellendus.', 99, 67, 591.93, 'seed-book-35.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(36, 'Libero voluptas accusantium sed tempore asperiores autem quis.', 'Quos nam ut rerum. Vel ut aut sint omnis sed officiis. Beatae quas non omnis facilis.', 14, 896, 342.29, 'seed-book-36.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(37, 'Sapiente laudantium quis incidunt quidem et quae.', 'Et ad id ut. Eum sit aliquid qui molestiae asperiores. Dicta quo quia debitis voluptas. Qui animi culpa dolore non iure laudantium nam possimus.', 33, 927, 4132.59, 'seed-book-37.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(38, 'Sed eos sed cum.', 'Voluptas velit officiis quis officia. Rerum est voluptas odit et ab.', 59, 565, 7050.10, 'seed-book-38.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(39, 'Id ut rerum doloribus.', 'Quis similique neque at enim ea repudiandae. Nihil voluptas neque sit qui iure. Maiores error ipsa mollitia amet. Magni sapiente omnis numquam velit.', 77, 682, 7948.07, 'seed-book-39.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(40, 'Et voluptas quis aliquid est quidem.', 'Illo totam expedita eum facere id totam omnis. Ipsam omnis est id est totam mollitia porro aperiam. Sed aliquam in accusamus quasi molestiae et accusamus. Culpa minus et ullam atque.', 47, 608, 3427.12, 'seed-book-40.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(41, 'Cupiditate officiis excepturi esse dolorem.', 'Est odit voluptate numquam saepe amet. Ducimus blanditiis voluptate voluptas vel ipsam autem.', 44, 855, 874.98, 'seed-book-41.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(42, 'Quia sunt voluptatem voluptatum officia.', 'Non omnis commodi sit vitae et voluptas ut. Voluptatem quaerat odit repudiandae facilis dolores vitae. Dolores iure voluptates natus iure qui vel ut.', 2, 190, 6778.24, 'seed-book-42.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(43, 'A cupiditate et quia possimus deleniti sed.', 'Autem et et eius sed voluptate voluptas est aliquid. Et sint eos quia quasi dicta cum culpa. Facere omnis qui consectetur consequatur neque dolor quia.', 80, 868, 9642.44, 'seed-book-43.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(44, 'Consequatur omnis non sint vel id itaque voluptatibus.', 'Doloremque velit adipisci aut est non. Esse illo est asperiores molestias doloremque. Et quam et facilis nostrum ut ab exercitationem est.', 55, 908, 9668.87, 'seed-book-44.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(45, 'Magni autem exercitationem velit nobis quae eaque.', 'Qui pariatur officia atque. Necessitatibus consectetur et nostrum ut neque maiores. Qui dolor similique velit. Ratione assumenda quia nam voluptatum.', 79, 523, 608.53, 'seed-book-45.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(46, 'Odit voluptas non et beatae ipsum facilis velit.', 'Quod qui labore sit quae esse rerum et. Culpa in nam molestias reiciendis magnam. Eum culpa rerum id at pariatur fugit voluptates. Quisquam ut aperiam repellendus distinctio.', 26, 72, 7188.16, 'seed-book-46.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(47, 'Enim error eum nihil quidem quisquam magni.', 'Vitae cum laudantium dolorem asperiores maiores. Et corrupti sint corrupti.', 86, 415, 9435.45, 'seed-book-47.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(48, 'Autem maxime atque voluptas architecto.', 'Voluptas rem consequatur est rerum vel. Nihil non molestiae non sunt. Et sunt odit qui ab ut aut.', 47, 221, 377.09, 'seed-book-48.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(49, 'Numquam quia itaque dolores molestiae temporibus libero.', 'Atque nostrum consequuntur qui ipsum cupiditate reiciendis nihil est. Atque non consequatur reprehenderit nemo enim. Accusantium omnis eos assumenda maiores numquam corrupti eveniet odit.', 97, 965, 3763.57, 'seed-book-49.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(50, 'Porro est deleniti laboriosam ullam excepturi consectetur.', 'Saepe tempora tenetur quis fugiat unde distinctio est. Sed mollitia quibusdam aspernatur error veniam possimus sit. Sapiente error molestiae rem ducimus.', 56, 452, 3354.81, 'seed-book-50.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(51, 'Quo omnis est vero.', 'Nisi error tempora similique. Rerum fugit ea autem. Recusandae quod rem facere rerum.', 60, 45, 4810.30, 'seed-book-51.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(52, 'Natus soluta et beatae et dolores.', 'Unde sit at voluptatem aliquam ex sunt. Aut necessitatibus dicta et ex accusamus. Illo in et possimus tempora aspernatur possimus ipsa. Quo nisi cum voluptate quia.', 100, 199, 7172.42, 'seed-book-52.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(53, 'Quia nam ex est nulla.', 'Consequatur qui odio sed non magni. Qui sunt sunt sint voluptas iure. Labore tenetur odio saepe quis.', 47, 402, 3452.81, 'seed-book-53.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(54, 'Magni ipsa alias ea et.', 'Aut est rerum sed voluptas omnis molestias voluptate. Cumque recusandae minima commodi sunt dignissimos et autem rem. Magni et esse laudantium unde quo voluptatem.', 93, 823, 186.30, 'seed-book-54.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:42'),
(55, 'Fuga ex enim alias reiciendis consequatur deserunt.', 'Voluptatem amet assumenda officiis ut iure. Exercitationem laudantium magni quia enim. Ut nulla cupiditate voluptas laborum odit. Accusamus voluptas consequatur fugit et.', 79, 174, 4512.98, 'seed-book-55.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(56, 'Dolore aliquid pariatur dolores dolor eum.', 'Aliquam possimus quam quibusdam ut ab. Commodi nihil est maxime at quam. Et corporis dolorem quis. Et distinctio sit facere deleniti earum.', 57, 130, 2635.90, 'seed-book-56.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(57, 'Itaque nisi quia accusamus et consectetur.', 'Et quod consequatur consectetur. Cum non praesentium autem et eum incidunt ab. Harum non distinctio harum ut.', 62, 131, 4868.55, 'seed-book-57.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(58, 'Reiciendis est possimus cum dolor a voluptas tempora.', 'Necessitatibus aut vitae quibusdam facere tempore. Occaecati voluptatem voluptate est consequatur. Qui est qui officia ut debitis itaque quia. Magnam consequatur alias dolorum rerum vel placeat.', 40, 63, 8860.24, 'seed-book-58.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(59, 'Sed consequatur unde voluptas placeat ut.', 'Rerum mollitia eos a sint dolore aut. Debitis rem enim quia ab. Amet reprehenderit eum et debitis sunt reiciendis.', 36, 46, 5533.12, 'seed-book-59.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(60, 'Minima amet natus iure.', 'Et enim et architecto sit rerum. Quo quidem qui consequuntur molestiae dolorum eveniet. Eveniet vel voluptatibus et et quidem est.', 83, 973, 1836.17, 'seed-book-60.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(61, 'Ipsum commodi vero quos.', 'Illo quisquam praesentium tempora repudiandae. Incidunt laborum commodi non provident aut est incidunt repudiandae. Iste velit perspiciatis quaerat vitae nobis.', 27, 713, 6960.85, 'seed-book-61.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(62, 'Cum provident quas maxime quibusdam.', 'Modi qui saepe sequi neque ex et. Aut deserunt rerum consectetur. Eius itaque velit vel deleniti veniam commodi illum ut. Eum maiores facilis unde pariatur illum laboriosam. Unde quas sint corrupti.', 50, 290, 2191.50, 'seed-book-62.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(63, 'Atque delectus quo officiis ut.', 'Nesciunt unde soluta tempora. Et sint cupiditate maxime asperiores qui voluptas. Molestiae quia vel quaerat ratione fuga molestias nisi. Facere sequi ipsam et tempora.', 26, 230, 4164.84, 'seed-book-63.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(64, 'Quas inventore sed minima odio saepe expedita aut.', 'Accusamus aut voluptates corrupti. Pariatur minus atque sed necessitatibus. Placeat similique totam consequatur fugiat cum numquam.', 40, 138, 921.96, 'seed-book-64.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(65, 'Aspernatur omnis alias officia quia sed.', 'Amet quasi dolorem aliquam sunt assumenda. Deserunt ipsa et ad dignissimos exercitationem officia autem. In unde sapiente non repellat vitae molestiae earum. Ut doloribus eaque nulla porro ut.', 51, 358, 1848.65, 'seed-book-65.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(66, 'Architecto molestias ratione et temporibus voluptatem autem excepturi.', 'Impedit occaecati debitis fuga qui quia. Et assumenda quas adipisci qui. Rerum ea dolorem corporis quae hic sint suscipit. Numquam quam numquam dolores veritatis molestiae ut nam sunt.', 7, 548, 6275.30, 'seed-book-66.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(67, 'Nemo est facilis autem ut quasi et.', 'Voluptatem rerum molestias quis ut cum. Dolor et assumenda rerum. Dignissimos quaerat adipisci voluptatem nobis voluptate magni assumenda. Mollitia iste aut qui placeat. Eos quia et laborum nisi.', 85, 268, 653.42, 'seed-book-67.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(68, 'Magnam architecto eos incidunt ut nulla praesentium officia.', 'Vitae qui ut sapiente ea. Fugiat officia fugiat est aliquid minus iusto non. Fugiat aut sunt ratione adipisci. Labore voluptas voluptas magnam quaerat aut.', 4, 623, 289.52, 'seed-book-68.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(69, 'Voluptatem aut accusamus qui culpa et.', 'Officia in dolores odio. Sunt deleniti ut in saepe omnis quasi dolores repudiandae. Porro sequi magni perspiciatis ad in.', 86, 403, 8575.71, 'seed-book-69.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(70, 'In non nam nisi itaque non totam.', 'Aut beatae non aliquam labore suscipit. Et ipsum assumenda corporis temporibus iste doloremque iure. Ut dolore praesentium rerum debitis rerum placeat hic odit.', 85, 59, 1382.95, 'seed-book-70.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(71, 'Dolorem velit at qui error.', 'Blanditiis voluptatem magni ex illum. Similique reiciendis dolor commodi consequatur quis. Quia laudantium est laudantium soluta ab consequuntur.', 20, 710, 7684.40, 'seed-book-71.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(72, 'Architecto eveniet aspernatur et sit.', 'Cum quia delectus illum. Saepe ut excepturi temporibus dolores reiciendis consequuntur. Qui inventore tempore saepe repellat.', 56, 468, 6066.49, 'seed-book-72.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(73, 'Rem eos facere nihil non enim.', 'Expedita ab atque rerum ratione beatae aliquam. Rerum quisquam nulla deserunt. Voluptas dicta autem beatae. Reiciendis cum enim temporibus eligendi.', 61, 80, 9210.49, 'seed-book-73.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(74, 'Quia natus rerum et.', 'Nobis reprehenderit et voluptas nulla. Vel dolore dolores eius officiis reprehenderit quibusdam. Accusamus consequatur corrupti nulla dolor asperiores sint sit.', 12, 212, 7340.99, 'seed-book-74.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(75, 'Nostrum qui tenetur maxime.', 'Deleniti autem suscipit laborum. Sint voluptatem eos consectetur labore. Quisquam delectus vitae eum iste nihil doloribus. Fugit reiciendis laboriosam deserunt.', 45, 48, 5096.66, 'seed-book-75.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(76, 'Dolores expedita quaerat adipisci enim.', 'Voluptas neque sapiente voluptatem pariatur perferendis nihil explicabo possimus. Quia ex consequuntur voluptas consequatur. Iusto labore est corrupti aut est ut qui.', 52, 387, 534.78, 'seed-book-76.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(77, 'Deleniti pariatur sed voluptas.', 'Nobis nemo officia natus qui mollitia. Ad maxime unde veniam autem iure earum. Voluptatem fugit vel unde omnis fugiat quos quia culpa.', 94, 939, 1906.38, 'seed-book-77.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(78, 'Saepe id illo perferendis facilis iusto dolorem provident.', 'Error itaque impedit quia tenetur. Eum eos neque ipsa magni eos maxime. Id ea ab sint est.', 90, 107, 3291.35, 'seed-book-78.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(79, 'Iste tempora eos aperiam tempore eius voluptatem possimus veniam.', 'Voluptas facere officia voluptatem sed molestias aut. Voluptas quasi aut qui praesentium laborum voluptatem. Nulla dolores molestias culpa enim.', 58, 360, 6641.40, 'seed-book-79.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(80, 'Optio nostrum nihil inventore officiis praesentium suscipit optio.', 'Sed quidem inventore ut in quis placeat et. Excepturi expedita delectus aut qui. In aut repellendus optio fugit omnis.', 18, 523, 871.54, 'seed-book-80.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(81, 'Sequi rerum neque et ab in eveniet.', 'Tenetur delectus ad aut culpa. Repellat facilis voluptas magni inventore.', 86, 217, 209.90, 'seed-book-81.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(82, 'Adipisci voluptatem ex quae eveniet unde exercitationem ipsum.', 'Occaecati ea iusto occaecati doloribus reprehenderit ut eius. Et quidem enim sint. Totam accusantium a ratione ea. Vitae aut sit tenetur qui ab quos quibusdam.', 85, 692, 565.76, 'seed-book-82.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(83, 'Assumenda repudiandae ab aspernatur sed accusamus.', 'Non voluptatem reiciendis nesciunt et tempore mollitia. Eos repellat labore consequatur quas dolorem. Nemo eaque inventore minus veritatis consequatur deleniti.', 99, 42, 2926.13, 'seed-book-83.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(84, 'Et autem qui odio dolores amet.', 'Commodi aut eum reiciendis dolores. Consequuntur autem similique quos recusandae non autem. Natus aut ut eos ut cupiditate.', 52, 526, 2135.99, 'seed-book-84.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(85, 'Nisi sit quis tempora minima quia vel numquam.', 'Aliquam facilis blanditiis occaecati qui doloribus deleniti est omnis. Cupiditate dolore quae maxime quas. Occaecati eligendi qui est. Asperiores explicabo veritatis beatae voluptatem animi quis.', 61, 696, 9886.86, 'seed-book-85.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(86, 'Ad laborum hic corporis repellat qui.', 'Animi in error sit officiis omnis. Et quidem autem perspiciatis. Possimus praesentium aut modi. Possimus deserunt sint et.', 21, 675, 2876.97, 'seed-book-86.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(87, 'Provident temporibus amet laborum qui exercitationem.', 'Magni fuga similique eum placeat eos. Qui debitis sed qui est placeat doloremque. Quidem ab at voluptatibus quo ipsum.', 94, 200, 4500.13, 'seed-book-87.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(88, 'Fuga sunt perspiciatis ullam non quis iure quasi.', 'Molestiae voluptates dolorum expedita. Eaque quisquam quis molestiae et sed error esse. Occaecati omnis omnis quibusdam quas quasi dicta nisi distinctio. Quo autem sequi nulla dicta hic veritatis.', 50, 906, 2188.94, 'seed-book-88.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(89, 'Iusto voluptas harum et.', 'Itaque autem officiis necessitatibus debitis. Aut voluptatibus eos sequi qui perferendis esse. Consequatur ea sed est consectetur et aliquam.', 84, 403, 1087.68, 'seed-book-89.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(90, 'Vel nostrum quo est autem ad.', 'Qui deserunt et excepturi omnis debitis esse. Sit repellendus ut vel omnis repudiandae. Consequuntur enim rerum quam aliquid. Non aliquam eos voluptas quas dolorem debitis voluptas suscipit.', 33, 986, 9949.28, 'seed-book-90.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(91, 'Dolor porro nihil in at autem beatae.', 'Eaque animi atque est quia. Sed unde similique est est. Aut corrupti itaque saepe quia. Blanditiis beatae numquam optio magni voluptatem et modi. Hic at dolorem enim perferendis.', 70, 708, 9915.41, 'seed-book-91.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(92, 'Eos omnis excepturi minima dolores cupiditate quis exercitationem et.', 'Perspiciatis dolores natus amet quo et. Et doloremque eum natus commodi. Qui molestias odio et fuga.', 80, 784, 7848.46, 'seed-book-92.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(93, 'Occaecati molestiae eos omnis soluta sint cupiditate.', 'Reiciendis veniam in autem et a aliquid pariatur sed. Ipsa consequuntur aspernatur qui. Non expedita error perspiciatis. Assumenda deleniti rerum et.', 75, 138, 249.57, 'seed-book-93.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(94, 'Eius quo qui voluptatibus mollitia voluptas ea ducimus in.', 'Corporis nostrum reprehenderit occaecati. Ullam repudiandae autem et distinctio blanditiis ab. Magni aspernatur voluptas qui voluptas. Pariatur doloribus totam quos quod deserunt et.', 76, 494, 5627.96, 'seed-book-94.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(95, 'Inventore provident tenetur aut repellendus.', 'Impedit est amet aut expedita est possimus. Et libero in in dolor illo.', 96, 852, 603.62, 'seed-book-95.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(96, 'Culpa sed qui placeat ex minima natus molestiae.', 'Omnis dolorem cum quisquam. Aut nihil laborum provident et. Accusantium assumenda vitae dolorem eum.', 64, 427, 9231.46, 'seed-book-96.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(97, 'Cumque sit odio eligendi sunt.', 'Iste impedit sit nisi deserunt. Eaque qui non repellendus deserunt. Dicta nobis sed nulla et voluptatem provident maiores.', 49, 112, 9308.27, 'seed-book-97.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(98, 'Nulla et eos ut nesciunt.', 'Ratione omnis voluptates omnis delectus rerum. Fugit quod nam dolor dolor illum amet id pariatur. Tempora id nesciunt fugit laborum nesciunt.', 12, 393, 2306.06, 'seed-book-98.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(99, 'Tempore voluptatibus eaque voluptas rem.', 'Hic dolores expedita occaecati aliquam. Laudantium amet quis odio eos voluptas ut sunt.', 57, 496, 4466.32, 'seed-book-99.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43'),
(100, 'Ad iste blanditiis quibusdam quos quia rerum.', 'Quisquam perferendis est quia voluptatibus. Autem deserunt doloribus impedit voluptatem consequatur et velit. Itaque animi et qui veritatis fuga aut et earum.', 36, 308, 3676.76, 'seed-book-100.svg', '2026-10-19 06:58:42', '2026-10-19 06:58:43');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_08_31_032104_create_books_table', 1),
(5, '2026_08_31_035130_create_movies_table', 1),
(6, '2026_09_04_022132_create_products_table', 1),
(7, '2026_09_04_041500_add_tz_account_fields_to_users_table', 1),
(8, '2026_09_04_060000_create_orders_tables', 1),
(9, '2026_09_04_070000_add_received_at_to_orders_table', 1),
(10, '2026_09_18_010833_create_posts_table', 1),
(11, '2026_09_18_012017_create_personal_access_tokens_table', 1),
(12, '2026_10_14_072227_create_activity_logs_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `genre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `release_year` int NOT NULL,
  `rating` decimal(3,1) NOT NULL,
  `poster` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `received_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `story` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Toyota',
  `year` smallint UNSIGNED NOT NULL DEFAULT '2025',
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'White',
  `transmission` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Automatic',
  `fuel_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Gasoline',
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `year`, `color`, `transmission`, `fuel_type`, `description`, `price`, `stock`, `image`, `created_at`, `updated_at`) VALUES
(1, 'Toyota Vios', 'Toyota', 2026, 'Silver Metallic', 'CVT', 'Gasoline', 'A dependable and efficient sedan made for everyday city driving.', 1092000.00, 6, 'https://images.unsplash.com/photo-1550355291-bbee04a92027?auto=format&fit=crop&w=1200&q=85', '2026-10-18 07:20:39', '2026-10-18 07:20:39'),
(2, 'Toyota Raize', 'Toyota', 2026, 'Turquoise Mica Metallic', 'CVT', 'Gasoline', 'A compact SUV with agile handling and practical urban versatility.', 1074000.00, 7, 'https://images.unsplash.com/photo-1619767886558-efdc259cde1a?auto=format&fit=crop&w=1200&q=85', '2026-10-18 07:20:39', '2026-10-18 07:27:17'),
(3, 'Toyota Corolla Altis', 'Toyota', 2026, 'Platinum White Pearl Mica', 'CVT', 'Hybrid', 'A refined sedan combining confident comfort with hybrid efficiency.', 1791000.00, 4, 'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?auto=format&fit=crop&w=1200&q=85', '2026-10-18 07:20:39', '2026-10-18 07:20:39'),
(4, 'Toyota Corolla Cross', 'Toyota', 2026, 'Metal Stream Metallic', 'CVT', 'Hybrid', 'A spacious crossover designed for efficient family adventures.', 1943000.00, 5, 'https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?auto=format&fit=crop&w=1200&q=85', '2026-10-18 07:20:39', '2026-10-18 07:20:39'),
(5, 'Toyota Camry', 'Toyota', 2026, 'Attitude Black Mica', 'CVT', 'Hybrid', 'A premium executive sedan with composed performance and comfort.', 2651000.00, 3, 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?auto=format&fit=crop&w=1200&q=85', '2026-10-18 07:20:39', '2026-10-18 07:20:39'),
(6, 'Toyota Hilux', 'Toyota', 2026, 'Emotional Red', 'Automatic', 'Diesel', 'A durable pickup engineered for demanding work and weekend travel.', 2076000.00, 7, 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&w=1200&q=85', '2026-10-18 07:20:39', '2026-10-18 07:20:39'),
(7, 'Toyota Fortuner', 'Toyota', 2026, 'Silver Metallic', 'Automatic', 'Diesel', 'A commanding seven-seat SUV built for comfort and capability.', 2656000.00, 4, 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=1200&q=85', '2026-10-18 07:20:39', '2026-10-18 07:20:39'),
(8, 'Toyota Hiace', 'Toyota', 2026, 'Luxury Pearl Toning', 'Automatic', 'Diesel', 'A roomy and reliable van for business, groups, and long journeys.', 3387000.00, 3, 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=1200&q=85', '2026-10-18 07:20:39', '2026-10-18 07:20:39'),
(9, 'Toyota GR Supra', 'Toyota', 2026, 'Prominence Red', 'Automatic', 'Gasoline', 'A flagship rear-wheel-drive sports car shaped for thrilling performance.', 5396000.00, 2, 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1200&q=85', '2026-10-18 07:20:39', '2026-10-18 07:20:39'),
(10, 'Toyota GR86', 'Toyota', 2026, 'Crystal Black Silica', 'Manual', 'Gasoline', 'A lightweight sports coupe with direct handling and driver-first character.', 2740000.00, 3, 'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?auto=format&fit=crop&w=1200&q=85', '2026-10-18 07:20:39', '2026-10-18 07:20:39'),
(11, 'Toyota GR Yaris', 'Toyota', 2026, 'Super White II', 'Manual', 'Gasoline', 'A rally-inspired performance hatchback with compact all-road confidence.', 3740000.00, 2, 'https://images.unsplash.com/photo-1542362567-b07e54358753?auto=format&fit=crop&w=1200&q=85', '2026-10-18 07:20:39', '2026-10-18 07:20:39');

-- --------------------------------------------------------

--
-- Table structure for table `recovery_lab_test`
--

CREATE TABLE `recovery_lab_test` (
  `id` int NOT NULL,
  `note` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('34KyQYXVjUcctERuRj6hYsK5e4MUwlIlfCsNY6jw', NULL, '127.0.0.1', 'curl/8.5.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTkxBZDRybUpxUm9tYWQxcW5HNWxsbXBtRXdRZ3FyZUVkTkZWQmFPViI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6ODM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9ib29rLXF1ZXJ5LWNvbXBhcmlzb24/bG93X3N0b2NrPTEmc2VhcmNoPWFsaWFzJnN0b2NrX2xpbWl0PTEwIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1792448738),
('42RBWoPHUcd7039bdVQagcTfzslZupL0Ezt8hfUj', NULL, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVnRLZk1Xb1JuYkJsWFV2N0lsSFNzODJVRDhtU0VxcHhqUHJtTDY1NiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6ODk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9ib29rLXF1ZXJ5LWNvbXBhcmlzb24tc3RvY2stbGltaXQ/bG93X3N0b2NrPTEmc2VhcmNoPSZzdG9ja19saW1pdD00IjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1792451731),
('eIQ0UNP7Pgu9UEkNq7PlwTAyPJ1uCSjq4RqWWZyh', NULL, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTXpueGtUV0MzOFlySUxrak5SZmRjQzB1aXB2UWdKTDc4QU44b1kwRSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDM6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9ib29rLXF1ZXJ5LWNvbXBhcmlzb24iO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1792447023),
('FlkXD8yb44eR1IBltEOEYMysXR6SQiDr8qXFqJ0N', NULL, '127.0.0.1', 'curl/8.5.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiNzJLV2NiY0Q5YUdhdnRHV1M4UHJ0N2JOTnZNTk5uQTJBaHhBT3YzSiI7czoxMDoiX29sZF9pbnB1dCI7YToxOntzOjExOiJzdG9ja19saW1pdCI7czo0OiIyMDAwIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjI6e2k6MDtzOjEwOiJfb2xkX2lucHV0IjtpOjE7czo2OiJlcnJvcnMiO319czo2OiJlcnJvcnMiO086MzE6IklsbHVtaW5hdGVcU3VwcG9ydFxWaWV3RXJyb3JCYWciOjE6e3M6NzoiACoAYmFncyI7YToxOntzOjc6ImRlZmF1bHQiO086Mjk6IklsbHVtaW5hdGVcU3VwcG9ydFxNZXNzYWdlQmFnIjoyOntzOjExOiIAKgBtZXNzYWdlcyI7YToxOntzOjExOiJzdG9ja19saW1pdCI7YToxOntpOjA7czo0OToiVGhlIHN0b2NrIGxpbWl0IGZpZWxkIG11c3QgYmUgYmV0d2VlbiAwIGFuZCAxMDAwLiI7fX1zOjk6IgAqAGZvcm1hdCI7czo4OiI6bWVzc2FnZSI7fX19czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NzI6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9ib29rLXF1ZXJ5LWNvbXBhcmlzb24tc3RvY2stbGltaXQ/c3RvY2tfbGltaXQ9MjAwMCI7czo1OiJyb3V0ZSI7Tjt9fQ==', 1792451541),
('j83Rccstkpz68lqyxKSTDSxrQjkSQ1PlhE1N0ekU', NULL, '127.0.0.1', 'curl/8.5.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiM05YNUpxb3V6RXhtaDVFd3Bza2dsdGJhelZQOW8xMWw2OEFXRVVKSiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6ODI6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9ib29rLXF1ZXJ5LWNvbXBhcmlzb24tc3RvY2stbGltaXQ/bG93X3N0b2NrPTEmc3RvY2tfbGltaXQ9NTAiO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1792451541),
('mAQfLWfqEJZSzMd0ex1Hs4G6cneTYYS58kgHuISA', NULL, '127.0.0.1', 'curl/8.5.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidlZxUlFpUW10MkxTckVkWkFuVzJsRUJla2dXSjJFTkgzVkVveHZzaSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9ib29rLXF1ZXJ5LWNvbXBhcmlzb24tc3RvY2stbGltaXQiO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1792451541),
('Mvxp7HuYgnVay3ptNm5iMA0jYHqTV77w8dXTJ1mC', NULL, '127.0.0.1', 'curl/8.5.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZjZ4YzNPbVZZTHlSdVZFc0pMVXBLT01QUXdMWkRCbXFCUDhqeFpwbiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6ODI6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9ib29rLXF1ZXJ5LWNvbXBhcmlzb24tc3RvY2stbGltaXQ/bG93X3N0b2NrPTEmc3RvY2tfbGltaXQ9NTAiO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1792452060),
('qjdzX7GRO75PGXWqghyVeNO6rXNY5nwtIHjDla0x', NULL, '127.0.0.1', 'curl/8.5.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQkVISThVam9lbFlobFVhaDcyeTdkemg2WkMwcEh0T0hMdUhhZXFucSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6ODM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9ib29rLXF1ZXJ5LWNvbXBhcmlzb24tc3RvY2stbGltaXQ/bG93X3N0b2NrPTEmc3RvY2tfbGltaXQ9MTAwIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1792451541);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `email_verified_at`, `password`, `role`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Test User', 'test.user', 'test@example.com', NULL, '$2y$12$rjSmZIi.L7SGr0P3KRooL.SuGYnHAk0h39qcDH4bSgwYBBaQIqu.S', 'customer', 'active', NULL, '2026-10-18 07:20:38', '2026-10-18 07:20:38'),
(2, 'Administrator', 'tz.admin', 'admin@email.com', NULL, '$2y$12$gN2oiW2i3.KqSdYFn7bLneN0v6wKFQkMieAOQ9sElHLrMzPAUQDzm', 'admin', 'active', NULL, '2026-10-18 07:20:39', '2026-10-18 07:20:39'),
(3, 'John Customer', 'tz.customer', 'customer@email.com', NULL, '$2y$12$NQbTzus1wewdiTrIpIHjKeJAc0NhJxQvPn3SB/jBRe0l0PxPFNSYy', 'customer', 'active', NULL, '2026-10-18 07:20:39', '2026-10-18 07:20:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_user_id_foreign` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recovery_lab_test`
--
ALTER TABLE `recovery_lab_test`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_username_unique` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `recovery_lab_test`
--
ALTER TABLE `recovery_lab_test`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
