# TYPO3 Extension Manager dump 1.1
#
#--------------------------------------------------------


#
# Table structure for table "static_territories"
#
DROP TABLE IF EXISTS static_territories;
CREATE TABLE static_territories (
  uid int(11) unsigned NOT NULL auto_increment,
  pid int(11) unsigned default '0',
  tr_iso_nr int(11) unsigned default '0',
  tr_parent_iso_nr int(11) unsigned default '0',
  tr_name_en varchar(50) default '',
  PRIMARY KEY (uid),
  UNIQUE uid (uid)
);


INSERT INTO static_territories VALUES ('1', '0', '2', '0', 'Africa');
INSERT INTO static_territories VALUES ('2', '0', '9', '0', 'Oceania');
INSERT INTO static_territories VALUES ('3', '0', '19', '0', 'Americas');
INSERT INTO static_territories VALUES ('4', '0', '142', '0', 'Asia');
INSERT INTO static_territories VALUES ('5', '0', '150', '0', 'Europe');
INSERT INTO static_territories VALUES ('6', '0', '30', '142', 'Eastern Asia');
INSERT INTO static_territories VALUES ('7', '0', '35', '142', 'South-eastern Asia');
INSERT INTO static_territories VALUES ('8', '0', '143', '142', 'Central Asia');
INSERT INTO static_territories VALUES ('9', '0', '145', '142', 'Western Asia');
INSERT INTO static_territories VALUES ('10', '0', '39', '150', 'Southern Europe');
INSERT INTO static_territories VALUES ('11', '0', '151', '150', 'Eastern Europe');
INSERT INTO static_territories VALUES ('12', '0', '154', '150', 'Northern Europe');
INSERT INTO static_territories VALUES ('13', '0', '155', '150', 'Western Europe');
INSERT INTO static_territories VALUES ('16', '0', '5', '19', 'South America');
INSERT INTO static_territories VALUES ('17', '0', '13', '19', 'Central America');
INSERT INTO static_territories VALUES ('18', '0', '21', '19', 'Northern America');
INSERT INTO static_territories VALUES ('19', '0', '29', '19', 'Caribbean');
INSERT INTO static_territories VALUES ('20', '0', '11', '2', 'Western Africa');
INSERT INTO static_territories VALUES ('21', '0', '14', '2', 'Eastern Africa');
INSERT INTO static_territories VALUES ('22', '0', '15', '2', 'Northern Africa');
INSERT INTO static_territories VALUES ('23', '0', '17', '2', 'Middle Africa');
INSERT INTO static_territories VALUES ('24', '0', '18', '2', 'Southern Africa');
INSERT INTO static_territories VALUES ('25', '0', '53', '9', 'Australia and New Zealand');
INSERT INTO static_territories VALUES ('26', '0', '54', '9', 'Melanesia');
INSERT INTO static_territories VALUES ('27', '0', '57', '9', 'Micronesian Region');
INSERT INTO static_territories VALUES ('28', '0', '61', '9', 'Polynesia');
INSERT INTO static_territories VALUES ('30', '0', '34', '142', 'Southern Asia');



# TYPO3 Extension Manager dump 1.1
#
#--------------------------------------------------------


#
# Table structure for table "static_countries"
#
DROP TABLE IF EXISTS static_countries;
CREATE TABLE static_countries (
  uid int(11) unsigned NOT NULL auto_increment,
  pid int(11) unsigned default '0',
  deleted tinyint(4) NOT NULL default '0',
  cn_iso_2 char(2) default '',
  cn_iso_3 char(3) default '',
  cn_iso_nr int(11) unsigned default '0',
  cn_parent_tr_iso_nr int(11) unsigned default '0',
  cn_official_name_local varchar(128) default '',
  cn_official_name_en varchar(128) default '',
  cn_capital varchar(45) default '',
  cn_tldomain char(2) default '',
  cn_currency_iso_3 char(3) default '',
  cn_currency_iso_nr int(10) unsigned default '0',
  cn_phone int(10) unsigned default '0',
  cn_eu_member tinyint(3) unsigned default '0',
  cn_address_format tinyint(3) unsigned default '0',
  cn_zone_flag tinyint(4) default '0',
  cn_short_local varchar(70) default '',
  cn_short_en varchar(50) default '',
  cn_uno_member tinyint(3) unsigned default '0',
  PRIMARY KEY (uid),
  UNIQUE uid (uid)
);


