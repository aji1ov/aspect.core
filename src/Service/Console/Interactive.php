<?php

namespace Aspect\Lib\Service\Console;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class Interactive
{
    private InputInterface $input;
    private OutputInterface $output;

    public function __construct(InputInterface $input, OutputInterface $output) {
        $this->input = $input;
        $this->output = $output;
    }

    public function ask(string $question, ?string $defaultAnswer = null): string
    {
        $helper = new QuestionHelper();
        return $helper->ask($this->input, $this->output, new Question($question."\n", $defaultAnswer));
    }
}