[![Latest Stable Version](https://img.shields.io/packagist/v/FriendsOfCake/fixturize.svg?style=flat-square)](https://packagist.org/packages/FriendsOfCake/fixturize)

# Installation

For CakePHP 3.x compatible version:

```
composer require friendsofcake/fixturize
```

# Introduction

The fixturize plugin will help improve performance of your fixture based tests.

This plugin currently only work with MySQL databases.

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

Feel free to submit your own results above

# Bugs

If you happen to stumble upon a bug, please feel free to create a pull request with a fix
(optionally with a test), and a description of the bug and how it was resolved.

You can also create an issue with a description to raise awareness of the bug.

# Features

If you have a good idea for a Fixturize feature, please join us on IRC and let's discuss it. Pull
requests are always more than welcome.

# Support / Questions

You can join us on IRC in the #CakePHP channel for any support or questions.
