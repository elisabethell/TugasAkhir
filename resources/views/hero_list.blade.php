<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MetaScout - Data Hero Lengkap</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 30px; background-color: #f0f2f5; color: #333; }
        
        /* Kontainer Utama dibatasi lebarnya agar tidak terlalu renggang */
        .container { max-width: 1100px; margin: 0 auto; }
        
        h2 { color: #2c3e50; margin-bottom: 5px; }
        p { color: #666; margin-top: 0; }
        .btn { display: inline-block; margin-bottom: 20px; padding: 8px 14px; background: #2c3e50; color: white; text-decoration: none; border-radius: 6px; font-size: 13px; font-weight: 600; }
        .btn:hover { background: #1a252f; }
        
        .table-container { background: #fff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); overflow: hidden; border: 1px solid #e1e4e8; }
        
        /* Pengaturan Lebar Kolom agar Simetris */
        table { width: 100%; border-collapse: collapse; font-size: 13px; table-layout: fixed; }
        th, td { padding: 12px 18px; text-align: left; border-bottom: 1px solid #eee; word-wrap: break-word; }
        
        th:nth-child(1), td:nth-child(1) { width: 28%; } /* Kolom Nama Hero */
        th:nth-child(2), td:nth-child(2) { width: 24%; } /* Kolom Class/Role */
        th:nth-child(3), td:nth-child(3) { width: 22%; } /* Kolom Laning */
        th:nth-child(4), td:nth-child(4) { width: 26%; } /* Kolom Speciality */

        th { background-color: #2c3e50; color: white; font-weight: 600; }
        tr:nth-child(even) { background-color: #f8f9fa; }
        tr:hover { background-color: #f1f4f8; }
        
        .hero-cell { display: flex; align-items: center; gap: 10px; }
        .hero-avatar { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid #ddd; background: #ccc; flex-shrink: 0; }
        
        /* Badge */
        .badge-role { background: #e8f8f5; color: #16a085; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; display: inline-block; margin: 2px; }
        .badge-lane { background: #ebf3fa; color: #2980b9; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; display: inline-block; margin: 2px; }
        .badge-spec { background: #fdf2f2; color: #c0392b; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; display: inline-block; margin: 2px; }
    </style>
</head>
<body>
    
    <div class="container">
        <h2>Kamus Data Hero - MetaScout</h2>
        <p>Daftar lengkap atribut, laning, kelas, dan relasi strategi RBR hero Mobile Legends.</p>
        
        <a href="/datasets" class="btn">Lihat Riwayat Dataset Pertandingan &raquo;</a>
        <hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nama Hero</th>
                        <th>Class (Role)</th>
                        <th>Laning</th>
                        <th>Speciality</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($heroes as $hero)
                    <tr>
                        <td>
                            <div class="hero-cell">
                                @php
                                    // Cek apakah portrait valid (bukan web galeri/kosong). Jika tidak valid, ambil dari folder lokal public/images/heroes/
                                    $imgSrc = (!empty($hero->portrait) && str_starts_with($hero->portrait, 'http') && !str_contains($hero->portrait, 'deviantart') && !str_contains($hero->portrait, 'mobilelegends.com')) 
                                              ? $hero->portrait 
                                              : asset('images/heroes/' . strtolower(str_replace(' ', '_', $hero->hero_name)) . '.png');
                                @endphp

                                <img src="{{ $imgSrc }}" 
                                     alt="{{ $hero->hero_name }}" 
                                     class="hero-avatar"
                                     onerror="this.src='https://via.placeholder.com/36?text=ML'">
                                     
                                <strong>{{ $hero->hero_name }}</strong>
                            </div>
                        </td>
                        <td>
                            @foreach(explode(',', $hero->class) as $role)
                                @if(trim($role))
                                    <span class="badge-role">{{ trim($role) }}</span>
                                @endif
                            @endforeach
                        </td>
                        <td>
                            @foreach(explode(',', $hero->laning) as $lane)
                                @if(trim($lane))
                                    <span class="badge-lane">{{ trim($lane) }}</span>
                                @endif
                            @endforeach
                        </td>
                        <td>
                            @foreach(explode(',', $hero->specialty) as $spec)
                                @if(trim($spec))
                                    <span class="badge-spec">{{ trim($spec) }}</span>
                                @endif
                            @endforeach
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>