-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: citygatefarm
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `appointment_attendees`
--

DROP TABLE IF EXISTS `appointment_attendees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appointment_attendees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appointment_id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `age` varchar(20) DEFAULT NULL,
  `contact` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `appointment_id` (`appointment_id`),
  CONSTRAINT `appointment_attendees_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appointment_attendees`
--

LOCK TABLES `appointment_attendees` WRITE;
/*!40000 ALTER TABLE `appointment_attendees` DISABLE KEYS */;
INSERT INTO `appointment_attendees` VALUES (1,2,'Immaculate Aciro','Teacher','41','+256 772 441 220'),(2,2,'Brian Okot','Student','16',''),(3,2,'Fiona Adong','Student','16',''),(4,3,'Denis Ojok','Teacher','38','+256 782 110 933'),(5,4,'Patricia Lamwaka','Lecturer','—','+256 700 556 812'),(6,4,'Solomon Ebong','Student','23',''),(7,4,'Ruth Nabirye','Student','22',''),(8,9,'Betty Achola','Teacher','34','+256 772 884 502'),(9,9,'Moses Ebulu','Teacher','29','');
/*!40000 ALTER TABLE `appointment_attendees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appointments`
--

DROP TABLE IF EXISTS `appointments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institution` varchar(190) DEFAULT NULL,
  `contact_name` varchar(150) NOT NULL,
  `contact_phone` varchar(40) NOT NULL,
  `contact_email` varchar(190) NOT NULL,
  `purpose` varchar(60) NOT NULL,
  `visit_date` date DEFAULT NULL,
  `visit_time` varchar(30) DEFAULT NULL,
  `num_visitors` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected','Arrived') NOT NULL DEFAULT 'Pending',
  `checked_in_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appointments`
--

LOCK TABLES `appointments` WRITE;
/*!40000 ALTER TABLE `appointments` DISABLE KEYS */;
INSERT INTO `appointments` VALUES (2,'Comboni College Lira','Sr. Immaculate Aciro','+256 772 441 220','aciro@combonilira.ac.ug','school-trip','2026-08-18','morning',35,'S3 Agriculture class study tour — would like a poultry-house walkthrough.','Pending',NULL,'2026-08-08 08:06:28'),(3,'St. Katherine Girls SS','Mr. Denis Ojok','+256 782 110 933','denis.ojok@skgss.sc.ug','farm-tour','2026-08-22','afternoon',28,'Career-day excursion, interested in dairy and crops sections.','Approved',NULL,'2026-08-04 08:06:28'),(4,'Gulu University — Agriculture Dept.','Dr. Patricia Lamwaka','+256 700 556 812','p.lamwaka@gu.ac.ug','study','2026-08-15','full-day',12,'Final-year students researching mixed-farm production models.','Arrived','2026-08-11 19:00:14','2026-08-01 08:06:28'),(5,NULL,'James Okwir','+256 752 903 471','j.okwir@gmail.com','purchase','2026-08-12','morning',2,'Would like to inspect Boer goats before a bulk purchase.','Pending',NULL,'2026-08-09 08:06:28'),(6,'Northern Uganda Youth Agripreneurs','Grace Amito','+256 772 660 145','grace.amito@nuya.org','training','2026-08-27','full-day',18,'Requesting the 1-day poultry management training package for our cohort.','Pending',NULL,'2026-08-07 08:06:28'),(7,'Lira City Council — Production Office','Mr. Vincent Ocen','+256 782 214 667','vincent.ocen@liracity.go.ug','partnership','2026-07-30','afternoon',4,'Discussing an extension-services partnership with the district.','Arrived','2026-07-27 08:06:28','2026-07-27 08:06:28'),(8,'Kakebe Technologies (Internship)','Sarah Nansubuga','+256 702 337 990','sarah.n@kakebetech.com','internship','2026-07-25','full-day',1,'6-week industrial training placement, agribusiness track.','Arrived','2026-07-23 08:06:28','2026-07-23 08:06:28'),(9,'Bishop Negri Primary School','Ms. Betty Achola','+256 772 884 502','betty.achola@bnps.sc.ug','school-trip','2026-07-18','morning',42,'P6/P7 field trip — the kids are most excited about the chicks!','Arrived','2026-07-17 08:06:28','2026-07-17 08:06:28'),(10,NULL,'Robert Ochola','+256 793 214 008','robert.ochola@yahoo.com','other','2026-07-10','',3,'Requested a Saturday afternoon slot, we had no availability that week.','Rejected',NULL,'2026-07-09 08:06:28'),(11,'Unity Farmers SACCO','Mrs. Christine Auma','+256 772 550 129','c.auma@unitysacco.co.ug','partnership','2026-07-05','afternoon',6,'SACCO members interested in a milk off-take agreement — bad weather forced a reschedule we never confirmed.','Rejected',NULL,'2026-07-04 08:06:28'),(12,'Starlight Nursery & Primary','Ms. Winnie Aketch','+256 774 302 981','winnie@starlightschool.ug','school-trip','2026-09-02','morning',50,'Annual farm day for P4-P7.','Pending',NULL,'2026-08-10 08:06:28'),(13,'dfad','Kakebe Technologies Limited','+256786462741','kakebetech.comms@gmail.com','partnership','2026-08-10','afternoon',1,NULL,'Arrived','2026-08-10 13:57:01','2026-08-10 13:56:47'),(14,NULL,'QA Tester','0700000000','qa.tester@example.com','farm-tour','2026-09-01',NULL,NULL,NULL,'Pending',NULL,'2026-08-11 20:01:40');
/*!40000 ALTER TABLE `appointments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_comments`
--

DROP TABLE IF EXISTS `blog_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(190) DEFAULT NULL,
  `comment` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  CONSTRAINT `blog_comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_comments`
--

