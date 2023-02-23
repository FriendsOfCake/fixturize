[![Latest Stable Version](https://img.shields.io/packagist/v/FriendsOfCake/fixturize.svg?style=flat-square)](https://packagist.org/packages/FriendsOfCake/fixturize)

# Installation

```
composer require friendsofcake/fixturize
```

# Introduction

The fixturize plugin will help improve performance of your fixture based tests.

This plugin currently only work with MySQL/MariaDB/Percona databases.

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
