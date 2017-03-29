# LazyEventDispatcher

An event dispatcher that holds any events until flushed.

## Use Case

If you want to make use of the [kernel.terminate](http://symfony.com/doc/current/components/http_kernel.html#the-kernel-terminate-event) event to do some
"heavy" action after the response has already streamed back to the client. Symfony does this already by default but with this
listener you'll have support for any custom event classes.

## Installation

The recommended way to install the library is through [Composer](http://getcomposer.org).

```bash
composer require robinvdvleuten/ulid
```

Install the listener as a service afterwards;

```yaml
services:
    app.lazy_event_dispatcher:
        class: Rvdv\LazyEventDispatcher\LazyEventDispatcher
        arguments:
          - "@event_dispatcher"
        tags:
          - { name: kernel.event_listener, event: kernel.terminate, method: flush }
```

Then add a custom compiler pass to have a new event listener type;

```php
<?php

namespace AppBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(
            new RegisterListenersPass('app.lazy_event_dispatcher', 'lazy.event_listener', 'lazy.event_subscriber')
        );
    }
}
```

You'll then can register any "lazy" event listeners like this;

```yaml
services:
    app.custom_event_listener:
        class: AppBundle\EventListener\CustomEventListener
        tags:
          - { name: lazy.event_listener, event: custom_event }
```

## License

MIT © [Robin van der Vleuten](https://www.robinvdvleuten.nl)
