# line-notifier

line-notifier is a package to [LINE Notify](https://notify-bot.line.me/en/) API client.

## Requirements
- [Guzzle](http://docs.guzzlephp.org/en/latest/)

## Installing your project

### Install via Composer

```
$ composer require neronplex/line-notifier
```

## Usage

Simple example...

```php
$line = new Neronplex\LineNotifier\Notify('your access token');
$line
    ->setMessage('message...')
    ->send();
```

More information [LINE Notify API Document](https://notify-bot.line.me/doc/en/).

## License

Copyright &copy; 2017 暖簾 ([@neronplex](http://twitter.com/neronplex))
Licensed under the [Apache License, Version 2.0][Apache]
 [Apache]: http://www.apache.org/licenses/LICENSE-2.0
