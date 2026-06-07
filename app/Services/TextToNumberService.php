<?php

namespace App\Services;

class TextToNumberService
{
    /**
     * Convert Indonesian number words in a string to numeric digits.
     * Example: "saya beli bakso dua puluh ribu" -> "saya beli bakso 20000"
     * 
     * @param string $text
     * @return string
     */
    public static function convert(string $text): string
    {
        $words = explode(' ', $text);
        
        $dictionary = [
            'satu' => 1, 'dua' => 2, 'tiga' => 3, 'empat' => 4, 'lima' => 5,
            'enam' => 6, 'tujuh' => 7, 'delapan' => 8, 'sembilan' => 9,
            'nol' => 0, 'kosong' => 0
        ];
        
        $multipliers = [
            'belas' => ['type' => 'add', 'value' => 10],
            'puluh' => ['type' => 'multiply', 'value' => 10],
            'ratus' => ['type' => 'multiply', 'value' => 100],
        ];

        $scales = [
            'ribu' => 1000,
            'juta' => 1000000,
            'miliar' => 1000000000,
            'triliun' => 1000000000000,
        ];

        $replacements = [
            'sepuluh' => 'satu puluh',
            'sebelas' => 'satu belas',
            'seratus' => 'satu ratus',
            'seribu' => 'satu ribu',
            'sejuta' => 'satu juta',
            'semiliar' => 'satu miliar',
            'setriliun' => 'satu triliun',
        ];

        $processedWords = [];
        foreach ($words as $word) {
            $lowerWord = strtolower($word);
            
            $numericCheck = str_replace(',', '', $lowerWord);
            if (is_numeric($numericCheck)) {
                $processedWords[] = [
                    'original' => $word,
                    'clean' => '',
                    'is_numeric' => true,
                    'value' => (float) $numericCheck
                ];
                continue;
            }

            $cleanWord = preg_replace('/[^a-z]/', '', $lowerWord);
            
            if (isset($replacements[$cleanWord])) {
                $replacementParts = explode(' ', $replacements[$cleanWord]);
                foreach ($replacementParts as $rp) {
                    $processedWords[] = [
                        'original' => $rp, // original casing/punctuation is lost for replaced words, but it's fine for numbers
                        'clean' => $rp
                    ];
                }
            } else {
                $processedWords[] = [
                    'original' => $word,
                    'clean' => $cleanWord
                ];
            }
        }

        $resultTokens = [];
        $numberSequence = [];

        foreach ($processedWords as $pw) {
            $c = $pw['clean'];
            if ((isset($pw['is_numeric']) && $pw['is_numeric']) || isset($dictionary[$c]) || isset($multipliers[$c]) || isset($scales[$c])) {
                $numberSequence[] = $pw;
            } else {
                if (!empty($numberSequence)) {
                    $resultTokens[] = self::parseSequence($numberSequence, $dictionary, $multipliers, $scales);
                    $numberSequence = [];
                }
                $resultTokens[] = $pw['original'];
            }
        }

        if (!empty($numberSequence)) {
            $resultTokens[] = self::parseSequence($numberSequence, $dictionary, $multipliers, $scales);
        }

        return implode(' ', $resultTokens);
    }

    private static function parseSequence(array $sequence, array $dictionary, array $multipliers, array $scales): string
    {
        $total = 0;
        $blockValue = 0;
        $partValue = 0;

        foreach ($sequence as $item) {
            if (isset($item['is_numeric']) && $item['is_numeric']) {
                $blockValue += $partValue;
                $partValue = $item['value'];
                continue;
            }

            $w = $item['clean'];

            if (isset($dictionary[$w])) {
                $blockValue += $partValue;
                $partValue = $dictionary[$w];
            } elseif (isset($multipliers[$w])) {
                if ($partValue === 0) $partValue = 1; // e.g. "ratus" alone
                
                if ($multipliers[$w]['type'] === 'add') {
                    $partValue += $multipliers[$w]['value'];
                } elseif ($multipliers[$w]['type'] === 'multiply') {
                    $partValue *= $multipliers[$w]['value'];
                }
                $blockValue += $partValue;
                $partValue = 0;
            } elseif (isset($scales[$w])) {
                $blockValue += $partValue;
                $partValue = 0;
                
                if ($blockValue === 0) $blockValue = 1; // e.g. "ribu" alone
                
                $total += $blockValue * $scales[$w];
                $blockValue = 0;
            }
        }

        $blockValue += $partValue;
        $total += $blockValue;

        return (string) $total;
    }
}