INSERT INTO static_countries VALUES ('1', '0', '0', 'AD', 'AND', '20', '39', 'Principat d\'Andorra', 'Principality of Andorra', 'Andorra la Vella', 'ad', 'EUR', '978', '376', '0', '1', '0', 'Andorra', 'Andorra', '1');
INSERT INTO static_countries VALUES ('2', '0', '0', 'AE', 'ARE', '784', '0', 'الإمارات العربيّة المتّحدة', 'United Arab Emirates', 'Abu Dhabi', 'ae', 'AED', '784', '971', '0', '1', '0', 'الإمارات العربيّة المتّحدة', 'United Arab Emirates', '1');
INSERT INTO static_countries VALUES ('3', '0', '0', 'AF', 'AFG', '4', '34', 'د افغانستان اسلامي دولت', 'Islamic Republic of Afghanistan', 'Kabul', 'af', 'AFN', '971', '93', '0', '2', '0', 'افغانستان', 'Afghanistan', '1');
INSERT INTO static_countries VALUES ('4', '0', '0', 'AG', 'ATG', '28', '29', 'Antigua and Barbuda', 'Antigua and Barbuda', 'St John\'s', 'ag', 'XCD', '951', '1268', '0', '1', '0', 'Antigua and Barbuda', 'Antigua and Barbuda', '1');
INSERT INTO static_countries VALUES ('5', '0', '0', 'AI', 'AIA', '660', '29', 'Anguilla', 'Anguilla', 'The Valley', 'ai', 'XCD', '951', '1264', '0', '1', '0', 'Anguilla', 'Anguilla', '0');
INSERT INTO static_countries VALUES ('6', '0', '0', 'AL', 'ALB', '8', '39', 'Republika e Shqipërisë', 'Republic of Albania', 'Tirana', 'al', 'ALL', '8', '355', '0', '1', '0', 'Shqipëria', 'Albania', '1');
INSERT INTO static_countries VALUES ('7', '0', '0', 'AM', 'ARM', '51', '145', 'Հայաստանի Հանրապետություն', 'Republic of Armenia', 'Yerevan', 'am', 'AMD', '51', '374', '0', '1', '0', 'Հայաստան', 'Armenia', '1');
INSERT INTO static_countries VALUES ('8', '0', '1', 'AN', 'ANT', '530', '29', 'Nederlandse Antillen', 'Netherlands Antilles', 'Willemstad', 'an', 'ANG', '532', '599', '0', '1', '0', 'Nederlandse Antillen', 'Netherlands Antilles', '0');
INSERT INTO static_countries VALUES ('9', '0', '0', 'AO', 'AGO', '24', '17', 'República de Angola', 'Republic of Angola', 'Luanda', 'ao', 'AOA', '973', '244', '0', '1', '0', 'Angola', 'Angola', '1');
INSERT INTO static_countries VALUES ('10', '0', '0', 'AQ', 'ATA', '10', '0', 'Antarctica', 'Antarctica', '', 'aq', '', '0', '67212', '0', '1', '0', 'Antarctica', 'Antarctica', '0');
INSERT INTO static_countries VALUES ('11', '0', '0', 'AR', 'ARG', '32', '5', 'República Argentina', 'Argentine Republic', 'Buenos Aires', 'ar', 'ARS', '32', '54', '0', '2', '0', 'Argentina', 'Argentina', '1');
INSERT INTO static_countries VALUES ('12', '0', '0', 'AS', 'ASM', '16', '61', 'Amerika Samoa', 'American Samoa', 'Pago Pago', 'as', 'USD', '840', '685', '0', '1', '0', 'Amerika Samoa', 'American Samoa', '0');
INSERT INTO static_countries VALUES ('13', '0', '0', 'AT', 'AUT', '40', '155', 'Republik Österreich', 'Republic of Austria', 'Vienna', 'at', 'EUR', '978', '43', '1', '1', '0', 'Österreich', 'Austria', '1');
INSERT INTO static_countries VALUES ('14', '0', '0', 'AU', 'AUS', '36', '53', 'Commonwealth of Australia', 'Commonwealth of Australia', 'Canberra', 'au', 'AUD', '36', '61', '0', '3', '0', 'Australia', 'Australia', '1');
INSERT INTO static_countries VALUES ('15', '0', '0', 'AW', 'ABW', '533', '29', 'Aruba', 'Aruba', 'Oranjestad', 'aw', 'AWG', '533', '297', '0', '0', '0', 'Aruba', 'Aruba', '0');
INSERT INTO static_countries VALUES ('16', '0', '0', 'AZ', 'AZE', '31', '145', 'Azərbaycan Respublikası', 'Republic of Azerbaijan', 'Baku', 'az', 'AZM', '31', '994', '0', '1', '0', 'Azərbaycan', 'Azerbaijan', '1');
INSERT INTO static_countries VALUES ('17', '0', '0', 'BA', 'BIH', '70', '39', 'Bosna i Hercegovina / Босна и Херцеговина', 'Bosnia and Herzegovina', 'Sarajevo', 'ba', 'BAM', '977', '387', '0', '0', '0', 'BiH/БиХ', 'Bosnia and Herzegovina', '1');
INSERT INTO static_countries VALUES ('18', '0', '0', 'BB', 'BRB', '52', '29', 'Barbados', 'Barbados', 'Bridgetown', 'bb', 'BBD', '52', '1246', '0', '1', '0', 'Barbados', 'Barbados', '1');
INSERT INTO static_countries VALUES ('19', '0', '0', 'BD', 'BGD', '50', '34', 'গনপ্রজাতন্ত্রী বাংলা', 'People’s Republic of Bangladesh', 'Dhaka', 'bd', 'BDT', '50', '880', '0', '1', '0', 'বাংলাদেশ', 'Bangladesh', '1');
INSERT INTO static_countries VALUES ('20', '0', '0', 'BE', 'BEL', '56', '155', 'Koninkrijk België / Royaume de Belgique', 'Kingdom of Belgium', 'Brussels', 'be', 'EUR', '978', '32', '1', '1', '0', 'Belgique', 'Belgium', '1');
INSERT INTO static_countries VALUES ('21', '0', '0', 'BF', 'BFA', '854', '11', 'Burkina Faso', 'Burkina Faso', 'Ouagadougou', 'bf', 'XOF', '952', '226', '0', '1', '0', 'Burkina', 'Burkina Faso', '1');
INSERT INTO static_countries VALUES ('22', '0', '0', 'BG', 'BGR', '100', '151', 'Република България', 'Republic of Bulgaria', 'Sofia', 'bg', 'BGL', '100', '359', '1', '1', '0', 'България', 'Bulgaria', '1');
INSERT INTO static_countries VALUES ('23', '0', '0', 'BH', 'BHR', '48', '145', 'مملكة البحرين', 'Kingdom of Bahrain', 'Manama', 'bh', 'BHD', '48', '973', '0', '1', '0', 'البحري', 'Bahrain', '1');
INSERT INTO static_countries VALUES ('24', '0', '0', 'BI', 'BDI', '108', '14', 'Republika y\'u Burundi', 'Republic of Burundi', 'Bujumbura', 'bi', 'BIF', '108', '257', '0', '1', '0', 'Burundi', 'Burundi', '1');
INSERT INTO static_countries VALUES ('25', '0', '0', 'BJ', 'BEN', '204', '11', 'République du Bénin', 'Republic of Benin', 'Porto Novo', 'bj', 'XOF', '952', '229', '0', '1', '0', 'Bénin', 'Benin', '1');
INSERT INTO static_countries VALUES ('26', '0', '0', 'BM', 'BMU', '60', '21', 'Bermuda', 'Bermuda', 'Hamilton', 'bm', 'BMD', '60', '1441', '0', '1', '0', 'Bermuda', 'Bermuda', '0');
INSERT INTO static_countries VALUES ('27', '0', '0', 'BN', 'BRN', '96', '35', 'برني دارالسلام', 'Sultanate of Brunei', 'Bandar Seri Begawan', 'bn', 'BND', '96', '673', '0', '1', '0', 'دارالسلام', 'Brunei', '1');
INSERT INTO static_countries VALUES ('28', '0', '0', 'BO', 'BOL', '68', '5', 'Estado Plurinacional de Bolivia', 'Plurinational State of Bolivia', 'Sucre', 'bo', 'BOB', '68', '591', '0', '1', '0', 'Bolivia', 'Bolivia', '1');
INSERT INTO static_countries VALUES ('29', '0', '0', 'BR', 'BRA', '76', '5', 'República Federativa do Brasil', 'Federative Republic of Brazil', 'Brasilia', 'br', 'BRL', '986', '55', '0', '9', '0', 'Brasil', 'Brazil', '1');
INSERT INTO static_countries VALUES ('30', '0', '0', 'BS', 'BHS', '44', '29', 'Commonwealth of The Bahamas', 'Commonwealth of The Bahamas', 'Nassau', 'bs', 'BSD', '44', '1242', '0', '1', '0', 'The Bahamas', 'The Bahamas', '1');
INSERT INTO static_countries VALUES ('31', '0', '0', 'BT', 'BTN', '64', '34', 'Druk-Yul', 'Kingdom of Bhutan', 'Thimphu', 'bt', 'BTN', '64', '975', '0', '1', '0', 'Druk-Yul', 'Bhutan', '1');
INSERT INTO static_countries VALUES ('32', '0', '0', 'BV', 'BVT', '74', '0', 'Bouvet Island', 'Bouvet Island', '', 'bv', 'NOK', '578', '0', '0', '1', '0', 'Bouvet Island', 'Bouvet Island', '0');
INSERT INTO static_countries VALUES ('33', '0', '0', 'BW', 'BWA', '72', '18', 'Republic of Botswana', 'Republic of Botswana', 'Gaborone', 'bw', 'BWP', '72', '267', '0', '1', '0', 'Botswana', 'Botswana', '1');
INSERT INTO static_countries VALUES ('34', '0', '0', 'BY', 'BLR', '112', '151', 'Рэспубліка Беларусь', 'Republic of Belarus', 'Minsk', 'by', 'BYR', '974', '375', '0', '1', '0', 'Беларусь', 'Belarus', '1');
INSERT INTO static_countries VALUES ('35', '0', '0', 'BZ', 'BLZ', '84', '13', 'Belize', 'Belize', 'Belmopan', 'bz', 'BZD', '84', '501', '0', '1', '0', 'Belize', 'Belize', '1');
INSERT INTO static_countries VALUES ('36', '0', '0', 'CA', 'CAN', '124', '21', 'Canada', 'Canada', 'Ottawa', 'ca', 'CAD', '124', '1', '0', '4', '0', 'Canada', 'Canada', '1');
INSERT INTO static_countries VALUES ('37', '0', '0', 'CC', 'CCK', '166', '53', 'Territory of Cocos (Keeling) Islands', 'Territory of Cocos (Keeling) Islands', 'Bantam', 'cc', 'AUD', '36', '6722', '0', '1', '0', 'Cocos (Keeling) Islands', 'Cocos (Keeling) Islands', '0');
INSERT INTO static_countries VALUES ('38', '0', '0', 'CD', 'COD', '180', '17', 'République Démocratique du Congo', 'Democratic Republic of the Congo', 'Kinshasa', 'cd', 'CDF', '976', '243', '0', '0', '0', 'Congo', 'Congo', '1');
INSERT INTO static_countries VALUES ('39', '0', '0', 'CF', 'CAF', '140', '17', 'République Centrafricaine', 'Central African Republic', 'Bangui', 'cf', 'XAF', '950', '236', '0', '1', '0', 'Centrafrique', 'Central African Republic', '1');
INSERT INTO static_countries VALUES ('40', '0', '0', 'CG', 'COG', '178', '17', 'République du Congo', 'Republic of the Congo', 'Brazzaville', 'cg', 'XAF', '950', '242', '0', '1', '0', 'Congo-Brazzaville', 'Congo-Brazzaville', '1');
INSERT INTO static_countries VALUES ('41', '0', '0', 'CH', 'CHE', '756', '155', 'Confédération suisse / Schweizerische Eidgenossenschaft', 'Swiss Confederation', 'Berne', 'ch', 'CHF', '756', '41', '0', '1', '0', 'Schweiz', 'Switzerland', '1');
INSERT INTO static_countries VALUES ('42', '0', '0', 'CI', 'CIV', '384', '11', 'République de Côte d’Ivoire', 'Republic of Côte d\'Ivoire', 'Yamoussoukro', 'ci', 'XOF', '952', '225', '0', '2', '0', 'Côte d’Ivoire', 'Côte d’Ivoire', '1');
INSERT INTO static_countries VALUES ('43', '0', '0', 'CK', 'COK', '184', '61', 'Cook Islands', 'Cook Islands', 'Avarua', 'ck', 'NZD', '554', '682', '0', '1', '0', 'Cook Islands', 'Cook Islands', '0');
INSERT INTO static_countries VALUES ('44', '0', '0', 'CL', 'CHL', '152', '5', 'República de Chile', 'Republic of Chile', 'Santiago', 'cl', 'CLP', '152', '56', '0', '1', '0', 'Chile', 'Chile', '1');
INSERT INTO static_countries VALUES ('45', '0', '0', 'CM', 'CMR', '120', '17', 'Republic of Cameroon / République du Cameroun', 'Republic of Cameroon', 'Yaoundé', 'cm', 'XAF', '950', '237', '0', '1', '0', 'Cameroun', 'Cameroon', '1');
INSERT INTO static_countries VALUES ('46', '0', '0', 'CN', 'CHN', '156', '30', '中华人民共和国', 'People’s Republic of China', 'Beijing', 'cn', 'CNY', '156', '86', '0', '1', '0', '中华', 'China', '1');
INSERT INTO static_countries VALUES ('47', '0', '0', 'CO', 'COL', '170', '5', 'República de Colombia', 'Republic of Colombia', 'Bogotá', 'co', 'COP', '170', '57', '0', '1', '0', 'Colombia', 'Colombia', '1');
INSERT INTO static_countries VALUES ('48', '0', '0', 'CR', 'CRI', '188', '13', 'República de Costa Rica', 'Republic of Costa Rica', 'San José', 'cr', 'CRC', '188', '506', '0', '1', '0', 'Costa Rica', 'Costa Rica', '1');
INSERT INTO static_countries VALUES ('49', '0', '0', 'CU', 'CUB', '192', '29', 'República de Cuba', 'Republic of Cuba', 'Havana', 'cu', 'CUP', '192', '53', '0', '1', '0', 'Cuba', 'Cuba', '1');
INSERT INTO static_countries VALUES ('50', '0', '0', 'CV', 'CPV', '132', '11', 'República de Cabo Verde', 'Republic of Cape Verde', 'Praia', 'cv', 'CVE', '132', '238', '0', '1', '0', 'Cabo Verde', 'Cape Verde', '1');
INSERT INTO static_countries VALUES ('51', '0', '0', 'CX', 'CXR', '162', '0', 'Territory of Christmas Island', 'Territory of Christmas Island', 'Flying Fish Cove', 'cx', 'AUD', '36', '6724', '0', '1', '0', 'Christmas Island', 'Christmas Island', '0');
INSERT INTO static_countries VALUES ('52', '0', '0', 'CY', 'CYP', '196', '145', 'Κυπριακή Δημοκρατία / Kıbrıs Cumhuriyeti', 'Republic of Cyprus', 'Nicosia', 'cy', 'CYP', '196', '357', '1', '1', '0', 'Κύπρος / Kıbrıs', 'Cyprus', '1');
INSERT INTO static_countries VALUES ('53', '0', '0', 'CZ', 'CZE', '203', '151', 'Česká republika', 'Czech Republic', 'Prague', 'cz', 'CZK', '203', '420', '1', '1', '0', 'Cesko', 'Czech Republic', '1');
INSERT INTO static_countries VALUES ('54', '0', '0', 'DE', 'DEU', '276', '155', 'Bundesrepublik Deutschland', 'Federal Republic of Germany', 'Berlin', 'de', 'EUR', '978', '49', '1', '1', '0', 'Deutschland', 'Germany', '1');
INSERT INTO static_countries VALUES ('55', '0', '0', 'DJ', 'DJI', '262', '14', 'جمهورية جيبوتي / République de Djibouti', 'Republic of Djibouti', 'Djibouti', 'dj', 'DJF', '262', '253', '0', '1', '0', 'جيبوتي /Djibouti', 'Djibouti', '1');
INSERT INTO static_countries VALUES ('56', '0', '0', 'DK', 'DNK', '208', '154', 'Kongeriget Danmark', 'Kingdom of Denmark', 'Copenhagen', 'dk', 'DKK', '208', '45', '1', '1', '0', 'Danmark', 'Denmark', '1');
INSERT INTO static_countries VALUES ('57', '0', '0', 'DM', 'DMA', '212', '29', 'Commonwealth of Dominica', 'Commonwealth of Dominica', 'Roseau', 'dm', 'XCD', '951', '1767', '0', '1', '0', 'Dominica', 'Dominica', '1');
INSERT INTO static_countries VALUES ('58', '0', '0', 'DO', 'DOM', '214', '29', 'República Dominicana', 'Dominican Republic', 'Santo Domingo', 'do', 'DOP', '214', '1809', '0', '1', '0', 'Quisqueya', 'Dominican Republic', '1');
INSERT INTO static_countries VALUES ('59', '0', '0', 'DZ', 'DZA', '12', '15', 'الجمهورية الجزائرية الديمقراطية', 'People’s Democratic Republic of Algeria', 'Algiers', 'dz', 'DZD', '12', '213', '0', '1', '0', 'الجزائ', 'Algeria', '1');
INSERT INTO static_countries VALUES ('60', '0', '0', 'EC', 'ECU', '218', '5', 'República del Ecuador', 'Republic of Ecuador', 'Quito', 'ec', 'USD', '840', '593', '0', '1', '0', 'Ecuador', 'Ecuador', '1');
INSERT INTO static_countries VALUES ('61', '0', '0', 'EE', 'EST', '233', '154', 'Eesti Vabariik', 'Republic of Estonia', 'Tallinn', 'ee', 'EEK', '233', '372', '1', '1', '0', 'Eesti', 'Estonia', '1');
INSERT INTO static_countries VALUES ('62', '0', '0', 'EG', 'EGY', '818', '15', 'جمهوريّة مصر العربيّة', 'Arab Republic of Egypt', 'Cairo', 'eg', 'EGP', '818', '20', '0', '1', '0', 'مصر', 'Egypt', '1');
INSERT INTO static_countries VALUES ('63', '0', '0', 'EH', 'ESH', '732', '15', 'الصحراء الغربية', 'Western Sahara', 'El Aaiún', 'eh', 'MAD', '504', '212', '0', '1', '0', 'الصحراء الغربي', 'Western Sahara', '0');
INSERT INTO static_countries VALUES ('64', '0', '0', 'ER', 'ERI', '232', '14', 'ሃግሬ ኤርትራ', 'State of Eritrea', 'Asmara', 'er', 'ERN', '232', '291', '0', '1', '0', 'ኤርትራ', 'Eritrea', '1');
INSERT INTO static_countries VALUES ('65', '0', '0', 'ES', 'ESP', '724', '39', 'Reino de España', 'Kingdom of Spain', 'Madrid', 'es', 'EUR', '978', '34', '1', '8', '0', 'España', 'Spain', '1');
INSERT INTO static_countries VALUES ('66', '0', '0', 'ET', 'ETH', '231', '14', 'የኢትዮጵያ ፌዴራላዊ', 'Federal Democratic Republic of Ethiopia', 'Addis Ababa', 'et', 'ETB', '230', '251', '0', '1', '0', 'ኢትዮጵያ', 'Ethiopia', '1');
INSERT INTO static_countries VALUES ('67', '0', '0', 'FI', 'FIN', '246', '154', 'Suomen Tasavalta / Republiken Finland', 'Republic of Finland', 'Helsinki', 'fi', 'EUR', '978', '358', '1', '1', '0', 'Suomi', 'Finland', '1');
INSERT INTO static_countries VALUES ('68', '0', '0', 'FJ', 'FJI', '242', '54', 'Republic of the Fiji Islands / Matanitu Tu-Vaka-i-koya ko Vi', 'Republic of the Fiji Islands', 'Suva', 'fj', 'FJD', '242', '679', '0', '1', '0', 'Viti', 'Fiji', '1');
INSERT INTO static_countries VALUES ('69', '0', '0', 'FK', 'FLK', '238', '5', 'Falkland Islands', 'Falkland Islands', 'Stanley', 'fk', 'FKP', '238', '500', '0', '1', '0', 'Falkland Islands', 'Falkland Islands', '0');
INSERT INTO static_countries VALUES ('70', '0', '0', 'FM', 'FSM', '583', '57', 'Federated States of Micronesia', 'Federated States of Micronesia', 'Palikir', 'fm', 'USD', '840', '691', '0', '1', '0', 'Micronesia', 'Micronesia', '1');
INSERT INTO static_countries VALUES ('71', '0', '0', 'FO', 'FRO', '234', '154', 'Føroyar / Færøerne', 'Faroe Islands', 'Thorshavn', 'fo', 'DKK', '208', '298', '0', '1', '0', 'Føroyar / Færøerne', 'Faroes', '0');
INSERT INTO static_countries VALUES ('72', '0', '0', 'FR', 'FRA', '250', '155', 'République française', 'French Republic', 'Paris', 'fr', 'EUR', '978', '33', '1', '1', '0', 'France', 'France', '1');
INSERT INTO static_countries VALUES ('73', '0', '0', 'GA', 'GAB', '266', '17', 'République Gabonaise', 'Gabonese Republic', 'Libreville', 'ga', 'XAF', '950', '241', '0', '1', '0', 'Gabon', 'Gabon', '1');
INSERT INTO static_countries VALUES ('74', '0', '0', 'GB', 'GBR', '826', '154', 'United Kingdom of Great Britain and Northern', 'United Kingdom of Great Britain and Northern', 'London', 'uk', 'GBP', '826', '44', '1', '5', '0', 'United Kingdom', 'United Kingdom', '1');
INSERT INTO static_countries VALUES ('75', '0', '0', 'GD', 'GRD', '308', '29', 'Grenada', 'Grenada', 'St George\'s', 'gd', 'XCD', '951', '1473', '0', '1', '0', 'Grenada', 'Grenada', '1');
INSERT INTO static_countries VALUES ('76', '0', '0', 'GE', 'GEO', '268', '145', 'საქართველო', 'Georgia', 'Tbilisi', 'ge', 'GEL', '981', '995', '0', '1', '0', 'საქართველო', 'Georgia', '1');
INSERT INTO static_countries VALUES ('77', '0', '0', 'GF', 'GUF', '254', '5', 'Guyane française', 'French Guiana', 'Cayenne', 'gf', 'EUR', '978', '594', '0', '1', '0', 'Guyane française', 'French Guiana', '0');
INSERT INTO static_countries VALUES ('78', '0', '0', 'GH', 'GHA', '288', '11', 'Republic of Ghana', 'Republic of Ghana', 'Accra', 'gh', 'GHC', '288', '233', '0', '1', '0', 'Ghana', 'Ghana', '1');
INSERT INTO static_countries VALUES ('79', '0', '0', 'GI', 'GIB', '292', '39', 'Gibraltar', 'Gibraltar', 'Gibraltar', 'gi', 'GIP', '292', '350', '0', '1', '0', 'Gibraltar', 'Gibraltar', '0');
INSERT INTO static_countries VALUES ('80', '0', '0', 'GL', 'GRL', '304', '21', 'Kalaallit Nunaat / Grønland', 'Greenland', 'Nuuk', 'gl', 'DKK', '208', '299', '0', '1', '0', 'Grønland', 'Greenland', '0');
INSERT INTO static_countries VALUES ('81', '0', '0', 'GM', 'GMB', '270', '11', 'Republic of The Gambia', 'Republic of The Gambia', 'Banjul', 'gm', 'GMD', '270', '220', '0', '1', '0', 'Gambia', 'Gambia', '1');
INSERT INTO static_countries VALUES ('82', '0', '0', 'GN', 'GIN', '324', '11', 'République de Guinée', 'Republic of Guinea', 'Conakry', 'gn', 'GNF', '324', '224', '0', '1', '0', 'Guinée', 'Guinea', '1');
INSERT INTO static_countries VALUES ('83', '0', '0', 'GP', 'GLP', '312', '29', 'Département de la Guadeloupe', 'Department of Guadeloupe', 'Basse Terre', 'gp', 'EUR', '978', '590', '0', '1', '0', 'Guadeloupe', 'Guadeloupe', '0');
INSERT INTO static_countries VALUES ('84', '0', '0', 'GQ', 'GNQ', '226', '17', 'República de Guinea Ecuatorial', 'Republic of Equatorial Guinea', 'Malabo', 'gq', 'XAF', '950', '240', '0', '1', '0', 'Guinea Ecuatorial', 'Equatorial Guinea', '1');
INSERT INTO static_countries VALUES ('85', '0', '0', 'GR', 'GRC', '300', '39', 'Ελληνική Δημοκρατία', 'Hellenic Republic', 'Athens', 'gr', 'EUR', '978', '30', '1', '1', '0', 'Ελλάδα', 'Greece', '1');
INSERT INTO static_countries VALUES ('86', '0', '0', 'GS', 'SGS', '239', '0', 'South Georgia and the South Sandwich Islands', 'South Georgia and the South Sandwich Islands', '', 'gs', '', '0', '0', '0', '0', '0', 'South Georgia and the South Sandwich Islands', 'South Georgia and the South Sandwich Islands', '0');
INSERT INTO static_countries VALUES ('87', '0', '0', 'GT', 'GTM', '320', '13', 'República de Guatemala', 'Republic of Guatemala', 'Guatemala City', 'gt', 'GTQ', '320', '502', '0', '1', '0', 'Guatemala', 'Guatemala', '1');
INSERT INTO static_countries VALUES ('88', '0', '0', 'GU', 'GUM', '316', '57', 'The Territory of Guam / Guåhån', 'The Territory of Guam', 'Hagåtña', 'gu', 'USD', '840', '671', '0', '1', '0', 'Guåhån', 'Guam', '0');
INSERT INTO static_countries VALUES ('89', '0', '0', 'GW', 'GNB', '624', '11', 'República da Guiné-Bissau', 'Republic of Guinea-Bissau', 'Bissau', 'gw', 'XOF', '952', '245', '0', '1', '0', 'Guiné-Bissau', 'Guinea-Bissau', '1');
INSERT INTO static_countries VALUES ('90', '0', '0', 'GY', 'GUY', '328', '5', 'Co-operative Republic of Guyana', 'Co-operative Republic of Guyana', 'Georgetown', 'gy', 'GYD', '328', '592', '0', '1', '0', 'Guyana', 'Guyana', '1');
INSERT INTO static_countries VALUES ('91', '0', '0', 'HK', 'HKG', '344', '30', '香港特別行政區', 'Hong Kong SAR of the People’s Republic of China', '', 'hk', 'HKD', '344', '852', '0', '1', '0', '香港', 'Hong Kong SAR of China', '0');
INSERT INTO static_countries VALUES ('92', '0', '0', 'HN', 'HND', '340', '13', 'República de Honduras', 'Republic of Honduras', 'Tegucigalpa', 'hn', 'HNL', '340', '504', '0', '1', '0', 'Honduras', 'Honduras', '1');
INSERT INTO static_countries VALUES ('93', '0', '0', 'HR', 'HRV', '191', '39', 'Republika Hrvatska', 'Republic of Croatia', 'Zagreb', 'hr', 'HRK', '191', '385', '0', '1', '0', 'Hrvatska', 'Croatia', '1');
INSERT INTO static_countries VALUES ('94', '0', '0', 'HT', 'HTI', '332', '29', 'Repiblik d Ayiti / République d\'Haïti', 'Republic of Haiti', 'Port-au-Prince', 'ht', 'HTG', '332', '509', '0', '1', '0', 'Ayiti', 'Haiti', '1');
INSERT INTO static_countries VALUES ('95', '0', '0', 'HU', 'HUN', '348', '151', 'Magyar Köztársaság', 'Republic of Hungary', 'Budapest', 'hu', 'HUF', '348', '36', '1', '1', '0', 'Magyarország', 'Hungary', '1');
INSERT INTO static_countries VALUES ('96', '0', '0', 'ID', 'IDN', '360', '35', 'Republik Indonesia', 'Republic of Indonesia', 'Jakarta', 'id', 'IDR', '360', '62', '0', '2', '0', 'Indonesia', 'Indonesia', '1');
INSERT INTO static_countries VALUES ('97', '0', '0', 'IE', 'IRL', '372', '154', 'Poblacht na hÉireann / Republic of Ireland', 'Republic of Ireland', 'Dublin', 'ie', 'EUR', '978', '353', '1', '1', '0', 'Éire', 'Ireland', '1');
INSERT INTO static_countries VALUES ('98', '0', '0', 'IL', 'ISR', '376', '145', 'دولة إسرائيل / מדינת ישראלل', 'State of Israel', 'Tel Aviv', 'il', 'ILS', '376', '972', '0', '2', '0', 'ישראל', 'Israel', '1');
INSERT INTO static_countries VALUES ('99', '0', '0', 'IN', 'IND', '356', '34', 'Bharat; Republic of India', 'Republic of India', 'New Delhi', 'in', 'INR', '356', '91', '0', '2', '0', 'India', 'India', '1');
INSERT INTO static_countries VALUES ('100', '0', '0', 'IO', 'IOT', '86', '0', 'British Indian Ocean Territory', 'British Indian Ocean Territory', '', 'io', '', '0', '0', '0', '1', '0', 'British Indian Ocean Territory', 'British Indian Ocean Territory', '0');
INSERT INTO static_countries VALUES ('101', '0', '0', 'IQ', 'IRQ', '368', '145', 'الجمهورية العراقية', 'Republic of Iraq', 'Baghdad', 'iq', 'IQD', '368', '964', '0', '1', '0', 'العراق / عيَراق', 'Iraq', '1');
INSERT INTO static_countries VALUES ('102', '0', '0', 'IR', 'IRN', '364', '34', 'جمهوری اسلامی ايران', 'Islamic Republic of Iran', 'Tehran', 'ir', 'IRR', '364', '98', '0', '1', '0', 'ايران', 'Iran', '1');
INSERT INTO static_countries VALUES ('103', '0', '0', 'IS', 'ISL', '352', '154', 'Lýðveldið Ísland', 'Republic of Iceland', 'Reykjavík', 'is', 'ISK', '352', '354', '0', '1', '0', 'Ísland', 'Iceland', '1');
INSERT INTO static_countries VALUES ('104', '0', '0', 'IT', 'ITA', '380', '39', 'Repubblica Italiana', 'Italian Republic', 'Rome', 'it', 'EUR', '978', '39', '1', '7', '0', 'Italia', 'Italy', '1');
INSERT INTO static_countries VALUES ('105', '0', '0', 'JM', 'JAM', '388', '29', 'Commonwealth of Jamaica', 'Commonwealth of Jamaica', 'Kingston', 'jm', 'JMD', '388', '1876', '0', '2', '0', 'Jamaica', 'Jamaica', '1');
INSERT INTO static_countries VALUES ('106', '0', '0', 'JO', 'JOR', '400', '145', 'المملكة الأردنية الهاشمية', 'Hashemite Kingdom of Jordan', 'Amman', 'jo', 'JOD', '400', '962', '0', '1', '0', 'أردنّ', 'Jordan', '1');
INSERT INTO static_countries VALUES ('107', '0', '0', 'JP', 'JPN', '392', '30', '日本国', 'Japan', 'Tokyo', 'jp', 'JPY', '392', '81', '0', '2', '0', '日本', 'Japan', '1');
INSERT INTO static_countries VALUES ('108', '0', '0', 'KE', 'KEN', '404', '14', 'Jamhuri va Kenya', 'Republic of Kenia', 'Nairobi', 'ke', 'KES', '404', '254', '0', '1', '0', 'Kenya', 'Kenya', '1');
INSERT INTO static_countries VALUES ('109', '0', '0', 'KG', 'KGZ', '417', '143', 'Кыргызстан', 'Kyrgyzstan', 'Bishkek', 'kg', 'KGS', '417', '996', '0', '1', '0', 'Кыргызстан', 'Kyrgyzstan', '1');
INSERT INTO static_countries VALUES ('110', '0', '0', 'KH', 'KHM', '116', '35', 'Preăh Réachéanachâkr Kâmpŭchea', 'Kingdom of Cambodia', 'Phnom Penh', 'kh', 'KHR', '116', '855', '0', '1', '0', 'Kâmpŭchea', 'Cambodia', '1');
INSERT INTO static_countries VALUES ('111', '0', '0', 'KI', 'KIR', '296', '57', 'Republic of Kiribati', 'Republic of Kiribati', 'Bairiki', 'ki', 'AUD', '36', '686', '0', '0', '0', 'Kiribati', 'Kiribati', '1');
INSERT INTO static_countries VALUES ('112', '0', '0', 'KM', 'COM', '174', '14', 'Udzima wa Komori /Union des Comores /اتحاد القمر', 'Union of the Comoros', 'Moroni', 'km', 'KMF', '174', '269', '0', '1', '0', 'اتحاد القمر', 'Comoros', '1');
INSERT INTO static_countries VALUES ('113', '0', '0', 'KN', 'KNA', '659', '29', 'Federation of Saint Kitts and Nevis', 'Federation of Saint Kitts and Nevis', 'Basseterre', 'kn', 'XCD', '951', '1869', '0', '1', '0', 'Saint Kitts and Nevis', 'Saint Kitts and Nevis', '1');
INSERT INTO static_countries VALUES ('114', '0', '0', 'KP', 'PRK', '408', '30', '조선민주주의인민화국', 'Democratic People’s Republic of Korea', 'Pyongyang', 'kp', 'KPW', '408', '850', '0', '0', '0', '북조선', 'North Korea', '1');
INSERT INTO static_countries VALUES ('115', '0', '0', 'KR', 'KOR', '410', '30', '대한민국', 'Republic of Korea', 'Seoul', 'kr', 'KRW', '410', '82', '0', '1', '0', '한국', 'South Korea', '1');
INSERT INTO static_countries VALUES ('116', '0', '0', 'KW', 'KWT', '414', '145', 'دولة الكويت', 'State of Kuweit', 'Kuwait City', 'kw', 'KWD', '414', '965', '0', '1', '0', 'الكويت', 'Kuwait', '1');
INSERT INTO static_countries VALUES ('117', '0', '0', 'KY', 'CYM', '136', '29', 'Cayman Islands', 'Cayman Islands', 'George Town', 'ky', 'KYD', '136', '1345', '0', '1', '0', 'Cayman Islands', 'Cayman Islands', '0');
INSERT INTO static_countries VALUES ('118', '0', '0', 'KZ', 'KAZ', '398', '143', 'Қазақстан Республикасы /Республика Казахстан', 'Republic of Kazakhstan', 'Astana', 'kz', 'KZT', '398', '7', '0', '1', '0', 'Қазақстан /Казахстан', 'Kazakhstan', '1');
INSERT INTO static_countries VALUES ('119', '0', '0', 'LA', 'LAO', '418', '35', 'ສາທາລະນະລັດປະຊາທິປະໄຕປະຊາຊົນລາວ', 'Lao People’s Democratic Republic', 'Vientiane', 'la', 'LAK', '418', '856', '0', '1', '0', 'ເມືອງລາວ', 'Laos', '1');
INSERT INTO static_countries VALUES ('120', '0', '0', 'LB', 'LBN', '422', '145', 'الجمهوريّة اللبنانيّة', 'Republic of Lebanon', 'Beirut', 'lb', 'LBP', '422', '961', '0', '1', '0', 'لبنان', 'Lebanon', '1');
INSERT INTO static_countries VALUES ('121', '0', '0', 'LC', 'LCA', '662', '29', 'Saint Lucia', 'Saint Lucia', 'Castries', 'lc', 'XCD', '951', '1758', '0', '1', '0', 'Saint Lucia', 'Saint Lucia', '1');
INSERT INTO static_countries VALUES ('122', '0', '0', 'LI', 'LIE', '438', '155', 'Fürstentum Liechtenstein', 'Principality of Liechtenstein', 'Vaduz', 'li', 'CHF', '756', '41', '0', '1', '0', 'Liechtenstein', 'Liechtenstein', '1');
INSERT INTO static_countries VALUES ('123', '0', '0', 'LK', 'LKA', '144', '34', 'ශ්‍රී ලංකා / இலங்கை சனநாயக சோஷலிசக் குடியரசு', 'Democratic Socialist Republic of Sri Lanka', 'Colombo', 'lk', 'LKR', '144', '94', '0', '2', '0', 'ශ්‍රී ලංකා / இலங்கை', 'Sri Lanka', '1');
INSERT INTO static_countries VALUES ('124', '0', '0', 'LR', 'LBR', '430', '11', 'Republic of Liberia', 'Republic of Liberia', 'Monrovia', 'lr', 'LRD', '430', '231', '0', '1', '0', 'Liberia', 'Liberia', '1');
INSERT INTO static_countries VALUES ('125', '0', '0', 'LS', 'LSO', '426', '18', 'Muso oa Lesotho / Kingdom of Lesotho', 'Kingdon of Lesotho', 'Maseru', 'ls', 'LSL', '426', '266', '0', '1', '0', 'Lesotho', 'Lesotho', '1');
INSERT INTO static_countries VALUES ('126', '0', '0', 'LT', 'LTU', '440', '154', 'Lietuvos Respublika', 'Republic of Lithuania', 'Vilnius', 'lt', 'LTL', '440', '370', '1', '1', '0', 'Lietuva', 'Lithuania', '1');
INSERT INTO static_countries VALUES ('127', '0', '0', 'LU', 'LUX', '442', '155', 'Grand-Duché de Luxembourg / Großherzogtum Luxemburg / Groussherzogtum Lëtzebuerg', 'Grand Duchy of Luxembourg', 'Luxembourg', 'lu', 'EUR', '978', '352', '1', '1', '0', 'Luxemburg', 'Luxembourg', '1');
INSERT INTO static_countries VALUES ('128', '0', '0', 'LV', 'LVA', '428', '154', 'Latvijas Republika', 'Republic of Latvia', 'Riga', 'lv', 'LVL', '428', '371', '1', '1', '0', 'Latvija', 'Latvia', '1');
INSERT INTO static_countries VALUES ('129', '0', '0', 'LY', 'LBY', '434', '15', 'الجماهيرية العربية الليبية الشعبية الإشتراكية ﺍﻟﻌﻆﻤﻰ', 'Great Socialist People’s Libyan Arab Jamahiriya', 'Tripoli', 'ly', 'LYD', '434', '218', '0', '1', '0', 'الليبية', 'Libya', '1');
INSERT INTO static_countries VALUES ('130', '0', '0', 'MA', 'MAR', '504', '15', 'المملكة المغربية', 'Kingdom of Morocco', 'Rabat', 'ma', 'MAD', '504', '212', '0', '1', '0', 'المغربية', 'Morocco', '1');
INSERT INTO static_countries VALUES ('131', '0', '0', 'MC', 'MCO', '492', '155', 'Principauté de Monaco / Principatu de Munegu', 'Principality of Monaco', 'Monaco', 'mc', 'EUR', '978', '377', '0', '1', '0', 'Monaco', 'Monaco', '1');
INSERT INTO static_countries VALUES ('132', '0', '0', 'MD', 'MDA', '498', '151', 'Republica Moldova', 'Republic of Moldova', 'Chisinau', 'md', 'MDL', '498', '373', '0', '1', '0', 'Moldova', 'Moldova', '1');
INSERT INTO static_countries VALUES ('133', '0', '0', 'MG', 'MDG', '450', '14', 'Repoblikan\'i Madagasikara / République de Madagascar', 'Republic of Madagascar', 'Antananarivo', 'mg', 'MGA', '969', '261', '0', '1', '0', 'Madagascar', 'Madagascar', '1');
INSERT INTO static_countries VALUES ('134', '0', '0', 'MH', 'MHL', '584', '57', 'Aolepān Aorōkin M̧ajeļ / Republic of the Marshall Islands', 'Republic of the Marshall Islands', 'Dalap-Uliga-Darrit (DUD)', 'mh', 'USD', '840', '692', '0', '1', '0', 'Marshall Islands', 'Marshall Islands', '1');
INSERT INTO static_countries VALUES ('135', '0', '0', 'MK', 'MKD', '807', '39', 'Република Македонија', 'Republic of Macedonia', 'Skopje', 'mk', 'MKD', '807', '389', '0', '1', '0', 'Македонија', 'Macedonia', '1');
INSERT INTO static_countries VALUES ('136', '0', '0', 'ML', 'MLI', '466', '11', 'République du Mali', 'Republik Mali', 'Bamako', 'ml', 'XOF', '952', '223', '0', '1', '0', 'Mali', 'Mali', '1');
INSERT INTO static_countries VALUES ('137', '0', '0', 'MM', 'MMR', '104', '35', 'Pyidaungzu Myanma Naingngandaw', 'Union of Myanmar', 'Yangon', 'mm', 'MMK', '104', '95', '0', '1', '0', 'Myanmar', 'Myanmar', '1');
INSERT INTO static_countries VALUES ('138', '0', '0', 'MN', 'MNG', '496', '30', 'Монгол Улс', 'Mongolia', 'Ulan Bator', 'mn', 'MNT', '496', '976', '0', '1', '0', 'Монгол Улс', 'Mongolia', '1');
INSERT INTO static_countries VALUES ('139', '0', '0', 'MO', 'MAC', '446', '30', '中華人民共和國澳門特別行政區 / Região Administrativa Especial de Macau da República Popular da China', 'Macao SAR of the People’s Republic of China', 'Macau', 'mo', 'MOP', '446', '853', '0', '1', '0', '澳門 / Macau', 'Macao SAR of China', '0');
INSERT INTO static_countries VALUES ('140', '0', '0', 'MP', 'MNP', '580', '57', 'Commonwealth of the Northern Mariana Islands', 'Commonwealth of the Northern Mariana Islands', 'Garapan', 'mp', 'USD', '840', '1670', '0', '0', '0', 'Northern Marianas', 'Northern Marianas', '0');
INSERT INTO static_countries VALUES ('141', '0', '0', 'MQ', 'MTQ', '474', '29', 'Département de la Martinique', 'Department of Martinique', 'Fort-de-France', 'mq', 'EUR', '978', '596', '0', '1', '0', 'Martinique', 'Martinique', '0');
INSERT INTO static_countries VALUES ('142', '0', '0', 'MR', 'MRT', '478', '11', 'الجمهورية الإسلامية الموريتانية', 'Islamic Republic of Mauritania', 'Nouakchott', 'mr', 'MRO', '478', '222', '0', '1', '0', 'الموريتانية', 'Mauritania', '1');
INSERT INTO static_countries VALUES ('143', '0', '0', 'MS', 'MSR', '500', '29', 'Montserrat', 'Montserrat', 'Plymouth', 'ms', 'XCD', '951', '1664', '0', '1', '0', 'Montserrat', 'Montserrat', '0');
INSERT INTO static_countries VALUES ('144', '0', '0', 'MT', 'MLT', '470', '39', 'Repubblika ta\' Malta / Republic of Malta', 'Republic of Malta', 'Valletta', 'mt', 'MTL', '470', '356', '1', '1', '0', 'Malta', 'Malta', '1');
INSERT INTO static_countries VALUES ('145', '0', '0', 'MU', 'MUS', '480', '14', 'Republic of Mauritius', 'Republic of Mauritius', 'Port Louis', 'mu', 'MUR', '480', '230', '0', '1', '0', 'Mauritius', 'Mauritius', '1');
INSERT INTO static_countries VALUES ('146', '0', '0', 'MV', 'MDV', '462', '34', 'ދިވެހިރާއްޖޭގެ ޖުމުހޫރިއްޔާ', 'Republic of Maldives', 'Malé', 'mv', 'MVR', '462', '960', '0', '1', '0', 'ޖުމުހޫރިއްޔ', 'Maldives', '1');
INSERT INTO static_countries VALUES ('147', '0', '0', 'MW', 'MWI', '454', '14', 'Republic of Malawi / Dziko la Malaŵi', 'Republic of Malawi', 'Lilongwe', 'mw', 'MWK', '454', '265', '0', '1', '0', 'Malawi', 'Malawi', '1');
INSERT INTO static_countries VALUES ('148', '0', '0', 'MX', 'MEX', '484', '13', 'Estados Unidos Mexicanos', 'United Mexican States', 'Mexico City', 'mx', 'MXN', '484', '52', '0', '6', '0', 'México', 'Mexico', '1');
INSERT INTO static_countries VALUES ('149', '0', '0', 'MY', 'MYS', '458', '35', 'ڤرسكوتوان مليسيا', 'Malaysia', 'Kuala Lumpur', 'my', 'MYR', '458', '60', '0', '1', '0', 'مليسيا', 'Malaysia', '1');
INSERT INTO static_countries VALUES ('150', '0', '0', 'MZ', 'MOZ', '508', '14', 'República de Moçambique', 'Republic of Mozambique', 'Maputo', 'mz', 'MZM', '508', '258', '0', '1', '0', 'Moçambique', 'Mozambique', '1');
INSERT INTO static_countries VALUES ('151', '0', '0', 'NA', 'NAM', '516', '18', 'Republic of Namibia', 'Republic of Namibia', 'Windhoek', 'na', 'NAD', '516', '264', '0', '1', '0', 'Namibia', 'Namibia', '1');
INSERT INTO static_countries VALUES ('152', '0', '0', 'NC', 'NCL', '540', '54', 'Territoire de Nouvelle-Caledonie et Dépendances', 'Territory of New Caledonia', 'Nouméa', 'nc', 'XPF', '953', '687', '0', '1', '0', 'Nouvelle-Calédonie', 'New Caledonia', '0');
INSERT INTO static_countries VALUES ('153', '0', '0', 'NE', 'NER', '562', '11', 'République du Niger', 'Republic of Niger', 'Niamey', 'ne', 'XOF', '952', '227', '0', '1', '0', 'Niger', 'Niger', '1');
INSERT INTO static_countries VALUES ('154', '0', '0', 'NF', 'NFK', '574', '53', 'Territory of Norfolk Island', 'Territory of Norfolk Island', 'Kingston', 'nf', 'AUD', '36', '6723', '0', '1', '0', 'Norfolk Island', 'Norfolk Island', '0');
INSERT INTO static_countries VALUES ('155', '0', '0', 'NG', 'NGA', '566', '11', 'Federal Republic of Nigeria', 'Federal Republic of Nigeria', 'Abuja', 'ng', 'NGN', '566', '234', '0', '1', '0', 'Nigeria', 'Nigeria', '1');
INSERT INTO static_countries VALUES ('156', '0', '0', 'NI', 'NIC', '558', '13', 'República de Nicaragua', 'Republic of Nicaragua', 'Managua', 'ni', 'NIO', '558', '505', '0', '1', '0', 'Nicaragua', 'Nicaragua', '1');
INSERT INTO static_countries VALUES ('157', '0', '0', 'NL', 'NLD', '528', '155', 'Koninkrijk der Nederlanden', 'Kingdom of the Netherlands', 'Amsterdam', 'nl', 'EUR', '978', '31', '1', '1', '0', 'Nederland', 'Netherlands', '1');
INSERT INTO static_countries VALUES ('158', '0', '0', 'NO', 'NOR', '578', '154', 'Kongeriket Norge', 'Kingdom of Norway', 'Oslo', 'no', 'NOK', '578', '47', '0', '1', '0', 'Norge', 'Norway', '1');
INSERT INTO static_countries VALUES ('159', '0', '0', 'NP', 'NPL', '524', '34', 'सङ्घीय लोकतान्त्रिक गणतन्त्र नेपाल', 'Federal Democratic Republic of Nepal', 'Kathmandu', 'np', 'NPR', '524', '977', '0', '1', '0', 'नेपाल', 'Nepal', '1');
INSERT INTO static_countries VALUES ('160', '0', '0', 'NR', 'NRU', '520', '57', 'Ripublik Naoero', 'Republic of Nauru', 'Yaren', 'nr', 'AUD', '36', '674', '0', '1', '0', 'Naoero', 'Nauru', '1');
INSERT INTO static_countries VALUES ('161', '0', '0', 'NU', 'NIU', '570', '61', 'Niue', 'Niue', 'Alofi', 'nu', 'NZD', '554', '683', '0', '1', '0', 'Niue', 'Niue', '0');
INSERT INTO static_countries VALUES ('162', '0', '0', 'NZ', 'NZL', '554', '53', 'New Zealand / Aotearoa', 'New Zealand', 'Wellington', 'nz', 'NZD', '554', '64', '0', '2', '0', 'New Zealand / Aotearoa', 'New Zealand', '1');
INSERT INTO static_countries VALUES ('163', '0', '0', 'OM', 'OMN', '512', '145', 'سلطنة عُمان', 'Sultanate of Oman', 'Muscat', 'om', 'OMR', '512', '968', '0', '1', '0', 'عُمان', 'Oman', '1');
INSERT INTO static_countries VALUES ('164', '0', '0', 'PA', 'PAN', '591', '13', 'República de Panamá', 'Repulic of Panama', 'Panama City', 'pa', 'PAB', '590', '507', '0', '2', '0', 'Panamá', 'Panama', '1');
INSERT INTO static_countries VALUES ('165', '0', '0', 'PE', 'PER', '604', '5', 'República del Perú', 'Republic of Peru', 'Lima', 'pe', 'PEN', '604', '51', '0', '2', '0', 'Perú', 'Peru', '1');
INSERT INTO static_countries VALUES ('166', '0', '0', 'PF', 'PYF', '258', '61', 'Polynésie française', 'French Polynesia', 'Papeete', 'pf', 'XPF', '953', '689', '0', '1', '0', 'Polynésie française', 'French Polynesia', '0');
INSERT INTO static_countries VALUES ('167', '0', '0', 'PG', 'PNG', '598', '54', 'Independent State of Papua New Guinea / Papua Niugini', 'Independent State of Papua New Guinea', 'Port Moresby', 'pg', 'PGK', '598', '675', '0', '1', '0', 'Papua New Guinea  / Papua Niugini', 'Papua New Guinea', '1');
INSERT INTO static_countries VALUES ('168', '0', '0', 'PH', 'PHL', '608', '35', 'Republika ng Pilipinas / Republic of the Philippines', 'Republic of the Philippines', 'Manila', 'ph', 'PHP', '608', '63', '0', '2', '0', 'Philippines', 'Philippines', '1');
INSERT INTO static_countries VALUES ('169', '0', '0', 'PK', 'PAK', '586', '34', 'Islamic Republic of Pakistan / اسلامی جمہوریۂ پاکستان', 'Islamic Republic of Pakistan', 'Islamabad', 'pk', 'PKR', '586', '92', '0', '1', '0', 'پاکستان', 'Pakistan', '1');
INSERT INTO static_countries VALUES ('170', '0', '0', 'PL', 'POL', '616', '151', 'Rzeczpospolita Polska', 'Republic of Poland', 'Warsaw', 'pl', 'PLN', '985', '48', '1', '1', '0', 'Polska', 'Poland', '1');
INSERT INTO static_countries VALUES ('171', '0', '0', 'PM', 'SPM', '666', '21', 'Saint-Pierre-et-Miquelon', 'Saint Pierre and Miquelon', 'Saint-Pierre', 'pm', 'EUR', '978', '508', '0', '1', '0', 'Saint-Pierre-et-Miquelon', 'Saint Pierre and Miquelon', '0');
INSERT INTO static_countries VALUES ('172', '0', '0', 'PN', 'PCN', '612', '61', 'Pitcairn Islands', 'Pitcairn Islands', 'Adamstown', 'pn', 'NZD', '554', '0', '0', '1', '0', 'Pitcairn Islands', 'Pitcairn Islands', '0');
INSERT INTO static_countries VALUES ('173', '0', '0', 'PR', 'PRI', '630', '29', 'Estado Libre Asociado de Puerto Rico / Commonwealth of Puerto Rico', 'Commonwealth of Puerto Rico', 'San Juan', 'pr', 'USD', '840', '1787', '0', '2', '0', 'Puerto Rico', 'Puerto Rico', '0');
INSERT INTO static_countries VALUES ('174', '0', '0', 'PT', 'PRT', '620', '39', 'República Portuguesa', 'Portuguese Republic', 'Lisbon', 'pt', 'EUR', '978', '351', '1', '1', '0', 'Portugal', 'Portugal', '1');
INSERT INTO static_countries VALUES ('175', '0', '0', 'PW', 'PLW', '585', '57', 'Belu\'u era Belau / Republic of Palau', 'Republic of Palau', 'Koror', 'pw', 'USD', '840', '680', '0', '1', '0', 'Belau / Palau', 'Palau', '1');
INSERT INTO static_countries VALUES ('176', '0', '0', 'PY', 'PRY', '600', '5', 'República del Paraguay / Tetä Paraguáype', 'Republic of Paraguay', 'Asunción', 'py', 'PYG', '600', '595', '0', '1', '0', 'Paraguay', 'Paraguay', '1');
INSERT INTO static_countries VALUES ('177', '0', '0', 'QA', 'QAT', '634', '145', 'دولة قطر', 'State of Qatar', 'Doha', 'qa', 'QAR', '634', '974', '0', '1', '0', 'قطر', 'Qatar', '1');
INSERT INTO static_countries VALUES ('178', '0', '0', 'RE', 'REU', '638', '14', 'Département de la Réunion', 'Department of Réunion', 'Saint-Denis', 're', 'EUR', '978', '262', '0', '1', '0', 'Réunion', 'Reunion', '0');
INSERT INTO static_countries VALUES ('179', '0', '0', 'RO', 'ROU', '642', '151', 'România', 'Romania', 'Bucharest', 'ro', 'ROL', '642', '40', '1', '1', '0', 'România', 'Romania', '1');
INSERT INTO static_countries VALUES ('180', '0', '0', 'RU', 'RUS', '643', '151', 'Российская Федерация', 'Russian Federation', 'Moscow', 'ru', 'RUB', '643', '7', '0', '1', '0', 'Росси́я', 'Russia', '1');
INSERT INTO static_countries VALUES ('181', '0', '0', 'RW', 'RWA', '646', '14', 'Repubulika y\'u Rwanda / République Rwandaise', 'Republic of Rwanda', 'Kigali', 'rw', 'RWF', '646', '250', '0', '1', '0', 'Rwanda', 'Rwanda', '1');
INSERT INTO static_countries VALUES ('182', '0', '0', 'SA', 'SAU', '682', '145', 'المملكة العربية السعودية', 'Kingdom of Saudi Arabia', 'Riyadh', 'sa', 'SAR', '682', '966', '0', '2', '0', 'السعودية', 'Saudi Arabia', '1');
INSERT INTO static_countries VALUES ('183', '0', '0', 'SB', 'SLB', '90', '54', 'Solomon Islands', 'Solomon Islands', 'Honiara', 'sb', 'SBD', '90', '677', '0', '1', '0', 'Solomon Islands', 'Solomon Islands', '1');
INSERT INTO static_countries VALUES ('184', '0', '0', 'SC', 'SYC', '690', '14', 'Repiblik Sesel / Republic of Seychelles / République des Seychelles', 'Republic of Seychelles', 'Victoria', 'sc', 'SCR', '690', '248', '0', '1', '0', 'Seychelles', 'Seychelles', '1');
INSERT INTO static_countries VALUES ('185', '0', '0', 'SD', 'SDN', '736', '15', 'جمهورية السودان', 'Republic of the Sudan', 'Khartoum', 'sd', 'SDD', '736', '249', '0', '1', '0', 'السودان', 'Sudan', '1');
INSERT INTO static_countries VALUES ('186', '0', '0', 'SE', 'SWE', '752', '154', 'Konungariket Sverige', 'Kingdom of Sweden', 'Stockholm', 'se', 'SEK', '752', '46', '1', '1', '0', 'Sverige', 'Sweden', '1');
INSERT INTO static_countries VALUES ('187', '0', '0', 'SG', 'SGP', '702', '35', 'Republic of Singapore / 新加坡共和国 / Republik Singapura / சிங்கப்பூர் குடியரசு', 'Republic of Singapore', 'Singapore', 'sg', 'SGD', '702', '65', '0', '2', '0', 'Singapore', 'Singapore', '1');
INSERT INTO static_countries VALUES ('188', '0', '0', 'SH', 'SHN', '654', '11', 'Saint Helena, Ascension and Tristan da Cunha', 'Saint Helena, Ascension and Tristan da Cunha', 'Jamestown', 'sh', 'SHP', '654', '290', '0', '1', '0', 'Saint Helena, Ascension and Tristan da Cunha', 'Saint Helena, Ascension and Tristan da Cunha', '0');
INSERT INTO static_countries VALUES ('189', '0', '0', 'SI', 'SVN', '705', '39', 'Republika Slovenija', 'Republic of Slovenia', 'Ljubljana', 'si', 'SIT', '705', '386', '1', '1', '0', 'Slovenija', 'Slovenia', '1');
INSERT INTO static_countries VALUES ('190', '0', '0', 'SJ', 'SJM', '744', '154', 'Svalbard', 'Svalbard', 'Longyearbyen', 'sj', 'NOK', '578', '47', '0', '1', '0', 'Svalbard', 'Svalbard', '0');
INSERT INTO static_countries VALUES ('191', '0', '0', 'SK', 'SVK', '703', '151', 'Slovenská republika', 'Slovak Republic', 'Bratislava', 'sk', 'SKK', '703', '421', '1', '1', '0', 'Slovensko', 'Slovakia', '1');
INSERT INTO static_countries VALUES ('192', '0', '0', 'SL', 'SLE', '694', '11', 'Republic of Sierra Leone', 'Republic of Sierra Leone', 'Freetown', 'sl', 'SLL', '694', '232', '0', '1', '0', 'Sierra Leone', 'Sierra Leone', '1');
INSERT INTO static_countries VALUES ('193', '0', '0', 'SM', 'SMR', '674', '39', 'Serenissima Repubblica di San Marino', 'Most Serene Republic of San Marino', 'San Marino', 'sm', 'EUR', '978', '378', '0', '1', '0', 'San Marino', 'San Marino', '1');
INSERT INTO static_countries VALUES ('194', '0', '0', 'SN', 'SEN', '686', '11', 'République de Sénégal', 'Republic of Senegal', 'Dakar', 'sn', 'XOF', '952', '221', '0', '1', '0', 'Sénégal', 'Senegal', '1');
INSERT INTO static_countries VALUES ('195', '0', '0', 'SO', 'SOM', '706', '14', 'Soomaaliya', 'Somalia', 'Mogadishu', 'so', 'SOS', '706', '252', '0', '1', '0', 'Soomaaliya', 'Somalia', '1');
INSERT INTO static_countries VALUES ('196', '0', '0', 'SR', 'SUR', '740', '5', 'Republiek Suriname', 'Republic of Surinam', 'Paramaribo', 'sr', 'SRD', '968', '597', '0', '1', '0', 'Suriname', 'Suriname', '1');
INSERT INTO static_countries VALUES ('197', '0', '0', 'ST', 'STP', '678', '17', 'República Democrática de São Tomé e Príncipe', 'Democratic Republic of São Tomé e Príncipe', 'São Tomé', 'st', 'STD', '678', '239', '0', '1', '0', 'São Tomé e Príncipe', 'São Tomé e Príncipe', '1');
INSERT INTO static_countries VALUES ('198', '0', '0', 'SV', 'SLV', '222', '13', 'República de El Salvador', 'Republic of El Salvador', 'San Salvador', 'sv', 'SVC', '222', '503', '0', '1', '0', 'El Salvador', 'El Salvador', '1');
INSERT INTO static_countries VALUES ('199', '0', '0', 'SY', 'SYR', '760', '145', 'الجمهوريّة العربيّة السّوريّة', 'Syrian Arab Republic', 'Damascus', 'sy', 'SYP', '760', '963', '0', '1', '0', 'سوري', 'Syria', '1');
INSERT INTO static_countries VALUES ('200', '0', '0', 'SZ', 'SWZ', '748', '18', 'Umboso weSwatini / Kingdom of Swaziland', 'Kingdom of Swaziland', 'Mbabane', 'sz', 'SZL', '748', '268', '0', '1', '0', 'weSwatini', 'Swaziland', '1');
INSERT INTO static_countries VALUES ('201', '0', '0', 'TC', 'TCA', '796', '29', 'Turks and Caicos Islands', 'Turks and Caicos Islands', 'Cockburn Town', 'tc', 'USD', '840', '1649', '0', '1', '0', 'Turks and Caicos Islands', 'Turks and Caicos Islands', '0');
INSERT INTO static_countries VALUES ('202', '0', '0', 'TD', 'TCD', '148', '17', 'جمهورية تشاد / République du Tchad', 'Republic of Chad', 'N\'Djamena', 'td', 'XAF', '950', '235', '0', '1', '0', 'تشاد / Tchad', 'Chad', '1');
INSERT INTO static_countries VALUES ('203', '0', '0', 'TF', 'ATF', '260', '0', 'Terres australes françaises', 'French Southern Territories', '', 'tf', '', '0', '0', '0', '0', '0', 'Terres australes françaises', 'French Southern Territories', '0');
INSERT INTO static_countries VALUES ('204', '0', '0', 'TG', 'TGO', '768', '11', 'République Togolaise', 'Republic of Togo', 'Lomé', 'tg', 'XOF', '952', '228', '0', '1', '0', 'Togo', 'Togo', '1');
INSERT INTO static_countries VALUES ('205', '0', '0', 'TH', 'THA', '764', '35', 'ราชอาณาจักรไทย', 'Kingdom of Thailand', 'Bangkok', 'th', 'THB', '764', '66', '0', '2', '0', 'ไทย', 'Thailand', '1');
INSERT INTO static_countries VALUES ('206', '0', '0', 'TJ', 'TJK', '762', '143', 'Ҷумҳурии Тоҷикистон', 'Republic of Tajikistan', 'Dushanbe', 'tj', 'TJS', '972', '992', '0', '1', '0', 'Тоҷикистон', 'Tajikistan', '1');
INSERT INTO static_countries VALUES ('207', '0', '0', 'TK', 'TKL', '772', '61', 'Tokelau', 'Tokelau', 'Fakaofo', 'tk', 'NZD', '554', '0', '0', '1', '0', 'Tokelau', 'Tokelau', '0');
INSERT INTO static_countries VALUES ('208', '0', '0', 'TM', 'TKM', '795', '143', 'Türkmenistan Jumhuriyäti', 'Republic of Turkmenistan', 'Ashgabat', 'tm', 'TMM', '795', '993', '0', '1', '0', 'Türkmenistan', 'Turkmenistan', '1');
INSERT INTO static_countries VALUES ('209', '0', '0', 'TN', 'TUN', '788', '15', 'الجمهورية التونسية', 'Republic of Tunisia', 'Tunis', 'tn', 'TND', '788', '216', '0', '1', '0', 'التونسية', 'Tunisia', '1');
INSERT INTO static_countries VALUES ('210', '0', '0', 'TO', 'TON', '776', '61', 'Pule\'anga Fakatu\'i \'o Tonga / Kingdom of Tonga', 'Kingdom of Tonga', 'Nuku\'alofa', 'to', 'TOP', '776', '676', '0', '1', '0', 'Tonga', 'Tonga', '1');
INSERT INTO static_countries VALUES ('211', '0', '0', 'TL', 'TLS', '626', '35', 'Repúblika Demokrátika Timor Lorosa\'e / República Democrática de Timor-Leste', 'Democratic Republic of Timor-Leste', 'Dili', 'tp', 'TPE', '626', '670', '0', '1', '0', 'Timor Lorosa\'e', 'Timor-Leste', '1');
INSERT INTO static_countries VALUES ('212', '0', '0', 'TR', 'TUR', '792', '145', 'Türkiye Cumhuriyeti', 'Republic of Turkey', 'Ankara', 'tr', 'TRY', '949', '90', '0', '1', '0', 'Türkiye', 'Turkey', '1');
INSERT INTO static_countries VALUES ('213', '0', '0', 'TT', 'TTO', '780', '29', 'Republic of Trinidad and Tobago', 'Republic of Trinidad and Tobago', 'Port of Spain', 'tt', 'TTD', '780', '1868', '0', '1', '0', 'Trinidad and Tobago', 'Trinidad and Tobago', '1');
INSERT INTO static_countries VALUES ('214', '0', '0', 'TV', 'TUV', '798', '61', 'Tuvalu', 'Tuvalu', 'Fongafale', 'tv', 'AUD', '36', '688', '0', '1', '0', 'Tuvalu', 'Tuvalu', '1');
INSERT INTO static_countries VALUES ('215', '0', '0', 'TW', 'TWN', '158', '30', '中華民國', 'Republic of China', 'Taipei', 'tw', 'TWD', '901', '886', '0', '1', '0', '中華', 'Taiwan', '0');
INSERT INTO static_countries VALUES ('216', '0', '0', 'TZ', 'TZA', '834', '14', 'Jamhuri ya Muungano wa Tanzania', 'United Republic of Tanzania', 'Dodoma', 'tz', 'TZS', '834', '255', '0', '1', '0', 'Tanzania', 'Tanzania', '1');
INSERT INTO static_countries VALUES ('217', '0', '0', 'UA', 'UKR', '804', '151', 'Україна', 'Ukraine', 'Kiev', 'ua', 'UAH', '980', '380', '0', '1', '0', 'Україна', 'Ukraine', '1');
INSERT INTO static_countries VALUES ('218', '0', '0', 'UG', 'UGA', '800', '14', 'Republic of Uganda', 'Republic of Uganda', 'Kampala', 'ug', 'UGX', '800', '256', '0', '1', '0', 'Uganda', 'Uganda', '1');
INSERT INTO static_countries VALUES ('219', '0', '0', 'UM', 'UMI', '581', '0', 'United States Minor Outlying Islands', 'United States Minor Outlying Islands', '', 'um', 'USD', '840', '0', '0', '0', '0', 'United States Minor Outlying Islands', 'United States Minor Outlying Islands', '0');
INSERT INTO static_countries VALUES ('220', '0', '0', 'US', 'USA', '840', '21', 'United States of America', 'United States of America', 'Washington DC', 'us', 'USD', '840', '1', '0', '3', '1', 'United States', 'United States', '1');
INSERT INTO static_countries VALUES ('221', '0', '0', 'UY', 'URY', '858', '5', 'República Oriental del Uruguay', 'Eastern Republic of Uruguay', 'Montevideo', 'uy', 'UYU', '858', '598', '0', '1', '0', 'Uruguay', 'Uruguay', '1');
INSERT INTO static_countries VALUES ('222', '0', '0', 'UZ', 'UZB', '860', '143', 'O‘zbekiston Respublikasi', 'Republic of Uzbekistan', 'Tashkent', 'uz', 'UZS', '860', '998', '0', '1', '0', 'O‘zbekiston', 'Uzbekistan', '1');
INSERT INTO static_countries VALUES ('223', '0', '0', 'VA', 'VAT', '336', '39', 'Status Civitatis Vaticanae / Città del Vaticano', 'Vatican City', 'Vatican City', 'va', 'EUR', '978', '396', '0', '1', '0', 'Vaticano', 'Vatican City', '0');
INSERT INTO static_countries VALUES ('224', '0', '0', 'VC', 'VCT', '670', '29', 'Saint Vincent and the Grenadines', 'Saint Vincent and the Grenadines', 'Kingstown', 'vc', 'XCD', '951', '1784', '0', '1', '0', 'Saint Vincent and the Grenadines', 'Saint Vincent and the Grenadines', '1');
INSERT INTO static_countries VALUES ('225', '0', '0', 'VE', 'VEN', '862', '5', 'República Bolivariana de Venezuela', 'Bolivarian Republic of Venezuela', 'Caracas', 've', 'VEB', '862', '58', '0', '1', '0', 'Venezuela', 'Venezuela', '1');
INSERT INTO static_countries VALUES ('226', '0', '0', 'VG', 'VGB', '92', '29', 'British Virgin Islands', 'British Virgin Islands', 'Road Town', 'vg', 'USD', '840', '1284', '0', '1', '0', 'British Virgin Islands', 'British Virgin Islands', '0');
INSERT INTO static_countries VALUES ('227', '0', '0', 'VI', 'VIR', '850', '29', 'United States Virgin Islands', 'United States Virgin Islands', 'Charlotte Amalie', 'vi', 'USD', '840', '1340', '0', '1', '0', 'US Virgin Islands', 'US Virgin Islands', '0');
INSERT INTO static_countries VALUES ('228', '0', '0', 'VN', 'VNM', '704', '35', 'Cộng Hòa Xã Hội Chủ Nghĩa Việt Nam', 'Socialist Republic of Vietnam', 'Hanoi', 'vn', 'VND', '704', '84', '0', '1', '0', 'Việt Nam', 'Vietnam', '1');
INSERT INTO static_countries VALUES ('229', '0', '0', 'VU', 'VUT', '548', '54', 'Ripablik blong Vanuatu / Republic of Vanuatu / République du Vanuatu', 'Republic of Vanuatu', 'Port Vila', 'vu', 'VUV', '548', '678', '0', '1', '0', 'Vanuatu', 'Vanuatu', '1');
INSERT INTO static_countries VALUES ('230', '0', '0', 'WF', 'WLF', '876', '61', 'Territoire de Wallis et Futuna', 'Territory of Wallis and Futuna Islands', 'Mata-Utu', 'wf', 'XPF', '953', '681', '0', '1', '0', 'Wallis and Futuna', 'Wallis and Futuna', '0');
INSERT INTO static_countries VALUES ('231', '0', '0', 'WS', 'WSM', '882', '61', 'Malo Sa\'oloto Tuto\'atasi o Samoa / Independent State of Samoa', 'Independent State of Samoa', 'Apia', 'ws', 'WST', '882', '685', '0', '1', '0', 'Samoa', 'Samoa', '1');
INSERT INTO static_countries VALUES ('232', '0', '0', 'YE', 'YEM', '887', '145', 'الجمهوريّة اليمنية', 'Republic of Yemen', 'San\'a', 'ye', 'YER', '886', '967', '0', '1', '0', 'اليمنية', 'Yemen', '1');
INSERT INTO static_countries VALUES ('233', '0', '0', 'YT', 'MYT', '175', '14', 'Mayotte', 'Mayotte', 'Mamoudzou', 'yt', 'EUR', '978', '269', '0', '0', '0', 'Mayotte', 'Mayotte', '0');
INSERT INTO static_countries VALUES ('235', '0', '0', 'ZA', 'ZAF', '710', '18', 'Republic of South Africa / Republiek van Suid-Afrika / Rephaboliki ya Afrika-Borwa', 'Republic of South Africa', 'Pretoria', 'za', 'ZAR', '710', '27', '0', '2', '0', 'Afrika-Borwa', 'South Africa', '1');
INSERT INTO static_countries VALUES ('236', '0', '0', 'ZM', 'ZMB', '894', '14', 'Republic of Zambia', 'Republic of Zambia', 'Lusaka', 'zm', 'ZMK', '894', '260', '0', '1', '0', 'Zambia', 'Zambia', '1');
INSERT INTO static_countries VALUES ('237', '0', '0', 'ZW', 'ZWE', '716', '14', 'Republic of Zimbabwe', 'Republic of Zimbabwe', 'Harare', 'zw', 'ZWD', '716', '263', '0', '1', '0', 'Zimbabwe', 'Zimbabwe', '1');
INSERT INTO static_countries VALUES ('238', '0', '0', 'PS', 'PSE', '275', '145', 'دولة فلسطين', 'Palestinian territories', '', 'ps', '0', '0', '0', '0', '0', '0', 'فلسطين', 'Palestine', '0');
INSERT INTO static_countries VALUES ('239', '0', '1', 'CS', 'CSG', '891', '39', 'Државна заједница Србија и Црна Гора', 'State Union of Serbia and Montenegro', 'Belgrade', 'cs', 'CSD', '891', '381', '0', '0', '0', 'Србија и Црна Гора', 'Serbia and Montenegro', '1');
INSERT INTO static_countries VALUES ('240', '0', '0', 'AX', 'ALA', '248', '154', 'Landskapet Åland', 'Åland Islands', 'Mariehamn', 'fi', 'EUR', '978', '35818', '1', '0', '0', 'Landskapet Åland', 'Åland Islands', '0');
INSERT INTO static_countries VALUES ('241', '0', '0', 'HM', 'HMD', '334', '53', 'Heard Island and McDonald Islands', 'Heard Island and McDonald Islands', '', '', 'AUD', '36', '0', '0', '0', '0', 'Heard Island and McDonald Islands', 'Heard Island and McDonald Islands', '0');
INSERT INTO static_countries VALUES ('242', '0', '0', 'ME', 'MNE', '499', '39', 'Republike Crne Gore', 'Montenegro', 'Podgorica', 'me', 'EUR', '978', '382', '0', '1', '0', 'Crna Gora', 'Montenegro', '1');
INSERT INTO static_countries VALUES ('243', '0', '0', 'RS', 'SRB', '688', '39', 'Republika Srbija', 'Republic of Serbia', 'Belgrade', 'rs', 'RSD', '941', '381', '0', '1', '0', 'Srbija', 'Serbia', '1');
INSERT INTO static_countries VALUES ('244', '0', '0', 'JE', 'JEY', '832', '154', 'Bailiwick of Jersey', 'Bailiwick of Jersey', 'Saint Helier', 'je', 'GBP', '826', '44', '0', '5', '0', 'Jersey', 'Jersey', '0');
INSERT INTO static_countries VALUES ('245', '0', '0', 'GG', 'GGY', '831', '154', 'Bailiwick of Guernsey', 'Bailiwick of Guernsey', 'Saint Peter Port', 'gg', 'GBP', '826', '44', '0', '5', '0', 'Guernsey', 'Guernsey', '0');
INSERT INTO static_countries VALUES ('246', '0', '0', 'IM', 'IMN', '833', '154', 'Isle of Man / Ellan Vannin', 'Isle of Man', 'Douglas', 'im', 'GBP', '826', '44', '0', '5', '0', 'Mann / Mannin', 'Isle of Man', '0');
INSERT INTO static_countries VALUES ('247', '0', '0', 'MF', 'MAF', '652', '29', 'Collectivité de Saint-Martin', 'Collectivity of Saint Martin', 'Marigot', 'fr', 'EUR', '978', '590', '0', '1', '0', 'Saint-Martin', 'Saint Martin', '0');
INSERT INTO static_countries VALUES ('248', '0', '0', 'BL', 'BLM', '652', '29', 'Collectivité de Saint-Barthélemy', 'Collectivity of Saint Barthélemy', 'Gustavia', 'fr', 'EUR', '978', '590', '0', '1', '0', 'Saint-Barthélemy', 'Saint Barthélemy', '0');
INSERT INTO static_countries VALUES ('249', '0', '0', 'BQ', 'BES', '535', '29', 'Bonaire, Sint Eustatius en Saba', 'Bonaire, Saint Eustatius and Saba', '', 'bq', '0', '0', '599', '0', '0', '0', 'Bonaire, Sint Eustatius en Saba', 'Bonaire, Saint Eustatius and Saba', '0');
INSERT INTO static_countries VALUES ('250', '0', '0', 'CW', 'CUW', '531', '29', 'Curaçao', 'Curaçao', 'Willemstad', 'cw', '0', '0', '599', '0', '0', '0', 'Curaçao', 'Curaçao', '0');
INSERT INTO static_countries VALUES ('251', '0', '0', 'SX', 'SXM', '534', '29', 'Sint Maarten', 'Sint Maarten', 'Philipsburg', 'sx', '0', '0', '599', '0', '0', '0', 'Sint Maarten', 'Sint Maarten', '0');



