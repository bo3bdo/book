-- أنشئ أو حدّث مستخدم root ليقبل اتصالات من أي مضيف
ALTER USER 'root'@'%' IDENTIFIED BY '374756477';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' WITH GRANT OPTION;

-- أنشئ قاعدة books لمستخدم app
CREATE DATABASE IF NOT EXISTS `books`;
GRANT ALL PRIVILEGES ON `books`.* TO 'user'@'%';

-- أنشئ قاعدة npm لمستخدم NPM
CREATE DATABASE IF NOT EXISTS `npm`;
GRANT ALL PRIVILEGES ON `npm`.* TO 'user'@'%';

FLUSH PRIVILEGES;
