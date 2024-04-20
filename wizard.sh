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

# Get main hub URL (IP address)
MAIN_HUB_URL=$(whiptail --inputbox "Enter the main hub URL (IP address):" 10 60 3>&1 1>&2 2>&3)

# Get MySQL username
MYSQL_USERNAME=$(whiptail --inputbox "Enter MySQL username:" 10 60 3>&1 1>&2 2>&3)

# Get MySQL password
MYSQL_PASSWORD=$(whiptail --passwordbox "Enter MySQL password:" 10 60 3>&1 1>&2 2>&3)

# Display the device type menu
DEVICE_TYPE=$(whiptail --title "Setup Wizard - Device Type" --menu "Select the type of device to install:" 20 60 4 \
"Hub" "Install as main hub" \
"eInk" "Install as eInk display device" \
"Micro Display" "Install as micro display device" \
"Custom" "Customize installation" 3>&1 1>&2 2>&3)

# Handle user choice
case $DEVICE_TYPE in
  "Hub")
    # Install as main hub
    #echo "Installing main hub with URL: $MAIN_HUB_URL, device name: $DEVICE_NAME, MySQL username: $MYSQL_USERNAME, and MySQL password: $MYSQL_PASSWORD"
    plugins_to_install="NullDisplay NullLights NullProfiles NullSensors NullWeather"
    extensions_to_install="MealPlanner"
    ;;
  "eInk")
    # Install as eInk display device
    #echo "Installing eInk display device with device name: $DEVICE_NAME, MySQL username: $MYSQL_USERNAME, and MySQL password: $MYSQL_PASSWORD"
    plugins_to_install="NullWeather NullDisplay"
    extensions_to_install="MealPlanner"
    ;;
  "Micro Display")
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

