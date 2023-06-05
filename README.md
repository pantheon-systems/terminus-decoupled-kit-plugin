# Terminus Decoupled Kit Plugin

Creates a Decoupled Kit project for use on Pantheon.

## Installation

```
terminus self:plugin:install terminus-build-tools-plugin
```

Or to install from source:

* Clone https://github.com/pantheon-systems/terminus-decoupled-kit-plugin
* `cd terminus-decoupled-kit-plugin`
* `composer install`
* `terminus self:plugin:install .`

## Usage

Run interactively:

```
terminus decoupled-kit:create
```

Run with command line flags:

```
terminus decoupled-kit:create my-site-name "My Site Label" --org="My Org" --cms=drupal --install-cms=TRUE
```

If you use the `--install-cms=FALSE` flag, the CMS sites won’t be installed automatically.
This allows you to install the site with your preferred options. `--install-cms` is an optional flag,
if it’s not provided, the default value is `TRUE`.
