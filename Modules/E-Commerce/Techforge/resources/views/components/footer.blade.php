@props(['storefrontName' => null, 'store' => null, 'logoUrl' => null])
@php
    $storefrontCompany = request()->attributes->get('ecommerce_company');

    if ($storefrontCompany) {
        $isPreview = request()->boolean('preview') && \Illuminate\Support\Facades\Auth::guard('ecommerce_admin')->check();
        $publishedLayout = $isPreview ? \Modules\Ecommerce\Models\StorefrontLayout::editableFor($storefrontCompany) : \Modules\Ecommerce\Models\StorefrontLayout::publishedFor($storefrontCompany);
        $storefrontName = $storefrontName ?? ($publishedLayout['brand_name'] ?? ($storefrontCompany->company_name ?: 'Nexora Store'));
        $store = $store ?? $storefrontCompany->ecommerce_slug;
        $logoUrl = $logoUrl ?? (!empty($publishedLayout['logo_path']) ? (str_starts_with($publishedLayout['logo_path'], 'Modules/') ? Vite::asset($publishedLayout['logo_path']) : asset('storage/'.$publishedLayout['logo_path'])) : ($storefrontCompany->logoUrl() ?: asset('ecommerce/Nexora_Logo.png')));
    } else {
        // Fallback for preview mode or when middleware doesn't set it
        $storefrontName = $storefrontName ?? 'Nexora Store';
        $store = $store ?? 'techforge';
        $logoUrl = $logoUrl ?? asset('ecommerce/Nexora_Logo.png');
    }
@endphp
    <!-- Footer -->
    <footer class="border-t border-white/5 pt-16 pb-8 mt-auto relative z-10 liquid-glass bg-black/60 backdrop-blur-2xl">
        <div class="max-w-7xl mx-auto px-10 sm:px-12 lg:px-14">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 lg:gap-8 mb-12">

                <!-- Brand -->
                <div class="col-span-1 lg:pr-8">
                    <a href="#" class="flex items-center gap-3 mb-4">
                        <div class="bg-gradient-to-br from-primary to-orange-400 w-10 h-10 rounded-xl flex items-center justify-center shadow-[0_0_15px_rgba(255,107,0,0.4)]">
                            <img src="{{ $logoUrl }}" alt="{{ $storefrontName }} Logo" class="h-6 w-auto object-contain">
                        </div>
                        <span class="text-xl font-bold tracking-wide text-white uppercase">{{ $storefrontName }}</span>
                    </a>
                    <p class="text-gray-500 text-xs leading-relaxed mb-6">
                        Performance-driven computers and accessories for every digital journey.
                    </p>
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-white/5 border border-white/10 w-max">
                        <span class="text-[10px] text-gray-500 uppercase tracking-widest font-semibold">Powered by</span>
                        <img src="{{ Vite::asset('Modules/E-Commerce/Techforge/resources/img/Nexora_Logo.png') }}" alt="Nexora Logo" class="h-5 w-auto object-contain opacity-80">
                    </div>
                </div>

                <!-- Links 1 (Shop) -->
                <div>
                    <h4 class="text-primary font-black text-xs tracking-widest uppercase mb-6">Shop</h4>
                    <ul class="space-y-4 text-[13px] text-gray-400 font-medium">
                        <li><a href="{{ route('ecommerce.prebuilt-pcs', ['store' => $store]) }}" class="hover:text-white transition-colors">Pre-built PCs</a></li>
                        <li><a href="{{ route('ecommerce.pc-configurator', ['store' => $store]) }}" class="hover:text-white transition-colors">PC Configurator</a></li>
                        <li><a href="{{ route('ecommerce.gaming-laptops', ['store' => $store]) }}" class="hover:text-white transition-colors">All Gaming Laptops</a></li>
                        <li><a href="{{ route('ecommerce.build-pc', ['store' => $store]) }}" class="hover:text-white transition-colors">PC Forge</a></li>
                        <li><a href="{{ route('ecommerce.forge-store', ['store' => $store]) }}" class="hover:text-white transition-colors">Explore Forge Store</a></li>
                    </ul>
                </div>

                <!-- Links 2 (Support) -->
                <div>
                    <h4 class="text-primary font-black text-xs tracking-widest uppercase mb-6">Support</h4>
                    <ul class="space-y-4 text-[13px] text-gray-400 font-medium">
                        <li><a href="#" class="hover:text-white transition-colors">3-Year Warranty</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Order Tracking</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Returns</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Live Chat</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">FAQ</a></li>
                    </ul>
                </div>

                <!-- Links 3 (Company) -->
                <div>
                    <h4 class="text-primary font-black text-xs tracking-widest uppercase mb-6">Company</h4>
                    <ul class="space-y-4 text-[13px] text-gray-400 font-medium">
                        <li><a href="#" class="hover:text-white transition-colors">About Us</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Careers</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Press Kit</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Affiliates</a></li>
                        <li><a href="{{ url('/contact') }}" class="hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom -->
            <div class="border-t border-white/10 pt-8 flex flex-col sm:flex-row justify-between items-center gap-4">
                <p class="text-gray-600 text-xs">
                    &copy; {{ date('Y') }} {{ $storefrontName }}. All rights reserved.
                </p>
                <div class="flex items-center gap-4 text-gray-400">
                    <a href="#" class="hover:text-primary transition-colors"><i class="ph ph-instagram-logo text-xl"></i></a>
                    <a href="#" class="hover:text-primary transition-colors"><i class="ph ph-twitter-logo text-xl"></i></a>
                    <a href="#" class="hover:text-primary transition-colors"><i class="ph ph-facebook-logo text-xl"></i></a>
                    <a href="#" class="hover:text-primary transition-colors"><i class="ph ph-youtube-logo text-xl"></i></a>
                </div>
            </div>
        </div>
    </footer>
