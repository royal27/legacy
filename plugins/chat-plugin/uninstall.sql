DROP TABLE IF EXISTS `chat_rooms`;
DROP TABLE IF EXISTS `chat_messages`;
DROP TABLE IF EXISTS `chat_room_members`;
DROP TABLE IF EXISTS `chat_banned_users`;
DELETE FROM `settings` WHERE `name` = 'chat_flood_time';
