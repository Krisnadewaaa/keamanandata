<?php
// File: app/Console/Commands/CancelExpiredTransactions.php (Enhanced)

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaksi;
use Carbon\Carbon;

class CancelExpiredTransactions extends Command
{
    protected $signature = 'transactions:cancel-expired {--dry-run : Show what would be cancelled without actually cancelling}';
    protected $description = 'Cancel expired transactions and restore stock and points';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('Checking for expired transactions...');
        
        // Get expired transactions
        $expiredTransactions = Transaksi::where('STATUS_TRANSAKSI', 'Menunggu Pembayaran')
                                      ->where('BATAS_WAKTU_PEMBAYARAN', '<', Carbon::now())
                                      ->with(['pembeli', 'barang'])
                                      ->get();

        if ($expiredTransactions->isEmpty()) {
            $this->info('No expired transactions found.');
            return 0;
        }

        $this->info("Found {$expiredTransactions->count()} expired transactions:");
        
        // Display table of expired transactions
        $headers = ['ID', 'Transaction #', 'Buyer', 'Product', 'Amount', 'Points', 'Expired At'];
        $rows = [];
        
        foreach ($expiredTransactions as $transaction) {
            $rows[] = [
                $transaction->ID_TRANSAKSI,
                $transaction->NOMOR_TRANSAKSI,
                $transaction->pembeli->NAMA_PEMBELI ?? 'N/A',
                $transaction->barang->NAMA_BARANG ?? 'N/A',
                'Rp ' . number_format($transaction->TOTAL_TRANSAKSI, 0, ',', '.'),
                $transaction->POIN_DITUKAR,
                $transaction->BATAS_WAKTU_PEMBAYARAN->format('Y-m-d H:i:s')
            ];
        }
        
        $this->table($headers, $rows);

        if ($dryRun) {
            $this->warn('DRY RUN MODE: No transactions will be cancelled.');
            return 0;
        }

        if (!$this->confirm('Do you want to cancel these expired transactions?')) {
            $this->info('Operation cancelled by user.');
            return 0;
        }

        // Cancel the transactions
        $cancelledCount = 0;
        $failedCount = 0;
        
        $progressBar = $this->output->createProgressBar($expiredTransactions->count());
        $progressBar->start();

        foreach ($expiredTransactions as $transaction) {
            try {
                \DB::beginTransaction();

                // Return points to buyer if they were redeemed
                if ($transaction->POIN_DITUKAR > 0) {
                    $pembeli = $transaction->pembeli;
                    if ($pembeli) {
                        $pembeli->POINT_PEMBELI += $transaction->POIN_DITUKAR;
                        $pembeli->save();
                        
                        $this->line("\nReturned {$transaction->POIN_DITUKAR} points to buyer {$pembeli->NAMA_PEMBELI}");
                    }
                }

                // Return stock to product
                $barang = $transaction->barang;
                if ($barang) {
                    $barang->stok += 1;
                    if ($barang->stok > 0) {
                        $barang->STATUS = 'Tersedia';
                    }
                    $barang->save();
                    
                    $this->line("\nRestored stock for product {$barang->NAMA_BARANG}, new stock: {$barang->stok}");
                }

                // Update transaction status
                $transaction->STATUS_TRANSAKSI = 'Batal';
                $transaction->STATUS_VERIFIKASI = 'Invalid';
                $transaction->CATATAN_VERIFIKASI = 'Transaksi dibatalkan otomatis karena melewati batas waktu pembayaran 1 menit pada ' . Carbon::now()->format('Y-m-d H:i:s');
                $transaction->save();

                \DB::commit();
                $cancelledCount++;
                
            } catch (\Exception $e) {
                \DB::rollback();
                $failedCount++;
                $this->error("\nFailed to cancel transaction {$transaction->ID_TRANSAKSI}: " . $e->getMessage());
            }
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Summary
        $this->info("Cancellation completed:");
        $this->info("✅ Successfully cancelled: {$cancelledCount} transactions");
        
        if ($failedCount > 0) {
            $this->error("❌ Failed to cancel: {$failedCount} transactions");
        }
        
        // Log the operation
        \Log::info("Auto-cancelled {$cancelledCount} expired transactions", [
            'cancelled_count' => $cancelledCount,
            'failed_count' => $failedCount,
            'executed_at' => Carbon::now()->toDateTimeString()
        ]);

        return 0;
    }
}