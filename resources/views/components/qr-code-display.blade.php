@props(['ksoItem', 'size' => 300, 'showLink' => true])

<div class="flex flex-col items-center gap-4 p-6 bg-white rounded-lg shadow-lg border border-gray-200">
    <!-- QR Code Image -->
    <div class="p-4 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
        <img 
            src="{{ \App\Helpers\QrCodeHelper::generateKsoItemQrCode($ksoItem->id, $size) }}" 
            alt="QR Code - {{ $ksoItem->nama_alat }}"
            title="QR Code - {{ $ksoItem->nama_alat }}"
            style="width: {{ $size }}px; height: {{ $size }}px;"
            class="qr-code-image"
        />
    </div>

    <!-- Info -->
    <div class="text-center">
        <h3 class="font-bold text-gray-900 mb-1">{{ $ksoItem->nama_alat }}</h3>
        <p class="text-sm text-gray-600">ID: {{ $ksoItem->id }}</p>
    </div>

    <!-- Access Link -->
    @if($showLink)
    <div class="w-full">
        <a 
            href="{{ route('qr.password', $ksoItem->id) }}"
            target="_blank"
            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold text-sm"
        >
            <i class="fas fa-external-link-alt"></i>
            Akses QR Code
        </a>
    </div>
    @endif

    <!-- Download Button -->
    <button 
        onclick="downloadQrCode({{ $ksoItem->id }}, '{{ $ksoItem->nama_alat }}')"
        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold text-sm"
    >
        <i class="fas fa-download"></i>
        Download QR Code
    </button>

    <!-- Copy URL Button -->
    <button 
        onclick="copyToClipboard('{{ \App\Helpers\QrCodeHelper::getFullQrAccessUrl($ksoItem->id) }}')"
        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-semibold text-sm"
    >
        <i class="fas fa-copy"></i>
        Salin URL
    </button>
</div>

<script>
function downloadQrCode(ksoItemId, itemName) {
    const url = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode(\App\Helpers\QrCodeHelper::getFullQrAccessUrl('') . ksoItemId) }}`;
    const link = document.createElement('a');
    link.href = url;
    link.download = `QR-KSO-${itemName}-${ksoItemId}.png`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('URL berhasil disalin ke clipboard!');
    }).catch(err => {
        console.error('Gagal menyalin:', err);
        alert('Gagal menyalin URL');
    });
}
</script>
