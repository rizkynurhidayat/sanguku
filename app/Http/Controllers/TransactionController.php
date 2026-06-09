<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\SpeechToTextService;
use App\Services\CategoryClassifierService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Exception;

class TransactionController extends Controller
{
    protected SpeechToTextService $sttService;
    protected CategoryClassifierService $classifier;

    public function __construct(SpeechToTextService $sttService, CategoryClassifierService $classifier)
    {
        $this->sttService = $sttService;
        $this->classifier = $classifier;
    }

    /**
     * Display the financial dashboard
     */
    public function index()
    {
        $transactions = Transaction::where('user_id', Auth::id())->orderBy('transaction_date', 'desc')->get();

        // Calculate summary statistics
        $totalIncome = Transaction::where('user_id', Auth::id())->where('type', 'income')->sum('amount');
        $totalExpense = Transaction::where('user_id', Auth::id())->where('type', 'expense')->sum('amount');
        $netBalance = $totalIncome - $totalExpense;

        // Calculate category allocation breakdown
        $needsSum = Transaction::where('user_id', Auth::id())
            ->where('category_group', 'Needs')
            ->sum('amount');

        $wantsSum = Transaction::where('user_id', Auth::id())
            ->where('category_group', 'Wants')
            ->sum('amount');

        $savingsSum = Transaction::where('user_id', Auth::id())
            ->where('category_group', 'Savings')
            ->sum('amount');

        $otherSum = Transaction::where('user_id', Auth::id())
            ->where('type', 'expense')
            ->where(function ($query) {
                $query->where('category_group', 'Lainnya')
                    ->orWhereNull('category_group');
            })
            ->sum('amount');

        return view('dashboard', compact(
            'transactions', 
            'totalIncome', 
            'totalExpense', 
            'netBalance', 
            'needsSum', 
            'wantsSum', 
            'savingsSum', 
            'otherSum'
        ));
    }

    /**
     * Process voice recording and save transaction
     */
    public function storeVoice(Request $request)
    {
        $request->validate([
            'audio' => 'required|file|mimes:webm,wav,mp3,ogg,m4a'
        ]);

        try {
            // Save upload temporarily
            $audioFile = $request->file('audio');
            $tempPath = $audioFile->storeAs('temp', uniqid() . '.' . $audioFile->getClientOriginalExtension());
            $absolutePath = storage_path('app/private/' . $tempPath); // Laravel 11/13 private storage directory path

            // In older Laravel versions, it might be storage_path('app/' . $tempPath)
            if (!file_exists($absolutePath)) {
                $absolutePath = storage_path('app/' . $tempPath);
            }

            // Transcribe audio to text
            $transcript = $this->sttService->transcribe($absolutePath);

            // Normalize Indonesian number words to digits
            if (!empty($transcript)) {
                $transcript = \App\Services\TextToNumberService::convert($transcript);
            }

            // Clean up temporary audio file
            if (file_exists($absolutePath)) {
                unlink($absolutePath);
            }

            if (empty($transcript)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada suara yang terdeteksi atau transkripsi kosong.'
                ], 422);
            }

            // Parse transaction details (Type and Amount)
            $parsedData = $this->parseTranscript($transcript);

            if ($parsedData['amount'] <= 0) {
                return response()->json([
                    'success' => false,
                    'transcript' => $transcript,
                    'message' => "Transkrip terdeteksi: \"{$transcript}\", tetapi nominal uang tidak ditemukan. Silakan ulangi dengan menyebutkan nominal nominal angka dengan jelas."
                ], 422);
            }

            // Classify category based on transcript
            $categoryData = $this->classifier->classify($transcript);

            // Save to database
            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'type' => $parsedData['type'],
                'amount' => $parsedData['amount'],
                'description' => $transcript,
                'category_group' => $categoryData['group'],
                'sub_category' => $categoryData['sub_category'],
                'transaction_date' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dicatat!',
                'data' => $transaction
            ]);

        } catch (Exception $e) {
            Log::error('Transaction creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a transaction
     */
    public function destroy(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $transaction->delete();
        return redirect()->route('dashboard')->with('success', 'Transaksi berhasil dihapus.');
    }

    /**
     * Update a transaction
     */
    public function update(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:income,expense',
            'category_group' => 'nullable|string|max:50',
            'sub_category' => 'nullable|string|max:50',
        ]);

        $transaction->update([
            'description' => $request->description,
            'amount' => $request->amount,
            'type' => $request->type,
            'category_group' => $request->category_group ?: 'Lainnya',
            'sub_category' => $request->sub_category ?: 'Belum Dikategorikan',
        ]);

        return redirect()->route('dashboard')->with('success', 'Transaksi berhasil diperbarui.');
    }

    /**
     * Export transaction history to PDF
     */
    public function exportPdf()
    {
        $transactions = Transaction::where('user_id', Auth::id())->orderBy('transaction_date', 'desc')->get();
        $totalIncome = Transaction::where('user_id', Auth::id())->where('type', 'income')->sum('amount');
        $totalExpense = Transaction::where('user_id', Auth::id())->where('type', 'expense')->sum('amount');
        $netBalance = $totalIncome - $totalExpense;

        $pdf = Pdf::loadView('pdf', compact('transactions', 'totalIncome', 'totalExpense', 'netBalance'));
        
        return $pdf->download('Laporan_Keuangan_SanguKu_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Parse text transcripts to categorize transaction and extract amount
     */
    protected function parseTranscript(string $transcript): array
    {
        $lowercase = strtolower($transcript);
        
        // Define keywords for income vs expense
        $expenseKeywords = ['beli', 'membeli', 'bayar', 'keluar', 'jajan', 'ongkos', 'belanja', 'pengeluaran', 'bensin', 'makan', 'minum', 'sewa', 'kos', 'pembayaran'];
        $incomeKeywords = ['dapat', 'menerima', 'terima', 'gaji', 'dikasih', 'bonus', 'pemasukan', 'transferan', 'untung', 'laba'];

        // Determine transaction type
        $type = 'expense'; // default fallback
        
        // Simple search
        $incomeScore = 0;
        $expenseScore = 0;

        foreach ($expenseKeywords as $keyword) {
            if (str_contains($lowercase, $keyword)) {
                $expenseScore++;
            }
        }

        foreach ($incomeKeywords as $keyword) {
            if (str_contains($lowercase, $keyword)) {
                $incomeScore++;
            }
        }

        if ($incomeScore > $expenseScore) {
            $type = 'income';
        }

        // Clean dots and commas commonly used in currency formatting in text, e.g. "50.000" or "800,000" -> "50000" or "800000"
        $cleanText = str_replace(['.', ','], '', $lowercase);
        
        // Find digits in the string
        preg_match_all('/\d+/', $cleanText, $matches);
        
        $amount = 0;
        if (!empty($matches[0])) {
            // Find the largest number in the array (e.g. if transcript is "beli bakso 1 porsi harga 15000", select 15000)
            $numbers = array_map('floatval', $matches[0]);
            $amount = max($numbers);
        }

        return [
            'type' => $type,
            'amount' => $amount
        ];
    }
}
