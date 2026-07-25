<?php

use Illuminate\Support\Facades\Route;

// One deployment serves every client store. For example, the company whose
// ecommerce_slug is "rog" is available at rog.shop.section4.tech.
Route::domain('{store}.'.config('ecommerce.storefront_base_domain'))
    ->middleware('ecommerce.client')
    ->name('ecommerce.')
    ->group(function (): void {

Route::get('/debug-session', function () {
    return [
        'session_id' => session()->getId(),
        'session_all' => session()->all(),
        'cookies' => request()->cookies->all(),
        'auth_check' => \Illuminate\Support\Facades\Auth::guard('ecommerce')->check(),
        'auth_id' => \Illuminate\Support\Facades\Auth::guard('ecommerce')->id(),
        'client_context' => app(\Modules\Ecommerce\Support\EcommerceClientContext::class)->clientId(),
    ];
});

Route::get('/', [\Modules\Ecommerce\Http\Controllers\StorefrontController::class, 'index'])->name('home');

Route::get('/login', function () {
    return view('ecommerce::auth.login');
})->name('login');

Route::get('/register', function () {
    return redirect()->route('ecommerce.login', ['register' => 'true']);
})->name('register');

Route::post('/login', [\Modules\Ecommerce\Http\Controllers\AuthController::class, 'login'])->name('login.post');
Route::post('/register', [\Modules\Ecommerce\Http\Controllers\AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [\Modules\Ecommerce\Http\Controllers\AuthController::class, 'logout'])->name('logout');
Route::get('/listings/{listing}', [\Modules\Ecommerce\Http\Controllers\StorefrontListingController::class, 'show'])->name('listings.show');
Route::post('/listings/{listing}/cart', [\Modules\Ecommerce\Http\Controllers\StorefrontListingController::class, 'addToCart'])->name('listings.cart');

// Social Auth Routes
Route::get('/auth/complete-registration', [\Modules\Ecommerce\Http\Controllers\Auth\SocialAuthController::class, 'completeRegistration'])->name('social.complete-registration');
Route::post('/auth/complete-registration', [\Modules\Ecommerce\Http\Controllers\Auth\SocialAuthController::class, 'processRegistration'])->name('social.process-registration');
Route::get('/auth/{provider}', [\Modules\Ecommerce\Http\Controllers\Auth\SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [\Modules\Ecommerce\Http\Controllers\Auth\SocialAuthController::class, 'callback'])->name('social.callback');

Route::get('/cart', [\Modules\Ecommerce\Http\Controllers\CartController::class, 'index'])->name('cart');

Route::get('/notifications', function () {
    return view('ecommerce::notifications');
})->name('notifications');

Route::middleware([\Modules\Ecommerce\Http\Middleware\RequireEcommerceAuth::class])->group(function () {
    Route::get('/account/profile', [\Modules\Ecommerce\Http\Controllers\AccountController::class, 'index'])->name('account.profile');
    Route::get('/account/purchases', [\Modules\Ecommerce\Http\Controllers\AccountController::class, 'index'])->name('account.purchases');
    Route::get('/account/order-history', [\Modules\Ecommerce\Http\Controllers\AccountController::class, 'orderHistory'])->name('account.order-history');
    Route::get('/account/orders/{id}', [\Modules\Ecommerce\Http\Controllers\AccountController::class, 'showOrder'])->name('account.orders.show');
    Route::post('/account/orders/{id}/received', [\Modules\Ecommerce\Http\Controllers\AccountController::class, 'confirmReceived'])->name('account.orders.received');

    Route::post('/account/profile', [\Modules\Ecommerce\Http\Controllers\AccountController::class, 'updateProfile'])->name('account.profile.update');

    // Payment Methods Routes
    Route::post('/account/payment-methods/card', [\Modules\Ecommerce\Http\Controllers\PaymentMethodController::class, 'storeCard'])->name('account.payment-methods.store-card');
    Route::post('/account/payment-methods/bank', [\Modules\Ecommerce\Http\Controllers\PaymentMethodController::class, 'storeBank'])->name('account.payment-methods.store-bank');
    Route::delete('/account/payment-methods/{paymentMethod}', [\Modules\Ecommerce\Http\Controllers\PaymentMethodController::class, 'destroy'])->name('account.payment-methods.destroy');
    Route::put('/account/payment-methods/{paymentMethod}', [\Modules\Ecommerce\Http\Controllers\PaymentMethodController::class, 'update'])->name('account.payment-methods.update');
    Route::post('/account/payment-methods/{paymentMethod}/default', [\Modules\Ecommerce\Http\Controllers\PaymentMethodController::class, 'setDefault'])->name('account.payment-methods.set-default');

    // Address Routes
    Route::post('/account/addresses', [\Modules\Ecommerce\Http\Controllers\AddressController::class, 'store'])->name('account.addresses.store');
    Route::put('/account/addresses/{address}', [\Modules\Ecommerce\Http\Controllers\AddressController::class, 'update'])->name('account.addresses.update');
    Route::delete('/account/addresses/{address}', [\Modules\Ecommerce\Http\Controllers\AddressController::class, 'destroy'])->name('account.addresses.destroy');
    Route::post('/account/addresses/{address}/default', [\Modules\Ecommerce\Http\Controllers\AddressController::class, 'setDefault'])->name('account.addresses.set-default');
});

Route::get('/configurator-overview/{id}', function ($id) {
    $product = \Modules\Ecommerce\Models\CustombuiltConfig::with(['intelCpu', 'amdCpu', 'gpu', 'intelMotherboard', 'amdMotherboard', 'intelRam', 'amdRam', 'storage', 'powerSupply', 'pcCase', 'cooler'])->findOrFail($id);

    $cpus = \Modules\Ecommerce\Models\Cpu::all()->map(function($i) { $i->component_category = 'Processor'; return $i; });
    $gpus = \Modules\Ecommerce\Models\Gpu::all()->map(function($i) { $i->component_category = 'Video Card'; return $i; });
    $rams = \Modules\Ecommerce\Models\Ram::all()->map(function($i) { $i->component_category = 'Memory'; return $i; });
    $storages = \Modules\Ecommerce\Models\Storage::all()->map(function($i) { $i->storage_type = $i->type; $i->component_category = 'Storage'; return $i; });
    $mobos = \Modules\Ecommerce\Models\Motherboard::all()->map(function($i) { $i->component_category = 'Motherboard'; return $i; });
    $psus = \Modules\Ecommerce\Models\PowerSupply::all()->map(function($i) { $i->component_category = 'Power Supply'; return $i; });
    $cases = \Modules\Ecommerce\Models\PcCase::all()->map(function($i) { $i->component_category = 'Case'; return $i; });
    $coolers = \Modules\Ecommerce\Models\Cooler::all()->map(function($i) { $i->component_category = 'Cooling'; return $i; });
    $caseFans = \Modules\Ecommerce\Models\ChasisFan::all()->map(function($i) { $i->component_category = 'Case Fan'; return $i; });

    $allComponents = $cpus->concat($gpus)->concat($rams)->concat($storages)->concat($mobos)->concat($psus)->concat($cases)->concat($coolers)->concat($caseFans);

    return view('ecommerce::configurator-overview', compact('product', 'allComponents'));
})->name('configurator-overview');

Route::get('/custompc-overview/{id}', function ($id) {
    if (str_starts_with($id, 'custom-pc-') || str_starts_with($id, 'custom_')) {
        $configuration = null;
        if (\Illuminate\Support\Facades\Auth::guard('ecommerce')->check()) {
            $cartItem = \Modules\Ecommerce\Models\CartItem::where('product_id', $id)->first();
            if ($cartItem) $configuration = $cartItem->configuration;
        } else {
            $cart = session()->get('cart', []);
            if (isset($cart[$id])) $configuration = $cart[$id]['configuration'] ?? null;
        }

        if (!$configuration) {
            return redirect('/cart')->with('error', 'This custom PC build is from an older session and its configuration data was lost. Please remove it and build a new one.');
        }

        $config = json_decode($configuration, true);
        $product = new \Modules\Ecommerce\Models\CustombuiltConfig();
        $product->id = $id;
        $product->name = 'Custom PC Build';

        // Sum total from config
        $total = 0;
        foreach($config as $part) {
            if (isset($part['price'])) $total += floatval($part['price']);
        }
        $product->price = $total;

        if (isset($config['Processor'])) $product->setRelation('intelCpu', new \Modules\Ecommerce\Models\Cpu((array)$config['Processor']));
        if (isset($config['Motherboard'])) $product->setRelation('intelMotherboard', new \Modules\Ecommerce\Models\Motherboard((array)$config['Motherboard']));
        if (isset($config['Memory'])) $product->setRelation('intelRam', new \Modules\Ecommerce\Models\Ram((array)$config['Memory']));
        if (isset($config['Video Card'])) $product->setRelation('gpu', new \Modules\Ecommerce\Models\Gpu((array)$config['Video Card']));
        if (isset($config['Primary Storage'])) $product->setRelation('storage', new \Modules\Ecommerce\Models\Storage((array)$config['Primary Storage']));
        if (isset($config['Power Supply'])) $product->setRelation('powerSupply', new \Modules\Ecommerce\Models\PowerSupply((array)$config['Power Supply']));
        if (isset($config['Case'])) $product->setRelation('pcCase', new \Modules\Ecommerce\Models\PcCase((array)$config['Case']));
        if (isset($config['Cooling'])) $product->setRelation('cooler', new \Modules\Ecommerce\Models\Cooler((array)$config['Cooling']));

        return view('ecommerce::custompc-overview', compact('product'));
    }

    $product = \Modules\Ecommerce\Models\CustombuiltConfig::with(['intelCpu', 'amdCpu', 'gpu', 'intelMotherboard', 'amdMotherboard', 'intelRam', 'amdRam', 'storage', 'powerSupply', 'pcCase', 'cooler'])->findOrFail($id);
    return view('ecommerce::custompc-overview', compact('product'));
})->name('custompc-overview');


Route::get('/laptop-overview/{id}', function ($id) {
    $product = \Modules\Ecommerce\Models\Laptop::findOrFail($id);
    return view('ecommerce::laptop-overview', compact('product'));
})->name('laptop-overview');

Route::get('/prebuilt-overview/{id}', function ($id) {
    $product = \Modules\Ecommerce\Models\PrebuiltConfig::with(['cpu', 'gpu', 'motherboard', 'ram', 'storage', 'powerSupply', 'pcCase'])->findOrFail($id);
    return view('ecommerce::prebuilt-overview', compact('product'));
})->name('prebuilt-overview');

Route::get('/build-pc', function () {
    $cpus = \Modules\Ecommerce\Models\Cpu::all()->map(function($i) { $i->type = 'Processor'; return $i; });
    $gpus = \Modules\Ecommerce\Models\Gpu::all()->map(function($i) { $i->type = 'Video Card'; return $i; });
    $rams = \Modules\Ecommerce\Models\Ram::all()->map(function($i) { $i->type = 'Memory'; return $i; });
    $storages = \Modules\Ecommerce\Models\Storage::all()->map(function($i) { $i->storage_type = $i->type; $i->type = 'Storage'; return $i; });
    $mobos = \Modules\Ecommerce\Models\Motherboard::all()->map(function($i) { $i->type = 'Motherboard'; return $i; });
    $psus = \Modules\Ecommerce\Models\PowerSupply::all()->map(function($i) { $i->type = 'Power Supply'; return $i; });
    $cases = \Modules\Ecommerce\Models\PcCase::all()->map(function($i) { $i->type = 'Case'; return $i; });
    $coolers = \Modules\Ecommerce\Models\Cooler::all()->map(function($i) { $i->type = 'Cooling'; return $i; });
    $caseFans = \Modules\Ecommerce\Models\ChasisFan::all()->map(function($i) { $i->type = 'Case Fan'; return $i; });

    $allComponents = $cpus->concat($gpus)->concat($rams)->concat($storages)->concat($mobos)->concat($psus)->concat($cases)->concat($coolers)->concat($caseFans);

    return view('ecommerce::plugins.build-pc', compact('allComponents'));
})->name('build-pc');

Route::get('/pc-configurator', [\Modules\Ecommerce\Http\Controllers\CustomPcController::class, 'index'])->name('pc-configurator');
Route::get('/prebuilt-pcs', [\Modules\Ecommerce\Http\Controllers\PrebuiltPcController::class, 'index'])->name('prebuilt-pcs');
Route::get('/gaming-laptops', [\Modules\Ecommerce\Http\Controllers\LaptopController::class, 'index'])->name('gaming-laptops');
Route::get('/store/accessories', [\Modules\Ecommerce\Http\Controllers\AccessoryController::class, 'index'])->name('store.accessories');
Route::get('/store/monitors', [\Modules\Ecommerce\Http\Controllers\AccessoryController::class, 'monitors'])->name('store.monitors');
Route::get('/store/pc-parts', [\Modules\Ecommerce\Http\Controllers\PcPartController::class, 'index'])->name('store.pc-parts');
Route::get('/forge-store', function () {
    $accessories = collect([
        \Modules\Ecommerce\Models\AccessoryKeyboard::latest()->first(),
        \Modules\Ecommerce\Models\AccessoryHeadset::latest()->first(),
        \Modules\Ecommerce\Models\AccessoryMouse::latest()->first(),
        \Modules\Ecommerce\Models\AccessoryMousePad::latest()->first(),
        \Modules\Ecommerce\Models\AccessorySpeakerSystem::latest()->first(),
        \Modules\Ecommerce\Models\AccessoryKeyboardAccessory::latest()->first()
    ])->filter()->map(function ($item) {
        $item->category = match(class_basename($item)) {
            'AccessoryKeyboard' => 'Keyboard',
            'AccessoryHeadset' => 'Headset',
            'AccessoryMouse' => 'Mouse',
            'AccessoryMousePad' => 'Mouse Pad',
            'AccessorySpeakerSystem' => 'Audio',
            'AccessoryKeyboardAccessory' => 'Keyboard Mod',
            default => 'Accessory'
        };
        $item->rating = 5;
        $item->reviews = rand(50, 200);
        $item->sale = true;
        $item->originalPrice = $item->price * 1.25;
        return $item;
    });

    $monitors = \Modules\Ecommerce\Models\AccessoryMonitor::latest()->take(6)->get()->map(function ($item) {
        $item->category = 'Monitor';
        $item->rating = 5;
        $item->reviews = rand(20, 300);
        $item->sale = true;
        $item->originalPrice = $item->price * 1.2;
        return $item;
    });

    $pcParts = collect([
        \Modules\Ecommerce\Models\Cpu::latest()->first(),
        \Modules\Ecommerce\Models\Gpu::latest()->first(),
        \Modules\Ecommerce\Models\Motherboard::latest()->first(),
        \Modules\Ecommerce\Models\Ram::latest()->first(),
        \Modules\Ecommerce\Models\Storage::latest()->first(),
        \Modules\Ecommerce\Models\PcCase::latest()->first(),
    ])->filter()->map(function ($item) {
        $item->category = match(class_basename($item)) {
            'Cpu' => 'Processor',
            'Gpu' => 'Video Card',
            'Motherboard' => 'Motherboard',
            'Ram' => 'Memory',
            'Storage' => 'Storage',
            'PcCase' => 'Case',
            default => 'PC Part'
        };
        $item->rating = 5;
        $item->reviews = rand(15, 200);
        $item->sale = true;
        $item->originalPrice = $item->price * 1.15;
        return $item;
    });

    return view('ecommerce::forge-store', compact('accessories', 'monitors', 'pcParts'));
})->name('forge-store');
Route::get('/search', [\Modules\Ecommerce\Http\Controllers\SearchController::class, 'index'])->name('search');
Route::get('/api/search/suggestions', [\Modules\Ecommerce\Http\Controllers\SearchController::class, 'suggestions'])->name('search.suggestions');

Route::get('/cart/checkout-redirect', function () {
    if (\Illuminate\Support\Facades\Auth::guard('ecommerce')->check()) {
        return redirect()->route('ecommerce.checkout.index');
    }
    session()->put('redirect_after_auth', route('ecommerce.cart'));
    return redirect()->route('ecommerce.login');
})->name('cart.checkout.redirect');

Route::post('/cart/add', [\Modules\Ecommerce\Http\Controllers\CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/update-quantity', [\Modules\Ecommerce\Http\Controllers\CartController::class, 'updateQuantity'])->name('cart.update-quantity');
Route::delete('/cart/remove', [\Modules\Ecommerce\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
Route::get('/cart/count', [\Modules\Ecommerce\Http\Controllers\CartController::class, 'getCount'])->name('cart.count');

Route::middleware([\Modules\Ecommerce\Http\Middleware\RequireEcommerceAuth::class])->group(function () {
    Route::get('/checkout', [\Modules\Ecommerce\Http\Controllers\CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [\Modules\Ecommerce\Http\Controllers\CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success/{id}', [\Modules\Ecommerce\Http\Controllers\CheckoutController::class, 'success'])->name('checkout.success');
});

Route::get('/{slug}', [\Modules\Ecommerce\Http\Controllers\DynamicPageController::class, 'show'])
    ->where('slug', '.*')
    ->name('dynamic.page');

});

Route::prefix('ecommerce-admin')->name('ecommerce.admin.')->group(function (): void {
    Route::get('/login', [\Modules\Ecommerce\Http\Controllers\EcommerceAdminController::class, 'login'])->name('login');
    Route::post('/login', [\Modules\Ecommerce\Http\Controllers\EcommerceAdminController::class, 'authenticate'])->name('login.post');

    Route::middleware('ecommerce.admin')->group(function (): void {
        Route::get('/', [\Modules\Ecommerce\Http\Controllers\EcommerceAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/listings', [\Modules\Ecommerce\Http\Controllers\EcommerceAdminController::class, 'listings'])->name('listings');
        Route::get('/listings/create', [\Modules\Ecommerce\Http\Controllers\EcommerceAdminController::class, 'createListing'])->name('listings.create');
        Route::post('/listings', [\Modules\Ecommerce\Http\Controllers\EcommerceAdminController::class, 'storeListing'])->name('listings.store');
        Route::get('/listings/{listing}/edit', [\Modules\Ecommerce\Http\Controllers\EcommerceAdminController::class, 'editListing'])->name('listings.edit');
        Route::put('/listings/{listing}', [\Modules\Ecommerce\Http\Controllers\EcommerceAdminController::class, 'updateListing'])->name('listings.update');
        Route::delete('/listings/{listing}', [\Modules\Ecommerce\Http\Controllers\EcommerceAdminController::class, 'destroyListing'])->name('listings.destroy');
        Route::get('/orders', [\Modules\Ecommerce\Http\Controllers\EcommerceAdminController::class, 'orders'])->name('orders');
        Route::get('/layout', [\Modules\Ecommerce\Http\Controllers\EcommerceAdminController::class, 'editLayout'])->name('layout.edit');
        Route::put('/layout', [\Modules\Ecommerce\Http\Controllers\EcommerceAdminController::class, 'saveLayout'])->name('layout.save');
        Route::get('/layout/preview', [\Modules\Ecommerce\Http\Controllers\EcommerceAdminController::class, 'previewLayout'])->name('layout.preview');
        Route::post('/layout/publish', [\Modules\Ecommerce\Http\Controllers\EcommerceAdminController::class, 'publishLayout'])->name('layout.publish');
        Route::post('/logout', [\Modules\Ecommerce\Http\Controllers\EcommerceAdminController::class, 'logout'])->name('logout');
    });
});

if (app()->environment('local')) {
    Route::get('/auth/{provider}/callback', function($provider, \Illuminate\Http\Request $request) {
        $domain = config('ecommerce.storefront_base_domain');
        if ($request->getHost() !== $domain) {
            $port = request()->getPort();
            $portStr = ($port != 80 && $port != 443) ? (':' . $port) : '';
            $url = 'http://' . $domain . $portStr . $request->getRequestUri();
            return redirect($url);
        }

        return app(\Modules\Ecommerce\Http\Controllers\Auth\SocialAuthController::class)->callback($provider);
    });
}
