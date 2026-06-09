<?php

namespace App\Services;

class CategoryClassifierService
{
    /**
     * Dictionary Keyword Kategori
     * Format: ['Grup Utama' => ['Sub Kategori' => ['keyword1', 'keyword2']]]
     */
    protected array $categories = [
        'Needs' => [
            'Makan & Minum' => ['makan', 'beli nasi', 'sarapan', 'makan siang', 'gofood', 'ketoprak'],
            'Transportasi & Kendaraan' => ['bensin', 'ojol', 'grab', 'gojek', 'parkir', 'servis motor', 'tiket kereta'],
            'Tagihan & Kebutuhan Rumah/Kos' => ['bayar kos', 'listrik', 'air', 'wifi', 'pulsa', 'paket data', 'belanja bulanan', 'kuota'],
            'Pendidikan / Kerja' => ['beli buku', 'print', 'fotokopi', 'alat tulis', 'kursus', 'seminar'],
        ],
        'Wants' => [
            'Nongkrong & Hiburan' => ['kopi', 'starbucks', 'cafe', 'nonton bioskop', 'netflix', 'spotify', 'biliar', 'konser'],
            'Belanja & Self-Reward' => ['beli baju', 'sepatu', 'skincare', 'makeup', 'checkout shopee', 'top up game'],
            'Sosial & Jajan' => ['kado ulang tahun', 'sumbangan', 'kondangan', 'jajan', 'cilok'],
        ],
        'Savings' => [
            'Tabungan & Investasi' => ['tabungan', 'beli emas', 'saham', 'reksadana', 'crypto', 'simpan uang'],
            'Dana Darurat' => ['dana darurat', 'simpanan darurat'],
        ]
    ];

    /**
     * Klasifikasikan teks STT ke dalam Kategori Utama dan Sub-kategori.
     *
     * @param string $sttText Teks hasil Speech to Text
     * @return array ['group' => string, 'sub_category' => string]
     */
    public function classify(string $sttText): array
    {
        $text = strtolower($sttText);

        foreach ($this->categories as $groupName => $subCategories) {
            foreach ($subCategories as $subCategoryName => $keywords) {
                foreach ($keywords as $keyword) {
                    if (str_contains($text, strtolower($keyword))) {
                        return [
                            'group' => $groupName,
                            'sub_category' => $subCategoryName,
                        ];
                    }
                }
            }
        }

        // Fallback jika tidak ada keyword yang cocok sama sekali
        return [
            'group' => 'Lainnya',
            'sub_category' => 'Belum Dikategorikan',
        ];
    }
}