# TYPO3 Extension Manager dump 1.1
#
#--------------------------------------------------------


#
# Table structure for table "static_country_zones"
#
DROP TABLE IF EXISTS static_country_zones;
CREATE TABLE static_country_zones (
  uid int(11) unsigned NOT NULL auto_increment,
  pid int(11) unsigned default '0',
  zn_country_iso_2 char(2) default '',
  zn_country_iso_3 char(3) default '',
  zn_country_iso_nr int(11) unsigned default '0',
  zn_code varchar(45) default '',
  zn_name_local varchar(128) default '',
  zn_name_en varchar(50) default '',
  PRIMARY KEY (uid),
  UNIQUE uid (uid)
);


INSERT INTO static_country_zones VALUES ('1', '0', 'US', 'USA', '840', 'AL', 'Alabama', '');
INSERT INTO static_country_zones VALUES ('2', '0', 'US', 'USA', '840', 'AK', 'Alaska', '');
INSERT INTO static_country_zones VALUES ('4', '0', 'US', 'USA', '840', 'AZ', 'Arizona', '');
INSERT INTO static_country_zones VALUES ('5', '0', 'US', 'USA', '840', 'AR', 'Arkansas', '');
INSERT INTO static_country_zones VALUES ('12', '0', 'US', 'USA', '840', 'CA', 'California', '');
INSERT INTO static_country_zones VALUES ('13', '0', 'US', 'USA', '840', 'CO', 'Colorado', '');
INSERT INTO static_country_zones VALUES ('14', '0', 'US', 'USA', '840', 'CT', 'Connecticut', '');
INSERT INTO static_country_zones VALUES ('15', '0', 'US', 'USA', '840', 'DE', 'Delaware', '');
INSERT INTO static_country_zones VALUES ('16', '0', 'US', 'USA', '840', 'DC', 'District of Columbia', '');
INSERT INTO static_country_zones VALUES ('18', '0', 'US', 'USA', '840', 'FL', 'Florida', '');
INSERT INTO static_country_zones VALUES ('19', '0', 'US', 'USA', '840', 'GA', 'Georgia', '');
INSERT INTO static_country_zones VALUES ('20', '0', 'US', 'USA', '840', 'GU', 'Guam', '');
INSERT INTO static_country_zones VALUES ('21', '0', 'US', 'USA', '840', 'HI', 'Hawaii', '');
INSERT INTO static_country_zones VALUES ('22', '0', 'US', 'USA', '840', 'ID', 'Idaho', '');
INSERT INTO static_country_zones VALUES ('23', '0', 'US', 'USA', '840', 'IL', 'Illinois', '');
INSERT INTO static_country_zones VALUES ('24', '0', 'US', 'USA', '840', 'IN', 'Indiana', '');
INSERT INTO static_country_zones VALUES ('25', '0', 'US', 'USA', '840', 'IA', 'Iowa', '');
INSERT INTO static_country_zones VALUES ('26', '0', 'US', 'USA', '840', 'KS', 'Kansas', '');
INSERT INTO static_country_zones VALUES ('27', '0', 'US', 'USA', '840', 'KY', 'Kentucky', '');
INSERT INTO static_country_zones VALUES ('28', '0', 'US', 'USA', '840', 'LA', 'Louisiana', '');
INSERT INTO static_country_zones VALUES ('29', '0', 'US', 'USA', '840', 'ME', 'Maine', '');
INSERT INTO static_country_zones VALUES ('31', '0', 'US', 'USA', '840', 'MD', 'Maryland', '');
INSERT INTO static_country_zones VALUES ('32', '0', 'US', 'USA', '840', 'MA', 'Massachusetts', '');
INSERT INTO static_country_zones VALUES ('33', '0', 'US', 'USA', '840', 'MI', 'Michigan', '');
INSERT INTO static_country_zones VALUES ('34', '0', 'US', 'USA', '840', 'MN', 'Minnesota', '');
INSERT INTO static_country_zones VALUES ('35', '0', 'US', 'USA', '840', 'MS', 'Mississippi', '');
INSERT INTO static_country_zones VALUES ('36', '0', 'US', 'USA', '840', 'MO', 'Missouri', '');
INSERT INTO static_country_zones VALUES ('37', '0', 'US', 'USA', '840', 'MT', 'Montana', '');
INSERT INTO static_country_zones VALUES ('38', '0', 'US', 'USA', '840', 'NE', 'Nebraska', '');
INSERT INTO static_country_zones VALUES ('39', '0', 'US', 'USA', '840', 'NV', 'Nevada', '');
INSERT INTO static_country_zones VALUES ('40', '0', 'US', 'USA', '840', 'NH', 'New Hampshire', '');
INSERT INTO static_country_zones VALUES ('41', '0', 'US', 'USA', '840', 'NJ', 'New Jersey', '');
INSERT INTO static_country_zones VALUES ('42', '0', 'US', 'USA', '840', 'NM', 'New Mexico', '');
INSERT INTO static_country_zones VALUES ('43', '0', 'US', 'USA', '840', 'NY', 'New York', '');
INSERT INTO static_country_zones VALUES ('44', '0', 'US', 'USA', '840', 'NC', 'North Carolina', '');
INSERT INTO static_country_zones VALUES ('45', '0', 'US', 'USA', '840', 'ND', 'North Dakota', '');
INSERT INTO static_country_zones VALUES ('47', '0', 'US', 'USA', '840', 'OH', 'Ohio', '');
INSERT INTO static_country_zones VALUES ('48', '0', 'US', 'USA', '840', 'OK', 'Oklahoma', '');
INSERT INTO static_country_zones VALUES ('49', '0', 'US', 'USA', '840', 'OR', 'Oregon', '');
INSERT INTO static_country_zones VALUES ('51', '0', 'US', 'USA', '840', 'PA', 'Pennsylvania', '');
INSERT INTO static_country_zones VALUES ('52', '0', 'US', 'USA', '840', 'PR', 'Puerto Rico', '');
INSERT INTO static_country_zones VALUES ('53', '0', 'US', 'USA', '840', 'RI', 'Rhode Island', '');
INSERT INTO static_country_zones VALUES ('54', '0', 'US', 'USA', '840', 'SC', 'South Carolina', '');
INSERT INTO static_country_zones VALUES ('55', '0', 'US', 'USA', '840', 'SD', 'South Dakota', '');
INSERT INTO static_country_zones VALUES ('56', '0', 'US', 'USA', '840', 'TN', 'Tennessee', '');
INSERT INTO static_country_zones VALUES ('57', '0', 'US', 'USA', '840', 'TX', 'Texas', '');
INSERT INTO static_country_zones VALUES ('58', '0', 'US', 'USA', '840', 'UT', 'Utah', '');
INSERT INTO static_country_zones VALUES ('59', '0', 'US', 'USA', '840', 'VT', 'Vermont', '');
INSERT INTO static_country_zones VALUES ('61', '0', 'US', 'USA', '840', 'VA', 'Virginia', '');
INSERT INTO static_country_zones VALUES ('62', '0', 'US', 'USA', '840', 'WA', 'Washington', '');
INSERT INTO static_country_zones VALUES ('63', '0', 'US', 'USA', '840', 'WV', 'West Virginia', '');
INSERT INTO static_country_zones VALUES ('64', '0', 'US', 'USA', '840', 'WI', 'Wisconsin', '');
INSERT INTO static_country_zones VALUES ('65', '0', 'US', 'USA', '840', 'WY', 'Wyoming', '');
INSERT INTO static_country_zones VALUES ('66', '0', 'CA', 'CAN', '124', 'AB', 'Alberta', '');
INSERT INTO static_country_zones VALUES ('67', '0', 'CA', 'CAN', '124', 'BC', 'British Columbia', '');
INSERT INTO static_country_zones VALUES ('68', '0', 'CA', 'CAN', '124', 'MB', 'Manitoba', '');
INSERT INTO static_country_zones VALUES ('69', '0', 'CA', 'CAN', '124', 'NF', 'Newfoundland and Labrabor', '');
INSERT INTO static_country_zones VALUES ('70', '0', 'CA', 'CAN', '124', 'NB', 'New Brunswick', '');
INSERT INTO static_country_zones VALUES ('71', '0', 'CA', 'CAN', '124', 'NS', 'Nova Scotia', '');
INSERT INTO static_country_zones VALUES ('72', '0', 'CA', 'CAN', '124', 'NT', 'Northwest Territories', '');
INSERT INTO static_country_zones VALUES ('73', '0', 'CA', 'CAN', '124', 'NU', 'Nunavut', '');
INSERT INTO static_country_zones VALUES ('74', '0', 'CA', 'CAN', '124', 'ON', 'Ontario', '');
INSERT INTO static_country_zones VALUES ('75', '0', 'CA', 'CAN', '124', 'PE', 'Prince Edward Island', '');
INSERT INTO static_country_zones VALUES ('76', '0', 'CA', 'CAN', '124', 'QC', 'Québec', 'Quebec');
INSERT INTO static_country_zones VALUES ('77', '0', 'CA', 'CAN', '124', 'SK', 'Saskatchewan', '');
INSERT INTO static_country_zones VALUES ('78', '0', 'CA', 'CAN', '124', 'YT', 'Yukon Territory', '');
INSERT INTO static_country_zones VALUES ('79', '0', 'DE', 'DEU', '276', 'NI', 'Niedersachsen', 'Lower Saxony');
INSERT INTO static_country_zones VALUES ('80', '0', 'DE', 'DEU', '276', 'BW', 'Baden-Württemberg', '');
INSERT INTO static_country_zones VALUES ('81', '0', 'DE', 'DEU', '276', 'BY', 'Bayern', 'Bavaria');
INSERT INTO static_country_zones VALUES ('82', '0', 'DE', 'DEU', '276', 'BE', 'Berlin', '');
INSERT INTO static_country_zones VALUES ('83', '0', 'DE', 'DEU', '276', 'BB', 'Brandenburg', '');
INSERT INTO static_country_zones VALUES ('84', '0', 'DE', 'DEU', '276', 'HB', 'Bremen', '');
INSERT INTO static_country_zones VALUES ('85', '0', 'DE', 'DEU', '276', 'HH', 'Hamburg', '');
INSERT INTO static_country_zones VALUES ('86', '0', 'DE', 'DEU', '276', 'HE', 'Hessen', 'Hesse');
INSERT INTO static_country_zones VALUES ('87', '0', 'DE', 'DEU', '276', 'MV', 'Mecklenburg-Vorpommern', 'Mecklenburg-Western Pomerania');
INSERT INTO static_country_zones VALUES ('88', '0', 'DE', 'DEU', '276', 'NW', 'Nordrhein-Westfalen', 'North Rhine-Westphalia');
INSERT INTO static_country_zones VALUES ('89', '0', 'DE', 'DEU', '276', 'RP', 'Rheinland-Pfalz', 'Rhineland-Palatinate');
INSERT INTO static_country_zones VALUES ('90', '0', 'DE', 'DEU', '276', 'SL', 'Saarland', '');
INSERT INTO static_country_zones VALUES ('91', '0', 'DE', 'DEU', '276', 'SN', 'Sachsen', 'Saxony');
INSERT INTO static_country_zones VALUES ('92', '0', 'DE', 'DEU', '276', 'ST', 'Sachsen-Anhalt', 'Saxony-Anhalt');
INSERT INTO static_country_zones VALUES ('93', '0', 'DE', 'DEU', '276', 'SH', 'Schleswig-Holstein', '');
INSERT INTO static_country_zones VALUES ('94', '0', 'DE', 'DEU', '276', 'TH', 'Thüringen', 'Thuringia');
INSERT INTO static_country_zones VALUES ('95', '0', 'AT', 'AUT', '40', '9', 'Wien', 'Vienna');
INSERT INTO static_country_zones VALUES ('96', '0', 'AT', 'AUT', '40', '3', 'Niederösterreich', 'Lower Austria');
INSERT INTO static_country_zones VALUES ('97', '0', 'AT', 'AUT', '40', '4', 'Oberösterreich', 'Upper Austria');
INSERT INTO static_country_zones VALUES ('98', '0', 'AT', 'AUT', '40', '5', 'Salzburg', '');
INSERT INTO static_country_zones VALUES ('99', '0', 'AT', 'AUT', '40', '2', 'Kärnten', 'Carinthia');
INSERT INTO static_country_zones VALUES ('100', '0', 'AT', 'AUT', '40', '6', 'Steiermark', 'Styria');
INSERT INTO static_country_zones VALUES ('101', '0', 'AT', 'AUT', '40', '7', 'Tirol', 'Tyrol');
INSERT INTO static_country_zones VALUES ('102', '0', 'AT', 'AUT', '40', '1', 'Burgenland', '');
INSERT INTO static_country_zones VALUES ('103', '0', 'AT', 'AUT', '40', '8', 'Vorarlberg', '');
INSERT INTO static_country_zones VALUES ('104', '0', 'CH', 'CHE', '756', 'AG', 'Aargau', '');
INSERT INTO static_country_zones VALUES ('105', '0', 'CH', 'CHE', '756', 'AI', 'Appenzell Innerrhoden', '');
INSERT INTO static_country_zones VALUES ('106', '0', 'CH', 'CHE', '756', 'AR', 'Appenzell Ausserrhoden', '');
INSERT INTO static_country_zones VALUES ('107', '0', 'CH', 'CHE', '756', 'BE', 'Bern', '');
INSERT INTO static_country_zones VALUES ('108', '0', 'CH', 'CHE', '756', 'BL', 'Basel-Landschaft', '');
INSERT INTO static_country_zones VALUES ('109', '0', 'CH', 'CHE', '756', 'BS', 'Basel-Stadt', '');
INSERT INTO static_country_zones VALUES ('110', '0', 'CH', 'CHE', '756', 'FR', 'Freiburg', '');
INSERT INTO static_country_zones VALUES ('111', '0', 'CH', 'CHE', '756', 'GE', 'Genf', 'Geneve');
INSERT INTO static_country_zones VALUES ('112', '0', 'CH', 'CHE', '756', 'GL', 'Glarus', '');
INSERT INTO static_country_zones VALUES ('113', '0', 'CH', 'CHE', '756', 'GR', 'Graubünden', '');
INSERT INTO static_country_zones VALUES ('114', '0', 'CH', 'CHE', '756', 'JU', 'Jura', '');
INSERT INTO static_country_zones VALUES ('115', '0', 'CH', 'CHE', '756', 'LU', 'Luzern', '');
INSERT INTO static_country_zones VALUES ('116', '0', 'CH', 'CHE', '756', 'NE', 'Neuenburg', '');
INSERT INTO static_country_zones VALUES ('117', '0', 'CH', 'CHE', '756', 'NW', 'Nidwalden', '');
INSERT INTO static_country_zones VALUES ('118', '0', 'CH', 'CHE', '756', 'OW', 'Obwalden', '');
INSERT INTO static_country_zones VALUES ('119', '0', 'CH', 'CHE', '756', 'SG', 'St. Gallen', '');
INSERT INTO static_country_zones VALUES ('120', '0', 'CH', 'CHE', '756', 'SH', 'Schaffhausen', '');
INSERT INTO static_country_zones VALUES ('121', '0', 'CH', 'CHE', '756', 'SO', 'Solothurn', '');
INSERT INTO static_country_zones VALUES ('122', '0', 'CH', 'CHE', '756', 'SZ', 'Schwyz', '');
INSERT INTO static_country_zones VALUES ('123', '0', 'CH', 'CHE', '756', 'TG', 'Thurgau', '');
INSERT INTO static_country_zones VALUES ('124', '0', 'CH', 'CHE', '756', 'TI', 'Tessin', '');
INSERT INTO static_country_zones VALUES ('125', '0', 'CH', 'CHE', '756', 'UR', 'Uri', '');
INSERT INTO static_country_zones VALUES ('126', '0', 'CH', 'CHE', '756', 'VD', 'Waadt', '');
INSERT INTO static_country_zones VALUES ('127', '0', 'CH', 'CHE', '756', 'VS', 'Wallis', '');
INSERT INTO static_country_zones VALUES ('128', '0', 'CH', 'CHE', '756', 'ZG', 'Zug', '');
INSERT INTO static_country_zones VALUES ('129', '0', 'CH', 'CHE', '756', 'ZH', 'Zürich', '');
INSERT INTO static_country_zones VALUES ('130', '0', 'ES', 'ESP', '724', 'Alava', 'Alava', '');
INSERT INTO static_country_zones VALUES ('131', '0', 'ES', 'ESP', '724', 'Malaga', 'Malaga', '');
INSERT INTO static_country_zones VALUES ('132', '0', 'ES', 'ESP', '724', 'Segovia', 'Segovia', '');
INSERT INTO static_country_zones VALUES ('133', '0', 'ES', 'ESP', '724', 'Granada', 'Granada', '');
INSERT INTO static_country_zones VALUES ('134', '0', 'ES', 'ESP', '724', 'Jaen', 'Jaen', '');
INSERT INTO static_country_zones VALUES ('135', '0', 'ES', 'ESP', '724', 'Sevilla', 'Sevilla', '');
INSERT INTO static_country_zones VALUES ('136', '0', 'ES', 'ESP', '724', 'Barcelona', 'Barcelona', '');
INSERT INTO static_country_zones VALUES ('137', '0', 'ES', 'ESP', '724', 'Valencia', 'Valencia', '');
INSERT INTO static_country_zones VALUES ('138', '0', 'ES', 'ESP', '724', 'Albacete', 'Albacete', '');
INSERT INTO static_country_zones VALUES ('139', '0', 'ES', 'ESP', '724', 'Alicante', 'Alicante', '');
INSERT INTO static_country_zones VALUES ('140', '0', 'ES', 'ESP', '724', 'Almeria', 'Almeria', '');
INSERT INTO static_country_zones VALUES ('141', '0', 'ES', 'ESP', '724', 'Asturias', 'Asturias', '');
INSERT INTO static_country_zones VALUES ('142', '0', 'ES', 'ESP', '724', 'Avila', 'Avila', '');
INSERT INTO static_country_zones VALUES ('143', '0', 'ES', 'ESP', '724', 'Badajoz', 'Badajoz', '');
INSERT INTO static_country_zones VALUES ('144', '0', 'ES', 'ESP', '724', 'Burgos', 'Burgos', '');
INSERT INTO static_country_zones VALUES ('145', '0', 'ES', 'ESP', '724', 'Caceres', 'Caceres', '');
INSERT INTO static_country_zones VALUES ('146', '0', 'ES', 'ESP', '724', 'Cadiz', 'Cadiz', '');
INSERT INTO static_country_zones VALUES ('147', '0', 'ES', 'ESP', '724', 'Cantabria', 'Cantabria', '');
INSERT INTO static_country_zones VALUES ('148', '0', 'ES', 'ESP', '724', 'Castellon', 'Castellon', '');
INSERT INTO static_country_zones VALUES ('149', '0', 'ES', 'ESP', '724', 'Ceuta', 'Ceuta', '');
INSERT INTO static_country_zones VALUES ('150', '0', 'ES', 'ESP', '724', 'Ciudad Real', 'Ciudad Real', '');
INSERT INTO static_country_zones VALUES ('151', '0', 'ES', 'ESP', '724', 'Cordoba', 'Cordoba', '');
INSERT INTO static_country_zones VALUES ('152', '0', 'ES', 'ESP', '724', 'Cuenca', 'Cuenca', '');
INSERT INTO static_country_zones VALUES ('153', '0', 'ES', 'ESP', '724', 'Girona', 'Girona', '');
INSERT INTO static_country_zones VALUES ('154', '0', 'ES', 'ESP', '724', 'Las Palmas', 'Las Palmas', '');
INSERT INTO static_country_zones VALUES ('155', '0', 'ES', 'ESP', '724', 'Guadalajara', 'Guadalajara', '');
INSERT INTO static_country_zones VALUES ('156', '0', 'ES', 'ESP', '724', 'Guipuzcoa', 'Guipuzcoa', '');
INSERT INTO static_country_zones VALUES ('157', '0', 'ES', 'ESP', '724', 'Huelva', 'Huelva', '');
INSERT INTO static_country_zones VALUES ('158', '0', 'ES', 'ESP', '724', 'Huesca', 'Huesca', '');
INSERT INTO static_country_zones VALUES ('159', '0', 'ES', 'ESP', '724', 'A Coruña', 'A Coruña', '');
INSERT INTO static_country_zones VALUES ('160', '0', 'ES', 'ESP', '724', 'La Rioja', 'La Rioja', '');
INSERT INTO static_country_zones VALUES ('161', '0', 'ES', 'ESP', '724', 'Leon', 'Leon', '');
INSERT INTO static_country_zones VALUES ('162', '0', 'ES', 'ESP', '724', 'Lugo', 'Lugo', '');
INSERT INTO static_country_zones VALUES ('163', '0', 'ES', 'ESP', '724', 'Lleida', 'Lleida', '');
INSERT INTO static_country_zones VALUES ('164', '0', 'ES', 'ESP', '724', 'Madrid', 'Madrid', '');
INSERT INTO static_country_zones VALUES ('165', '0', 'ES', 'ESP', '724', 'Baleares', 'Baleares', '');
INSERT INTO static_country_zones VALUES ('166', '0', 'ES', 'ESP', '724', 'Murcia', 'Murcia', '');
INSERT INTO static_country_zones VALUES ('167', '0', 'ES', 'ESP', '724', 'Navarra', 'Navarra', '');
INSERT INTO static_country_zones VALUES ('168', '0', 'ES', 'ESP', '724', 'Ourense', 'Ourense', '');
INSERT INTO static_country_zones VALUES ('169', '0', 'ES', 'ESP', '724', 'Palencia', 'Palencia', '');
INSERT INTO static_country_zones VALUES ('170', '0', 'ES', 'ESP', '724', 'Pontevedra', 'Pontevedra', '');
INSERT INTO static_country_zones VALUES ('171', '0', 'ES', 'ESP', '724', 'Salamanca', 'Salamanca', '');
INSERT INTO static_country_zones VALUES ('172', '0', 'ES', 'ESP', '724', 'Soria', 'Soria', '');
INSERT INTO static_country_zones VALUES ('173', '0', 'ES', 'ESP', '724', 'Tarragona', 'Tarragona', '');
INSERT INTO static_country_zones VALUES ('174', '0', 'ES', 'ESP', '724', 'Tenerife', 'Tenerife', '');
INSERT INTO static_country_zones VALUES ('175', '0', 'ES', 'ESP', '724', 'Teruel', 'Teruel', '');
INSERT INTO static_country_zones VALUES ('176', '0', 'ES', 'ESP', '724', 'Toledo', 'Toledo', '');
INSERT INTO static_country_zones VALUES ('177', '0', 'ES', 'ESP', '724', 'Valladolid', 'Valladolid', '');
INSERT INTO static_country_zones VALUES ('178', '0', 'ES', 'ESP', '724', 'Vizcaya', 'Vizcaya', '');
INSERT INTO static_country_zones VALUES ('179', '0', 'ES', 'ESP', '724', 'Zamora', 'Zamora', '');
INSERT INTO static_country_zones VALUES ('180', '0', 'ES', 'ESP', '724', 'Zaragoza', 'Zaragoza', '');
INSERT INTO static_country_zones VALUES ('181', '0', 'ES', 'ESP', '724', 'Melilla', 'Melilla', '');
INSERT INTO static_country_zones VALUES ('182', '0', 'MX', 'MEX', '484', 'AGS', 'Aguascalientes', '');
INSERT INTO static_country_zones VALUES ('183', '0', 'MX', 'MEX', '484', 'BCS', 'Baja California Sur', '');
INSERT INTO static_country_zones VALUES ('184', '0', 'MX', 'MEX', '484', 'BC', 'Baja California Norte', '');
INSERT INTO static_country_zones VALUES ('185', '0', 'MX', 'MEX', '484', 'CAM', 'Campeche', '');
INSERT INTO static_country_zones VALUES ('186', '0', 'MX', 'MEX', '484', 'CHIS', 'Chiapas', '');
INSERT INTO static_country_zones VALUES ('187', '0', 'MX', 'MEX', '484', 'CHIH', 'Chihuahua', '');
INSERT INTO static_country_zones VALUES ('188', '0', 'MX', 'MEX', '484', 'COAH', 'Coahuila', '');
INSERT INTO static_country_zones VALUES ('189', '0', 'MX', 'MEX', '484', 'COL', 'Colima', '');
INSERT INTO static_country_zones VALUES ('190', '0', 'MX', 'MEX', '484', 'DIF', 'Distrito Federal', '');
INSERT INTO static_country_zones VALUES ('191', '0', 'MX', 'MEX', '484', 'DGO', 'Durango', '');
INSERT INTO static_country_zones VALUES ('192', '0', 'MX', 'MEX', '484', 'GTO', 'Guanajuato', '');
INSERT INTO static_country_zones VALUES ('193', '0', 'MX', 'MEX', '484', 'GRO', 'Guerrero', '');
INSERT INTO static_country_zones VALUES ('194', '0', 'MX', 'MEX', '484', 'HGO', 'Hidalgo', '');
INSERT INTO static_country_zones VALUES ('195', '0', 'MX', 'MEX', '484', 'JAL', 'Jalisco', '');
INSERT INTO static_country_zones VALUES ('196', '0', 'MX', 'MEX', '484', 'MEX', 'México', '');
INSERT INTO static_country_zones VALUES ('197', '0', 'MX', 'MEX', '484', 'MICH', 'Michoacán', '');
INSERT INTO static_country_zones VALUES ('198', '0', 'MX', 'MEX', '484', 'MOR', 'Morelos', '');
INSERT INTO static_country_zones VALUES ('199', '0', 'MX', 'MEX', '484', 'NAY', 'Nayarit', '');
INSERT INTO static_country_zones VALUES ('200', '0', 'MX', 'MEX', '484', 'NL', 'Nuevo León', '');
INSERT INTO static_country_zones VALUES ('201', '0', 'MX', 'MEX', '484', 'OAX', 'Oaxaca', '');
INSERT INTO static_country_zones VALUES ('202', '0', 'MX', 'MEX', '484', 'PUE', 'Puebla', '');
INSERT INTO static_country_zones VALUES ('203', '0', 'MX', 'MEX', '484', 'QRO', 'Querétaro', '');
INSERT INTO static_country_zones VALUES ('204', '0', 'MX', 'MEX', '484', 'QROO', 'Quintana Roo', '');
INSERT INTO static_country_zones VALUES ('205', '0', 'MX', 'MEX', '484', 'SLP', 'San Luis Potosí', '');
INSERT INTO static_country_zones VALUES ('206', '0', 'MX', 'MEX', '484', 'SIN', 'Sinaloa', '');
INSERT INTO static_country_zones VALUES ('207', '0', 'MX', 'MEX', '484', 'SON', 'Sonora', '');
INSERT INTO static_country_zones VALUES ('208', '0', 'MX', 'MEX', '484', 'TAB', 'Tabasco', '');
INSERT INTO static_country_zones VALUES ('209', '0', 'MX', 'MEX', '484', 'TAMPS', 'Tamaulipas', '');
INSERT INTO static_country_zones VALUES ('210', '0', 'MX', 'MEX', '484', 'TLAX', 'Tlaxcala', '');
INSERT INTO static_country_zones VALUES ('211', '0', 'MX', 'MEX', '484', 'VER', 'Veracruz', '');
INSERT INTO static_country_zones VALUES ('212', '0', 'MX', 'MEX', '484', 'YUC', 'Yucatán', '');
INSERT INTO static_country_zones VALUES ('213', '0', 'MX', 'MEX', '484', 'ZAC', 'Zacatecas', '');
INSERT INTO static_country_zones VALUES ('214', '0', 'AU', 'AUS', '36', 'ACT', 'Australian Capital Territory', '');
INSERT INTO static_country_zones VALUES ('215', '0', 'AU', 'AUS', '36', 'NSW', 'New South Wales', '');
INSERT INTO static_country_zones VALUES ('216', '0', 'AU', 'AUS', '36', 'NT', 'Northern Territory', '');
INSERT INTO static_country_zones VALUES ('217', '0', 'AU', 'AUS', '36', 'QLD', 'Queensland', '');
INSERT INTO static_country_zones VALUES ('218', '0', 'AU', 'AUS', '36', 'SA', 'South Australia', '');
INSERT INTO static_country_zones VALUES ('219', '0', 'AU', 'AUS', '36', 'TAS', 'Tasmania', '');
INSERT INTO static_country_zones VALUES ('220', '0', 'AU', 'AUS', '36', 'VIC', 'Victoria', '');
INSERT INTO static_country_zones VALUES ('221', '0', 'AU', 'AUS', '36', 'WA', 'Western Australia', '');
INSERT INTO static_country_zones VALUES ('222', '0', 'IT', 'ITA', '380', 'AG', 'Agrigento', '');
INSERT INTO static_country_zones VALUES ('223', '0', 'IT', 'ITA', '380', 'AL', 'Alessandria', '');
INSERT INTO static_country_zones VALUES ('224', '0', 'IT', 'ITA', '380', 'AN', 'Ancona', '');
INSERT INTO static_country_zones VALUES ('225', '0', 'IT', 'ITA', '380', 'AO', 'Aosta', '');
INSERT INTO static_country_zones VALUES ('226', '0', 'IT', 'ITA', '380', 'AP', 'Ascoli Piceno', '');
INSERT INTO static_country_zones VALUES ('227', '0', 'IT', 'ITA', '380', 'AQ', 'L\'Aquila', '');
INSERT INTO static_country_zones VALUES ('228', '0', 'IT', 'ITA', '380', 'AR', 'Arezzo', '');
INSERT INTO static_country_zones VALUES ('229', '0', 'IT', 'ITA', '380', 'AT', 'Asti', '');
INSERT INTO static_country_zones VALUES ('230', '0', 'IT', 'ITA', '380', 'AV', 'Avellino', '');
INSERT INTO static_country_zones VALUES ('231', '0', 'IT', 'ITA', '380', 'BA', 'Bari', '');
INSERT INTO static_country_zones VALUES ('232', '0', 'IT', 'ITA', '380', 'BG', 'Bergamo', '');
INSERT INTO static_country_zones VALUES ('233', '0', 'IT', 'ITA', '380', 'BI', 'Biella', '');
INSERT INTO static_country_zones VALUES ('234', '0', 'IT', 'ITA', '380', 'BL', 'Belluno', '');
INSERT INTO static_country_zones VALUES ('235', '0', 'IT', 'ITA', '380', 'BN', 'Benevento', '');
INSERT INTO static_country_zones VALUES ('236', '0', 'IT', 'ITA', '380', 'BO', 'Bologna', '');
INSERT INTO static_country_zones VALUES ('237', '0', 'IT', 'ITA', '380', 'BR', 'Brindisi', '');
INSERT INTO static_country_zones VALUES ('238', '0', 'IT', 'ITA', '380', 'BS', 'Brescia', '');
INSERT INTO static_country_zones VALUES ('239', '0', 'IT', 'ITA', '380', 'BT', 'Barletta-Andria-Trani', '');
INSERT INTO static_country_zones VALUES ('240', '0', 'IT', 'ITA', '380', 'BZ', 'Bozen', '');
INSERT INTO static_country_zones VALUES ('241', '0', 'IT', 'ITA', '380', 'CA', 'Cagliari', '');
INSERT INTO static_country_zones VALUES ('242', '0', 'IT', 'ITA', '380', 'CB', 'Campobasso', '');
INSERT INTO static_country_zones VALUES ('243', '0', 'IT', 'ITA', '380', 'CE', 'Caserta', '');
INSERT INTO static_country_zones VALUES ('244', '0', 'IT', 'ITA', '380', 'CH', 'Chieti', '');
INSERT INTO static_country_zones VALUES ('245', '0', 'IT', 'ITA', '380', 'CI', 'Carbonia-Iglesias', '');
INSERT INTO static_country_zones VALUES ('246', '0', 'IT', 'ITA', '380', 'CL', 'Caltanissetta', '');
INSERT INTO static_country_zones VALUES ('247', '0', 'IT', 'ITA', '380', 'CN', 'Cuneo', '');
INSERT INTO static_country_zones VALUES ('248', '0', 'IT', 'ITA', '380', 'CO', 'Como', '');
INSERT INTO static_country_zones VALUES ('249', '0', 'IT', 'ITA', '380', 'CR', 'Cremona', '');
INSERT INTO static_country_zones VALUES ('250', '0', 'IT', 'ITA', '380', 'CS', 'Cosenza', '');
INSERT INTO static_country_zones VALUES ('251', '0', 'IT', 'ITA', '380', 'CT', 'Catania', '');
INSERT INTO static_country_zones VALUES ('252', '0', 'IT', 'ITA', '380', 'CZ', 'Catanzaro', '');
INSERT INTO static_country_zones VALUES ('253', '0', 'IT', 'ITA', '380', 'EN', 'Enna', '');
INSERT INTO static_country_zones VALUES ('254', '0', 'IT', 'ITA', '380', 'FE', 'Ferrara', '');
INSERT INTO static_country_zones VALUES ('255', '0', 'IT', 'ITA', '380', 'FG', 'Foggia', '');
INSERT INTO static_country_zones VALUES ('256', '0', 'IT', 'ITA', '380', 'FI', 'Firenze', 'Florence');
INSERT INTO static_country_zones VALUES ('257', '0', 'IT', 'ITA', '380', 'FM', 'Fermo', '');
INSERT INTO static_country_zones VALUES ('258', '0', 'IT', 'ITA', '380', 'FC', 'Forli', '');
INSERT INTO static_country_zones VALUES ('259', '0', 'IT', 'ITA', '380', 'FR', 'Frosinone', '');
INSERT INTO static_country_zones VALUES ('260', '0', 'IT', 'ITA', '380', 'GE', 'Genova', '');
INSERT INTO static_country_zones VALUES ('261', '0', 'IT', 'ITA', '380', 'GO', 'Gorizia', '');
INSERT INTO static_country_zones VALUES ('262', '0', 'IT', 'ITA', '380', 'GR', 'Grosseto', '');
INSERT INTO static_country_zones VALUES ('263', '0', 'IT', 'ITA', '380', 'IM', 'Imperia', '');
INSERT INTO static_country_zones VALUES ('264', '0', 'IT', 'ITA', '380', 'IS', 'Isernia', '');
INSERT INTO static_country_zones VALUES ('265', '0', 'IT', 'ITA', '380', 'KR', 'Crotone', '');
INSERT INTO static_country_zones VALUES ('266', '0', 'IT', 'ITA', '380', 'LC', 'Lecco', '');
INSERT INTO static_country_zones VALUES ('267', '0', 'IT', 'ITA', '380', 'LE', 'Lecce', '');
INSERT INTO static_country_zones VALUES ('268', '0', 'IT', 'ITA', '380', 'LI', 'Livorno', '');
INSERT INTO static_country_zones VALUES ('269', '0', 'IT', 'ITA', '380', 'LO', 'Lodi', '');
INSERT INTO static_country_zones VALUES ('270', '0', 'IT', 'ITA', '380', 'LT', 'Latina', '');
INSERT INTO static_country_zones VALUES ('271', '0', 'IT', 'ITA', '380', 'LU', 'Lucca', '');
INSERT INTO static_country_zones VALUES ('272', '0', 'IT', 'ITA', '380', 'MB', 'Monza e della Brianza', '');
INSERT INTO static_country_zones VALUES ('273', '0', 'IT', 'ITA', '380', 'MC', 'Macerata', '');
INSERT INTO static_country_zones VALUES ('274', '0', 'IT', 'ITA', '380', 'ME', 'Messina', '');
INSERT INTO static_country_zones VALUES ('275', '0', 'IT', 'ITA', '380', 'MI', 'Milano', '');
INSERT INTO static_country_zones VALUES ('276', '0', 'IT', 'ITA', '380', 'MN', 'Mantova', '');
INSERT INTO static_country_zones VALUES ('277', '0', 'IT', 'ITA', '380', 'MO', 'Modena', '');
INSERT INTO static_country_zones VALUES ('278', '0', 'IT', 'ITA', '380', 'MS', 'Massa Carrara', '');
INSERT INTO static_country_zones VALUES ('279', '0', 'IT', 'ITA', '380', 'MT', 'Matera', '');
INSERT INTO static_country_zones VALUES ('280', '0', 'IT', 'ITA', '380', 'NA', 'Napoli', 'Naples');
INSERT INTO static_country_zones VALUES ('281', '0', 'IT', 'ITA', '380', 'NO', 'Novara', '');
INSERT INTO static_country_zones VALUES ('282', '0', 'IT', 'ITA', '380', 'NU', 'Nuoro', '');
INSERT INTO static_country_zones VALUES ('283', '0', 'IT', 'ITA', '380', 'OG', 'Ogliastra', '');
INSERT INTO static_country_zones VALUES ('284', '0', 'IT', 'ITA', '380', 'OR', 'Oristano', '');
INSERT INTO static_country_zones VALUES ('285', '0', 'IT', 'ITA', '380', 'OT', 'Olbia-Tempio', '');
INSERT INTO static_country_zones VALUES ('286', '0', 'IT', 'ITA', '380', 'PA', 'Palermo', '');
INSERT INTO static_country_zones VALUES ('287', '0', 'IT', 'ITA', '380', 'PC', 'Piacenza', '');
INSERT INTO static_country_zones VALUES ('288', '0', 'IT', 'ITA', '380', 'PD', 'Padova', '');
INSERT INTO static_country_zones VALUES ('289', '0', 'IT', 'ITA', '380', 'PE', 'Pescara', '');
INSERT INTO static_country_zones VALUES ('290', '0', 'IT', 'ITA', '380', 'PG', 'Perugia', '');
INSERT INTO static_country_zones VALUES ('291', '0', 'IT', 'ITA', '380', 'PI', 'Pisa', '');
INSERT INTO static_country_zones VALUES ('292', '0', 'IT', 'ITA', '380', 'PN', 'Pordenone', '');
INSERT INTO static_country_zones VALUES ('293', '0', 'IT', 'ITA', '380', 'PR', 'Parma', '');
INSERT INTO static_country_zones VALUES ('294', '0', 'IT', 'ITA', '380', 'PT', 'Pistoia', '');
INSERT INTO static_country_zones VALUES ('295', '0', 'IT', 'ITA', '380', 'PU', 'Pesaro e Urbino', '');
INSERT INTO static_country_zones VALUES ('296', '0', 'IT', 'ITA', '380', 'PV', 'Pavia', '');
INSERT INTO static_country_zones VALUES ('297', '0', 'IT', 'ITA', '380', 'PO', 'Prato', '');
INSERT INTO static_country_zones VALUES ('298', '0', 'IT', 'ITA', '380', 'PZ', 'Potenza', '');
INSERT INTO static_country_zones VALUES ('299', '0', 'IT', 'ITA', '380', 'RA', 'Ravenna', '');
INSERT INTO static_country_zones VALUES ('300', '0', 'IT', 'ITA', '380', 'RC', 'Reggio Calabria', '');
INSERT INTO static_country_zones VALUES ('301', '0', 'IT', 'ITA', '380', 'RE', 'Reggio Emilia', '');
INSERT INTO static_country_zones VALUES ('302', '0', 'IT', 'ITA', '380', 'RG', 'Ragusa', '');
INSERT INTO static_country_zones VALUES ('303', '0', 'IT', 'ITA', '380', 'RI', 'Rieti', '');
INSERT INTO static_country_zones VALUES ('304', '0', 'IT', 'ITA', '380', 'RM', 'Roma', 'Rome');
INSERT INTO static_country_zones VALUES ('305', '0', 'IT', 'ITA', '380', 'RN', 'Rimini', '');
INSERT INTO static_country_zones VALUES ('306', '0', 'IT', 'ITA', '380', 'RO', 'Rovigo', '');
INSERT INTO static_country_zones VALUES ('307', '0', 'IT', 'ITA', '380', 'SA', 'Salerno', '');
INSERT INTO static_country_zones VALUES ('308', '0', 'IT', 'ITA', '380', 'SI', 'Siena', '');
INSERT INTO static_country_zones VALUES ('309', '0', 'IT', 'ITA', '380', 'SO', 'Sondrio', '');
INSERT INTO static_country_zones VALUES ('310', '0', 'IT', 'ITA', '380', 'SP', 'La Spezia', '');
INSERT INTO static_country_zones VALUES ('311', '0', 'IT', 'ITA', '380', 'SR', 'Siracusa', '');
INSERT INTO static_country_zones VALUES ('312', '0', 'IT', 'ITA', '380', 'SS', 'Sassari', '');
INSERT INTO static_country_zones VALUES ('313', '0', 'IT', 'ITA', '380', 'SV', 'Savona', '');
INSERT INTO static_country_zones VALUES ('314', '0', 'IT', 'ITA', '380', 'TA', 'Taranto', '');
INSERT INTO static_country_zones VALUES ('315', '0', 'IT', 'ITA', '380', 'TE', 'Teramo', '');
INSERT INTO static_country_zones VALUES ('316', '0', 'IT', 'ITA', '380', 'TN', 'Trento', '');
INSERT INTO static_country_zones VALUES ('317', '0', 'IT', 'ITA', '380', 'TO', 'Torino', '');
INSERT INTO static_country_zones VALUES ('318', '0', 'IT', 'ITA', '380', 'TP', 'Trapani', '');
INSERT INTO static_country_zones VALUES ('319', '0', 'IT', 'ITA', '380', 'TR', 'Terni', '');
INSERT INTO static_country_zones VALUES ('320', '0', 'IT', 'ITA', '380', 'TS', 'Trieste', '');
INSERT INTO static_country_zones VALUES ('321', '0', 'IT', 'ITA', '380', 'TV', 'Treviso', '');
INSERT INTO static_country_zones VALUES ('322', '0', 'IT', 'ITA', '380', 'UD', 'Udine', '');
INSERT INTO static_country_zones VALUES ('323', '0', 'IT', 'ITA', '380', 'VA', 'Varese', '');
INSERT INTO static_country_zones VALUES ('324', '0', 'IT', 'ITA', '380', 'VB', 'Verbano-Cusio-Ossola', '');
INSERT INTO static_country_zones VALUES ('325', '0', 'IT', 'ITA', '380', 'VC', 'Vercelli', '');
INSERT INTO static_country_zones VALUES ('326', '0', 'IT', 'ITA', '380', 'VE', 'Venezia', 'Venice');
INSERT INTO static_country_zones VALUES ('327', '0', 'IT', 'ITA', '380', 'VI', 'Vicenza', '');
INSERT INTO static_country_zones VALUES ('328', '0', 'IT', 'ITA', '380', 'VR', 'Verona', '');
INSERT INTO static_country_zones VALUES ('329', '0', 'IT', 'ITA', '380', 'VS', 'Medio Campidano', '');
INSERT INTO static_country_zones VALUES ('330', '0', 'IT', 'ITA', '380', 'VT', 'Viterbo', '');
INSERT INTO static_country_zones VALUES ('331', '0', 'IT', 'ITA', '380', 'VV', 'Vibo Valentia', '');
INSERT INTO static_country_zones VALUES ('332', '0', 'GB', 'GBR', '826', 'ALD', 'Alderney', '');
INSERT INTO static_country_zones VALUES ('333', '0', 'GB', 'GBR', '826', 'ARM', 'Armagh', '');
INSERT INTO static_country_zones VALUES ('334', '0', 'GB', 'GBR', '826', 'ATM', 'Antrim', '');
INSERT INTO static_country_zones VALUES ('335', '0', 'GB', 'GBR', '826', 'BDS', 'Borders', '');
INSERT INTO static_country_zones VALUES ('336', '0', 'GB', 'GBR', '826', 'BFD', 'Bedfordshire', '');
INSERT INTO static_country_zones VALUES ('337', '0', 'GB', 'GBR', '826', 'BIR', 'Birmingham', '');
INSERT INTO static_country_zones VALUES ('338', '0', 'GB', 'GBR', '826', 'BLG', 'Blaenau Gwent', '');
INSERT INTO static_country_zones VALUES ('339', '0', 'GB', 'GBR', '826', 'BRI', 'Bridgend', '');
INSERT INTO static_country_zones VALUES ('340', '0', 'GB', 'GBR', '826', 'BRK', 'Berkshire', '');
INSERT INTO static_country_zones VALUES ('341', '0', 'GB', 'GBR', '826', 'BRS', 'Bristol', '');
INSERT INTO static_country_zones VALUES ('342', '0', 'GB', 'GBR', '826', 'BUX', 'Buckinghamshire', '');
INSERT INTO static_country_zones VALUES ('343', '0', 'GB', 'GBR', '826', 'CAP', 'Caerphilly', '');
INSERT INTO static_country_zones VALUES ('344', '0', 'GB', 'GBR', '826', 'CAR', 'Cardiff', '');
INSERT INTO static_country_zones VALUES ('345', '0', 'GB', 'GBR', '826', 'CAS', 'Carmarthenshire', '');
INSERT INTO static_country_zones VALUES ('346', '0', 'GB', 'GBR', '826', 'CBA', 'Cumbria', '');
INSERT INTO static_country_zones VALUES ('347', '0', 'GB', 'GBR', '826', 'CBE', 'Cambridgeshire', '');
INSERT INTO static_country_zones VALUES ('348', '0', 'GB', 'GBR', '826', 'CER', 'Ceredigion', '');
INSERT INTO static_country_zones VALUES ('349', '0', 'GB', 'GBR', '826', 'CHI', 'Channel Islands', '');
INSERT INTO static_country_zones VALUES ('350', '0', 'GB', 'GBR', '826', 'CHS', 'Cheshire', '');
INSERT INTO static_country_zones VALUES ('351', '0', 'GB', 'GBR', '826', 'CLD', 'Clwyd', '');
INSERT INTO static_country_zones VALUES ('352', '0', 'GB', 'GBR', '826', 'CNL', 'Cornwall', '');
INSERT INTO static_country_zones VALUES ('353', '0', 'GB', 'GBR', '826', 'CON', 'Conway', '');
INSERT INTO static_country_zones VALUES ('354', '0', 'GB', 'GBR', '826', 'CTR', 'Central', '');
INSERT INTO static_country_zones VALUES ('355', '0', 'GB', 'GBR', '826', 'CVE', 'Cleveland', '');
INSERT INTO static_country_zones VALUES ('356', '0', 'GB', 'GBR', '826', 'DEN', 'Denbighshire', '');
INSERT INTO static_country_zones VALUES ('357', '0', 'GB', 'GBR', '826', 'DFD', 'Dyfed', '');
INSERT INTO static_country_zones VALUES ('358', '0', 'GB', 'GBR', '826', 'DGL', 'Dumfries and Galloway', '');
INSERT INTO static_country_zones VALUES ('359', '0', 'GB', 'GBR', '826', 'DHM', 'Durham', '');
INSERT INTO static_country_zones VALUES ('360', '0', 'GB', 'GBR', '826', 'DOR', 'Dorset', '');
INSERT INTO static_country_zones VALUES ('361', '0', 'GB', 'GBR', '826', 'DVN', 'Devon', '');
INSERT INTO static_country_zones VALUES ('362', '0', 'GB', 'GBR', '826', 'DWN', 'Down', '');
INSERT INTO static_country_zones VALUES ('363', '0', 'GB', 'GBR', '826', 'DYS', 'Derbyshire', '');
INSERT INTO static_country_zones VALUES ('364', '0', 'GB', 'GBR', '826', 'ESX', 'Essex', '');
INSERT INTO static_country_zones VALUES ('365', '0', 'GB', 'GBR', '826', 'FER', 'Fermanagh', '');
INSERT INTO static_country_zones VALUES ('366', '0', 'GB', 'GBR', '826', 'FFE', 'Fife', '');
INSERT INTO static_country_zones VALUES ('367', '0', 'GB', 'GBR', '826', 'FLI', 'Flintshire', '');
INSERT INTO static_country_zones VALUES ('368', '0', 'GB', 'GBR', '826', 'FMH', 'County Fermanagh', '');
INSERT INTO static_country_zones VALUES ('369', '0', 'GB', 'GBR', '826', 'GDD', 'Gwynedd', '');
INSERT INTO static_country_zones VALUES ('370', '0', 'GB', 'GBR', '826', 'GLO', 'Gloucestershire', '');
INSERT INTO static_country_zones VALUES ('371', '0', 'GB', 'GBR', '826', 'GLR', 'Gloucester', '');
INSERT INTO static_country_zones VALUES ('372', '0', 'GB', 'GBR', '826', 'GNM', 'Mid Glamorgan', '');
INSERT INTO static_country_zones VALUES ('373', '0', 'GB', 'GBR', '826', 'GNS', 'South Glamorgan', '');
INSERT INTO static_country_zones VALUES ('374', '0', 'GB', 'GBR', '826', 'GNW', 'West Glamorgan', '');
INSERT INTO static_country_zones VALUES ('375', '0', 'GB', 'GBR', '826', 'GRN', 'Grampian', '');
INSERT INTO static_country_zones VALUES ('376', '0', 'GB', 'GBR', '826', 'GUR', 'Guernsey', '');
INSERT INTO static_country_zones VALUES ('377', '0', 'GB', 'GBR', '826', 'GWT', 'Gwent', '');
INSERT INTO static_country_zones VALUES ('378', '0', 'GB', 'GBR', '826', 'HBS', 'Humberside', '');
INSERT INTO static_country_zones VALUES ('379', '0', 'GB', 'GBR', '826', 'HFD', 'Hertfordshire', '');
INSERT INTO static_country_zones VALUES ('380', '0', 'GB', 'GBR', '826', 'HLD', 'Highlands', '');
INSERT INTO static_country_zones VALUES ('381', '0', 'GB', 'GBR', '826', 'HPH', 'Hampshire', '');
INSERT INTO static_country_zones VALUES ('382', '0', 'GB', 'GBR', '826', 'HWR', 'Hereford and Worcester', '');
INSERT INTO static_country_zones VALUES ('383', '0', 'GB', 'GBR', '826', 'IOM', 'Isle of Man', '');
INSERT INTO static_country_zones VALUES ('384', '0', 'GB', 'GBR', '826', 'IOW', 'Isle of Wight', '');
INSERT INTO static_country_zones VALUES ('385', '0', 'GB', 'GBR', '826', 'ISL', 'Isle of Anglesey', '');
INSERT INTO static_country_zones VALUES ('386', '0', 'GB', 'GBR', '826', 'JER', 'Jersey', '');
INSERT INTO static_country_zones VALUES ('387', '0', 'GB', 'GBR', '826', 'KNT', 'Kent', '');
INSERT INTO static_country_zones VALUES ('388', '0', 'GB', 'GBR', '826', 'LCN', 'Lincolnshire', '');
INSERT INTO static_country_zones VALUES ('389', '0', 'GB', 'GBR', '826', 'LDN', 'Greater London', '');
INSERT INTO static_country_zones VALUES ('390', '0', 'GB', 'GBR', '826', 'LDR', 'Londonderry', '');
INSERT INTO static_country_zones VALUES ('391', '0', 'GB', 'GBR', '826', 'LEC', 'Leicestershire', '');
INSERT INTO static_country_zones VALUES ('392', '0', 'GB', 'GBR', '826', 'LNH', 'Lancashire', '');
INSERT INTO static_country_zones VALUES ('393', '0', 'GB', 'GBR', '826', 'LON', 'London', '');
INSERT INTO static_country_zones VALUES ('394', '0', 'GB', 'GBR', '826', 'LTE', 'East Lothian', '');
INSERT INTO static_country_zones VALUES ('395', '0', 'GB', 'GBR', '826', 'LTM', 'Mid Lothian', '');
INSERT INTO static_country_zones VALUES ('396', '0', 'GB', 'GBR', '826', 'LTW', 'West Lothian', '');
INSERT INTO static_country_zones VALUES ('397', '0', 'GB', 'GBR', '826', 'MCH', 'Greater Manchester', '');
INSERT INTO static_country_zones VALUES ('398', '0', 'GB', 'GBR', '826', 'MER', 'Merthyr Tydfil', '');
INSERT INTO static_country_zones VALUES ('399', '0', 'GB', 'GBR', '826', 'MON', 'Monmouthshire', '');
INSERT INTO static_country_zones VALUES ('400', '0', 'GB', 'GBR', '826', 'MSY', 'Merseyside', '');
INSERT INTO static_country_zones VALUES ('401', '0', 'GB', 'GBR', '826', 'NET', 'Neath Port Talbot', '');
INSERT INTO static_country_zones VALUES ('402', '0', 'GB', 'GBR', '826', 'NEW', 'Newport', '');
INSERT INTO static_country_zones VALUES ('403', '0', 'GB', 'GBR', '826', 'NHM', 'Northamptonshire', '');
INSERT INTO static_country_zones VALUES ('404', '0', 'GB', 'GBR', '826', 'NLD', 'Northumberland', '');
INSERT INTO static_country_zones VALUES ('405', '0', 'GB', 'GBR', '826', 'NOR', 'Norfolk', '');
INSERT INTO static_country_zones VALUES ('406', '0', 'GB', 'GBR', '826', 'NOT', 'Nottinghamshire', '');
INSERT INTO static_country_zones VALUES ('407', '0', 'GB', 'GBR', '826', 'NWH', 'North West Highlands', '');
INSERT INTO static_country_zones VALUES ('408', '0', 'GB', 'GBR', '826', 'OFE', 'Oxfordshire', '');
INSERT INTO static_country_zones VALUES ('409', '0', 'GB', 'GBR', '826', 'ORK', 'Orkney', '');
INSERT INTO static_country_zones VALUES ('410', '0', 'GB', 'GBR', '826', 'PEM', 'Pembrokeshire', '');
INSERT INTO static_country_zones VALUES ('411', '0', 'GB', 'GBR', '826', 'PWS', 'Powys', '');
INSERT INTO static_country_zones VALUES ('412', '0', 'GB', 'GBR', '826', 'SCD', 'Strathclyde', '');
INSERT INTO static_country_zones VALUES ('413', '0', 'GB', 'GBR', '826', 'SFD', 'Staffordshire', '');
INSERT INTO static_country_zones VALUES ('414', '0', 'GB', 'GBR', '826', 'SFK', 'Suffolk', '');
INSERT INTO static_country_zones VALUES ('415', '0', 'GB', 'GBR', '826', 'SLD', 'Shetland', '');
INSERT INTO static_country_zones VALUES ('416', '0', 'GB', 'GBR', '826', 'SOM', 'Somerset', '');
INSERT INTO static_country_zones VALUES ('417', '0', 'GB', 'GBR', '826', 'SPE', 'Shropshire', '');
INSERT INTO static_country_zones VALUES ('418', '0', 'GB', 'GBR', '826', 'SRK', 'Sark', '');
INSERT INTO static_country_zones VALUES ('419', '0', 'GB', 'GBR', '826', 'SRY', 'Surrey', '');
INSERT INTO static_country_zones VALUES ('420', '0', 'GB', 'GBR', '826', 'SWA', 'Swansea', '');
INSERT INTO static_country_zones VALUES ('421', '0', 'GB', 'GBR', '826', 'SXE', 'East Sussex', '');
INSERT INTO static_country_zones VALUES ('422', '0', 'GB', 'GBR', '826', 'SXW', 'West Sussex', '');
INSERT INTO static_country_zones VALUES ('423', '0', 'GB', 'GBR', '826', 'TAF', 'Rhondda Cynon Taff', '');
INSERT INTO static_country_zones VALUES ('424', '0', 'GB', 'GBR', '826', 'TOR', 'Torfaen', '');
INSERT INTO static_country_zones VALUES ('425', '0', 'GB', 'GBR', '826', 'TWR', 'Tyne and Wear', '');
INSERT INTO static_country_zones VALUES ('426', '0', 'GB', 'GBR', '826', 'TYR', 'Tyrone', '');
INSERT INTO static_country_zones VALUES ('427', '0', 'GB', 'GBR', '826', 'TYS', 'Tayside', '');
INSERT INTO static_country_zones VALUES ('428', '0', 'GB', 'GBR', '826', 'VAL', 'Vale of Glamorgan', '');
INSERT INTO static_country_zones VALUES ('429', '0', 'GB', 'GBR', '826', 'WIL', 'Western Isles', '');
INSERT INTO static_country_zones VALUES ('430', '0', 'GB', 'GBR', '826', 'WKS', 'Warwickshire', '');
INSERT INTO static_country_zones VALUES ('431', '0', 'GB', 'GBR', '826', 'WLT', 'Wiltshire', '');
INSERT INTO static_country_zones VALUES ('432', '0', 'GB', 'GBR', '826', 'WMD', 'West Midlands', '');
INSERT INTO static_country_zones VALUES ('433', '0', 'GB', 'GBR', '826', 'WRE', 'Wrexham', '');
INSERT INTO static_country_zones VALUES ('434', '0', 'GB', 'GBR', '826', 'YSN', 'North Yorkshire', '');
INSERT INTO static_country_zones VALUES ('435', '0', 'GB', 'GBR', '826', 'YSS', 'South Yorkshire', '');
INSERT INTO static_country_zones VALUES ('436', '0', 'GB', 'GBR', '826', 'YSW', 'West Yorkshire', '');
INSERT INTO static_country_zones VALUES ('460', '0', 'IE', 'IRL', '372', 'CAR', 'Carlow', '');
INSERT INTO static_country_zones VALUES ('461', '0', 'IE', 'IRL', '372', 'CAV', 'Cavan', '');
INSERT INTO static_country_zones VALUES ('462', '0', 'IE', 'IRL', '372', 'CLA', 'Clare', '');
INSERT INTO static_country_zones VALUES ('463', '0', 'IE', 'IRL', '372', 'COR', 'Cork', '');
INSERT INTO static_country_zones VALUES ('464', '0', 'IE', 'IRL', '372', 'DON', 'Donegal', '');
INSERT INTO static_country_zones VALUES ('465', '0', 'IE', 'IRL', '372', 'DUB', 'Dublin', '');
INSERT INTO static_country_zones VALUES ('466', '0', 'IE', 'IRL', '372', 'GAL', 'Galway', '');
INSERT INTO static_country_zones VALUES ('467', '0', 'IE', 'IRL', '372', 'KER', 'Kerry', '');
INSERT INTO static_country_zones VALUES ('468', '0', 'IE', 'IRL', '372', 'KIL', 'Kildare', '');
INSERT INTO static_country_zones VALUES ('469', '0', 'IE', 'IRL', '372', 'KLK', 'Kilkenny', '');
INSERT INTO static_country_zones VALUES ('470', '0', 'IE', 'IRL', '372', 'LAO', 'Laois', '');
INSERT INTO static_country_zones VALUES ('471', '0', 'IE', 'IRL', '372', 'LEI', 'Leitrim', '');
INSERT INTO static_country_zones VALUES ('472', '0', 'IE', 'IRL', '372', 'LIM', 'Limerick', '');
INSERT INTO static_country_zones VALUES ('473', '0', 'IE', 'IRL', '372', 'LON', 'Longford', '');
INSERT INTO static_country_zones VALUES ('474', '0', 'IE', 'IRL', '372', 'LOU', 'Louth', '');
INSERT INTO static_country_zones VALUES ('475', '0', 'IE', 'IRL', '372', 'MAY', 'Mayo', '');
INSERT INTO static_country_zones VALUES ('476', '0', 'IE', 'IRL', '372', 'MEA', 'Meath', '');
INSERT INTO static_country_zones VALUES ('477', '0', 'IE', 'IRL', '372', 'MON', 'Monaghan', '');
INSERT INTO static_country_zones VALUES ('478', '0', 'IE', 'IRL', '372', 'OFF', 'Offaly', '');
INSERT INTO static_country_zones VALUES ('479', '0', 'IE', 'IRL', '372', 'ROS', 'Roscommon', '');
INSERT INTO static_country_zones VALUES ('480', '0', 'IE', 'IRL', '372', 'SLI', 'Sligo', '');
INSERT INTO static_country_zones VALUES ('481', '0', 'IE', 'IRL', '372', 'TIP', 'Tipperary', '');
INSERT INTO static_country_zones VALUES ('482', '0', 'IE', 'IRL', '372', 'WAT', 'Waterford', '');
INSERT INTO static_country_zones VALUES ('483', '0', 'IE', 'IRL', '372', 'WES', 'Westmeath', '');
INSERT INTO static_country_zones VALUES ('484', '0', 'IE', 'IRL', '372', 'WEX', 'Wexford', '');
INSERT INTO static_country_zones VALUES ('485', '0', 'IE', 'IRL', '372', 'WIC', 'Wicklow', '');
INSERT INTO static_country_zones VALUES ('490', '0', 'BR', 'BRA', '76', 'AC', 'Acre', '');
INSERT INTO static_country_zones VALUES ('491', '0', 'BR', 'BRA', '76', 'AP', 'Amapá', '');
INSERT INTO static_country_zones VALUES ('492', '0', 'BR', 'BRA', '76', 'AL', 'Alagoas', '');
INSERT INTO static_country_zones VALUES ('493', '0', 'BR', 'BRA', '76', 'AM', 'Amazonas', '');
INSERT INTO static_country_zones VALUES ('494', '0', 'BR', 'BRA', '76', 'BA', 'Bahia', '');
INSERT INTO static_country_zones VALUES ('495', '0', 'BR', 'BRA', '76', 'CE', 'Ceará', '');
INSERT INTO static_country_zones VALUES ('496', '0', 'BR', 'BRA', '76', 'DF', 'Distrito Federal', '');
INSERT INTO static_country_zones VALUES ('497', '0', 'BR', 'BRA', '76', 'ES', 'Espírito Santo', '');
INSERT INTO static_country_zones VALUES ('498', '0', 'BR', 'BRA', '76', 'GO', 'Goiás', '');
INSERT INTO static_country_zones VALUES ('499', '0', 'BR', 'BRA', '76', 'MA', 'Maranhão', '');
INSERT INTO static_country_zones VALUES ('500', '0', 'BR', 'BRA', '76', 'MG', 'Minas Gerais', '');
INSERT INTO static_country_zones VALUES ('501', '0', 'BR', 'BRA', '76', 'MS', 'Mato Grosso do Sul', '');
INSERT INTO static_country_zones VALUES ('502', '0', 'BR', 'BRA', '76', 'MT', 'Mato Grosso', '');
INSERT INTO static_country_zones VALUES ('503', '0', 'BR', 'BRA', '76', 'PA', 'Pará', '');
INSERT INTO static_country_zones VALUES ('504', '0', 'BR', 'BRA', '76', 'PB', 'Paraíba', '');
INSERT INTO static_country_zones VALUES ('505', '0', 'BR', 'BRA', '76', 'PE', 'Pernambuco', '');
INSERT INTO static_country_zones VALUES ('506', '0', 'BR', 'BRA', '76', 'PI', 'Piauí', '');
INSERT INTO static_country_zones VALUES ('507', '0', 'BR', 'BRA', '76', 'PR', 'Paraná', '');
INSERT INTO static_country_zones VALUES ('508', '0', 'BR', 'BRA', '76', 'RJ', 'Rio de Janeiro', '');
INSERT INTO static_country_zones VALUES ('509', '0', 'BR', 'BRA', '76', 'RN', 'Rio Grande do Norte', '');
INSERT INTO static_country_zones VALUES ('510', '0', 'BR', 'BRA', '76', 'RO', 'Rondônia', '');
INSERT INTO static_country_zones VALUES ('511', '0', 'BR', 'BRA', '76', 'RR', 'Roraima', '');
INSERT INTO static_country_zones VALUES ('512', '0', 'BR', 'BRA', '76', 'RS', 'Rio Grande do Sul', '');
INSERT INTO static_country_zones VALUES ('513', '0', 'BR', 'BRA', '76', 'SC', 'Santa Catarina', '');
INSERT INTO static_country_zones VALUES ('514', '0', 'BR', 'BRA', '76', 'SE', 'Sergipe', '');
INSERT INTO static_country_zones VALUES ('515', '0', 'BR', 'BRA', '76', 'SP', 'São Paulo', '');
INSERT INTO static_country_zones VALUES ('516', '0', 'BR', 'BRA', '76', 'TO', 'Tocantins', '');
INSERT INTO static_country_zones VALUES ('530', '0', 'NL', 'NLD', '528', 'DR', 'Drenthe', '');
INSERT INTO static_country_zones VALUES ('531', '0', 'NL', 'NLD', '528', 'FL', 'Flevoland', '');
INSERT INTO static_country_zones VALUES ('532', '0', 'NL', 'NLD', '528', 'FR', 'Friesland', '');
INSERT INTO static_country_zones VALUES ('533', '0', 'NL', 'NLD', '528', 'GE', 'Gelderland', '');
INSERT INTO static_country_zones VALUES ('534', '0', 'NL', 'NLD', '528', 'GR', 'Groningen', '');
INSERT INTO static_country_zones VALUES ('535', '0', 'NL', 'NLD', '528', 'LI', 'Limburg', '');
INSERT INTO static_country_zones VALUES ('536', '0', 'NL', 'NLD', '528', 'NB', 'Noord-Brabant', '');
INSERT INTO static_country_zones VALUES ('537', '0', 'NL', 'NLD', '528', 'NH', 'Noord-Holland', '');
INSERT INTO static_country_zones VALUES ('538', '0', 'NL', 'NLD', '528', 'OV', 'Overijssel', '');
INSERT INTO static_country_zones VALUES ('539', '0', 'NL', 'NLD', '528', 'UT', 'Utrecht', '');
INSERT INTO static_country_zones VALUES ('540', '0', 'NL', 'NLD', '528', 'ZH', 'Zuid-Holland', '');
INSERT INTO static_country_zones VALUES ('541', '0', 'NL', 'NLD', '528', 'ZE', 'Zeeland', '');
INSERT INTO static_country_zones VALUES ('542', '0', 'FR', 'FRA', '250', 'A', 'Alsace', '');
INSERT INTO static_country_zones VALUES ('543', '0', 'FR', 'FRA', '250', 'B', 'Aquitaine', '');
INSERT INTO static_country_zones VALUES ('544', '0', 'FR', 'FRA', '250', 'C', 'Auvergne', '');
INSERT INTO static_country_zones VALUES ('545', '0', 'FR', 'FRA', '250', 'D', 'Bourgogne', '');
INSERT INTO static_country_zones VALUES ('546', '0', 'FR', 'FRA', '250', 'E', 'Bretagne', '');
INSERT INTO static_country_zones VALUES ('547', '0', 'FR', 'FRA', '250', 'F', 'Centre', '');
INSERT INTO static_country_zones VALUES ('548', '0', 'FR', 'FRA', '250', 'G', 'Champagne-Ardenne', '');
INSERT INTO static_country_zones VALUES ('549', '0', 'FR', 'FRA', '250', 'H', 'Corse', '');
INSERT INTO static_country_zones VALUES ('550', '0', 'FR', 'FRA', '250', 'I', 'Franche-Comté', '');
INSERT INTO static_country_zones VALUES ('551', '0', 'FR', 'FRA', '250', 'J', 'Île-de-France', '');
INSERT INTO static_country_zones VALUES ('552', '0', 'FR', 'FRA', '250', 'K', 'Languedoc-Roussillon', '');
INSERT INTO static_country_zones VALUES ('553', '0', 'FR', 'FRA', '250', 'L', 'Limousin', '');
INSERT INTO static_country_zones VALUES ('554', '0', 'FR', 'FRA', '250', 'M', 'Lorraine', '');
INSERT INTO static_country_zones VALUES ('555', '0', 'FR', 'FRA', '250', 'N', 'Midi-Pyrénées', '');
INSERT INTO static_country_zones VALUES ('556', '0', 'FR', 'FRA', '250', 'O', 'Nord-Pas-de-Calais', '');
INSERT INTO static_country_zones VALUES ('557', '0', 'FR', 'FRA', '250', 'P', 'Basse-Normandie', '');
INSERT INTO static_country_zones VALUES ('558', '0', 'FR', 'FRA', '250', 'Q', 'Haute-Normandie', '');
INSERT INTO static_country_zones VALUES ('559', '0', 'FR', 'FRA', '250', 'R', 'Pays de la Loire', '');
INSERT INTO static_country_zones VALUES ('560', '0', 'FR', 'FRA', '250', 'S', 'Picardie', '');
INSERT INTO static_country_zones VALUES ('561', '0', 'FR', 'FRA', '250', 'T', 'Poitou-Charentes', '');
INSERT INTO static_country_zones VALUES ('562', '0', 'FR', 'FRA', '250', 'U', 'Provence-Alpes-Côte d\'Azur', '');
INSERT INTO static_country_zones VALUES ('563', '0', 'FR', 'FRA', '250', 'V', 'Rhône-Alpes', '');
INSERT INTO static_country_zones VALUES ('564', '0', 'FR', 'FRA', '250', '01', 'Ain', '');
INSERT INTO static_country_zones VALUES ('565', '0', 'FR', 'FRA', '250', '02', 'Aisne', '');
INSERT INTO static_country_zones VALUES ('566', '0', 'FR', 'FRA', '250', '03', 'Allier', '');
INSERT INTO static_country_zones VALUES ('567', '0', 'FR', 'FRA', '250', '04', 'Alpes-de-Haute-Provence', '');
INSERT INTO static_country_zones VALUES ('568', '0', 'FR', 'FRA', '250', '05', 'Hautes-Alpes', '');
INSERT INTO static_country_zones VALUES ('569', '0', 'FR', 'FRA', '250', '06', 'Alpes-Maritimes', '');
INSERT INTO static_country_zones VALUES ('570', '0', 'FR', 'FRA', '250', '07', 'Ardèche', '');
INSERT INTO static_country_zones VALUES ('571', '0', 'FR', 'FRA', '250', '08', 'Ardennes', '');
INSERT INTO static_country_zones VALUES ('572', '0', 'FR', 'FRA', '250', '09', 'Ariège', '');
INSERT INTO static_country_zones VALUES ('573', '0', 'FR', 'FRA', '250', '10', 'Aube', '');
INSERT INTO static_country_zones VALUES ('574', '0', 'FR', 'FRA', '250', '11', 'Aude', '');
INSERT INTO static_country_zones VALUES ('575', '0', 'FR', 'FRA', '250', '12', 'Aveyron', '');
INSERT INTO static_country_zones VALUES ('576', '0', 'FR', 'FRA', '250', '13', 'Bouches-du-Rhône', '');
INSERT INTO static_country_zones VALUES ('577', '0', 'FR', 'FRA', '250', '14', 'Calvados', '');
INSERT INTO static_country_zones VALUES ('578', '0', 'FR', 'FRA', '250', '15', 'Cantal', '');
INSERT INTO static_country_zones VALUES ('579', '0', 'FR', 'FRA', '250', '16', 'Charente', '');
INSERT INTO static_country_zones VALUES ('580', '0', 'FR', 'FRA', '250', '17', 'Charente-Maritime', '');
INSERT INTO static_country_zones VALUES ('581', '0', 'FR', 'FRA', '250', '18', 'Cher', '');
INSERT INTO static_country_zones VALUES ('582', '0', 'FR', 'FRA', '250', '19', 'Corrèze', '');
INSERT INTO static_country_zones VALUES ('583', '0', 'FR', 'FRA', '250', '2A', 'Corse-du-Sud', '');
INSERT INTO static_country_zones VALUES ('584', '0', 'FR', 'FRA', '250', '2B', 'Haute-Corse', '');
INSERT INTO static_country_zones VALUES ('585', '0', 'FR', 'FRA', '250', '21', 'Côte-d\'Or', '');
INSERT INTO static_country_zones VALUES ('586', '0', 'FR', 'FRA', '250', '22', 'Côtes-d\'Armor', '');
INSERT INTO static_country_zones VALUES ('587', '0', 'FR', 'FRA', '250', '23', 'Creuse', '');
INSERT INTO static_country_zones VALUES ('588', '0', 'FR', 'FRA', '250', '24', 'Dordogne', '');
INSERT INTO static_country_zones VALUES ('589', '0', 'FR', 'FRA', '250', '25', 'Doubs', '');
INSERT INTO static_country_zones VALUES ('590', '0', 'FR', 'FRA', '250', '26', 'Drôme', '');
INSERT INTO static_country_zones VALUES ('591', '0', 'FR', 'FRA', '250', '27', 'Eure', '');
INSERT INTO static_country_zones VALUES ('592', '0', 'FR', 'FRA', '250', '28', 'Eure-et-Loir', '');
INSERT INTO static_country_zones VALUES ('593', '0', 'FR', 'FRA', '250', '29', 'Finistère', '');
INSERT INTO static_country_zones VALUES ('594', '0', 'FR', 'FRA', '250', '30', 'Gard', '');
INSERT INTO static_country_zones VALUES ('595', '0', 'FR', 'FRA', '250', '31', 'Haute-Garonne', '');
INSERT INTO static_country_zones VALUES ('596', '0', 'FR', 'FRA', '250', '32', 'Gers', '');
INSERT INTO static_country_zones VALUES ('597', '0', 'FR', 'FRA', '250', '33', 'Gironde', '');
INSERT INTO static_country_zones VALUES ('598', '0', 'FR', 'FRA', '250', '34', 'Hérault', '');
INSERT INTO static_country_zones VALUES ('599', '0', 'FR', 'FRA', '250', '35', 'Ille-et-Vilaine', '');
INSERT INTO static_country_zones VALUES ('600', '0', 'FR', 'FRA', '250', '36', 'Indre', '');
INSERT INTO static_country_zones VALUES ('601', '0', 'FR', 'FRA', '250', '37', 'Indre-et-Loire', '');
INSERT INTO static_country_zones VALUES ('602', '0', 'FR', 'FRA', '250', '38', 'Isère', '');
INSERT INTO static_country_zones VALUES ('603', '0', 'FR', 'FRA', '250', '39', 'Jura', '');
INSERT INTO static_country_zones VALUES ('604', '0', 'FR', 'FRA', '250', '40', 'Landes', '');
INSERT INTO static_country_zones VALUES ('605', '0', 'FR', 'FRA', '250', '41', 'Loir-et-Cher', '');
INSERT INTO static_country_zones VALUES ('606', '0', 'FR', 'FRA', '250', '42', 'Loire', '');
INSERT INTO static_country_zones VALUES ('607', '0', 'FR', 'FRA', '250', '43', 'Haute-Loire', '');
INSERT INTO static_country_zones VALUES ('608', '0', 'FR', 'FRA', '250', '44', 'Loire-Atlantique', '');
INSERT INTO static_country_zones VALUES ('609', '0', 'FR', 'FRA', '250', '45', 'Loiret', '');
INSERT INTO static_country_zones VALUES ('610', '0', 'FR', 'FRA', '250', '46', 'Lot', '');
INSERT INTO static_country_zones VALUES ('611', '0', 'FR', 'FRA', '250', '47', 'Lot-et-Garonne', '');
INSERT INTO static_country_zones VALUES ('612', '0', 'FR', 'FRA', '250', '48', 'Lozère', '');
INSERT INTO static_country_zones VALUES ('613', '0', 'FR', 'FRA', '250', '49', 'Maine-et-Loire', '');
INSERT INTO static_country_zones VALUES ('614', '0', 'FR', 'FRA', '250', '50', 'Manche', '');
INSERT INTO static_country_zones VALUES ('615', '0', 'FR', 'FRA', '250', '51', 'Marne', '');
INSERT INTO static_country_zones VALUES ('616', '0', 'FR', 'FRA', '250', '52', 'Haute-Marne', '');
INSERT INTO static_country_zones VALUES ('617', '0', 'FR', 'FRA', '250', '53', 'Mayenne', '');
INSERT INTO static_country_zones VALUES ('618', '0', 'FR', 'FRA', '250', '54', 'Meurthe-et-Moselle', '');
INSERT INTO static_country_zones VALUES ('619', '0', 'FR', 'FRA', '250', '55', 'Meuse', '');
INSERT INTO static_country_zones VALUES ('620', '0', 'FR', 'FRA', '250', '56', 'Morbihan', '');
INSERT INTO static_country_zones VALUES ('621', '0', 'FR', 'FRA', '250', '57', 'Moselle', '');
INSERT INTO static_country_zones VALUES ('622', '0', 'FR', 'FRA', '250', '58', 'Nièvre', '');
INSERT INTO static_country_zones VALUES ('623', '0', 'FR', 'FRA', '250', '59', 'Nord', '');
INSERT INTO static_country_zones VALUES ('624', '0', 'FR', 'FRA', '250', '60', 'Oise', '');
INSERT INTO static_country_zones VALUES ('625', '0', 'FR', 'FRA', '250', '61', 'Orne', '');
INSERT INTO static_country_zones VALUES ('626', '0', 'FR', 'FRA', '250', '62', 'Pas-de-Calais', '');
INSERT INTO static_country_zones VALUES ('627', '0', 'FR', 'FRA', '250', '63', 'Puy-de-Dôme', '');
INSERT INTO static_country_zones VALUES ('628', '0', 'FR', 'FRA', '250', '64', 'Pyrénées-Atlantiques', '');
INSERT INTO static_country_zones VALUES ('629', '0', 'FR', 'FRA', '250', '65', 'Hautes-Pyrénées', '');
INSERT INTO static_country_zones VALUES ('630', '0', 'FR', 'FRA', '250', '66', 'Pyrénées-Orientales', '');
INSERT INTO static_country_zones VALUES ('631', '0', 'FR', 'FRA', '250', '67', 'Bas-Rhin', '');
INSERT INTO static_country_zones VALUES ('632', '0', 'FR', 'FRA', '250', '68', 'Haut-Rhin', '');
INSERT INTO static_country_zones VALUES ('633', '0', 'FR', 'FRA', '250', '69', 'Rhône', '');
INSERT INTO static_country_zones VALUES ('634', '0', 'FR', 'FRA', '250', '70', 'Haute-Saône', '');
INSERT INTO static_country_zones VALUES ('635', '0', 'FR', 'FRA', '250', '71', 'Saône-et-Loire', '');
INSERT INTO static_country_zones VALUES ('636', '0', 'FR', 'FRA', '250', '72', 'Sarthe', '');
INSERT INTO static_country_zones VALUES ('637', '0', 'FR', 'FRA', '250', '73', 'Savoie', '');
INSERT INTO static_country_zones VALUES ('638', '0', 'FR', 'FRA', '250', '74', 'Haute-Savoie', '');
INSERT INTO static_country_zones VALUES ('639', '0', 'FR', 'FRA', '250', '75', 'Paris', '');
INSERT INTO static_country_zones VALUES ('640', '0', 'FR', 'FRA', '250', '76', 'Seine-Maritime', '');
INSERT INTO static_country_zones VALUES ('641', '0', 'FR', 'FRA', '250', '77', 'Seine-et-Marne', '');
INSERT INTO static_country_zones VALUES ('642', '0', 'FR', 'FRA', '250', '78', 'Yvelines', '');
INSERT INTO static_country_zones VALUES ('643', '0', 'FR', 'FRA', '250', '79', 'Deux-Sèvres', '');
INSERT INTO static_country_zones VALUES ('644', '0', 'FR', 'FRA', '250', '80', 'Somme', '');
INSERT INTO static_country_zones VALUES ('645', '0', 'FR', 'FRA', '250', '81', 'Tarn', '');
INSERT INTO static_country_zones VALUES ('646', '0', 'FR', 'FRA', '250', '82', 'Tarn-et-Garonne', '');
INSERT INTO static_country_zones VALUES ('647', '0', 'FR', 'FRA', '250', '83', 'Var', '');
INSERT INTO static_country_zones VALUES ('648', '0', 'FR', 'FRA', '250', '84', 'Vaucluse', '');
INSERT INTO static_country_zones VALUES ('649', '0', 'FR', 'FRA', '250', '85', 'Vendée', '');
INSERT INTO static_country_zones VALUES ('650', '0', 'FR', 'FRA', '250', '86', 'Vienne', '');
INSERT INTO static_country_zones VALUES ('651', '0', 'FR', 'FRA', '250', '87', 'Haute-Vienne', '');
INSERT INTO static_country_zones VALUES ('652', '0', 'FR', 'FRA', '250', '88', 'Vosges', '');
INSERT INTO static_country_zones VALUES ('653', '0', 'FR', 'FRA', '250', '89', 'Yonne', '');
INSERT INTO static_country_zones VALUES ('654', '0', 'FR', 'FRA', '250', '90', 'Territoire de Belfort', '');
INSERT INTO static_country_zones VALUES ('655', '0', 'FR', 'FRA', '250', '91', 'Essonne', '');
INSERT INTO static_country_zones VALUES ('656', '0', 'FR', 'FRA', '250', '92', 'Hauts-de-Seine', '');
INSERT INTO static_country_zones VALUES ('657', '0', 'FR', 'FRA', '250', '93', 'Seine-Saint-Denis', '');
INSERT INTO static_country_zones VALUES ('658', '0', 'FR', 'FRA', '250', '94', 'Val-de-Marne', '');
INSERT INTO static_country_zones VALUES ('659', '0', 'FR', 'FRA', '250', '95', 'Val-d\'Oise', '');
INSERT INTO static_country_zones VALUES ('660', '0', 'FR', 'FRA', '250', 'GP', 'Guadeloupe', '');
INSERT INTO static_country_zones VALUES ('661', '0', 'FR', 'FRA', '250', 'GF', 'Guyane française', 'French Guiana');
INSERT INTO static_country_zones VALUES ('662', '0', 'FR', 'FRA', '250', 'MQ', 'Martinique', 'Martinique');
INSERT INTO static_country_zones VALUES ('663', '0', 'FR', 'FRA', '250', 'RE', 'La Réunion', 'Réunion');
INSERT INTO static_country_zones VALUES ('664', '0', 'FR', 'FRA', '250', 'CP', 'Clipperton', '');
INSERT INTO static_country_zones VALUES ('665', '0', 'FR', 'FRA', '250', 'YT', 'Mayotte', '');
INSERT INTO static_country_zones VALUES ('666', '0', 'FR', 'FRA', '250', 'NC', 'Nouvelle-Calédonie', 'New Caledonia');
INSERT INTO static_country_zones VALUES ('667', '0', 'FR', 'FRA', '250', 'PF', 'Polynésie française', 'French Polynesia');
INSERT INTO static_country_zones VALUES ('668', '0', 'FR', 'FRA', '250', 'BL', 'Saint-Barthélemy', 'Saint Barthélemy');
INSERT INTO static_country_zones VALUES ('669', '0', 'FR', 'FRA', '250', 'MF', 'Saint-Martin', 'Saint Martin');
INSERT INTO static_country_zones VALUES ('670', '0', 'FR', 'FRA', '250', 'PM', 'Saint-Pierre-et-Miquelon', 'Saint Pierre and Miquelon');
INSERT INTO static_country_zones VALUES ('671', '0', 'FR', 'FRA', '250', 'TF', 'Terres australes françaises', 'French Southern Territories');
INSERT INTO static_country_zones VALUES ('672', '0', 'FR', 'FRA', '250', 'WF', 'Wallis-et-Futuna', 'Wallis and Futuna');



