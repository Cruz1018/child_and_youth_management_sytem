/*
SQLyog Community v13.3.0 (64 bit)
MySQL - 11.4.3-MariaDB : Database - cyms
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


/*Table structure for table `announcements` */

SET FOREIGN_KEY_CHECKS=0;
-- Import your SQL file here
SET FOREIGN_KEY_CHECKS=1;
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `announcements`;

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `announcements` */

insert  into `announcements`(`id`,`title`,`description`,`image_path`,`created_at`) values 
(44,'Mental Health day','We are all inviting you to mental health day!!','uploads/360_F_534140463_IgCaQIwo1SscFB5oldx1DxOODFmR1Mhm.webp','2025-03-12 22:58:56'),
(46,'tirla','ndauewnd','uploads/360_F_534140463_IgCaQIwo1SscFB5oldx1DxOODFmR1Mhm.webp','2025-03-14 16:15:54'),
(47,'trial','aieuda','uploads/360_F_534140463_IgCaQIwo1SscFB5oldx1DxOODFmR1Mhm.webp','2025-03-14 16:21:40'),
(48,'awd','awdawd','uploads/360_F_534140463_IgCaQIwo1SscFB5oldx1DxOODFmR1Mhm.webp','2025-03-14 16:26:02'),
(50,'dwad','awdawd','uploads/360_F_534140463_IgCaQIwo1SscFB5oldx1DxOODFmR1Mhm.webp','2025-03-14 16:46:47'),
(51,'Trial announcement','Tryyaaa','','2025-03-14 16:50:46');

/*Table structure for table `comments` */
SET FOREIGN_KEY_CHECKS=1;
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `comments`;

CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `comments` */

insert  into `comments`(`id`,`post_id`,`content`,`image_path`,`created_at`) values 
(1,2,'Hello','uploads/comments/IMG_20240927_225920.jpg','2025-03-13 22:04:55'),
(2,2,'Hi',NULL,'2025-03-13 22:06:31'),
(3,2,'Hello','uploads/comments/IMG_20240927_225920.jpg','2025-03-13 22:06:45'),
(4,1,'Anyeong','uploads/comments/9de5b723-476d-4fe0-9fb7-472bd12e008a.jpg','2025-03-13 22:10:27'),
(5,1,'Eney',NULL,'2025-03-13 22:10:50'),
(6,1,'hello',NULL,'2025-03-13 22:13:17'),
(7,4,'Nothing to see here',NULL,'2025-03-13 23:11:16'),
(8,2,'Hi!',NULL,'2025-03-14 19:05:30'),
(9,4,'hi',NULL,'2025-03-14 19:23:27'),
(10,4,'hi',NULL,'2025-03-14 19:23:34'),
(11,5,'we',NULL,'2025-03-14 19:24:15'),
(12,5,'hello',NULL,'2025-03-14 20:18:31'),
(13,5,'hi',NULL,'2025-03-14 20:30:52');

/*Table structure for table `cy` */
SET FOREIGN_KEY_CHECKS=1;
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `cy`;

CREATE TABLE `cy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `age` int(100) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `guardian` varchar(255) DEFAULT NULL,
  `contacts` varchar(255) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `cy` */

insert  into `cy`(`id`,`name`,`lastname`,`age`,`location`,`guardian`,`contacts`,`tags`) values 
(28,'Rovick','Cruz',22,'Caloocan city','Me myself andI','09102471669','sports, arts'),
(29,'Emerald','Espenida',10,'Deparo','She herself and her ','09350290248','sports, music'),
(30,'Shiki','Totoo',5,'Di ko alam eh','Nawawala nga','09258572975','Music, theater, sports');

/*Table structure for table `events` */
SET FOREIGN_KEY_CHECKS=1;
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `events`;

CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_name` varchar(255) NOT NULL,
  `event_description` text DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `organizer_id` int(11) DEFAULT NULL,
  `proposal_file` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `events` */

/*Table structure for table `image` */
SET FOREIGN_KEY_CHECKS=1;
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `image`;

CREATE TABLE `image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image_path` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `image` */

insert  into `image`(`id`,`image_path`) values 
(1,'uploads/7e216714-1cc9-4b21-9e84-583805789b43.jpg'),
(2,'uploads/9de5b723-476d-4fe0-9fb7-472bd12e008a.jpg'),
(3,'uploads/9de5b723-476d-4fe0-9fb7-472bd12e008a.jpg');

