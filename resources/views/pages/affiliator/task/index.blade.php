@extends('layouts.app')

@section('title', 'Tugas Saya')

@section('content')
<x-organisms.mobile-page-wrapper title="Tugas Affiliator" subtitle="Unggah bukti link video TikTok untuk menyelesaikan kewajiban sampel gratis Anda.">
    
    <x-molecules.glass-tabs>
        <x-molecules.glass-tab-item 
            :active="$currentTab === 'process-overdue' || $currentTab === 'request-sample'" 
            href="{{ route('affiliator.task.index', ['tab' => 'process-overdue']) }}">
            Perlu Dikerjakan
        </x-molecules.glass-tab-item>
        
        <x-molecules.glass-tab-item 
            :active="$currentTab === 'completed'" 
            href="{{ route('affiliator.task.index', ['tab' => 'completed']) }}">
            Tugas Selesai
        </x-molecules.glass-tab-item>
    </x-molecules.glass-tabs>

    <div class="tab-content" style="animation: fadeInUp 0.4s ease; margin-top: 16px;">
        @if($currentTab === 'completed')
            @include('pages.affiliator.task.completed.index', ['data' => $data])
        @else
            @include('pages.affiliator.task.all.index', ['data' => $data])
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
        if($(window).scrollTop() + $(window).height() >= $(document).height() - 120) {
            if(nextPageUrl && !isLoading) {
                loadMoreTasks();
            }
        }
    });

    function loadMoreTasks() {
        isLoading = true;
        $('#task-loading-spinner').show();

        $.ajax({
            url: nextPageUrl,
            type: 'GET',
            data: { tab: currentTab },
            success: function(response) {
                if(response.html) {
                    $('#task-infinite-container').append(response.html);
                    nextPageUrl = response.next_page_url;
                }
                isLoading = false;
                $('#task-loading-spinner').hide();
            },
            error: function() {
                isLoading = false;
                $('#task-loading-spinner').hide();
            }
        });
    }
</script>
@endpush