# TYPO3 Extension Manager dump 1.1
#
#--------------------------------------------------------


#
# Table structure for table "static_currencies"
#
DROP TABLE IF EXISTS static_currencies;
CREATE TABLE static_currencies (
  uid int(11) unsigned NOT NULL auto_increment,
  pid int(11) unsigned default '0',
  cu_iso_3 char(3) default '',
  cu_iso_nr int(11) unsigned default '0',
  cu_name_en varchar(50) default '',
  cu_symbol_left varchar(12) default '',
  cu_symbol_right varchar(12) default '',
  cu_thousands_point char(1) default '',
  cu_decimal_point char(1) default '',
  cu_decimal_digits tinyint(3) unsigned default '0',
  cu_sub_name_en varchar(20) default '',
  cu_sub_divisor int(11) default '1',
  cu_sub_symbol_left varchar(12) default '',
  cu_sub_symbol_right varchar(12) default '',
  PRIMARY KEY (uid),
  UNIQUE uid (uid),
  KEY parent (pid)
);


INSERT INTO static_currencies VALUES ('2', '0', 'AED', '784', 'United Arab Emirates dirham', 'Dhs.', '', '.', ',', '2', 'fils', '100', '', '');
INSERT INTO static_currencies VALUES ('4', '0', 'ALL', '8', 'Albanian Lek', 'L', '', '.', ',', '2', 'qindarka', '100', '', '');
INSERT INTO static_currencies VALUES ('5', '0', 'AMD', '51', 'Armenian Dram', 'Dram', '', '.', ',', '2', 'luma', '100', '', '');
INSERT INTO static_currencies VALUES ('6', '0', 'ANG', '532', 'Netherlands Antillean gulden', 'NAƒ', '', '.', ',', '2', 'cent', '100', '', '');
INSERT INTO static_currencies VALUES ('7', '0', 'AOA', '973', 'Angolan Kwanza', 'Kz', '', '.', ',', '2', 'centavo', '100', '', '');
INSERT INTO static_currencies VALUES ('8', '0', 'ARS', '32', 'Argentine Peso', '$', '', '.', ',', '2', 'centavo', '100', '', '');
INSERT INTO static_currencies VALUES ('9', '0', 'AUD', '36', 'Australian Dollar', '$A', '', '.', ',', '2', 'cent', '100', '', '');
INSERT INTO static_currencies VALUES ('10', '0', 'AWG', '533', 'Aruban Guilder', 'Af.', '', '.', ',', '2', 'cent', '100', '', '');
INSERT INTO static_currencies VALUES ('11', '0', 'AZN', '944', 'Azerbaijani Manat', '', '', '.', ',', '2', 'qəpik', '100', '', '');
INSERT INTO static_currencies VALUES ('12', '0', 'BAM', '977', 'Bosnia-Herzegovina Convertible Mark', 'KM', '', '.', ',', '2', 'feninga', '100', '', '');
INSERT INTO static_currencies VALUES ('13', '0', 'BBD', '52', 'Barbados Dollar', 'Bds$', '', '.', ',', '2', 'cent', '100', '', '');
INSERT INTO static_currencies VALUES ('14', '0', 'BDT', '50', 'Bangladeshi taka', 'Tk', '', '.', ',', '2', 'paisa', '100', '', '');
INSERT INTO static_currencies VALUES ('16', '0', 'BGN', '975', 'Bulgarian Lev', 'lv', '', '.', ',', '2', 'stotinka', '100', '', '');
INSERT INTO static_currencies VALUES ('17', '0', 'BHD', '48', 'Bahraini Dinar', 'BD', '', '.', ',', '3', 'fils', '1000', '', '');
INSERT INTO static_currencies VALUES ('18', '0', 'BIF', '108', 'Burundi Franc', 'FBu', '', '.', '', '2', 'centime', '100', '', '');
INSERT INTO static_currencies VALUES ('19', '0', 'BMD', '60', 'Bermuda Dollar', 'BM$', '', '.', ',', '2', 'cent', '100', '', '');
INSERT INTO static_currencies VALUES ('20', '0', 'BND', '96', 'Brunei Dollar', 'B$', '', '.', ',', '2', 'sen', '100', '', '');
INSERT INTO static_currencies VALUES ('21', '0', 'BOB', '68', 'Boliviano', 'Bs', '', '.', ',', '2', 'centavo', '100', '', '');
INSERT INTO static_currencies VALUES ('23', '0', 'BRL', '986', 'Brazilian Real', 'R$', '', '.', ',', '2', 'centavo', '100', '', '');
INSERT INTO static_currencies VALUES ('24', '0', 'BSD', '44', 'Bahamian Dollar', '$', '', '.', ',', '2', 'cent', '100', '', '');
INSERT INTO static_currencies VALUES ('25', '0', 'BTN', '64', 'Bhutanese Ngultrum', 'Nu', '', '.', ',', '2', 'chetrum', '100', '', '');
INSERT INTO static_currencies VALUES ('26', '0', 'BWP', '72', 'Botswana pula', 'P', '', '.', ',', '2', 'thebe', '100', '', '');
INSERT INTO static_currencies VALUES ('27', '0', 'BYR', '974', 'Belarussian Ruble', 'Br', '', '.', ',', '2', 'kapiejka', '100', '', '');
INSERT INTO static_currencies VALUES ('28', '0', 'BZD', '84', 'Belize Dollar', 'BZ', '', '.', ',', '2', 'cent', '100', '', '');
INSERT INTO static_currencies VALUES ('29', '0', 'CAD', '124', 'Canadian Dollar', '$', '', '.', ',', '2', 'cent', '100', '', '¢');
INSERT INTO static_currencies VALUES ('30', '0', 'CDF', '976', 'Congolese franc', 'FC', '', '.', ',', '2', 'centime', '100', '', '');
INSERT INTO static_currencies VALUES ('31', '0', 'CHF', '756', 'Swiss franc', 'SFr.', '', '\'', '.', '2', 'centime', '100', '', '');
INSERT INTO static_currencies VALUES ('33', '0', 'CLP', '152', 'Chilean Peso', '$', '', '.', '', '0', '', '1', '', '');
INSERT INTO static_currencies VALUES ('34', '0', 'CNY', '156', 'Chinese Yuan Renminbi', 'Ұ', '', '.', ',', '2', 'fen', '100', '', '');
INSERT INTO static_currencies VALUES ('35', '0', 'COP', '170', 'Colombian Peso', '$', '', '.', ',', '2', 'centavo', '100', '', '');
INSERT INTO static_currencies VALUES ('36', '0', 'CRC', '188', 'Costa Rican colón', '₡', '', '.', ',', '2', 'centimo', '100', '', '');
INSERT INTO static_currencies VALUES ('37', '0', 'CUP', '192', 'Cuban peso', 'Cub$', '', '.', ',', '2', 'centavo', '100', '', '');
INSERT INTO static_currencies VALUES ('38', '0', 'CVE', '132', 'Cape Verde Escudo', 'CVEsc.', '', '.', ',', '2', 'centavo', '100', '', '');
INSERT INTO static_currencies VALUES ('39', '0', 'CYP', '196', 'Cypriot pound', 'C£', '', '.', ',', '2', 'cent', '100', '', '');
INSERT INTO static_currencies VALUES ('40', '0', 'CZK', '203', 'Czech koruna', '', 'Kč', '.', ',', '2', 'haléř', '100', '', '');
INSERT INTO static_currencies VALUES ('41', '0', 'DJF', '262', 'Djibouti franc', 'FD', '', '.', '', '0', '', '1', '', '');
INSERT INTO static_currencies VALUES ('42', '0', 'DKK', '208', 'Danish krone', 'kr.', '', '.', ',', '2', 'Øre', '100', '', '');
INSERT INTO static_currencies VALUES ('43', '0', 'DOP', '214', 'Dominican peso', 'RD$', '', '.', ',', '2', 'centavo', '100', '', '');
INSERT INTO static_currencies VALUES ('44', '0', 'DZD', '12', 'Algerian Dinar', 'DA', '', '.', ',', '2', 'centime', '100', '', '');
INSERT INTO static_currencies VALUES ('45', '0', 'EEK', '233', 'Estonian kroon', '', 'ekr', '.', ',', '2', 'sent', '100', '', '');
INSERT INTO static_currencies VALUES ('46', '0', 'EGP', '818', 'Egyptian pound', 'LE', '', '.', ',', '2', 'piastre', '100', '', '');
INSERT INTO static_currencies VALUES ('47', '0', 'ERN', '232', 'Eritrean nakfa', 'Nfa', '', '.', ',', '2', 'cent', '100', '', '');
INSERT INTO static_currencies VALUES ('48', '0', 'ETB', '230', 'Ethiopian birr', 'Br', '', '.', ',', '2', 'santim', '100', '', '');
INSERT INTO static_currencies VALUES ('49', '0', 'EUR', '978', 'Euro', '€', '', '.', ',', '2', 'cent', '100', '¢', '');
INSERT INTO static_currencies VALUES ('50', '0', 'FJD', '242', 'Fijian dollar', 'FJ$', '', '.', ',', '2', 'cent', '100', '', '');
INSERT INTO static_currencies VALUES ('51', '0', 'FKP', '238', 'Falkland Islands pound', 'Fl£', '', '.', ',', '2', 'penny', '100', '', '');
INSERT INTO static_currencies VALUES ('52', '0', 'GBP', '826', 'Pound sterling', '£', '', ',', '.', '2', 'penny', '100', '', 'p');
INSERT INTO static_currencies VALUES ('53', '0', 'GEL', '981', 'Georgian lari', '', 'lari', '.', ',', '2', 'tetri', '100', '', '');
INSERT INTO static_currencies VALUES ('54', '0', 'GHC', '288', 'Ghanaian cedi', '', '', '.', ',', '2', 'pesewa', '100', '', '');
INSERT INTO static_currencies VALUES ('55', '0', 'GIP', '292', 'Gibraltar pound', '£', '', '.', ',', '2', 'penny', '100', '', '');
INSERT INTO static_currencies VALUES ('56', '0', 'GMD', '270', 'Gambian dalasi', 'D', '', '.', ',', '2', 'butut', '100', '', '');
INSERT INTO static_currencies VALUES ('57', '0', 'GNF', '324', 'Guinea Franc', 'GF', '', '.', '', '0', '', '1', '', '');
INSERT INTO static_currencies VALUES ('58', '0', 'GTQ', '320', 'Guatemalan quetzal', 'Q.', '', '.', ',', '2', 'centavo', '100', '', '');
INSERT INTO static_currencies VALUES ('59', '0', 'GWP', '624', 'Guinea-Bissau Peso', '', '', '.', ',', '2', '', '100', '', '');
INSERT INTO static_currencies VALUES ('60', '0', 'GYD', '328', 'Guyana Dollar', 'G$', '', '.', ',', '2', 'Cent', '100', '', '');
INSERT INTO static_currencies VALUES ('61', '0', 'HKD', '344', 'Hong Kong dollar', 'HK$', '', '.', ',', '2', 'cent', '100', '', '');
INSERT INTO static_currencies VALUES ('62', '0', 'HNL', '340', 'Honduran lempira', 'L', '', '.', ',', '2', 'centavo', '100', '', '');
INSERT INTO static_currencies VALUES ('63', '0', 'HRK', '191', 'Croatian kuna', 'kn', '', '.', ',', '2', 'lipa', '100', '', '');
INSERT INTO static_currencies VALUES ('64', '0', 'HTG', '332', 'Haitian gourde', 'Gde.', '', '.', ',', '2', 'centime', '100', '', '');
INSERT INTO static_currencies VALUES ('65', '0', 'HUF', '348', 'Hungarian forint', '', 'Ft', '.', ',', '2', 'fillér', '100', '', '');
INSERT INTO static_currencies VALUES ('66', '0', 'IDR', '360', 'Indonesian rupiah', 'Rp.', '', '.', ',', '2', 'sen', '100', '', '');
INSERT INTO static_currencies VALUES ('67', '0', 'ILS', '376', 'New Israeli Sheqel', '', 'NIS', '.', ',', '2', 'agora', '100', '', '');
INSERT INTO static_currencies VALUES ('68', '0', 'INR', '356', 'Indian rupee', 'Rs', '', '.', ',', '2', 'paisha', '100', '', '');
INSERT INTO static_currencies VALUES ('69', '0', 'IQD', '368', 'Iraqi dinar', 'ID', '', '.', ',', '3', 'fils', '1000', '', '');
INSERT INTO static_currencies VALUES ('70', '0', 'IRR', '364', 'Iranian rial', 'Rls', '', '.', ',', '2', 'dinar', '100', '', '');
INSERT INTO static_currencies VALUES ('71', '0', 'ISK', '352', 'Icelandic króna', '', 'ikr', '.', ',', '2', 'aurar', '100', '', '');
INSERT INTO static_currencies VALUES ('72', '0', 'JMD', '388', 'Jamaican dollar', 'J$', '', '.', ',', '2', 'cent', '100', '', '');
INSERT INTO static_currencies VALUES ('73', '0', 'JOD', '400', 'Jordanian dinar', 'JD', '', '.', ',', '2', 'piastre', '100', '', '');
INSERT INTO static_currencies VALUES ('74', '0', 'JPY', '392', 'Japanese yen', '¥', '', '.', '', '2', 'sen', '100', '', '');
INSERT INTO static_currencies VALUES ('75', '0', 'KES', '404', 'Kenyan shilling', 'Kshs.', '', '.', ',', '2', 'cent', '100', '', '');
INSERT INTO static_currencies VALUES ('76', '0', 'KGS', '417', 'Kyrgyzstani som', 'K.S.', '', '.', ',', '2', 'tyiyn', '100', '', '');
INSERT INTO static_currencies VALUES ('77', '0', 'KHR', '116', 'Cambodian riel', 'CR', '', '.', ',', '2', 'sen', '100', '', '');
INSERT INTO static_currencies VALUES ('78', '0', 'KMF', '174', 'Comorian Franc', 'CF', '', '.', '', '0', '', '1', '', '');
INSERT INTO static_currencies VALUES ('79', '0', 'KPW', '408', 'North Korean won', '₩n', '', '.', ',', '2', 'chon', '100', '', '');
INSERT INTO static_currencies VALUES ('80', '0', 'KRW', '410', 'South Corean won', '￦', '', '.', '', '2', 'jeon', '100', '', '');
INSERT INTO static_currencies VALUES ('81', '0', 'KWD', '414', 'Kuwaiti dinar', 'KD', '', '.', ',', '3', 'fils', '1000', '', '');
INSERT INTO static_currencies VALUES ('82', '0', 'KYD', '136', 'Cayman Islands Dollar', '$', '', '.', ',', '2', 'cent', '100', '', '');
INSERT INTO static_currencies VALUES ('83', '0', 'KZT', '398', 'Kazakhstani tenge', 'T', '', '.', ',', '2', 'tiyin', '100', '', '');
INSERT INTO static_currencies VALUES ('84', '0', 'LAK', '418', 'Lao kip', '₭', '', '.', ',', '2', 'at', '100', '', '');
INSERT INTO static_currencies VALUES ('85', '0', 'LBP', '422', 'Lebanese pound', '', 'LL', '.', ',', '2', 'piastre', '100', '', '');
INSERT INTO static_currencies VALUES ('86', '0', 'LKR', '144', 'Sri Lankan rupee', 'Re', '', '.', ',', '2', 'cent', '100', '', '');
INSERT INTO static_currencies VALUES ('87', '0', 'LRD', '430', 'Liberian dollar', 'Lib$', '', '.', ',', '2', 'cent', '100', '', '');
INSERT INTO static_currencies VALUES ('88', '0', 'LSL', '426', 'Lesotho loti', 'M', '', '.', ',', '2', 'sente', '100', '', '');
INSERT INTO static_currencies VALUES ('89', '0', 'LTL', '440', 'Lithuanian litas', '', 'Lt', '.', ',', '2', 'centas', '100', '', '');
INSERT INTO static_currencies VALUES ('90', '0', 'LVL', '428', 'Latvian lats', 'Ls', '', '.', ',', '2', 'santim', '100', '', '');
INSERT INTO static_currencies VALUES ('91', '0', 'LYD', '434', 'Libyan dinar', 'LD.', '', '.', ',', '3', 'dirham', '1000', '', '');
INSERT INTO static_currencies VALUES ('92', '0', 'MAD', '504', 'Moroccan dirham', 'Dh', '', '.', ',', '2', 'centime', '100', '', '');
INSERT INTO static_currencies VALUES ('93', '0', 'MDL', '498', 'Moldovan leu', '', '', '.', ',', '2', 'ban', '100', '', '');
INSERT INTO static_currencies VALUES ('95', '0', 'MKD', '807', 'Macedonian denar', 'Den', '', '.', ',', '2', 'deni', '100', '', '');
INSERT INTO static_currencies VALUES ('96', '0', 'MMK', '104', 'Myanmar kyat', 'K', '', '.', ',', '2', 'pya', '100', '', '');
INSERT INTO static_currencies VALUES ('97', '0', 'MNT', '496', 'Mongolian tugrug', '₮', '', '.', ',', '2', 'mongo', '100', '', '');
INSERT INTO static_currencies VALUES ('98', '0', 'MOP', '446', 'Macanese pataca', 'Pat.', '', '.', ',', '2', 'avo', '100', '', '');
INSERT INTO static_currencies VALUES ('99', '0', 'MRO', '478', 'Mauritanian ouguiya', 'UM', '', '.', ',', '2', 'khoum', '100', '', '');
INSERT INTO static_currencies VALUES ('100', '0', 'MTL', '470', 'Maltese lira', 'Lm', '', '.', ',', '2', 'cent', '100', '', '');
INSERT INTO static_currencies VALUES ('101', '0', 'MUR', '480', 'Mauritian rupee', 'Rs', '', '.', ',', '2', 'cent', '100', '', '');
INSERT INTO static_currencies VALUES ('102', '0', 'MVR', '462', 'Maldivian rufiyaa', 'Rf', '', '.', ',', '2', 'laari', '100', '', '');
INSERT INTO static_currencies VALUES ('103', '0', 'MWK', '454', 'Malawian kwacha', 'MK', '', '.', ',', '2', 'tambala', '100', '', '');
INSERT INTO static_currencies VALUES ('104', '0', 'MXN', '484', 'Mexican peso', '$', '', '.', ',', '2', 'centavo', '100', '', '');
INSERT INTO static_currencies VALUES ('106', '0', 'MYR', '458', 'Malaysian ringgit', 'RM', '', '.', ',', '2', 'sen', '100', '', '');
INSERT INTO static_currencies VALUES ('107', '0', 'MZM', '508', 'Mozambican metical', '', 'Mt', '.', ',', '2', 'centavo', '100', '', '');
INSERT INTO static_currencies VALUES ('108', '0', 'NAD', '516', 'Namibian dollar', 'N$', '', '.', ',', '2', 'cent', '100', '', '');
INSERT INTO static_currencies VALUES ('109', '0', 'NGN', '566', 'Nigerian naira', '₦', '', '.', ',', '2', 'kobo', '100', '', '');
INSERT INTO static_currencies VALUES ('110', '0', 'NIO', '558', 'Nicaraguan córdoba', 'C$', '', '.', ',', '2', 'centavo', '100', '', '');
INSERT INTO static_currencies VALUES ('111', '0', 'NOK', '578', 'Norvegian krone', 'kr', '', '.', ',', '2', 'øre', '100', '', '');
INSERT INTO static_currencies VALUES ('112', '0', 'NPR', '524', 'Nepalese rupee', 'Rs.', '', '.', ',', '2', 'paisa', '100', '', '');
INSERT INTO static_currencies VALUES ('113', '0', 'NZD', '554', 'New Zealand dollar', '$', '', '.', ',', '2', 'cent', '100', '', '');
INSERT INTO static_currencies VALUES ('114', '0', 'OMR', '512', 'Omani rial', 'OR', '', '.', ',', '3', 'baiza', '1000', '', '');
INSERT INTO static_currencies VALUES ('115', '0', 'PAB', '590', 'Panamanian balboa', 'B', '', '.', ',', '2', 'centésimo', '100', '', '');
INSERT INTO static_currencies VALUES ('116', '0', 'PEN', '604', 'Peruvian nuevo sol', 'Sl.', '', '.', ',', '2', 'centimo', '100', '', '');
INSERT INTO static_currencies VALUES ('117', '0', 'PGK', '598', 'Papua New Guinean kina', 'K', '', '.', ',', '2', 'toea', '100', '', '');
INSERT INTO static_currencies VALUES ('118', '0', 'PHP', '608', 'Philippine peso', 'P', '', '.', ',', '2', 'centavo', '100', '', '');
INSERT INTO static_currencies VALUES ('119', '0', 'PKR', '586', 'Pakistani rupee', 'Rs.', '', '.', ',', '2', 'paisa', '100', '', '');
INSERT INTO static_currencies VALUES ('120', '0', 'PLN', '985', 'Polish złoty', '', 'zł', '.', ',', '2', 'grosz', '100', '', '');
INSERT INTO static_currencies VALUES ('121', '0', 'PYG', '600', 'Paraguayan guaraní', 'G', '', '.', '', '2', 'centimo', '100', '', '');
INSERT INTO static_currencies VALUES ('122', '0', 'QAR', '634', 'Qatari riyal', 'QR', '', '.', ',', '2', 'dirham', '100', '', '');
INSERT INTO static_currencies VALUES ('123', '0', 'ROL', '642', 'Romanian leu', '', 'l', '.', ',', '2', 'ban', '100', '', '');
INSERT INTO static_currencies VALUES ('124', '0', 'RUB', '643', 'Russian ruble', '', 'R', '.', ',', '2', 'kopek', '100', '', '');
INSERT INTO static_currencies VALUES ('126', '0', 'RWF', '646', 'Rwandan franc', 'frw', '', '.', '', '0', 'centime', '1', '', '');
INSERT INTO static_currencies VALUES ('127', '0', 'SAR', '682', 'Saudi riyal', 'SR', '', '.', ',', '2', 'hallalah', '100', '', '');
INSERT INTO static_currencies VALUES ('128', '0', 'SBD', '90', 'Solomon Islands dollar', 'SI$', '', '.', ',', '2', 'cent', '100', '', '');
INSERT INTO static_currencies VALUES ('129', '0', 'SCR', '690', 'Seychelles rupee', 'SR', '', '.', ',', '2', 'cent', '100', '', '');
INSERT INTO static_currencies VALUES ('130', '0', 'SDD', '736', 'Sudanese dinar', 'sD', '', '.', ',', '0', '', '1', '', '');
INSERT INTO static_currencies VALUES ('131', '0', 'SEK', '752', 'Swedish krona', '', 'kr', '.', ',', '2', 'öre', '100', '', '');
INSERT INTO static_currencies VALUES ('132', '0', 'SGD', '702', 'Singapore dollar', '$', '', '.', ',', '2', 'cent', '100', '', '');
INSERT INTO static_currencies VALUES ('133', '0', 'SHP', '654', 'Saint Helena pound', '£', '', '.', ',', '2', 'penny', '100', '', '');
INSERT INTO static_currencies VALUES ('134', '0', 'SIT', '705', 'Slovenian tolar', 'SIT', '', '.', ',', '2', 'stotin', '100', '', '');
INSERT INTO static_currencies VALUES ('135', '0', 'SKK', '703', 'Slovak koruna', '', 'Sk', '.', ',', '2', 'halier', '100', '', 'h');
INSERT INTO static_currencies VALUES ('136', '0', 'SLL', '694', 'Sierra Leonean leone', 'Le', '', '.', ',', '2', 'cent', '100', '', '');
INSERT INTO static_currencies VALUES ('137', '0', 'SOS', '706', 'Somali shilling', 'So.', '', '.', ',', '2', 'centesimo', '100', '', '');
INSERT INTO static_currencies VALUES ('139', '0', 'STD', '678', 'São Tomé and Príncipe dobra', 'Db', '', '.', ',', '2', 'cêntimo', '100', '', '');
INSERT INTO static_currencies VALUES ('140', '0', 'SVC', '222', 'Salvadoran colón', '₡', '', '.', ',', '2', 'centavo', '100', '', '');
INSERT INTO static_currencies VALUES ('141', '0', 'SYP', '760', 'Syrian pound', '£S', '', '.', ',', '2', 'piastre', '100', '', '');
INSERT INTO static_currencies VALUES ('142', '0', 'SZL', '748', 'Swazi lilangeni', '', '', '.', ',', '2', 'cent', '100', '', '');
INSERT INTO static_currencies VALUES ('143', '0', 'THB', '764', 'Baht', '', 'Bt', '.', ',', '2', 'satang', '100', '', '');
INSERT INTO static_currencies VALUES ('144', '0', 'TJS', '972', 'Tajikistani somoni', '', '', '.', ',', '2', 'diram', '100', '', '');
INSERT INTO static_currencies VALUES ('145', '0', 'TMM', '795', 'Turkmenistani manat', '', '', '.', ',', '2', 'tenge', '100', '', '');
INSERT INTO static_currencies VALUES ('146', '0', 'TND', '788', 'Tunisian dinar', 'TD', '', '.', ',', '3', 'millime', '1000', '', '');
INSERT INTO static_currencies VALUES ('147', '0', 'TOP', '776', 'Tongan pa\'anga', 'T$', '', '.', ',', '2', 'seniti', '100', '', '');
INSERT INTO static_currencies VALUES ('150', '0', 'TTD', '780', 'Trinidad and Tobago dollar', 'TT$', '', '.', ',', '2', 'cent', '100', '', '');
INSERT INTO static_currencies VALUES ('151', '0', 'TWD', '901', 'New Taiwan dollar', 'NT$', '', '.', ',', '2', 'cent', '100', '', '');
INSERT INTO static_currencies VALUES ('152', '0', 'TZS', '834', 'Tanzanian shilling', 'TSh', '', '.', ',', '2', 'cent', '100', '', '');
INSERT INTO static_currencies VALUES ('153', '0', 'UAH', '980', 'Ukrainian hryvnia', 'hrn', '', '.', ',', '2', 'kopiyka', '100', '', '');
INSERT INTO static_currencies VALUES ('154', '0', 'UGX', '800', 'Ugandan shilling', 'USh', '', '.', ',', '2', 'cent', '100', '', '');
INSERT INTO static_currencies VALUES ('155', '0', 'USD', '840', 'US dollar', '$', '', ',', '.', '2', 'cent', '100', '', '¢');
INSERT INTO static_currencies VALUES ('156', '0', 'UYU', '858', 'Uruguayan peso', 'UR$', '', '.', ',', '2', 'centésimo', '100', '', '');
INSERT INTO static_currencies VALUES ('157', '0', 'UZS', '860', 'Uzbekistani som', 'U.S.', '', '.', ',', '2', 'tiyin', '100', '', '');
INSERT INTO static_currencies VALUES ('158', '0', 'VEB', '862', 'Bolivar', 'Bs.', '', '.', ',', '2', 'céntimo', '100', '', '');
INSERT INTO static_currencies VALUES ('159', '0', 'VND', '704', 'Vietnamese đồng', '', '₫', '.', ',', '2', 'xu', '100', '', '');
INSERT INTO static_currencies VALUES ('160', '0', 'VUV', '548', 'Vatu', '', 'VT', '.', '', '0', 'centime', '1', '', '');
INSERT INTO static_currencies VALUES ('161', '0', 'WST', '882', 'Samoan tala', 'WS$', '', '.', ',', '2', 'sene', '100', '', '');
INSERT INTO static_currencies VALUES ('162', '0', 'XAF', '950', 'CFA Franc BEAC', 'CFAF', '', '.', '', '0', '', '1', '', '');
INSERT INTO static_currencies VALUES ('163', '0', 'XCD', '951', 'East Caribbean dollar', 'EC$', '', '.', ',', '2', 'cent', '100', '', '');
INSERT INTO static_currencies VALUES ('164', '0', 'XOF', '952', 'CFA Franc BCEAO', 'CFAF', '', '.', '', '0', '', '1', '', '');
INSERT INTO static_currencies VALUES ('165', '0', 'XPF', '953', 'CFP Franc', 'CFPF', '', '.', '', '0', '', '1', '', '');
INSERT INTO static_currencies VALUES ('166', '0', 'YER', '886', 'Yemeni rial', 'RI', '', '.', ',', '2', 'fils', '100', '', '');
INSERT INTO static_currencies VALUES ('168', '0', 'ZAR', '710', 'South African rand', 'R', '', '.', ',', '2', 'cent', '100', '', '');
INSERT INTO static_currencies VALUES ('169', '0', 'ZMK', '894', 'Zambian kwacha', 'K', '', '.', ',', '2', 'ngwee', '100', '', '');
INSERT INTO static_currencies VALUES ('170', '0', 'ZWD', '716', 'Zimbabwean dollar', '$', '', '.', ',', '2', 'cent', '100', '', '');
INSERT INTO static_currencies VALUES ('171', '0', 'AFN', '971', 'Afghan afghani', 'Af', '', '.', ',', '2', 'pul', '100', '', '');
INSERT INTO static_currencies VALUES ('172', '0', 'CSD', '891', 'Serbian dinar', '', '', '.', ',', '2', 'para', '100', '', '');
INSERT INTO static_currencies VALUES ('173', '0', 'MGA', '969', 'Malagasy ariary', '', '', '.', ',', '1', 'iraimbilanja', '5', '', '');
INSERT INTO static_currencies VALUES ('174', '0', 'SRD', '968', 'Suriname dollar', '$', '', '.', ',', '2', 'cent', '100', '', '');
INSERT INTO static_currencies VALUES ('175', '0', 'TRY', '949', 'Turkish new lira', 'YTL', '', '.', ',', '2', 'new kuruş', '100', '', '');



