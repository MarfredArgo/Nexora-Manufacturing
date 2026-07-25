{{-- ── UNIVERSAL CONFIRM MODAL ─────────────────────────────────────────────── --}}
<div id="universal-confirm-backdrop"
     class="modal-backdrop fixed inset-0 z-[999] flex items-center justify-center hidden"
     onclick="handleBackdropClick(event, 'universal-confirm-backdrop')">
    <div class="absolute inset-0 bg-nexora-deep-navy/40 backdrop-blur-sm pointer-events-none"></div>
    <div onclick="event.stopPropagation()"
         class="relative z-10 bg-nexora-off-white border border-nexora-corporate/50 rounded-2xl shadow-2xl w-full max-w-sm mx-4 flex flex-col">
        <div class="px-5 pt-5 pb-3">
            <h2 id="universal-confirm-title" class="text-base font-bold text-nexora-deep-navy">Are you sure?</h2>
            <p id="universal-confirm-message" class="text-xs text-nexora-navy-mid mt-1.5 leading-relaxed"></p>
        </div>
        <div class="flex gap-2 justify-end px-5 pb-5">
            <button onclick="closeModal('universal-confirm-backdrop')"
                    class="px-4 py-1.5 rounded-full text-xs font-medium border border-nexora-corporate/50
                           text-nexora-navy-mid hover:bg-nexora-slate-200 transition-colors">
                Cancel
            </button>
            <button id="universal-confirm-btn"
                    onclick="runConfirmedAction()"
                    class="px-4 py-1.5 rounded-full text-xs font-semibold bg-nexora-corporate text-white
                           hover:bg-nexora-navy-mid transition-colors">
                Confirm
            </button>
        </div>
    </div>
</div>

