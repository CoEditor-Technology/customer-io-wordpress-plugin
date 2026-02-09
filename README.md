# customer-io-wordpress-plugin

A simple plugin to enable tracking of user behvaiour on a WordPress site with customer.io. This will require configuration of a JS tracking script in Customer.io. This plugin is designed to be installed via composer

## Installation
In your sites composer.json file, include the following:

```
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/CoEditor-Technology/customer-io-wordpress-plugin"
    }
  ],
  "require": {
    "coeditor/customerio-plugin": "^1.0"
  }
}
```


## Versioning

To version the plugin for release, run:

```
git push origin master --tags
git tag v1.0.0
```