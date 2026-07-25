@extends('bi::layouts.app')

@section('content')
    <div id="ai-insights-view" class="tab-content active-tab" style="display:block;">
        <div class="subheader-bar">
            <div class="subheader-title">
                <h3>AI Insights Center</h3>
                <p>AI-generated business insights, recommendations, and alerts.</p>
            </div>
            <div class="subheader-controls">
                <div class="control-date-selector">
                    <i data-lucide="calendar" class="control-icon-sm"></i>
                    {{ now()->format('M d') }} - {{ now()->addDays(7)->format('M d, Y') }}
                </div>
            </div>
        </div>
        <div class="content-container">

            {{-- System Alerts --}}
            <div class="insight-card" id="alertsCard">
                <div class="alerts-header-row">
                    <h3>Recent System Alerts</h3>
                </div>
                <p style="font-size:11px; color: var(--slate-500); margin-bottom: 0.75rem;">Full details for all active
                    alerts</p>
                <div class="alerts-scroll-row">
                    @forelse($alerts as $alert)
                        <div class="alert-square alert-square-{{ $alert['type'] }}">
                            <div class="alert-square-icon"><i data-lucide="{{ $alert['icon'] }}"></i></div>
                            <strong>{{ $alert['title'] }}</strong>
                            <span class="alert-square-time">{{ $alert['time'] }} • {{ $alert['priority'] }}</span>
                            <p>{{ $alert['description'] }}</p>
                            @if(!empty($alert['details']))
                                <table>
                                    @foreach($alert['details'] as $row)
                                        <tr>
                                            @foreach($row as $cell)
                                                <td>{{ $cell }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </table>
                            @endif
                            <p class="alert-square-action">{{ $alert['action'] }}</p>
                        </div>
                    @empty
                        <p style="color: var(--slate-500); font-size: 11px;">No active alerts at this time.</p>
                    @endforelse
                </div>
            </div>

            <div class="ai-insights-grid">
                {{-- Executive Summary --}}
                <div class="insight-card">
                    <h3>Executive Summary <span class="info-dot"
                            data-tooltip="Overview of the most critical business metrics and performance indicators across all modules.">i</span>
                    </h3>
                    <div class="card-subtitle">{{ empty($executiveSummary) ? 'No data available' : 'Metric-driven analysis' }}
                    </div>
                    <div class="insight-list">
                        @forelse($executiveSummary as $item)
                            <div class="insight-item">
                                <div class="insight-icon-circle bg-icon-{{ $item['color'] }}">
                                    <i data-lucide="{{ $item['icon'] }}" class="insight-icon-sm"></i>
                                </div>
                                <div class="insight-text-wrapper">
                                    <p>{{ $item['text'] }}</p>
                                    @if(!empty($item['sub_text']))
                                        <div class="sub-text">{{ $item['sub_text'] }}</div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="insight-item">
                                <div class="insight-text-wrapper">
                                    <p style="color: var(--slate-500);">Insights will appear here once connected to data
                                        sources.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Top Recommendations --}}
                <div class="insight-card">
                    <h3>Top Recommendations <span class="info-dot"
                            data-tooltip="Prioritized actionable recommendations generated from your live metrics.">i</span></h3>
                    <div class="card-subtitle">&nbsp;</div>
                    <div class="insight-list">
                        @forelse($recommendations as $index => $rec)
                            <div class="insight-item">
                                <div class="insight-icon-circle bg-icon-num">{{ $index + 1 }}</div>
                                <div class="insight-text-wrapper">
                                    <p><strong>{{ $rec['title'] }}</strong></p>
                                    <div class="sub-text">{{ $rec['description'] }}</div>
                                </div>
                                <span class="mock-badge mb-{{ strtolower($rec['impact']) }}-impact">{{ $rec['impact'] }}
                                    Impact</span>
                            </div>
                        @empty
                            <div class="insight-item">
                                <div class="insight-text-wrapper">
                                    <p style="color: var(--slate-500);">No recommendations right now — all tracked metrics look
                                        healthy.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Risk Detection --}}
                <div class="insight-card">
                    <h3>Risk Detection <span class="info-dot"
                            data-tooltip="Automated risk monitoring across supply chain, operations, and financial domains.">i</span>
                    </h3>
                    <div class="card-subtitle">&nbsp;</div>
                    <div class="insight-list">
                        @forelse($risks as $risk)
                            <div class="insight-item">
                                <div class="insight-icon-circle bg-icon-{{ $risk['color'] }}">
                                    <i data-lucide="{{ $risk['icon'] }}" class="insight-icon-sm"></i>
                                </div>
                                <div class="insight-text-wrapper">
                                    <p><strong>{{ $risk['title'] }}</strong></p>
                                    <div class="sub-text">{{ $risk['description'] }}</div>
                                </div>
                                <span class="mock-badge mb-{{ strtolower($risk['level']) }}">{{ $risk['level'] }}</span>
                            </div>
                        @empty
                            <div class="insight-item">
                                <div class="insight-text-wrapper">
                                    <p style="color: var(--slate-500);">No risks detected across the tracked metrics.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
