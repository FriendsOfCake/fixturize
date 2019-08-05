[![Latest Stable Version](https://img.shields.io/packagist/v/FriendsOfCake/fixturize.svg?style=flat-square)](https://packagist.org/packages/FriendsOfCake/fixturize)

# Introduction

The fixturize plugin will help improve performance of your fixture based tests.

This plugin currently only work with MySQL/MariaDB/Percona databases.

# Installation

```
composer require friendsofcake/fixturize
```

Load the plugin in ``Application::bootstrap``, if you want to use the [themed Bake template](#themed-bake-template):

```` php
if (PHP_SAPI === 'cli') {
	try {
		$this->addPlugin('FriendsOfCake/Fixturize');
	} catch (MissingPluginException $e) {
		// Do not halt if the plugin is missing
	}
}
````

# Usage

Instead of

``use Cake\TestSuite\Fixture\TestFixture;``

simply use

``use FriendsOfCake\Fixturize\TestSuite\Fixture\ChecksumTestFixture as TestFixture;``

Re-run your tests and enjoy the speed!

# Real life improvements

<table>
    <thead>
        <th>Who</th>
        <th># Tests</th>
        <th>Before</th>
        <th>After</th>
    </thead>
    <tbody>
        <tr>
            <td>Bownty Content Importer</td>
            <td>364</td>
            <td>15s</td>
            <td>5s</td>
        </tr>
        <tr>
            <td>Apacta REST API (110 fixtures)</td>
            <td>318</td>
            <td>180s</td>
            <td>30s</td>
        </tr>
    </tbody>
</table>

Feel free to submit your own results above.

# Themed Bake Template

To use the built-in [themed Bake template](https://book.cakephp.org/bake/1.x/en/development.html#creating-a-bake-theme),
execute:

```` bash
php bin/cake.php bake fixture ModelName --theme FriendsOfCake/Fixturize
````

Make sure, you have [loaded the plugin](#installation).

For more information, see the [Bake documentation](https://book.cakephp.org/bake/1.x/en/index.html).
