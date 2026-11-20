@extends('layouts.app')

@section('title', 'Kalkulator Kerugian')
@section('greeting', 'Kalkulator Kerugian Servis')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm flex items-center justify-between">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-[#B83A74] bg-[#F5DBE8] px-3 py-1 rounded-full">Menu 2: Dev 3</span>
            <h2 class="text-xl font-bold text-slate-800 mt-2">Kalkulator Kerugian (Cost of Neglect)</h2>
            <p class="text-xs text-slate-500 mt-1">Area kerja Menu 2: Slider simulasi telat servis, komparasi box hijau vs merah, & analisis finansial.</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-[#F5DBE8] flex items-center justify-center text-[#B83A74]">
            <i data-lucide="calculator" class="w-6 h-6"></i>
        </div>
    </div>
</div>
@endsection
