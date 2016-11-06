SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `passwd` text COLLATE utf8_unicode_ci NOT NULL,
  `ga_token` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `admins` (`id`, `username`, `passwd`, `ga_token`) VALUES
(1,	'admin',	'8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918',	'');

DROP TABLE IF EXISTS `admin_logs`;
CREATE TABLE `admin_logs` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `admin_username` text COLLATE utf8_unicode_ci NOT NULL,
  `ip` text COLLATE utf8_unicode_ci NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `browser` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `chat`;
CREATE TABLE `chat` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `sender` int(255) NOT NULL,
  `for` int(255) NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `room` int(11) NOT NULL,
  `displayed` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `deposits`;
CREATE TABLE `deposits` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `player_id` int(255) NOT NULL,
  `address` text COLLATE utf8_unicode_ci NOT NULL,
  `received` int(1) NOT NULL DEFAULT '0',
  `amount` double NOT NULL DEFAULT '0',
  `coins_amount` int(255) NOT NULL,
  `currency` text NOT NULL,
  `txid` text COLLATE utf8_unicode_ci NOT NULL,
  `confirmed` int(11) NOT NULL,
  `time_generated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `withdrawals`;
CREATE TABLE `withdrawals` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `player_id` int(255) NOT NULL,
  `address` text COLLATE utf8_unicode_ci NOT NULL,
  `withdrawned` int(11) NOT NULL DEFAULT '0',
  `amount` double NOT NULL DEFAULT '0',
  `coins_amount` int(255) NOT NULL,
  `currency` text NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `chat_rooms`;
CREATE TABLE `chat_rooms` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `player_relations`;
CREATE TABLE `player_relations` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `player` int(11) NOT NULL,
  `friend` int(11) NOT NULL,
  `relation` int(11) NOT NULL,
  `state` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `screenshots`;
CREATE TABLE `screenshots` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `tid` int(255) NOT NULL,
  `name` text NOT NULL,
  `path` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `games`;
CREATE TABLE `games` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `player` int(255) NOT NULL,
  `player_deck` text COLLATE utf8_unicode_ci NOT NULL,
  `player_deck_stand` int(1) NOT NULL DEFAULT '0',
  `player_deck_2` text COLLATE utf8_unicode_ci NOT NULL,
  `player_deck_2_stand` int(1) NOT NULL DEFAULT '0',
  `dealer_deck` text COLLATE utf8_unicode_ci NOT NULL,
  `ended` int(1) NOT NULL DEFAULT '0',
  `bet_amount` double NOT NULL,
  `winner` text COLLATE utf8_unicode_ci NOT NULL,
  `multiplier` double NOT NULL DEFAULT '0',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `initial_shuffle` text COLLATE utf8_unicode_ci NOT NULL,
  `client_seed` text COLLATE utf8_unicode_ci NOT NULL,
  `final_shuffle` text COLLATE utf8_unicode_ci NOT NULL,
  `used_cards` int(255) NOT NULL,
  `accessable_actions` int(1) NOT NULL,
  `canhit` int(1) NOT NULL DEFAULT '1',
  `insurance_process` int(1) NOT NULL DEFAULT '0',
  `note` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `currencies`;
CREATE TABLE `currencies` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `currency` text NOT NULL,
  `enabled` int(11) NOT NULL DEFAULT '0',
  `min_deposit` int(11) NOT NULL DEFAULT '0',
  `rate` int(11) NOT NULL DEFAULT '0',
  `instructions` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `giveaway_ip_limit`;
CREATE TABLE `giveaway_ip_limit` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `ip` text COLLATE utf8_unicode_ci NOT NULL,
  `claimed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `investors`;
CREATE TABLE `investors` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `player_id` int(255) NOT NULL,
  `amount` double NOT NULL DEFAULT '0',
  `profit` double NOT NULL DEFAULT '0',
  `joined` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `players`;
