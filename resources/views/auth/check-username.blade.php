@extends('layouts.auth')
@section('title', 'Cek Akun')

@section('content')
    <x-organisms.auth-card 
        title="Selamat Datang," 
        subtitle="Masukkan Username TikTok"
    >
        <form action="{{ route('login.verify-username') }}" method="POST">
            @csrf
            <div class="form-group" id="input-username-tiktok">
                <x-atoms.label value="Username Tiktok" />
                <x-atoms.input type="text" name="username" placeholder="@mazzprifarm" required pattern="^@.*" title="Username harus diawali dengan tanda @" />
            </div>
            
            <x-atoms.button variant="primary" type="submit" class="btn-block">
                Lanjutkan
            </x-atoms.button>
        </form>

        <x-slot name="footer">
            Langkah untuk konfirmasi akun terdaftar
        </x-slot>
    </x-organisms.auth-card>
@endsection

@section('scripts')
    <script>
        const tour = new Shepherd.Tour({
            useModalOverlay: true,
            defaultStepOptions: {
                classes: 'shadow-md bg-purple-dark',
                scrollTo: true,
                cancelIcon: {
                    enabled: true
                }
            }
        });

        tour.addStep({
            id: 'step-username',
            title: 'Informasi Username',
            text: 'Buka profil TikTok Anda. Username Anda diawali tanda <strong>"@"</strong>.<br><br>💡 <strong>Tips Cepat:</strong> Cukup <strong>ketuk (tap)</strong> username tersebut di profil TikTok Anda, maka akan otomatis tercopy. Setelah itu, paste (tempel) di kolom ini beserta tanda "@".',
            attachTo: {
                element: '#input-username-tiktok', 
                on: 'bottom' 
            },
            buttons: [
                {
                    text: 'Mengerti',
                    action: tour.complete,
                    classes: 'shepherd-button-primary'
                }
            ]
        });

        function startTour() {
            tour.start();
        }

        document.addEventListener('DOMContentLoaded', function() {
            startTour();
        });
    </script>
@endsection