<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\App;

use olml89\TelegramUserbot\Shared\Bot\Process\ProcessResult;
use Stringable;

/**
 * Wrapper of the results of the php exec() function
 */
final readonly class ExecResult implements Stringable
{
    public function __construct(
        public false|string $result,
        /**
         * @var string[]
         */
        public array $output,
        public int $code,
    ) {}

    public function hasProcessResult(ProcessResult $processResult): bool
    {
        if ($this->code !== 0 || count($this->output) === 0) {
            return false;
        }

        return str_contains(mb_strtolower($this->output[0]), $processResult->value);
    }

    public function __toString(): string
    {
        $result = ($this->result === false)
            ? 'null'
            : $this->result;

        $output = (count($this->output) === 0)
            ? 'null'
            : $this->output[0];

        $string = json_encode([
            'result' => $result,
            'output' => $output,
            'exitCode' => $this->code,
        ]);

        return ($string === false) ? '' : $string;
    }
}
