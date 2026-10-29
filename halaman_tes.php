<!DOCTYPE html>
<html lang="id">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<head>

  
    <title>Lembar Kerja Tes Kecermatan</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/css/bootstrap.min.css">
    <style>
        .table td{
            padding: .45rem!important;
        }
        .tabel-kecermatan{
            width: 100%;
        }
        .tabel-kecermatan th, .tabel-kecermatan td {
            text-align: center;
            vertical-align: middle;
            border: 1px solid #b2b2b2!important;
        }
        .header-merah {
            
            color: white !important;
            font-weight: bold;
        }
         .header-merah th{
            background-color: #e60000!important;
         }
        .baris-abu {
            background-color: #d9d9d9 !important;
        }
        .input-radio {
            transform: scale(1.3);
            cursor: pointer;
        }
        .kotak-soal {
            /*letter-spacing: 15px;*/
            font-weight: bold;
            /*padding-left: 15px;*/
           text-align: center;
           /*width: 20px!important;*/

        }
    </style>
</head>
<body class="bg-light">
<div class="container my-4">
   <form id="form-kecermatan" action="?action=submit_kolektif" method="POST">
        <input type="hidden" id="hd_timer" value="<?= $timer ?>">
        <div class="table-responsive shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-secondary fw-bold">Kolom Soal Aktif</h4>
            <div id="box-timer" class="badge bg-dark fs-5 p-2 px-3 shadow-sm text-white">
                Sisa Waktu: <span id="teks-durasi" class="fw-bold">60</span> Detik
            </div>
        </div>

            <table class="table tabel-kecermatan mb-0">
                <thead>
                    <!-- Baris Kunci Angka -->
                    <tr class="header-merah" >
                        <th class="align-middle" rowspan="2" style="width: 10%" >No</th>
                        <th colspan="4" rowspan="2" class="align-middle" style="width: 40%">Soal</th>
                        <th style="width: 10%"><?= $kunci[0]; ?></th>
                        <th style="width: 10%"><?= $kunci[1]; ?></th>
                        <th style="width: 10%"><?= $kunci[2]; ?></th>
                        <th style="width: 10%"><?= $kunci[3]; ?></th>
                        <th style="width: 10%"><?= $kunci[4]; ?></th>
                    </tr>
                    <!-- Baris Kunci Huruf Opsi -->
                    <tr class="header-merah bg-danger">
                        <th>A</th>
                        <th>B</th>
                        <th>C</th>
                        <th>D</th>
                        <th>E</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($daftar_soal as $nomor => $soal): ?>
                    <tr class="baris-abu">
                        <!-- Nomor Urut Soal -->
                        <td style="width: 5%; font-weight: bold;"><?= $nomor; ?></td>
                        
                        <!-- Blok 4 Angka Soal -->
                            <?php for($i=0;$i<count($soal['angka_soal']);$i++) 
                                   echo ' <td class="kotak-soal" >'.$soal['angka_soal'][$i] . '</td>';    
                            ?>
                           
                        </td>
                        
                        <!-- Pilihan Radio Jawaban A sampai E -->
                        <td><input type="radio" name="jawaban[<?= $nomor; ?>]" value="A" class="input-radio"></td>
                        <td><input type="radio" name="jawaban[<?= $nomor; ?>]" value="B" class="input-radio"></td>
                        <td><input type="radio" name="jawaban[<?= $nomor; ?>]" value="C" class="input-radio"></td>
                        <td><input type="radio" name="jawaban[<?= $nomor; ?>]" value="D" class="input-radio"></td>
                        <td><input type="radio" name="jawaban[<?= $nomor; ?>]" value="E" class="input-radio"></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="text-end mt-4">
            <button type="submit" id="btn-submit-tes" class="btn btn-danger btn-lg px-5 fw-bold">Selesai & Kirim Jawaban</button>

        </div>
    </form>
</div>
<script src="../js/JQuery/jQuery-2.1.4.min.js"></script>
<script>
    // Konfigurasi Durasi Waktu Ujian (Contoh: 60 detik / 1 menit)
    let sisaWaktu = 60; 
    let timerOtomatis;
    let formulirSudahDikirim = false;

    function mulaiHitungMundur() {
        timerOtomatis = setInterval(function() {
            sisaWaktu--;
            
            // Perbarui teks durasi di elemen HTML UI
            $('#teks-durasi').text(sisaWaktu);

            // Efek visual: ubah teks menjadi merah jika waktu kritis di bawah 10 detik
            if (sisaWaktu <= 10) {
                $('#box-timer').removeClass('bg-dark').addClass('bg-danger animate-pulse');
            }

            // Jika waktu habis, matikan timer dan trigger auto-submit
            if (sisaWaktu <= 0) {
                clearInterval(timerOtomatis);
                autoSubmitFormulir();
            }
        }, 1000);
    }

    function autoSubmitFormulir() {
        if (!formulirSudahDikirim) {
            formulirSudahDikirim = true;
            
            // Hapus proteksi window agar form bisa tersubmit tanpa memicu alert browser
            $(window).off('beforeunload');
            
            // Ubah teks tombol menjadi status memproses
            $('#btn-submit-tes').prop('disabled', true).text('Waktu Habis! Mengirim jawaban...');
            
            // Lakukan submit form otomatis
            $('#form-kecermatan').submit();
        }
    }

    $(document).ready(function() {
        // Jalankan timer saat halaman selesai dimuat
        // console.log($("#hd_timer").val());
        if($("#hd_timer").val()==1)
        mulaiHitungMundur();

        // Ketika user menekan tombol submit manual
        $('#form-kecermatan').on('submit', function() {
            formulirSudahDikirim = true;
            $(window).off('beforeunload');
        });

        // Proteksi Sistem: Mencegah peserta tidak sengaja refresh (F5) atau menutup tab browser
        $(window).on('beforeunload', function() {
            if (!formulirSudahDikirim) {
                return "Apakah Anda yakin ingin meninggalkan tes? Progress jawaban Anda akan hilang.";
            }
        });
    });
</script>

</body>
</html>
