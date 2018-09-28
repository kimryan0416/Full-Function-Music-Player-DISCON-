-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Sep 28, 2018 at 05:16 PM
-- Server version: 5.6.38
-- PHP Version: 7.2.1

SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Music_Player`
--

-- --------------------------------------------------------

--
-- Table structure for table `Music`
--

CREATE TABLE IF NOT EXISTS "Music" ;

--
-- Dumping data for table `Music`
--

INSERT INTO `Music` (`music_id`, `title`, `artist`, `album`, `album_artist`, `url`, `file_name`, `composer`, `year`, `rating`, `favorite`, `play_count`, `comments`, `length`) VALUES
(6, 'Sekiranun Graffiti (feat. Hatsune Miku)', 'Ryo (Supercell)', 'Ryo (Supercell)', 'Vocaloids', 'media/music/Ryo_Supercell/Sekiranun_Graffiti_feat_Hatsune_Miku/', 'Sekiranun_Graffiti_feat_Hatsune_Miku.m4a', 'Ryo (Supercell)', 2011, NULL, NULL, 9, '', '3:35'),
(9, '누덕누덕 스타카토 [Patchwork Staccato] [Kor. Cover]', '지라라', '지라라', 'Vocaloids', 'media/music/지라라/누덕누덕_스타카토_Patchwork_Staccato_Kor_Cover/', '누덕누덕_스타카토_Patchwork_Staccato_Kor_Cover.m4a', 'Toa', 2017, NULL, NULL, 14, '', '4:09'),
(11, 'Sick Sick Sick (feat. Hatsune Miku)', 'PinocchioP', 'PinocchioP', 'Vocaloids', 'media/music/PinocchioP/Sick_Sick_Sick_feat_Hatsune_Miku/', 'Sick_Sick_Sick_feat_Hatsune_Miku.m4a', 'PinocchioP', 2017, NULL, NULL, 4, 'Producer&#39;s Comments:\r\n&#34;This year I had a medical checkup for the first time.&#34; ', '3:53'),
(20, 'Patchwork Staccato', 'nameless', 'TOA', 'Vocaloids', 'media/music/nameless/Patchwork_Staccato/', 'Patchwork_Staccato.m4a', 'TOA', 2015, NULL, NULL, 0, 'Original Album:\r\n212', '4:08');

-- --------------------------------------------------------

--
-- Table structure for table `Playlist`
--

CREATE TABLE IF NOT EXISTS "Playlist" ;

--
-- Dumping data for table `Playlist`
--

INSERT INTO `Playlist` (`playlist_id`, `playlist_title`, `playlist_description`, `user_id`) VALUES
(1, 'First Playlist', NULL, 1),
(2, 'Second Playlist  ', 'My second playlist - this is a test', 1);

-- --------------------------------------------------------

--
-- Table structure for table `playlistmusicmap`
--

CREATE TABLE IF NOT EXISTS "playlistmusicmap" ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
