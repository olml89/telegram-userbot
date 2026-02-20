<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Infrastructure\Symfony\Process;

use InvalidArgumentException;
use JsonException;
use olml89\TelegramUserbot\Backend\File\Domain\FileName\FileName;
use olml89\TelegramUserbot\Backend\File\Domain\StorageFile\StorageFile;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Uid\Uuid;
use Webmozart\Assert\Assert;

trait ItRunsExternalProcess
{
    protected function createTemporaryFile(StorageFile $storageFile): StorageFile
    {
        /**
         * tmp StorageFile name: random UUID, same extension as the original StorageFile
         * (ffmpeg deducts the format of the container from the extension of the output file)
         */
        $tmpFileName = FileName::from(
            name: Uuid::v4(),
            extension: $storageFile->getExtension(),
        );

        return new StorageFile(
            sprintf(
                '%s/%s',
                $storageFile->getPath(),
                $tmpFileName->value,
            ),
        );
    }

    /**
     * @throws ProcessFailedException
     */
    protected function run(Process $process, ?callable $onError = null): string
    {
        $process->run();

        if (!$process->isSuccessful()) {
            if (!is_null($onError)) {
                $onError();
            }

            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }

    /**
     * @return array<string, mixed>
     *
     * @throws JsonException
     */
    protected function decodeJsonOutput(string $output): array
    {
        try {
            $data = json_decode(
                $output,
                associative: true,
                flags: JSON_THROW_ON_ERROR,
            );

            Assert::isArray($data);
            Assert::allString(array_keys($data));

            /** @var array<string, mixed> $data */
            return $data;
        } catch (InvalidArgumentException $e) {
            throw new JsonException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
