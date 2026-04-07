<?php

namespace App\Containers\Dashboard\Tasks\AdditionalEducations;

class GenerateSearchDataTask
{
    public function run(array $content): string
    {
        $result = '';

        foreach ($content as $block) {
            $result .= $this->extractDataFromBlock($block);
        }

        // Удаляем лишние пробелы и переносы строк
        $result = preg_replace('/\s+/', ' ', $result);
        $result = trim($result);

        return strtolower($result);
    }

    private function extractDataFromBlock(array $block): string
    {
        $data = '';

        switch ($block['type']) {
            case 'paragraph':
            case 'heading':
                $data .= strip_tags($block['data']['content'] ?? '') . ' ';
                break;

            case 'files':
                if (isset($block['data']['file']) && is_array($block['data']['file'])) {
                    foreach ($block['data']['file'] as $file) {
                        $data .= ($file['title'] ?? '') . ' ';
                    }
                }
                break;

            case 'person':
                $data .= ($block['data']['name'] ?? '') . ' ';
                break;

            case 'stepper':
                $data .= ($block['data']['step_name'] ?? '') . ' ';
                if (isset($block['data']['steps']) && is_array($block['data']['steps'])) {
                    foreach ($block['data']['steps'] as $step) {
                        $data .= ($step['title'] ?? '') . ' ';
                        $data .= strip_tags($step['content'] ?? '') . ' ';
                    }
                }
                break;

            case 'tabs':
                if (isset($block['data']['tab']) && is_array($block['data']['tab'])) {
                    foreach ($block['data']['tab'] as $item) {
                        if (isset($item['content']) && is_array($item['content'])) {
                            foreach ($item['content'] as $nestedBlock) {
                                $data .= $this->extractDataFromBlock($nestedBlock);
                            }
                        }
                    }
                }
                break;
        }

        return $data;
    }
}
