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

        $this->scriptProcess->start("php aspect script.exec ".$name);
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