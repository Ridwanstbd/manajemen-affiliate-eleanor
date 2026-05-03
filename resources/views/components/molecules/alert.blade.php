@php
    $alertType = null;
    $alertMessage = null;
    $alertTitle = 'Informasi';

    // Menangkap pesan dari Controller (Session Flash)
    if (session('success')) {
        $alertType = 'success';
        $alertMessage = session('success');
        $alertTitle = 'Berhasil!';
    } elseif (session('error')) {
        $alertType = 'error';
        $alertMessage = session('error');
        $alertTitle = 'Terjadi Kesalahan!';
    } elseif (session('warning')) {
        $alertType = 'warning';
        $alertMessage = session('warning');
        $alertTitle = 'Peringatan!';
    } elseif (session('info')) {
        $alertType = 'info';
        $alertMessage = session('info');
        $alertTitle = 'Informasi';
    } 
    elseif ($errors->any()) {
        $alertType = 'error';
        $alertMessage = implode('<br>', $errors->all()); 
        $alertTitle = 'Validasi Gagal!';
    }
@endphp

@if($alertType)
<div id="custom-alert-box" class="alert-box alert-{{ $alertType }}">
    <div class="alert-content">
        <h3 class="alert-title">{{ $alertTitle }}</h3>
        <p class="alert-text">{!! $alertMessage !!}</p>
    </div>
    <button class="alert-button alert-btn-{{ $alertType }}" onclick="closeAlert()">Mengerti</button>
</div>

<script>
    function closeAlert() {
        const alertBox = document.getElementById('custom-alert-box');
        
        if(alertBox) {
            alertBox.style.animation = 'popOutAlertFixed 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards';
            
            setTimeout(() => {
                alertBox.style.display = 'none';
            }, 300);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const alertBox = document.getElementById('custom-alert-box');
        if(alertBox) {
            setTimeout(() => {
                closeAlert();
            }, 3000);
        }
    });
</script>
@endif