# TYPO3 Extension Manager dump 1.1
#
#--------------------------------------------------------


#
# Table structure for table "static_languages"
#
DROP TABLE IF EXISTS static_languages;
CREATE TABLE static_languages (
  uid int(11) unsigned NOT NULL auto_increment,
  pid int(11) unsigned default '0',
  lg_iso_2 char(2) default '',
  lg_name_en varchar(50) default '',
  lg_typo3 char(2) default '',
  lg_country_iso_2 char(2) default '',
  lg_collate_locale varchar(5) default '',
  lg_name_local varchar(99) default '',
  lg_sacred tinyint(3) unsigned default '0',
  lg_constructed tinyint(3) unsigned default '0',
  PRIMARY KEY (uid),
  UNIQUE uid (uid),
  KEY parent (pid)
);


INSERT INTO static_languages VALUES ('1', '0', 'AB', 'Abkhazian', '', '', '', 'Аҧсуа бызшәа', '0', '0');
INSERT INTO static_languages VALUES ('2', '0', 'AA', 'Afar', '', '', '', 'Afaraf', '0', '0');
INSERT INTO static_languages VALUES ('3', '0', 'AF', 'Afrikaans', '', '', '', 'Afrikaans', '0', '0');
INSERT INTO static_languages VALUES ('4', '0', 'SQ', 'Albanian', 'sq', '', 'sq', 'Gjuha shqipe', '0', '0');
INSERT INTO static_languages VALUES ('5', '0', 'AM', 'Amharic', '', '', '', 'አማርኛ', '0', '0');
INSERT INTO static_languages VALUES ('6', '0', 'AR', 'Arabic', 'ar', '', 'ar_SA', 'العربية', '0', '0');
INSERT INTO static_languages VALUES ('7', '0', 'HY', 'Armenian', '', '', '', 'Հայերեն', '0', '0');
INSERT INTO static_languages VALUES ('8', '0', 'AS', 'Assamese', '', '', '', 'অসমীয়া', '0', '0');
INSERT INTO static_languages VALUES ('9', '0', 'AY', 'Aymara', '', '', '', 'Aymar aru', '0', '0');
INSERT INTO static_languages VALUES ('10', '0', 'AZ', 'Azerbaijani', '', '', '', 'Azərbaycan dili', '0', '0');
INSERT INTO static_languages VALUES ('11', '0', 'BA', 'Bashkir', '', '', '', 'Башҡорт', '0', '0');
INSERT INTO static_languages VALUES ('12', '0', 'EU', 'Basque', 'eu', '', 'eu_ES', 'Euskara', '0', '0');
INSERT INTO static_languages VALUES ('13', '0', 'BN', 'Bengali', '', '', '', 'বাংলা', '0', '0');
INSERT INTO static_languages VALUES ('14', '0', 'DZ', 'Dzongkha', '', '', '', 'ཇོང་ཁ', '0', '0');
INSERT INTO static_languages VALUES ('15', '0', 'BH', 'Bihari', '', '', '', 'भोजपुरी', '0', '0');
INSERT INTO static_languages VALUES ('16', '0', 'BI', 'Bislama', '', '', '', 'Bislama', '0', '0');
INSERT INTO static_languages VALUES ('17', '0', 'BR', 'Breton', '', '', '', 'Brezhoneg', '0', '0');
INSERT INTO static_languages VALUES ('18', '0', 'BG', 'Bulgarian', 'bg', '', 'bg_BG', 'Български', '0', '0');
INSERT INTO static_languages VALUES ('19', '0', 'MY', 'Burmese', 'my', '', 'my_MM', 'မ္ရန္‌မာစာ', '0', '0');
INSERT INTO static_languages VALUES ('20', '0', 'BE', 'Belarusian', '', '', '', 'Беларуская', '0', '0');
INSERT INTO static_languages VALUES ('21', '0', 'KM', 'Khmer', 'km', '', 'km', 'ភាសាខ្មែរ', '0', '0');
INSERT INTO static_languages VALUES ('22', '0', 'CA', 'Catalan', 'ca', '', 'ca_ES', 'Català', '0', '0');
INSERT INTO static_languages VALUES ('23', '0', 'ZA', 'Zhuang', '', '', '', 'Sawcuengh', '0', '0');
INSERT INTO static_languages VALUES ('24', '0', 'ZH', 'Chinese (Traditional)', 'hk', 'HK', 'zh_HK', '漢語', '0', '0');
INSERT INTO static_languages VALUES ('25', '0', 'CO', 'Corsican', '', '', '', 'Corsu', '0', '0');
INSERT INTO static_languages VALUES ('26', '0', 'HR', 'Croatian', 'hr', '', 'hr_HR', 'Hrvatski', '0', '0');
INSERT INTO static_languages VALUES ('27', '0', 'CS', 'Czech', 'cz', '', 'cs_CZ', 'Čeština', '0', '0');
INSERT INTO static_languages VALUES ('28', '0', 'DA', 'Danish', 'dk', '', 'da_DK', 'Dansk', '0', '0');
INSERT INTO static_languages VALUES ('29', '0', 'NL', 'Dutch', 'nl', '', 'nl_NL', 'Nederlands', '0', '0');
INSERT INTO static_languages VALUES ('30', '0', 'EN', 'English', '', '', 'en_GB', 'English', '0', '0');
INSERT INTO static_languages VALUES ('31', '0', 'EO', 'Esperanto', 'eo', '', '', 'Esperanto', '0', '1');
INSERT INTO static_languages VALUES ('32', '0', 'ET', 'Estonian', 'et', '', 'et_EE', 'Eesti', '0', '0');
INSERT INTO static_languages VALUES ('33', '0', 'FO', 'Faeroese', 'fo', '', 'fo_FO', 'Føroyskt', '0', '0');
INSERT INTO static_languages VALUES ('34', '0', 'FA', 'Persian', 'fa', '', 'fa_IR', 'فارسی', '0', '0');
INSERT INTO static_languages VALUES ('35', '0', 'FJ', 'Fijian', '', '', '', 'Na Vosa Vakaviti', '0', '0');
INSERT INTO static_languages VALUES ('36', '0', 'FI', 'Finnish', 'fi', '', 'fi_FI', 'Suomi', '0', '0');
INSERT INTO static_languages VALUES ('37', '0', 'FR', 'French', 'fr', '', 'fr_FR', 'Français', '0', '0');
INSERT INTO static_languages VALUES ('38', '0', 'FY', 'Frisian', '', '', '', 'Frysk', '0', '0');
INSERT INTO static_languages VALUES ('39', '0', 'GL', 'Galician', 'ga', '', 'gl_ES', 'Galego', '0', '0');
INSERT INTO static_languages VALUES ('40', '0', 'GD', 'Scottish Gaelic', '', '', '', 'Gàidhlig', '0', '0');
INSERT INTO static_languages VALUES ('41', '0', 'GV', 'Manx', '', '', '', 'Gaelg', '0', '0');
INSERT INTO static_languages VALUES ('42', '0', 'KA', 'Georgian', 'ge', '', 'ka', 'ქართული', '0', '0');
INSERT INTO static_languages VALUES ('43', '0', 'DE', 'German', 'de', '', 'de_DE', 'Deutsch', '0', '0');
INSERT INTO static_languages VALUES ('44', '0', 'EL', 'Greek', 'gr', '', 'el_GR', 'Ελληνικά', '0', '0');
INSERT INTO static_languages VALUES ('45', '0', 'KL', 'Greenlandic', 'gl', '', 'kl_DK', 'Kalaallisut', '0', '0');
INSERT INTO static_languages VALUES ('46', '0', 'GN', 'Guaraní', '', '', '', 'Avañe\'ẽ', '0', '0');
INSERT INTO static_languages VALUES ('47', '0', 'GU', 'Gujarati', '', '', '', 'ગુજરાતી', '0', '0');
INSERT INTO static_languages VALUES ('48', '0', 'HA', 'Hausa', '', '', '', 'Hausa', '0', '0');
INSERT INTO static_languages VALUES ('49', '0', 'HE', 'Hebrew', 'he', '', 'he_IL', 'עברית', '0', '0');
INSERT INTO static_languages VALUES ('50', '0', 'HI', 'Hindi', 'hi', '', 'hi_IN', 'हिन्दी', '0', '0');
INSERT INTO static_languages VALUES ('51', '0', 'HU', 'Hungarian', 'hu', '', 'hu_HU', 'Magyar', '0', '0');
INSERT INTO static_languages VALUES ('52', '0', 'IS', 'Icelandic', 'is', '', 'is_IS', 'Íslenska', '0', '0');
INSERT INTO static_languages VALUES ('53', '0', 'ID', 'Indonesian', '', '', '', 'Bahasa Indonesia', '0', '0');
INSERT INTO static_languages VALUES ('54', '0', 'IA', 'Interlingua', '', '', '', 'Interlingua', '0', '1');
INSERT INTO static_languages VALUES ('55', '0', 'IE', 'Interlingue', '', '', '', 'Interlingue', '0', '1');
INSERT INTO static_languages VALUES ('56', '0', 'IU', 'Inuktitut', '', '', '', 'ᐃᓄᒃᑎᑐᑦ', '0', '0');
INSERT INTO static_languages VALUES ('57', '0', 'IK', 'Inupiaq', '', '', '', 'Iñupiak', '0', '0');
INSERT INTO static_languages VALUES ('58', '0', 'GA', 'Irish', '', '', '', 'Gaeilge', '0', '0');
INSERT INTO static_languages VALUES ('59', '0', 'IT', 'Italian', 'it', '', 'it_IT', 'Italiano', '0', '0');
INSERT INTO static_languages VALUES ('60', '0', 'JA', 'Japanese', 'jp', '', 'ja_JP', '日本語', '0', '0');
INSERT INTO static_languages VALUES ('62', '0', 'KN', 'Kannada', '', '', '', 'ಕನ್ನಡ', '0', '0');
INSERT INTO static_languages VALUES ('63', '0', 'KS', 'Kashmiri', '', '', '', 'कॉशुर', '0', '0');
INSERT INTO static_languages VALUES ('64', '0', 'KK', 'Kazakh', '', '', '', 'Қазақ тілі', '0', '0');
INSERT INTO static_languages VALUES ('65', '0', 'RW', 'Kinyarwanda', '', '', '', 'Kinyarwanda', '0', '0');
INSERT INTO static_languages VALUES ('66', '0', 'KY', 'Kirghiz', '', '', '', 'Кыргыз тили', '0', '0');
INSERT INTO static_languages VALUES ('67', '0', 'RN', 'Kirundi', '', '', '', 'kiRundi', '0', '0');
INSERT INTO static_languages VALUES ('68', '0', 'KO', 'Korean', 'kr', '', 'ko_KR', '한국말', '0', '0');
INSERT INTO static_languages VALUES ('69', '0', 'KU', 'Kurdish', '', '', '', 'Kurdî', '0', '0');
INSERT INTO static_languages VALUES ('70', '0', 'LO', 'Lao', '', '', '', 'ພາສາລາວ', '0', '0');
INSERT INTO static_languages VALUES ('71', '0', 'LA', 'Latin', '', '', '', 'Lingua latina', '1', '0');
INSERT INTO static_languages VALUES ('72', '0', 'LV', 'Latvian', 'lv', '', 'lv_LV', 'Latviešu', '0', '0');
INSERT INTO static_languages VALUES ('73', '0', 'LN', 'Lingala', '', '', '', 'Lingála', '0', '0');
INSERT INTO static_languages VALUES ('74', '0', 'LT', 'Lithuanian', 'lt', '', 'lt_LT', 'Lietuvių', '0', '0');
INSERT INTO static_languages VALUES ('75', '0', 'MK', 'Macedonian', '', '', '', 'Македонски', '0', '0');
INSERT INTO static_languages VALUES ('76', '0', 'MG', 'Malagasy', '', '', '', 'Merina', '0', '0');
INSERT INTO static_languages VALUES ('77', '0', 'MS', 'Malay', '', '', '', 'Bahasa Melayu', '0', '0');
INSERT INTO static_languages VALUES ('78', '0', 'ML', 'Malayalam', '', '', '', 'മലയാളം', '0', '0');
INSERT INTO static_languages VALUES ('79', '0', 'MT', 'Maltese', '', '', 'mt_MT', 'Malti', '0', '0');
INSERT INTO static_languages VALUES ('80', '0', 'MI', 'Māori', '', '', '', 'Māori', '0', '0');
INSERT INTO static_languages VALUES ('81', '0', 'MR', 'Marathi', '', '', '', 'मराठी', '0', '0');
INSERT INTO static_languages VALUES ('82', '0', 'MO', 'Moldavian', '', '', '', 'молдовеняскэ', '0', '0');
INSERT INTO static_languages VALUES ('83', '0', 'MN', 'Mongolian', '', '', '', 'Монгол', '0', '0');
INSERT INTO static_languages VALUES ('84', '0', 'NA', 'Nauru', '', '', '', 'Ekakairũ Naoero', '0', '0');
INSERT INTO static_languages VALUES ('85', '0', 'NE', 'Nepali', '', '', '', 'नेपाली', '0', '0');
INSERT INTO static_languages VALUES ('86', '0', 'NO', 'Norwegian', 'no', '', 'no_NO', 'Norsk', '0', '0');
INSERT INTO static_languages VALUES ('87', '0', 'OC', 'Occitan', '', '', '', 'Occitan', '0', '0');
INSERT INTO static_languages VALUES ('88', '0', 'OR', 'Oriya', '', '', '', 'ଓଡ଼ିଆ', '0', '0');
INSERT INTO static_languages VALUES ('89', '0', 'OM', 'Oromo', '', '', '', 'Afaan Oromoo', '0', '0');
INSERT INTO static_languages VALUES ('90', '0', 'PS', 'Pashto', '', '', '', 'پښت', '0', '0');
INSERT INTO static_languages VALUES ('91', '0', 'PL', 'Polish', 'pl', '', 'pl_PL', 'Polski', '0', '0');
INSERT INTO static_languages VALUES ('92', '0', 'PT', 'Portuguese', 'pt', '', 'pt_PT', 'Português', '0', '0');
INSERT INTO static_languages VALUES ('93', '0', 'PA', 'Punjabi', '', '', '', 'ਪੰਜਾਬੀ / پنجابی', '0', '0');
INSERT INTO static_languages VALUES ('94', '0', 'QU', 'Quechua', '', '', '', 'Runa Simi', '0', '0');
INSERT INTO static_languages VALUES ('95', '0', 'RM', 'Rhaeto-Romance', '', '', '', 'Rumantsch', '0', '0');
INSERT INTO static_languages VALUES ('96', '0', 'RO', 'Romanian', 'ro', '', 'ro_RO', 'Română', '0', '0');
INSERT INTO static_languages VALUES ('97', '0', 'RU', 'Russian', 'ru', '', 'ru_RU', 'Русский', '0', '0');
INSERT INTO static_languages VALUES ('98', '0', 'SM', 'Samoan', '', '', '', 'Gagana faʼa Samoa', '0', '0');
INSERT INTO static_languages VALUES ('99', '0', 'SG', 'Sango', '', '', '', 'Sängö', '0', '0');
INSERT INTO static_languages VALUES ('100', '0', 'SA', 'Sanskrit', '', '', '', 'संस्कृतम्', '1', '0');
INSERT INTO static_languages VALUES ('101', '0', 'SR', 'Serbian', 'sr', '', 'sr_YU', 'Српски / Srpski', '0', '0');
INSERT INTO static_languages VALUES ('103', '0', 'ST', 'Sesotho', '', '', '', 'seSotho', '0', '0');
INSERT INTO static_languages VALUES ('104', '0', 'TN', 'Setswana', '', '', '', 'Setswana', '0', '0');
INSERT INTO static_languages VALUES ('105', '0', 'SN', 'Shona', '', '', '', 'chiShona', '0', '0');
INSERT INTO static_languages VALUES ('106', '0', 'SD', 'Sindhi', '', '', '', 'سنڌي، سندھی', '0', '0');
INSERT INTO static_languages VALUES ('107', '0', 'SI', 'Sinhala', '', '', '', 'සිංහල', '0', '0');
INSERT INTO static_languages VALUES ('108', '0', 'SS', 'Swati', '', '', '', 'siSwati', '0', '0');
INSERT INTO static_languages VALUES ('109', '0', 'SK', 'Slovak', 'sk', '', 'sk_SK', 'Slovenčina', '0', '0');
INSERT INTO static_languages VALUES ('110', '0', 'SL', 'Slovenian', 'si', '', 'sl_SI', 'Slovenščina', '0', '0');
INSERT INTO static_languages VALUES ('111', '0', 'SO', 'Somali', '', '', '', 'af Soomaali', '0', '0');
INSERT INTO static_languages VALUES ('112', '0', 'ES', 'Spanish', 'es', '', 'es_ES', 'Español', '0', '0');
INSERT INTO static_languages VALUES ('113', '0', 'SU', 'Sundanese', '', '', '', 'Basa Sunda', '0', '0');
INSERT INTO static_languages VALUES ('114', '0', 'SW', 'Swahili', '', '', '', 'Kiswahili', '0', '0');
INSERT INTO static_languages VALUES ('115', '0', 'SV', 'Swedish', 'se', '', 'sv_SE', 'Svenska', '0', '0');
INSERT INTO static_languages VALUES ('116', '0', 'TL', 'Tagalog', '', '', '', 'Tagalog', '0', '0');
INSERT INTO static_languages VALUES ('117', '0', 'TG', 'Tajik', '', '', '', 'тоҷикӣ / تاجیکی', '0', '0');
INSERT INTO static_languages VALUES ('118', '0', 'TA', 'Tamil', '', '', '', 'தமிழ்', '0', '0');
INSERT INTO static_languages VALUES ('119', '0', 'TT', 'Tatar', '', '', '', 'татарча / tatarça / تاتارچ', '0', '0');
INSERT INTO static_languages VALUES ('120', '0', 'TE', 'Telugu', '', '', '', 'తెలుగు', '0', '0');
INSERT INTO static_languages VALUES ('121', '0', 'TH', 'Thai', 'th', '', 'th_TH', 'ภาษาไทย', '0', '0');
INSERT INTO static_languages VALUES ('122', '0', 'BO', 'Tibetan', '', '', '', 'བོད་ཡིག', '0', '0');
INSERT INTO static_languages VALUES ('123', '0', 'TI', 'Tigrinya', '', '', '', 'ትግርኛ', '0', '0');
INSERT INTO static_languages VALUES ('124', '0', 'TO', 'Tongan', '', '', '', 'faka-Tonga', '0', '0');
INSERT INTO static_languages VALUES ('125', '0', 'TS', 'Tsonga', '', '', '', 'Tsonga', '0', '0');
INSERT INTO static_languages VALUES ('126', '0', 'TR', 'Turkish', 'tr', '', 'tr_TR', 'Türkçe', '0', '0');
INSERT INTO static_languages VALUES ('127', '0', 'TK', 'Turkmen', '', '', '', 'Türkmen dili', '0', '0');
INSERT INTO static_languages VALUES ('128', '0', 'TW', 'Twi', '', '', '', 'Twi', '0', '0');
INSERT INTO static_languages VALUES ('129', '0', 'UG', 'Uyghur', '', '', '', 'ئۇيغۇرچه', '0', '0');
INSERT INTO static_languages VALUES ('130', '0', 'UK', 'Ukrainian', 'ua', '', 'uk_UA', 'Українська', '0', '0');
INSERT INTO static_languages VALUES ('131', '0', 'UR', 'Urdu', '', '', '', 'اردو', '0', '0');
INSERT INTO static_languages VALUES ('132', '0', 'UZ', 'Uzbek', '', '', '', 'Ўзбек / O\'zbek', '0', '0');
INSERT INTO static_languages VALUES ('133', '0', 'VI', 'Vietnamese', 'vn', '', 'vi_VN', 'Tiếng Việt', '0', '0');
INSERT INTO static_languages VALUES ('134', '0', 'VO', 'Volapük', '', '', '', 'Volapük', '0', '1');
INSERT INTO static_languages VALUES ('135', '0', 'CY', 'Welsh', '', '', '', 'Cymraeg', '0', '0');
INSERT INTO static_languages VALUES ('136', '0', 'WO', 'Wolof', '', '', '', 'Wolof', '0', '0');
INSERT INTO static_languages VALUES ('137', '0', 'XH', 'Xhosa', '', '', '', 'isiXhosa', '0', '0');
INSERT INTO static_languages VALUES ('138', '0', 'YI', 'Yiddish', '', '', '', 'ייִדיש', '0', '0');
INSERT INTO static_languages VALUES ('139', '0', 'YO', 'Yoruba', '', '', '', 'Yorùbá', '0', '0');
INSERT INTO static_languages VALUES ('140', '0', 'ZU', 'Zulu', '', '', '', 'isiZulu', '0', '0');
INSERT INTO static_languages VALUES ('141', '0', 'BS', 'Bosnian', 'ba', '', 'bs_BA', 'Bosanski', '0', '0');
INSERT INTO static_languages VALUES ('142', '0', 'AE', 'Avestan', '', '', '', 'Avestan', '1', '0');
INSERT INTO static_languages VALUES ('143', '0', 'AK', 'Akan', '', '', '', 'Akan', '0', '0');
INSERT INTO static_languages VALUES ('144', '0', 'AN', 'Aragonese', '', '', '', 'Aragonés', '0', '0');
INSERT INTO static_languages VALUES ('145', '0', 'AV', 'Avar', '', '', '', 'магӀарул мацӀ', '0', '0');
INSERT INTO static_languages VALUES ('146', '0', 'BM', 'Bambara', '', '', '', 'Bamanankan', '0', '0');
INSERT INTO static_languages VALUES ('147', '0', 'CE', 'Chechen', '', '', '', 'Нохчийн', '0', '0');
INSERT INTO static_languages VALUES ('148', '0', 'CH', 'Chamorro', '', '', '', 'Chamoru', '0', '0');
INSERT INTO static_languages VALUES ('149', '0', 'CR', 'Cree', '', '', '', 'ᓀᐦᐃᔭᐤ', '0', '0');
INSERT INTO static_languages VALUES ('150', '0', 'CU', 'Church Slavonic', '', '', '', 'церковнославя́нский язы́к', '1', '0');
INSERT INTO static_languages VALUES ('151', '0', 'CV', 'Chuvash', '', '', '', 'Чăваш чěлхи', '0', '0');
INSERT INTO static_languages VALUES ('152', '0', 'DV', 'Dhivehi', '', '', '', 'ދިވެހި', '0', '0');
INSERT INTO static_languages VALUES ('153', '0', 'EE', 'Ewe', '', '', '', 'Ɛʋɛgbɛ', '0', '0');
INSERT INTO static_languages VALUES ('154', '0', 'FF', 'Fula', '', '', '', 'Fulfulde / Pulaar', '0', '0');
INSERT INTO static_languages VALUES ('155', '0', 'HO', 'Hiri motu', '', '', '', 'Hiri motu', '0', '0');
INSERT INTO static_languages VALUES ('156', '0', 'HT', 'Haïtian Creole', '', '', '', 'Krèyol ayisyen', '0', '0');
INSERT INTO static_languages VALUES ('157', '0', 'HZ', 'Herero', '', '', '', 'otsiHerero', '0', '0');
INSERT INTO static_languages VALUES ('158', '0', 'IG', 'Igbo', '', '', '', 'Igbo', '0', '0');
INSERT INTO static_languages VALUES ('159', '0', 'II', 'Yi', '', '', '', 'ꆇꉙ', '0', '0');
INSERT INTO static_languages VALUES ('160', '0', 'IO', 'Ido', '', '', '', 'Ido', '0', '1');
INSERT INTO static_languages VALUES ('161', '0', 'JV', 'Javanese', '', '', '', 'Basa Jawa', '0', '0');
INSERT INTO static_languages VALUES ('162', '0', 'KG', 'Kongo', '', '', '', 'Kikongo', '0', '0');
INSERT INTO static_languages VALUES ('163', '0', 'KI', 'Kikuyu', '', '', '', 'Gĩkũyũ', '0', '0');
INSERT INTO static_languages VALUES ('164', '0', 'KJ', 'Kuanyama', '', '', '', 'Kuanyama', '0', '0');
INSERT INTO static_languages VALUES ('165', '0', 'KR', 'Kanuri', '', '', '', 'Kanuri', '0', '0');
INSERT INTO static_languages VALUES ('166', '0', 'KV', 'Komi', '', '', '', 'коми кыв', '0', '0');
INSERT INTO static_languages VALUES ('167', '0', 'KW', 'Cornish', '', '', '', 'Kernewek', '0', '0');
INSERT INTO static_languages VALUES ('168', '0', 'LB', 'Luxembourgish', '', '', '', 'Lëtzebuergesch', '0', '0');
INSERT INTO static_languages VALUES ('169', '0', 'LG', 'Luganda', '', '', '', 'Luganda', '0', '0');
INSERT INTO static_languages VALUES ('170', '0', 'LI', 'Limburgish', '', '', '', 'Limburgs', '0', '0');
INSERT INTO static_languages VALUES ('171', '0', 'LU', 'Luba-Katanga', '', '', '', 'Luba-Katanga', '0', '0');
INSERT INTO static_languages VALUES ('172', '0', 'MH', 'Marshallese', '', '', '', 'Kajin M̧ajeļ', '0', '0');
INSERT INTO static_languages VALUES ('173', '0', 'NB', 'Norwegian Bokmål', '', '', '', 'Norsk bokmål', '0', '0');
INSERT INTO static_languages VALUES ('174', '0', 'ND', 'North Ndebele', '', '', '', 'isiNdebele', '0', '0');
INSERT INTO static_languages VALUES ('175', '0', 'NG', 'Ndonga', '', '', '', 'Owambo', '0', '0');
INSERT INTO static_languages VALUES ('176', '0', 'NN', 'Norwegian Nynorsk', '', '', '', 'Norsk nynorsk', '0', '0');
INSERT INTO static_languages VALUES ('177', '0', 'NR', 'South Ndebele', '', '', '', 'Ndébélé', '0', '0');
INSERT INTO static_languages VALUES ('178', '0', 'NV', 'Navajo', '', '', '', 'Dinékʼehǰí', '0', '0');
INSERT INTO static_languages VALUES ('179', '0', 'NY', 'Chichewa', '', '', '', 'chiCheŵa', '0', '0');
INSERT INTO static_languages VALUES ('180', '0', 'OJ', 'Ojibwa', '', '', '', 'ᐊᓂᔑᓈᐯᒧᐎᓐ', '0', '0');
INSERT INTO static_languages VALUES ('181', '0', 'OS', 'Ossetic', '', '', '', 'Ирон æвзаг', '0', '0');
INSERT INTO static_languages VALUES ('182', '0', 'PI', 'Pali', '', '', '', 'Pāli', '1', '0');
INSERT INTO static_languages VALUES ('183', '0', 'SC', 'Sardinian', '', '', '', 'Sardu', '0', '0');
INSERT INTO static_languages VALUES ('184', '0', 'SE', 'Northern Sami', '', '', '', ' Sámegiella', '0', '0');
INSERT INTO static_languages VALUES ('186', '0', 'TY', 'Tahitian', '', '', '', 'Reo Tahiti', '0', '0');
INSERT INTO static_languages VALUES ('187', '0', 'VE', 'Venda', '', '', '', 'tshiVenḓa', '0', '0');
INSERT INTO static_languages VALUES ('188', '0', 'WA', 'Walloon', '', '', '', 'Walon', '0', '0');
INSERT INTO static_languages VALUES ('189', '0', 'PT', 'Brazilian Portuguese', 'br', 'BR', 'pt_BR', 'Português brasileiro', '0', '0');
INSERT INTO static_languages VALUES ('190', '0', 'ZH', 'Chinese (Simplified)', 'ch', 'CN', 'zh_CN', '汉语', '0', '0');
INSERT INTO static_languages VALUES ('191', '0', 'FR', 'Canadian French', 'qc', 'CA', 'fr_CA', 'Français canadien', '0', '0');
INSERT INTO static_languages VALUES ('192', '0', 'TL', 'Filipino', '', 'PH', 'fil', 'Filipino', '0', '0');



