#!/usr/bin/env bash
#
# Needs to have jq installed, for automatic WordPress tested up to 
# version detection.
#
# `brew install jq`
#

# Get needed versions from a
PLUGIN_CURRENT_VERSION=$(awk '/   Version/{print $NF}' wpmktgengine-bbpress.php)
PLUGIN_NEXT_VERSION=$(echo $PLUGIN_CURRENT_VERSION | awk -F. '{$NF = $NF + 1;} 1' | sed 's/ /./g')
# Get latest WordPress Version
PLUGIN_WORDPRESS_NEXT_VERSION=$(curl -s "https://api.wordpress.org/core/version-check/1.7/" | jq -r '[.offers[]|select(.response=="upgrade")][0].version')
PLUGIN_WORDPRESS_CURRENT_VERSION=$(awk '/Tested up to/{print $NF}' readme.txt)

read -p "Would you like to update plugin from $PLUGIN_CURRENT_VERSION to $PLUGIN_NEXT_VERSION ?" response
if [[ $response =~ ^(yes|y| ) ]] || [[ -z $response ]]; then
  echo "Updating to a new version..."
  # New version update
  # Replace versions with new version
  # - In main file
  sed -i "" "s/${PLUGIN_CURRENT_VERSION}/${PLUGIN_NEXT_VERSION}/g" wpmktgengine-bbpress.php
  # - In readme file
  sed -i "" "s/Stable tag: ${PLUGIN_CURRENT_VERSION}/Stable tag: ${PLUGIN_NEXT_VERSION}/g" readme.txt
  sed -i "" "s/Tested up to: ${PLUGIN_WORDPRESS_CURRENT_VERSION}/Tested up to: ${PLUGIN_WORDPRESS_NEXT_VERSION}/g" readme.txt
  # New tag and push
  git commit -am "Release: $PLUGIN_NEXT_VERSION"
  git tag -a $PLUGIN_NEXT_VERSION -m "Release: $PLUGIN_NEXT_VERSION"
  git push origin master --tags
  # All done, yay
  echo "Updated new version"
fi
