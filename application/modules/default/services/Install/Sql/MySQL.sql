-- phpMyAdmin SQL Dump
-- version 3.3.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 28, 2010 at 06:42 PM
-- Server version: 5.1.47
-- PHP Version: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `bizsense`
--

-- --------------------------------------------------------

--
-- Table structure for table `access`
--

CREATE TABLE `access` (
  `access_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL COMMENT 'roleid',
  `privilege_id` int(10) unsigned NOT NULL COMMENT 'privilege id',
  PRIMARY KEY (`access_id`),
  KEY `role_id` (`role_id`),
  KEY `privilege_id` (`privilege_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `account_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_name` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(320) COLLATE utf8_unicode_ci DEFAULT NULL,
  `billing_address_line_1` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `billing_address_line_2` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `billing_address_line_3` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `billing_address_line_4` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `billing_city` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `billing_state` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `billing_postal_code` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `billing_country` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shipping_address_line_1` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shipping_address_line_2` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shipping_address_line_3` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shipping_address_line_4` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shipping_city` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shipping_state` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shipping_postal_code` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shipping_country` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `assigned_to` int(10) unsigned NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `created` int(11) NOT NULL,
  `branch_id` int(10) unsigned NOT NULL,
  `updated` int(11) DEFAULT NULL,
  `ledger_id` int(10) unsigned DEFAULT NULL,
  `campaign_id` int(10) unsigned DEFAULT NULL,
  `tin` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pan` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `service_tax_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`account_id`),
  KEY `branch_id` (`branch_id`),
  KEY `created_by` (`created_by`),
  KEY `assigned_to` (`assigned_to`),
  KEY `ledger_id` (`ledger_id`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `account_notes`
--

CREATE TABLE `account_notes` (
  `account_notes_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(10) unsigned NOT NULL,
  `notes` text COLLATE utf8_unicode_ci NOT NULL,
  `created` int(11) NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`account_notes_id`),
  KEY `account_id` (`account_id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bank_account`
--

CREATE TABLE `bank_account` (
  `bank_account_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_no` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `bank_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `bank_branch` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `fa_ledger_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`bank_account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bank_transaction`
--

CREATE TABLE `bank_transaction` (
  `bank_transaction_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bank_account_id` int(10) unsigned NOT NULL,
  `transaction_type` tinyint(4) NOT NULL,
  `amount` decimal(20,2) NOT NULL,
  `cash_account_id` int(11) NOT NULL,
  `s_ledger_entry_ids` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `date` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`bank_transaction_id`),
  KEY `bank_account_id` (`bank_account_id`),
  KEY `cash_account_id` (`cash_account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `branch`
--

CREATE TABLE `branch` (
  `branch_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `branch_manager` int(10) unsigned DEFAULT NULL,
  `branch_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `address_line_1` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `address_line_2` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `address_line_3` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_line_4` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `state` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `postal_code` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `country` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(320) COLLATE utf8_unicode_ci DEFAULT NULL,
  `service_tax_number` varchar(40) CHARACTER SET utf8 DEFAULT NULL,
  `tin` varchar(40) CHARACTER SET utf8 DEFAULT NULL,
  `parent_branch_id` int(10) unsigned DEFAULT NULL,
  `description` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_hq` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`branch_id`),
  UNIQUE KEY `branch_name` (`branch_name`),
  KEY `branch_manager` (`branch_manager`),
  KEY `parent_branch_id` (`parent_branch_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `call`
--

CREATE TABLE `call` (
  `call_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `start_date` int(11) NOT NULL,
  `end_date` int(11) NOT NULL,
  `assigned_to` int(11) unsigned NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `created` int(11) NOT NULL,
  `created_by` int(11) unsigned NOT NULL,
  `call_status_id` int(11) unsigned NOT NULL,
  `to_type` tinyint(1) NOT NULL,
  `to_type_id` int(11) NOT NULL,
  `reminder` tinyint(2) NOT NULL,
  `reminder_sent` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`call_id`),
  KEY `assigned_to` (`assigned_to`),
  KEY `created_by` (`created_by`),
  KEY `call_status_id` (`call_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `call_notes`
--

CREATE TABLE `call_notes` (
  `call_notes_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `call_id` int(10) unsigned NOT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `created` int(11) NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  PRIMARY KEY (`call_notes_id`),
  KEY `call_id` (`call_id`,`created_by`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `call_status`
--

CREATE TABLE `call_status` (
  `call_status_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `context` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`call_status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `campaign`
--

CREATE TABLE `campaign` (
  `campaign_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `start_date` int(11) NOT NULL,
  `end_date` int(11) NOT NULL,
  `assigned_to` int(10) unsigned NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `created` int(11) NOT NULL COMMENT 'when campaign was created',
  `branch_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`campaign_id`),
  KEY `assigned_to` (`assigned_to`),
  KEY `branch_id` (`branch_id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cash_account`
--

CREATE TABLE `cash_account` (
  `cash_account_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `branch_id` int(10) unsigned NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `fa_ledger_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`cash_account_id`),
  UNIQUE KEY `name` (`name`),
  KEY `branch_id` (`branch_id`,`created_by`),
  KEY `created_by` (`created_by`),
  KEY `fa_ledger_id` (`fa_ledger_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `contact_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `middle_name` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `last_name` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `work_phone` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `home_phone` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `mobile` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `fax` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `title` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `department` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `work_email` varchar(320) CHARACTER SET latin1 DEFAULT NULL,
  `other_email` varchar(320) CHARACTER SET latin1 DEFAULT NULL,
  `do_not_call` tinyint(1) NOT NULL DEFAULT '0',
  `email_opt_out` tinyint(1) NOT NULL DEFAULT '0',
  `billing_city` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `billing_state` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `billing_postal_code` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `billing_country` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `shipping_city` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `shipping_state` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `shipping_postal_code` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `shipping_country` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `description` varchar(250) CHARACTER SET latin1 DEFAULT NULL,
  `reports_to` int(10) unsigned DEFAULT NULL,
  `salutation_id` int(10) unsigned DEFAULT NULL,
  `assistant_id` int(10) unsigned DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `birthday_date` int(10) unsigned DEFAULT NULL,
  `birthday_month` int(10) unsigned DEFAULT NULL,
  `assigned_to` int(10) unsigned DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `created` int(11) NOT NULL,
  `updated` int(11) NOT NULL,
  `branch_id` int(10) unsigned NOT NULL,
  `account_id` int(10) unsigned DEFAULT NULL,
  `billing_address_line_1` varchar(100) CHARACTER SET latin1 NOT NULL,
  `billing_address_line_2` varchar(100) CHARACTER SET latin1 NOT NULL,
  `billing_address_line_3` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `billing_address_line_4` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `shipping_address_line_1` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `shipping_address_line_2` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `shipping_address_line_3` varchar(100) CHARACTER SET latin1 NOT NULL,
  `shipping_address_line_4` varchar(100) CHARACTER SET latin1 NOT NULL,
  `ss_enabled` tinyint(1) DEFAULT '0',
  `ss_active` tinyint(1) DEFAULT NULL,
  `ss_password` varchar(32) CHARACTER SET latin1 DEFAULT NULL,
  `ledger_id` int(10) unsigned DEFAULT NULL,
  `campaign_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`contact_id`),
  KEY `reports_to` (`reports_to`),
  KEY `salutation_id` (`salutation_id`),
  KEY `assistant_id` (`assistant_id`),
  KEY `branch_id` (`branch_id`),
  KEY `account_id` (`account_id`),
  KEY `ledger_id` (`ledger_id`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_notes`
--

CREATE TABLE `contact_notes` (
  `contact_notes_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contact_id` int(10) unsigned NOT NULL,
  `notes` text CHARACTER SET utf8 NOT NULL,
  `created` int(11) NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  PRIMARY KEY (`contact_notes_id`),
  KEY `opportunity_id` (`contact_id`,`created_by`),
  KEY `created_by` (`created_by`),
  KEY `contact_id` (`contact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `currency`
--

CREATE TABLE `currency` (
  `currency_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `symbol` varchar(10) CHARACTER SET latin1 DEFAULT NULL,
  `name` varchar(10) CHARACTER SET latin1 NOT NULL,
  `short_description` varchar(10) CHARACTER SET latin1 DEFAULT NULL,
  `description` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`currency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `domain_blacklist`
--

CREATE TABLE `domain_blacklist` (
  `domain_blacklist_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`domain_blacklist_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fa_group`
--

CREATE TABLE `fa_group` (
  `fa_group_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) CHARACTER SET utf8 NOT NULL,
  `fa_group_category_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`fa_group_id`),
  UNIQUE KEY `name` (`name`),
  KEY `fa_group_category_id` (`fa_group_category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fa_group_category`
--

CREATE TABLE `fa_group_category` (
  `fa_group_category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`fa_group_category_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fa_ledger`
--

CREATE TABLE `fa_ledger` (
  `fa_ledger_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) CHARACTER SET utf8 NOT NULL,
  `fa_group_id` int(10) unsigned DEFAULT NULL,
  `opening_balance_ledger_entry_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`fa_ledger_id`),
  KEY `fa_group_id` (`fa_group_id`),
  KEY `opening_balance_ledger_entry_id` (`opening_balance_ledger_entry_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fa_ledger_entry`
--

CREATE TABLE `fa_ledger_entry` (
  `fa_ledger_entry_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fa_ledger_id` int(10) unsigned NOT NULL,
  `debit` decimal(10,2) NOT NULL,
  `credit` decimal(10,2) NOT NULL,
  `notes` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `transaction_timestamp` int(11) NOT NULL,
  PRIMARY KEY (`fa_ledger_entry_id`),
  KEY `fa_ledger_id` (`fa_ledger_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `invoice_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `invoice_type` int(10) unsigned NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `branch_id` int(10) unsigned NOT NULL,
  `to_type` int(10) unsigned NOT NULL,
  `to_type_id` int(10) unsigned NOT NULL,
  `notes` text CHARACTER SET utf8,
  `delivery_terms` text CHARACTER SET utf8,
  `payment_terms` text CHARACTER SET utf8,
  `purchase_order` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `contact_id` int(10) unsigned DEFAULT NULL,
  `s_ledger_entry_ids` text COLLATE utf8_unicode_ci,
  `campaign_id` int(10) unsigned DEFAULT NULL,
  `discount_amount` decimal(10,0) DEFAULT NULL,
  `freight_amount` decimal(10,0) DEFAULT NULL,
  PRIMARY KEY (`invoice_id`),
  KEY `contact_id` (`contact_id`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_item`
--

CREATE TABLE `invoice_item` (
  `invoice_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) unsigned NOT NULL,
  `invoice_item_inventory_id` int(10) unsigned NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `tax_type_id` int(10) unsigned DEFAULT NULL,
  `item_description` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`invoice_item_id`),
  KEY `invoice_id` (`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_service_item`
--

CREATE TABLE `invoice_service_item` (
  `invoice_service_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) unsigned NOT NULL,
  `service_item_id` int(10) unsigned NOT NULL,
  `amount` decimal(20,2) NOT NULL,
  `tax_type_id` int(10) unsigned DEFAULT NULL,
  `description` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`invoice_service_item_id`),
  KEY `invoice_id` (`invoice_id`,`service_item_id`,`tax_type_id`),
  KEY `service_item_id` (`service_item_id`),
  KEY `tax_type_id` (`tax_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `lead`
--

CREATE TABLE `lead` (
  `lead_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `middle_name` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `last_name` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `company_name` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `home_phone` varchar(40) CHARACTER SET latin1 DEFAULT NULL,
  `work_phone` varchar(40) CHARACTER SET latin1 DEFAULT NULL,
  `mobile` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `fax` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `email` varchar(320) CHARACTER SET latin1 DEFAULT NULL,
  `address_line_1` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `address_line_2` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `address_line_3` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `address_line_4` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `city` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `state` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `postal_code` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `country` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `description` varchar(250) CHARACTER SET latin1 DEFAULT NULL,
  `do_not_call` tinyint(1) NOT NULL DEFAULT '0',
  `email_opt_out` tinyint(1) NOT NULL DEFAULT '0',
  `converted` tinyint(1) NOT NULL,
  `created` int(11) NOT NULL,
  `updated` int(11) DEFAULT NULL,
  `lead_source_id` int(10) unsigned DEFAULT NULL,
  `lead_status_id` int(10) unsigned DEFAULT NULL,
  `assigned_to` int(10) unsigned DEFAULT NULL,
  `branch_id` int(10) unsigned DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `campaign_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`lead_id`),
  KEY `created_by` (`created_by`),
  KEY `branch_id` (`branch_id`),
  KEY `assigned_to` (`assigned_to`),
  KEY `lead_status_id` (`lead_status_id`),
  KEY `lead_source_id` (`lead_source_id`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lead_notes`
--

CREATE TABLE `lead_notes` (
  `lead_notes_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` int(10) unsigned NOT NULL,
  `notes` text NOT NULL,
  `created` int(11) NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  PRIMARY KEY (`lead_notes_id`),
  KEY `opportunity_id` (`lead_id`,`created_by`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lead_source`
--

CREATE TABLE `lead_source` (
  `lead_source_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `description` varchar(250) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`lead_source_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lead_status`
--

CREATE TABLE `lead_status` (
  `lead_status_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `description` varchar(250) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`lead_status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `list`
--

CREATE TABLE `list` (
  `list_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` int(11) NOT NULL COMMENT 'when list was created',
  `created_by` int(10) unsigned DEFAULT NULL,
  `show_in_customer_portal` tinyint(1) DEFAULT '1',
  `auto_bounce_handle` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`list_id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `list_subscriber`
--

CREATE TABLE `list_subscriber` (
  `list_subscriber_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `list_id` int(10) unsigned NOT NULL,
  `subscriber_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`list_subscriber_id`),
  KEY `list_id` (`list_id`),
  KEY `subscriber_id` (`subscriber_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE `log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `priority` varchar(100) CHARACTER SET latin1 NOT NULL,
  `message` varchar(250) CHARACTER SET latin1 NOT NULL,
  `log_timestamp` varchar(50) CHARACTER SET latin1 NOT NULL,
  `priority_name` varchar(25) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `manufacturer`
--

CREATE TABLE `manufacturer` (
  `manufacturer_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `description` varchar(250) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`manufacturer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meeting`
--

CREATE TABLE `meeting` (
  `meeting_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `start_date` int(11) NOT NULL,
  `end_date` int(11) NOT NULL,
  `assigned_to` int(11) unsigned NOT NULL,
  `meeting_status_id` int(11) unsigned NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `created` int(11) NOT NULL,
  `created_by` int(11) unsigned NOT NULL,
  `venue` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `reminder` tinyint(2) NOT NULL,
  `reminder_sent` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`meeting_id`),
  KEY `assigned_to` (`assigned_to`),
  KEY `created_by` (`created_by`),
  KEY `meeting_status_id` (`meeting_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meeting_attendee`
--

CREATE TABLE `meeting_attendee` (
  `meeting_attendee_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `meeting_id` int(10) unsigned NOT NULL,
  `attendee_type` tinyint(1) NOT NULL,
  `attendee_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`meeting_attendee_id`),
  KEY `meeting_id` (`meeting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meeting_notes`
--

CREATE TABLE `meeting_notes` (
  `meeting_notes_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `meeting_id` int(10) unsigned NOT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `created` int(11) NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  PRIMARY KEY (`meeting_notes_id`),
  KEY `meeting_id` (`meeting_id`,`created_by`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meeting_status`
--

CREATE TABLE `meeting_status` (
  `meeting_status_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `context` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`meeting_status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `message_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `subject` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `html` text COLLATE utf8_unicode_ci NOT NULL,
  `created` int(11) NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `campaign_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`message_id`),
  KEY `created_by` (`created_by`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `message_queue`
--

CREATE TABLE `message_queue` (
  `message_queue_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `status` tinyint(1) NOT NULL,
  `sent_time` int(11) NOT NULL,
  `message_id` int(10) unsigned DEFAULT NULL,
  `hash` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `list_id` int(10) unsigned DEFAULT NULL,
  `custom_text_body` text COLLATE utf8_unicode_ci,
  `custom_html_body` text COLLATE utf8_unicode_ci,
  `custom_subject` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subscriber_id` int(10) unsigned NOT NULL,
  `unique_message` int(10) NOT NULL,
  PRIMARY KEY (`message_queue_id`),
  KEY `message_id` (`message_id`),
  KEY `list_id` (`list_id`),
  KEY `subscriber_id` (`subscriber_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `opportunity`
--

CREATE TABLE `opportunity` (
  `opportunity_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `customer_type` tinyint(1) NOT NULL DEFAULT '1',
  `name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `amount` double DEFAULT NULL,
  `expected_close_date` int(11) NOT NULL,
  `description` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `lead_source_id` int(10) unsigned DEFAULT NULL,
  `sales_stage_id` int(10) unsigned DEFAULT NULL,
  `account_id` int(10) unsigned DEFAULT NULL,
  `contact_id` int(10) unsigned DEFAULT NULL,
  `assigned_to` int(10) unsigned NOT NULL,
  `branch_id` int(10) unsigned DEFAULT NULL,
  `created` int(11) NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `updated` int(11) DEFAULT NULL,
  `campaign_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`opportunity_id`),
  KEY `created_by` (`created_by`),
  KEY `branch_id` (`branch_id`),
  KEY `assigned_to` (`assigned_to`),
  KEY `contact_id` (`contact_id`),
  KEY `account_id` (`account_id`),
  KEY `sales_stage_id` (`sales_stage_id`),
  KEY `lead_source_id` (`lead_source_id`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `opportunity_contact`
--

CREATE TABLE `opportunity_contact` (
  `opportunity_id` int(11) unsigned NOT NULL,
  `contact_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`opportunity_id`),
  KEY `contact_id` (`contact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `opportunity_notes`
--

CREATE TABLE `opportunity_notes` (
  `opportunity_notes_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `opportunity_id` int(10) unsigned NOT NULL,
  `notes` text CHARACTER SET utf8 NOT NULL,
  `created` int(11) NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  PRIMARY KEY (`opportunity_notes_id`),
  KEY `opportunity_id` (`opportunity_id`,`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `organization_details`
--

CREATE TABLE `organization_details` (
  `organization_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_name` varchar(150) CHARACTER SET latin1 NOT NULL,
  `website` varchar(200) CHARACTER SET latin1 DEFAULT NULL COMMENT 'website url',
  `description` varchar(500) CHARACTER SET latin1 DEFAULT NULL,
  `logo` varchar(500) CHARACTER SET latin1 DEFAULT NULL,
  `logo_for_documents` varchar(500) CHARACTER SET latin1 DEFAULT NULL,
  `footer` varchar(500) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`organization_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` int(11) NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `amount` decimal(10,2) NOT NULL,
  `type` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `type_id` int(10) unsigned DEFAULT NULL,
  `s_fa_ledger_entry_ids` text COLLATE utf8_unicode_ci,
  `mode` tinyint(4) NOT NULL,
  `mode_id` int(10) unsigned DEFAULT NULL,
  `indirect_expense_ledger_id` int(10) unsigned DEFAULT NULL,
  `date` int(10) unsigned NOT NULL,
  PRIMARY KEY (`payment_id`),
  KEY `created_by` (`created_by`),
  KEY `indirect_expense_ledger_id` (`indirect_expense_ledger_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_bank`
--

CREATE TABLE `payment_bank` (
  `payment_bank_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` int(10) unsigned NOT NULL,
  `instrument_number` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `instrument_date` int(11) NOT NULL,
  `reconciliation_date` int(11) DEFAULT NULL,
  `returned` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`payment_bank_id`),
  KEY `payment_id` (`payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_tds`
--

CREATE TABLE `payment_tds` (
  `payment_tds_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` int(10) unsigned NOT NULL,
  `tds_amount` decimal(20,2) NOT NULL,
  PRIMARY KEY (`payment_tds_id`),
  KEY `payment_id` (`payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payslip`
--

CREATE TABLE `payslip` (
  `payslip_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) unsigned NOT NULL,
  `date` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `s_fa_ledger_ids` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`payslip_id`),
  KEY `employee_id` (`employee_id`,`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payslip_field`
--

CREATE TABLE `payslip_field` (
  `payslip_field_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `ledger_id` int(10) unsigned DEFAULT NULL,
  `machine_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`payslip_field_id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `machine_name` (`machine_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payslip_item`
--

CREATE TABLE `payslip_item` (
  `payslip_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `payslip_field_id` int(10) unsigned NOT NULL,
  `amount` decimal(20,2) NOT NULL,
  `payslip_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`payslip_item_id`),
  KEY `payslip_field_id` (`payslip_field_id`),
  KEY `payslip_id` (`payslip_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `privilege`
--

CREATE TABLE `privilege` (
  `privilege_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`privilege_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `description` varchar(500) CHARACTER SET latin1 DEFAULT NULL,
  `selling_price` decimal(10,2) NOT NULL,
  `taxable` tinyint(4) NOT NULL DEFAULT '1',
  `tax_type_id` int(10) unsigned DEFAULT NULL,
  `subscribable` tinyint(4) NOT NULL DEFAULT '0',
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `buying_price` decimal(10,2) NOT NULL,
  `part_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `model_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `make` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`product_id`),
  KEY `tax_type_id` (`tax_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_item_general`
--

CREATE TABLE `product_item_general` (
  `product_general_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `unit_price` double NOT NULL,
  PRIMARY KEY (`product_general_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profile`
--

CREATE TABLE `profile` (
  `profile_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `middle_name` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `last_name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `personal_email` varchar(320) CHARACTER SET latin1 DEFAULT NULL,
  `work_phone` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `home_phone` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `mobile` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `branch_id` int(10) unsigned NOT NULL,
  `reports_to` int(10) unsigned DEFAULT NULL,
  `primary_role` int(11) DEFAULT NULL,
  `employee_number` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pf_number` int(100) DEFAULT NULL,
  `esi_number` int(100) DEFAULT NULL,
  `ledger_id` int(11) DEFAULT NULL,
  `blood_group` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `advance_ledger_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`profile_id`),
  KEY `primary_role` (`primary_role`),
  KEY `reports_to` (`reports_to`),
  KEY `branch_id` (`branch_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase`
--

CREATE TABLE `purchase` (
  `purchase_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` int(10) unsigned NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `branch_id` int(10) unsigned NOT NULL,
  `vendor_id` int(10) unsigned NOT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `delivery_terms` text COLLATE utf8_unicode_ci,
  `payment_terms` text COLLATE utf8_unicode_ci,
  `vendor_quote_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `s_ledger_ids` text COLLATE utf8_unicode_ci,
  `discount_amount` decimal(20,2) DEFAULT NULL,
  `freight_amount` decimal(10,0) DEFAULT NULL,
  `type` tinyint(4) NOT NULL,
  PRIMARY KEY (`purchase_id`),
  KEY `created_by` (`created_by`,`branch_id`,`vendor_id`),
  KEY `branch_id` (`branch_id`),
  KEY `vendor_id` (`vendor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_item`
--

CREATE TABLE `purchase_item` (
  `purchase_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `purhcase_id` int(10) unsigned NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `quantity` float(10,2) NOT NULL,
  `tax_type_id` int(10) unsigned DEFAULT NULL,
  `item_description` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `product_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`purchase_item_id`),
  KEY `purhcase_id` (`purhcase_id`,`tax_type_id`),
  KEY `purhcase_id_2` (`purhcase_id`),
  KEY `tax_type_id` (`tax_type_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_item_others`
--

CREATE TABLE `purchase_item_others` (
  `purchase_item_others_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `purchase_id` int(10) unsigned DEFAULT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `quantity` float(10,2) NOT NULL,
  `tax_type_id` int(10) unsigned DEFAULT NULL,
  `item_description` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`purchase_item_others_id`),
  KEY `tax_type_id` (`tax_type_id`),
  KEY `purchase_id` (`purchase_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_return`
--

CREATE TABLE `purchase_return` (
  `purchase_return_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` int(10) unsigned NOT NULL,
  `date` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `s_ledger_entry_ids` text COLLATE utf8_unicode_ci,
  `customer_purchase_return_reference` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`purchase_return_id`),
  KEY `purchase_id` (`purchase_id`,`created_by`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_return_item`
--

CREATE TABLE `purchase_return_item` (
  `purchase_return_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_return_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `tax_type_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`purchase_return_item_id`),
  KEY `purchase_return_id` (`purchase_return_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quote`
--

CREATE TABLE `quote` (
  `quote_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created` int(11) NOT NULL,
  `branch_id` int(10) unsigned NOT NULL,
  `assigned_to` int(10) unsigned NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `to_type` tinyint(1) NOT NULL,
  `to_type_id` int(10) unsigned NOT NULL,
  `contact_id` int(10) unsigned DEFAULT NULL,
  `subject` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `delivery_terms` text COLLATE utf8_unicode_ci,
  `payment_terms` text COLLATE utf8_unicode_ci,
  `internal_notes` text COLLATE utf8_unicode_ci,
  `updated` int(11) DEFAULT NULL,
  `date` int(11) NOT NULL,
  `campaign_id` int(10) unsigned DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT NULL,
  `quote_status_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`quote_id`),
  KEY `campaign_id` (`campaign_id`),
  KEY `assigned_to` (`assigned_to`),
  KEY `created_by` (`created_by`),
  KEY `quote_status_id` (`quote_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quote_item`
--

CREATE TABLE `quote_item` (
  `quote_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `quote_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `unit_price` double NOT NULL,
  `quantity` double NOT NULL,
  `tax_type_id` int(10) unsigned NOT NULL,
  `description` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`quote_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quote_status`
--

CREATE TABLE `quote_status` (
  `quote_status_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `context` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`quote_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `receipt`
--

CREATE TABLE `receipt` (
  `receipt_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mode` tinyint(1) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` int(10) unsigned NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `branch_id` int(10) unsigned NOT NULL,
  `type` tinyint(1) unsigned NOT NULL,
  `type_id` int(10) unsigned DEFAULT NULL,
  `s_fa_ledger_entry_ids` text COLLATE utf8_unicode_ci,
  `indirect_income_ledger_id` int(11) unsigned DEFAULT NULL,
  `mode_account_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`receipt_id`),
  KEY `indirect_income_ledger_id` (`indirect_income_ledger_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `receipt_bank`
--

CREATE TABLE `receipt_bank` (
  `receipt_bank_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `receipt_id` int(11) unsigned NOT NULL,
  `bank_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `bank_branch` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `instrument_account_no` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `instrument_date` int(11) NOT NULL,
  `instrument_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `reconciliation_date` int(11) DEFAULT NULL,
  `returned` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`receipt_bank_id`),
  KEY `receipt_id` (`receipt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resource`
--

CREATE TABLE `resource` (
  `resource_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET latin1 NOT NULL,
  `description` varchar(250) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'role id',
  `name` varchar(50) CHARACTER SET latin1 NOT NULL COMMENT 'role name',
  `description` varchar(250) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_return`
--

CREATE TABLE `sales_return` (
  `sales_return_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) unsigned NOT NULL,
  `date` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `s_ledger_entry_ids` text COLLATE utf8_unicode_ci,
  `customer_sales_return_reference` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`sales_return_id`),
  KEY `invoice_id` (`invoice_id`,`created_by`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_return_item`
--

CREATE TABLE `sales_return_item` (
  `sales_return_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sales_return_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `tax_type_id` int(11) DEFAULT NULL,
  `item_description` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`sales_return_item_id`),
  KEY `sales_return_id` (`sales_return_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_stage`
--

CREATE TABLE `sales_stage` (
  `sales_stage_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `description` varchar(250) CHARACTER SET latin1 DEFAULT NULL,
  `context` int(1) NOT NULL,
  PRIMARY KEY (`sales_stage_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `salutation`
--

CREATE TABLE `salutation` (
  `salutation_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET latin1 NOT NULL,
  `description` varchar(250) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`salutation_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `saved_search`
--

CREATE TABLE `saved_search` (
  `saved_search_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `type` tinyint(1) NOT NULL,
  `s_criteria` text COLLATE utf8_unicode_ci NOT NULL,
  `created` int(11) NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  PRIMARY KEY (`saved_search_id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_item`
--

CREATE TABLE `service_item` (
  `service_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `amount` decimal(20,2) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `tax_type_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`service_item_id`),
  KEY `tax_type_id` (`tax_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timezone` varchar(50) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `site_email`
--

CREATE TABLE `site_email` (
  `site_email_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(320) CHARACTER SET latin1 NOT NULL,
  `from_name` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT 'BizSense Site Mailer',
  `transport` varchar(50) CHARACTER SET latin1 NOT NULL,
  `smtp_server` varchar(250) CHARACTER SET latin1 DEFAULT NULL,
  `smtp_require_auth` tinyint(1) DEFAULT NULL,
  `smtp_auth` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `smtp_username` varchar(250) CHARACTER SET latin1 DEFAULT NULL,
  `smtp_password` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `smtp_secure_connection` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `smtp_port` int(11) DEFAULT NULL,
  `footer` longtext CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`site_email_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscriber`
--

CREATE TABLE `subscriber` (
  `subscriber_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `middle_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `format` int(1) NOT NULL,
  `status` int(1) unsigned NOT NULL,
  `blocked_timestamp` int(10) unsigned NOT NULL,
  `bounce_count` int(10) unsigned NOT NULL,
  `domain` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`subscriber_id`),
  KEY `format` (`format`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `task`
--

CREATE TABLE `task` (
  `task_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `start_date` int(11) DEFAULT NULL,
  `end_date` int(11) DEFAULT NULL,
  `assigned_to` int(10) unsigned DEFAULT NULL,
  `created` int(11) DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `task_status_id` int(10) unsigned NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `reminder` tinyint(2) NOT NULL,
  `reminder_sent` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`task_id`),
  KEY `assigned_to` (`assigned_to`),
  KEY `task_status_id` (`task_status_id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `task_notes`
--

CREATE TABLE `task_notes` (
  `task_notes_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(10) unsigned NOT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `created` int(11) NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  PRIMARY KEY (`task_notes_id`),
  KEY `task_id` (`task_id`,`created_by`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `task_status`
--

CREATE TABLE `task_status` (
  `task_status_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `closed_context` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`task_status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tax_type`
--

CREATE TABLE `tax_type` (
  `tax_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `percentage` double NOT NULL,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `has_add_on` tinyint(1) NOT NULL DEFAULT '0',
  `fa_ledger_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`tax_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket`
--

CREATE TABLE `ticket` (
  `ticket_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) CHARACTER SET utf8 NOT NULL,
  `description` text CHARACTER SET utf8,
  `contact_id` int(10) unsigned NOT NULL,
  `created` int(11) DEFAULT NULL,
  `ticket_status_id` int(10) unsigned NOT NULL,
  `assigned_to` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`ticket_id`),
  KEY `contact_id` (`contact_id`),
  KEY `ticket_status_id` (`ticket_status_id`),
  KEY `assigned_to` (`assigned_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_comment`
--

CREATE TABLE `ticket_comment` (
  `ticket_comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` int(10) unsigned NOT NULL,
  `title` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8,
  `created` int(11) NOT NULL,
  `created_by_type` int(11) NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`ticket_comment_id`),
  KEY `ticket_id` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_status`
--

CREATE TABLE `ticket_status` (
  `ticket_status_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `closed_context` int(11) NOT NULL,
  PRIMARY KEY (`ticket_status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `url_access`
--

CREATE TABLE `url_access` (
  `url_access_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'urlAccessId',
  `url` varchar(100) CHARACTER SET latin1 NOT NULL COMMENT 'URL module/controller/action',
  `privilege_name` varchar(500) CHARACTER SET latin1 DEFAULT NULL,
  `assertion_class` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`url_access_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(320) CHARACTER SET latin1 NOT NULL COMMENT 'username',
  `password` varchar(50) CHARACTER SET latin1 NOT NULL COMMENT 'password',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'block status',
  `mode` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'pw req+',
  `hash` varchar(32) CHARACTER SET latin1 NOT NULL,
  `created` int(11) NOT NULL COMMENT 'when account was created',
  `alt_email` varchar(150) CHARACTER SET latin1 NOT NULL COMMENT 'alternate email',
  `host_created` varchar(250) CHARACTER SET latin1 NOT NULL COMMENT 'hostname',
  `ip_created` varchar(20) CHARACTER SET latin1 NOT NULL COMMENT 'IP address',
  `last_access_ip` varchar(32) CHARACTER SET latin1 DEFAULT NULL,
  `last_access_host` varchar(60) CHARACTER SET latin1 DEFAULT NULL,
  `accessed` int(11) DEFAULT NULL,
  `type` int(11) NOT NULL DEFAULT '1',
  `user_timezone` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_role`
--

CREATE TABLE `user_role` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) NOT NULL,
  KEY `role_id` (`role_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `variable`
--

CREATE TABLE `variable` (
  `name` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `value` longtext COLLATE utf8_unicode_ci NOT NULL,
  `variable_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`variable_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendor`
--

CREATE TABLE `vendor` (
  `vendor_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `company_name` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `addresss_line_1` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `addresss_line_2` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `addresss_line_3` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `addresss_line_4` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `postal_code` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(320) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ledger_id` int(10) unsigned DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `type` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`vendor_id`),
  KEY `ledger_id` (`ledger_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ws_access`
--

CREATE TABLE `ws_access` (
  `ws_access_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ws_application_id` int(10) unsigned NOT NULL,
  `ws_access_timestamp` int(10) unsigned NOT NULL,
  `success` int(11) NOT NULL,
  PRIMARY KEY (`ws_access_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ws_application`
--

CREATE TABLE `ws_application` (
  `ws_application_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) CHARACTER SET latin1 NOT NULL,
  `api_key` varchar(40) CHARACTER SET latin1 NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ws_application_id`),
  UNIQUE KEY `api_key` (`api_key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ws_ss_client`
--

CREATE TABLE `ws_ss_client` (
  `ws_ss_client_id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(300) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`ws_ss_client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `access`
--
ALTER TABLE `access`
  ADD CONSTRAINT `access_ibfk_3` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `access_ibfk_4` FOREIGN KEY (`privilege_id`) REFERENCES `privilege` (`privilege_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `account`
--
ALTER TABLE `account`
  ADD CONSTRAINT `account_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `account_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `account_ibfk_3` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`branch_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `account_ibfk_4` FOREIGN KEY (`ledger_id`) REFERENCES `fa_ledger` (`fa_ledger_id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `account_ibfk_5` FOREIGN KEY (`campaign_id`) REFERENCES `campaign` (`campaign_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `account_notes`
--
ALTER TABLE `account_notes`
  ADD CONSTRAINT `account_notes_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `account` (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `account_notes_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `bank_transaction`
--
ALTER TABLE `bank_transaction`
  ADD CONSTRAINT `bank_transaction_ibfk_1` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_account` (`bank_account_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `branch`
--
ALTER TABLE `branch`
  ADD CONSTRAINT `branch_ibfk_1` FOREIGN KEY (`branch_manager`) REFERENCES `user` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `branch_ibfk_2` FOREIGN KEY (`parent_branch_id`) REFERENCES `branch` (`branch_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `call`
--
ALTER TABLE `call`
  ADD CONSTRAINT `call_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `call_ibfk_3` FOREIGN KEY (`call_status_id`) REFERENCES `call_status` (`call_status_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `call_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `call_notes`
--
ALTER TABLE `call_notes`
  ADD CONSTRAINT `call_notes_ibfk_1` FOREIGN KEY (`call_id`) REFERENCES `call` (`call_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `call_notes_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `campaign`
--
ALTER TABLE `campaign`
  ADD CONSTRAINT `campaign_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `campaign_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `campaign_ibfk_3` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`branch_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cash_account`
--
ALTER TABLE `cash_account`
  ADD CONSTRAINT `cash_account_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`branch_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cash_account_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cash_account_ibfk_3` FOREIGN KEY (`fa_ledger_id`) REFERENCES `fa_ledger` (`fa_ledger_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `contact`
--
ALTER TABLE `contact`
  ADD CONSTRAINT `contact_ibfk_1` FOREIGN KEY (`ledger_id`) REFERENCES `fa_ledger` (`fa_ledger_id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `contact_ibfk_2` FOREIGN KEY (`campaign_id`) REFERENCES `campaign` (`campaign_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `contact_notes`
--
ALTER TABLE `contact_notes`
  ADD CONSTRAINT `contact_notes_ibfk_1` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`contact_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `contact_notes_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `fa_group`
--
ALTER TABLE `fa_group`
  ADD CONSTRAINT `fa_group_ibfk_1` FOREIGN KEY (`fa_group_category_id`) REFERENCES `fa_group` (`fa_group_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `fa_ledger`
--
ALTER TABLE `fa_ledger`
  ADD CONSTRAINT `fa_ledger_ibfk_1` FOREIGN KEY (`fa_group_id`) REFERENCES `fa_group` (`fa_group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fa_ledger_ibfk_2` FOREIGN KEY (`opening_balance_ledger_entry_id`) REFERENCES `fa_ledger_entry` (`fa_ledger_entry_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `fa_ledger_entry`
--
ALTER TABLE `fa_ledger_entry`
  ADD CONSTRAINT `fa_ledger_entry_ibfk_1` FOREIGN KEY (`fa_ledger_id`) REFERENCES `fa_ledger` (`fa_ledger_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`contact_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `invoice_ibfk_2` FOREIGN KEY (`campaign_id`) REFERENCES `campaign` (`campaign_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `invoice_item`
--
ALTER TABLE `invoice_item`
  ADD CONSTRAINT `invoice_item_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoice` (`invoice_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `invoice_service_item`
--
ALTER TABLE `invoice_service_item`
  ADD CONSTRAINT `invoice_service_item_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoice` (`invoice_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `invoice_service_item_ibfk_2` FOREIGN KEY (`service_item_id`) REFERENCES `service_item` (`service_item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `invoice_service_item_ibfk_3` FOREIGN KEY (`tax_type_id`) REFERENCES `tax_type` (`tax_type_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lead`
--
ALTER TABLE `lead`
  ADD CONSTRAINT `lead_ibfk_2` FOREIGN KEY (`lead_status_id`) REFERENCES `lead_status` (`lead_status_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lead_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `lead_ibfk_5` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`branch_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lead_ibfk_6` FOREIGN KEY (`lead_source_id`) REFERENCES `lead_source` (`lead_source_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lead_ibfk_7` FOREIGN KEY (`campaign_id`) REFERENCES `campaign` (`campaign_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `lead_notes`
--
ALTER TABLE `lead_notes`
  ADD CONSTRAINT `lead_notes_ibfk_1` FOREIGN KEY (`lead_id`) REFERENCES `lead` (`lead_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lead_notes_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `list`
--
ALTER TABLE `list`
  ADD CONSTRAINT `list_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `list_subscriber`
--
ALTER TABLE `list_subscriber`
  ADD CONSTRAINT `list_subscriber_ibfk_1` FOREIGN KEY (`list_id`) REFERENCES `list` (`list_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `list_subscriber_ibfk_2` FOREIGN KEY (`subscriber_id`) REFERENCES `subscriber` (`subscriber_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `meeting`
--
ALTER TABLE `meeting`
  ADD CONSTRAINT `meeting_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `meeting_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `meeting_ibfk_3` FOREIGN KEY (`meeting_status_id`) REFERENCES `meeting_status` (`meeting_status_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `meeting_attendee`
--
ALTER TABLE `meeting_attendee`
  ADD CONSTRAINT `meeting_attendee_ibfk_1` FOREIGN KEY (`meeting_id`) REFERENCES `meeting` (`meeting_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `meeting_notes`
--
ALTER TABLE `meeting_notes`
  ADD CONSTRAINT `meeting_notes_ibfk_1` FOREIGN KEY (`meeting_id`) REFERENCES `meeting` (`meeting_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `meeting_notes_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `message_ibfk_2` FOREIGN KEY (`campaign_id`) REFERENCES `campaign` (`campaign_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `message_queue`
--
ALTER TABLE `message_queue`
  ADD CONSTRAINT `message_queue_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `message` (`message_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `message_queue_ibfk_2` FOREIGN KEY (`list_id`) REFERENCES `list` (`list_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `message_queue_ibfk_3` FOREIGN KEY (`subscriber_id`) REFERENCES `subscriber` (`subscriber_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `opportunity`
--
ALTER TABLE `opportunity`
  ADD CONSTRAINT `opportunity_ibfk_1` FOREIGN KEY (`lead_source_id`) REFERENCES `lead_source` (`lead_source_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `opportunity_ibfk_2` FOREIGN KEY (`sales_stage_id`) REFERENCES `sales_stage` (`sales_stage_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `opportunity_ibfk_3` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`branch_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `opportunity_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `opportunity_ibfk_5` FOREIGN KEY (`campaign_id`) REFERENCES `campaign` (`campaign_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `opportunity_contact`
--
ALTER TABLE `opportunity_contact`
  ADD CONSTRAINT `opportunity_contact_ibfk_1` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`contact_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `opportunity_contact_ibfk_2` FOREIGN KEY (`opportunity_id`) REFERENCES `opportunity` (`opportunity_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payment_ibfk_2` FOREIGN KEY (`indirect_expense_ledger_id`) REFERENCES `fa_ledger` (`fa_ledger_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payment_bank`
--
ALTER TABLE `payment_bank`
  ADD CONSTRAINT `payment_bank_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `payment` (`payment_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payment_tds`
--
ALTER TABLE `payment_tds`
  ADD CONSTRAINT `payment_tds_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `payment` (`payment_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payslip`
--
ALTER TABLE `payslip`
  ADD CONSTRAINT `payslip_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payslip_item`
--
ALTER TABLE `payslip_item`
  ADD CONSTRAINT `payslip_item_ibfk_1` FOREIGN KEY (`payslip_field_id`) REFERENCES `payslip_field` (`payslip_field_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payslip_item_ibfk_2` FOREIGN KEY (`payslip_id`) REFERENCES `payslip` (`payslip_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `profile`
--
ALTER TABLE `profile`
  ADD CONSTRAINT `profile_ibfk_2` FOREIGN KEY (`reports_to`) REFERENCES `user` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `profile_ibfk_3` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`branch_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `profile_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `purchase`
--
ALTER TABLE `purchase`
  ADD CONSTRAINT `purchase_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`branch_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_ibfk_3` FOREIGN KEY (`vendor_id`) REFERENCES `vendor` (`vendor_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `purchase_item`
--
ALTER TABLE `purchase_item`
  ADD CONSTRAINT `purchase_item_ibfk_1` FOREIGN KEY (`purhcase_id`) REFERENCES `purchase` (`purchase_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_item_ibfk_2` FOREIGN KEY (`tax_type_id`) REFERENCES `tax_type` (`tax_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_item_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `purchase_item_others`
--
ALTER TABLE `purchase_item_others`
  ADD CONSTRAINT `purchase_item_others_ibfk_1` FOREIGN KEY (`tax_type_id`) REFERENCES `tax_type` (`tax_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_item_others_ibfk_2` FOREIGN KEY (`purchase_id`) REFERENCES `purchase` (`purchase_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `purchase_return`
--
ALTER TABLE `purchase_return`
  ADD CONSTRAINT `purchase_return_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchase` (`purchase_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_return_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `purchase_return_item`
--
ALTER TABLE `purchase_return_item`
  ADD CONSTRAINT `purchase_return_item_ibfk_1` FOREIGN KEY (`purchase_return_id`) REFERENCES `purchase_return` (`purchase_return_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `quote`
--
ALTER TABLE `quote`
  ADD CONSTRAINT `quote_ibfk_1` FOREIGN KEY (`campaign_id`) REFERENCES `campaign` (`campaign_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `quote_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `quote_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `quote_ibfk_4` FOREIGN KEY (`quote_status_id`) REFERENCES `quote_status` (`quote_status_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `receipt`
--
ALTER TABLE `receipt`
  ADD CONSTRAINT `receipt_ibfk_1` FOREIGN KEY (`indirect_income_ledger_id`) REFERENCES `fa_ledger` (`fa_ledger_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `receipt_bank`
--
ALTER TABLE `receipt_bank`
  ADD CONSTRAINT `receipt_bank_ibfk_1` FOREIGN KEY (`receipt_id`) REFERENCES `receipt` (`receipt_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sales_return`
--
ALTER TABLE `sales_return`
  ADD CONSTRAINT `sales_return_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoice` (`invoice_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sales_return_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sales_return_item`
--
ALTER TABLE `sales_return_item`
  ADD CONSTRAINT `sales_return_item_ibfk_1` FOREIGN KEY (`sales_return_id`) REFERENCES `sales_return` (`sales_return_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `saved_search`
--
ALTER TABLE `saved_search`
  ADD CONSTRAINT `saved_search_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `service_item`
--
ALTER TABLE `service_item`
  ADD CONSTRAINT `service_item_ibfk_1` FOREIGN KEY (`tax_type_id`) REFERENCES `tax_type` (`tax_type_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `task`
--
ALTER TABLE `task`
  ADD CONSTRAINT `task_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `task_ibfk_2` FOREIGN KEY (`task_status_id`) REFERENCES `task_status` (`task_status_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `task_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `task_notes`
--
ALTER TABLE `task_notes`
  ADD CONSTRAINT `task_notes_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `task` (`task_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `task_notes_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ticket`
--
ALTER TABLE `ticket`
  ADD CONSTRAINT `ticket_ibfk_1` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`contact_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ticket_ibfk_2` FOREIGN KEY (`ticket_status_id`) REFERENCES `ticket_status` (`ticket_status_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ticket_ibfk_3` FOREIGN KEY (`assigned_to`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ticket_comment`
--
ALTER TABLE `ticket_comment`
  ADD CONSTRAINT `ticket_comment_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`ticket_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_role`
--
ALTER TABLE `user_role`
  ADD CONSTRAINT `user_role_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_role_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `vendor`
--
ALTER TABLE `vendor`
  ADD CONSTRAINT `vendor_ibfk_1` FOREIGN KEY (`ledger_id`) REFERENCES `fa_ledger` (`fa_ledger_id`) ON DELETE SET NULL ON UPDATE CASCADE;
