#!/bin/bash

# Function to show custom display menu
plugins_menu() {
    CHOICE=$(whiptail --title "Custom Install" --checklist "Select plugins to install (spacebar to select, enter to continue):" 20 60 5 \
    NullWeather "Install weather plugin" OFF \
    NullLights "Install lights plugin" OFF \
    NullDisplay "Install display plugin" OFF \
    NullProfile "Install profile plugin" OFF 3>&1 1>&2 2>&3)
    CHOICE=$(echo $CHOICE | tr -d \")
    echo "$CHOICE"
}
extensions_menu() {
    CHOICE=$(whiptail --title "Custom Install" --checklist "Select extensions to install:" 20 60 5 \
    MealPlanner "Install meal planner extension" OFF 3>&1 1>&2 2>&3)
    CHOICE=$(echo $CHOICE | tr -d \")
    echo "$CHOICE"
}

# Get device name
DEVICE_NAME=$(whiptail --inputbox "Enter the device name:" 10 60 3>&1 1>&2 2>&3)

# Get if this is a dev or prod install
INSTALL_TYPE=$(whiptail --title "Setup Wizard - Install Type" --menu "Select the type of installation:" 20 60 4 \
"Development" "Install as development device" \
"Production" "Install as production device" 3>&1 1>&2 2>&3)

# Get main hub URL (IP address)
MAIN_HUB_URL=$(whiptail --inputbox "Enter the main hub URL (IP address):" 10 60 3>&1 1>&2 2>&3)

# Get MySQL username
MYSQL_USERNAME=$(whiptail --inputbox "Enter MySQL username:" 10 60 3>&1 1>&2 2>&3)

# Get MySQL password
MYSQL_PASSWORD=$(whiptail --passwordbox "Enter MySQL password:" 10 60 3>&1 1>&2 2>&3)

# Display the device type menu
DEVICE_TYPE=$(whiptail --title "Setup Wizard - Device Type" --menu "Select the type of device to install:" 20 60 4 \
"hub" "Install as main hub" \
"display" "Install as eInk display device" \
"micro display" "Install as micro display device" \
"Custom" "Customize installation" 3>&1 1>&2 2>&3)

# Handle user choice
case $DEVICE_TYPE in
  "hub")
    # Install as main hub
    #echo "Installing main hub with URL: $MAIN_HUB_URL, device name: $DEVICE_NAME, MySQL username: $MYSQL_USERNAME, and MySQL password: $MYSQL_PASSWORD"
    plugins_to_install="NullDisplay NullLights NullProfiles NullSensors NullWeather"
    extensions_to_install="MealPlanner"
    ;;
  "display")
    # Install as eInk display device
    #echo "Installing eInk display device with device name: $DEVICE_NAME, MySQL username: $MYSQL_USERNAME, and MySQL password: $MYSQL_PASSWORD"
    plugins_to_install="NullWeather NullDisplay"
    extensions_to_install="MealPlanner"
    ;;
  "micro display")
    # Install as micro display device
    #echo "Installing micro display device with device name: $DEVICE_NAME, MySQL username: $MYSQL_USERNAME, and MySQL password: $MYSQL_PASSWORD"
    plugins_to_install="NullDisplay NullWeather NullSensors"
    extensions_to_install=""
    ;;
  "Custom")
    # Custom installation
    #echo "Installing custom device with URL: $MAIN_HUB_URL, device name: $DEVICE_NAME, MySQL username: $MYSQL_USERNAME, and MySQL password: $MYSQL_PASSWORD"
    # Show custom menu
    plugins_to_install=$(plugins_menu)
    extensions_to_install=$(extensions_menu)
    #echo "Selected plugins and extensions: $CHOICES"
    DEVICE_TYPE=$(whiptail --inputbox "Enter custom device type:" 10 60 3>&1 1>&2 2>&3)
    ;;
  *)
    # Unexpected input
    whiptail --msgbox "Invalid choice" 10 30
    ;;
esac

echo "Device name: $DEVICE_NAME"
echo "Main hub URL: $MAIN_HUB_URL"
echo "MySQL username: $MYSQL_USERNAME"
echo "MySQL password: $MYSQL_PASSWORD"
echo "Device type: $DEVICE_TYPE"
echo "Plugins to install: $plugins_to_install"
echo "Extensions to install: $extensions_to_install"

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
sudo apt-get install mariadb-server -y
sudo mysql_secure_installation

# Change root user authentication method to password
sudo mysql -u root -p$MYSQL_ROOT_PASSWORD <<MYSQL_SCRIPT
USE mysql;
UPDATE user SET plugin='' WHERE User='root';
FLUSH PRIVILEGES;
MYSQL_SCRIPT

# Create database and user
sudo mysql -u root -p <<MYSQL_SCRIPT
CREATE DATABASE null_device;
CREATE USER '$MYSQL_USERNAME'@'localhost' IDENTIFIED BY '$MYSQL_PASSWORD';
GRANT ALL PRIVILEGES ON null_device.* TO '$MYSQL_USERNAME'@'localhost';
FLUSH PRIVILEGES;
MYSQL_SCRIPT

# Create settings.php with MySQL credentials
echo "<?php
\$device_info = []
\$device_info['username'] = '$MYSQL_USERNAME';
\$device_info['password'] = '$MYSQL_PASSWORD';
\$device_info['database'] = 'null_device';
\$device_info['url'] = '$MAIN_HUB_URL';
\$device_info['name'] = '$DEVICE_NAME';
//\$device_info['type'] = '$DEVICE_TYPE';
?>" | sudo tee /var/www/html/settings.php

# Only install phpMyAdmin on Development devices
if [ "$INSTALL_TYPE" == "Development" ]; then
    # Install phpMyAdmin
    sudo apt-get install phpmyadmin -y
    sudo ln -s /usr/share/phpmyadmin /var/www/html/phpMyAdmin
    sudo service apache2 restart
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
\$db_info['username'] = '$MYSQL_USERNAME';
\$db_info['password'] = '$MYSQL_PASSWORD';
\$db_info['database'] = '$extension';
?>" | sudo tee /var/www/html/extensions/${extension}/settings.php > /dev/null
    # Create the database
    sudo mysql -u root -p$MYSQL_PASSWORD <<MYSQL_SCRIPT
CREATE DATABASE $extension;
CREATE USER '$MYSQL_USERNAME'@'localhost' IDENTIFIED BY '$MYSQL_PASSWORD';
GRANT ALL PRIVILEGES ON $extension.* TO '$MYSQL_USERNAME'@'localhost';
FLUSH PRIVILEGES;
MYSQL_SCRIPT
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
if [ "$INSTALL_TYPE" == "Development" ]; then
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


echo "Installation completed. Null $DEVICE_TYPE is ready to use."
if [ "$DEVICE_TYPE" == "Micro Display" ]; then
    read -p "Would you like to reboot now? (y/n) " reboot
    if [ "$reboot" == "y" ]; then
        sudo reboot
    fi
fi