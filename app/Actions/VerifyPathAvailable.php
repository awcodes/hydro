<?php

namespace App\Actions;

use App\ConsoleWriter;
use App\InstallerException;
use Illuminate\Support\Facades\File;

class VerifyPathAvailable
{
     use AbortsCommands;

     public function __construct(
         protected ConsoleWriter $consoleWriter
     ){}

    public function __invoke(): void
     {
         $this->consoleWriter->logStep('Verifying path availability...');

         $rootPath = getcwd();

         if (! File::isDirectory($rootPath)) {
             throw new InstallerException("{$rootPath} is not a directory.");
         }
     }
}