/*Table structure for table `post_images` */
SET FOREIGN_KEY_CHECKS=1;
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `post_images`;

CREATE TABLE `post_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  CONSTRAINT `post_images_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `post_images` */

insert  into `post_images`(`id`,`post_id`,`image_path`) values 
(1,1,'uploads/IMG_20240927_225920.jpg'),
(2,2,'uploads/IMG_20240927_225920.jpg'),
(3,3,'uploads/360_F_534140463_IgCaQIwo1SscFB5oldx1DxOODFmR1Mhm.webp'),
(4,4,'uploads/'),
(5,5,'uploads/360_F_534140463_IgCaQIwo1SscFB5oldx1DxOODFmR1Mhm.webp');
SET FOREIGN_KEY_CHECKS=1;
SET FOREIGN_KEY_CHECKS=0;

/*Table structure for table `posts` */

DROP TABLE IF EXISTS `posts`;

CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `posts` */

insert  into `posts`(`id`,`content`,`created_at`) values 
(1,'dwew','2025-03-13 00:09:22'),
(2,'dwew','2025-03-13 00:12:10'),
(3,'Event Happening','2025-03-13 22:50:34'),
(4,'*bold* letters','2025-03-13 23:10:57'),
(5,'Trial','2025-03-13 23:18:43');

/*Table structure for table `programs` */
SET FOREIGN_KEY_CHECKS=1;
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `programs`;

CREATE TABLE `programs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_name` varchar(225) DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `participants` int(11) DEFAULT NULL,
  `location` varchar(225) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `programs` */
SET FOREIGN_KEY_CHECKS=1;
SET FOREIGN_KEY_CHECKS=0;
/*Table structure for table `user` */

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(120) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `user` */

insert  into `user`(`id`,`firstname`,`lastname`,`email`,`username`,`password`) values 
(8,'user1','try','hatdog@gmail.com','qwewq','$2y$10$SsXWxdoe9Si7miUXeFDKhOueGWhneAfkkOGBP/kBk1F0j5CTWQM0G'),
(9,'Emerald','Espenida','kaito4127@gmail.com','User','$2y$10$.RbUjp.xjtJH8uGl9b3xFuypFkFAXUkKigc8kL7uEkUp8yB1qQhmO'),
(10,'Rovick','Cruz','cruzrovick@gmail.com','Admin','$2y$10$n1lTFkBaIyd0Waspn4vC6.rC0beiYrOIjJpbduqQeAv1km2lh6EDy'),
(12,'Shiki','Totoo','hahah@gmail.com','user2','$2y$10$2P5Dy6YHH8XThfG6DJSu/.tBSnCcxO6jiaVROaRBD9gj6U9EP2aoK');

/*Table structure for table `user_points` */
SET FOREIGN_KEY_CHECKS=1;
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `user_points`;

CREATE TABLE `user_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `points` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_points_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `user_points` */

insert  into `user_points`(`id`,`user_id`,`points`) values 
(1,9,19),
(2,8,11),
(3,12,47);

/*Table structure for table `user_points_log` */
SET FOREIGN_KEY_CHECKS=1;
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `user_points_log`;

CREATE TABLE `user_points_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `points_change` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_points_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `user_points_log` */

insert  into `user_points_log`(`id`,`user_id`,`date`,`points_change`,`description`) values 
(1,9,'2025-03-14',0,''),
(2,8,'2025-03-14',0,''),
(3,12,'2025-03-14',0,''),
(4,8,'2025-03-14',3,'Points added'),
(5,9,'2025-03-14',3,'Points added'),
(6,12,'2025-03-14',3,'Points added'),
(7,12,'2025-03-14',8,'Points added'),
(8,12,'2025-03-14',5,'Points added');
SET FOREIGN_KEY_CHECKS=1;
SET FOREIGN_KEY_CHECKS=0;
/*Table structure for table `volunteer` */

DROP TABLE IF EXISTS `volunteer`;

CREATE TABLE `volunteer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `age` int(100) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `volunteer` */

insert  into `volunteer`(`id`,`name`,`age`,`location`) values 
(5,'dwd',2,'weq'),
(6,'qw',2,'we'),
(7,'qw',2,'we'),
(8,'we',2,'q');
SET FOREIGN_KEY_CHECKS=1;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
