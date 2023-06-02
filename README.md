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
terminus decoupled-kit:create my-site-name "My Site Label" drupal-composer-managed --org="My Org" --cms=drupal --install-cms=TRUE --region=us
```

Different Upstream options:

Drupal 10:

Machine name: `decoupled-drupal-10-composer-managed`
UUID: `19a917a4-c56e-44d6-98a1-49e7f779e678`

Drupal 9:

Machine name: `drupal-composer-managed`
UUID: `c76c0e51-ad85-41d7-b095-a98a75869760`

WordPress:

Machine name: `wordpress`
UUID: `c9f5e5c0-248f-4205-b63a-d2729572dd1f`

Note: You can use either the machine name or UUID for the upstream parameter.

If you use the `--install-cms=FALSE` flag, the CMS sites won't be installed automatically.
This allows you to install the site with your preferred options. `--install-cms` is an optional flag,
if it's not provided, the default value is `TRUE`.

The `--region` option is also optional. Please refer to the documentation for a list of [valid regions](https://docs.pantheon.io/regions#create-a-new-site-in-a-specific-region-using-terminus).