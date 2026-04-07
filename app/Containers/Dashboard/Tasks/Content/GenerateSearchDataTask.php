<?php

namespace App\Containers\Dashboard\Tasks\Content;

class GenerateSearchDataTask
{
    /**
     * Generate searchable text from content builder blocks
     */
    public function run(array $content): string
    {
        $result = '';
        
        foreach ($content as $block) {
            $result .= $this->getDataFromBlocks($block);
        }
        
        // Remove extra whitespace and newlines
        $result = preg_replace('/\s+/', ' ', $result);
        $result = trim($result);

        return strtolower($result);
    }

    private function getDataFromBlocks($block): string
    {
        $data = '';
        
        switch ($block['type']) {
            case 'paragraph':
                $data .= strip_tags($block['data']['content']) . ' ';
                break;
            case 'heading':
                $data .= strip_tags($block['data']['content']) . ' ';
                break;
            case 'files':
                foreach ($block['data']['file'] as $file) {
                    $data .= $file['title'] . ' ';
                }
                break;
            case 'person':
                $data .= $block['data']['name'] . ' ';
                break;
            case 'stepper':
                $data .= $block['data']['step_name'] . ' ';
                foreach ($block['data']['steps'] as $step) {
                    $data .= $step['title'] . ' ';
                    $data .= strip_tags($step['content']) . ' ';
                }
                break;
            case 'tabs':
                foreach ($block['data']['tab'] as $item) {
                    foreach ($item['content'] as $block) {
                        $data .= $this->getDataFromBlocks($block);
                    }
                }
                break;
        }
        
        return $data;
    }
}
