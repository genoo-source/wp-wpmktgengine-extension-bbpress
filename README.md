#  bbPress - WPMktgEngine | Genoo Extension [![Build Status](https://travis-ci.org/genoo-source/wp-wpmktgengine-extension-bbpress.svg?branch=master)](https://travis-ci.org/genoo-source/wp-wpmktgengine-extension-bbpress) [![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-blue.svg)](https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html) [![Plugin Version](https://img.shields.io/wordpress/plugin/v/wpmktgengine-extension-bbpress.svg)](https://wordpress.org/plugins/wpmktgengine-extension-bbpress)


This is a mirror of the Genoo WordPress plugin found here. https://wordpress.org/plugins/wpmktgengine-extension-bbpress/

### Deployment

Travis CI will auto deploy when a new tag is created. Do this after the PR is merged into master. This should be done with new version number.

~~~~
# In project root
# This will increment the version number and echo it in the terminal
$ sh deploy/increment.sh
# Pressing enter will create a new version, update latest stable tag, and push it all up the github
~~~~

### Tests

Travis CI will auto lint PHP files for syntax errors. If you'd like to do that manually run:

~~~~
$ find . -name "*.php" -print0 | xargs -0 -n1 -P8 php -l
~~~~
