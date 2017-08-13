<?
/*
#===========================================================================
|
|	Unisolutions Callisto
|
|	by Rafał Pitoń
|	Copyright 2007 by Unisolutions
|	http://www.unisolutions.pl
|
#===========================================================================
|
|	This software is released under GNU General Public License v3
|	http://www.gnu.org/licenses/gpl.txt
|
#===========================================================================
|
|	Database Content
|	by Rafał Pitoń
|
#===========================================================================
*/

$install_query[0] = "INSERT INTO `attachments_types` (`attachments_type_id`, `attachments_type_extension`, `attachments_type_mime`, `attachments_type_image`) VALUES 
(1, 'jpg', 'image/jpeg', 'image.png'),
(2, 'png', 'image/png', 'image.png'),
(3, 'gif', 'image/gif', 'image.png'),
(4, 'zip', 'application/zip', 'zip.png'),
(5, 'rar', 'multipart/mixed', 'rar.png'),
(6, 'exe', 'application/octet-stream', 'exe.png'),
(7, 'mp3', 'audio/mpeg', 'music.abc.png')";

$install_query[1] = "INSERT INTO `styles` (`style_id`, `style_name`, `style_path`, `style_author`, `style_www`, `style_users`) VALUES 
(1, 'Callisto Impression', 'Callisto', 'Rafa&#322; Pito&#324;', 'http://www.unisolutions.pl', 1)";

?>