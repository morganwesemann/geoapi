-- phpMyAdmin SQL Dump
-- version 4.4.10
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 08, 2017 at 01:09 AM
-- Server version: 5.5.42
-- PHP Version: 7.0.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `geoapi`
--

-- --------------------------------------------------------

--
-- Table structure for table `owner`
--

CREATE TABLE `owner` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(30) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `owner`
--

INSERT INTO `owner` (`id`, `name`, `type`) VALUES
(2, 'Donald Trump', 'Government'),
(3, 'Robert Kraft', 'person'),
(4, 't1', 'test'),
(5, 'Billy Bob Thornton', 'government'),
(6, 'Bob Burkins', 'Personal');

-- --------------------------------------------------------

--
-- Table structure for table `rainfall`
--

CREATE TABLE `rainfall` (
  `id` int(11) NOT NULL,
  `latitude` float(10,6) NOT NULL,
  `longitude` float(10,6) NOT NULL,
  `amount` float NOT NULL,
  `averageAmount` float NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rainfall`
--

INSERT INTO `rainfall` (`id`, `latitude`, `longitude`, `amount`, `averageAmount`, `date`) VALUES
(1, 12.340000, -36.799999, 1.3, 0.9, '2017-05-03 00:00:00'),
(2, 10.000000, 10.000000, 50, 15, '2013-05-08 12:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `recordings`
--

CREATE TABLE `recordings` (
  `transducerID` varchar(100) NOT NULL,
  `date` datetime NOT NULL,
  `temperature` float NOT NULL,
  `conductivity` float NOT NULL,
  `pressure` float NOT NULL,
  `salinity` float NOT NULL,
  `tds` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `recordings`
--

INSERT INTO `recordings` (`transducerID`, `date`, `temperature`, `conductivity`, `pressure`, `salinity`, `tds`) VALUES
('0987g', '0000-00-00 00:00:00', 67, 19, 65, 54, 18),
('0987g', '2015-05-08 12:34:15', 67, 19, 65, 54, 18),
('0987g', '2017-05-08 12:34:15', 67, 19, 65, 54, 18),
('0987g', '2017-05-09 22:34:00', 72, 1000, 8, 23, 3456);

-- --------------------------------------------------------

--
-- Table structure for table `transducer`
--

CREATE TABLE `transducer` (
  `id` varchar(100) NOT NULL,
  `type` varchar(30) NOT NULL,
  `name` varchar(100) NOT NULL,
  `wellID` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `transducer`
--

INSERT INTO `transducer` (`id`, `type`, `name`, `wellID`) VALUES
('0987g', 'test', 't1', '12335y');

-- --------------------------------------------------------

--
-- Table structure for table `well`
--

CREATE TABLE `well` (
  `id` varchar(100) NOT NULL,
  `acquiferCode` varchar(30) NOT NULL,
  `typeCode` varchar(1) NOT NULL,
  `ownerID` int(11) NOT NULL DEFAULT '0',
  `latitude` float(10,6) NOT NULL,
  `longitude` float(10,6) NOT NULL,
  `county` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `depth` float NOT NULL,
  `usageType` varchar(50) NOT NULL,
  `pump` varchar(3) NOT NULL,
  `pumpDescr` varchar(1024) DEFAULT NULL,
  `bottomElevation` float NOT NULL,
  `waterElevation` float NOT NULL,
  `surfaceElevation` float NOT NULL,
  `casingID` int(11) DEFAULT NULL,
  `diameter` float DEFAULT NULL,
  `topDepth` float DEFAULT NULL,
  `bottomDepth` float DEFAULT NULL,
  `additionalText` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `well`
--

INSERT INTO `well` (`id`, `acquiferCode`, `typeCode`, `ownerID`, `latitude`, `longitude`, `county`, `state`, `depth`, `usageType`, `pump`, `pumpDescr`, `bottomElevation`, `waterElevation`, `surfaceElevation`, `casingID`, `diameter`, `topDepth`, `bottomDepth`, `additionalText`) VALUES
('12335f', 'person', 'A', 2, 15.000000, -10.000000, 'McLennan', 'Texas', 10.5, 'well', 'yes', 'This is a pump yo', 10, 5, 7, NULL, NULL, NULL, NULL, NULL),
('12335y', 'test2', 'A', 2, 10.000000, -10.000000, 'McLennan', 'Texas', 5, 'well', 'yes', 'This is a pump yo', 10, 5, 7, NULL, 5, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `owner`
--
ALTER TABLE `owner`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rainfall`
--
ALTER TABLE `rainfall`
  ADD PRIMARY KEY (`id`,`latitude`,`longitude`,`date`) USING BTREE;

--
-- Indexes for table `recordings`
--
ALTER TABLE `recordings`
  ADD PRIMARY KEY (`transducerID`,`date`) USING BTREE;

--
-- Indexes for table `transducer`
--
ALTER TABLE `transducer`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `wellConstraint` (`wellID`);

--
-- Indexes for table `well`
--
ALTER TABLE `well`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `ownerConstraint` (`ownerID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `owner`
--
ALTER TABLE `owner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `rainfall`
--
ALTER TABLE `rainfall`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `recordings`
--
ALTER TABLE `recordings`
  ADD CONSTRAINT `transducerConstraint` FOREIGN KEY (`transducerID`) REFERENCES `transducer` (`id`);

--
-- Constraints for table `transducer`
--
ALTER TABLE `transducer`
  ADD CONSTRAINT `wellConstraint` FOREIGN KEY (`wellID`) REFERENCES `well` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
