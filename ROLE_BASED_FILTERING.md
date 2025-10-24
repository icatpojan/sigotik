# Role-Based Filtering untuk API

## Overview
Sistem filtering berdasarkan role user telah diimplementasikan untuk API Kapal dan BBM. User non-admin hanya dapat melihat data yang terkait dengan kapal yang mereka miliki.

## Implementasi

### 1. Kapal Controller (`/api/v1/kapals`)

#### Filtering Logic:
- **Admin (conf_group_id = 1)**: Dapat melihat semua kapal
- **Non-Admin**: Hanya dapat melihat kapal yang terkait dengan user melalui tabel `sys_user_kapal`

#### Method yang Dimodifikasi:
- `index()`: List kapal dengan filtering
- `show($id)`: Detail kapal dengan filtering

#### Code Example:
```php
// Role-based filtering
if ($user->conf_group_id != 1) { // Jika bukan admin
    // Ambil kapal yang terkait dengan user melalui sys_user_kapal
    $userKapalIds = DB::table('sys_user_kapal')
        ->where('conf_user_id', $user->conf_user_id)
        ->pluck('m_kapal_id')
        ->toArray();
    
    if (!empty($userKapalIds)) {
        $query->whereIn('m_kapal_id', $userKapalIds);
    } else {
        // Jika user tidak memiliki kapal, return empty
        return response()->json([
            'success' => true,
            'data' => []
        ]);
    }
}
```

### 2. BBM Controller (`/api/v1/bbm`)

#### Filtering Logic:
- **Admin (conf_group_id = 1)**: Dapat melihat semua data BBM
- **Non-Admin**: Hanya dapat melihat data BBM dari kapal yang mereka miliki

#### Method yang Dimodifikasi:
- `index()`: List BBM dengan filtering

#### Code Example:
```php
// Role-based filtering
if ($user->conf_group_id != 1) { // Jika bukan admin
    // Ambil kapal yang terkait dengan user melalui sys_user_kapal
    $userKapalIds = DB::table('sys_user_kapal')
        ->where('conf_user_id', $user->conf_user_id)
        ->pluck('m_kapal_id')
        ->toArray();
    
    if (!empty($userKapalIds)) {
        // Filter BBM berdasarkan kapal yang dimiliki user
        $query->whereHas('kapal', function ($kapalQuery) use ($userKapalIds) {
            $kapalQuery->whereIn('m_kapal_id', $userKapalIds);
        });
    } else {
        // Jika user tidak memiliki kapal, return empty
        return response()->json([
            'success' => true,
            'data' => [],
            'pagination' => [...]
        ]);
    }
}
```

### 3. BA Sebelum Pengisian Controller (`/api/v1/ba-sebelum-pengisian`)

#### Filtering Logic:
- **Admin (conf_group_id = 1)**: Dapat melihat semua data BA Sebelum Pengisian
- **Non-Admin**: Hanya dapat melihat data BA dari kapal yang mereka miliki

#### Method yang Dimodifikasi:
- `getData()`: List BA Sebelum Pengisian dengan filtering
- `getKapalData()`: Data kapal dengan filtering

### 4. BA Pemeriksaan Sarana Pengisian Controller (`/api/v1/ba-pemeriksaan-sarana-pengisian`)

#### Filtering Logic:
- **Admin (conf_group_id = 1)**: Dapat melihat semua data BA Pemeriksaan Sarana Pengisian
- **Non-Admin**: Hanya dapat melihat data BA dari kapal yang mereka miliki

#### Method yang Dimodifikasi:
- `getData()`: List BA Pemeriksaan Sarana Pengisian dengan filtering
- `getKapalData()`: Data kapal dengan filtering

### 5. BA Penerimaan BBM Controller (`/api/v1/ba-penerimaan-bbm`)

#### Filtering Logic:
- **Admin (conf_group_id = 1)**: Dapat melihat semua data BA Penerimaan BBM
- **Non-Admin**: Hanya dapat melihat data BA dari kapal yang mereka miliki

#### Method yang Dimodifikasi:
- `getData()`: List BA Penerimaan BBM dengan filtering
- `getKapalData()`: Data kapal dengan filtering

## Database Schema

### Tabel `sys_user_kapal`
```sql
CREATE TABLE `sys_user_kapal` (
  `sys_user_kapal_id` int(11) NOT NULL,
  `conf_user_id` int(11) NOT NULL,
  `m_kapal_id` int(11) NOT NULL
);
```

### Relasi User-Kapal
- `conf_user_id`: ID user dari tabel `conf_user`
- `m_kapal_id`: ID kapal dari tabel `m_kapal`

## Seeder Data

### SysUserKapalSeeder
Seeder telah dibuat untuk menghubungkan user dengan kapal berdasarkan data dari `sigotik.sql`:

```php
$userKapalRelations = [
    [
        'sys_user_kapal_id' => 141,
        'conf_user_id' => 16, // orca01
        'm_kapal_id' => 1 // Akar Bahar 01
    ],
    [
        'sys_user_kapal_id' => 142,
        'conf_user_id' => 17, // orca02
        'm_kapal_id' => 2 // Baracuda 1
    ],
    // ... dan seterusnya
];
```

## Testing

### Test Cases:
1. **Admin User**: Dapat melihat semua kapal dan BBM
2. **Non-Admin User**: Hanya dapat melihat kapal dan BBM yang terkait
3. **User tanpa kapal**: Mendapat response kosong
4. **Search functionality**: Tetap berfungsi dengan filtering role

### API Endpoints:
- `GET /api/v1/kapals` - List kapal dengan filtering
- `GET /api/v1/kapals/{id}` - Detail kapal dengan filtering
- `GET /api/v1/bbm` - List BBM dengan filtering
- `GET /api/v1/ba-sebelum-pengisian/data` - List BA Sebelum Pengisian dengan filtering
- `GET /api/v1/ba-sebelum-pengisian/kapal-data` - Data kapal dengan filtering
- `GET /api/v1/ba-pemeriksaan-sarana-pengisian/data` - List BA Pemeriksaan Sarana Pengisian dengan filtering
- `GET /api/v1/ba-pemeriksaan-sarana-pengisian/kapal-data` - Data kapal dengan filtering
- `GET /api/v1/ba-penerimaan-bbm/data` - List BA Penerimaan BBM dengan filtering
- `GET /api/v1/ba-penerimaan-bbm/kapal-data` - Data kapal dengan filtering

## Security Benefits

1. **Data Isolation**: User hanya dapat mengakses data yang relevan
2. **Role-based Access**: Admin memiliki akses penuh, user terbatas
3. **Consistent Filtering**: Filtering diterapkan di semua endpoint terkait
4. **Performance**: Query dioptimalkan dengan filtering yang tepat

## Notes

- Filtering diterapkan di level controller, bukan di middleware
- User authentication tetap menggunakan `auth:sanctum` middleware
- Response format tetap konsisten dengan API yang ada
- Pagination tetap berfungsi dengan filtering role
