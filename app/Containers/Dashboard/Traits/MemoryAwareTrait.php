<?php

namespace App\Containers\Dashboard\Traits;

use Illuminate\Support\Facades\Log;

/**
 * Трейт для мониторинга использования памяти
 *
 * Добавляет методы для отслеживания потребления памяти
 * и автоматической сборки мусора в Tasks и Actions.
 *
 * Usage:
 *   class MyTask {
 *       use MemoryAwareTrait;
 *
 *       public function run() {
 *           $this->logMemoryUsage('start');
 *           // ... logic
 *           $this->collectGarbageIfNeeded();
 *       }
 *   }
 */
trait MemoryAwareTrait
{
    /**
     * Порог памяти для логирования (MB)
     */
    private int $memoryLogThreshold = 100 * 1024 * 1024; // 100MB

    /**
     * Порог памяти для принудительной сборки мусора (MB)
     */
    private int $gcThreshold = 400 * 1024 * 1024; // 400MB

    /**
     * Логировать текущее использование памяти
     *
     * @param string $context Контекст вызова (например, 'start', 'end', 'after_processing')
     * @param array $extra Дополнительные данные для логирования
     * @return void
     */
    protected function logMemoryUsage(string $context, array $extra = []): void
    {
        $currentMemory = memory_get_usage(true);
        $peakMemory = memory_get_peak_usage(true);

        // Логируем только если превышен порог
        if ($currentMemory > $this->memoryLogThreshold) {
            Log::info('[MemoryMonitor] Использование памяти', [
                'context' => $context,
                'current' => round($currentMemory / 1024 / 1024, 2) . 'MB',
                'peak' => round($peakMemory / 1024 / 1024, 2) . 'MB',
                'memory_limit' => ini_get('memory_limit'),
                ...$extra,
            ]);
        }
    }

    /**
     * Принудительная сборка мусора при превышении порога
     *
     * @return bool Была ли выполнена сборка мусора
     */
    protected function collectGarbageIfNeeded(): bool
    {
        $currentMemory = memory_get_usage(true);

        if ($currentMemory > $this->gcThreshold) {
            $memoryBefore = round($currentMemory / 1024 / 1024, 2);

            // Принудительная сборка мусора
            gc_collect_cycles();

            $memoryAfter = round(memory_get_usage(true) / 1024 / 1024, 2);
            $freed = round(($memoryBefore - $memoryAfter), 2);

            Log::info('[MemoryMonitor] Сборка мусора выполнена', [
                'memory_before' => $memoryBefore . 'MB',
                'memory_after' => $memoryAfter . 'MB',
                'freed' => $freed . 'MB',
            ]);

            return true;
        }

        return false;
    }

    /**
     * Проверить, не превышен ли лимит памяти
     *
     * @param float $usagePercent Процент использования (0.0 - 1.0)
     * @return bool
     */
    protected function isMemoryLimitExceeded(float $usagePercent = 0.8): bool
    {
        $memoryLimit = $this->parseMemoryLimit(ini_get('memory_limit'));

        if ($memoryLimit === -1) {
            // Без лимита
            return false;
        }

        $currentMemory = memory_get_usage(true);

        return $currentMemory > ($memoryLimit * $usagePercent);
    }

    /**
     * Распарсить значение memory_limit из PHP ini
     *
     * @param string $value Значение из ini_get('memory_limit')
     * @return int Размер в байтах (-1 если без лимита)
     */
    private function parseMemoryLimit(string $value): int
    {
        $value = trim($value);

        if ($value === '-1') {
            return -1;
        }

        // Проверяем, что последний символ — цифра (значит уже в байтах)
        $lastChar = strtolower(substr($value, -1));
        if (ctype_digit($lastChar)) {
            return (int) $value;
        }

        $number = (int) substr($value, 0, -1);

        return match ($lastChar) {
            'g' => $number * 1024 * 1024 * 1024,
            'm' => $number * 1024 * 1024,
            'k' => $number * 1024,
            default => (int) $value,
        };
    }

    /**
     * Установить порог логирования памяти (MB)
     *
     * @param int $megabytes
     * @return void
     */
    protected function setMemoryLogThreshold(int $megabytes): void
    {
        $this->memoryLogThreshold = $megabytes * 1024 * 1024;
    }

    /**
     * Установить порог сборки мусора (MB)
     *
     * @param int $megabytes
     * @return void
     */
    protected function setGcThreshold(int $megabytes): void
    {
        $this->gcThreshold = $megabytes * 1024 * 1024;
    }
}
