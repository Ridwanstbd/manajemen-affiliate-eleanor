@extends('layouts.app')

@section('title', 'Pengajuan Sampel Saya')

@section('content')
<x-organisms.mobile-page-wrapper title="Pengajuan Sampel Saya" subtitle="Pantau proses pengiriman dan konfirmasi sampel Anda.">
    <x-molecules.glass-tabs>
        <x-molecules.glass-tab-item 
            :active="$currentTab === 'request-sample'" 
            href="{{ route('affiliator.sample-request.index', ['tab' => 'request-sample']) }}">
            Diajukan
        </x-molecules.glass-tab-item>
        
        <x-molecules.glass-tab-item 
            :active="$currentTab === 'shipped'" 
            href="{{ route('affiliator.sample-request.index', ['tab' => 'shipped']) }}">
            Selesai
        </x-molecules.glass-tab-item>
    </x-molecules.glass-tabs>

        <div class="tab-content" style="animation: fadeInUp 0.4s ease;">
            @if($currentTab === 'request-sample')
                @include('pages.affiliator.sample-request.all.index', get_defined_vars())
            @elseif($currentTab === 'shipped')
                @include('pages.affiliator.sample-request.shipped.index', get_defined_vars())
            @endif
        </div>
    
</x-organisms.mobile-page-wrapper>
@endsection

@push('scripts')
<style>
    .spin-animation { animation: spin 1s linear infinite; }
    @keyframes spin { 100% { transform: rotate(360deg); } }
</style>

<script>
    let nextPageUrl = '{{ $data->nextPageUrl() }}';
    let isLoading = false;
    let currentTab = '{{ $currentTab }}';

    $(window).scroll(function() {
        if($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
            if(nextPageUrl && !isLoading) {
                loadMoreData();
            }
        }
    });

    function loadMoreData() {
        isLoading = true;
        $('#loading-spinner').show();

        $.ajax({
            url: nextPageUrl,
            type: 'GET',
            data: { tab: currentTab }, 
            success: function(response) {
                if(response.html) {
                    $('#infinite-scroll-container').append(response.html);
                    nextPageUrl = response.next_page_url; 
                }
                isLoading = false;
                $('#loading-spinner').hide();
            },
            error: function() {
                isLoading = false;
                $('#loading-spinner').hide();
            }
        });
    }
</script>
@endpush