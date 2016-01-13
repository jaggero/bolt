<?php

namespace Bolt\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\VarDumper;

/**
 * DI for Symfony's VarDumper.
 *
 * @author Carson Full <carsonfull@gmail.com>
 */
class DumperServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        $app['dump'] = $app->protect(
            function ($var) use ($app) {
                $app['dumper']->dump($app['dumper.cloner']->cloneVar($var));
            }
        );

        VarDumper::setHandler($app['dump']);

        $app['dumper'] = $app->share(
            function ($app) {
                return PHP_SAPI === 'cli' ? $app['dumper.cli'] : $app['dumper.html'];
            }
        );

        $app['dumper.cli'] = $app->share(
            function () {
                return new CliDumper();
            }
        );

        $app['dumper.html'] = $app->share(
            function () {
                return new HtmlDumper();
            }
        );

        $app['dumper.cloner'] = $app->share(
            function () {
                $cloner = new VarCloner();

                return $cloner;
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
    }
}
