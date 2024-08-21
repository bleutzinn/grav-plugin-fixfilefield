# FixFileField Plugin

***Abandonment Notice:** Creating and improving my plugins for Grav was fun to do. However times are changing and so am I. My interests have shifted away from coding and so I am abandoning my plugins. If you are interested in taking over please follow the  ["Abandoned Resource Protocol"](https://learn.getgrav.org/17/advanced/grav-development#abandoned-resource-protocol). Simply skip the first two steps and refer to this statement in step 3.*

This plugin provides a workaround so file fields in Grav Forms can be set to required or are mandatory.

The **FixFileField** Plugin is an extension for [Grav CMS](https://github.com/getgrav/grav).

## Installation

Installing theFixFileField plugin can be done in one of three ways: The GPM (Grav Package Manager) installation method lets you quickly install the plugin with a simple terminal command, the manual method lets you do so via a zipFile, and the admin method lets you do so via the Admin Plugin.

### GPM Installation (Preferred)

To install the plugin via the [GPM](https://learn.getgrav.org/cli-console/grav-cli-gpm), through your system's terminal (also called the command line), navigate to the root of your Grav-installation, and enter:

    bin/gpm install fixfilefield

This will install the FixFileField plugin into your `/user/plugins`-directory within Grav. It's files can be found under `/your/site/grav/user/plugins/fixfilefield`.

### Manual Installation

To install the plugin manually, download the zip-version of this repository and unzip it under `/your/site/grav/user/plugins`. Then rename the folder to `fixfilefield`. You can find these files on [GitHub](https://github.com/bleutzinn/grav-plugin-fixfilefield) or via [GetGrav.org](https://getgrav.org/downloads/plugins).

You should now have all the plugin files under

    /your/site/grav/user/plugins/fixfilefield
	
> NOTE: This plugin is a modular component for Grav which may require other plugins to operate, please see its [blueprints.yamlfile on GitHub](https://github.com/bleutzinn/grav-plugin-fixfilefield/blob/main/blueprints.yaml).

### Admin Plugin

If you use the Admin Plugin, you can install the plugin directly by browsing the `Plugins`-menu and clicking on the `Add` button.

## Configuration

Before configuring this plugin, you should copy the `user/plugins/fixfilefield/fixfilefield.yaml` to `user/config/plugins/fixfilefield.yaml` and only edit that copy.

Here is the default configuration and an explanation of available options:

```yaml
enabled: true
```

Note that if you use the Admin Plugin, a file with your configuration named fixfilefield.yaml will be saved in the `user/config/plugins/`-folder once the configuration is saved in the Admin.

## Usage

To set a file field to required use the standard field attribute `validate.required` and set it to `true`.

To set a maximum on the number of files which may be uploaded use the standard field attribute `limit`.

A new attribute for the file field is `lower_limit`. It allows to set a minimum number of files which should be uploaded before submitting the form.

## Credits

**Did you incorporate third-party code? Want to thank somebody?**

## To Do

- [ ] Future plans, if any

