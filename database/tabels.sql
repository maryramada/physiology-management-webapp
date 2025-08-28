CREATE DATABASE fisiology_v1;
USE fisiology_v1;

CREATE TABLE `customers` (
	`id_customers` INT(10) NOT NULL AUTO_INCREMENT,
	`first_name` VARCHAR(50) NOT NULL DEFAULT '' COLLATE 'utf8mb4_0900_ai_ci',
	`last_name` VARCHAR(50) NOT NULL DEFAULT '' COLLATE 'utf8mb4_0900_ai_ci',
	`email` VARCHAR(50) NOT NULL DEFAULT '' COLLATE 'utf8mb4_0900_ai_ci',
	`gender` ENUM('female','male','') NULL DEFAULT '' COLLATE 'utf8mb4_0900_ai_ci',
	`date_of_birth` DATE NOT NULL,
	PRIMARY KEY (`id_customers`) USING BTREE
)
COLLATE='utf8mb4_0900_ai_ci'
ENGINE=InnoDB
AUTO_INCREMENT=51
;

CREATE TABLE `physiologist` (
	`id_physiologist` INT(10) NOT NULL AUTO_INCREMENT,
	`first_name` VARCHAR(50) NOT NULL DEFAULT '' COLLATE 'utf8mb4_0900_ai_ci',
	PRIMARY KEY (`id_physiologist`) USING BTREE
)
COLLATE='utf8mb4_0900_ai_ci'
ENGINE=InnoDB
AUTO_INCREMENT=31
;

CREATE TABLE `assignments` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`id_customer` INT(10) NOT NULL,
	`id_physiologist` INT(10) NOT NULL,
	`date_time` DATETIME NOT NULL,
	PRIMARY KEY (`id`) USING BTREE,
	INDEX `id_customer_fk` (`id_customer`) USING BTREE,
	INDEX `id_physiologist_fk` (`id_physiologist`) USING BTREE,
	CONSTRAINT `id_customers_FK1` FOREIGN KEY (`id_customer`) REFERENCES `customers` (`id_customers`) ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT `id_physiologist_FK2` FOREIGN KEY (`id_physiologist`) REFERENCES `physiologist` (`id_physiologist`) ON UPDATE NO ACTION ON DELETE NO ACTION
)
COLLATE='utf8mb4_0900_ai_ci'
ENGINE=InnoDB
AUTO_INCREMENT=302
;

CREATE TABLE `workouts` (
	`id` INT(10) NOT NULL,
	`id_customers` INT(10) NOT NULL,
	`period_minutes` INT(10) NOT NULL,
	`week_number` INT(10) NOT NULL,
	`year` INT(10) NOT NULL,
	`date_time` DATETIME NOT NULL,
	PRIMARY KEY (`id`) USING BTREE,
	INDEX `workouts_id_customers_FK1` (`id_customers`) USING BTREE,
	CONSTRAINT `workouts_id_customers_FK1` FOREIGN KEY (`id_customers`) REFERENCES `customers` (`id_customers`) ON UPDATE NO ACTION ON DELETE NO ACTION
)
COLLATE='utf8mb4_0900_ai_ci'
ENGINE=InnoDB
;

CREATE TABLE `assessments` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`id_customers` INT(10) NOT NULL,
	`id_physiologist` INT(10) NOT NULL,
	`is_renovation` ENUM('no','yes','') NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`pull_down` TEXT NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`leg_extension` TEXT NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`chest_press` TEXT NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`leg_press` TEXT NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`date_time` DATETIME NOT NULL,
	PRIMARY KEY (`id`) USING BTREE,
	INDEX `assements_physiologist_id_fk` (`id_physiologist`) USING BTREE,
	INDEX `assements_customer_id_fk` (`id_customers`) USING BTREE,
	CONSTRAINT `assements_customer_id_fk` FOREIGN KEY (`id_customers`) REFERENCES `customers` (`id_customers`) ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT `assements_physiologist_id_fk` FOREIGN KEY (`id_physiologist`) REFERENCES `physiologist` (`id_physiologist`) ON UPDATE NO ACTION ON DELETE NO ACTION
)
COLLATE='utf8mb4_0900_ai_ci'
ENGINE=InnoDB
AUTO_INCREMENT=12
;

CREATE TABLE `assessment_hgt` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`grip_strength_values` INT(10) NOT NULL DEFAULT '0',
	`considerations` TEXT NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	PRIMARY KEY (`id`) USING BTREE
)
COLLATE='utf8mb4_0900_ai_ci'
ENGINE=InnoDB
AUTO_INCREMENT=51
;

CREATE TABLE `assessement_first_serie` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`machine_pull_down` TEXT NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`machine_leg_extension` TEXT NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`machine_chest_press` TEXT NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`machine_leg_press` TEXT NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	PRIMARY KEY (`id`) USING BTREE
)
COLLATE='utf8mb4_0900_ai_ci'
ENGINE=InnoDB
AUTO_INCREMENT=51
;

