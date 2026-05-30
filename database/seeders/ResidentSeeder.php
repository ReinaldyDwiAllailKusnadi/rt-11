<?php

namespace Database\Seeders;

use App\Models\Resident;
use App\Models\Payment;
use Illuminate\Database\Seeder;

class ResidentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Setting::updateOrCreate(['key' => 'saldo_awal_kas'], ['value' => 2500000]);
        \App\Models\Setting::updateOrCreate(['key' => 'saldo_awal_keamanan'], ['value' => 3000000]);
        \App\Models\Setting::updateOrCreate(['key' => 'nama_ketua_rt'], ['value' => 'MUHAMMAD NASIKUN']);

        $residents = [
            ['name' => 'MATHEUS ALAN SEPTIAN', 'no_rumah' => 'I.1'],
            ['name' => 'ANTUN JATMIKO', 'no_rumah' => 'I.2'],
            ['name' => 'MUHAMMAD FUAD HASIM', 'no_rumah' => 'I.3'],
            ['name' => 'SONNY AMMY', 'no_rumah' => 'I.6'],
            ['name' => 'HERU KURNIAWA', 'no_rumah' => 'J.1'],
            ['name' => 'YULIANTO', 'no_rumah' => 'J.2'],
            ['name' => 'RIAN RISKI RINALDI', 'no_rumah' => 'J.4'],
            ['name' => 'CATUR KURNIAWAN CAHYO', 'no_rumah' => 'J.5'],
            ['name' => 'TROY TRESTINO', 'no_rumah' => 'J.6'],
            ['name' => 'ANDRY RACHMAN', 'no_rumah' => 'J.7'],
            ['name' => 'IMRLDA STEFARIANA', 'no_rumah' => 'J.8'],
            ['name' => 'MAS RAFI', 'no_rumah' => 'J.9'],
            ['name' => 'ZAM ZAM PRIONO', 'no_rumah' => 'J.10'],
            ['name' => 'HARSITO', 'no_rumah' => 'J.11'],
            ['name' => 'SUYADI', 'no_rumah' => 'J.12'],
            ['name' => 'PUPUT ADE IRAWAN', 'no_rumah' => 'J.15'],
            ['name' => 'MARGO SANTOSO', 'no_rumah' => 'J.16'],
            ['name' => 'FARHAN/FARHAT', 'no_rumah' => 'J.17'],
            ['name' => 'HANANTINO RAMADANI', 'no_rumah' => 'J.19'],
            ['name' => 'HERMANTARULI HANDONO', 'no_rumah' => 'J.20'],
            ['name' => 'FASIAL ANGGRAIAWAN', 'no_rumah' => 'J.21'], // corrected to FASIAL ANGGRAIAWAN or FASIAL ANGGRIAWAN
            ['name' => 'ROSI', 'no_rumah' => 'J.22'],
            ['name' => 'YUSTIAN INDRASWARA SAMPRADANA', 'no_rumah' => 'K.1'],
            ['name' => 'DYAH CATUR PRATIWI', 'no_rumah' => 'K.3'],
            ['name' => 'ASHARI INDRAMAWAN', 'no_rumah' => 'K.4'],
            ['name' => 'BRIAN BAGUS PANUNTUN', 'no_rumah' => 'K.5'],
            ['name' => 'SITI SETIA NINGSIH', 'no_rumah' => 'K.6'],
            ['name' => 'SATRIA ARIQ', 'no_rumah' => 'K.7'],
            ['name' => 'ACHMAD YANI AZIZ', 'no_rumah' => 'K.8'],
            ['name' => 'LUHUR BUDI SANTOSO', 'no_rumah' => 'K.9'],
            ['name' => 'MUHAMMAD SENOPATI', 'no_rumah' => 'K.10'],
            ['name' => 'HARSITO/MESS', 'no_rumah' => 'K.12'],
            ['name' => 'ADITYA PRASETYO', 'no_rumah' => 'K.14'],
            ['name' => 'RAMADHAN BAGUS SAPUTRO', 'no_rumah' => 'K.15'],
            ['name' => 'ISWANDI', 'no_rumah' => 'K.16'],
            ['name' => 'TARYO', 'no_rumah' => 'K.17'],
            ['name' => 'ANTONI MIKWIN', 'no_rumah' => 'K.18'],
            ['name' => 'MBAH MUJI', 'no_rumah' => 'K.19'],
            ['name' => 'ANGGA YOSAINTO BEQUET', 'no_rumah' => 'K.20'],
            ['name' => 'WIWIT MUGIONO', 'no_rumah' => 'K.21'],
            ['name' => 'TRI BUDI SEPTIONO', 'no_rumah' => 'L.1'],
            ['name' => 'ALI RAHMAN', 'no_rumah' => 'L.2'],
            ['name' => 'HERI SUPRIYANTO', 'no_rumah' => 'L.3'],
            ['name' => 'HARISYA CAHYO WIBOWO', 'no_rumah' => 'L.4'],
            ['name' => 'ALI MAKSUM', 'no_rumah' => 'L.5'],
            ['name' => 'BAGJA PRAYITNO', 'no_rumah' => 'L.6'],
            ['name' => 'DEDY KURNIAWAN', 'no_rumah' => 'L.8'],
            ['name' => 'SUMARNO', 'no_rumah' => 'L.10'],
            ['name' => 'TRI ANGGARAWATI', 'no_rumah' => 'L.11'],
            ['name' => 'TITIN PURWANINGSIH', 'no_rumah' => 'L.12'],
            ['name' => 'MUHAMMAD NASIKUN', 'no_rumah' => 'L.14'],
            ['name' => 'SUMIANTO RAHARDJO', 'no_rumah' => 'L.16'],
            ['name' => 'BONDAN PURBO NUGROHO', 'no_rumah' => 'L.17'],
            ['name' => 'AHWAL YANUAR ESTOWO', 'no_rumah' => 'L.18'],
            ['name' => 'GALIH YOGA PATRIA', 'no_rumah' => 'L.19'],
            ['name' => 'HENDRA MARDOVA', 'no_rumah' => 'L.20'],
            ['name' => 'SINGGIH RAHMANA', 'no_rumah' => 'L.21'],
            ['name' => 'BARAZATI AKRIMUL AZIZ', 'no_rumah' => 'M.1'],
            ['name' => 'DANY AGUS P', 'no_rumah' => 'M.2'],
            ['name' => 'NUGRAHA ADI PRABAWA', 'no_rumah' => 'M.3'],
            ['name' => 'BUDI WALUYO', 'no_rumah' => 'M.4'],
            ['name' => 'RAHENDRA', 'no_rumah' => 'M.5'],
            ['name' => 'SUGIYANTO', 'no_rumah' => 'M.7'],
            ['name' => 'ERLANGGA', 'no_rumah' => 'M.8'],
            ['name' => 'GIGIEH HANGGAR JAYA', 'no_rumah' => 'M.9'],
            ['name' => 'REINALDY', 'no_rumah' => 'M.10']
        ];

        foreach ($residents as $r) {
            Resident::create($r);
        }

        // Seed some sample payments from the HTML file
        Payment::create([
            'resident_id' => 1, // Matheus
            'type' => 'kas',
            'amount' => 20000,
            'date' => '2026-03-01',
            'keterangan' => 'Bayar iuran Kas RT untuk bulan: Maret 2026 (1 bulan)',
            'bulan_list' => ['2026-03']
        ]);

        Payment::create([
            'resident_id' => 2, // Antun Jatmiko
            'type' => 'kas',
            'amount' => 20000,
            'date' => '2026-03-02',
            'keterangan' => 'Bayar iuran Kas RT untuk bulan: Maret 2026 (1 bulan)',
            'bulan_list' => ['2026-03']
        ]);

        Payment::create([
            'resident_id' => 3, // Muhammad Fuad
            'type' => 'keamanan',
            'amount' => 55000,
            'date' => '2026-03-02',
            'keterangan' => 'Bayar iuran Keamanan untuk bulan: Maret 2026 (1 bulan)',
            'bulan_list' => ['2026-03']
        ]);

        Payment::create([
            'resident_id' => 4, // Sonny Ammy
            'type' => 'kemalangan',
            'amount' => 200000,
            'date' => '2026-03-03',
            'keterangan' => 'Bantuan kemalangan'
        ]);

        Payment::create([
            'resident_id' => 1, // Matheus
            'type' => 'sakit',
            'amount' => 150000,
            'date' => '2026-03-05',
            'keterangan' => 'Biaya pengobatan'
        ]);

        Payment::create([
            'resident_id' => null,
            'type' => 'bayarSATPAM',
            'amount' => 1000000,
            'date' => '2026-03-20',
            'nama_satpam' => 'SATPAM BUDI',
            'keterangan' => 'Gaji Satpam'
        ]);

        Payment::create([
            'resident_id' => null,
            'type' => 'konsumsiRAPAT',
            'amount' => 250000,
            'date' => '2026-03-22',
            'keterangan' => 'Rapat rutin'
        ]);

        Payment::create([
            'resident_id' => null,
            'type' => 'lainLAIN',
            'amount' => 150000,
            'date' => '2026-03-24',
            'keterangan' => 'Perlengkapan RT'
        ]);
    }
}
