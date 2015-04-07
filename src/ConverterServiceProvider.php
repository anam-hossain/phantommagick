<?php
namespace Anam\PhantomMagick;

use Illuminate\Support\ServiceProvider;

class ConverterServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('converter', function() {
            return new \Anam\PhantomMagick\Converter;
        });
    }
}