CREATE TABLE `assessment_m2r` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`test_a1_result` TEXT NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`test_a1_considerations` TEXT NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`test_a2_result` TEXT NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`test_a2_considerations` TEXT NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`test_a3_result` TEXT NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`test_a3_considerations` TEXT NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`test_a4_distance` INT(10) NOT NULL,
	`test_a4_vo2max` INT(10) NOT NULL,
	`test_a4_resting_heart_rate` INT(10) NOT NULL,
	`teste_a4_end_heart_race` INT(10) NOT NULL,
	`test_a4_heart_race_after_1second` INT(10) NOT NULL,
	`test_a4_considerations` TEXT NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	PRIMARY KEY (`id`) USING BTREE
)
COLLATE='utf8mb4_0900_ai_ci'
ENGINE=InnoDB
AUTO_INCREMENT=21
;

CREATE TABLE `assessment_second_serie` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`contraction_quality` TEXT NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`muscular_resistance` TEXT NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	PRIMARY KEY (`id`) USING BTREE
)
COLLATE='utf8mb4_0900_ai_ci'
ENGINE=InnoDB
AUTO_INCREMENT=21
;

CREATE TABLE `assessment_second_serie_constraction_quality` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`p1_pull_down` ENUM('Y','N','') NOT NULL DEFAULT '' COLLATE 'utf8mb4_0900_ai_ci',
	`p1_leg_extension` ENUM('Y','N','') NOT NULL DEFAULT '' COLLATE 'utf8mb4_0900_ai_ci',
	`p1_chest_press` ENUM('Y','N','') NOT NULL DEFAULT '' COLLATE 'utf8mb4_0900_ai_ci',
	`p1_leg_press` ENUM('Y','N','') NOT NULL DEFAULT '' COLLATE 'utf8mb4_0900_ai_ci',
	`p2_pull_down` ENUM('Y','N','') NOT NULL DEFAULT '' COLLATE 'utf8mb4_0900_ai_ci',
	`p2_leg_extension` ENUM('Y','N','') NOT NULL DEFAULT '' COLLATE 'utf8mb4_0900_ai_ci',
	`p2_chest_press` ENUM('Y','N','') NOT NULL DEFAULT '' COLLATE 'utf8mb4_0900_ai_ci',
	`p2_leg_press` ENUM('Y','N','') NOT NULL DEFAULT '' COLLATE 'utf8mb4_0900_ai_ci',
	`p3_pull_down` ENUM('Y','N','') NOT NULL DEFAULT '' COLLATE 'utf8mb4_0900_ai_ci',
	`p3_leg_extension` ENUM('Y','N','') NOT NULL DEFAULT '' COLLATE 'utf8mb4_0900_ai_ci',
	`p3_chest_press` ENUM('Y','N','') NOT NULL DEFAULT '' COLLATE 'utf8mb4_0900_ai_ci',
	`p3_leg_press` ENUM('Y','N','') NOT NULL DEFAULT '' COLLATE 'utf8mb4_0900_ai_ci',
	PRIMARY KEY (`id`) USING BTREE
)
COLLATE='utf8mb4_0900_ai_ci'
ENGINE=InnoDB
AUTO_INCREMENT=21
;

CREATE TABLE `assessment_second_serie_load_register` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`subjective_effort_perspective` TEXT NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`scale_of_feeling` TEXT NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	PRIMARY KEY (`id`) USING BTREE
)
COLLATE='utf8mb4_0900_ai_ci'
ENGINE=InnoDB
AUTO_INCREMENT=21
;

CREATE TABLE `assessment_second_serie_muscular_resistance` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`p1_pull_down` ENUM('Y','N','') NOT NULL DEFAULT '' COLLATE 'utf8mb4_0900_ai_ci',
	`p1_leg_extension` ENUM('Y','N','') NOT NULL DEFAULT '' COLLATE 'utf8mb4_0900_ai_ci',
	`p1_chest_press` ENUM('Y','N','') NOT NULL DEFAULT '' COLLATE 'utf8mb4_0900_ai_ci',
	`p1_leg_press` ENUM('Y','N','') NOT NULL DEFAULT '' COLLATE 'utf8mb4_0900_ai_ci',
	`p2_pull_down` ENUM('Y','N','') NOT NULL DEFAULT '' COLLATE 'utf8mb4_0900_ai_ci',
	`p2_leg_extension` ENUM('Y','N','') NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`p2_chest_press` ENUM('Y','N','') NOT NULL DEFAULT '' COLLATE 'utf8mb4_0900_ai_ci',
	`p2_leg_press` ENUM('Y','N','') NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`p3_pull_down` TEXT NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`p3_leg_extension` TEXT NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`p3_chest_press` TEXT NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`p3_leg_press` TEXT NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	PRIMARY KEY (`id`) USING BTREE
)
COLLATE='utf8mb4_0900_ai_ci'
ENGINE=InnoDB
AUTO_INCREMENT=21
;
