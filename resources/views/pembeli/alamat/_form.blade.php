<form action="{{ isset($alamat) ? route('pembeli.alamat.update', $alamat->ID_ALAMAT) : route('pembeli.alamat.store') }}" method="POST">
    @csrf
    @if(isset($alamat))
        @method('PUT')
    @endif

    <div class="mb-3">
        <label>Provinsi</label>
        <select id="provinsi" name="PROVINSI" class="form-control" required>
            <option value="">-- Pilih Provinsi --</option>
            <!-- Provinsi options akan ditambahkan secara dinamis -->
        </select>
    </div>

    <div class="mb-3">
        <label>Kota/Kabupaten</label>
        <select id="kota" name="KOTA" class="form-control" required>
            <option value="">-- Pilih Kota/Kabupaten --</option>
            <!-- Kota/Kabupaten options akan ditambahkan setelah memilih provinsi -->
        </select>
    </div>

    <div class="mb-3">
        <label>Alamat Lengkap</label>
        <input type="text" name="ALAMAT_LENGKAP" value="{{ old('ALAMAT_LENGKAP', $alamat->ALAMAT_LENGKAP ?? '') }}" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-success">Simpan</button>
    <a href="{{ route('pembeli.alamat.index') }}" class="btn btn-secondary">Batal</a>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Data provinsi
        const dataProvinsi = [
            { id: "11", name: "ACEH" },
            { id: "12", name: "SUMATERA UTARA" },
            { id: "13", name: "SUMATERA BARAT" },
            { id: "14", name: "RIAU" },
            { id: "15", name: "JAMBI" },
            { id: "16", name: "SUMATERA SELATAN" },
            { id: "17", name: "BENGKULU" },
            { id: "18", name: "LAMPUNG" },
            { id: "19", name: "KEPULAUAN BANGKA BELITUNG" },
            { id: "21", name: "KEPULAUAN RIAU" },
            { id: "31", name: "DKI JAKARTA" },
            { id: "32", name: "JAWA BARAT" },
            { id: "33", name: "JAWA TENGAH" },
            { id: "34", name: "DI YOGYAKARTA" },
            { id: "35", name: "JAWA TIMUR" },
            { id: "36", name: "BANTEN" },
            { id: "51", name: "BALI" },
            { id: "52", name: "NUSA TENGGARA BARAT" },
            { id: "53", name: "NUSA TENGGARA TIMUR" },
            { id: "61", name: "KALIMANTAN BARAT" },
            { id: "62", name: "KALIMANTAN TENGAH" },
            { id: "63", name: "KALIMANTAN SELATAN" },
            { id: "64", name: "KALIMANTAN TIMUR" },
            { id: "65", name: "KALIMANTAN UTARA" },
            { id: "71", name: "SULAWESI UTARA" },
            { id: "72", name: "SULAWESI TENGAH" },
            { id: "73", name: "SULAWESI SELATAN" },
            { id: "74", name: "SULAWESI TENGGARA" },
            { id: "75", name: "GORONTALO" },
            { id: "76", name: "SULAWESI BARAT" },
            { id: "81", name: "MALUKU" },
            { id: "82", name: "MALUKU UTARA" },
            { id: "91", name: "PAPUA BARAT" },
            { id: "94", name: "PAPUA" }
        ];

        // Data kota berdasarkan provinsi
        const dataKota = {
        // Aceh (provinsi code: 11)
        "11": [
            { id: "1101", name: "KOTA BANDA ACEH" }, 
            { id: "1102", name: "KOTA SABANG" }, 
            { id: "1103", name: "KOTA LHOKSEUMAWE" }, 
            { id: "1104", name: "KOTA LANGSA" },
            { id: "1105", name: "KOTA SUBULUSSALAM" },
            { id: "1106", name: "KAB. ACEH SELATAN" },
            { id: "1107", name: "KAB. ACEH TENGGARA" },
            { id: "1108", name: "KAB. ACEH TIMUR" },
            { id: "1109", name: "KAB. ACEH TENGAH" },
            { id: "1110", name: "KAB. ACEH BARAT" },
            { id: "1111", name: "KAB. ACEH BESAR" },
            { id: "1112", name: "KAB. PIDIE" },
            { id: "1113", name: "KAB. ACEH UTARA" },
            { id: "1114", name: "KAB. SIMEULUE" },
            { id: "1115", name: "KAB. ACEH SINGKIL" },
            { id: "1116", name: "KAB. BIREUN" },
            { id: "1117", name: "KAB. ACEH BARAT DAYA" },
            { id: "1118", name: "KAB. GAYO LUES" },
            { id: "1119", name: "KAB. ACEH JAYA" },
            { id: "1120", name: "KAB. NAGAN RAYA" },
            { id: "1121", name: "KAB. ACEH TAMIANG" },
            { id: "1122", name: "KAB. BENER MERIAH" },
            { id: "1123", name: "KAB. PIDIE JAYA" }
        ],
        
        // Sumatera Utara (provinsi code: 12)
        "12": [
            { id: "1201", name: "KOTA MEDAN" },
            { id: "1202", name: "KOTA PEMATANG SIANTAR" },
            { id: "1203", name: "KOTA SIBOLGA" },
            { id: "1204", name: "KOTA TANJUNG BALAI" },
            { id: "1205", name: "KOTA BINJAI" },
            { id: "1206", name: "KOTA TEBING TINGGI" },
            { id: "1207", name: "KOTA PADANG SIDEMPUAN" },
            { id: "1208", name: "KOTA GUNUNG SITOLI" },
            { id: "1209", name: "KAB. SERDANG BEDAGAI" },
            { id: "1210", name: "KAB. SAMOSIR" },
            { id: "1211", name: "KAB. HUMBANG HASUNDUTAN" },
            { id: "1212", name: "KAB. PAKPAK BHARAT" },
            { id: "1213", name: "KAB. NIAS SELATAN" },
            { id: "1214", name: "KAB. MANDAILING NATAL" },
            { id: "1215", name: "KAB. TOBA SAMOSIR" },
            { id: "1216", name: "KAB. DAIRI" },
            { id: "1217", name: "KAB. LABUHAN BATU" },
            { id: "1218", name: "KAB. ASAHAN" },
            { id: "1219", name: "KAB. SIMALUNGUN" },
            { id: "1220", name: "KAB. DELI SERDANG" },
            { id: "1221", name: "KAB. KARO" },
            { id: "1222", name: "KAB. LANGKAT" },
            { id: "1223", name: "KAB. NIAS" },
            { id: "1224", name: "KAB. TAPANULI SELATAN" },
            { id: "1225", name: "KAB. TAPANULI UTARA" },
            { id: "1226", name: "KAB. TAPANULI TENGAH" },
            { id: "1227", name: "KAB. BATU BARA" },
            { id: "1228", name: "KAB. PADANG LAWAS UTARA" },
            { id: "1229", name: "KAB. PADANG LAWAS" },
            { id: "1230", name: "KAB. LABUHANBATU SELATAN" },
            { id: "1231", name: "KAB. LABUHANBATU UTARA" },
            { id: "1232", name: "KAB. NIAS UTARA" },
            { id: "1233", name: "KAB. NIAS BARAT" }
        ],
        
        // Sumatera Barat (provinsi code: 13)
        "13": [
            { id: "1301", name: "KOTA PADANG" },
            { id: "1302", name: "KOTA SOLOK" },
            { id: "1303", name: "KOTA SAWHLUNTO" },
            { id: "1304", name: "KOTA PADANG PANJANG" },
            { id: "1305", name: "KOTA BUKITTINGGI" },
            { id: "1306", name: "KOTA PAYAKUMBUH" },
            { id: "1307", name: "KOTA PARIAMAN" },
            { id: "1308", name: "KAB. PASAMAN BARAT" },
            { id: "1309", name: "KAB. SOLOK SELATAN" },
            { id: "1310", name: "KAB. DHARMASRAYA" },
            { id: "1311", name: "KAB. KEPULAUAN MENTAWAI" },
            { id: "1312", name: "KAB. PASAMAN" },
            { id: "1313", name: "KAB. LIMA PULUH KOTA" },
            { id: "1314", name: "KAB. AGAM" },
            { id: "1315", name: "KAB. PADANG PARIAMAN" },
            { id: "1316", name: "KAB. TANAH DATAR" },
            { id: "1317", name: "KAB. SIJUNJUNG" },
            { id: "1318", name: "KAB. SOLOK" },
            { id: "1319", name: "KAB. PESISIR SELATAN" }
        ],
        
        // Riau (provinsi code: 14)
        "14": [
            { id: "1401", name: "KOTA PEKAN BARU" },
            { id: "1402", name: "KOTA DUMAI" },
            { id: "1403", name: "KAB. KEPULAUAN MERANTI" },
            { id: "1404", name: "KAB. KUANTAN SINGINGI" },
            { id: "1405", name: "KAB. SIAK" },
            { id: "1406", name: "KAB. ROKAN HILIR" },
            { id: "1407", name: "KAB. ROKAN HULU" },
            { id: "1408", name: "KAB. PELALAWAN" },
            { id: "1409", name: "KAB. INDRAGIRI HILIR" },
            { id: "1410", name: "KAB. BENGKALIS" },
            { id: "1411", name: "KAB. INDRAGIRI HULU" },
            { id: "1412", name: "KAB. KAMPAR" }
        ],
        
        // Jambi (provinsi code: 15)
        "15": [
            { id: "1501", name: "KOTA JAMBI" },
            { id: "1502", name: "KOTA SUNGAI PENUH" },
            { id: "1503", name: "KAB. TEBO" },
            { id: "1504", name: "KAB. BUNGO" },
            { id: "1505", name: "KAB. TANJUNG JABUNG TIMUR" },
            { id: "1506", name: "KAB. TANJUNG JABUNG BARAT" },
            { id: "1507", name: "KAB. MUARO JAMBI" },
            { id: "1508", name: "KAB. BATANGHARI" },
            { id: "1509", name: "KAB. SAROLANGUN" },
            { id: "1510", name: "KAB. MERANGIN" },
            { id: "1511", name: "KAB. KERINCI" }
        ],
        
        // Sumatera Selatan (provinsi code: 16)
        "16": [
            { id: "1601", name: "KOTA PALEMBANG" },
            { id: "1602", name: "KOTA PAGAR ALAM" },
            { id: "1603", name: "KOTA LUBUK LINGGAU" },
            { id: "1604", name: "KOTA PRABUMULIH" },
            { id: "1605", name: "KAB. MUSI RAWAS UTARA" },
            { id: "1606", name: "KAB. PENUKAL ABAB LEMATANG ILIR" },
            { id: "1607", name: "KAB. EMPAT LAWANG" },
            { id: "1608", name: "KAB. OGAN ILIR" },
            { id: "1609", name: "KAB. OGAN KOMERING ULU SELATAN" },
            { id: "1610", name: "KAB. OGAN KOMERING ULU TIMUR" },
            { id: "1611", name: "KAB. BANYUASIN" },
            { id: "1612", name: "KAB. MUSI BANYUASIN" },
            { id: "1613", name: "KAB. MUSI RAWAS" },
            { id: "1614", name: "KAB. LAHAT" },
            { id: "1615", name: "KAB. MUARA ENIM" },
            { id: "1616", name: "KAB. OGAN KOMERING ILIR" },
            { id: "1617", name: "KAB. OGAN KOMERING ULU" }
        ],
        
        // Bengkulu (provinsi code: 17)
        "17": [
            { id: "1701", name: "KOTA BENGKULU" },
            { id: "1702", name: "KAB. BENGKULU TENGAH" },
            { id: "1703", name: "KAB. KEPAHIANG" },
            { id: "1704", name: "KAB. LEBONG" },
            { id: "1705", name: "KAB. MUKO MUKO" },
            { id: "1706", name: "KAB. SELUMA" },
            { id: "1707", name: "KAB. KAUR" },
            { id: "1708", name: "KAB. BENGKULU UTARA" },
            { id: "1709", name: "KAB. REJANG LEBONG" },
            { id: "1710", name: "KAB. BENGKULU SELATAN" }
        ],
        
        // Lampung (provinsi code: 18)
        "18": [
            { id: "1801", name: "KOTA BANDAR LAMPUNG" },
            { id: "1802", name: "KOTA METRO" },
            { id: "1803", name: "KAB. PESISIR BARAT" },
            { id: "1804", name: "KAB. TULANGBAWANG BARAT" },
            { id: "1805", name: "KAB. MESUJI" },
            { id: "1806", name: "KAB. PRINGSEWU" },
            { id: "1807", name: "KAB. PESAWARAN" },
            { id: "1808", name: "KAB. WAY KANAN" },
            { id: "1809", name: "KAB. LAMPUNG TIMUR" },
            { id: "1810", name: "KAB. TANGGAMUS" },
            { id: "1811", name: "KAB. TULANG BAWANG" },
            { id: "1812", name: "KAB. LAMPUNG BARAT" },
            { id: "1813", name: "KAB. LAMPUNG UTARA" },
            { id: "1814", name: "KAB. LAMPUNG TENGAH" },
            { id: "1815", name: "KAB. LAMPUNG SELATAN" }
        ],
        
        // Kepulauan Bangka Belitung (provinsi code: 19)
        "19": [
            { id: "1901", name: "KOTA PANGKAL PINANG" },
            { id: "1902", name: "KAB. BELITUNG TIMUR" },
            { id: "1903", name: "KAB. BANGKA BARAT" },
            { id: "1904", name: "KAB. BANGKA TENGAH" },
            { id: "1905", name: "KAB. BANGKA SELATAN" },
            { id: "1906", name: "KAB. BELITUNG" },
            { id: "1907", name: "KAB. BANGKA" }
        ],
        
        // Kepulauan Riau (provinsi code: 21)
        "21": [
            { id: "2101", name: "KOTA BATAM" },
            { id: "2102", name: "KOTA TANJUNG PINANG" },
            { id: "2103", name: "KAB. KEPULAUAN ANAMBAS" },
            { id: "2104", name: "KAB. LINGGA" },
            { id: "2105", name: "KAB. NATUNA" },
            { id: "2106", name: "KAB. KARIMUN" },
            { id: "2107", name: "KAB. BINTAN" }
        ],
        
        // DKI Jakarta (provinsi code: 31)
        "31": [
            { id: "3101", name: "KOTA JAKARTA TIMUR" },
            { id: "3102", name: "KOTA JAKARTA SELATAN" },
            { id: "3103", name: "KOTA JAKARTA BARAT" },
            { id: "3104", name: "KOTA JAKARTA UTARA" },
            { id: "3105", name: "KOTA JAKARTA PUSAT" },
            { id: "3106", name: "KAB. KEPULAUAN SERIBU" }
        ],
        
        // Jawa Barat (provinsi code: 32)
        "32": [
            { id: "3201", name: "KOTA BANDUNG" },
            { id: "3202", name: "KOTA BANJAR" },
            { id: "3203", name: "KOTA TASIKMALAYA" },
            { id: "3204", name: "KOTA CIMAHI" },
            { id: "3205", name: "KOTA DEPOK" },
            { id: "3206", name: "KOTA BEKASI" },
            { id: "3207", name: "KOTA CIREBON" },
            { id: "3208", name: "KOTA SUKABUMI" },
            { id: "3209", name: "KOTA BOGOR" },
            { id: "3210", name: "KAB. PANGANDARAN" },
            { id: "3211", name: "KAB. BANDUNG BARAT" },
            { id: "3212", name: "KAB. BEKASI" },
            { id: "3213", name: "KAB. KARAWANG" },
            { id: "3214", name: "KAB. PURWAKARTA" },
            { id: "3215", name: "KAB. SUBANG" },
            { id: "3216", name: "KAB. INDRAMAYU" },
            { id: "3217", name: "KAB. SUMEDANG" },
            { id: "3218", name: "KAB. MAJALENGKA" },
            { id: "3219", name: "KAB. CIREBON" },
            { id: "3220", name: "KAB. KUNINGAN" },
            { id: "3221", name: "KAB. CIAMIS" },
            { id: "3222", name: "KAB. TASIKMALAYA" },
            { id: "3223", name: "KAB. GARUT" },
            { id: "3224", name: "KAB. BANDUNG" },
            { id: "3225", name: "KAB. CIANJUR" },
            { id: "3226", name: "KAB. SUKABUMI" },
            { id: "3227", name: "KAB. BOGOR" }
        ],
        
        // Jawa Tengah (provinsi code: 33)
        "33": [
            { id: "3301", name: "KOTA SEMARANG" },
            { id: "3302", name: "KOTA TEGAL" },
            { id: "3303", name: "KOTA PEKALONGAN" },
            { id: "3304", name: "KOTA SALATIGA" },
            { id: "3305", name: "KOTA SURAKARTA" },
            { id: "3306", name: "KOTA MAGELANG" },
            { id: "3307", name: "KAB. BREBES" },
            { id: "3308", name: "KAB. TEGAL" },
            { id: "3309", name: "KAB. PEMALANG" },
            { id: "3310", name: "KAB. PEKALONGAN" },
            { id: "3311", name: "KAB. BATANG" },
            { id: "3312", name: "KAB. KENDAL" },
            { id: "3313", name: "KAB. TEMANGGUNG" },
            { id: "3314", name: "KAB. SEMARANG" },
            { id: "3315", name: "KAB. DEMAK" },
            { id: "3316", name: "KAB. JEPARA" },
            { id: "3317", name: "KAB. KUDUS" },
            { id: "3318", name: "KAB. PATI" },
            { id: "3319", name: "KAB. REMBANG" },
            { id: "3320", name: "KAB. BLORA" },
            { id: "3321", name: "KAB. GROBOGAN" },
            { id: "3322", name: "KAB. SRAGEN" },
            { id: "3323", name: "KAB. KARANGANYAR" },
            { id: "3324", name: "KAB. WONOGIRI" },
            { id: "3325", name: "KAB. SUKOHARJO" },
            { id: "3326", name: "KAB. KLATEN" },
            { id: "3327", name: "KAB. BOYOLALI" },
            { id: "3328", name: "KAB. MAGELANG" },
            { id: "3329", name: "KAB. WONOSOBO" },
            { id: "3330", name: "KAB. PURWOREJO" },
            { id: "3331", name: "KAB. KEBUMEN" },
            { id: "3332", name: "KAB. BANJARNEGARA" },
            { id: "3333", name: "KAB. PURBALINGGA" },
            { id: "3334", name: "KAB. BANYUMAS" },
            { id: "3335", name: "KAB. CILACAP" }
        ],
        
        // DI Yogyakarta (provinsi code: 34)
        "34": [
            { id: "3401", name: "KOTA YOGYAKARTA" },
            { id: "3402", name: "KAB. SLEMAN" },
            { id: "3403", name: "KAB. GUNUNG KIDUL" },
            { id: "3404", name: "KAB. BANTUL" },
            { id: "3405", name: "KAB. KULON PROGO" }
        ],
        
        // Jawa Timur (provinsi code: 35)
        "35": [
            { id: "3501", name: "KOTA SURABAYA" },
            { id: "3502", name: "KOTA BATU" },
            { id: "3503", name: "KOTA MADIUN" },
            { id: "3504", name: "KOTA MOJOKERTO" },
            { id: "3505", name: "KOTA PASURUAN" },
            { id: "3506", name: "KOTA PROBOLINGGO" },
            { id: "3507", name: "KOTA MALANG" },
            { id: "3508", name: "KOTA BLITAR" },
            { id: "3509", name: "KOTA KEDIRI" },
            { id: "3510", name: "KAB. SUMENEP" },
            { id: "3511", name: "KAB. PAMEKASAN" },
            { id: "3512", name: "KAB. SAMPANG" },
            { id: "3513", name: "KAB. BANGKALAN" },
            { id: "3514", name: "KAB. GRESIK" },
            { id: "3515", name: "KAB. LAMONGAN" },
            { id: "3516", name: "KAB. TUBAN" },
            { id: "3517", name: "KAB. BOJONEGORO" },
            { id: "3518", name: "KAB. NGAWI" },
            { id: "3519", name: "KAB. MAGETAN" },
            { id: "3520", name: "KAB. MADIUN" },
            { id: "3521", name: "KAB. NGANJUK" },
            { id: "3522", name: "KAB. JOMBANG" },
            { id: "3523", name: "KAB. MOJOKERTO" },
            { id: "3524", name: "KAB. SIDOARJO" },
            { id: "3525", name: "KAB. PASURUAN" },
            { id: "3526", name: "KAB. PROBOLINGGO" },
            { id: "3527", name: "KAB. SITUBONDO" },
            { id: "3528", name: "KAB. BONDOWOSO" },
            { id: "3529", name: "KAB. BANYUWANGI" },
            { id: "3530", name: "KAB. JEMBER" },
            { id: "3531", name: "KAB. LUMAJANG" },
            { id: "3532", name: "KAB. MALANG" },
            { id: "3533", name: "KAB. KEDIRI" },
            { id: "3534", name: "KAB. BLITAR" },
            { id: "3535", name: "KAB. TULUNGAGUNG" },
            { id: "3536", name: "KAB. TRENGGALEK" },
            { id: "3537", name: "KAB. PONOROGO" },
            { id: "3538", name: "KAB. PACITAN" }
        ],
        
        // Banten (provinsi code: 36)
        "36": [
            { id: "3601", name: "KOTA SERANG" },
            { id: "3602", name: "KOTA CILEGON" },
            { id: "3603", name: "KOTA TANGERANG" },
            { id: "3604", name: "KOTA TANGERANG SELATAN" },
            { id: "3605", name: "KAB. SERANG" },
            { id: "3606", name: "KAB. TANGERANG" },
            { id: "3607", name: "KAB. LEBAK" },
            { id: "3608", name: "KAB. PANDEGLANG" }
        ],
        
        // Bali (provinsi code: 51)
        "51": [
            { id: "5101", name: "KOTA DENPASAR" },
            { id: "5102", name: "KAB. BULELENG" },
            { id: "5103", name: "KAB. KARANGASEM" },
            { id: "5104", name: "KAB. BANGLI" },
            { id: "5105", name: "KAB. KLUNGKUNG" },
            { id: "5106", name: "KAB. GIANYAR" },
            { id: "5107", name: "KAB. BADUNG" },
            { id: "5108", name: "KAB. TABANAN" },
            { id: "5109", name: "KAB. JEMBRANA" }
        ],
        
        // Nusa Tenggara Barat (provinsi code: 52)
        "52": [
            { id: "5201", name: "KOTA MATARAM" },
            { id: "5202", name: "KOTA BIMA" },
            { id: "5203", name: "KAB. LOMBOK UTARA" },
            { id: "5204", name: "KAB. SUMBAWA BARAT" },
            { id: "5205", name: "KAB. BIMA" },
            { id: "5206", name: "KAB. DOMPU" },
            { id: "5207", name: "KAB. SUMBAWA" },
            { id: "5208", name: "KAB. LOMBOK TIMUR" },
            { id: "5209", name: "KAB. LOMBOK TENGAH" },
            { id: "5210", name: "KAB. LOMBOK BARAT" }
        ],
        
        // Nusa Tenggara Timur (provinsi code: 53)
        "53": [
            { id: "5301", name: "KOTA KUPANG" },
            { id: "5302", name: "KAB. MALAKA" },
            { id: "5303", name: "KAB. SABU RAIJUA" },
            { id: "5304", name: "KAB. MANGGARAI TIMUR" },
            { id: "5305", name: "KAB. SUMBA BARAT DAYA" },
            { id: "5306", name: "KAB. SUMBA TENGAH" },
            { id: "5307", name: "KAB. NAGEKEO" },
            { id: "5308", name: "KAB. MANGGARAI BARAT" },
            { id: "5309", name: "KAB. ROTE NDAO" },
            { id: "5310", name: "KAB. LEMBATA" },
            { id: "5311", name: "KAB. SUMBA BARAT" },
            { id: "5312", name: "KAB. SUMBA TIMUR" },
            { id: "5313", name: "KAB. MANGGARAI" },
            { id: "5314", name: "KAB. NGADA" },
            { id: "5315", name: "KAB. ENDE" },
            { id: "5316", name: "KAB. SIKKA" },
            { id: "5317", name: "KAB. FLORES TIMUR" },
            { id: "5318", name: "KAB. ALOR" },
            { id: "5319", name: "KAB. BELU" },
            { id: "5320", name: "KAB. TIMOR TENGAH UTARA" },
            { id: "5321", name: "KAB. TIMOR TENGAH SELATAN" },
            { id: "5322", name: "KAB. KUPANG" }
        ],
        
        // Kalimantan Barat (provinsi code: 61)
        "61": [
            { id: "6101", name: "KOTA PONTIANAK" },
            { id: "6102", name: "KOTA SINGKAWANG" },
            { id: "6103", name: "KAB. KUBU RAYA" },
            { id: "6104", name: "KAB. KAYONG UTARA" },
            { id: "6105", name: "KAB. SEKADAU" },
            { id: "6106", name: "KAB. MELAWI" },
            { id: "6107", name: "KAB. LANDAK" },
            { id: "6108", name: "KAB. BENGKAYANG" },
            { id: "6109", name: "KAB. KAPUAS HULU" },
            { id: "6110", name: "KAB. SINTANG" },
            { id: "6111", name: "KAB. KETAPANG" },
            { id: "6112", name: "KAB. SANGGAU" },
            { id: "6113", name: "KAB. MEMPAWAH" },
            { id: "6114", name: "KAB. SAMBAS" }
        ],

        "62": [
            { id: 6201, name: "KOTAWARINGIN BARAT" },
            { id: 6202, name: "KOTAWARINGIN TIMUR" },
            { id: 6203, name: "KAPUAS" },
            { id: 6204, name: "BARITO SELATAN" },
            { id: 6205, name: "BARITO UTARA" },
            { id: 6206, name: "SUKAMARA" },
            { id: 6207, name: "KATINGAN" },
            { id: 6208, name: "PULANG PISAU" },
            { id: 6209, name: "MURUNG RAYA" },
            { id: 6210, name: "SERUYAN" },
            { id: 6211, name: "LAMANDAU" },
            { id: 6212, name: "GUNUNG MAS" },
            { id: 6213, name: "KATINGAN" },
            { id: 6214, name: "PULANG PISAU" }
        ],
        "63": [
            { id: 6301, name: "BANJAR" },
            { id: 6302, name: "TANAH LAUT" },
            { id: 6303, name: "TANAH BUMBU" },
            { id: 6304, name: "BALANGAN" },
            { id: 6305, name: "HULU SUNGAI SELATAN" },
            { id: 6306, name: "HULU SUNGAI UTARA" },
            { id: 6307, name: "TABALONG" },
            { id: 6308, name: "TABALONG" }
        ],
        "64": [
            { id: 6401, name: "BALIKPAPAN" },
            { id: 6402, name: "SAMARINDA" },
            { id: 6403, name: "BONTANG" },
            { id: 6404, name: "PENAJAM PASER UTARA" },
            { id: 6405, name: "KUTAI KARTANEGARA" }
        ],
        "65": [
            { id: 6501, name: "MALINAU" },
            { id: 6502, name: "TARAKAN" },
            { id: 6503, name: "NUNUKAN" },
            { id: 6504, name: "BULUNGAN" },
            { id: 6505, name: "KOTABARU" }
        ],
        "71": [
            { id: 7101, name: "MINAHASA" },
            { id: 7102, name: "SULAWESI UTARA" },
            { id: 7103, name: "SANGIHE" },
            { id: 7104, name: "BOLAANG MONGONDOW" }
        ],
        "72": [
            { id: 7201, name: "POSO" },
            { id: 7202, name: "PARIGI MOUTONG" },
            { id: 7203, name: "TOJO UNA-UNA" },
            { id: 7204, name: "SIGI" }
        ],
        "73": [
            { id: 7301, name: "MAKASSAR" },
            { id: 7302, name: "GOWA" },
            { id: 7303, name: "SINJAI" },
            { id: 7304, name: "BULUKUMBA" }
        ],
        "74": [
            { id: 7401, name: "BOMBANA" },
            { id: 7402, name: "KONAWE" },
            { id: 7403, name: "MUNA" },
            { id: 7404, name: "BUTON" }
        ],
        "75": [
            { id: 7501, name: "GORONTALO" },
            { id: 7502, name: "BOALEMO" }
        ],
        "76": [
            { id: 7601, name: "MAMASA" },
            { id: 7602, name: "POLMAN" }
        ],
        "81": [
            { id: 8101, name: "MALUKU" },
            { id: 8102, name: "SERAM" },
            { id: 8103, name: "AMBOINA" }
        ],
        "82": [
            { id: 8201, name: "TIDORE" },
            { id: 8202, name: "HALMAHERA" }
        ],
        "91": [
            { id: 9101, name: "MANOKWARI" },
            { id: 9102, name: "SORONG" }
        ],
        "94": [
            { id: 9401, name: "JAYAPURA" },
            { id: 9402, name: "MERAUKE" },
            { id: 9403, name: "TIMIKA" }
        ]}

        const provinsiSelect = document.getElementById('provinsi');
        const kotaSelect = document.getElementById('kota');
        
        // Menyimpan data ID dan nama untuk digunakan saat pemilihan
        let selectedProvinsiName = '';
        let selectedKotaName = '';

        // Fungsi untuk mengisi dropdown provinsi
        dataProvinsi.forEach(provinsi => {
            const option = document.createElement('option');
            option.value = provinsi.name; // Menggunakan nama sebagai value, bukan ID
            option.textContent = provinsi.name;
            option.dataset.id = provinsi.id; // Menyimpan ID sebagai data attribute untuk referensi
            provinsiSelect.appendChild(option);
        });

        // Fungsi untuk mengupdate dropdown kota berdasarkan provinsi
        provinsiSelect.addEventListener('change', (event) => {
            selectedProvinsiName = event.target.value;
            const selectedProvinsiId = event.target.options[event.target.selectedIndex].dataset.id;
            
            kotaSelect.innerHTML = '<option value="">-- Pilih Kota/Kabupaten --</option>'; // Reset opsi kota
            
            if (selectedProvinsiId && dataKota[selectedProvinsiId]) {
                dataKota[selectedProvinsiId].forEach(kota => {
                    const option = document.createElement('option');
                    option.value = kota.name; // Menggunakan nama sebagai value, bukan ID
                    option.textContent = kota.name;
                    option.dataset.id = kota.id; // Menyimpan ID sebagai data attribute untuk referensi
                    kotaSelect.appendChild(option);
                });
            }
        });
        
        // Menangkap perubahan pada pilihan kota
        kotaSelect.addEventListener('change', (event) => {
            selectedKotaName = event.target.value;
        });
        
        // Jika ada data alamat yang sudah tersimpan sebelumnya, set nilai dropdown
        if ({{ isset($alamat) ? 'true' : 'false' }}) {
            // Menggunakan timeout untuk memastikan provinsi terisi terlebih dahulu
            setTimeout(() => {
                // Set provinsi
                const savedProvinsi = "{{ $alamat->PROVINSI ?? '' }}";
                if (savedProvinsi) {
                    for (let i = 0; i < provinsiSelect.options.length; i++) {
                        if (provinsiSelect.options[i].value === savedProvinsi) {
                            provinsiSelect.selectedIndex = i;
                            // Trigger change event untuk mengisi kota
                            provinsiSelect.dispatchEvent(new Event('change'));
                            break;
                        }
                    }
                    
                    // Set kota setelah provinsi terisi
                    setTimeout(() => {
                        const savedKota = "{{ $alamat->KOTA ?? '' }}";
                        if (savedKota) {
                            for (let i = 0; i < kotaSelect.options.length; i++) {
                                if (kotaSelect.options[i].value === savedKota) {
                                    kotaSelect.selectedIndex = i;
                                    break;
                                }
                            }
                        }
                    }, 100);
                }
            }, 100);
        }
    });
</script>