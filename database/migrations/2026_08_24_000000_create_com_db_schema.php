<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const TABLES = [
        'address_cities',
        'address_countries',
        'address_districts',
        'address_provinces',
        'address_states',
        'api_logs',
        'autonumbers',
        'branches',
        'branch_recipients',
        'configurations',
        'consignees',
        'container_broken_images',
        'container_datas',
        'container_histories',
        'container_logs',
        'customers',
        'divisions',
        'do_details',
        'do_headers',
        'employees',
        'employee_positions',
        'expeditions',
        'failed_jobs',
        'hierarchies',
        'items',
        'item_categories',
        'item_units',
        'menus',
        'model_has_permissions',
        'model_has_roles',
        'oauth_access_tokens',
        'oauth_auth_codes',
        'oauth_clients',
        'oauth_personal_access_clients',
        'oauth_refresh_tokens',
        'password_resets',
        'permissions',
        'personal_access_tokens',
        'roles',
        'role_has_permissions',
        'shipping_instruction_details',
        'shipping_instruction_headers',
        'shipping_instruction_monitors',
        'shipping_instruction_uploads',
        'statuses',
        'timbangan_datas',
        'unit_materials',
        'users',
        'user_branches',
        'vehicles',
        'warehouses',
    ];

    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            DB::unprepared(<<<'SQL'
CREATE TABLE `address_cities` (
  `id` int(11) NOT NULL,
  `code` varchar(10) DEFAULT NULL,
  `province_id` int(11) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `name` varchar(60) DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `address_countries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone_code` varchar(255) DEFAULT NULL,
  `currency_code` varchar(255) DEFAULT NULL,
  `currency_symbol` varchar(255) DEFAULT NULL,
  `lang_code` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `address_districts` (
  `id` int(11) NOT NULL,
  `code` varchar(10) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `address_provinces` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) DEFAULT NULL,
  `name` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `address_states` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `country_id` int(10) unsigned NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `states_country_id_foreign` (`country_id`),
  CONSTRAINT `states_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `address_countries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `api_logs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(255) DEFAULT NULL,
  `json_request` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `autonumbers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `doc_code` varchar(50) DEFAULT NULL,
  `next_no` bigint(20) DEFAULT NULL,
  `current_month` int(11) DEFAULT NULL,
  `current_year` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `branches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `address` longtext DEFAULT NULL,
  `latitude` decimal(8,6) DEFAULT NULL,
  `longitude` decimal(9,6) DEFAULT NULL,
  `parent_id` bigint(20) DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `country_id` int(10) unsigned DEFAULT NULL,
  `country_name` varchar(200) DEFAULT NULL,
  `province_id` int(10) unsigned DEFAULT NULL,
  `province_name` varchar(200) DEFAULT NULL,
  `city_id` int(10) unsigned DEFAULT NULL,
  `city_name` varchar(200) DEFAULT NULL,
  `district_id` int(10) unsigned DEFAULT NULL,
  `district_name` varchar(200) DEFAULT NULL,
  `status_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `free_storage_duration` int(11) DEFAULT 0,
  `free_demurrage_duration` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `FK_branches_status_id_idx` (`status_id`),
  KEY `FK_branches_created_by_idx` (`created_by`),
  KEY `FK_branches_updated_by_idx` (`updated_by`),
  CONSTRAINT `FK_branches_status_id` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`),
  CONSTRAINT `FK_branches_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `branch_recipients` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) DEFAULT NULL,
  `code` varchar(100) DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `status_id` int(11) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code_UNIQUE` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `configurations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data_key` longtext DEFAULT NULL,
  `value` longtext DEFAULT NULL,
  `value_int` decimal(18,4) DEFAULT NULL,
  `branch_id` bigint(20) DEFAULT NULL,
  `value_2` longtext DEFAULT NULL,
  `value_3` longtext DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `consignees` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `province_id` int(11) DEFAULT NULL,
  `province_name` varchar(200) DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `status_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_consignees_branch_id_idx` (`branch_id`),
  KEY `FK_consignees_created_by_idx` (`created_by`),
  KEY `FK_consignees_updated_by_idx` (`updated_by`),
  KEY `FK_consignees_province_id_idx` (`province_id`),
  CONSTRAINT `FK_consignees_branch_id` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_consignees_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_consignees_province_id` FOREIGN KEY (`province_id`) REFERENCES `address_provinces` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_consignees_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `container_broken_images` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) DEFAULT NULL,
  `container_id` bigint(20) DEFAULT NULL,
  `filename` varchar(200) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_container_broken_images_container_id_idx` (`container_id`),
  CONSTRAINT `FK_container_broken_images_container_id` FOREIGN KEY (`container_id`) REFERENCES `container_datas` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `container_datas` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `warehouse_id` bigint(20) DEFAULT NULL,
  `do_id` bigint(20) DEFAULT NULL,
  `do_code` varchar(100) DEFAULT NULL,
  `do_type` varchar(100) DEFAULT NULL,
  `timbangan_id` bigint(20) DEFAULT NULL,
  `timbangan_code` varchar(255) DEFAULT NULL,
  `timbangan_ceramic_code` varchar(255) DEFAULT NULL,
  `status_id` int(11) DEFAULT NULL,
  `warehouse_location_id` int(11) DEFAULT 13,
  `warehouse_location_status` varchar(255) DEFAULT 'ON DELIVERY',
  `warehouse_area` varchar(45) DEFAULT 'B',
  `warehouse_area_row` varchar(45) DEFAULT 'B1',
  `warehouse_status_id` int(11) DEFAULT 3,
  `warehouse_status` varchar(255) DEFAULT 'FULL',
  `warehouse_status_reason` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  `si_code` varchar(100) DEFAULT NULL,
  `si_destination_id` bigint(20) DEFAULT NULL,
  `si_destination_name` varchar(100) DEFAULT NULL,
  `si_etd_date` datetime DEFAULT NULL,
  `si_eta_date` datetime DEFAULT NULL,
  `free_storage_start_at` datetime DEFAULT NULL,
  `free_storage_end_at` datetime DEFAULT NULL,
  `free_demurrage_start_at` datetime DEFAULT NULL,
  `free_demurrage_end_at` datetime DEFAULT NULL,
  `last_status_updated_at` datetime DEFAULT NULL,
  `received_at` datetime DEFAULT NULL,
  `is_over_storage` tinyint(1) DEFAULT 0,
  `returned_at` datetime DEFAULT NULL,
  `is_over_demurrage` tinyint(1) DEFAULT 0,
  `free_storage_duration` int(11) DEFAULT 0,
  `free_demurrage_duration` int(11) DEFAULT 0,
  `is_storage_monitor_start` tinyint(1) DEFAULT 0,
  `is_storage_monitor_end` tinyint(1) DEFAULT 0,
  `is_demurrage_monitor_start` tinyint(1) DEFAULT 0,
  `is_demurrage_monitor_end` tinyint(1) DEFAULT 0,
  `is_sync` tinyint(1) DEFAULT 0,
  `dooring_at` datetime DEFAULT NULL,
  `item_name` varchar(200) DEFAULT NULL,
  `berthing_at` datetime DEFAULT NULL,
  `vessel_name` varchar(200) DEFAULT NULL,
  `semen_out_at` datetime DEFAULT NULL,
  `ceramic_received_at` datetime DEFAULT NULL,
  `ceramic_out_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `container_histories` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `do_id` bigint(20) DEFAULT NULL,
  `do_code` varchar(100) DEFAULT NULL,
  `do_type` varchar(100) DEFAULT NULL,
  `item_name` varchar(200) DEFAULT NULL,
  `vessel_name` varchar(200) DEFAULT NULL,
  `timbangan_id` bigint(20) DEFAULT NULL,
  `timbangan_code` varchar(255) DEFAULT NULL,
  `timbangan_ceramic_code` varchar(255) DEFAULT NULL,
  `si_code` varchar(100) DEFAULT NULL,
  `si_destination_id` bigint(20) DEFAULT NULL,
  `si_destination_name` varchar(100) DEFAULT NULL,
  `si_etd_date` datetime DEFAULT NULL,
  `si_eta_date` datetime DEFAULT NULL,
  `free_storage_duration` int(11) DEFAULT 0,
  `free_demurrage_duration` int(11) DEFAULT 0,
  `free_storage_start_at` datetime DEFAULT NULL,
  `free_storage_end_at` datetime DEFAULT NULL,
  `free_demurrage_start_at` datetime DEFAULT NULL,
  `free_demurrage_end_at` datetime DEFAULT NULL,
  `is_over_storage` tinyint(1) DEFAULT 0,
  `is_over_demurrage` tinyint(1) DEFAULT 0,
  `berthing_at` datetime DEFAULT NULL,
  `dooring_at` datetime DEFAULT NULL,
  `received_at` datetime DEFAULT NULL,
  `returned_at` datetime DEFAULT NULL,
  `semen_out_at` datetime DEFAULT NULL,
  `ceramic_received_at` datetime DEFAULT NULL,
  `ceramic_out_at` datetime DEFAULT NULL,
  `days_since_berthing` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `container_logs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) DEFAULT NULL,
  `container_id` bigint(20) DEFAULT NULL,
  `reference_id` bigint(20) DEFAULT NULL,
  `reference_code` varchar(200) DEFAULT NULL,
  `reference_type` varchar(200) DEFAULT NULL,
  `transaction_description` longtext DEFAULT NULL,
  `transaction_date` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `customers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `guid` longtext DEFAULT NULL,
  `name` longtext DEFAULT NULL,
  `phone` longtext DEFAULT NULL,
  `email` longtext DEFAULT NULL,
  `pic` varchar(200) DEFAULT NULL,
  `is_supplier` varchar(200) DEFAULT NULL,
  `is_from_app` int(11) DEFAULT 0,
  `birthdate` timestamp NULL DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `sex` varchar(50) DEFAULT NULL,
  `marital_status` varchar(50) DEFAULT NULL,
  `img_path` varchar(200) DEFAULT NULL,
  `address` longtext DEFAULT NULL,
  `ktp` varchar(50) DEFAULT NULL,
  `identity_address` longtext DEFAULT NULL,
  `status_id` int(11) DEFAULT NULL,
  `is_client` int(11) DEFAULT 0,
  `password` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `forgot_password_otp_code` varchar(45) DEFAULT NULL,
  `forgot_password_otp_expired_at` timestamp NULL DEFAULT NULL,
  `forgot_password_unique_code` varchar(45) DEFAULT NULL,
  `otp_code` varchar(20) DEFAULT NULL,
  `pin` longtext DEFAULT NULL,
  `is_thirdparty_auth` int(11) DEFAULT 0,
  `thirdparty_name` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_patients_status_id_idx` (`status_id`),
  CONSTRAINT `FK_patients_status_id` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `divisions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `status_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_divisions_branch_id_idx` (`branch_id`),
  KEY `FK_divisions_status_id_idx` (`status_id`),
  KEY `FK_divisions_created_by_idx` (`created_by`),
  KEY `FK_divisions_updated_by_idx` (`updated_by`),
  CONSTRAINT `FK_divisions_branch_id` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `FK_divisions_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_divisions_status_id` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`),
  CONSTRAINT `FK_divisions_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `do_details` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `header_id` bigint(20) DEFAULT NULL,
  `transfer_no` varchar(100) DEFAULT NULL,
  `item_id` bigint(20) DEFAULT NULL,
  `item_code` varchar(100) DEFAULT NULL,
  `item_name` varchar(200) DEFAULT NULL,
  `qty` float DEFAULT 0,
  `unit` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_do_details_header_id_idx` (`header_id`),
  KEY `IDX_do_details_item_id_idx` (`item_id`),
  KEY `IDX_do_details_item_code_idx` (`item_code`),
  CONSTRAINT `FK_do_details_header_id` FOREIGN KEY (`header_id`) REFERENCES `do_headers` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `do_headers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) DEFAULT NULL,
  `code` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `type` varchar(30) DEFAULT NULL,
  `date` timestamp NULL DEFAULT NULL,
  `so_code` varchar(100) DEFAULT NULL,
  `dr_code` varchar(100) DEFAULT NULL,
  `customer_code` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `customer_name` varchar(200) DEFAULT NULL,
  `branch_destination_id` int(11) DEFAULT NULL,
  `shipto_code` varchar(100) DEFAULT NULL,
  `shipto_name` varchar(200) DEFAULT NULL,
  `memo` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `shipping_company` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `do_pengalihan` varchar(200) DEFAULT NULL,
  `si_id` bigint(20) DEFAULT NULL,
  `si_code` varchar(100) DEFAULT NULL,
  `is_close` tinyint(1) DEFAULT NULL,
  `closed_by_si_id` bigint(20) DEFAULT NULL,
  `container1_id` bigint(20) DEFAULT NULL,
  `container1_code` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `container2_id` bigint(20) DEFAULT NULL,
  `container2_code` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transportation` varchar(200) DEFAULT NULL,
  `status_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_do_datas_status_id_idx` (`status_id`),
  KEY `IDX_do_headers_code_idx` (`code`),
  KEY `IDX_do_headers_date_idx` (`date`),
  KEY `IDX_do_headers_created_at_idx` (`created_at`),
  CONSTRAINT `FK_do_datas_status_id` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `employees` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `name` longtext DEFAULT NULL,
  `phone` longtext DEFAULT NULL,
  `email` longtext DEFAULT NULL,
  `address` longtext DEFAULT NULL,
  `division_id` bigint(20) DEFAULT NULL,
  `hierarchy_id` bigint(20) DEFAULT NULL,
  `position_id` bigint(20) DEFAULT NULL,
  `npwp` longtext DEFAULT NULL,
  `id_card_no` longtext DEFAULT NULL,
  `join_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `status_id` int(11) DEFAULT NULL,
  `guid` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code_UNIQUE` (`code`),
  KEY `FK_employees_status_id_idx` (`status_id`),
  KEY `FK_employees_branch_id_idx` (`branch_id`),
  KEY `FK_employees_created_by_idx` (`created_by`),
  KEY `FK_employees_updated_by_idx` (`updated_by`),
  KEY `FK_employees_position_id_idx` (`position_id`),
  KEY `FK_employees_division_id_idx` (`division_id`),
  CONSTRAINT `FK_employees_branch_id` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `FK_employees_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_employees_division_id` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`),
  CONSTRAINT `FK_employees_position_id` FOREIGN KEY (`position_id`) REFERENCES `employee_positions` (`id`),
  CONSTRAINT `FK_employees_status_id` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`),
  CONSTRAINT `FK_employees_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `employee_positions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `expeditions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `status_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `hierarchies` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `items` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `guid` longtext DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `barcode` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `image_path` longtext DEFAULT NULL,
  `have_bom` int(11) DEFAULT 0,
  `category_id` int(11) DEFAULT 1,
  `category_2_id` int(11) DEFAULT NULL,
  `status_id` int(11) DEFAULT NULL,
  `price` decimal(20,4) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `img_path` longtext DEFAULT NULL,
  `is_unlimited_stock` int(11) DEFAULT 0,
  `capacity` int(11) DEFAULT NULL,
  `cost_price` decimal(20,4) DEFAULT 0.0000,
  `default_uom_id` bigint(20) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `max_capacity` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `discounted_price` decimal(20,6) DEFAULT NULL,
  `discount` decimal(20,6) DEFAULT NULL,
  `rating` decimal(20,2) DEFAULT NULL,
  `total_qty_sold` varchar(45) DEFAULT NULL,
  `sku_height` decimal(18,4) DEFAULT 0.0000,
  `sku_length` decimal(18,4) DEFAULT 0.0000,
  `sku_width` decimal(18,4) DEFAULT 0.0000,
  `gross_weight` decimal(18,8) DEFAULT 0.00000000,
  `net_weight` decimal(18,8) DEFAULT 0.00000000,
  PRIMARY KEY (`id`),
  KEY `FK_items_branch_id_idx` (`branch_id`),
  KEY `FK_items_created_by_idx` (`created_by`),
  KEY `FK_items_updated_by_idx` (`updated_by`),
  KEY `FK_items_status_id_idx` (`status_id`),
  CONSTRAINT `FK_items_branch_id` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `FK_items_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_items_status_id` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`),
  CONSTRAINT `FK_items_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `item_categories` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_item_categories_branch_id_idx` (`branch_id`),
  CONSTRAINT `FK_item_categories_branch_id` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `item_units` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `item_id` bigint(20) DEFAULT NULL,
  `unit_id` bigint(20) DEFAULT NULL,
  `qty` double DEFAULT NULL,
  `purchase_price` double DEFAULT NULL,
  `selling_price` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_item_units_item_id_idx` (`item_id`),
  KEY `FK_item_units_unit_id_idx` (`unit_id`),
  CONSTRAINT `FK_item_units_item_id` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  CONSTRAINT `FK_item_units_unit_id` FOREIGN KEY (`unit_id`) REFERENCES `unit_materials` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sequence` int(11) DEFAULT NULL,
  `code` varchar(45) DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  `actions` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `scopes` text DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_access_tokens_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `scopes` text DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_auth_codes_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `oauth_clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `secret` varchar(100) DEFAULT NULL,
  `provider` varchar(255) DEFAULT NULL,
  `redirect` text NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_clients_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `oauth_personal_access_clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) NOT NULL,
  `access_token_id` varchar(100) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `shipping_instruction_details` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) DEFAULT NULL,
  `header_id` bigint(20) DEFAULT NULL,
  `header_code` varchar(255) DEFAULT NULL,
  `consignee_id` bigint(20) DEFAULT NULL,
  `consignee_code` varchar(100) DEFAULT NULL,
  `trucking` varchar(200) DEFAULT NULL,
  `delivery_type` varchar(45) DEFAULT NULL,
  `do_id` bigint(20) DEFAULT NULL,
  `do_code` varchar(45) DEFAULT NULL,
  `do_type` varchar(100) DEFAULT NULL,
  `so_code` varchar(100) DEFAULT NULL,
  `dr_code` varchar(100) DEFAULT NULL,
  `shipto_code` varchar(200) DEFAULT NULL,
  `shipto_name` varchar(200) DEFAULT NULL,
  `item_id` bigint(20) DEFAULT NULL,
  `item_code` varchar(100) DEFAULT NULL,
  `item_name` varchar(200) DEFAULT NULL,
  `qty` float DEFAULT 0,
  `real_qty` float DEFAULT 0,
  `unit` varchar(50) DEFAULT NULL,
  `transportation` varchar(200) DEFAULT NULL,
  `container1_id` bigint(20) DEFAULT NULL,
  `container1_code` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `container2_id` bigint(20) DEFAULT NULL,
  `container2_code` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remark` varchar(200) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_si_detail_header_id_idx` (`header_id`),
  CONSTRAINT `FK_si_detail_header_id` FOREIGN KEY (`header_id`) REFERENCES `shipping_instruction_headers` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `shipping_instruction_headers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) DEFAULT NULL,
  `code` varchar(100) DEFAULT NULL,
  `sailing_date` datetime DEFAULT NULL,
  `docking_date` datetime DEFAULT NULL,
  `shipper` varchar(200) DEFAULT NULL,
  `vessel_name` varchar(200) DEFAULT NULL,
  `eta_date` datetime DEFAULT NULL,
  `etd_date` datetime DEFAULT NULL,
  `pic_receiver` varchar(200) DEFAULT NULL,
  `port_receiver` varchar(200) DEFAULT NULL,
  `province_id` bigint(20) DEFAULT NULL,
  `destination` varchar(200) DEFAULT NULL,
  `destination_branch_id` int(11) DEFAULT NULL,
  `status_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  `is_sync` tinyint(1) DEFAULT 0,
  `dooring_at` datetime DEFAULT NULL,
  `received_at` datetime DEFAULT NULL,
  `total_berthing_cont` int(11) DEFAULT 0,
  `total_dooring_cont` int(11) DEFAULT 0,
  `total_received_cont` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `FK_si_headers_status_id_idx` (`status_id`),
  CONSTRAINT `FK_si_headers_status_id` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `shipping_instruction_monitors` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) DEFAULT NULL,
  `header_id` bigint(20) DEFAULT NULL,
  `header_code` varchar(255) DEFAULT NULL,
  `consignee_id` bigint(20) DEFAULT NULL,
  `consignee_code` varchar(100) DEFAULT NULL,
  `trucking` varchar(200) DEFAULT NULL,
  `delivery_type` varchar(45) DEFAULT NULL,
  `do_id` bigint(20) DEFAULT NULL,
  `do_code` varchar(100) DEFAULT NULL,
  `do_date` datetime DEFAULT NULL,
  `do_type` varchar(100) DEFAULT NULL,
  `so_code` varchar(100) DEFAULT NULL,
  `dr_code` varchar(100) DEFAULT NULL,
  `shipto_code` varchar(200) DEFAULT NULL,
  `shipto_name` varchar(200) DEFAULT NULL,
  `item_id` bigint(20) DEFAULT NULL,
  `item_code` varchar(100) DEFAULT NULL,
  `item_name` varchar(200) DEFAULT NULL,
  `qty` float DEFAULT 0,
  `real_qty` float DEFAULT 0,
  `unit` varchar(50) DEFAULT NULL,
  `container_id` bigint(20) DEFAULT NULL,
  `container_code` varchar(100) DEFAULT NULL,
  `transportation` varchar(200) DEFAULT NULL,
  `status_id` int(11) DEFAULT NULL,
  `status_updated_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  `is_sync` tinyint(1) DEFAULT 0,
  `dooring_at` datetime DEFAULT NULL,
  `received_at` datetime DEFAULT NULL,
  `berthing_at` datetime DEFAULT NULL,
  `remark` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_shipping_instruction_monitors_header_id_idx` (`header_id`),
  KEY `FK_shipping_instruction_monitors_status_id_idx` (`status_id`),
  CONSTRAINT `FK_shipping_instruction_monitors_header_id` FOREIGN KEY (`header_id`) REFERENCES `shipping_instruction_headers` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_shipping_instruction_monitors_status_id` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `shipping_instruction_uploads` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `header_id` bigint(20) DEFAULT NULL,
  `filename` text DEFAULT NULL,
  `ext` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_shipping_instruction_uploads_header_id_idx` (`header_id`),
  CONSTRAINT `FK_shipping_instruction_uploads_header_id` FOREIGN KEY (`header_id`) REFERENCES `shipping_instruction_headers` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `display` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `timbangan_datas` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) DEFAULT NULL,
  `code` varchar(100) DEFAULT NULL,
  `container_no` varchar(100) DEFAULT NULL,
  `weight_type` varchar(100) DEFAULT NULL,
  `weight_1` decimal(20,0) DEFAULT NULL,
  `weight_1_datetime` timestamp NULL DEFAULT NULL,
  `weight_2` decimal(20,0) DEFAULT NULL,
  `weight_2_datetime` timestamp NULL DEFAULT NULL,
  `berat_bruto` decimal(20,0) DEFAULT NULL,
  `berat_netto` decimal(20,0) DEFAULT NULL,
  `plat_no` varchar(45) DEFAULT NULL,
  `trado_no` varchar(45) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `driver_name` varchar(200) DEFAULT NULL,
  `out_destination` varchar(200) DEFAULT NULL,
  `status_id` int(11) DEFAULT NULL,
  `customer_code` varchar(200) DEFAULT NULL COMMENT 'nama customer',
  `customer_name` varchar(200) DEFAULT NULL,
  `expedition` varchar(200) DEFAULT NULL,
  `item_name` varchar(200) DEFAULT NULL,
  `pos_in` varchar(200) DEFAULT NULL,
  `pos_out` varchar(200) DEFAULT NULL,
  `udf_01` varchar(200) DEFAULT NULL,
  `udf_02` varchar(200) DEFAULT NULL,
  `udf_03` varchar(200) DEFAULT NULL,
  `udf_04` varchar(200) DEFAULT NULL,
  `udf_05` varchar(200) DEFAULT NULL,
  `is_sync` tinyint(1) DEFAULT 0,
  `is_printed` int(11) DEFAULT NULL,
  `print_counter` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `is_approve` int(11) DEFAULT NULL,
  `approve_by` bigint(20) DEFAULT NULL,
  `approve_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `unit_materials` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `status_id` int(11) DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_unit_materials_branch_id_idx` (`branch_id`),
  KEY `FK_unit_materials_created_by_idx` (`created_by`),
  KEY `FK_unit_materials_updated_by_idx` (`updated_by`),
  KEY `FK_unit_materials_status_id_idx` (`status_id`),
  CONSTRAINT `FK_unit_materials_branch_id` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `FK_unit_materials_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_unit_materials_status_id` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`),
  CONSTRAINT `FK_unit_materials_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `hierarchy_id` bigint(20) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `employee_id` bigint(20) DEFAULT NULL,
  `is_superadmin` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `status_id` int(11) DEFAULT NULL,
  `forgot_password_otp_code` varchar(45) DEFAULT NULL,
  `forgot_password_otp_expired_at` timestamp NULL DEFAULT NULL,
  `forgot_password_unique_code` varchar(45) DEFAULT NULL,
  `otp_code` varchar(20) DEFAULT NULL,
  `sampling_station_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `FK_users_employee_id_idx` (`employee_id`),
  KEY `FK_users_branch_id_idx` (`branch_id`),
  KEY `FK_users_hierarchy_id_idx` (`hierarchy_id`),
  CONSTRAINT `FK_users_branch_id` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `FK_users_employee_id` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `FK_users_hierarchy_id` FOREIGN KEY (`hierarchy_id`) REFERENCES `hierarchies` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `user_branches` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_user_branches_user_id_idx` (`user_id`),
  KEY `FK_user_branches_branch_id_idx` (`branch_id`),
  CONSTRAINT `FK_user_branches_branch_id` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_user_branches_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `vehicles` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `status_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
            );

            DB::unprepared(<<<'SQL'
CREATE TABLE `warehouses` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `status_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_warehouses_branch_id_idx` (`branch_id`),
  KEY `FK_warehouses_created_by_idx` (`created_by`),
  KEY `FK_warehouses_updated_by_idx` (`updated_by`),
  CONSTRAINT `FK_warehouses_branch_id` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `FK_warehouses_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_warehouses_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL
            );

        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            foreach (array_reverse(self::TABLES) as $table) {
                DB::statement('DROP TABLE IF EXISTS `'.$table.'`');
            }
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }
};
