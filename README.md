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

# Bugs

If you happen to stumble upon a bug, please feel free to create a pull request with a fix
(optionally with a test), and a description of the bug and how it was resolved.

You can also create an issue with a description to raise awareness of the bug.

# Features

If you have a good idea for a Crud feature, please join us on IRC and let's discuss it. Pull
requests are always more than welcome.

# Support / Questions

You can join us on IRC in the #FriendsOfCake channel for any support or questions.
