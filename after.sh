#!/bin/sh
mysql -u homestead -psecret -e "GRANT ALL PRIVILEGES ON homestead.* TO 'travis'@'%' IDENTIFIED BY ''"
/usr/local/bin/composer self-update
echo "" >> /home/vagrant/.bashrc
echo "PATH=$PATH:/home/vagrant/nerdsaredrinking/vendor/bin" >> /home/vagrant/.bashrc
echo "export PATH" >> /home/vagrant/.bashrc
echo Done!