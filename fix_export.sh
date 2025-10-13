#!/bin/bash

# Fix berita-acara-pembayaran.blade.php
sed -i '' 's/exportReport('\''pdf'\'', '\''berita-acara-pembayaran'\'', {/\/\/ Create form and submit via POST\
            const form = document.createElement('\''form'\'');\
            form.method = '\''POST'\'';\
            form.action = '\''{{ route("laporan-anggaran.export.pdf", "berita-acara-pembayaran") }}'\'';\
            form.target = '\''_blank'\'';\
            \
            const csrfToken = document.createElement('\''input'\'');\
            csrfToken.type = '\''hidden'\'';\
            csrfToken.name = '\''_token'\'';\
            csrfToken.value = '\''{{ csrf_token() }}'\'';\
            form.appendChild(csrfToken);\
            \
            const tglAwalInput = document.createElement('\''input'\'');\
            tglAwalInput.type = '\''hidden'\'';\
            tglAwalInput.name = '\''tgl_awal'\'';\
            tglAwalInput.value = tglAwal;\
            form.appendChild(tglAwalInput);\
            \
            const tglAkhirInput = document.createElement('\''input'\'');\
            tglAkhirInput.type = '\''hidden'\'';\
            tglAkhirInput.name = '\''tgl_akhir'\'';\
            tglAkhirInput.value = tglAkhir;\
            form.appendChild(tglAkhirInput);\
            \
            const uptCodeInput = document.createElement('\''input'\'');\
            uptCodeInput.type = '\''hidden'\'';\
            uptCodeInput.name = '\''upt_code'\'';\
            uptCodeInput.value = uptCode;\
            form.appendChild(uptCodeInput);\
            \
            document.body.appendChild(form);\
            form.submit();\
            document.body.removeChild(form);/g' resources/views/laporan/berita-acara-pembayaran.blade.php

echo "Fixed berita-acara-pembayaran.blade.php"
