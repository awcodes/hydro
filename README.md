# Hydro

A Hydro CLI Tool.

This is a command-line tool that replaces the need to use Filament's Plugin Skeleton through GitHub by allowing local setup of your plugin from the start.

# Requirements

- PHP 8.0+

# Installation

```bash
composer global require awcodes/hydro
```

# Upgrading

```bash
composer global update awcodes/hydro
```

# Usage

Make sure `~/.composer/vendor/bin` is in your terminal's path.

```bash
cd ~/<working-directory>
hydro new AwesomePlugin
```

# Customizing Hydro

While the default actions Hydro provides are great, most users will want to 
customize at least a few of the steps. Thankfully, Hydro is built to be 
customized!

There are three ways to customize your usage of Hydro: command-line arguments or a config file.

Most users will want to set their preferred configuration options once and then never think about it again. That's best solved by creating a config file.

But if you find yourself needing to change the way you interact with Hydro on a 
project-by-project basis, you may also want to use the command-line 
parameters to customize Hydro when you're using it.

## Creating a config file

You can create a config file at `~/.hydro/config` rather than pass the same 
arguments each time you create a new project.

The following command creates the file, if it doesn't exist, and edits it:

```bash
hydro edit-config
```

The config file contains the configuration parameters you can customize, and 
will be read on every usage of Hydro.

## Using command-line parameters

Any command-line parameters passed in will override Hydro's defaults and your 
config settings. See a [full list of the parameters you can pass in](#parameters).

# Hydro Commands

- `help` or `help-screen` show the help screen

<a id="config-files"></a>
- `edit-config` edits your config file (and creates one if it doesn't exist)

  ```bash
  hydro edit-config
  ```

<a id="parameters"></a>
# Configurable parameters

You can optionally pass one or more of these parameters every time you use 
Hydro.
If you find yourself wanting to configure any of these settings every time 
you run Hydro, that's a perfect use for the [config files](#config-files).

- `-e` or `--editor` to define your editor command. Whatever is passed here will be run as `$EDITOR .` after creating the project.

  ```bash
  # runs "code ." in the project directory after creating the project
  hydro new AwesomePlugin --editor=code
  ```

- `-p` or `--path` to specify where to install the application.

  ```bash
  hydro new AwesomePlugin --path=~/Sites
  ```

- `-m` or `--message` to set the first Git commit message.

  ```bash
  hydro new AwesomePlugin --message="This filament plugin runs fast!"
  ```

- `-f` or `--force` to force install even if the directory already exists

  ```bash
  # Creates a new scaffolding after deleting ~/Sites/awesome-plugin  
  hydro new AwesomePlugin --force
  ```

**GitHub Repository Creation**

**Important:** To create new repositories Hydro requires one of the following 
tools to be installed:
- the official [GitHub command line tool](https://github.com/cli/cli#installation)
- the [hub command line tool](https://github.com/github/hub#installation)

Hydro will give you the option to continue without GitHub repository 
creation if neither tool is installed.

- `-g` or `--github` to  Initialize a new private GitHub repository and push your new project to it.

```bash
# Repository created at https://github.com/<your_github_username>/awesome-plugin
hydro new AwesomePlugin --github
```

- Use `--gh-public` with `--github` to make the new GitHub repository public.

```bash
hydro new AwesomePlugin --github --gh-public
```

- Use `--gh-description` with `--github` to initialize the new GitHub repository with a description.

```bash
hydro new AwesomePlugin --github --gh-description='My awesome Filament plugin'
```

- Use `--gh-homepage` with `--github` to initialize the new GitHub repository with a homepage url.

```bash
hydro new AwesomePlugin --github --gh-homepage=https://example.com
```
- Use `--gh-org` with `--github` to initialize the new GitHub repository with a specified organization.

```bash
# Repository created at https://github.com/acme/awesome-plugin
hydro new AwesomePlugin --github --gh-org=acme
```
