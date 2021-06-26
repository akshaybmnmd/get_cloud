ALTER TABLE `images` ADD `likes` INT(20) NULL AFTER `privacy`, ADD INDEX `likes` (`likes`);

ALTER TABLE `videos` ADD `likes` INT(20) NULL AFTER `privacy`, ADD INDEX `likes` (`likes`);

