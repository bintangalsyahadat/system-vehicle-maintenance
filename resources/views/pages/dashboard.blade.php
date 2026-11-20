@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-[#1E293B] rounded-3xl p-8 border border-slate-100 dark:border-slate-800 shadow-sm flex items-center justify-between transition-colors">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-brand-main dark:text-brand-accent bg-brand-secondary/40 dark:bg-brand-main/20 px-3 py-1 rounded-full">Menu 1: Dev 1 (Lead)</span>
            <h2 class="text-xl font-bold text-slate-800 dark:text-white mt-2">Dashboard Servis & Smart Odometer</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Area kerja Menu 1: Odometer Real-time Decay Engine, Kalender Servis, & 5 Part Vital.</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-brand-secondary/40 dark:bg-brand-main/20 flex items-center justify-center text-brand-main dark:text-brand-accent">
            <i data-lucide="layout-grid" class="w-6 h-6"></i>
        </div>
    </div>
</div>
@endsection
