--
-- source files list for table `file_queue`
--

INSERT INTO `file_queue` VALUES 
(1,'levy.framingham.rnaseq.1539.v2.init.tsv','sample-study-dataset','2020-07-17 00:00:00',NULL,'2020-07-20 18:57:56',NULL),
(2,'mesa.pilot.harmonized.v3.init.tsv','sample-study-dataset','2020-10-06 00:00:00',NULL,'2020-10-06 18:57:56',NULL),
(3,'meyers.topmed.rnaseq.v2.init.tsv','sample-study-dataset','2020-05-04 00:00:00',NULL,'2020-07-20 18:57:56',NULL),
(4,'ramachandran.framingham.v2.init.tsv','sample-study-dataset','2020-05-04 00:00:00',NULL,'2020-07-20 18:57:56',NULL),
(5,'silverman.uw.rnaseq.v2.init.tsv','sample-study-dataset','2020-07-17 00:00:00',NULL,'2020-07-20 18:57:56',NULL),
(6,'TOPMed.Burchard.v2.init.tsv','sample-study-dataset','2020-07-17 00:00:00',NULL,'2020-07-20 18:57:56',NULL),
(7,'TOPMed.Gelb.v2.init.tsv','sample-study-dataset','2020-05-04 00:00:00',NULL,'2020-07-20 18:57:56',NULL),
(8,'TOPMed.Kooperberg.v2.init.tsv','sample-study-dataset','2020-05-04 00:00:00',NULL,'2020-07-20 18:57:56',NULL),
(9,'TOPMed.Taylor.v2.init.tsv','sample-study-dataset','2020-05-04 00:00:00',NULL,'2020-07-20 18:57:56',NULL),
(10,'levy.framingham.rnaseq.1539.metrics.tsv','NWGC.RNASeQCv2.3.4','2019-09-27 00:00:00',NULL,'2020-07-20 19:01:14',NULL),
(11,'mesa.pilot.harmonized.qc.metrics.tsv','UMich.RNASeQCv2.3.3','2020-07-28 00:00:00',NULL,'2020-10-06 19:01:14',NULL),
(12,'meyers.topmed.rnaseq.metrics.tsv','NWGC.RNASeQCv2.3.4','2019-12-12 00:00:00',NULL,'2020-07-20 19:01:14',NULL),
(13,'ramachandran.topmed.to4.rnaseq.metrics.tsv','NWGC.RNASeQCv2.3.4','2020-03-17 00:00:00',NULL,'2020-07-20 19:01:14',NULL),
(14,'silverman.uw.rnaseq.v2.metrics.tsv','NWGC.RNASeQCv2.3.4','2019-10-15 00:00:00',NULL,'2020-07-20 19:01:14',NULL),
(15,'TOPMed.Burchard.P4.RNASeQCv2.3.4.metrics.tsv','Broad.RNASeQCv2.3.4','2020-02-23 00:00:00',NULL,'2020-07-20 19:01:14',NULL),
(16,'TOPMed.Gelb.P4.RNASeQCv2.3.4.metrics.tsv','Broad.RNASeQCv2.3.4','2020-02-19 00:00:00',NULL,'2020-07-20 19:01:14',NULL),
(17,'TOPMed.Kooperberg.P4.RNASeQCv2.3.4.metrics.tsv','Broad.RNASeQCv2.3.4','2020-01-03 00:00:00',NULL,'2020-07-20 19:01:14',NULL),
(18,'TOPMed.Taylor.P4.RNASeQCv2.3.4.metrics.tsv','Broad.RNASeQCv2.3.4','2020-03-26 00:00:00',NULL,'2020-07-20 19:01:14',NULL); 

--
-- Insert studies data updated on Oct 6, 2020
--
INSERT INTO `studies` (`id`, `study`, `pi`, `datatype`, `center`, `samplereceived`) VALUES
('1', 'GALAII', 'Burchard', 'RNA-seq', 'Broad', '2491'),
('2', 'SAGE', 'Burchard', 'RNA-seq', 'Broad', '950'),
('3', 'WHI', 'Kooperberg', 'RNA-seq', 'Broad', '1367'),
('4', 'Framingham', 'Levy', 'RNA-seq', 'NWGC', '1522'),
('5', 'Spiromics', 'Meyers', 'RNA-seq', 'NWGC', '3980'),
('6', 'Framingham', 'Ramachandran', 'RNA-seq', 'NWGC', '1191'),
('7', 'MESA pilot', 'Rotter', 'RNA-seq', 'Broad', '1500'),
('8', 'MESA pilot', 'Rotter', 'RNA-seq', 'NWGC', '1473'),
('9', 'PCGC', 'Seidman', 'RNA-seq', 'Broad', '95'),
('10', 'LTRC', 'Silverman', 'RNA-seq', 'NWGC', '2354'),
('11', 'TOP-CHeF', 'M.Taylor', 'RNA-seq', 'Broad', '654'),
('12', 'Sentinel', 'UW', 'RNA-seq', 'NWGC', '105'),
('13', 'PVDOMICS', 'Erzurum', 'Methylation', 'NWGC', '958'),
('14', 'CARDIA', 'Fornage', 'Methylation', 'USC', '8983'),
('15', 'WHI', 'Kooperberg', 'Methylation', 'USC', '1334'),
('16', 'Framingham', 'Ramachandran', 'Methylation', 'USC', '1814'),
('17', 'MESA pilot', 'Rotter', 'Methylation', 'USC', '2086'),
('18', 'COPDGene', 'Silverman', 'Methylation', 'NWGC', '11843'),
('19', 'LTRC', 'Silverman', 'Methylation', 'NWGC', '3041'),
('20', 'CAMP', 'Weiss', 'Methylation', 'USC', '1616'),
('21', 'CRA', 'Weiss', 'Methylation', 'USC', '1238'),
('22', 'WHI', 'Kooperberg', 'Metabolomics', 'Broad', '1400'),
('23', 'Framingham', 'Ramachandran', 'Metabolomics', 'Broad', '3025'),
('24', 'CAMP', 'Weiss', 'Metabolomics', 'Broad', '962'),
('25', 'CRA', 'Weiss', 'Metabolomics', 'Broad', '2038');
