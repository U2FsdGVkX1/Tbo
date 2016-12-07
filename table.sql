CREATE TABLE `options` (
  `name` char(255) DEFAULT NULL,
  `value` char(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `plugins` (
  `pcn` char(255) DEFAULT NULL,
  `enabled` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `options` (`name`, `value`) VALUES
('password', 'admin');