CREATE  TABLE IF NOT EXISTS `testimonials` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'The unique ID for this testimonial.' ,
  `user_id` INT(11) NOT NULL COMMENT 'The ID of the user that added this testimonial.' ,
  `language` VARCHAR(10) NOT NULL COMMENT 'The language of this testimonial.' ,
  `name` VARCHAR(128) NOT NULL COMMENT 'The original author of this testimonial.' ,
  `testimonial` TEXT NOT NULL COMMENT 'The actual testimonial.' ,
  `hidden` ENUM('N', 'Y') NOT NULL COMMENT 'Whether this testimonial is shown or not.' ,
  `sequence` INT(11) NOT NULL COMMENT 'The sequence of this testimonial.' ,
  `created_on` DATETIME NOT NULL COMMENT 'The date and time this testimonial was created.' ,
  `edited_on` DATETIME NOT NULL COMMENT 'The date and time this testimonial was last edited.' ,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_unicode_ci;
