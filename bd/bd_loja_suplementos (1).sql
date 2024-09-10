CREATE DATABASE  IF NOT EXISTS `loja_suplementos` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `loja_suplementos`;
-- MySQL dump 10.13  Distrib 8.0.38, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: loja_suplementos
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `encomendas`
--

DROP TABLE IF EXISTS `encomendas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `encomendas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilizador_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `morada` text NOT NULL,
  `preco_total` decimal(10,2) NOT NULL,
  `data_encomenda` datetime DEFAULT current_timestamp(),
  `produtos` text DEFAULT NULL,
  `metodo_pagamento` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Produtos em processo de separação',
  PRIMARY KEY (`id`),
  KEY `utilizador_id` (`utilizador_id`),
  CONSTRAINT `encomendas_ibfk_1` FOREIGN KEY (`utilizador_id`) REFERENCES `utilizadores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `encomendas`
--

LOCK TABLES `encomendas` WRITE;
/*!40000 ALTER TABLE `encomendas` DISABLE KEYS */;
INSERT INTO `encomendas` VALUES (1,4,'','Rua Almada Negreiros 80, apt 9º direito, São Cosme',39.99,'2024-09-04 23:13:51','[{\"nome\":\"Whey Protein Goldstand 908g\",\"quantidade\":\"1\",\"preco\":\"39.99\"}]','Cartão de Crédito','Produtos em processo de separação'),(2,5,'','sdadasfdasfas',60.00,'2024-09-05 12:51:42','[{\"nome\":\"combo (Creatina + Whey)\",\"quantidade\":\"1\",\"preco\":\"60.00\"}]','Cartão de Crédito','Produtos em processo de separação'),(3,4,'','Rua Almada Negreiros 80, apt 9º direito, São Cosme',60.00,'2024-09-05 13:00:32','[{\"nome\":\"combo (Creatina + Whey)\",\"quantidade\":\"1\",\"preco\":\"60.00\"}]','Cartão de Crédito','Produtos em processo de separação'),(4,4,'','Rua Almada Negreiros 80, apt 9º direito, São Cosme',60.00,'2024-09-05 13:22:28','[{\"nome\":\"combo (Creatina + Whey)\",\"quantidade\":\"1\",\"preco\":\"60.00\"}]','Boleto Bancário','Produtos em processo de separação'),(5,4,'','Rua Almada Negreiros 80, apt 9º direito, São Cosme',24.00,'2024-09-05 13:35:34','[{\"nome\":\"Creatina Micronizada em P\\u00f3\",\"quantidade\":\"1\",\"preco\":\"24.00\"}]','Cartão de Crédito','Produtos em processo de separação'),(6,4,'gilsondag','teste',39.99,'2024-09-05 13:46:12','[{\"nome\":\"Whey Protein Goldstand 908g\",\"quantidade\":\"1\",\"preco\":\"39.99\"}]','Cartão de Crédito','Produtos em processo de separação'),(7,4,'gilsondag','teste 2',39.99,'2024-09-05 13:54:12','[{\"nome\":\"Whey Protein Goldstand 908g\",\"quantidade\":\"1\",\"preco\":\"39.99\"}]','Cartão de Crédito','Cancelada'),(8,4,'gilsondag','Rua Almada Negreiros 80, apt 9º direito, São Cosme',24.00,'2024-09-05 14:03:05','[{\"nome\":\"Creatina Micronizada em P\\u00f3\",\"quantidade\":\"1\",\"preco\":\"24.00\"}]','Cartão de Crédito','Cancelada'),(9,6,'viniciusdiogo','Rua Almada Negreiros 80, apt 9º direito, São Cosme',39.99,'2024-09-06 22:16:32','[{\"nome\":\"Whey Protein Goldstand 908g\",\"quantidade\":\"1\",\"preco\":\"39.99\"}]','Cartão de Crédito','Produtos em processo de separação');
/*!40000 ALTER TABLE `encomendas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `encomendas_produtos`
--

DROP TABLE IF EXISTS `encomendas_produtos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `encomendas_produtos` (
  `encomenda_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  PRIMARY KEY (`encomenda_id`,`produto_id`),
  KEY `produto_id` (`produto_id`),
  CONSTRAINT `encomendas_produtos_ibfk_1` FOREIGN KEY (`encomenda_id`) REFERENCES `encomendas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `encomendas_produtos_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `encomendas_produtos`
--

LOCK TABLES `encomendas_produtos` WRITE;
/*!40000 ALTER TABLE `encomendas_produtos` DISABLE KEYS */;
/*!40000 ALTER TABLE `encomendas_produtos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produtos`
--

DROP TABLE IF EXISTS `produtos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produtos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 0,
  `imagens` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produtos`
--

LOCK TABLES `produtos` WRITE;
/*!40000 ALTER TABLE `produtos` DISABLE KEYS */;
INSERT INTO `produtos` VALUES (17,'Whey Protein Goldstand 908g',39.99,7,'66cde4b173336.jpg'),(18,'Creatina Micronizada em Pó',24.00,8,'66ce0f52122f0.jpg'),(19,'combo (Creatina + Whey)',60.00,2,'66ce0f63cc1a1.jpg,66ce0f63cde3e.jpg');
/*!40000 ALTER TABLE `produtos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilizadores`
--

DROP TABLE IF EXISTS `utilizadores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `utilizadores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo` enum('admin','cliente') NOT NULL,
  `data_nascimento` date NOT NULL,
  `morada` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilizadores`
--

LOCK TABLES `utilizadores` WRITE;
/*!40000 ALTER TABLE `utilizadores` DISABLE KEYS */;
INSERT INTO `utilizadores` VALUES (2,'admin','gilsongalvao@outlook.pt','admin_senha','admin','0000-00-00',' '),(4,'gilsondag','gilsondiogo@hotmail.com','SENHA','cliente','1992-04-07','Rua Franklin Távora, 48'),(5,'jordana','jordana.coimbraa@gmail.com','nova_senha','cliente','1994-04-28','dsafasfsafasfsafa'),(6,'viniciusdiogo','viniciusdiogoadm@gmail.com','nova_senha','cliente','1995-10-21','Rua Nova do Seixo 693, apt 4 esquerdo tras');
/*!40000 ALTER TABLE `utilizadores` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-09-06 23:22:25