CREATE TABLE `players` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `hash` text COLLATE utf8_unicode_ci NOT NULL,
  `balance` double NOT NULL DEFAULT '0',
  `username` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `time_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `time_last_active` datetime NOT NULL,
  `password` text COLLATE utf8_unicode_ci NOT NULL,
  `lastip` text COLLATE utf8_unicode_ci NOT NULL,
  `slots_seed` text COLLATE utf8_unicode_ci NOT NULL,
  `dice_seed` text COLLATE utf8_unicode_ci NOT NULL,
  `client_seed` text COLLATE utf8_unicode_ci NOT NULL,
  `last_slots_seed` text COLLATE utf8_unicode_ci NOT NULL,
  `last_dice_seed` text COLLATE utf8_unicode_ci NOT NULL,
  `last_client_seed` text COLLATE utf8_unicode_ci NOT NULL,
  `slots_last_result` text COLLATE utf8_unicode_ci NOT NULL,
  `dice_last_result` text COLLATE utf8_unicode_ci NOT NULL,
  `initial_shuffle` text COLLATE utf8_unicode_ci NOT NULL,
  `last_initial_shuffle` text COLLATE utf8_unicode_ci NOT NULL,
  `last_final_shuffle` text COLLATE utf8_unicode_ci NOT NULL,
  `ga_token` text COLLATE utf8_unicode_ci NOT NULL,
  `email` text COLLATE utf8_unicode_ci NOT NULL,
  `activation_hash` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `state` int(11) NOT NULL,
  `chat_status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `spins`;
CREATE TABLE `spins` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `player` int(255) NOT NULL,
  `bet_amount` double NOT NULL,
  `server_seed` text COLLATE utf8_unicode_ci NOT NULL,
  `client_seed` text COLLATE utf8_unicode_ci NOT NULL,
  `result` text COLLATE utf8_unicode_ci NOT NULL,
  `game` VARCHAR(20) NOT NULL,
  `multiplier` double NOT NULL,
  `payout` double NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `player` (`player`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `system`;
CREATE TABLE `system` (
  `id` int(1) NOT NULL DEFAULT '1',
  `autoalias_increment` int(255) NOT NULL DEFAULT '1',
  `title` text COLLATE utf8_unicode_ci NOT NULL,
  `url` text COLLATE utf8_unicode_ci NOT NULL,
  `currency` text COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `giveaway` int(1) NOT NULL DEFAULT '0',
  `giveaway_amount` double NOT NULL DEFAULT '0',
  `chat_enable` int(1) NOT NULL DEFAULT '1',
  `giveaway_freq` int(255) NOT NULL DEFAULT '30',
  `min_bet` double NOT NULL DEFAULT '0.00000001',
  `min_withdrawal` double NOT NULL DEFAULT '0.001',
  `btc_min_deposit` double NOT NULL DEFAULT '0.00000001',
  `min_confirmations` int(255) NOT NULL DEFAULT '1',
  `bankroll_maxbet_ratio` double NOT NULL DEFAULT '25',
  `jackpot` double NOT NULL DEFAULT '12339',
  `inv_enable` int(1) NOT NULL DEFAULT '1',
  `inv_perc` double NOT NULL DEFAULT '0',
  `inv_min` double NOT NULL DEFAULT '0.0001',
  `inv_casproit` double NOT NULL DEFAULT '0',
  `deposits_last_round` datetime NOT NULL,
  `installed` int(1) NOT NULL DEFAULT '0',
  `maintenance` int(1) NOT NULL DEFAULT '0',
  `withdrawal_mode` int(1) NOT NULL DEFAULT '1',
  `number_of_decks` int(11) NOT NULL DEFAULT '4',
  `bj_pays` int(11) NOT NULL DEFAULT '1',
  `btc_rate` int(11) NOT NULL,
  `insurance` int(11) NOT NULL DEFAULT '1',
  `tie_dealerwon` int(11) NOT NULL DEFAULT '1',
  `hits_on_soft` int(11) NOT NULL DEFAULT '1',
  `house_edge` int(11) NOT NULL DEFAULT '1',
  `smtp_enabled` int(11) NOT NULL DEFAULT '0',
  `smtp_encryption` int(11) NOT NULL DEFAULT '0',
  `smtp_auth` int(11) NOT NULL DEFAULT '0',
  `smtp_server` text NOT NULL,
  `smtp_password` text NOT NULL,
  `email` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `system` (`id`,`active_theme`) VALUES
(1,'Basic');

DROP TABLE IF EXISTS `transactions`;
CREATE TABLE `transactions` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `player_id` int(255) NOT NULL,
  `amount` double NOT NULL,
  `txid` text COLLATE utf8_unicode_ci NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `player_id` (`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- 2015-07-30 16:01:34