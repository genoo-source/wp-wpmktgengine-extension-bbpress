#!/usr/bin/env bash
#
# Needs to have jq installed, for automatic WordPress tested up to 
# version detection.
#
# `brew install jq`
#

# File:
# wpmktgengine-bbpress.php

# Get needed versions from a
PLUGIN_CURRENT_VERSION=$(awk '/   Version/{print $NF}' wpmktgengine-bbpress.php)
PLUGIN_NEXT_VERSION=$(echo $PLUGIN_CURRENT_VERSION | awk -F. '{$NF = $NF + 1;} 1' | sed 's/ /./g')
# Get latest WordPress Version
PLUGIN_WORDPRESS_NEXT_VERSION=$(curl -s "https://api.wordpress.org/core/version-check/1.7/" | jq -r '[.offers[]|select(.response=="upgrade")][0].version')
PLUGIN_WORDPRESS_CURRENT_VERSION=$(awk '/Tested up to/{print $NF}' readme.txt)

read -p "Would you like to update plugin from $PLUGIN_CURRENT_VERSION to $PLUGIN_NEXT_VERSION ?" response
if [[ $response = "yes" ]] || [[ $response = "y" ]] || [[ -z $response ]]; then
  echo "Updating to a new version..."
  # Check OS type, as macOS (Darwin) uses different version of `sed` command
  if [ "$(uname)" == "Darwin" ]; then
    # macOs
    sed -i "" "s/${PLUGIN_CURRENT_VERSION}/${PLUGIN_NEXT_VERSION}/g" wpmktgengine-bbpress.php
    sed -i "" "s/Stable tag: ${PLUGIN_CURRENT_VERSION}/Stable tag: ${PLUGIN_NEXT_VERSION}/g" readme.txt
    sed -i "" "s/Tested up to: ${PLUGIN_WORDPRESS_CURRENT_VERSION}/Tested up to: ${PLUGIN_WORDPRESS_NEXT_VERSION}/g" readme.txt
  else
    # Any other
    sed -i"" "s/${PLUGIN_CURRENT_VERSION}/${PLUGIN_NEXT_VERSION}/g" wpmktgengine-bbpress.php
    sed -i"" "s/Stable tag: ${PLUGIN_CURRENT_VERSION}/Stable tag: ${PLUGIN_NEXT_VERSION}/g" readme.txt
    sed -i"" "s/Tested up to: ${PLUGIN_WORDPRESS_CURRENT_VERSION}/Tested up to: ${PLUGIN_WORDPRESS_NEXT_VERSION}/g" readme.txt
  fi

  # New tag and push
  if [ "$GITHUB_ACTIONS" = true ]; then
    # Github Action, don't do anything
    echo "Running in Github Actions, abort commit"
  else
    # Local run, push changes
    git commit -am "Release: $PLUGIN_NEXT_VERSION"
    git tag -a $PLUGIN_NEXT_VERSION -m "Release: $PLUGIN_NEXT_VERSION"
    git push origin master --tags
  fi

  # All done, yay
  echo "Updated new version"
  exit 0;
fi

exit 0;