{{-- ── PROFILE DROPDOWN ────────────────────────────────────────────────────── --}}
<div id="profileDropdown"
     class="fixed top-16 right-6 z-[200] w-80 max-w-[calc(100vw-24px)] bg-[#e8f0fe] text-nexora-deep-navy
            rounded-[18px] shadow-2xl p-5 transition-all duration-200 opacity-0 -translate-y-2 pointer-events-none">
    <button type="button" onclick="toggleProfileDropdown()" aria-label="Close"
            class="absolute top-2.5 right-4 bg-transparent border-0 text-[22px] leading-none text-nexora-slate-500 cursor-pointer hover:text-nexora-deep-navy">&times;</button>

    <div class="text-center text-sm font-semibold text-nexora-deep-navy mb-0.5">{{ session('employee_email', '') }}</div>

    <div class="relative w-[72px] h-[72px] mx-auto mb-2.5">
        <div class="w-full h-full rounded-full bg-nexora-deep-navy text-white flex items-center justify-center text-[28px] font-semibold overflow-hidden">
            {{ strtoupper(substr(session('employee_name', 'U'), 0, 1)) }}
        </div>
    </div>

    <div class="text-center text-lg font-semibold text-nexora-deep-navy mb-3.5">Hi, {{ session('employee_name', 'User') }}!</div>

    <ul class="list-none p-0 m-0 flex flex-col gap-0.5">
        <li>
            <a href="#" class="flex items-center gap-3 px-2.5 py-3 rounded-[10px] text-sm text-nexora-deep-navy no-underline hover:bg-nexora-corporate/10 transition-colors">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" class="w-[18px] h-[18px] text-nexora-slate-500 flex-shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Settings
            </a>
        </li>
        <li>
            <a href="#" class="flex items-center gap-3 px-2.5 py-3 rounded-[10px] text-sm text-nexora-deep-navy no-underline hover:bg-nexora-corporate/10 transition-colors">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" class="w-[18px] h-[18px] text-nexora-slate-500 flex-shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Help
            </a>
        </li>
        <li>
            <form method="POST" action="{{ route('manufacturing.logout') }}" class="m-0 p-0">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-2.5 py-3 rounded-[10px] text-sm text-[#d93025] bg-transparent border-0 cursor-pointer hover:bg-[#d93025]/10 transition-colors">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" class="w-[18px] h-[18px] text-[#d93025] flex-shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Logout
                </button>
            </form>
        </li>
    </ul>
</div>

{{-- ── QC · ENTER RESULTS MODAL ────────────────────────────────────────────── --}}
<div id="benchmark-backdrop"
    class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center hidden"
    onclick="handleBackdropClick(event, 'benchmark-backdrop')">

    <div class="absolute inset-0 bg-nexora-deep-navy/40 backdrop-blur-sm pointer-events-none"></div>

    <div onclick="event.stopPropagation()"
        class="relative z-10 bg-nexora-off-white border border-nexora-corporate/50 rounded-2xl
                shadow-2xl w-full max-w-2xl mx-4 max-h-[85vh] flex flex-col">

        <div class="flex items-center justify-between px-5 pt-5 pb-3
                    border-b border-nexora-corporate/20 flex-shrink-0">
            <div>
                <p id="bm-modal-woid" class="text-[10px] text-nexora-navy-mid mb-0.5 font-['Courier_New']"></p>
                <h2 id="bm-modal-name" class="text-lg font-bold text-nexora-deep-navy"></h2>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex gap-1.5" id="bm-live-counts">
                    <span id="bm-count-pass"
                        class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-nexora-success/80 text-white">
                        0 Pass
                    </span>
                    <span id="bm-count-warn"
                        class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-nexora-warning/80 text-white">
                        0 Warn
                    </span>
                    <span id="bm-count-fail"
                        class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-nexora-danger/80 text-white">
                        0 Fail
                    </span>
                </div>
                <button onclick="closeModal('benchmark-backdrop')"
                        class="w-7 h-7 rounded-full flex items-center justify-center text-nexora-navy-mid
                            hover:bg-nexora-slate-500/20 hover:text-nexora-deep-navy transition-colors text-lg leading-none">
                    ✕
                </button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto [&::-webkit-scrollbar]:hidden px-5 py-3">
            <div class="flex flex-col gap-2" id="bm-check-list"></div>
        </div>

        <div class="flex items-center justify-between px-5 py-3
                    border-t border-nexora-corporate/20 flex-shrink-0">
            <p id="bm-save-msg" class="text-xs text-nexora-success hidden">✓ Results saved</p>
            <div class="flex gap-2 ml-auto">
                <button onclick="closeModal('benchmark-backdrop')"
                        class="px-4 py-1.5 rounded-full text-xs font-medium border border-nexora-corporate/50
                            text-nexora-navy-mid hover:bg-nexora-slate-200 transition-colors">
                    Cancel
                </button>
                <button onclick="saveBenchmarkResults()"
                        class="px-4 py-1.5 rounded-full text-xs font-semibold bg-nexora-corporate text-white
                            hover:bg-nexora-navy-mid transition-colors">
                    Save Results
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── QC · SEND TO INVENTORY MODAL ────────────────────────────────────────── --}}
<div id="inventory-backdrop"
    class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center hidden"
    onclick="handleBackdropClick(event, 'inventory-backdrop')">
    <div class="absolute inset-0 bg-nexora-deep-navy/40 backdrop-blur-sm pointer-events-none"></div>
    <div onclick="event.stopPropagation()"
         class="relative z-10 bg-nexora-off-white border border-nexora-corporate/50 rounded-2xl shadow-2xl w-full max-w-sm mx-4 flex flex-col">
        <div class="flex items-center justify-between px-5 pt-5 pb-3 border-b border-nexora-corporate/20">
            <h2 class="text-base font-bold text-nexora-deep-navy">Send to Inventory</h2>
            <button onclick="closeModal('inventory-backdrop')" class="w-7 h-7 rounded-full flex items-center justify-center text-nexora-navy-mid hover:bg-nexora-slate-500/20 transition-colors text-lg leading-none">✕</button>
        </div>
        <div class="px-5 py-4 flex flex-col gap-3">
            <div>
                <label class="text-[10px] font-semibold text-nexora-slate-500 uppercase tracking-wider">Part Name</label>
                <input id="req-part-name" type="text" placeholder="e.g. Replacement GPU"
                       class="mt-1.5 w-full border border-nexora-corporate/40 rounded-lg px-3 py-2 text-xs text-nexora-deep-navy bg-nexora-slate-200 focus:outline-none focus:border-nexora-corporate">
            </div>
            <div>
                <label class="text-[10px] font-semibold text-nexora-slate-500 uppercase tracking-wider">Quantity</label>
                <input id="req-quantity" type="number" min="1" value="1"
                       class="mt-1.5 w-full border border-nexora-corporate/40 rounded-lg px-3 py-2 text-xs text-nexora-deep-navy bg-nexora-slate-200 focus:outline-none focus:border-nexora-corporate">
            </div>
            <div>
                <label class="text-[10px] font-semibold text-nexora-slate-500 uppercase tracking-wider">Notes (optional)</label>
                <textarea id="req-notes" rows="3" placeholder="Additional context for inventory..."
                          class="mt-1.5 w-full border border-nexora-corporate/40 rounded-lg px-3 py-2 text-xs text-nexora-deep-navy bg-nexora-slate-200 focus:outline-none focus:border-nexora-corporate resize-none"></textarea>
            </div>
        </div>
        <div class="flex gap-2 justify-end px-5 pb-5">
            <button onclick="closeModal('inventory-backdrop')" class="px-4 py-1.5 rounded-full text-xs font-medium border border-nexora-corporate/50 text-nexora-navy-mid hover:bg-nexora-slate-200 transition-colors">Cancel</button>
            <button onclick="submitInventoryRequest()" class="px-4 py-1.5 rounded-full text-xs font-semibold bg-nexora-danger text-white hover:opacity-90 transition-colors">Send Request</button>
        </div>
    </div>
