<?php

namespace Boris\Loader\Provider;

class Symfony2 extends AbstractProvider
{
    public $name = 'symfony';

    public function assertDir($dir)
    {
        return  (
                    is_file("$dir/app/bootstrap.php.cache") ||
                    is_file("$dir/app/autoload.php")
                )
            && is_file("$dir/app/AppKernel.php");
    }

    public function initialize(\Boris\Boris $boris, $dir)
    {
        parent::initialize($boris, $dir);

        if(is_file("$dir/app/bootstrap.php.cache")) {
            require "$dir/app/bootstrap.php.cache";
        } else {
            require "$dir/app/autoload.php";
        }
        require_once "$dir/app/AppKernel.php";

        $kernel = new \AppKernel('dev', true);
        $kernel->loadClassCache();
        $kernel->boot();

        $boris->onStart(function ($worker, $vars) use ($kernel) {
            $worker->setLocal('kernel', $kernel);
            $worker->setLocal('container', $kernel->getContainer());
        });
    }

}
