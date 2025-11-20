<?php

namespace App\Helpers;

class QrCodeHelper
{
    /**
     * Generate QR Code URL menggunakan QR Server API
     * 
     * @param string $data - Data yang akan di-encode ke QR Code
     * @param int $size - Ukuran QR Code (default: 300)
     * @return string - URL QR Code
     */
    public static function generateQrCodeUrl($data, $size = 300)
    {
        $encodedData = urlencode($data);
        return "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data={$encodedData}";
    }

    /**
     * Generate QR Code URL untuk KSO Item
     * 
     * @param int|string $ksoItemIdOrUniqueId - ID atau unique_id dari KSO Item
     * @param int $size - Ukuran QR Code (default: 300)
     * @return string - URL QR Code
     */
    public static function generateKsoItemQrCode($ksoItemIdOrUniqueId, $size = 300)
    {
        // Jika parameter adalah numeric (ID), convert ke unique_id
        if (is_numeric($ksoItemIdOrUniqueId)) {
            $ksoItem = \App\Models\KsoItem::find($ksoItemIdOrUniqueId);
            $uniqueId = $ksoItem ? $ksoItem->unique_id : $ksoItemIdOrUniqueId;
        } else {
            $uniqueId = $ksoItemIdOrUniqueId;
        }

        $url = route('qr.verify', $uniqueId, false);
        $fullUrl = config('app.url') . $url;
        return self::generateQrCodeUrl($fullUrl, $size);
    }

    /**
     * Generate QR Code HTML img tag untuk KSO Item
     * 
     * @param int $ksoItemId - ID dari KSO Item
     * @param int $size - Ukuran QR Code (default: 300)
     * @param array $attributes - HTML attributes untuk img tag
     * @return string - HTML img tag
     */
    public static function generateKsoItemQrCodeHtml($ksoItemId, $size = 300, $attributes = [])
    {
        $qrUrl = self::generateKsoItemQrCode($ksoItemId, $size);

        $attrs = [
            'src' => $qrUrl,
            'alt' => "QR Code - KSO Item #{$ksoItemId}",
            'title' => "QR Code - KSO Item #{$ksoItemId}",
            'class' => 'qr-code-image',
            'style' => "width: {$size}px; height: {$size}px;"
        ];

        // Merge dengan custom attributes
        $attrs = array_merge($attrs, $attributes);

        // Build HTML
        $html = '<img';
        foreach ($attrs as $key => $value) {
            $html .= " {$key}=\"{$value}\"";
        }
        $html .= ' />';

        return $html;
    }

    /**
     * Generate QR Code dengan download link
     * 
     * @param int $ksoItemId - ID dari KSO Item
     * @param int $size - Ukuran QR Code
     * @return string - URL untuk download QR Code
     */
    public static function generateQrCodeDownloadUrl($ksoItemId, $size = 300)
    {
        $qrUrl = self::generateKsoItemQrCode($ksoItemId, $size);
        return $qrUrl . '&format=png';
    }

    /**
     * Get full QR Code access URL
     * 
     * @param int|string $ksoItemIdOrUniqueId - ID atau unique_id dari KSO Item
     * @return string - Full URL untuk akses QR
     */
    public static function getQrAccessUrl($ksoItemIdOrUniqueId)
    {
        // Jika parameter adalah numeric (ID), convert ke unique_id
        if (is_numeric($ksoItemIdOrUniqueId)) {
            $ksoItem = \App\Models\KsoItem::find($ksoItemIdOrUniqueId);
            $uniqueId = $ksoItem ? $ksoItem->unique_id : $ksoItemIdOrUniqueId;
        } else {
            $uniqueId = $ksoItemIdOrUniqueId;
        }

        return route('qr.verify', $uniqueId);
    }

    /**
     * Get full QR Code access URL dengan base URL
     * 
     * @param int|string $ksoItemIdOrUniqueId - ID atau unique_id dari KSO Item
     * @return string - Full URL dengan domain
     */
    public static function getFullQrAccessUrl($ksoItemIdOrUniqueId)
    {
        // Jika parameter adalah numeric (ID), convert ke unique_id
        if (is_numeric($ksoItemIdOrUniqueId)) {
            $ksoItem = \App\Models\KsoItem::find($ksoItemIdOrUniqueId);
            $uniqueId = $ksoItem ? $ksoItem->unique_id : $ksoItemIdOrUniqueId;
        } else {
            $uniqueId = $ksoItemIdOrUniqueId;
        }

        return config('app.url') . route('qr.verify', $uniqueId, false);
    }

    /**
     * Generate QR Code SVG menggunakan endroid/qr-code library
     * Fallback ke API jika library tidak tersedia
     * 
     * @param int $ksoItemId - ID dari KSO Item
     * @param int $size - Ukuran QR Code (default: 300)
     * @return string - SVG atau URL QR Code
     */
    public static function generateQrCodeSvg($ksoItemId, $size = 300)
    {
        $url = self::getFullQrAccessUrl($ksoItemId);

        // Try to use endroid/qr-code if available
        if (class_exists('Endroid\QrCode\QrCode')) {
            try {
                $qrCode = new \Endroid\QrCode\QrCode($url);
                $qrCode->setSize($size);
                return $qrCode->writeString();
            } catch (\Exception $e) {
                // Fallback to API
                return self::generateQrCodeUrl($url, $size);
            }
        }

        // Fallback to API
        return self::generateQrCodeUrl($url, $size);
    }

    /**
     * Generate QR Code Data URL (base64 encoded)
     * Untuk digunakan langsung di img tag
     * 
     * @param int $ksoItemId - ID dari KSO Item
     * @param int $size - Ukuran QR Code (default: 300)
     * @return string - Data URL
     */
    public static function generateQrCodeDataUrl($ksoItemId, $size = 300)
    {
        $url = self::getFullQrAccessUrl($ksoItemId);
        $qrImageUrl = self::generateQrCodeUrl($url, $size);

        // Fetch image from API and convert to data URL
        try {
            $imageData = @file_get_contents($qrImageUrl);
            if ($imageData) {
                return 'data:image/png;base64,' . base64_encode($imageData);
            }
        } catch (\Exception $e) {
            // Return API URL as fallback
        }

        return $qrImageUrl;
    }

    /**
     * Generate QR Code dengan custom text
     * 
     * @param string $text - Text untuk di-encode
     * @param int $size - Ukuran QR Code (default: 300)
     * @return string - URL QR Code
     */
    public static function generateQrCodeFromText($text, $size = 300)
    {
        return self::generateQrCodeUrl($text, $size);
    }
}
