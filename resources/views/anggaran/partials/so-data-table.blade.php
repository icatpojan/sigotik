@if($data->count() > 0)
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm mb-4">
    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input type="checkbox" id="check_alls" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                <label for="check_alls" class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Semua Data SO</label>
            </div>
            <span class="text-xs text-gray-500 dark:text-gray-400">Total: {{ $data->count() }} item</span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-12"></th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nomor Surat</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Transportasi</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nomor SO</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nomor DO</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Volume</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-12">Harga</th>

                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">No Invoice</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($data as $item)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <input type="checkbox" class="custom-control-input w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" id="check_{{ $item->bbm_transdetail_id }}" value="{{ $item->bbm_transdetail_id }}">
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900 dark:text-white max-w-xs truncate" title="{{ $item->nomor_surat }}">{{ $item->nomor_surat }}</div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">{{ $item->transportasi }}</span>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-blue-600 dark:text-blue-400">{{ $item->no_so }}</div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-white">{{ $item->no_do }}</div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-right">
                        <div class="text-sm font-medium text-gray-900 dark:text-white" id="volume_{{ $item->bbm_transdetail_id }}">{{ number_format($item->volume_isi, 0, ',', '.') }} L</div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <input type="text" class="form-control x w-48 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" id="detail_harga_{{ $item->bbm_transdetail_id }}" value="{{ number_format($item->harga_total, 0, ',', '.') }}" placeholder="Masukkan harga">
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-white">{{ $item->no_invoice }}</div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.x').autoNumeric('init', {
            aSep: ','
            , aDec: '.'
            , mDec: '0'
        });
        $('.y').autoNumeric('init', {
            aSep: '.'
            , aDec: ','
            , mDec: '0'
        });
        $('#check_alls').on('click', function() {
            if (this.checked) {
                $('.custom-control-input').each(function() {
                    this.checked = true;
                });
            } else {
                $('.custom-control-input').each(function() {
                    this.checked = false;
                });
            }
            // Trigger calculation when select all is clicked
            calculateTotalHarga();
        });

        $('.custom-control-input').on('click', function() {
            if ($('.custom-control-input:checked').length == $('.custom-control-input').length) {
                $('#check_alls').prop('checked', true);
            } else {
                $('#check_alls').prop('checked', false);
            }
            // Trigger calculation when checkbox is clicked
            calculateTotalHarga();
        });
    });

    $(document).on('change', '.form-control', function() {
        calculateTotalHarga();
    });

    // Function to calculate total harga and harga per liter
    function calculateTotalHarga() {
        var totharga = 0;
        var totalQuantity = 0;

        // Calculate total from selected SO items
        $('.custom-control-input:checked').each(function() {
            var id = $(this).val();
            var hargaInput = $('#detail_harga_' + id);
            var hargas = hargaInput.val();
            var harga = hargas.replace(/,/g, '');
            if ($.isNumeric(harga) && parseFloat(harga) > 0) {
                totharga += parseFloat(harga);
            }
        });

        // Calculate total quantity from selected SO items
        $('.custom-control-input:checked').each(function() {
            var id = $(this).val();
            var volume = $('#volume_' + id).text().replace(/[^\d]/g, '');
            if ($.isNumeric(volume)) {
                totalQuantity += parseFloat(volume);
            }
        });

        // Update total harga
        $('#real_harga').val(totharga);
        var formattedHarga = addCommas(totharga);
        $('#harga').val(formattedHarga);

        // Update total quantity
        $('#real_quantity').val(totalQuantity);
        var formattedQuantity = addCommas(totalQuantity);
        $('#quantity').val(formattedQuantity);

        // Calculate harga per liter
        var hargaperliter = 0;
        if (totalQuantity > 0 && totharga > 0) {
            hargaperliter = totharga / totalQuantity;
        }

        $('#real_hargaperliter').val(hargaperliter);
        var formattedHargaPerLiter = addCommas(hargaperliter);
        $('#hargaperliter').val(formattedHargaPerLiter);
    }

</script>
@else
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm mb-4">
    <div class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
        <div class="flex flex-col items-center">
            <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="text-sm">Tidak ada data SO</span>
        </div>
    </div>
</div>
@endif
