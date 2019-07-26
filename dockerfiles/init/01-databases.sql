# create databases
CREATE DATABASE IF NOT EXISTS `evqueue`;

# create root user and grant rights
# CREATE USER 'root'@'%' IDENTIFIED BY 'root';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'%';
