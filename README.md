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
terminus decoupled-kit:create my-site-name "My Site Label" --org="My Org" --cms=drupal --install-cms=TRUE --region=us
```

If you use the `--install-cms=FALSE` flag, the CMS sites won't be installed automatically.
This allows you to install the site with your preferred options. `--install-cms` is an optional flag,
if it's not provided, the default value is `TRUE`.

The `--region` option is also optional. Please refer to the documentation for a list of [valid regions](https://docs.pantheon.io/regions#create-a-new-site-in-a-specific-region-using-terminus).

If you don't provide an Upstream ID, the value of the --cms option will be used to determine the default Upstream.

### Specifying a Custom Upstream ID

To specify a custom Upstream ID, execute the following command:

```
terminus decoupled-kit:create my-site-name "My Site Label" decoupled-drupal-10-composer-managed --org="My Org" --cms=drupal --install-cms=FALSE --region=us
```
Note: Please ensure that the following parameters, `site_name`, `label`, and `upstream_id`, are included in the specified sequence. Placing these parameter values incorrectly may result in their association with the wrong values.