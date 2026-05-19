<?php

namespace App\Console\Commands;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Console\Command;

class RegisterArabicFont extends Command
{
    protected $signature = 'pdf:register-arabic-font';

    protected $description = 'Register Traditional Arabic font with DomPDF so Arabic text renders correctly in PDFs';

    public function handle(): int
    {
        $fontDir = storage_path('fonts');

        if (! is_dir($fontDir)) {
            mkdir($fontDir, 0755, true);
        }

        $fontFile = $fontDir.'/trado.ttf';

        if (! file_exists($fontFile)) {
            $this->error('Font file not found: '.$fontFile);
            $this->line('Run: Copy-Item "C:\\Windows\\Fonts\\trado.ttf" "'.$fontFile.'"');

            return self::FAILURE;
        }

        $options = new Options([
            'font_dir'   => $fontDir,
            'font_cache' => $fontDir,
            'chroot'     => realpath(base_path()),
        ]);

        $dompdf = new Dompdf($options);
        $canvas  = $dompdf->getCanvas();
        $metrics = new \Dompdf\FontMetrics($canvas, $options);

        $metrics->registerFont(
            ['family' => 'Traditional Arabic', 'weight' => 'normal', 'style' => 'normal'],
            $fontFile
        );

        $this->info('Traditional Arabic font registered successfully in: '.$fontDir);

        return self::SUCCESS;
    }
}
