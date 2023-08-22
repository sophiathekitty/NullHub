#!/bin/bash

echo "Installing Null Hub..."

read -p "Enter Main Hub URL: " main_hub_url
read -p "Enter Device Name: " device_name
read -p "Enter Device Type (hub, display, micro display, kiosk): " device_type

# Define plugin and extension names
plugins_hub="NullDisplay NullLights NullProfiles NullSensors NullWeather"
plugins_display="NullWeather NullDisplay"
plugins_micro_display="NullDisplay NullWeather NullSensors"
plugins_kiosk="$plugins_hub"

# Install packages based on device type
case "$device_type" in
    hub)
        plugins_to_install="$plugins_hub"
        extensions_to_install="MealPlanner"
        ;;
    display)
        plugins_to_install="$plugins_display"
        extensions_to_install="MealPlanner"
        ;;
    micro\ display)
        plugins_to_install="$plugins_micro_display"
        ;;
    kiosk)
        plugins_to_install="$plugins_kiosk"
        extensions_to_install="MealPlanner"
        ;;
    *)
        echo "Device type not recognized."
        exit 1
        ;;
esac


# Update package list and upgrade existing packages
sudo apt-get update
sudo apt-get upgrade -y

# Install Apache
sudo apt-get install apache2 -y
sudo a2enmod rewrite
sudo service apache2 restart

# Install PHP and required extensions
sudo apt-get install php libapache2-mod-php php-mysql -y
# Install curl and php-curl
sudo apt-get install curl php-curl -y
sudo service apache2 restart

# Install MySQL and set root password
#sudo apt-get install mysql-server -y
sudo apt-get install mariadb-server -y
sudo mysql_secure_installation

# Ask for MySQL credentials
read -p "Enter MySQL username: " mysql_user
read -s -p "Enter MySQL password: " mysql_password

# Create settings.php with MySQL credentials
echo "<?php
\$device_info = []
\$device_info['username'] = '$mysql_user';
\$device_info['username'] = '$mysql_password';
\$device_info['database'] = 'null_device';
\$device_info['url'] = '$main_hub_url';
\$device_info['name'] = '$device_name';
\$device_info['type'] = '$device_type';
?>" | sudo tee /var/www/html/settings.php > /dev/null

# Ask if this is a dev or production install
read -p "Is this a development (dev) or production (prod) install? " install_type

# Install phpMyAdmin only for development install
if [ "$install_type" == "dev" ]; then
    sudo apt-get install phpmyadmin php-mbstring php-gettext -y
    sudo phpenmod mbstring
    sudo systemctl restart apache2

    # Enable mcrypt for phpMyAdmin (if needed)
    # sudo phpenmod mcrypt
    # sudo systemctl restart apache2

    echo "phpMyAdmin is installed."
else
    echo "Skipping phpMyAdmin installation."
fi

# Install Python 3 and pip
sudo apt-get install python3 python3-pip python3-urllib3 -y

# Install git
sudo apt-get install git -y

# Install nmap
sudo apt-get install nmap -y

# Clone Null Hub repository
cd /var/www/html
git clone https://github.com/sophiathekitty/NullHub.git .
mkdir plugins
mkdir extensions

# Install plugins
for plugin in $plugins_to_install; do
    echo "Installing $plugin..."
    cd /var/www/html/plugins
    # Clone the repository
    clone https://github.com/sophiathekitty/${plugin}.git
    cd "$plugin"
    # Run the install script if it exists
    if [ ! -f install.sh ]; then
        echo "No install script found for $plugin."
        continue
    fi
    chmod +x install.sh
    ./install.sh
done

# Install extensions
for extension in $extensions_to_install; do
    echo "Installing extension: $extension..."
    # Clone the repository
    cd /var/www/html/extensions
    clone https://github.com/sophiathekitty/${extension}.git
    cd "$extension"
    echo "<?php
\$db_info = []
\$db_info['username'] = '$mysql_user';
\$db_info['username'] = '$mysql_password';
\$db_info['database'] = '$extension';
?>" | sudo tee /var/www/html/extensions/${extension}/settings.php > /dev/null
    # Run its install script if it exists
    if [ ! -f install.sh ]; then
        echo "No install script found for $extension."
        continue
    fi
    chmod +x install.sh
    ./install.sh
done

# Install models
wget -O/dev/null -q http://localhost/helpers/validate_models.php
# Run setup service (try to pull from main hub)
wget -O/dev/null -q http://localhost/services/setup.php

# Set permissions
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 755 /var/www/html

# Setup crontab
sudo crontab -l > /tmp/crontab
if [ "$install_type" == "dev" ]; then
    echo "#1 * * * * sh /var/www/html/gitpull.sh" >> /tmp/crontab
else
    echo "1 * * * * sh /var/www/html/gitpull.sh" >> /tmp/crontab
fi
echo "3 * * * * wget -O/dev/null -q http://localhost/helpers/validate_models.php" >> /tmp/crontab
echo "* * * * * wget -O/dev/null -q http://localhost/services/every_minute.php" >> /tmp/crontab
echo "*/5 * * * * wget -O/dev/null -q http://localhost/services/every_five_minutes.php" >> /tmp/crontab
echo "*/10 * * * * wget -O/dev/null -q http://localhost/services/every_ten_minutes.php" >> /tmp/crontab
echo "*/15 * * * * wget -O/dev/null -q http://localhost/services/every_fifteen_minutes.php" >> /tmp/crontab
echo "*/30 * * * * wget -O/dev/null -q http://localhost/services/every_thirty_minutes.php" >> /tmp/crontab
echo "0 * * * * wget -O/dev/null -q http://localhost/services/every_hour.php" >> /tmp/crontab
echo "0 0 * * * wget -O/dev/null -q http://localhost/services/every_day.php" >> /tmp/crontab
echo "0 0 * * 0 wget -O/dev/null -q http://localhost/services/every_week.php" >> /tmp/crontab
echo "0 0 1 * * wget -O/dev/null -q http://localhost/services/every_month.php" >> /tmp/crontab
echo "0 0 1 1 * wget -O/dev/null -q http://localhost/services/every_year.php" >> /tmp/crontab
sudo crontab /tmp/crontab
rm /tmp/crontab


echo "Installation completed. Null Hub is ready to use."
if [ "$device_type" == "micro display" ]; then
    read -p "Would you like to reboot now? (y/n) " reboot
    if [ "$reboot" == "y" ]; then
        sudo reboot
    fi
fi