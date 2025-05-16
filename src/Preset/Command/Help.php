<?php

namespace Aspect\Lib\Preset\Command;

use Aspect\Lib\Blueprint\DI\Fetch;
use Aspect\Lib\Exception\CommandException;
use Aspect\Lib\Service\Console\Argument;
use Aspect\Lib\Service\Console\Color;
use Aspect\Lib\Service\Console\Command;
use Aspect\Lib\Service\Console\CommandLocator;
use Aspect\Lib\Service\Console\Fake;
use Bitrix\Main\Localization\Loc;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Help extends Command
{

    #[Fetch]
    private CommandLocator $locator;

    public static function structure(): array
    {
        return [
            static::argument('command', Argument::OPTIONAL, Loc::getMessage('ASPECT_PRESET_COMMAND_HELP_COMMAND_ARGUMENT')),
        ];
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        if (!$input->hasArgument('command') || !$input->getArgument('command')) {
            $this->showList($output);
        } else {
            $this->explainCommand($input->getArgument('command'), $output);
        }
    }

    protected function showList(OutputInterface $output): void
    {

        $help = $this->withTable(function (Table $table) {
            $table->addRow([
                Color::BLUE->wrap("Aspect CLI: list of commands\n")
            ]);
            $table->addRow([
                Color::YELLOW->wrap("Usage:")
            ]);
            $table->addRow([
                '  ' . Color::GREEN->wrap("command") . Color::DARK_GREY->wrap(' [options] arguments')
            ]);
            $table->addRow([
                "\n" . Color::YELLOW->wrap("Available commands:")
            ]);


            $commands = $this->locator->groupOfCommands();
            $this->printCommandSection($table, $commands);

        });

        $output->write(trim($help, "\n\r\t\v\0") . "\n\n");
    }

    private function printCommandSection(Table $table, array $section) {
        foreach ($section as $name => $commandData) {
            if(isset($commandData['COMMAND']) && $command = $commandData['COMMAND']) {
                $table->addRow(["  " . $this->colorize(Color::GREEN, $name), $command::getDescription()]);
            }

            if (isset($commandData['SECTION'])) {
                $table->addRow([$this->colorize(Color::DARK_GREY, $name)]);
                $this->printCommandSection($table, $commandData['SECTION']);
            }
        }
    }

    private function withTable(callable $closure): string
    {
        $tableOutput = Fake::makeTextOutput();
        $table = new Table($tableOutput);
        $style = new TableStyle();
        $style->setBorderFormat('');
        $table->setStyle($style);

        $closure($table);

        $table->render();
        return $tableOutput->fetch();
    }

    public static function getDescription(): string
    {
        return Loc::getMessage('ASPECT_PRESET_COMMAND_HELP_DESCRIPTION');
    }

    /**
     * @throws CommandException
     */
    protected function explainCommand(string $commandName, OutputInterface $output): void
    {
        if ($command = $this->locator->locate($commandName)) {

            $definition = $this->getDefinition($command);

            $header = $this->withTable(function (Table $table) use ($definition, $command) {
                $table->addRow([Color::BLUE->wrap($command::getDescription())]);
                $table->addRow([Color::YELLOW->wrap('Usage:')]);
                $table->addRow(["  " . $command::getName() . $this->explainShort($definition)]);
            });

            $help = $this->withTable(function (Table $table) use ($definition) {
                if ($arguments = $definition->getArguments()) {
                    $table->addRow([Color::YELLOW->wrap("\nArguments:")]);

                    foreach ($arguments as $argument) {
                        $name = Color::GREEN->wrap('  ' . $argument->getName());
                        $desc = '';


                        if ($argument->isRequired()) {
                            $desc = Color::RED->wrap('[required]') . ' ';
                        }

                        if ($argument->isArray()) {
                            $desc .= Color::DARK_GREY->wrap('[repeatable]') . ' ';
                        }

                        $desc .= $argument->getDescription();

                        if (!$argument->isRequired() && $argument->getDefault() !== null) {
                            $default = $argument->getDefault();
                            if ($default === false) {
                                $default = 'false';
                            } else if (is_array($default)) {
                                $default = implode(", ", $default);
                            }

                            $desc .= Color::YELLOW->wrap(' [default: ' . $default . ']');
                        }

                        $table->addRow([$name, $desc]);
                    }
                }

                if ($options = $definition->getOptions()) {
                    $table->addRow([Color::YELLOW->wrap("\nOptions:")]);

                    foreach ($options as $option) {
                        $name = Color::GREEN->wrap("  --" . $option->getName());
                        if ($shortcuts = $option->getShortcut()) {
                            $shortcutIterator = is_array($shortcuts) ? $shortcuts : [$shortcuts];
                            foreach ($shortcutIterator as $shortcut) {
                                $name .= Color::GREEN->wrap(" -" . $shortcut);
                            }
                        }

                        $desc = '';
                        if ($option->isValueRequired()) {
                            $desc = Color::RED->wrap('[required]') . ' ';
                        }

                        if ($option->isArray()) {
                            $desc .= Color::DARK_GREY->wrap('[repeatable]') . ' ';
                        }

                        $desc .= $option->getDescription();

                        if (!$option->isValueRequired() && $option->getDefault() !== null) {
                            $default = $option->getDefault();
                            if ($default === false) {
                                $default = 'false';
                            } else if (is_array($default)) {
                                $default = implode(", ", $default);
                            }

                            $desc .= Color::YELLOW->wrap(' [default: ' . $default . ']');
                        }

                        $table->addRow([$name, $desc]);
                    }
                }

            });
            $output->write(trim($header, "\n\r\t\v\0") . "\n");
            $output->write(trim($help, "\n\r\t\v\0") . "\n\n");

        } else {
            throw new CommandException("Unknown command: " . $commandName);
        }
    }

    protected function explainShort(InputDefinition $definition): string
    {
        $result = '';

        foreach ($definition->getOptions() as $option) {
            $result .= ' [';
            if ($shortcuts = $option->getShortcut()) {
                $shortcutIterator = is_array($shortcuts) ? $shortcuts : [$shortcuts];
                foreach ($shortcutIterator as $shortcut) {
                    $result .= "-" . $shortcut . "|";
                }
            }
            $result .= "--" . $option->getName();
            $result .= "]";
        }

        foreach ($definition->getArguments() as $argument) {
            $result .= ' ' . $argument->getName();
        }

        return $result;
    }
}