LOCK TABLES `blog_comments` WRITE;
/*!40000 ALTER TABLE `blog_comments` DISABLE KEYS */;
INSERT INTO `blog_comments` VALUES (1,1,'QA Tester','qa@example.com','This is a test comment from automated verification.','2026-08-10 08:12:42'),(2,1,'Browser QA Agent','browserqa@example.com','Second verification comment submitted via curl PRG test.','2026-08-10 08:24:06'),(3,1,'Michael Ojara','michael.o@gmail.com','Impressive growth! Would love to see a follow-up on your feed conversion ratio.','2026-07-21 08:06:28'),(4,1,'Agnes N.',NULL,'We toured the poultry houses last month — exactly as described here. Very clean operation.','2026-07-24 08:06:28'),(5,2,'Dr. Komakech','komakech@health.go.ug','Great, simple explanation. Sharing this with our community health volunteers.','2026-07-27 08:06:28'),(6,4,'Farmer Richard',NULL,'Been thinking about intercropping my own plot — how far apart did you space the banana rows?','2026-07-30 08:06:28'),(7,5,'Susan A.','susan.a@yahoo.com','Your milk is the best in Lira, hands down!','2026-08-02 08:06:28'),(8,1,'Structural Cleanup QA',NULL,'Verifying comment form still works after banner/style cleanup.','2026-08-11 19:59:39');
/*!40000 ALTER TABLE `blog_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_post_images`
--

DROP TABLE IF EXISTS `blog_post_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog_post_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  CONSTRAINT `blog_post_images_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_post_images`
--

LOCK TABLES `blog_post_images` WRITE;
/*!40000 ALTER TABLE `blog_post_images` DISABLE KEYS */;
INSERT INTO `blog_post_images` VALUES (4,4,'images/gallary/christoph-coffee-171653_1920.jpg','Coffee cherries ripening under banana shade',0),(5,4,'images/gallary/marcusvu-coffee-2992598_1920.jpg','Rows of intercropped coffee and banana',1),(6,4,'images/gallary/hat3m-seedling-4394118_1920.jpg','New seedlings ready for the next plot',2);
/*!40000 ALTER TABLE `blog_post_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_posts`
--

DROP TABLE IF EXISTS `blog_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(220) NOT NULL,
  `slug` varchar(220) NOT NULL,
  `category` varchar(80) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `status` enum('draft','published','scheduled') NOT NULL DEFAULT 'draft',
  `publish_date` date DEFAULT NULL,
  `meta_title` varchar(220) DEFAULT NULL,
  `meta_description` varchar(320) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `author_id` (`author_id`),
  CONSTRAINT `blog_posts_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_posts`
--

LOCK TABLES `blog_posts` WRITE;
/*!40000 ALTER TABLE `blog_posts` DISABLE KEYS */;
INSERT INTO `blog_posts` VALUES (1,'Scaling From 14,000 to 100,000 Birds','scaling-from-14000-to-100000-birds','Poultry','poultry,growth,broiler','How we grew our broiler operation while keeping bird health and housing standards high.','<p>Over the last two seasons we scaled our broiler operation from 14,000 to 100,000 birds by investing in clean housing, organized feeding schedules, and staff training.</p><p>The keys were phased expansion, disease-testing infrastructure, and never compromising on brooding conditions.</p>','images/gallary/9883074-chicken-3727097_1920.jpg',2,'published','2026-03-25','Scaling From 14,000 to 100,000 Birds | City Gate Mixed Farm','How City Gate Mixed Farm scaled its broiler operation while keeping bird health and housing standards high in Lira City, Uganda.','2026-08-10 00:38:46','2026-08-11 22:28:29'),(2,'Know the Benefit of a Boiled Egg','know-the-benefit-of-a-boiled-egg','Nutrition','eggs,health,nutrition','A closer look at why fresh farm eggs are a simple, affordable source of daily protein.','<p>A boiled egg is one of the most affordable, nutrient-dense foods available to Northern Ugandan households.</p><p>Our layer houses collect fresh eggs daily, tested and graded before they reach the shop.</p>','images/gallary/akirevarga-egg-7345934.jpg',4,'published','2026-04-09','Know the Benefit of a Boiled Egg | City Gate Mixed Farm','Why fresh farm eggs from City Gate Mixed Farm are a simple, affordable source of daily protein.','2026-08-10 00:38:46','2026-08-11 22:28:29'),(3,'Why We Started a Goat Breeding Program','why-we-started-a-goat-breeding-program','Goats','goats,breeding,boer','Boer and Savannah goats are proving to be a resilient, profitable addition to the farm.','<p>We introduced Boer and Savannah goat breeding to diversify income and demonstrate mixed farming to visiting students.</p><p>Draft post ÔÇö pending final photos before publishing.</p>','images/img43.png',2,'draft',NULL,NULL,NULL,'2026-08-10 00:38:46','2026-08-10 00:38:46'),(4,'Inside Our Coffee & Banana Intercropping Trial','coffee-banana-intercropping-trial','Crops','coffee,bananas,intercropping,soil-health','How planting bananas alongside coffee is improving soil moisture and giving us a second income stream from the same plot.','<p>Two seasons ago we began intercropping bananas with our Robusta coffee on the eastern plot. The banana canopy shades the young coffee bushes, cuts water loss, and gives us matoke to sell while the coffee matures.</p><p>Early results: soil moisture readings are consistently higher between rows, and we have not lost a single coffee seedling to sun-scorch this dry season — a real problem in previous years.</p>','images/gallary/christoph-coffee-171653_1920.jpg',2,'published','2026-06-18','Coffee & Banana Intercropping at City Gate Mixed Farm','How City Gate Mixed Farm is intercropping coffee and bananas to improve soil health and diversify income in Lira City, Uganda.','2026-08-10 09:06:28','2026-08-10 09:06:28'),(5,'Meet the Team Behind Our Dairy Herd','meet-the-team-behind-our-dairy-herd','Dairy','dairy,team,cows,milk','A day in the life of the herders and milkers who keep roughly 80 litres flowing every single morning.','<p>Before sunrise, our dairy team is already in the milking shed. Consistency is everything in dairy — the same feeding schedule, the same hygiene routine, the same calm handling every day is what keeps yields steady.</p><p>We currently milk twice daily and supply both retail customers and a local processor in Lira.</p>','images/gallary/jaclou-dl-cow-4270355_1920.jpg',4,'published','2026-07-02','Meet the Dairy Team | City Gate Mixed Farm','The daily routine behind City Gate Mixed Farm\'s dairy herd in Amuca, Lira City, Uganda.','2026-08-10 09:06:28','2026-08-10 09:06:28'),(6,'Preparing for Our Busiest School-Visit Season','preparing-for-busiest-school-visit-season','Training','school-trips,visits,training','Term 3 brings our highest volume of school bookings — here is how the front desk keeps every group organised.','<p>Between August and October we typically host 15-20 school groups. Preparation matters: each teacher submits a headcount and purpose in advance through our online booking form, and our front desk checks every group in on arrival so nothing is duplicated on paper.</p><p>If you are a teacher planning a visit, book early — our September slots are filling up fast.</p>','images/gallary/pexels-chicken-1867521_1920.jpg',2,'published','2026-08-05','Planning a School Farm Visit | City Gate Mixed Farm','What to expect when booking a school trip to City Gate Mixed Farm in Lira City, Uganda.','2026-08-10 09:06:28','2026-08-10 09:06:28'),(7,'Biosecurity Basics: How We Keep 14,000 Birds Healthy','biosecurity-basics-keeping-14000-birds-healthy','Poultry','biosecurity, poultry health, disease prevention','Disease can wipe out a flock in days. Here is the everyday routine that keeps our poultry houses healthy — and what any farmer, large or small, can copy.','<p>With 14,000 birds across our brooding, growing and laying houses, biosecurity is not a poster on the wall — it is a daily routine that everyone on the team follows without exception.</p><h3>Controlled entry, every time</h3><p>Every poultry house has a footbath at the entrance and a strict no-visitors-inside-the-cages policy. Staff change into dedicated boots before entering, and anyone coming from another farm waits 48 hours before setting foot in ours.</p><h3>Clean water, clean feed, clean sheds</h3><p>Drinkers are scrubbed daily, feed is stored off the ground in sealed bins, and each house is fully cleared, washed and rested between flocks. Sick birds are isolated immediately, not \"watched for a few days.\"</p><h3>What smallholders can copy today</h3><ul><li>Keep visitors and other animals away from your birds</li><li>Wash your hands and boots before and after handling poultry</li><li>Never mix new birds into an existing flock without a quarantine period</li><li>Remove sick birds from the flock the moment you notice symptoms</li></ul><p>None of this requires expensive equipment — just discipline. It is the single biggest reason we have been able to scale from a small flock toward 100,000 birds without a major disease outbreak.</p>','images/gallary/neelam279-floor-7049278_1920.jpg',2,'published','2026-07-20',NULL,NULL,'2026-08-12 00:18:39','2026-08-12 00:18:39'),(8,'Boer vs. Savannah: Choosing the Right Goat Breed for Your Farm','boer-vs-savannah-choosing-the-right-goat-breed','Goats','goats, boer, savannah, breeding','Both breeds thrive in Northern Uganda\'s climate, but they serve different goals. Here is how we decide which one to recommend to a buyer.','<p>We keep both Boer and Savannah goats on the farm, and customers often ask which one they should start with. The honest answer is: it depends on what you are optimising for.</p><h3>Boer goats — built for meat</h3><p>Boer goats are the choice when weight gain and carcass quality matter most. They grow fast, handle a wide range of feed, and their distinctive white bodies with reddish-brown heads make them easy to spot in a mixed herd. If you are selling into the meat market, Boer or a Boer cross is usually the better starting point.</p><h3>Savannah goats — hardy and heat-tolerant</h3><p>Savannahs are all-white, heat-tolerant, and known for strong disease resistance and good mothering ability. They do well on pasture that would stress other breeds, which makes them a solid choice for drier plots or first-time goat keepers who want a lower-maintenance start.</p><h3>Why we cross-breed</h3><p>On our own herd, we cross Boer and Savannah to combine fast weight gain with hardiness — the same cross-breeding service we offer to other farmers looking to improve their herd\'s market value. If you are not sure which direction to go, come see both breeds in person; that is usually the fastest way to decide.</p>','images/gallary/techvaran-goats-5902053_1920.jpg',4,'published','2026-07-28',NULL,NULL,'2026-08-12 00:18:39','2026-08-12 00:18:39'),(9,'From Seedling to Shade: A Season on Our Crop Plots','from-seedling-to-shade-a-season-on-our-crop-plots','Crops','crops, coffee, bananas, farming','Coffee, bananas, maize and pasture share the same 4-hectare plot at City Gate. Here is what a growing season actually looks like, month by month.','<p>Our crop plots do double duty — they feed the farm\'s livestock program and produce coffee and bananas we sell directly. Nothing here is ornamental; every row earns its place.</p><h3>Starting in the nursery</h3><p>Coffee and banana starts spend their first weeks in a shaded nursery bed before they are strong enough to transplant. This is the stage where most losses happen if watering is inconsistent, so it gets checked twice a day without exception.</p><h3>Intercropping by design</h3><p>We plant bananas alongside our coffee rows deliberately — the banana canopy shades the young coffee plants, keeps soil moisture more consistent, and gives us a second harvest from the same footprint. It is a small change that has measurably improved yields on both crops.</p><h3>Closing the loop</h3><p>Maize and pasture grown on the same land feed our poultry and dairy herd, and manure from the animal side goes back into the soil. It is not a large operation, but it is a genuinely integrated one — which is the point of the whole farm.</p>','images/gallary/hat3m-seedling-4394118_1920.jpg',1,'published','2026-08-03',NULL,NULL,'2026-08-12 00:18:39','2026-08-12 00:18:39'),(10,'5 Things Every First-Time Visitor Should Know Before Touring City Gate','5-things-first-time-visitor-should-know','Training','farm visits, tours, visitors, school trips','Planning your first trip to City Gate Mixed Farm? Here is what to bring, what to expect, and how to make the most of your visit.','<p>We host farmers, students, and curious visitors from across Northern Uganda every week. A little preparation makes the visit far more useful — here is what we tell every first-time group.</p><h3>1. Book ahead, even for small groups</h3><p>We keep tour groups small enough that everyone can actually see and ask questions, so slots fill up — especially during the Term 3 school-visit season. Use the Book a Visit form and we will confirm within 48 hours.</p><h3>2. Wear closed shoes</h3><p>You will be walking through poultry houses, goat pens and crop rows on real farm ground — sandals are not ideal. Closed shoes you do not mind getting a little dusty are best.</p><h3>3. Come with questions</h3><p>Our guides are farm staff, not tour-script readers — ask about feed costs, disease management, or what it actually takes to run a mixed farm. That is where most visitors get the most value.</p><h3>4. Groups get a tailored focus</h3><p>Tell us your purpose when booking — school trips, agribusiness research, or buyers scouting livestock all get a slightly different route through the farm.</p><h3>5. Bring a note pad if you are training</h3><p>If you are here for hands-on training rather than a general tour, you will see and do more than you can remember afterward. Most trainees find a notebook more useful than a phone camera.</p><p>We look forward to hosting you — book your visit any time through the website.</p>','images/gallary/citygate-farm-photo-6.jpg',4,'published','2026-08-09',NULL,NULL,'2026-08-12 00:18:39','2026-08-12 00:18:39');
/*!40000 ALTER TABLE `blog_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_content`
--

DROP TABLE IF EXISTS `cms_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_key` varchar(60) NOT NULL,
  `field_key` varchar(80) NOT NULL,
  `value` text DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_page_field` (`page_key`,`field_key`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_content`
--

LOCK TABLES `cms_content` WRITE;
/*!40000 ALTER TABLE `cms_content` DISABLE KEYS */;
INSERT INTO `cms_content` VALUES (1,'home','impact_years','8','2026-08-10 00:38:46'),(2,'home','impact_years_label','Years Operating','2026-08-10 00:38:46'),(3,'home','impact_farmers','300+','2026-08-10 00:38:46'),(4,'home','impact_farmers_label','Farmers Trained','2026-08-10 00:38:46'),(5,'home','impact_students','1,200+','2026-08-10 00:38:46'),(6,'home','impact_students_label','Students Hosted','2026-08-10 00:38:46'),(7,'home','impact_sectors','4','2026-08-10 00:38:46'),(8,'home','impact_sectors_label','Farm Sectors','2026-08-10 00:38:46');
/*!40000 ALTER TABLE `cms_content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `email` varchar(190) DEFAULT NULL,
  `phone` varchar(40) DEFAULT NULL,
  `subject` varchar(190) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_messages`
--

LOCK TABLES `contact_messages` WRITE;
/*!40000 ALTER TABLE `contact_messages` DISABLE KEYS */;
INSERT INTO `contact_messages` VALUES (2,'Test Visitor QA','testvisitor.qa@example.com','+256700000000','PHP conversion QA test','This is a test submission verifying contact-us.php still posts correctly to contactmail.php after the html-to-php conversion.','2026-08-10 08:14:22'),(3,'QA Test Verifier','qa-verify@example.com','+256700000000','Automated verification test','This is an automated test submission from the contact-us.php verification pass.','2026-08-10 08:26:22'),(4,'Browser QA Tester','browserqa@example.com','+256711222333','Browser-driven verification','Submitted via real browser DOM interaction to verify contact-us.php + contactmail.php end to end.','2026-08-10 08:28:26'),(5,'Moses Okello','moses.okello@gmail.com','+256 772 118 664','Bulk maize order','Hello, I run a small feed shop in Lira and would like a quote for 1 tonne of maize delivered monthly.','2026-07-31 08:06:28'),(6,'Immaculate Aweko','i.aweko@yahoo.com','+256 782 990 213','School partnership','We are a primary school in Otuke district interested in a termly educational partnership. Please advise on availability.','2026-08-03 08:06:28'),(7,'QA Tester','qa.tester@example.com','0700000000','CSS cleanup verification','This is an automated test message verifying the contact form still works after the CSS cleanup.','2026-08-11 20:03:33');
/*!40000 ALTER TABLE `contact_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_log`
--

DROP TABLE IF EXISTS `email_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(220) NOT NULL,
  `body` longtext NOT NULL,
  `recipient_type` enum('single','bulk') NOT NULL DEFAULT 'single',
  `sent_by` int(11) DEFAULT NULL,
  `sent_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `sent_by` (`sent_by`),
  CONSTRAINT `email_log_ibfk_1` FOREIGN KEY (`sent_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_log`
--

LOCK TABLES `email_log` WRITE;
/*!40000 ALTER TABLE `email_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `email_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_recipients`
--

DROP TABLE IF EXISTS `email_recipients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_recipients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email_log_id` int(11) NOT NULL,
  `recipient_name` varchar(150) DEFAULT NULL,
  `recipient_email` varchar(190) NOT NULL,
  `status` enum('sent','failed') NOT NULL DEFAULT 'sent',
  `error_message` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email_log_id` (`email_log_id`),
  CONSTRAINT `email_recipients_ibfk_1` FOREIGN KEY (`email_log_id`) REFERENCES `email_log` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_recipients`
--

LOCK TABLES `email_recipients` WRITE;
/*!40000 ALTER TABLE `email_recipients` DISABLE KEYS */;
/*!40000 ALTER TABLE `email_recipients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` enum('Feed','Labor','Veterinary','Utilities','Repairs','Transport','Other') NOT NULL,
  `description` varchar(255) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `expense_date` date NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `recorded_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `recorded_by` (`recorded_by`),
  CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers_buyers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_ibfk_2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expenses`
--

LOCK TABLES `expenses` WRITE;
/*!40000 ALTER TABLE `expenses` DISABLE KEYS */;
INSERT INTO `expenses` VALUES (1,'Feed','Broiler starter feed restock',850000.00,'2026-07-28',1,3,'2026-08-10 12:52:42'),(2,'Veterinary','Newcastle vaccine batch + vet callout',320000.00,'2026-07-16',2,3,'2026-08-10 12:52:42'),(3,'Labor','July casual labor — poultry house cleaning',400000.00,'2026-07-31',NULL,3,'2026-08-10 12:52:42'),(4,'Utilities','Electricity bill — July',180000.00,'2026-08-01',NULL,3,'2026-08-10 12:52:42'),(5,'Repairs','Poultry house roof repair',250000.00,'2026-07-10',NULL,3,'2026-08-10 12:52:42'),(6,'Transport','Fuel — feed delivery truck',120000.00,'2026-08-05',NULL,3,'2026-08-10 12:52:42');
/*!40000 ALTER TABLE `expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gallery_media`
--

DROP TABLE IF EXISTS `gallery_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gallery_media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('photo','video') NOT NULL DEFAULT 'photo',
  `file_path` varchar(255) DEFAULT NULL,
  `embed_url` varchar(400) DEFAULT NULL,
  `thumbnail_path` varchar(255) DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `category` varchar(80) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `uploaded_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `uploaded_by` (`uploaded_by`),
  CONSTRAINT `gallery_media_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gallery_media`
--

LOCK TABLES `gallery_media` WRITE;
/*!40000 ALTER TABLE `gallery_media` DISABLE KEYS */;
INSERT INTO `gallery_media` VALUES (1,'photo','images/gallary/9883074-chicken-3727097_1920.jpg',NULL,NULL,'Healthy broilers in a clean house','Poultry',1,NULL,'2026-08-10 00:38:46'),(2,'photo','images/gallary/akirevarga-egg-7345934.jpg',NULL,NULL,'Fresh eggs collected daily','Poultry',2,NULL,'2026-08-10 00:38:46'),(3,'photo','images/gallary/andreasgoellner-hen-5642953_1920.jpg',NULL,NULL,'Layer hens at City Gate','Poultry',3,NULL,'2026-08-10 00:38:46'),(4,'photo','images/gallary/bernhardjaeck-billy-goat-7397721_1920.jpg',NULL,NULL,'Boer billy goat','Goats',4,NULL,'2026-08-10 00:38:46'),(5,'photo','images/gallary/christoph-coffee-171653_1920.jpg',NULL,NULL,'Coffee cherries ready for harvest','Crops',5,NULL,'2026-08-10 00:38:46'),(6,'photo','images/gallary/couleur-milk-2474993.jpg',NULL,NULL,'Fresh cow milk','Dairy',6,NULL,'2026-08-10 00:38:46'),(7,'photo','images/gallary/die_berlinerin-animal-7189976_1920.jpg',NULL,NULL,'Farm livestock','Goats',7,NULL,'2026-08-10 00:38:46'),(8,'photo','images/gallary/engin_akyurt-egg-6757508_1920.jpg',NULL,NULL,'Farm-fresh eggs','Poultry',8,NULL,'2026-08-10 00:38:46'),(9,'photo','images/gallary/hat3m-seedling-4394118_1920.jpg',NULL,NULL,'Young seedlings in the nursery','Crops',9,NULL,'2026-08-10 00:38:46'),(10,'photo','images/gallary/jaclou-dl-cow-4270355_1920.jpg',NULL,NULL,'Dairy cow grazing','Dairy',10,NULL,'2026-08-10 00:38:46'),(11,'photo','images/gallary/marcusvu-coffee-2992598_1920.jpg',NULL,NULL,'Coffee plot','Crops',11,NULL,'2026-08-10 00:38:46'),(12,'photo','images/gallary/neelam279-floor-7049278_1920.jpg',NULL,NULL,'Inside the poultry house','Poultry',12,NULL,'2026-08-10 00:38:46'),(13,'photo','images/gallary/pexels-chicken-1867521_1920.jpg',NULL,NULL,'Free-range chickens','Poultry',13,NULL,'2026-08-10 00:38:46'),(14,'photo','images/gallary/pezibear-animal-6756751_1920.jpg',NULL,NULL,'Farm animals at pasture','Goats',14,NULL,'2026-08-10 00:38:46'),(15,'photo','images/gallary/rooster-farm.jpg',NULL,NULL,'Rooster on the farm','Poultry',15,NULL,'2026-08-10 00:38:46'),(16,'photo','images/gallary/rschaubhut-goat-5642612_1920.jpg',NULL,NULL,'Boer/Savannah goats','Goats',16,NULL,'2026-08-10 00:38:46'),(17,'photo','images/gallary/stevepb-nest-1050964_1920.jpg',NULL,NULL,'Nesting boxes','Poultry',17,NULL,'2026-08-10 00:38:46'),(18,'photo','images/gallary/techvaran-goats-5902053_1920.jpg',NULL,NULL,'Goat herd','Goats',18,NULL,'2026-08-10 00:38:46'),(19,'photo','images/gallary/walter46-goat-4138049_1920.jpg',NULL,NULL,'Goats grazing','Goats',19,NULL,'2026-08-10 00:38:46'),(20,'photo','images/gallary/rschaubhut-goat-5642612_1920.jpg',NULL,NULL,'City Gate goat breeding stock','Goats',20,NULL,'2026-08-10 00:38:46'),(25,'photo','images/gallary/citygate-farm-photo-1.jpg',NULL,NULL,'Our team at a partnership and training showcase event','Events',21,1,'2026-08-11 21:02:05'),(26,'photo','images/gallary/citygate-farm-photo-2.jpg',NULL,NULL,'Checking on our dairy herd','Dairy',22,1,'2026-08-11 21:02:05'),(27,'photo','images/gallary/citygate-farm-photo-3.jpg',NULL,NULL,'Inspecting the layer flock in our poultry house','Poultry',23,1,'2026-08-11 21:02:05'),(28,'photo','images/gallary/citygate-farm-photo-4.jpg',NULL,NULL,'Farm partners and stakeholders on site','Events',24,1,'2026-08-11 21:02:05'),(29,'photo','images/gallary/citygate-farm-photo-5.jpg',NULL,NULL,'Touring the layer cages','Poultry',25,1,'2026-08-11 21:02:05'),(30,'photo','images/gallary/citygate-farm-photo-6.jpg',NULL,NULL,'Staff and visitors on a farm walk-through','Visits',26,1,'2026-08-11 21:02:05');
/*!40000 ALTER TABLE `gallery_media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_items`
--

DROP TABLE IF EXISTS `inventory_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `category` enum('Feed','Medicine','Equipment','Other') NOT NULL,
  `unit` varchar(40) NOT NULL,
  `quantity_on_hand` decimal(12,2) NOT NULL DEFAULT 0.00,
  `reorder_level` decimal(12,2) NOT NULL DEFAULT 0.00,
  `unit_cost` decimal(12,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_items`
--

LOCK TABLES `inventory_items` WRITE;
/*!40000 ALTER TABLE `inventory_items` DISABLE KEYS */;
INSERT INTO `inventory_items` VALUES (1,'Layer Mash','Feed','Kg',800.00,200.00,2200.00,NULL,'2026-08-10 13:38:01'),(2,'Broiler Starter Feed','Feed','Kg',150.00,200.00,2800.00,'Running low — reorder soon.','2026-08-10 12:52:42'),(3,'Maize Bran','Feed','Kg',500.00,150.00,1200.00,NULL,'2026-08-10 13:38:01'),(4,'Newcastle Vaccine','Medicine','Doses',40.00,50.00,500.00,'Running low — reorder soon.','2026-08-10 12:52:42'),(5,'Dewormer','Medicine','Bottles',12.00,5.00,15000.00,NULL,'2026-08-10 12:52:42'),(6,'Wheelbarrow','Equipment','Units',3.00,1.00,180000.00,NULL,'2026-08-10 12:52:42'),(7,'Egg Trays','Equipment','Pieces',300.00,100.00,1500.00,NULL,'2026-08-10 12:52:42');
/*!40000 ALTER TABLE `inventory_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_transactions`
--

DROP TABLE IF EXISTS `inventory_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `type` enum('In','Out') NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `related_livestock_id` int(11) DEFAULT NULL,
  `performed_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  KEY `related_livestock_id` (`related_livestock_id`),
  KEY `performed_by` (`performed_by`),
  CONSTRAINT `inventory_transactions_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_transactions_ibfk_2` FOREIGN KEY (`related_livestock_id`) REFERENCES `livestock_records` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventory_transactions_ibfk_3` FOREIGN KEY (`performed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_transactions`
--

LOCK TABLES `inventory_transactions` WRITE;
/*!40000 ALTER TABLE `inventory_transactions` DISABLE KEYS */;
INSERT INTO `inventory_transactions` VALUES (1,1,'In',1000.00,'Monthly restock from Northern Feeds Ltd',NULL,2,'2026-07-28 09:00:00'),(2,1,'Out',200.00,'Daily feeding — layer house',NULL,2,'2026-08-08 07:00:00'),(3,2,'In',400.00,'Restock',NULL,2,'2026-07-05 09:00:00'),(4,2,'Out',250.00,'Feeding — broiler batch PLT-2026-08-01',NULL,2,'2026-08-06 07:00:00'),(5,4,'Out',60.00,'Vaccination round — layer batch',NULL,2,'2026-07-16 10:00:00');
/*!40000 ALTER TABLE `inventory_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `livestock_health_records`
--

DROP TABLE IF EXISTS `livestock_health_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `livestock_health_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `livestock_id` int(11) NOT NULL,
  `record_type` enum('Vaccination','Treatment','Checkup','Incident') NOT NULL,
  `description` text NOT NULL,
  `record_date` date NOT NULL,
  `next_due_date` date DEFAULT NULL,
  `cost` decimal(12,2) DEFAULT NULL,
  `performed_by` varchar(150) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `livestock_id` (`livestock_id`),
  CONSTRAINT `livestock_health_records_ibfk_1` FOREIGN KEY (`livestock_id`) REFERENCES `livestock_records` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `livestock_health_records`
--

LOCK TABLES `livestock_health_records` WRITE;
/*!40000 ALTER TABLE `livestock_health_records` DISABLE KEYS */;
INSERT INTO `livestock_health_records` VALUES (1,2,'Vaccination','Newcastle Disease vaccine — full batch dose','2026-07-16','2026-10-16',150000.00,'Dr. Okello Vet Services','2026-08-10 12:52:42'),(2,3,'Vaccination','Gumboro (IBD) vaccine','2026-08-03','2026-08-24',90000.00,'Dr. Okello Vet Services','2026-08-10 12:52:42'),(3,1,'Checkup','Pre-slaughter health check, batch cleared healthy','2026-08-05',NULL,NULL,'David Okello','2026-08-10 12:52:42'),(4,4,'Checkup','Routine deworming','2026-07-01','2026-10-01',20000.00,'David Okello','2026-08-10 12:52:42'),(5,8,'Treatment','Mastitis treatment — antibiotic course','2026-06-10',NULL,45000.00,'Dr. Okello Vet Services','2026-08-10 12:52:42'),(6,9,'Vaccination','Foot-and-mouth disease (FMD) vaccine','2026-05-20','2026-11-20',60000.00,'Dr. Okello Vet Services','2026-08-10 12:52:42');
/*!40000 ALTER TABLE `livestock_health_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `livestock_records`
--

DROP TABLE IF EXISTS `livestock_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `livestock_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sector` enum('Poultry','Goats','Dairy') NOT NULL,
  `identifier` varchar(60) NOT NULL,
  `breed` varchar(100) DEFAULT NULL,
  `sex` enum('Male','Female') DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `date_acquired` date NOT NULL,
  `source` enum('Hatched','Purchased','Born') NOT NULL DEFAULT 'Purchased',
  `status` enum('Active','Sold','Deceased') NOT NULL DEFAULT 'Active',
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `identifier` (`identifier`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `livestock_records_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `livestock_records`
--

LOCK TABLES `livestock_records` WRITE;
/*!40000 ALTER TABLE `livestock_records` DISABLE KEYS */;
INSERT INTO `livestock_records` VALUES (1,'Poultry','PLT-2026-06-01','Broiler — Cobb 500',NULL,5000,'2026-06-01','Purchased','Active','Main broiler house, batch 1.',2,'2026-08-10 12:52:42','2026-08-10 12:52:42'),(2,'Poultry','PLT-2026-07-15','Layer — Bovans Brown',NULL,3000,'2026-07-15','Purchased','Active','Layer house, egg production.',2,'2026-08-10 12:52:42','2026-08-10 12:52:42'),(3,'Poultry','PLT-2026-08-01','Broiler — Cobb 500',NULL,2000,'2026-08-01','Hatched','Active','Own hatchery batch.',2,'2026-08-10 12:52:42','2026-08-10 12:52:42'),(4,'Goats','GT-001','Boer','Male',1,'2025-11-10','Purchased','Active','Breeding buck.',2,'2026-08-10 12:52:42','2026-08-10 12:52:42'),(5,'Goats','GT-002','Boer','Female',1,'2025-11-10','Purchased','Active',NULL,2,'2026-08-10 12:52:42','2026-08-10 12:52:42'),(6,'Goats','GT-003','Savannah','Female',1,'2026-02-20','Born','Active','Born on farm.',2,'2026-08-10 12:52:42','2026-08-10 12:52:42'),(7,'Goats','GT-004','Boer','Female',1,'2026-01-05','Purchased','Sold','Sold to Kitgum Livestock Traders.',2,'2026-08-10 12:52:42','2026-08-10 12:52:42'),(8,'Dairy','DC-001','Friesian','Female',1,'2024-08-01','Purchased','Active','Primary milker.',2,'2026-08-10 12:52:42','2026-08-10 12:52:42'),(9,'Dairy','DC-002','Friesian x Jersey','Female',1,'2025-03-15','Born','Active',NULL,2,'2026-08-10 12:52:42','2026-08-10 12:52:42'),(10,'Dairy','DC-003','Ankole','Female',1,'2024-11-20','Purchased','Active',NULL,2,'2026-08-10 12:52:42','2026-08-10 12:52:42');
/*!40000 ALTER TABLE `livestock_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_reviews`
--

DROP TABLE IF EXISTS `product_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_slug` varchar(150) NOT NULL,
  `name` varchar(150) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_reviews`
--

LOCK TABLES `product_reviews` WRITE;
/*!40000 ALTER TABLE `product_reviews` DISABLE KEYS */;
INSERT INTO `product_reviews` VALUES (1,'bovans-browns','QA Reviewer',5,'Great layer birds, very healthy and consistent egg production.','2026-08-10 08:14:30'),(2,'bovans-browns','Browser QA Reviewer',4,'Second verification review submitted via curl PRG test.','2026-08-10 08:29:47'),(3,'bovans-browns','Patricia K.',5,'Bought 10 pullets last month, all healthy and laying well already.','2026-07-26 08:06:28'),(4,'bovans-browns','Emmanuel T.',4,'Good stock, transport arrangement from the farm was smooth.','2026-07-30 08:06:28'),(5,'bovans-browns','Nakato Ritah',5,'Second time buying from City Gate — consistent quality every time.','2026-08-03 08:06:28');
/*!40000 ALTER TABLE `product_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `production_records`
--

DROP TABLE IF EXISTS `production_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `production_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sector` enum('Poultry','Dairy','Crops','Goats') NOT NULL,
  `metric` varchar(60) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `unit` varchar(40) NOT NULL,
  `record_date` date NOT NULL,
  `livestock_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `recorded_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `livestock_id` (`livestock_id`),
  KEY `recorded_by` (`recorded_by`),
  CONSTRAINT `production_records_ibfk_1` FOREIGN KEY (`livestock_id`) REFERENCES `livestock_records` (`id`) ON DELETE SET NULL,
  CONSTRAINT `production_records_ibfk_2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `production_records`
--

LOCK TABLES `production_records` WRITE;
/*!40000 ALTER TABLE `production_records` DISABLE KEYS */;
INSERT INTO `production_records` VALUES (1,'Poultry','Eggs Collected',96.00,'Trays','2026-08-01',2,NULL,2,'2026-08-10 12:52:42'),(2,'Poultry','Eggs Collected',98.00,'Trays','2026-08-02',2,NULL,2,'2026-08-10 12:52:42'),(3,'Poultry','Eggs Collected',94.00,'Trays','2026-08-03',2,NULL,2,'2026-08-10 12:52:42'),(4,'Poultry','Eggs Collected',101.00,'Trays','2026-08-05',2,NULL,2,'2026-08-10 12:52:42'),(5,'Poultry','Eggs Collected',99.00,'Trays','2026-08-07',2,NULL,2,'2026-08-10 12:52:42'),(6,'Poultry','Eggs Collected',103.00,'Trays','2026-08-09',2,NULL,2,'2026-08-10 12:52:42'),(7,'Dairy','Milk Yield',27.50,'Litres','2026-08-01',8,NULL,2,'2026-08-10 12:52:42'),(8,'Dairy','Milk Yield',26.00,'Litres','2026-08-03',8,NULL,2,'2026-08-10 12:52:42'),(9,'Dairy','Milk Yield',28.00,'Litres','2026-08-05',8,NULL,2,'2026-08-10 12:52:42'),(10,'Dairy','Milk Yield',24.00,'Litres','2026-08-07',9,NULL,2,'2026-08-10 12:52:42'),(11,'Dairy','Milk Yield',25.50,'Litres','2026-08-09',9,NULL,2,'2026-08-10 12:52:42'),(12,'Poultry','Weight Gain',1.80,'Kg avg/bird','2026-08-01',1,NULL,2,'2026-08-10 12:52:42'),(13,'Poultry','Weight Gain',2.30,'Kg avg/bird','2026-08-08',1,NULL,2,'2026-08-10 12:52:42'),(14,'Crops','Harvest',85.00,'Kg','2026-07-20',NULL,NULL,2,'2026-08-10 12:52:42'),(15,'Crops','Harvest',60.00,'Bunches','2026-07-28',NULL,NULL,2,'2026-08-10 12:52:42');
/*!40000 ALTER TABLE `production_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product` varchar(150) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit` varchar(40) NOT NULL,
  `buyer` varchar(150) NOT NULL,
  `buyer_phone` varchar(40) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `status` enum('Paid','Unpaid') NOT NULL DEFAULT 'Unpaid',
  `sale_date` date NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales`
--

LOCK TABLES `sales` WRITE;
/*!40000 ALTER TABLE `sales` DISABLE KEYS */;
INSERT INTO `sales` VALUES (1,'Fresh Eggs',10.00,'Trays','Lira City Hotel','+256 772 100 200',120000.00,'Paid','2026-07-20',NULL,'2026-08-10 00:38:46'),(2,'Fresh Cow Milk',50.00,'Litres','Amuca Dairy Co-op','+256 772 300 400',125000.00,'Paid','2026-07-22',NULL,'2026-08-10 00:38:46'),(3,'Boer Goats',2.00,'Heads','John Okwir','+256 772 500 600',1200000.00,'Unpaid','2026-07-25',NULL,'2026-08-10 00:38:46'),(4,'Day-Old Chicks',200.00,'Chicks','Northern Poultry Traders','+256 772 700 800',2000000.00,'Paid','2026-08-01',NULL,'2026-08-10 00:38:46'),(5,'Maize',500.00,'Kg','Lira Grain Millers','+256 772 900 100',900000.00,'Unpaid','2026-08-03',NULL,'2026-08-10 00:38:46'),(7,'Broiler Chickens',150.00,'Birds','Savannah Grill Restaurant','+256 772 118 400',1500000.00,'Paid','2026-06-14',3,'2026-08-10 09:06:28'),(8,'Coffee Beans',80.00,'Kg','Lira Roasters Co-op','+256 782 552 310',960000.00,'Paid','2026-06-20',3,'2026-08-10 09:06:28'),(9,'Bananas',60.00,'Bunches','Amuca Market Traders','+256 701 229 887',900000.00,'Unpaid','2026-06-28',3,'2026-08-10 09:06:28'),(10,'Fresh Eggs',25.00,'Trays','Divine Hotel Lira','+256 772 900 214',300000.00,'Paid','2026-07-04',3,'2026-08-10 09:06:28'),(11,'Fresh Cow Milk',100.00,'Litres','Lira Dairy Processors','+256 793 447 761',250000.00,'Paid','2026-07-11',3,'2026-08-10 09:06:28'),(12,'Day-Old Chicks',500.00,'Chicks','Otwal Poultry Farm','+256 772 336 004',5000000.00,'Unpaid','2026-07-19',3,'2026-08-10 09:06:28'),(13,'Boer Goats',4.00,'Heads','Kitgum Livestock Traders','+256 782 118 552',2400000.00,'Paid','2026-07-28',3,'2026-08-10 09:06:28'),(14,'Maize',300.00,'Kg','Northern Grain Bank','+256 700 664 219',540000.00,'Paid','2026-08-02',3,'2026-08-10 09:06:28'),(15,'Fresh Eggs',15.00,'Trays','Corner Café Lira','+256 772 553 809',180000.00,'Paid','2026-08-06',3,'2026-08-10 09:06:28'),(16,'Fresh Cow Milk',40.00,'Litres','Amuca Trading Center','+256 782 990 447',100000.00,'Unpaid','2026-08-09',3,'2026-08-10 09:06:28');
/*!40000 ALTER TABLE `sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers_buyers`
--

DROP TABLE IF EXISTS `suppliers_buyers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suppliers_buyers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `type` enum('Supplier','Buyer','Both') NOT NULL DEFAULT 'Supplier',
  `contact_person` varchar(150) DEFAULT NULL,
  `phone` varchar(40) DEFAULT NULL,
  `email` varchar(190) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `category` varchar(80) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers_buyers`
--

LOCK TABLES `suppliers_buyers` WRITE;
/*!40000 ALTER TABLE `suppliers_buyers` DISABLE KEYS */;
INSERT INTO `suppliers_buyers` VALUES (1,'Northern Feeds Ltd','Supplier','Timothy Ocaya','+256 772 441 990','sales@northernfeeds.ug','Industrial Area, Lira City','Feed Supplier','Main feed supplier — monthly restock terms.','2026-08-10 12:52:42'),(2,'Uganda Veterinary Services — Lira Branch','Supplier','Dr. Okello','+256 782 336 771','lira@uvs.ug','Lira City','Veterinary','Vaccines, treatments, and routine checkups.','2026-08-10 12:52:42'),(3,'Lira City Hotel','Buyer','Procurement Office','+256 772 100 200','procurement@liracityhotel.com','Lira City','Produce Buyer','Regular weekly egg + milk order.','2026-08-10 12:52:42'),(4,'Savannah Grill Restaurant','Buyer','Kitchen Manager','+256 772 118 400',NULL,'Lira City','Produce Buyer','Bulk broiler chicken buyer.','2026-08-10 12:52:42'),(5,'Amuca Dairy Co-op','Both','Chairperson','+256 772 300 400',NULL,'Amuca, Lira City','Dairy','Buys surplus milk, occasionally sells feed supplements.','2026-08-10 12:52:42'),(6,'Kitgum Livestock Traders','Buyer','Manager','+256 782 118 552',NULL,'Kitgum','Livestock Buyer','Regular goat buyer.','2026-08-10 12:52:42');
/*!40000 ALTER TABLE `suppliers_buyers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(60) DEFAULT NULL,
  `due_date` date NOT NULL,
  `due_time` time DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `status` enum('Pending','Done') NOT NULL DEFAULT 'Pending',
  `recurrence_label` varchar(40) DEFAULT NULL,
  `livestock_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `completed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assigned_to` (`assigned_to`),
  KEY `livestock_id` (`livestock_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`livestock_id`) REFERENCES `livestock_records` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tasks_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tasks`
--

LOCK TABLES `tasks` WRITE;
/*!40000 ALTER TABLE `tasks` DISABLE KEYS */;
INSERT INTO `tasks` VALUES (1,'Vaccinate layer batch — Newcastle booster','Follow-up Newcastle dose due for PLT-2026-07-15.','Vaccination','2026-10-16',NULL,2,'Pending','Quarterly',2,1,'2026-08-10 12:52:42',NULL),(2,'Clean broiler house B','Full wash-down and disinfect ahead of next batch.','Cleaning','2026-08-11',NULL,2,'Pending','Weekly',NULL,1,'2026-08-10 12:52:42',NULL),(3,'Deworm goat herd','Routine deworming for all active goats.','Health','2026-08-05',NULL,2,'Pending','Quarterly',NULL,1,'2026-08-10 12:52:42',NULL),(4,'Restock layer mash','Order and receive next layer mash delivery.','Feeding','2026-08-08',NULL,2,'Done',NULL,NULL,1,'2026-08-10 12:52:42','2026-08-08 15:20:00'),(5,'Repair broiler house fence','Section near the east gate needs new wire mesh.','Repairs','2026-08-15',NULL,2,'Pending',NULL,NULL,1,'2026-08-10 12:52:42',NULL),(6,'Morning egg collection & grading','Daily collection, grading, and tray count.','Feeding','2026-08-10',NULL,2,'Pending','Daily',NULL,1,'2026-08-10 12:52:42',NULL),(7,'Milk collection & cooling check','Morning milking, record yield, verify cooler temperature.','Feeding','2026-08-10',NULL,2,'Pending','Daily',NULL,1,'2026-08-10 12:52:42',NULL);
/*!40000 ALTER TABLE `tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('SuperAdmin','Manager','Finance','Admin') NOT NULL,
  `status` enum('active','suspended') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Sarah Achen','superadmin@citygatefarms.com','$2y$10$5WC7XD0F989Op4/JySr13.PI6YI7v92TYRKbpcIORK.Xp.RJKPRp.','SuperAdmin','active','2026-08-10 00:38:46'),(2,'David Okello','manager@citygatefarms.com','$2y$10$SNxMTr/sbvDiXuqpGGeETec4slaW4X.mLhed9Z/uFvmMly4dNJfTu','Manager','active','2026-08-10 00:38:46'),(3,'Grace Auma','finance@citygatefarms.com','$2y$10$2x4.hyv0s8xnWw7nKMp8m.Dx1.N4BHRmt6qxyNOi6tIc6VfnT3qB.','Finance','active','2026-08-10 00:38:46'),(4,'Peter Otim','admin@citygatefarms.com','$2y$10$x8Neg2oYmAubw1ogM5gqRuokwja9Jkjm353H3uWdKLzNnwXq/pKv2','Admin','active','2026-08-10 00:38:46');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visitor_registry`
--

DROP TABLE IF EXISTS `visitor_registry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `visitor_registry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(150) NOT NULL,
  `phone` varchar(40) DEFAULT NULL,
  `email` varchar(190) DEFAULT NULL,
  `purpose` varchar(190) NOT NULL,
  `host_person` varchar(150) DEFAULT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `signed_in_at` datetime NOT NULL DEFAULT current_timestamp(),
  `signed_out_at` datetime DEFAULT NULL,
  `recorded_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `appointment_id` (`appointment_id`),
  KEY `recorded_by` (`recorded_by`),
  CONSTRAINT `visitor_registry_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `visitor_registry_ibfk_2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visitor_registry`
--

LOCK TABLES `visitor_registry` WRITE;
/*!40000 ALTER TABLE `visitor_registry` DISABLE KEYS */;
INSERT INTO `visitor_registry` VALUES (2,'Mr. Vincent Ocen','+256 782 214 667','vincent.ocen@liracity.go.ug','partnership',NULL,7,'2026-07-27 08:06:28','2026-07-27 10:06:28',2),(3,'Sarah Nansubuga','+256 702 337 990','sarah.n@kakebetech.com','internship',NULL,8,'2026-07-23 08:06:28','2026-07-23 10:06:28',2),(4,'Ms. Betty Achola','+256 772 884 502','betty.achola@bnps.sc.ug','school-trip',NULL,9,'2026-07-17 08:06:28','2026-07-17 10:06:28',2),(5,'Michael Opio','+256 701 887 234',NULL,'Enquiring about broiler chick prices','Front Desk',NULL,'2026-07-19 17:06:28','2026-07-19 17:51:28',2),(6,'Agnes Nakato','+256 772 004 561','agnes.nakato@gmail.com','Delivering veterinary supplies','Farm Manager',NULL,'2026-07-26 17:06:28','2026-07-26 17:51:28',2),(7,'Hassan Mugisha','+256 782 337 120',NULL,'Meeting about feed supply contract','David Okello',NULL,'2026-07-31 17:06:28','2026-07-31 17:51:28',2),(8,'Florence Aol','+256 700 118 943','florence.aol@outlook.com','Journalist — feature story on model farms','Sarah Achen',NULL,'2026-08-03 17:06:28','2026-08-03 17:51:28',2),(9,'Peter Lubega','+256 772 665 890',NULL,'Dropping off a job application','Front Desk',NULL,'2026-08-06 17:06:28','2026-08-06 17:51:28',2),(10,'Joyce Amongi','+256 793 441 076',NULL,'Picking up an egg order','Front Desk',NULL,'2026-08-09 17:06:28',NULL,2),(11,'Kakebe Technologies Limited','+256786462741','kakebetech.comms@gmail.com','partnership',NULL,13,'2026-08-10 13:57:01',NULL,1),(12,'Dr. Patricia Lamwaka','+256 700 556 812','p.lamwaka@gu.ac.ug','study',NULL,4,'2026-08-11 19:00:14',NULL,1);
/*!40000 ALTER TABLE `visitor_registry` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'citygatefarm'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-12  9:01:32