</div>

{{-- ── QC · EDIT REWORK MODAL ──────────────────────────────────────────────── --}}
<div id="rework-edit-backdrop" class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center hidden" onclick="handleBackdropClick(event,'rework-edit-backdrop')">
    <div class="absolute inset-0 bg-nexora-deep-navy/40 backdrop-blur-sm pointer-events-none"></div>
    <div onclick="event.stopPropagation()" class="relative z-10 bg-nexora-off-white border border-nexora-corporate/50 rounded-2xl shadow-2xl w-full max-w-md mx-4 max-h-[80vh] flex flex-col">
        <div class="flex items-center justify-between px-5 pt-5 pb-3 border-b border-nexora-corporate/20 flex-shrink-0">
            <div>
                <p class="text-[10px] text-nexora-navy-mid mb-0.5">Edit Rework Order</p>
                <h2 id="rw-modal-title" class="text-base font-bold text-nexora-deep-navy"></h2>
            </div>
            <button onclick="closeModal('rework-edit-backdrop')" class="w-7 h-7 rounded-full flex items-center justify-center text-nexora-navy-mid hover:bg-nexora-slate-500/20 transition-colors text-lg leading-none">✕</button>
        </div>
        <div class="flex-1 overflow-y-auto [&::-webkit-scrollbar]:hidden px-5 py-4 flex flex-col gap-4">
            <div>
                <label class="text-[10px] font-semibold text-nexora-slate-500 uppercase tracking-wider">Status</label>
                <select id="rw-modal-status" class="mt-1.5 w-full border border-nexora-corporate/40 rounded-lg px-3 py-2 text-xs text-nexora-deep-navy bg-nexora-slate-200 focus:outline-none focus:border-nexora-corporate">
                    <option>Waiting for Part</option>
                    <option>In Rework</option>
                    <option>Ready for QC</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-semibold text-nexora-slate-500 uppercase tracking-wider">Priority</label>
                <select id="rw-modal-priority" class="mt-1.5 w-full border border-nexora-corporate/40 rounded-lg px-3 py-2 text-xs text-nexora-deep-navy bg-nexora-slate-200 focus:outline-none focus:border-nexora-corporate">
                    <option>High</option><option>Medium</option><option>Low</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-semibold text-nexora-slate-500 uppercase tracking-wider">Technician Notes</label>
                <textarea id="rw-modal-notes" rows="4" class="mt-1.5 w-full border border-nexora-corporate/40 rounded-lg px-3 py-2 text-xs text-nexora-deep-navy bg-nexora-slate-200 focus:outline-none focus:border-nexora-corporate resize-none"></textarea>
            </div>
        </div>
        <div class="flex items-center justify-between px-5 py-3 border-t border-nexora-corporate/20 flex-shrink-0">
            <p id="rw-modal-save-msg" class="text-xs text-nexora-success hidden">✓ Saved</p>
            <div class="flex gap-2 ml-auto">
                <button onclick="closeModal('rework-edit-backdrop')" class="px-4 py-1.5 rounded-full text-xs font-medium border border-nexora-corporate/50 text-nexora-navy-mid hover:bg-nexora-slate-200 transition-colors">Cancel</button>
                <button onclick="saveReworkEdit()" class="px-4 py-1.5 rounded-full text-xs font-semibold bg-nexora-corporate text-white hover:bg-nexora-navy-mid transition-colors">Save</button>
            </div>
        </div>
    </div>
</div>

