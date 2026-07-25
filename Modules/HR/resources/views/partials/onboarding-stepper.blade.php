@php
    $currentStep = (int) ($currentStep ?? 1);
    $isSuccess = request()->routeIs('hr.onboarding.success');
    $steps = [
        1 => 'PERSONAL',
        2 => 'EMPLOYMENT',
        3 => 'DOCUMENTS',
        4 => 'POLICIES',
    ];
@endphp

<div class="flex items-start mb-10 max-w-5xl w-full">
    @foreach ($steps as $number => $title)
        @php
            $isComplete = $isSuccess || $currentStep > $number;
            $isActive = ! $isSuccess && $currentStep === $number;
            $circleClass = ($isComplete || $isActive)
                ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/40'
                : 'bg-[#0d1730] text-white';
            $labelClass = ($isComplete || $isActive)
                ? 'text-blue-300'
                : 'text-slate-500';
            $lineComplete = $isSuccess || $currentStep > $number;
        @endphp

        <div class="flex flex-col items-center w-[100px] shrink-0 z-10">
            <div class="flex items-center justify-center w-[100px] h-[100px] rounded-full font-bold {{ $circleClass }}">
                @if ($isComplete)
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                @else
                    {{ $number }}
                @endif
            </div>
            <div class="mt-3 text-[11px] font-semibold tracking-wider uppercase text-center leading-tight {{ $labelClass }}">
                {{ $title }}
            </div>
        </div>

        @if ($number < 4)
            <div class="flex-1 self-start mt-[50px] border-t-2 border-dashed mx-2 min-w-[24px] {{ $lineComplete ? 'border-blue-400' : 'border-slate-500' }}"></div>
        @endif
    @endforeach
</div>
