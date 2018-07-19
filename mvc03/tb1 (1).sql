-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 2017-04-28 08:56:37
-- 服务器版本： 5.7.14
-- PHP Version: 5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db0425`
--

-- --------------------------------------------------------

--
-- 表的结构 `tb1`
--

CREATE TABLE `tb1` (
  `id` int(11) NOT NULL,
  `title` varchar(20) NOT NULL,
  `content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `tb1`
--

INSERT INTO `tb1` (`id`, `title`, `content`) VALUES
(7, '测试0427', '测试内容0428'),
(8, '测试0427', '测试内容0428'),
(11, '测试0427', '测试内容0428'),
(12, '测试0427', '测试内容0428'),
(13, '测试0427', '测试内容0429'),
(14, '测试0427', '测试内容0428'),
(15, '测试0427', '测试内容0428'),
(17, '测试0427', '测试内容0428'),
(18, '测试0427', '测试内容0428'),
(19, '测试0427', '测试内容0428'),
(20, '测试0427', '测试内容0428'),
(21, '测试0427', '测试内容0428'),
(22, '测试0427', '测试内容0428'),
(23, '测试0427', '测试内容0428'),
(24, '测试0427', '测试内容0428'),
(25, '测试0427', '测试内容0428'),
(26, '测试0427', '测试内容0428'),
(27, '测试0427', '测试内容0428'),
(28, '测试0427', '测试内容0428'),
(29, '测试0427', '测试内容0428'),
(30, '测试0427', '测试内容0428'),
(31, '测试0427', '测试内容0428'),
(32, '测试0427', '测试内容0428'),
(33, '测试0427', '测试内容0428'),
(34, '测试0427', '测试内容0428'),
(35, '测试0427', '测试内容0428'),
(36, '测试0427', '测试内容0428'),
(37, '测试0427', '测试内容0428'),
(38, '测试0427', '测试内容0428'),
(39, '测试0427', '测试内容0428'),
(40, '测试0427', '测试内容0428'),
(41, '测试0427', '测试内容0428'),
(42, '测试0427', '测试内容0428'),
(43, '测试0427', '测试内容0428'),
(44, '测试0427', '测试内容0428'),
(45, '测试0427', '测试内容0428'),
(46, 'xin', 'xing');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb1`
--
ALTER TABLE `tb1`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `tb1`
--
ALTER TABLE `tb1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