{{-- ── QC · MARK READY FOR QC MODAL ────────────────────────────────────────── --}}
<div id="qc-ready-backdrop" class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center hidden" onclick="handleBackdropClick(event,'qc-ready-backdrop')">
    <div class="absolute inset-0 bg-nexora-deep-navy/40 backdrop-blur-sm pointer-events-none"></div>
    <div onclick="event.stopPropagation()" class="relative z-10 bg-nexora-off-white border border-nexora-corporate/50 rounded-2xl shadow-2xl w-full max-w-sm mx-4">
        <div class="px-5 pt-5 pb-3 border-b border-nexora-corporate/20">
            <h2 class="text-base font-bold text-nexora-deep-navy">Mark Ready for QC?</h2>
            <p class="text-xs text-nexora-navy-mid mt-1">This will update the rework status to "Ready for QC" and queue it for a full benchmark re-check.</p>
        </div>
        <div class="flex gap-2 justify-end px-5 py-4">
            <button onclick="closeModal('qc-ready-backdrop')" class="px-4 py-1.5 rounded-full text-xs font-medium border border-nexora-corporate/50 text-nexora-navy-mid hover:bg-nexora-slate-200 transition-colors">Cancel</button>
            <button onclick="confirmMarkReadyForQC()" class="px-4 py-1.5 rounded-full text-xs font-semibold bg-nexora-success text-white hover:opacity-90 transition-colors">Confirm</button>
        </div>
    </div>
</div>

