tee create_tables_and_data_LCWEB_PORTFOLIO_02112025.log


-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for osx10.10 (x86_64)
--
-- Host: localhost    Database: LCWEB_PORTFOLIO
-- ------------------------------------------------------
-- Server version	10.4.28-MariaDB

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
-- Table structure for table `attivita`
--

DROP TABLE IF EXISTS `attivita`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attivita` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titolo` varchar(255) NOT NULL,
  `descrizione` text NOT NULL,
  `link_pagina` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attivita`
--

LOCK TABLES `attivita` WRITE;
/*!40000 ALTER TABLE `attivita` DISABLE KEYS */;
INSERT INTO `attivita` VALUES (1,'Gestione utenti','Area per creare , modificare o cancellare utenti di accesso a questa area riservata','gestione_utenti.php'),(2,'Gestione Servizi','Area per creare , modificare o cancellare i servizi offerti','gestione_servizi.php'),(3,'Gestione Portfolio','Area per creare, modificare o cancellare i progetti eseguiti','gestione_progetti.php');
/*!40000 ALTER TABLE `attivita` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chi_sono`
--

DROP TABLE IF EXISTS `chi_sono`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chi_sono` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `articolo` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chi_sono`
--

LOCK TABLES `chi_sono` WRITE;
/*!40000 ALTER TABLE `chi_sono` DISABLE KEYS */;
INSERT INTO `chi_sono` VALUES (1,'Chi sono','LA MIA STORIA','Nato a Roma il 10 maggio del 1998, ho sempre avuto una forte inclinazione per il mondo del motion e brand design. Fin da bambino, ho coltivato una grande passione per i graffiti, un’ arte che mi ha permesso di esprimere la mia creatività in modo libero e autentico. Attraverso questa forma d’arte urbana, ho sviluppato un occhio attento per la composizione visiva, il colore e soprattutto la tipografia, un elemento che con il tempo è diventato un punto centrale del mio percorso artistico e professionale. L''amore per la tipografia non si è limitato solo all’osservazione, ma si è trasformato in un vero e proprio studio della sua struttura, della sua evoluzione nel tempo e delle sue infinite possibilità di applicazione nel mondo del design. Questo interesse mi ha spinto ad approfondire sempre di più le tecniche di comunicazione visiva, sperimentando con lettering, logotipi e animazioni in movimento, fino a farne la mia principale area di specializzazione. Oggi, grazie a questa passione nata in modo spontaneo, ho trovato la mia strada nel mondo del design, dove posso coniugare creatività, tecnica e innovazione per dare vita a progetti visivi unici ed efficaci.');
/*!40000 ALTER TABLE `chi_sono` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `immagini_lavoro`
--

DROP TABLE IF EXISTS `immagini_lavoro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `immagini_lavoro` (
  `id_lavoro` int(11) NOT NULL AUTO_INCREMENT,
  `id_progetto` int(11) NOT NULL,
  `src_immagine` varchar(255) NOT NULL,
  `alt_immagine` varchar(255) NOT NULL,
  PRIMARY KEY (`id_lavoro`),
  KEY `id_progetto` (`id_progetto`),
  CONSTRAINT `immagini_lavoro_ibfk_1` FOREIGN KEY (`id_progetto`) REFERENCES `progetti` (`id_progetto`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `immagini_lavoro`
--

LOCK TABLES `immagini_lavoro` WRITE;
/*!40000 ALTER TABLE `immagini_lavoro` DISABLE KEYS */;
INSERT INTO LCWEB_PORTFOLIO.immagini_lavoro (id_lavoro, id_progetto, src_immagine, alt_immagine) VALUES(1, 1, 'Img/social_media1.avif', 'Immagine 1');
INSERT INTO LCWEB_PORTFOLIO.immagini_lavoro (id_lavoro, id_progetto, src_immagine, alt_immagine) VALUES(2, 1, 'Img/social_media2.jpg', 'Immagine 2');
INSERT INTO LCWEB_PORTFOLIO.immagini_lavoro (id_lavoro, id_progetto, src_immagine, alt_immagine) VALUES(3, 2, 'Img/1Cine.png', 'Immagine 1');
INSERT INTO LCWEB_PORTFOLIO.immagini_lavoro (id_lavoro, id_progetto, src_immagine, alt_immagine) VALUES(4, 2, 'Img/2Cine.png', 'Immagine 2');
INSERT INTO LCWEB_PORTFOLIO.immagini_lavoro (id_lavoro, id_progetto, src_immagine, alt_immagine) VALUES(5, 3, 'Img/logo1.webp', 'Immagine 1');
INSERT INTO LCWEB_PORTFOLIO.immagini_lavoro (id_lavoro, id_progetto, src_immagine, alt_immagine) VALUES(6, 3, 'Img/logo2.png', 'Immagine 2');
INSERT INTO LCWEB_PORTFOLIO.immagini_lavoro (id_lavoro, id_progetto, src_immagine, alt_immagine) VALUES(7, 4, 'Img/visual1.jpg', 'Immagine 1');
INSERT INTO LCWEB_PORTFOLIO.immagini_lavoro (id_lavoro, id_progetto, src_immagine, alt_immagine) VALUES(8, 4, 'Img/visual2.jpg', 'Immagine 2');
INSERT INTO LCWEB_PORTFOLIO.immagini_lavoro (id_lavoro, id_progetto, src_immagine, alt_immagine) VALUES(9, 5, 'Img/advertising1.webp', 'Immagine 1');
INSERT INTO LCWEB_PORTFOLIO.immagini_lavoro (id_lavoro, id_progetto, src_immagine, alt_immagine) VALUES(10, 5, 'Img/advertising2.webp', 'Immagine 2');
INSERT INTO LCWEB_PORTFOLIO.immagini_lavoro (id_lavoro, id_progetto, src_immagine, alt_immagine) VALUES(11, 6, 'Img/branding1.webp', 'Immagine 1');
INSERT INTO LCWEB_PORTFOLIO.immagini_lavoro (id_lavoro, id_progetto, src_immagine, alt_immagine) VALUES(12, 6, 'Img/branding2.webp', 'Immagine 2');
/*!40000 ALTER TABLE `immagini_lavoro` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lavori_video`
--

DROP TABLE IF EXISTS `lavori_video`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lavori_video` (
  `id` int(11) NOT NULL,
  `src` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lavori_video`
--

LOCK TABLES `lavori_video` WRITE;
/*!40000 ALTER TABLE `lavori_video` DISABLE KEYS */;
INSERT INTO `lavori_video` VALUES (1,'Video/Michael_Jordan.mp4','Michael Jordan','video/mp4');
/*!40000 ALTER TABLE `lavori_video` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `progetti`
--

DROP TABLE IF EXISTS `progetti`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `progetti` (
  `id_progetto` int(11) NOT NULL,
  `alt_immagine_principale` varchar(255) NOT NULL,
  `descrizione` text NOT NULL,
  `tipo` varchar(100) NOT NULL,
  `src_immagine_principale` varchar(255) DEFAULT NULL,
  `nome_progetto` varchar(255) NOT NULL,
  `link_dettaglio` varchar(255) NOT NULL,
  PRIMARY KEY (`id_progetto`),
  UNIQUE KEY `link_dettaglio` (`link_dettaglio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `progetti`
--


LOCK TABLES `progetti` WRITE;
/*!40000 ALTER TABLE `progetti` DISABLE KEYS */;
INSERT INTO LCWEB_PORTFOLIO.progetti (id_progetto, alt_immagine_principale, descrizione, tipo, src_immagine_principale, nome_progetto, link_dettaglio) VALUES(1, 'Bagnolo', 'Grazie al mio sviluppo grafico per i social media, realizzo contenuti visivi accattivanti e su misura, capaci di catturare l’essenza di progetti unici, dando vita a un immaginario ricco di ruoli, luoghi e storie che coinvolgono ed emozionano il pubblico digitale.', 'Sviluppo', 'Img/1Lavoro.png', 'Social Content ristorante Bagnolo', 'dettagliolavoro.php?project=1');
INSERT INTO LCWEB_PORTFOLIO.progetti (id_progetto, alt_immagine_principale, descrizione, tipo, src_immagine_principale, nome_progetto, link_dettaglio) VALUES(2, 'Cinecittà', 'I video promozionali che si trovano di seguito, hanno l''obiettivo di esplicare nel migliore dei modi le esposizioni della mostra di Cinecittà, sottolineando l’immaginario cinematografico riguardante i ruoli, i luoghi e la storia di essa.', 'Sviluppo', 'Img/2Lavoro.png', 'Motion Graphics per Cinecittà', 'dettagliolavoro.php?project=2');
INSERT INTO LCWEB_PORTFOLIO.progetti (id_progetto, alt_immagine_principale, descrizione, tipo, src_immagine_principale, nome_progetto, link_dettaglio) VALUES(3, 'Qadmio', 'Con il mio sviluppo grafico di loghi per i social media, progetto identità visive distintive e memorabili. Ogni logo è pensato per riflettere l’essenza unica di un brand, combinando creatività e strategia. Il risultato sono simboli che catturano l’attenzione e si fissano nella mente del pubblico digitale.', 'Sviluppo', 'Img/3Lavoro.png', 'Qadmio Web Services', 'dettagliolavoro.php?project=3');
INSERT INTO LCWEB_PORTFOLIO.progetti (id_progetto, alt_immagine_principale, descrizione, tipo, src_immagine_principale, nome_progetto, link_dettaglio) VALUES(4, 'Polemos', 'Mediante il mio sviluppo grafico di visual per i social media, creo immagini che raccontano storie e attirano sguardi. Ogni visual è progettato per trasmettere emozioni e valorizzare l’identità di un progetto con stile e originalità.   Il mio obiettivo è rendere ogni contenuto un’esperienza visiva che lascia il segno nel pubblico online.', 'Sviluppo', 'Img/4Lavoro.png', 'Polemos Podcast', 'dettagliolavoro.php?project=4');
INSERT INTO LCWEB_PORTFOLIO.progetti (id_progetto, alt_immagine_principale, descrizione, tipo, src_immagine_principale, nome_progetto, link_dettaglio) VALUES(5, 'Adidas', 'Attraverso il mio sviluppo grafico per l’advertising sui social media, progetto contenuti che spiccano e persuadono. Ogni design è calibrato per suscitare interesse e rafforzare il brand con un mix di estetica e finalità. L’obiettivo è trasformare visioni in promozioni efficaci che risuonano con il pubblico online.', 'Sviluppo', 'Img/5Lavoro.png', 'PogbaXAdidas', 'dettagliolavoro.php?project=5');
INSERT INTO LCWEB_PORTFOLIO.progetti (id_progetto, alt_immagine_principale, descrizione, tipo, src_immagine_principale, nome_progetto, link_dettaglio) VALUES(6, 'Wildside', 'Con il mio sviluppo grafico per il branding sui social media, costruisco identità visive solide e riconoscibili. Ogni elemento è pensato per incarnare i valori di un marchio, fondendo coerenza stilistica e creatività unica. Il risultato è un’immagine di brand che si distingue e crea connessioni autentiche con il pubblico digitale.', 'Sviluppo', 'Img/6Lavoro.png', 'Branding Wildside Brewery', 'dettagliolavoro.php?project=6');
/*!40000 ALTER TABLE `progetti` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `servizi`
--

DROP TABLE IF EXISTS `servizi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `servizi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `servizi`
--

LOCK TABLES `servizi` WRITE;
/*!40000 ALTER TABLE `servizi` DISABLE KEYS */;
INSERT INTO LCWEB_PORTFOLIO.servizi (id, title, description) VALUES(1, 'Brand Design', 'Trasformo idee in identità visive che comunicano valori, personalità e unicità. Dallo sviluppo del logo alla definizione del tono visivo, mi assicuro che ogni progetto di branding racconti una storia distintiva.');
INSERT INTO LCWEB_PORTFOLIO.servizi (id, title, description) VALUES(2, 'Motion Design', 'Creo animazioni dinamiche che danno vita a idee complesse, rendendo la comunicazione visiva più coinvolgente ed efficace. Dai video promozionali alle infografiche animate, offro soluzioni innovative per catturare l’attenzione del pubblico.');
INSERT INTO LCWEB_PORTFOLIO.servizi (id, title, description) VALUES(3, 'Layout Design', 'Realizzo layout accattivanti per materiali pubblicitari, presentazioni aziendali e contenuti digitali. Ogni progetto è studiato per combinare estetica e funzionalità, adattandosi alle esigenze specifiche del cliente.');
INSERT INTO LCWEB_PORTFOLIO.servizi (id, title, description) VALUES(4, 'Web', 'Con una visione d’insieme e competenze organizzative, posso gestire ogni fase di un progetto creativo: dalla pianificazione all’esecuzione, fino alla consegna finale, garantendo qualità e puntualità.');
/*!40000 ALTER TABLE `servizi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utenti`
--

DROP TABLE IF EXISTS `utenti`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `utenti` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `cancellato` varchar(2) NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utenti`
--

LOCK TABLES `utenti` WRITE;
/*!40000 ALTER TABLE `utenti` DISABLE KEYS */;
INSERT INTO `utenti` VALUES (1,'mc.cipollini@gmail.com','$2y$10$6Ahm7SRq0lVS6Q8S/oThq.a29ulfbKrEoHzkuf6hZwINVy5bwqTUO','0','2025-07-15 18:55:55'),(2,'leonardocipollini108@gmail.com','$2y$10$xbL9wPJJ8KJAoJ9MeffPSeJ85hSLYwPgvQDIttixBVba5x.lriCaq','0','2025-07-19 13:57:01'),(3,'Matteo','$2y$10$h3G/U0mAJRiEJ39xSxEwf.LL.5XpgFp0eOmifJHC620vLOtFkO.Vy','0','2025-10-19 18:39:12'),(4, 'esame3', '$2y$10$UYlevm7C3kiqYe3wmtMyIul50Q4Dk0i/yBvJlQDou3BVW25tQROZ2', '0', '2025-11-02 23:39:23');
/*!40000 ALTER TABLE `utenti` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-02 20:27:09

notee;

quit