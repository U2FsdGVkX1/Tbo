CREATE TABLE `options` (
  `name` char(255) DEFAULT NULL,
  `value` char(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `plugins` (
  `pcn` char(255) DEFAULT NULL,
  `enabled` int(1) DEFAULT NULL,
  `lasterror` int DEFAULT NULL,
  `priority` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `options` (`name`, `value`) VALUES
('password', '77e2edcc9b40441200e31dc57dbb8829');
INSERT INTO `options` (`name`, `value`) VALUES
('message_total', '0');
INSERT INTO `options` (`name`, `value`) VALUES
('send_total', '0');
INSERT INTO `options` (`name`, `value`) VALUES
('error_total', '0');
INSERT INTO `options` (`name`, `value`) VALUES
('fastlogin_ip', '');