{{-- ── QC · ADD QC NOTE MODAL ──────────────────────────────────────────────── --}}
<div id="qc-note-backdrop" class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center hidden" onclick="handleBackdropClick(event,'qc-note-backdrop')">
    <div class="absolute inset-0 bg-nexora-deep-navy/40 backdrop-blur-sm pointer-events-none"></div>
    <div onclick="event.stopPropagation()" class="relative z-10 bg-nexora-off-white border border-nexora-corporate/50 rounded-2xl shadow-2xl w-full max-w-sm mx-4 flex flex-col">
        <div class="flex items-center justify-between px-5 pt-5 pb-3 border-b border-nexora-corporate/20">
            <h2 class="text-base font-bold text-nexora-deep-navy">Add QC Note</h2>
            <button onclick="closeModal('qc-note-backdrop')" class="w-7 h-7 rounded-full flex items-center justify-center text-nexora-navy-mid hover:bg-nexora-slate-500/20 transition-colors text-lg leading-none">✕</button>
        </div>
        <div class="px-5 py-4 flex flex-col gap-3">
            <div>
                <label class="text-[10px] font-semibold text-nexora-slate-500 uppercase tracking-wider">Work Order</label>
                <select id="qc-note-wo" class="mt-1.5 w-full border border-nexora-corporate/40 rounded-lg px-3 py-2 text-xs text-nexora-deep-navy bg-nexora-slate-200 focus:outline-none focus:border-nexora-corporate">
                    @foreach($qcSessions as $sess)
                        <option value="{{ $sess['woId'] }}">{{ $sess['woId'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[10px] font-semibold text-nexora-slate-500 uppercase tracking-wider">Note</label>
                <textarea id="qc-note-text" rows="4" placeholder="Enter your QC observation or note..." class="mt-1.5 w-full border border-nexora-corporate/40 rounded-lg px-3 py-2 text-xs text-nexora-deep-navy bg-nexora-slate-200 focus:outline-none focus:border-nexora-corporate resize-none"></textarea>
            </div>
        </div>
        <div class="flex gap-2 justify-end px-5 pb-5">
            <button onclick="closeModal('qc-note-backdrop')" class="px-4 py-1.5 rounded-full text-xs font-medium border border-nexora-corporate/50 text-nexora-navy-mid hover:bg-nexora-slate-200 transition-colors">Cancel</button>
            <button onclick="saveQcNote()" class="px-4 py-1.5 rounded-full text-xs font-semibold bg-nexora-corporate text-white hover:bg-nexora-navy-mid transition-colors">Save Note</button>
        </div>
    </div>
</div>

{{-- ── WORK ORDERS · EDIT / STATUS MODAL ───────────────────────────────────── --}}
<div id="edit-backdrop"
    class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center hidden"
    onclick="handleBackdropClick(event,'edit-backdrop')">

    <div class="absolute inset-0 bg-nexora-deep-navy/40 backdrop-blur-sm pointer-events-none"></div>

    <div id="edit-modal"
        onclick="event.stopPropagation()"
        class="relative z-10 bg-nexora-off-white border border-nexora-corporate/50 rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[80vh] flex flex-col">

        <div class="flex items-center justify-between px-5 pt-5 pb-3 border-b border-nexora-corporate/20 flex-shrink-0">
            <div>
                <p id="modal-order-id" class="text-[10px] text-nexora-navy-mid mb-0.5"></p>
                <h2 id="modal-order-name" class="text-lg font-bold text-nexora-deep-navy"></h2>
            </div>
            <div class="flex items-center gap-3">
                <span id="modal-order-status" class="px-2.5 py-1 rounded-full text-xs font-bold"></span>
                <button onclick="closeModal('edit-backdrop')"
                        class="w-7 h-7 rounded-full flex items-center justify-center text-nexora-navy-mid
                            hover:bg-nexora-slate-500/20 hover:text-nexora-deep-navy transition-colors text-lg leading-none">
                    ✕
                </button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto [&::-webkit-scrollbar]:hidden px-5 py-4 flex flex-col gap-4">

            <p class="text-xs font-semibold tracking-widest text-nexora-slate-500 uppercase mb-2">Order Status</p>
            <div id="section-order-status" class="hidden">
                <div class="bg-nexora-slate-200 border border-nexora-corporate/30 rounded-xl p-4 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-nexora-deep-navy">Send to QC Check</p>
                        <p class="text-xs text-nexora-navy-mid mt-0.5">Mark this finished build as ready for quality control.</p>
                    </div>
                    <button onclick="sendToQC()"
                            class="flex-shrink-0 ml-4 px-4 py-1.5 rounded-full text-xs font-semibold bg-nexora-corporate text-white
                                hover:bg-nexora-navy-mid transition-colors">
                        Send to QC
                    </button>
                </div>
            </div>
            <div id="section-cancel-order" class="bg-nexora-slate-200 border border-nexora-corporate/30 rounded-xl p-4 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-nexora-deep-navy">Cancel Build</p>
                    <p class="text-xs text-nexora-navy-mid mt-0.5">Mark this as cancelled build</p>
                </div>
                <button onclick="confirmCancelOrder(); closeModal('edit-backdrop');"
                        class="px-4 py-1.5 rounded-full text-xs font-medium bg-nexora-danger border border-nexora-stat-red/50
                            text-nexora-off-white hover:bg-nexora-stat-red hover:text-white transition-colors">
                    Cancel Order
                </button>
            </div>

            <div>
                <p class="text-xs font-semibold tracking-widest text-nexora-slate-500 uppercase mb-2">Parts</p>
                <div id="modal-parts-list" class="flex flex-col gap-1.5">
                    {{-- Populated by JS --}}
                </div>
            </div>

        </div>

        <div class="flex items-center justify-between px-5 py-3 border-t border-nexora-corporate/20 flex-shrink-0">
            <p id="modal-save-msg" class="text-xs text-nexora-success hidden">✓ Changes saved</p>
            <div class="flex gap-4 ml-auto">
                <button onclick="closeModal('edit-backdrop')"
                        class="px-4 py-1.5 rounded-full text-xs font-medium border border-nexora-corporate/50
                            text-nexora-navy-mid hover:bg-nexora-slate-200 transition-colors">
                    Cancel
                </button>
                <button onclick="saveChanges()"
                        class="px-4 py-1.5 rounded-full text-xs font-semibold bg-nexora-corporate text-white
                            hover:bg-nexora-navy-mid transition-colors">
                    Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── BoM · COMPONENT SEARCH MODAL ────────────────────────────────────────── --}}
<div id="bom-search-backdrop" class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center hidden"
     onclick="handleBackdropClick(event,'bom-search-backdrop')">
    <div class="absolute inset-0 bg-nexora-deep-navy/40 backdrop-blur-sm pointer-events-none"></div>
    <div onclick="event.stopPropagation()"
         class="relative z-10 bg-nexora-off-white border border-nexora-corporate/50 rounded-2xl shadow-2xl w-full max-w-md mx-4 max-h-[80vh] flex flex-col">
        <div class="flex items-center justify-between px-5 pt-5 pb-3 border-b border-nexora-corporate/20">
            <h2 class="text-base font-bold text-nexora-deep-navy">Search Components</h2>
            <button onclick="closeModal('bom-search-backdrop')" class="w-7 h-7 rounded-full flex items-center justify-center text-nexora-navy-mid hover:bg-nexora-slate-500/20 transition-colors text-lg leading-none">✕</button>
        </div>
        <div class="px-5 py-3 flex gap-2 border-b border-nexora-corporate/10">
            <input id="bom-search-input" type="text" placeholder="Search name or SKU..." oninput="renderBomResults()"
                   class="flex-1 border border-nexora-corporate/40 rounded-lg px-3 py-2 text-xs text-nexora-deep-navy bg-nexora-slate-200 focus:outline-none focus:border-nexora-corporate">
            <select id="bom-search-category" onchange="renderBomResults()"
                    class="border border-nexora-corporate/40 rounded-lg px-2.5 py-2 text-xs text-nexora-deep-navy bg-nexora-slate-200 focus:outline-none focus:border-nexora-corporate">
                <option value="">All categories</option>
            </select>
        </div>
        <div id="bom-search-results" class="flex-1 overflow-y-auto [&::-webkit-scrollbar]:hidden px-3 py-2 flex flex-col gap-1"></div>
    </div>
</div>
