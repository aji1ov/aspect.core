<?php

namespace Aspect\Lib\Service\ScriptManager;

use Aspect\Lib\Blueprint\DI\Fetch;
use Aspect\Lib\Manager\ScriptManager;
use Aspect\Lib\Support\Interfaces\ScriptManagerInterface;
use Aspect\Lib\Support\Interfaces\ScriptProcessInterface;

class LoopScriptManager implements ScriptManagerInterface
{
    #[Fetch]
    protected ScriptProcessInterface $scriptProcess;

    #[Fetch]
    protected ScriptManager $manager;

    public function observe(string $name): void
    {
        $env = parse_ini_file($_SERVER['DOCUMENT_ROOT']."/.env");
        $phpShell = $env['PHP_SHELL'] ?? 'php';

        $this->scriptProcess->start($phpShell . " aspect script.exec ".$name);
        while ($this->scriptProcess->isRunning()) {

            if ($outputData = $this->scriptProcess->readAvailableOutput()) {
               $this->manager->addOutput($name, $outputData);

               if ($this->manager->isCanceled($name)) {
                  $this->scriptProcess->kill();
               }
            }
        }
        $this->scriptProcess->close();
    }
}