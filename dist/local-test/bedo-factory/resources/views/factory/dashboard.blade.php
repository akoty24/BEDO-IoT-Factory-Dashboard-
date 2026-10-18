@extends('layouts.factory')

@section('title', 'Dashboard')

@php
    $card = 'bg-white border border-gray-200 dark:bg-gray-900 dark:border-gray-800 rounded-2xl p-5 shadow-sm';
    $title = 'text-sm font-bold text-gray-800 dark:text-gray-100 tracking-wider';
    $empty = 'text-xs text-gray-500 dark:text-gray-400';
@endphp

@section('content')
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

        <!-- Line status: factory/state, factory/online, factory/message -->
        <section class="{{ $card }}">
            <div class="flex items-center justify-between mb-3 border-b border-gray-100 dark:border-gray-800 pb-2">
                <h3 class="{{ $title }}">Line Status</h3>
                <button type="button" @click="refreshData()" title="Ask the trainer to send all data again"
                        class="text-xs font-semibold text-amber-600 dark:text-amber-400 hover:underline">↻ Refresh</button>
            </div>
            <dl class="grid grid-cols-2 gap-2 text-xs">
                <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 rounded-xl p-3">
                    <dt class="text-gray-500 dark:text-gray-400 uppercase tracking-wider text-[10px]">Status</dt>
                    <dd class="font-mono font-bold text-gray-800 dark:text-gray-100" x-text="machineStateLabel">—</dd>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 rounded-xl p-3">
                    <dt class="text-gray-500 dark:text-gray-400 uppercase tracking-wider text-[10px]">Trainer</dt>
                    <dd class="font-mono font-bold" :class="trainerOnline ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'"
                        x-text="trainerOnline === null ? '—' : (trainerOnline ? 'ONLINE' : 'OFFLINE')">—</dd>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 rounded-xl p-3">
                    <dt class="text-gray-500 dark:text-gray-400 uppercase tracking-wider text-[10px]">Mode</dt>
                    <dd class="font-mono font-bold text-gray-800 dark:text-gray-100" x-text="display(machine?.mode)">—</dd>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 rounded-xl p-3">
                    <dt class="text-gray-500 dark:text-gray-400 uppercase tracking-wider text-[10px]">Step</dt>
                    <dd class="font-mono font-bold text-gray-800 dark:text-gray-100" x-text="display(machine?.step)">—</dd>
                </div>
                <div class="col-span-2 bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 rounded-xl p-3">
                    <dt class="text-gray-500 dark:text-gray-400 uppercase tracking-wider text-[10px]">Message</dt>
                    <dd class="font-medium text-gray-800 dark:text-gray-100" x-text="message || '—'">—</dd>
                </div>
            </dl>
        </section>

        <!-- Current part: factory/part -->
        <section class="{{ $card }}">
            <h3 class="{{ $title }} mb-3 border-b border-gray-100 dark:border-gray-800 pb-2">Current Part</h3>
            <p class="{{ $empty }}" x-show="!part">No part on the line.</p>
            <dl class="divide-y divide-gray-100 dark:divide-gray-800 text-xs" x-show="part">
                <template x-for="[key, value] in entries(part)" :key="key">
                    <div class="flex justify-between gap-3 py-1.5">
                        <dt class="text-gray-500 dark:text-gray-400 capitalize" x-text="key.replaceAll('_', ' ')"></dt>
                        <dd class="font-mono font-semibold text-gray-800 dark:text-gray-100 text-right break-all" x-text="display(value)"></dd>
                    </div>
                </template>
            </dl>
        </section>

        <!-- Camera: factory/cmd/camera on|off → factory/camera/frame -->
        <section class="{{ $card }}">
            <div class="flex items-center justify-between mb-3 border-b border-gray-100 dark:border-gray-800 pb-2">
                <h3 class="{{ $title }}">Camera</h3>
                <button type="button" @click="toggleCamera()" :aria-pressed="cameraOn.toString()"
                        class="text-xs font-bold px-3 py-1 rounded-lg transition"
                        :class="cameraOn ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-amber-500 text-white hover:bg-amber-600'"
                        x-text="cameraOn ? 'Stop' : 'Start'">Start</button>
            </div>
            <div class="aspect-video rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center overflow-hidden">
                <img x-show="cameraUrl" x-cloak :src="cameraUrl" alt="Live camera picture from the trainer" class="w-full h-full object-contain">
                <span class="{{ $empty }}" x-show="!cameraUrl" x-text="cameraOn ? 'Waiting for pictures…' : 'Camera is off'"></span>
            </div>
        </section>

        <!-- Finished parts: factory/part/finished -->
        <section class="{{ $card }} xl:col-span-2">
            <h3 class="{{ $title }} mb-3 border-b border-gray-100 dark:border-gray-800 pb-2">Finished Parts <span class="font-normal text-gray-500">(since this page opened)</span></h3>
            <p class="{{ $empty }}" x-show="finishedParts.length === 0">No finished parts yet.</p>
            <ul class="space-y-1.5 text-xs max-h-48 overflow-y-auto" x-show="finishedParts.length">
                <template x-for="(item, index) in finishedParts" :key="index">
                    <li class="font-mono bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-1.5 text-gray-700 dark:text-gray-300 break-all"
                        x-text="entries(item).map(([k, v]) => k + ': ' + display(v)).join(' · ')"></li>
                </template>
            </ul>
        </section>

        <!-- Alarm history: factory/alarm/event -->
        <section class="{{ $card }}">
            <h3 class="{{ $title }} mb-3 border-b border-gray-100 dark:border-gray-800 pb-2">Alarm History</h3>
            <p class="{{ $empty }}" x-show="alarmEvents.length === 0">No alarms since this page opened.</p>
            <ul class="space-y-1.5 text-xs max-h-48 overflow-y-auto" x-show="alarmEvents.length">
                <template x-for="(event, index) in alarmEvents" :key="index">
                    <li class="bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-900/60 rounded-lg px-3 py-1.5">
                        <p class="font-bold text-red-700 dark:text-red-300" x-text="event.title"></p>
                        <p class="text-red-700/80 dark:text-red-200/80" x-show="event.detail" x-text="event.detail"></p>
                        <p class="font-mono text-[10px] text-red-600/70 dark:text-red-300/60" x-text="event.time"></p>
                    </li>
                </template>
            </ul>
        </section>
    </div>
@endsection
