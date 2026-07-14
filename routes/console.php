<?php

use Illuminate\Support\Facades\Schedule;

// Sinkronisasi kurs mata uang setiap hari jam 06:00 (untuk grafik naik-turun devisa historis)
Schedule::command('scm:sync-exchange-rates')->dailyAt('06:00');

// Hitung ulang skor risiko semua pengiriman aktif setiap 3 jam
// (menarik data cuaca terbaru, berita geopolitik/logistik terbaru, dsb)
Schedule::command('scm:recalculate-risks')->everyThreeHours();