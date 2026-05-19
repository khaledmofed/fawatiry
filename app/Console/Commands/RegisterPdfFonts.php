<?php

namespace App\Console\Commands;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Console\Command;

class RegisterPdfFonts extends Command
{
    protected $signature = 'pdf:register-fonts';

    protected $description = 'Register Inter and Arabic fonts with DomPDF for correct PDF rendering';

    public function handle(): int
    {
        $fontDir = storage_path('fonts');

        if (! is_dir($fontDir)) {
            mkdir($fontDir, 0755, true);
        }

        $options = new Options([
            'font_dir'   => $fontDir,
            'font_cache' => $fontDir,
            'chroot'     => realpath(base_path()),
        ]);

        $dompdf  = new Dompdf($options);
        $canvas  = $dompdf->getCanvas();
        $metrics = new \Dompdf\FontMetrics($canvas, $options);

        $registered = [];

        // Inter — Regular / Bold / SemiBold
        $interFonts = [
            ['file' => 'Inter-Regular.ttf',  'weight' => 'normal', 'style' => 'normal'],
            ['file' => 'Inter-Bold.ttf',     'weight' => 'bold',   'style' => 'normal'],
            ['file' => 'Inter-SemiBold.ttf', 'weight' => '600',    'style' => 'normal'],
        ];

        foreach ($interFonts as $f) {
            $path = $fontDir.'/'.$f['file'];
            if (! file_exists($path)) {
                $this->warn("Skipping {$f['file']} — not found in {$fontDir}");
                continue;
            }
            $metrics->registerFont(
                ['family' => 'Inter', 'weight' => $f['weight'], 'style' => $f['style']],
                $path
            );
            $registered[] = "Inter {$f['weight']}";
        }

        // Traditional Arabic
        $arabicPath = $fontDir.'/trado.ttf';
        if (file_exists($arabicPath)) {
            $metrics->registerFont(
                ['family' => 'Traditional Arabic', 'weight' => 'normal', 'style' => 'normal'],
                $arabicPath
            );
            $registered[] = 'Traditional Arabic';
        } else {
            $this->warn('Traditional Arabic font not found — skipping.');
        }

        if ($registered) {
            $this->info('Registered fonts: '.implode(', ', $registered));
            $this->info('Font cache: '.$fontDir);
        } else {
            $this->error('No fonts were registered. Check that TTF files exist in '.$fontDir);
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
