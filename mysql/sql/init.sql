DROP SCHEMA IF EXISTS shukatsu;
CREATE SCHEMA shukatsu;
USE shukatsu;

-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- ホスト: db
-- 生成日時: 2022 年 5 月 29 日 14:08
-- サーバのバージョン： 8.0.29
-- PHP のバージョン: 8.0.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `shukatsu`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `admin_login`
--

CREATE TABLE `admin_login` (
  `id` int NOT NULL,
  `email` text NOT NULL,
  `login_password` text CHARACTER SET utf8mb3 COLLATE utf8_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- テーブルのデータのダンプ `admin_login`
--

INSERT INTO `admin_login` (`id`, `email`, `login_password`) VALUES
(1, 'email@email', '$2y$10$4NDL25xbvDDnk/Xmgywluug.focPjCiGpgL8psMotMkyeX3YcA72G');

-- --------------------------------------------------------

--
-- テーブルの構造 `agents`
--

CREATE TABLE `agents` (
  `id` int NOT NULL,
  `corporate_name` text CHARACTER SET utf8mb3 COLLATE utf8_general_ci NOT NULL,
  `insert_company_name` text CHARACTER SET utf8mb3 COLLATE utf8_general_ci NOT NULL,
  `started_at` date DEFAULT NULL,
  `ended_at` date DEFAULT NULL,
  `login_email` text CHARACTER SET utf8mb3 COLLATE utf8_general_ci NOT NULL,
  `login_pass` text CHARACTER SET utf8mb3 COLLATE utf8_general_ci NOT NULL,
  `to_send_email` text CHARACTER SET utf8mb3 COLLATE utf8_general_ci NOT NULL,
  `application_max` int NOT NULL,
  `charge` int NOT NULL,
  `client_name` text CHARACTER SET utf8mb3 COLLATE utf8_general_ci NOT NULL,
  `client_department` text NOT NULL,
  `client_email` text NOT NULL,
  `client_tel` text NOT NULL,
  `insert_logo` text CHARACTER SET utf8mb3 COLLATE utf8_general_ci NOT NULL,
  `insert_recommend_1` text CHARACTER SET utf8mb3 COLLATE utf8_general_ci NOT NULL,
  `insert_recommend_2` text CHARACTER SET utf8mb3 COLLATE utf8_general_ci NOT NULL,
  `insert_recommend_3` text CHARACTER SET utf8mb3 COLLATE utf8_general_ci NOT NULL,
  `insert_handled_number` text NOT NULL,
  `list_status` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- テーブルのデータのダンプ `agents`
--

INSERT INTO `agents` (`id`, `corporate_name`, `insert_company_name`, `started_at`, `ended_at`, `login_email`, `login_pass`, `to_send_email`, `application_max`, `charge`, `client_name`, `client_department`, `client_email`, `client_tel`, `insert_logo`, `insert_recommend_1`, `insert_recommend_2`, `insert_recommend_3`, `insert_handled_number`, `list_status`) VALUES
(1, '株式会社キャリセン', 'キャリセン就活エージェント', '2022-05-01', '2022-06-11', 'kyari@kyari', '$2y$10$QukH2bY/MbYrD69UwYh8zu6LjSXF01m44Nu82H/4r4xf7Qt9cmuJu', 'kashiken@gmail.com', 7, 1000, '鹿島健太', '広報部', 'kirin.myk2018@gmail.com', '0809000300', '20220522060636_st-logo-careecen.png', '先輩利用者のES回答例や体験談もチェックできる', '初回から専任のコンサルタントと1.5時間じっくり面談', '累計6万人が利用してきた豊富な経験と実績', '1000', 3),
(2, '株式会社リクナビ ', 'リクナビ', '2022-05-19', '2022-06-11', 'rikunabi@rikunabi', '$2y$10$2VShvf6Hi23.EAmNepwxrObnY/EHASuF3VTM5mXjIVhwakfIzT/tK', 'rikunabi@rikunabi', 1000, 1000, 'rikunabi-sun', '営業部', 'rikunabi-sun@sun', '333', '20220522060246_rikunabi.jpg', '手厚い面接サポートあり', '全体の8割が文系学生のため、文系に合う職種を見つけられる', '週3～4回相談できるから不安を解消しやすい', '10000', 1),
(3, 'NaS株式会社', ' DiG UP CAREER', '2022-05-19', '2022-06-09', 'dig@dig', '$2y$10$ccOzfFRzDwrF/zXeyYjIhO54QNqNSw9i5dSSEpmtIrnAF/6MGKBou', 'dig@up', 50, 1000, '鹿島健太', 'カルチャー局', 'kashiken@kkkkkkk', '08011112222', '20220525090926_dig.png', '就活のプロ（元人事・人材会社出身）がLINEも使って親身に手厚いサポート', '企業選び・自己理解のプロによる就活セミナーが受けられ、理解が深まる', '就活生に寄り添ったサービス、満足度90%超！友人紹介も60%超！', '3000', 1),
(4, '株式会社meets company', 'meets company', '2022-05-19', '2022-06-11', 'meets@meets', '$2y$10$27loDXgCzBvWBaHLv2btyuuInKMTyF6Y7YpWsmOpOfqdd1/n5ilX2', 'meets@meets', 1000, 1000, 'meetsさん', '営業部', 'meets-sun@sun', '120', '20220522060525_meets.jpg', '年間1000回を超えるマッチングイベントを開催！', '東証一部上場企業からベンチャー企業まで豊富な企業数', '7拠点を中心に全国対応のため地方在住でも利用しやすい', '非公開', 1),
(5, '株式会社マイナビ', 'マイナビ新卒紹介', '2022-05-12', '2022-06-11', 'mainabi@mainabi', '$2y$10$Sc90axzgOw6CZhP6pinxG.kVZsfLO3CcCMbaNbMbSuXzNpqnBzTh6', 'mainabi@mainabi', 1000, 1000, '西山直輝', 'カルチャー局', 'naoki@kinketsu.co', '08011380033', '20220522062036_mainabi.jpg', '海外企業の理系職種もカバー', '採用基準や面接情報をほぼ全て把握	', 'ここにしかない非公開求人に出会いたい人におすすめ', '50000', 1),
(6, 'Leverages', 'キャリアチケット', '2022-05-01', '2022-05-28', 'tichet@tichet', '$2y$10$GJRafDdGOQAnE9uXfr1WK.qUoPN3SuwgKJYdjScdtlDr432ttIGaa', 'ticket@gmail.com', 400, 1000, 'テスト氏名', '部署名', 'ticket@gmail', '04022220000', '', 'エントリーシート（ES）の添削や面接対策を受けられる', '紹介企業を厳選。ブラック企業を避けられ、最短3日の内定実績もある', 'キャリアアドバイザーに無料で就活相談ができる', '4444', 2),
(7, '株式会社ネオキャリア', 'Neo', '2022-05-12', '2022-06-11', 'neo@neo', '$2y$10$IR7uaXqkFGOhChrOmDIegOgr..A6o7xiwF614KishGWslcijH.lKW', 'kashiken@ddddd', 1000, 1000, '鹿島健太', 'カルチャー局', 'kkkkk@co.jp', '08099999999', '20220526030357_neo.png', 'イニシャル課金の送客サービスあり。1名15,000円～', '体育会系出身の学生に特化したサポートを受けられる', 'ES添削の回数無制限', '10000', 1),
(8, 'レバテック株式会社', 'レバテック', '2022-05-10', '2022-06-11', 'leva@leva', '$2y$10$3ljD3l5qCEZTllfWvcnUzeHIEicE0uf/mXsWibNFAXNpnm8IMuuYe', 'leverages@co.jp', 1000, 1000, '西山直輝', 'カルチャー局', 'naoki@kinketsu.com', '08070707070', '20220525072023_leva2.jpg', 'レバテック登録者実績20万人', 'エンジニアが利用したい転職エージェントNo.1', '志望度の高い企業は現場社員との面談も可能', '5000', 1),
(67, 'Jobspirng株式会社', 'JobSpring', '2022-05-23', '2022-06-11', 'jobspring@jobspring', '$2y$10$y67K4aNtk9THkvFKczXDHeButjyiEGm3PDd1iTqntRTKNjKqxXaWe', 'jobspring@jobspring', 1000, 1000, 'jobさん', '営業部', 'job-sun@sun', '53266', '20220529014022_JobSpring_logo-min.png', '自己分析からの徹底サポートあり', '適性検査を活かしたAIマッチング', '内定後の定着率は88%', '8500', 1),
(68, 'Goodfindエージェント株式会社', 'Goodfind', '2022-05-28', '2022-06-11', 'goodfind@goodfind', '$2y$10$99pe1yJwvsOfT4pRbjGg1eZCJLdU0jh1zzSoVz0XMy5jBzOfAf/4.', 'good@good', 1000, 1000, 'goodfing-sun', '営業部', 'goodfind@sun', '3451', '20220529085804_2e7a276ffc67a456ff2bc06d75c0d2a0.png', 'ベンチャー企業の取扱企業数は日本一！', 'セミナーのレベルが非常に高く大好評', '徹底した面談サポートにより、内定率88%', '5600', 1),
(69, 'ツイング株式会社', 'ツイング', '2022-05-28', '2022-06-11', 'twing@twing', '$2y$10$b5EZDclmkV8P6EjXTeqeweG53hYfQe0ZKOkzUtdjtypjTEAYA5fhi', 'twing@a', 1000, 1000, 'twing-sun', '営業部', 'twing@sun', '22222', '20220529090317_og.png', '大手企業の元人事からES添削サポートあり', '相談回数は無制限！即返答！', '1 on 1の手厚い面接サポートあり', '8000', 1),
(70, 'duda株式会社', 'duda新卒エージェント', '2022-05-28', '2022-06-11', 'duda@duda', '$2y$10$OJFzz49XhwS//4LInbKOUeLb.5pYVgYYfzL/CCb9vxJhFQdIYDjIe', 'du@du', 1000, 5000, 'duda-sun', '営業部', 'duda@sun', '3233', '20220529091423_images.png', '選考のフィードバックがもらえる', '初回から専任のコンサルタントと1時間じっくり面談', 'ES添削、面接サポート受け放題！', '7500', 1);

-- --------------------------------------------------------

--
-- テーブルの構造 `agents_tags`
--

CREATE TABLE `agents_tags` (
  `id` int NOT NULL,
  `agent_id` int NOT NULL,
  `tag_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- テーブルのデータのダンプ `agents_tags`
--

INSERT INTO `agents_tags` (`id`, `agent_id`, `tag_id`) VALUES
(193, 4, 16),
(194, 4, 19),
(195, 4, 23),
(208, 3, 16),
(209, 3, 19),
(210, 3, 24),
(228, 8, 17),
(229, 8, 19),
(230, 8, 23),
(231, 8, 24),
(232, 8, 29),
(233, 8, 30),
(278, 2, 16),
(279, 2, 18),
(280, 2, 20),
(281, 2, 24),
(285, 7, 17),
(286, 7, 18),
(287, 7, 23),
(288, 7, 24),
(289, 67, 17),
(290, 67, 20),
(291, 67, 24),
(292, 68, 17),
(293, 68, 19),
(294, 68, 23),
(295, 68, 24),
(296, 69, 16),
(297, 69, 18),
(298, 69, 24),
(303, 5, 17),
(304, 5, 20),
(305, 5, 23),
(318, 1, 16),
(319, 1, 18),
(320, 1, 20),
(321, 1, 23),
(322, 6, 16),
(323, 6, 19),
(324, 6, 23),
(325, 70, 17),
(326, 70, 18),
(327, 70, 20),
(328, 70, 24);

-- --------------------------------------------------------

--
-- テーブルの構造 `agent_list_status`
--

CREATE TABLE `agent_list_status` (
  `id` int NOT NULL,
  `list_status` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- テーブルのデータのダンプ `agent_list_status`
--

INSERT INTO `agent_list_status` (`id`, `list_status`) VALUES
(1, '掲載中'),
(2, '掲載停止'),
(3, '申込上限到達');

-- --------------------------------------------------------

--
-- テーブルの構造 `filter_sorts`
--

CREATE TABLE `filter_sorts` (
  `id` int NOT NULL,
  `sort_name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- テーブルのデータのダンプ `filter_sorts`
--

INSERT INTO `filter_sorts` (`id`, `sort_name`) VALUES
(11, 'エージェントのタイプ'),
(12, '志望会社の規模'),
(14, '理系 / 文系');

-- --------------------------------------------------------

--
-- テーブルの構造 `filter_tags`
--

CREATE TABLE `filter_tags` (
  `tag_id` int NOT NULL,
  `sort_id` int NOT NULL,
  `tag_name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- テーブルのデータのダンプ `filter_tags`
--

INSERT INTO `filter_tags` (`tag_id`, `sort_id`, `tag_name`) VALUES
(16, 11, '総合型'),
(17, 11, '特化型'),
(18, 12, '大企業'),
(19, 12, 'ベンチャー企業'),
(20, 12, '外資系'),
(23, 14, '理系'),
(24, 14, '文系');

-- --------------------------------------------------------

--
-- テーブルの構造 `invalid_requests`
--

CREATE TABLE `invalid_requests` (
  `id` int NOT NULL,
  `contact_id` int NOT NULL,
  `invalid_request_memo` text NOT NULL,
  `invalid_request_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- テーブルのデータのダンプ `invalid_requests`
--

INSERT INTO `invalid_requests` (`id`, `contact_id`, `invalid_request_memo`, `invalid_request_created`) VALUES
(1, 1, '不適切', '2022-05-08 12:59:39'),
(2, 2, '連絡つかない', '2022-05-08 12:59:39'),
(3, 12, '通報テスト', '2022-05-21 01:25:14'),
(13, 7, '通報初めて', '2022-05-21 02:45:25'),
(19, 13, '名前が氏名になっていない', '2022-05-21 09:00:58'),
(20, 11, 'なんか生意気そうだから', '2022-05-26 07:32:25'),
(21, 11, 'なんか生意気そうだから', '2022-05-26 07:32:26'),
(22, 11, 'なんか生意気そうだから', '2022-05-26 07:32:30'),
(23, 11, 'なんか生意気そうだから', '2022-05-26 07:32:51'),
(24, 155, 'ID144と申込内容が同じで、同一人物であったので、こちらを無効化していただければと思います。', '2022-05-29 21:49:56'),
(25, 66, 'ID26と申込内容が同じため、無効申請致します。', '2022-05-29 21:51:15'),
(26, 156, 'ID145と重複しているため', '2022-05-29 21:53:44'),
(27, 145, 'ID156と重複しているため', '2022-05-29 21:54:11');

-- --------------------------------------------------------

--
-- テーブルの構造 `students`
--

CREATE TABLE `students` (
  `id` int NOT NULL,
  `name` text NOT NULL,
  `collage` text NOT NULL,
  `department` text NOT NULL,
  `class_of` int NOT NULL,
  `email` text NOT NULL,
  `tel` text NOT NULL,
  `address` text NOT NULL,
  `memo` text NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- テーブルのデータのダンプ `students`
--

INSERT INTO `students` (`id`, `name`, `collage`, `department`, `class_of`, `email`, `tel`, `address`, `memo`, `created`) VALUES
(11, '鹿島健太', '東京大学', '理工学部', 24, 'kasiken@gmail.', '080903387777', '練馬区', 'pp', '2022-05-19 07:49:14'),
(13, '小林あきら', '新潟大学', '教育学部', 24, 'akira@gmail.com', '0809033822222', '練馬区', '', '2022-05-21 16:04:37'),
(14, '田口まいの', '北海道大学', '経済学部', 24, 'maino@mail.com', '08011111111', '練馬区', '', '2022-04-20 16:04:37'),
(15, '山田太郎', '山形大学', '経済学部', 24, 'tarou@email.com', '080030304444', '山形県山形市2-28-19', 'できれば地元で就職したいと考えております。', '2022-05-29 20:50:02'),
(16, '山田太郎', '山形大学', '経済学部', 24, 'tarou@email.com', '080030304444', '山形県山形市2-28-19', 'できれば地元で就職したいと考えております。', '2022-05-29 20:51:04'),
(17, '井戸健太', '富山県立大学', '情報学科', 24, 'ido@gmail.com', '080300600', '富山県高岡市3-19-199', '', '2022-04-06 20:55:33'),
(18, '山岡まゆ', '慶應大学', '法学部政治学科', 26, 'mayu@gmail.com', '0305559999', '東京都町田市本町田2-19-1966', '地域に密着した企業に就職希望です', '2022-04-21 20:57:59'),
(19, '岡田正樹', '早稲田大学', '商学部', 25, 'okada@email.com', '393000020010', '東京都青山一丁目2-1-1999', '', '2022-05-29 21:02:55'),
(20, '山田太郎', '山形大学', '経済学部', 25, 'tarou@email.com', '080030304444', '山形県山形市2-28-19', '地元就職を希望します。', '2022-05-29 21:06:05'),
(21, '水口あき', '秋田市立大学', '農学部', 24, 'aki@gmail.com', '202007777111', '秋田県由利本荘市20-2221-1', '上京して就職を考えております', '2022-05-29 21:17:40'),
(22, '秋田', '慶應', 'なし', 24, 'a@a', 'a', 'a', '', '2022-05-29 21:18:51'),
(23, '滋賀正美', '明治大学', '理工学部', 24, 'siga@gmail.com', '08099992222', '東京都八王子市3-3-1111', '大学院に進学せずに就職を考えております', '2022-05-29 21:20:59'),
(24, '藤田まさき', '中央大学', '国際教養学部', 25, 'akira@gmail.com', '08033332222', '神奈川県川崎市10-12222', '', '2022-05-29 21:23:38'),
(25, '北村巧', '東京都芸術大学', 'なし', 25, 'takumi@gmail.com', '08033338888', '東京都板橋区高島平2-2-111', 'クリエイター志望です', '2022-05-29 21:26:36'),
(26, '井口りさ', '日本大学', '教育学部', 24, 'iguti@gmail.com', '0805550333', '東京都八王子市3-3-1111', '', '2022-05-29 21:28:23'),
(27, '谷口まこと', 'プログラミング大学', '総合技術学部', 24, 'tani@email', '09033339999', '東京都板橋区高島平2-2-111', '', '2022-05-29 21:43:16'),
(28, '島田洋一', '海洋大学', '機関工学科', 25, 'sima@email', '0402220333', '埼玉県', '', '2022-05-29 21:46:45'),
(29, '島田洋一', '海洋大学', '機関工学科', 25, 'sima@email', '0402220333', '埼玉県', '', '2022-05-29 21:47:08');

-- --------------------------------------------------------

--
-- テーブルの構造 `students_contacts`
--

CREATE TABLE `students_contacts` (
  `id` int NOT NULL,
  `student_id` int NOT NULL,
  `agent_id` int NOT NULL,
  `valid_status_id` int NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reason` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- テーブルのデータのダンプ `students_contacts`
--

INSERT INTO `students_contacts` (`id`, `student_id`, `agent_id`, `valid_status_id`, `created`, `reason`) VALUES
(3, 1, 2, 1, '2022-05-08 13:18:44', ''),
(4, 1, 2, 1, '2022-05-08 13:18:44', ''),
(5, 1, 3, 1, '2022-05-08 13:18:44', ''),
(6, 2, 3, 1, '2022-05-08 13:18:44', ''),
(7, 9, 1, 2, '2022-04-21 14:01:10', ''),
(8, 9, 3, 1, '2022-05-18 14:01:10', ''),
(9, 9, 16, 1, '2022-05-18 14:01:10', ''),
(10, 10, 1, 2, '2022-05-18 14:04:42', ''),
(11, 10, 3, 2, '2022-05-18 14:04:42', ''),
(12, 10, 16, 2, '2022-05-18 14:04:42', ''),
(13, 11, 1, 2, '2022-05-19 07:49:14', ''),
(14, 11, 16, 1, '2022-05-19 07:49:14', ''),
(15, 12, 1, 1, '2022-05-20 08:45:34', ''),
(16, 12, 3, 3, '2022-05-20 08:45:34', ''),
(17, 12, 16, 1, '2022-05-20 08:45:34', ''),
(18, 13, 1, 3, '2022-05-21 16:04:37', ''),
(19, 13, 3, 3, '2022-05-21 16:04:37', ''),
(20, 13, 16, 1, '2022-05-21 16:04:37', ''),
(21, 13, 69, 1, '2022-05-21 16:04:37', ''),
(22, 12, 69, 3, '2022-05-20 08:45:34', ''),
(23, 12, 62, 1, '2022-05-20 08:45:34', ''),
(24, 13, 62, 1, '2022-05-20 08:45:34', ''),
(25, 14, 62, 1, '2022-05-20 08:45:34', ''),
(26, 16, 70, 1, '2022-05-29 20:51:04', ''),
(27, 16, 69, 1, '2022-05-29 20:51:04', ''),
(28, 16, 68, 1, '2022-05-29 20:51:04', ''),
(29, 16, 67, 1, '2022-05-29 20:51:04', ''),
(30, 16, 8, 1, '2022-05-29 20:51:04', ''),
(31, 16, 7, 1, '2022-05-29 20:51:04', ''),
(32, 16, 5, 1, '2022-05-29 20:51:04', ''),
(33, 16, 4, 1, '2022-05-29 20:51:04', ''),
(34, 16, 3, 1, '2022-05-29 20:51:04', ''),
(35, 16, 2, 1, '2022-05-29 20:51:04', ''),
(36, 17, 70, 1, '2022-05-29 20:55:33', ''),
(37, 17, 69, 1, '2022-05-29 20:55:33', ''),
(38, 17, 68, 1, '2022-05-29 20:55:33', ''),
(39, 17, 67, 1, '2022-05-29 20:55:33', ''),
(40, 17, 8, 1, '2022-05-29 20:55:33', ''),
(41, 17, 7, 1, '2022-05-29 20:55:33', ''),
(42, 17, 5, 1, '2022-05-29 20:55:33', ''),
(43, 17, 4, 1, '2022-05-29 20:55:33', ''),
(44, 17, 3, 1, '2022-05-29 20:55:33', ''),
(45, 17, 2, 1, '2022-05-29 20:55:33', ''),
(46, 18, 70, 1, '2022-05-29 20:57:59', ''),
(47, 18, 69, 1, '2022-05-29 20:57:59', ''),
(48, 18, 68, 1, '2022-05-29 20:57:59', ''),
(49, 18, 67, 1, '2022-05-29 20:57:59', ''),
(50, 18, 8, 1, '2022-05-29 20:57:59', ''),
(51, 18, 7, 1, '2022-05-29 20:57:59', ''),
(52, 18, 5, 1, '2022-05-29 20:57:59', ''),
(53, 18, 4, 1, '2022-05-29 20:57:59', ''),
(54, 18, 3, 1, '2022-05-29 20:57:59', ''),
(55, 18, 2, 1, '2022-05-29 20:57:59', ''),
(56, 19, 70, 1, '2022-05-29 21:02:55', ''),
(57, 19, 69, 1, '2022-05-29 21:02:55', ''),
(58, 19, 68, 1, '2022-05-29 21:02:55', ''),
(59, 19, 67, 1, '2022-05-29 21:02:55', ''),
(60, 19, 8, 1, '2022-05-29 21:02:55', ''),
(61, 19, 7, 1, '2022-05-29 21:02:55', ''),
(62, 19, 5, 1, '2022-05-29 21:02:55', ''),
(63, 19, 4, 1, '2022-05-29 21:02:55', ''),
(64, 19, 3, 1, '2022-05-29 21:02:55', ''),
(65, 19, 2, 1, '2022-05-29 21:02:55', ''),
(66, 20, 70, 3, '2022-05-29 21:06:05', '重複を確認しました。無効申請を承認いたします'),
(67, 20, 69, 1, '2022-05-29 21:06:05', ''),
(68, 20, 68, 1, '2022-05-29 21:06:05', ''),
(69, 20, 67, 1, '2022-05-29 21:06:05', ''),
(70, 20, 8, 1, '2022-05-29 21:06:05', ''),
(71, 20, 7, 1, '2022-05-29 21:06:05', ''),
(72, 20, 5, 1, '2022-05-29 21:06:05', ''),
(73, 20, 4, 1, '2022-05-29 21:06:05', ''),
(74, 20, 3, 1, '2022-05-29 21:06:05', ''),
(75, 20, 2, 1, '2022-05-29 21:06:05', ''),
(76, 21, 70, 1, '2022-05-29 21:17:40', ''),
(77, 21, 69, 1, '2022-05-29 21:17:40', ''),
(78, 21, 68, 1, '2022-05-29 21:17:40', ''),
(79, 21, 67, 1, '2022-05-29 21:17:40', ''),
(80, 21, 8, 1, '2022-05-29 21:17:40', ''),
(81, 21, 7, 1, '2022-05-29 21:17:40', ''),
(82, 21, 5, 1, '2022-05-29 21:17:40', ''),
(83, 21, 4, 1, '2022-05-29 21:17:40', ''),
(84, 21, 3, 1, '2022-05-29 21:17:40', ''),
(85, 21, 2, 1, '2022-05-29 21:17:40', ''),
(86, 21, 1, 1, '2022-05-29 21:17:40', ''),
(87, 22, 70, 3, '2022-05-29 21:18:51', '無効化申請されていませんが、入力情報に抜けが多いためこちらで無効化いたします'),
(88, 22, 69, 1, '2022-05-29 21:18:51', ''),
(89, 22, 68, 1, '2022-05-29 21:18:51', ''),
(90, 22, 67, 1, '2022-05-29 21:18:51', ''),
(91, 22, 8, 1, '2022-05-29 21:18:51', ''),
(92, 23, 70, 1, '2022-05-29 21:20:59', ''),
(93, 23, 69, 1, '2022-05-29 21:20:59', ''),
(94, 23, 68, 1, '2022-05-29 21:21:00', ''),
(95, 23, 67, 1, '2022-05-29 21:21:00', ''),
(96, 23, 8, 1, '2022-05-29 21:21:00', ''),
(97, 23, 7, 1, '2022-05-29 21:21:00', ''),
(98, 23, 5, 1, '2022-05-29 21:21:00', ''),
(99, 23, 4, 1, '2022-05-29 21:21:00', ''),
(100, 23, 3, 1, '2022-05-29 21:21:00', ''),
(101, 23, 2, 1, '2022-05-29 21:21:00', ''),
(102, 23, 1, 1, '2022-05-29 21:21:00', ''),
(103, 24, 70, 1, '2022-05-29 21:23:38', ''),
(104, 24, 69, 1, '2022-05-29 21:23:38', ''),
(105, 24, 68, 1, '2022-05-29 21:23:38', ''),
(106, 24, 67, 1, '2022-05-29 21:23:38', ''),
(107, 24, 8, 1, '2022-05-29 21:23:38', ''),
(108, 24, 7, 1, '2022-05-29 21:23:38', ''),
(109, 24, 5, 1, '2022-05-29 21:23:38', ''),
(110, 24, 4, 1, '2022-05-29 21:23:38', ''),
(111, 24, 3, 1, '2022-05-29 21:23:38', ''),
(112, 24, 2, 1, '2022-05-29 21:23:38', ''),
(113, 24, 1, 1, '2022-05-29 21:23:38', ''),
(114, 25, 70, 1, '2022-05-29 21:26:36', ''),
(115, 25, 69, 1, '2022-05-29 21:26:36', ''),
(116, 25, 68, 1, '2022-05-29 21:26:36', ''),
(117, 25, 67, 1, '2022-05-29 21:26:36', ''),
(118, 25, 8, 1, '2022-05-29 21:26:36', ''),
(119, 25, 7, 1, '2022-05-29 21:26:36', ''),
(120, 25, 5, 1, '2022-05-29 21:26:36', ''),
(121, 25, 4, 1, '2022-05-29 21:26:36', ''),
(122, 25, 3, 1, '2022-05-29 21:26:36', ''),
(123, 25, 2, 1, '2022-05-29 21:26:36', ''),
(124, 26, 70, 1, '2022-05-29 21:28:23', ''),
(125, 26, 69, 1, '2022-05-29 21:28:23', ''),
(126, 26, 68, 1, '2022-05-29 21:28:23', ''),
(127, 26, 67, 1, '2022-05-29 21:28:23', ''),
(128, 26, 8, 1, '2022-05-29 21:28:23', ''),
(129, 26, 7, 1, '2022-05-29 21:28:23', ''),
(130, 26, 5, 1, '2022-05-29 21:28:23', ''),
(131, 26, 4, 1, '2022-05-29 21:28:23', ''),
(132, 26, 3, 1, '2022-05-29 21:28:23', ''),
(133, 26, 2, 1, '2022-05-29 21:28:23', ''),
(134, 27, 70, 1, '2022-05-29 21:43:16', ''),
(135, 27, 69, 1, '2022-05-29 21:43:16', ''),
(136, 27, 68, 1, '2022-05-29 21:43:16', ''),
(137, 27, 67, 1, '2022-05-29 21:43:16', ''),
(138, 27, 8, 1, '2022-05-29 21:43:16', ''),
(139, 27, 7, 1, '2022-05-29 21:43:16', ''),
(140, 27, 5, 1, '2022-05-29 21:43:16', ''),
(141, 27, 4, 1, '2022-05-29 21:43:16', ''),
(142, 27, 3, 1, '2022-05-29 21:43:16', ''),
(143, 27, 2, 1, '2022-05-29 21:43:16', ''),
(144, 28, 70, 1, '2022-05-29 21:46:45', ''),
(145, 28, 69, 3, '2022-05-29 21:46:45', '重複を確認しましたので、申請を承認いたします'),
(146, 28, 68, 1, '2022-05-29 21:46:45', ''),
(147, 28, 67, 1, '2022-05-29 21:46:45', ''),
(148, 28, 8, 1, '2022-05-29 21:46:45', ''),
(149, 28, 7, 1, '2022-05-29 21:46:45', ''),
(150, 28, 5, 1, '2022-05-29 21:46:45', ''),
(151, 28, 4, 1, '2022-05-29 21:46:45', ''),
(152, 28, 3, 1, '2022-05-29 21:46:45', ''),
(153, 28, 2, 1, '2022-05-29 21:46:45', ''),
(154, 28, 1, 1, '2022-05-29 21:46:45', ''),
(155, 29, 70, 2, '2022-05-29 21:47:08', ''),
(156, 29, 69, 4, '2022-05-29 21:47:08', '重複している場合、どちらかをお消しいたします。今回はどちらも申請いただきましたが、ID145を無効化し、ID156のこの問い合わせに関しては申請を拒否いたします。'),
(157, 29, 68, 1, '2022-05-29 21:47:08', ''),
(158, 29, 67, 1, '2022-05-29 21:47:08', ''),
(159, 29, 8, 1, '2022-05-29 21:47:08', ''),
(160, 29, 7, 1, '2022-05-29 21:47:08', ''),
(161, 29, 5, 1, '2022-05-29 21:47:08', ''),
(162, 29, 4, 1, '2022-05-29 21:47:08', ''),
(163, 29, 3, 1, '2022-05-29 21:47:08', ''),
(164, 29, 2, 1, '2022-05-29 21:47:08', ''),
(165, 29, 1, 1, '2022-05-29 21:47:08', '');

-- --------------------------------------------------------

--
-- テーブルの構造 `students_valid_status`
--

CREATE TABLE `students_valid_status` (
  `id` int NOT NULL,
  `vlid_status` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- テーブルのデータのダンプ `students_valid_status`
--

INSERT INTO `students_valid_status` (`id`, `vlid_status`) VALUES
(1, '正常'),
(2, '無効登録済'),
(3, '無効申請あり');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `admin_login`
--
ALTER TABLE `admin_login`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `agents`
--
ALTER TABLE `agents`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `agents_tags`
--
ALTER TABLE `agents_tags`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `agent_list_status`
--
ALTER TABLE `agent_list_status`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `filter_sorts`
--
ALTER TABLE `filter_sorts`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `filter_tags`
--
ALTER TABLE `filter_tags`
  ADD PRIMARY KEY (`tag_id`);

--
-- テーブルのインデックス `invalid_requests`
--
ALTER TABLE `invalid_requests`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `students_contacts`
--
ALTER TABLE `students_contacts`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `students_valid_status`
--
ALTER TABLE `students_valid_status`
  ADD PRIMARY KEY (`id`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `admin_login`
--
ALTER TABLE `admin_login`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- テーブルの AUTO_INCREMENT `agents`
--
ALTER TABLE `agents`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- テーブルの AUTO_INCREMENT `agents_tags`
--
ALTER TABLE `agents_tags`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=329;

--
-- テーブルの AUTO_INCREMENT `agent_list_status`
--
ALTER TABLE `agent_list_status`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- テーブルの AUTO_INCREMENT `filter_sorts`
--
ALTER TABLE `filter_sorts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- テーブルの AUTO_INCREMENT `filter_tags`
--
ALTER TABLE `filter_tags`
  MODIFY `tag_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- テーブルの AUTO_INCREMENT `invalid_requests`
--
ALTER TABLE `invalid_requests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- テーブルの AUTO_INCREMENT `students`
--
ALTER TABLE `students`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- テーブルの AUTO_INCREMENT `students_contacts`
--
ALTER TABLE `students_contacts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=166;

--
-- テーブルの AUTO_INCREMENT `students_valid_status`
--
ALTER TABLE `students_valid_status`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
