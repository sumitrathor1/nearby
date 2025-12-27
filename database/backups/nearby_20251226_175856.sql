/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `accommodations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accommodations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `title` varchar(180) NOT NULL,
  `type` enum('PG','Flat','Room','Hostel') NOT NULL,
  `allowed_for` enum('Male','Female','Family') NOT NULL,
  `rent` int(10) unsigned NOT NULL,
  `location` varchar(255) NOT NULL,
  `facilities` text DEFAULT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_accommodations_user` (`user_id`),
  CONSTRAINT `fk_accommodations_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `accommodations` WRITE;
/*!40000 ALTER TABLE `accommodations` DISABLE KEYS */;
/*!40000 ALTER TABLE `accommodations` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `contact_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_requests` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `accommodation_id` int(10) unsigned NOT NULL,
  `requester_id` int(10) unsigned NOT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_contact_accommodation` (`accommodation_id`),
  KEY `fk_contact_requester` (`requester_id`),
  CONSTRAINT `fk_contact_accommodation` FOREIGN KEY (`accommodation_id`) REFERENCES `accommodations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_contact_requester` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `contact_requests` WRITE;
/*!40000 ALTER TABLE `contact_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_requests` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `guidance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `guidance` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(120) NOT NULL,
  `title` varchar(180) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `guidance` WRITE;
/*!40000 ALTER TABLE `guidance` DISABLE KEYS */;
/*!40000 ALTER TABLE `guidance` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `post_category` enum('room','service') NOT NULL,
  `service_type` enum('tiffin','gas','milk','sabji','other') DEFAULT NULL,
  `accommodation_type` enum('PG','Flat','Room','Hostel') DEFAULT NULL,
  `allowed_for` enum('Male','Female','Family') DEFAULT NULL,
  `rent_or_price` int(10) unsigned DEFAULT NULL,
  `location` varchar(255) NOT NULL,
  `facilities` text DEFAULT NULL,
  `availability_time` varchar(120) DEFAULT NULL,
  `description` text NOT NULL,
  `contact_phone` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_posts_user` (`user_id`),
  KEY `idx_post_category` (`post_category`),
  KEY `idx_post_service_type` (`service_type`),
  KEY `idx_post_location` (`location`(100)),
  CONSTRAINT `fk_posts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=202 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` (`id`, `user_id`, `post_category`, `service_type`, `accommodation_type`, `allowed_for`, `rent_or_price`, `location`, `facilities`, `availability_time`, `description`, `contact_phone`, `created_at`) VALUES (1,2,'room',NULL,'Flat','Male',15000,'Gole ka mandir, gwalior','Wi-Fi,Food,Water,Electricity,Parking,CCTV,Power Backup',NULL,'Gole ka mandir, gwalior property','7566868709','2025-12-26 16:40:23'),(102,1,'room',NULL,'PG','Male',5500,'Navrangpura','WiFi, AC, Laundry','Immediate','Best PG for students','9825012345','2025-12-26 16:45:37'),(103,2,'service','tiffin',NULL,NULL,3000,'Satellite','Veg, Non-Veg','10 AM - 8 PM','Homemade healthy food','9825012346','2025-12-26 16:45:37'),(104,1,'room',NULL,'Flat','Family',18000,'Vastrapur','Parking, Lift, Security','Available now','3BHK semi-furnished flat','9825012347','2025-12-26 16:45:37'),(105,2,'service','gas',NULL,NULL,950,'Bopal','Fast delivery','9 AM - 6 PM','New gas connection provider','9825012348','2025-12-26 16:45:37'),(106,1,'room',NULL,'Hostel','Female',7000,'C.G. Road','CCTV, Warden, Food','Immediate','Safe hostel for girls','9825012349','2025-12-26 16:45:37'),(107,2,'service','milk',NULL,NULL,65,'Ghatlodia','Pure Buffalo Milk','Morning','Daily milk delivery','9825012350','2025-12-26 16:45:37'),(108,1,'room',NULL,'Room','Male',4500,'Paldi','Bed, Cupboard','Immediate','Single room for students','9825012351','2025-12-26 16:45:37'),(109,2,'service','sabji',NULL,NULL,150,'Maninagar','Organic','Morning','Fresh farm vegetables','9825012352','2025-12-26 16:45:37'),(110,1,'room',NULL,'PG','Female',6000,'Gurukul','Cleaning, Food','Next week','Well maintained PG','9825012353','2025-12-26 16:45:37'),(111,2,'service','other',NULL,NULL,500,'Thaltej','Cleaning','Flexible','Home cleaning service','9825012354','2025-12-26 16:45:37'),(112,1,'room',NULL,'Flat','Male',12000,'Prahlad Nagar','Gym, Pool','Immediate','Luxury flat sharing','9825012355','2025-12-26 16:45:37'),(113,2,'service','tiffin',NULL,NULL,2500,'Naranpura','Pure Veg','Lunch only','Gujarati Thali available','9825012356','2025-12-26 16:45:37'),(114,1,'room',NULL,'Room','Family',9000,'Sabarmati','Ground floor','Immediate','Independent house room','9825012357','2025-12-26 16:45:37'),(115,2,'service','milk',NULL,NULL,62,'Chandkheda','Cow Milk','Morning/Evening','A2 Quality milk','9825012358','2025-12-26 16:45:37'),(116,1,'room',NULL,'Hostel','Male',5000,'Gota','WiFi, Gym','Immediate','Boys hostel near highway','9825012359','2025-12-26 16:45:37'),(117,2,'service','gas',NULL,NULL,1100,'Vatva','Same day service','24/7','Gas repair and booking','9825012360','2025-12-26 16:45:37'),(118,1,'room',NULL,'PG','Family',10000,'Nikol','All inclusive','Immediate','PG for small families','9825012361','2025-12-26 16:45:37'),(119,2,'service','sabji',NULL,NULL,100,'Naroda','Daily Fresh','Morning','Sabji mandi rates','9825012362','2025-12-26 16:45:37'),(120,1,'room',NULL,'Flat','Female',14000,'Bodakdev','Fully Furnished','1st Dec','Premium flat for girls','9825012363','2025-12-26 16:45:37'),(121,2,'service','other',NULL,NULL,300,'Ranip','Plumbing','Anytime','Fast plumbing service','9825012364','2025-12-26 16:45:37'),(122,1,'room',NULL,'PG','Male',4800,'Odhav','Basic','Immediate','Affordable PG','9825012365','2025-12-26 16:45:37'),(123,2,'service','tiffin',NULL,NULL,2800,'Ashram Road','Custom Menu','Dinner','Student special tiffin','9825012366','2025-12-26 16:45:37'),(124,1,'room',NULL,'Room','Female',5500,'Memnagar','Safe area','Immediate','Private room in apartment','9825012367','2025-12-26 16:45:37'),(125,2,'service','milk',NULL,NULL,70,'Ambawadi','Desi Cow','Morning','Fresh dairy products','9825012368','2025-12-26 16:45:37'),(126,1,'room',NULL,'Hostel','Female',8500,'Law Garden','Full Security','Available','Executive girls hostel','9825012369','2025-12-26 16:45:37'),(127,2,'service','gas',NULL,NULL,900,'Sarkhej','Emergency','Day shift','Gas leakage repair','9825012370','2025-12-26 16:45:37'),(128,1,'room',NULL,'Flat','Family',22000,'Sindhu Bhavan','Luxury','Immediate','High-end 3BHK','9825012371','2025-12-26 16:45:37'),(129,2,'service','sabji',NULL,NULL,200,'Bopal','Exotic Veg','Weekend','Salad and fruits','9825012372','2025-12-26 16:45:37'),(130,1,'room',NULL,'PG','Male',5200,'Motera','AC Room','Next month','Cricket stadium view PG','9825012373','2025-12-26 16:45:37'),(131,2,'service','other',NULL,NULL,150,'Chandlodia','Electrical','Anytime','Electrician on call','9825012374','2025-12-26 16:45:37'),(132,1,'room',NULL,'Room','Male',3500,'Aslali','Basic','Immediate','Cheap room for workers','9825012375','2025-12-26 16:45:37'),(133,2,'service','tiffin',NULL,NULL,3500,'Drive-in Road','North Indian','Full day','Authentic Punjabi food','9825012376','2025-12-26 16:45:37'),(134,1,'room',NULL,'Flat','Female',16000,'South Bopal','Clubhouse','Immediate','Modern living for ladies','9825012377','2025-12-26 16:45:37'),(135,2,'service','milk',NULL,NULL,58,'Isanpur','Bulk supply','Morning','Milk for shops and homes','9825012378','2025-12-26 16:45:37'),(136,1,'room',NULL,'Hostel','Male',6500,'Navrangpura','Library','Immediate','Hostel with study room','9825012379','2025-12-26 16:45:37'),(137,2,'service','gas',NULL,NULL,1050,'Vasna','Online booking','24/7','Gas refill service','9825012380','2025-12-26 16:45:37'),(138,1,'room',NULL,'PG','Female',7500,'Nehrunagar','High speed internet','Immediate','Digital nomad friendly PG','9825012381','2025-12-26 16:45:37'),(139,2,'service','sabji',NULL,NULL,120,'Juhapura','Fresh','Morning','Best quality sabji','9825012382','2025-12-26 16:45:37'),(140,1,'room',NULL,'Room','Family',8000,'Lambha','Spacious','Immediate','Big room for family','9825012383','2025-12-26 16:45:37'),(141,2,'service','other',NULL,NULL,1000,'Shilaj','Gardening','Weekly','Expert garden maintenance','9825012384','2025-12-26 16:45:37'),(142,1,'room',NULL,'Flat','Male',11000,'Vejalpur','Semi furnished','Next week','Sharing flat for bachelors','9825012385','2025-12-26 16:45:37'),(143,2,'service','tiffin',NULL,NULL,2200,'Khanpur','Diet food','Lunch','Weight loss tiffin','9825012386','2025-12-26 16:45:37'),(144,1,'room',NULL,'PG','Male',4500,'Kalupur','Near Station','Immediate','Convenient for travelers','9825012387','2025-12-26 16:45:37'),(145,2,'service','milk',NULL,NULL,64,'Shahibaug','Fresh','Morning','Quality assurance','9825012388','2025-12-26 16:45:37'),(146,1,'room',NULL,'Hostel','Female',9000,'Ellisbridge','AC, Geyser','Immediate','Comfortable stay','9825012389','2025-12-26 16:45:37'),(147,2,'service','gas',NULL,NULL,1200,'Dani Limda','Quick','Day','Stove repair','9825012390','2025-12-26 16:45:37'),(148,1,'room',NULL,'Flat','Family',25000,'Bodakdev','Fully AC','Immediate','Luxurious penthouse','9825012391','2025-12-26 16:45:37'),(149,2,'service','sabji',NULL,NULL,80,'Gomtipur','Local','Morning','Cheap vegetables','9825012392','2025-12-26 16:45:37'),(150,1,'room',NULL,'Room','Male',4200,'Hatkeshwar','Attached Bath','Immediate','Simple room','9825012393','2025-12-26 16:45:37'),(151,2,'service','other',NULL,NULL,400,'Usmanpura','Carpentry','Flexible','Furniture repair','9825012394','2025-12-26 16:45:37'),(152,1,'room',NULL,'PG','Female',6800,'Navrangpura','Food, Bed','Available','Homely PG','9825012395','2025-12-26 16:45:37'),(153,2,'service','tiffin',NULL,NULL,2700,'Paldi','Jain Food','Full day','Pure Jain tiffin service','9825012396','2025-12-26 16:45:37'),(154,1,'room',NULL,'Flat','Male',13500,'Ambli','High security','Immediate','Bachelors sharing flat','9825012397','2025-12-26 16:45:37'),(155,2,'service','milk',NULL,NULL,66,'Science City','Organic','6 AM','Top quality milk','9825012398','2025-12-26 16:45:37'),(156,1,'room',NULL,'Hostel','Male',5500,'Chandkheda','Play area','Immediate','Modern boys hostel','9825012399','2025-12-26 16:45:37'),(157,2,'service','gas',NULL,NULL,980,'Gota','New Pipe','10 AM','Gas safety check','9825012400','2025-12-26 16:45:37'),(158,1,'room',NULL,'PG','Male',5000,'Makarba','WiFi','Immediate','IT employees PG','9825012401','2025-12-26 16:45:37'),(159,2,'service','sabji',NULL,NULL,130,'Jodhpur','Sorted','Morning','Ready to cook cut veggies','9825012402','2025-12-26 16:45:37'),(160,1,'room',NULL,'Room','Female',6000,'Vastrapur','Terrace access','Immediate','Safe room for single girl','9825012403','2025-12-26 16:45:37'),(161,2,'service','other',NULL,NULL,600,'Sola','AC Repair','Anytime','Best AC technician','9825012404','2025-12-26 16:45:37'),(162,1,'room',NULL,'Flat','Family',17000,'New CG Road','Modern','Jan 1st','Spacious 2BHK','9825012405','2025-12-26 16:45:37'),(163,2,'service','tiffin',NULL,NULL,3200,'Income Tax','Luxury Thali','Full day','Premium lunch service','9825012406','2025-12-26 16:45:37'),(164,1,'room',NULL,'PG','Female',7200,'Commerce Six Road','Food Included','Immediate','Girls PG near college','9825012407','2025-12-26 16:45:37'),(165,2,'service','milk',NULL,NULL,60,'Sarkhej','Direct from farm','Morning','Fresh farm milk','9825012408','2025-12-26 16:45:37'),(166,1,'room',NULL,'Hostel','Female',7800,'Naranpura','Security','Immediate','Quiet study environment','9825012409','2025-12-26 16:45:37'),(167,2,'service','gas',NULL,NULL,1000,'Vatva','Booking','Day','Gas cylinder delivery','9825012410','2025-12-26 16:45:37'),(168,1,'room',NULL,'Flat','Male',15000,'Satellite','Fully loaded','Immediate','High speed internet flat','9825012411','2025-12-26 16:45:37'),(169,2,'service','sabji',NULL,NULL,110,'Ognaj','Wholesale','Morning','Bulk orders accepted','9825012412','2025-12-26 16:45:37'),(170,1,'room',NULL,'Room','Family',7500,'Bapunagar','Parking','Immediate','Budget room for family','9825012413','2025-12-26 16:45:37'),(171,2,'service','other',NULL,NULL,200,'Amraiwadi','Laundry','Weekly','Cloth wash and press','9825012414','2025-12-26 16:45:37'),(172,1,'room',NULL,'PG','Male',4900,'Khadia','Basic','Immediate','Old city PG','9825012415','2025-12-26 16:45:37'),(173,2,'service','tiffin',NULL,NULL,2400,'Astodia','Home taste','Lunch','Affordable tiffin','9825012416','2025-12-26 16:45:37'),(174,1,'room',NULL,'Flat','Female',19000,'Prahlad Nagar','Gated Community','Immediate','Safe flat for ladies','9825012417','2025-12-26 16:45:37'),(175,2,'service','milk',NULL,NULL,68,'Jetalpur','Pure Cow','Morning','Chemical free milk','9825012418','2025-12-26 16:45:37'),(176,1,'room',NULL,'Hostel','Male',5200,'Asarwa','Canteen','Immediate','Student hostel','9825012419','2025-12-26 16:45:37'),(177,2,'service','gas',NULL,NULL,1150,'Narol','Emergency','Night','24hr Gas repair','9825012420','2025-12-26 16:45:37'),(178,1,'room',NULL,'PG','Family',12000,'Memnagar','Full Floor','Immediate','Independent PG unit','9825012421','2025-12-26 16:45:37'),(179,2,'service','sabji',NULL,NULL,140,'Shahpur','Seasonal','Day','All seasonal vegetables','9825012422','2025-12-26 16:45:37'),(180,1,'room',NULL,'Room','Male',3800,'Rakhial','Table/Chair','Immediate','Study room for boys','9825012423','2025-12-26 16:45:37'),(181,2,'service','other',NULL,NULL,450,'Jamalpur','Painter','Flexible','Home painting service','9825012424','2025-12-26 16:45:37'),(182,1,'room',NULL,'Flat','Family',30000,'SG Highway','Penthouse','Immediate','Ultra luxury living','9825012425','2025-12-26 16:45:37'),(183,2,'service','tiffin',NULL,NULL,3100,'Shyamal','Rajasthani','Full day','Dal Bati special','9825012426','2025-12-26 16:45:37'),(184,1,'room',NULL,'PG','Female',8000,'Navrangpura','AC, WiFi','Immediate','Premium girls PG','9825012427','2025-12-26 16:45:37'),(185,2,'service','milk',NULL,NULL,63,'New Ranip','Daily','Morning','Packet milk delivery','9825012428','2025-12-26 16:45:37'),(186,1,'room',NULL,'Hostel','Female',6200,'Vasna','Food','Immediate','Working women hostel','9825012429','2025-12-26 16:45:37'),(187,2,'service','gas',NULL,NULL,1020,'Vejalpur','Regular','Day','LPG connection help','9825012430','2025-12-26 16:45:37'),(188,1,'room',NULL,'Room','Male',4000,'Dudheshwar','Simple','Immediate','Budget room','9825012431','2025-12-26 16:45:37'),(189,2,'service','sabji',NULL,NULL,160,'South Bopal','Cleaned','Morning','Peeled and cut veg','9825012432','2025-12-26 16:45:37'),(190,1,'room',NULL,'Flat','Male',11500,'Thaltej','Bachelors','Next month','3 Sharing flat','9825012433','2025-12-26 16:45:37'),(191,2,'service','other',NULL,NULL,700,'Gurukul','Pest Control','Weekend','Ant and termite control','9825012434','2025-12-26 16:45:37'),(192,1,'room',NULL,'PG','Male',5600,'Satellite','TV, AC','Immediate','Executive PG for men','9825012435','2025-12-26 16:45:37'),(193,2,'service','tiffin',NULL,NULL,2900,'C.G. Road','South Indian','Full day','Idli Dosa special','9825012436','2025-12-26 16:45:37'),(194,1,'room',NULL,'Room','Female',5800,'Drive-in','Attached Balcony','Immediate','Ventilated room','9825012437','2025-12-26 16:45:37'),(195,2,'service','milk',NULL,NULL,67,'Science City','Paneer/Ghee','Morning','Dairy products home delivery','9825012438','2025-12-26 16:45:37'),(196,1,'room',NULL,'Hostel','Male',4800,'Gota','Sports','Available','Hostel with ground','9825012439','2025-12-26 16:45:37'),(197,2,'service','gas',NULL,NULL,1080,'Bodakdev','Fast','24/7','Gas station helper','9825012440','2025-12-26 16:45:37'),(198,1,'room',NULL,'Flat','Family',16000,'Chandkheda','Garden Facing','Immediate','Beautiful view flat','9825012441','2025-12-26 16:45:37'),(199,2,'service','sabji',NULL,NULL,180,'Sindhu Bhavan','Exotic','Morning','Broccoli, Avocado etc','9825012442','2025-12-26 16:45:37'),(200,1,'room',NULL,'PG','Female',6400,'Navrangpura','Kitchen access','Immediate','Self cooking allowed PG','9825012443','2025-12-26 16:45:37'),(201,2,'service','other',NULL,NULL,350,'Ranip','RO Repair','Day','Water purifier service','9825012444','2025-12-26 16:45:37');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `college_email` varchar(160) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('junior','senior') NOT NULL,
  `user_type` enum('student','owner','service_provider') NOT NULL DEFAULT 'student',
  `user_category` enum('student','home_owner','room_owner','tiffin','gas','milk','sabji','other_service') NOT NULL DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `college_email` (`college_email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `name`, `college_email`, `password`, `role`, `user_type`, `user_category`, `created_at`) VALUES (1,'Sumit Rathor','dfdfdf@mitsgwl.ac.in','$2y$10$319UnuAVTFzn/EzrfoV3kOzx14.rJVWdoOBR.1NAP3wSUCPl1wMG.','senior','service_provider','gas','2025-12-26 10:59:09'),(2,'Sumit Rathor','sumitrathor142272@gmail.com','$2y$10$cOBWwHO4tZPnS9Ws54QW0OM/mofwtUKIJXyEI5EjRf.VG8cLhNvuW','senior','service_provider','student','2025-12-26 11:53:06');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
