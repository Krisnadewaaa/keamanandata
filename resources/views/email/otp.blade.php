<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Login ReuseMart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #198754;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 30px;
            border: 1px solid #dee2e6;
            border-top: none;
        }
        .otp-code {
            background-color: #fff;
            border: 2px dashed #198754;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            border-radius: 5px;
        }
        .otp-number {
            font-size: 36px;
            font-weight: bold;
            color: #198754;
            letter-spacing: 10px;
        }
        .footer {
            background-color: #343a40;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            border-radius: 0 0 5px 5px;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 10px;
            margin: 15px 0;
            border-radius: 5px;
        }
        .button {
            display: inline-block;
            background-color: #198754;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">ReuseMart</h1>
        <p style="margin: 5px 0 0 0;">Kode OTP Login</p>
    </div>
    
    <div class="content">
        <p>Halo{{ $name ? ' ' . $name : '' }},</p>
        
        <p>Anda telah meminta kode OTP untuk login ke akun ReuseMart Anda. Berikut adalah kode verifikasi Anda:</p>
        
        <div class="otp-code">
            <div class="otp-number">{{ $otp }}</div>
            <p style="margin: 10px 0 0 0; color: #666;">Kode OTP Anda</p>
        </div>
        
        <div class="warning">
            <strong>⚠️ Penting:</strong>
            <ul style="margin: 5px 0;">
                <li>Kode ini berlaku selama <strong>5 menit</strong></li>
                <li>Jangan bagikan kode ini kepada siapapun</li>
                <li>ReuseMart tidak akan pernah meminta kode OTP Anda</li>
            </ul>
        </div>
        
        <p>Jika Anda tidak meminta kode ini, abaikan email ini atau hubungi customer service kami.</p>
        
        <p style="margin-top: 30px;">
            Terima kasih,<br>
            <strong>Tim ReuseMart</strong>
        </p>
    </div>
    
    <div class="footer">
        <p style="margin: 0;">© {{ date('Y') }} ReuseMart. All rights reserved.</p>
        <p style="margin: 5px 0 0 0;">Jl. Green Eco Park No. 456, Yogyakarta</p>
    </div>
</body>
</html>