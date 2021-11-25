#  bbPress - WPMktgEngine | Genoo Extension [![Build Status](https://travis-ci.org/genoo-source/wp-wpmktgengine-extension-bbpress.svg?branch=master)](https://travis-ci.org/genoo-source/wp-wpmktgengine-extension-bbpress) [![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-blue.svg)](https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html) [![Plugin Version](https://img.shields.io/wordpress/plugin/v/wpmktgengine-extension-bbpress.svg)](https://wordpress.org/plugins/wpmktgengine-extension-bbpress)


This is a mirror of the Genoo WordPress plugin found here. https://wordpress.org/plugins/wpmktgengine-extension-bbpress/

### Deployment

#### Using command line

You can deploy via command line using this command:

```bash
$ sh ./deploy/increment.sh
```

This will run the script, ask you if you'd like to upgrade to given version, once updated it will push the new tag to the remote repository, where the `.github/workflows/deployed-to-wordpress.yml` workflow will be triggered and that will push all new changes to the wp.org repository.

#### Using Github Interface

You can deploy a new version using a GitHub interface too, all you need to do is, go to `Repository -> Actions -> Select: "Manually Deploy New Version" -> Click: "Run Workflow" -> Click Green Button: "Run Workflow". This will trigger an update script through the command line, and after that it will trigger the original deployment workflow.
### Tests

Travis CI will auto lint PHP files for syntax errors. If you'd like to do that manually run:

~~~~
$ find . -name "*.php" -print0 | xargs -0 -n1 -P8 php -l
~~~~
