<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class SpeechToTextService
{
    protected ?string $apiUrl;
    protected ?string $apiKey;

    public function __construct()
    {
        $this->apiUrl = env('STT_API_URL', 'https://api.stt.ai/v1/transcribe');
        $this->apiKey = env('STT_API_KEY');
    }

    /**
     * Transcribe audio file to text
     *
     * @param string $filePath Absolute path to the audio file
     * @return string
     * @throws Exception
     */
    public function transcribe(string $filePath): string
    {
        if (!file_exists($filePath)) {
            throw new Exception("File audio tidak ditemukan.");
        }

        // Check if API configuration is missing or set to mock
        if (empty($this->apiKey) || $this->apiKey === 'mock') {
            Log::info('Running STT in mock mode');
            return $this->getMockTranscription();
        }

        try {
            $fileStream = fopen($filePath, 'r');
            
            $response = Http::withToken($this->apiKey)
                ->attach('file', $fileStream, basename($filePath))
                ->post($this->apiUrl, [
                    'model' => 'large-v3-turbo',
                    'language' => 'id',
                ]);

            if ($response->failed()) {
                Log::error('STT API Error Response: ' . $response->body());
                throw new Exception("Gagal melakukan transkripsi: " . $response->status() . " " . $response->reason());
            }

            $data = $response->json();
            
            // Handle different JSON structures returned by STT APIs
            $text = $data['text'] ?? $data['transcription'] ?? $data['transcript'] ?? null;
            
            if ($text === null) {
                Log::warning('STT Response format unexpected: ' . json_encode($data));
                throw new Exception("Format data transkripsi tidak dikenali.");
            }

            return trim($text);

        } catch (Exception $e) {
            Log::error('SpeechToTextService Exception: ' . $e->getMessage());
            
            // In local/development, fallback to a mock transcription to keep the app working
            if (config('app.env') === 'local') {
                Log::warning('STT API failed. Falling back to mock transcription for local testing.');
                return $this->getMockTranscription();
            }
            
            throw $e;
        }
    }

    /**
     * Return a mock transcription for testing purposes
     */
    protected function getMockTranscription(): string
    {
        $phrases = [
            "saya membeli nasi goreng seharga 25000 rupiah",
            "saya bayar uang kos sebesar 800000 rupiah",
            "saya mendapatkan bonus gaji 1500000 rupiah dari kantor",
            "saya jajan bakso seharga 15000",
            "saya menerima kiriman uang dari orang tua 500000 rupiah",
            "saya membeli bensin 20000 rupiah tadi pagi",
        ];
        
        return $phrases[array_rand($phrases)];
    }
}
