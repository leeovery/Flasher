# Flasher

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)

A Laravel package to flash 1 or more messages to session, with the ability to override previous or future messages, and to cap messages so they only appear once per session, hour, day etc.

Can be used with other frameworks with some edits but comes setup for Laravel sessions and cache.

## Install

Via Composer

``` bash
$ composer require leeovery/flasher
```

After adding the package, add the ServiceProvider to the providers array in `config/app.php`

``` php
LeeOvery\Flasher\FlasherServiceProvider::class,
```

You can also publish the config file and view file to make any adjustments as needed:

```bash
$ php artisan vendor:publish --tag="flasher"
```

### The View

A simple Vue / Semantic-UI implementation has been included as the view, but you can publish and edit the blade file as needed.

To use the Vue Alert component copy it to your own project, then `import` it into your bootstrap js file, and then setup the component with Vue:

``` js
import Vue from "vue";
import Alert from "./components/Alert.vue";
Vue.component('alert', Alert);
```

## Usage

The simplest implementation is just to use the flasher helper function. If you pass a message to the function it'll just output an info message to the session as a flash message, to be shown on the next request.

``` php
flasher('This is an info message);
```

If you want to show different types of messages you can do the following:

``` php
flasher()->success('This is a success message');
flasher()->error('This is an error message');
flasher()->warning('This is a warning message');
flasher()->info('This is an info message');
```

You can optionally pass a title too:

``` php
flasher()->title('This is a title.')
         ->info('This is an info message.');
```

### Flash this request

Sometimes, especially in middleware, you want to flash a message but show it in the this request, not the next request. You can do that by passing in the now() method to the chain:

``` php
flasher()->now()
         ->title('This is a title.')
         ->info('This info message will show in this request.');
```

### Overriding messages

If you want to flash a message (either for this request using now(), or the next request) but want to ensure its the only message flashed you can include the override method, which will wipe out any past or future flash messages during the request cycle:

``` php
flasher()->override()
         ->title('This is a title.')
         ->info('This info message will show in this request.');
```

If you only want to override messages of the same type, you can pass false as the 1st parameter to the override method. In the above example, it'll mean only info messages will be overridden, but any other type of flash message will be permitted.

If you pass false as the 2nd parameter to the override method, this will allow you to only override previous messages, but allow any future messages that might flash after this one. This can be useful in some cases.
 
###Capping messages
 
You can use the capping methods to control how often a flash message is shown. For example, if you want to remind a user to complete their profile once every session you can do the following:

``` php
flasher()->oncePerSession()
         ->info('You still need to complete your profile.');
```
 
This message will only flash once per session. Meaning when they login, they will see the message. On the next request it will not show. But if they logout (or are logged out automatically), they will see it again when they next login.

There are other types of caps you can use for other scenarios.

``` php
flasher()->oncePerDay(5)
flasher()->oncePerMinute(15)
flasher()->oncePerHour()
flasher()->oncePerWeek(2)
flasher()->oncePerMonth(2)
flasher()->oncePerYear()
flasher()->oncePerSession()
```

As you can see, you can pass an integer to the time-based methods, and this does what you'd expect. OncePerDay(5) will only show this flash message once every 5 days.

The time based 'oncePer' methods use the cache to store a hash of the message data. A Laravel implementation is included using the default cache driver setup in your config.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email :author_email instead of using the issue tracker.

## Credits

- [Lee Overy][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/:vendor/:package_name.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/:vendor/:package_name/master.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/:vendor/:package_name
[link-travis]: https://travis-ci.org/:vendor/:package_name
[link-downloads]: https://packagist.org/packages/:vendor/:package_name
[link-author]: https://github.com/:author_username
[link-contributors]: ../../contributors
