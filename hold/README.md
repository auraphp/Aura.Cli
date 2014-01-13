### Catching Signals

The _ProcessControl_ class lets you interface with the `pcntl_*` family so
that you can catch signals usually sent from the command line.

```php
<?php
use Aura\Cli\ProcessControl;

$process_control = new ProcessControl();

declare(ticks = 5);

$process_control->handle(SIGINT, function() {
    echo "You hit 'Ctrl + C'";
    exit;
});

do {
    echo '.';
    sleep(1);
} while (true);
?>
```

Running the above will put you into an endless loop _echoing_ a '.' every
second. 'Ctrl + C' will send the _SIGINT_ signal to the script that we will
catch allowing us to kill the script ourselves.

The `declare(ticks = 5)` line is important and it is you the user who has to
_declare_ this in your code, in the correct scope.

