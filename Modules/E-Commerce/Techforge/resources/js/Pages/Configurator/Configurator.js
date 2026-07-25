document.addEventListener('DOMContentLoaded', () => {
    // Data injected from window.PageConfig
    if (!window.PageConfig) return;

    const cpus = window.PageConfig.cpus || [];
    const motherboards = window.PageConfig.motherboards || [];
    const rams = window.PageConfig.rams || [];
    const gpus = window.PageConfig.gpus || [];
    const powerSupplies = window.PageConfig.powerSupplies || [];
    const cases = window.PageConfig.cases || [];

    // State
    let selected = {
        cpu: null,
        mobo: null,
        ram: null,
        gpu: null,
        psu: null,
        case: null
    };

    // DOM Elements
    const selCpu = document.getElementById('cpu-select');
    const selMobo = document.getElementById('mobo-select');
    const selRam = document.getElementById('ram-select');
    const selGpu = document.getElementById('gpu-select');
    const selPsu = document.getElementById('psu-select');
    const selCase = document.getElementById('case-select');

    if (!selCpu || !selMobo || !selRam || !selGpu || !selPsu || !selCase) return;

    // Helper to format currency
    const formatPrice = (p) => 'P' + parseFloat(p).toLocaleString();

    // Initialize Independent Dropdowns (CPU and GPU)
    function initBaseDropdowns() {
        selCpu.innerHTML = '<option value="">Select CPU...</option>';
        cpus.forEach(c => {
            selCpu.innerHTML += `<option value="${c.id}">${c.name} - ${formatPrice(c.price)}</option>`;
        });

        selGpu.innerHTML = '<option value="">Select GPU...</option>';
        gpus.forEach(g => {
            selGpu.innerHTML += `<option value="${g.id}">${g.name} - ${formatPrice(g.price)}</option>`;
        });
    }

    // Logic 1: Filter Motherboards by CPU Socket
    function updateMotherboards() {
        if (!selected.cpu) {
            selMobo.innerHTML = '<option value="">Select CPU First...</option>';
            selMobo.disabled = true;
            selected.mobo = null;
            updateRam();
            updateCases();
            return;
        }

        selMobo.disabled = false;
        selMobo.innerHTML = '<option value="">Select Motherboard...</option>';
        
        const compatible = motherboards.filter(m => m.socket === selected.cpu.socket);
        if (compatible.length === 0) {
            selMobo.innerHTML = '<option value="">No compatible motherboards found</option>';
        } else {
            compatible.forEach(m => {
                selMobo.innerHTML += `<option value="${m.id}">${m.name} - ${formatPrice(m.price)}</option>`;
            });
        }

        // Reset downstream
        selected.mobo = null;
        updateRam();
        updateCases();
    }

    // Logic 2: Filter RAM by Motherboard Supported Gen
    function updateRam() {
        if (!selected.mobo) {
            selRam.innerHTML = '<option value="">Select Motherboard First...</option>';
            selRam.disabled = true;
            selected.ram = null;
            return;
        }

        selRam.disabled = false;
        selRam.innerHTML = '<option value="">Select RAM...</option>';
        
        const compatible = rams.filter(r => r.generation === selected.mobo.supported_ram_gen);
        if (compatible.length === 0) {
            selRam.innerHTML = '<option value="">No compatible RAM found</option>';
        } else {
            compatible.forEach(r => {
                selRam.innerHTML += `<option value="${r.id}">${r.name} - ${formatPrice(r.price)}</option>`;
            });
        }
    }

    // Logic 3: Filter Cases by Motherboard Form Factor
    function updateCases() {
        if (!selected.mobo) {
            selCase.innerHTML = '<option value="">Select Motherboard First...</option>';
            selCase.disabled = true;
            selected.case = null;
            return;
        }

        selCase.disabled = false;
        selCase.innerHTML = '<option value="">Select Case...</option>';
        
        // max_mobo_size >= form_factor
        const compatible = cases.filter(c => c.max_mobo_size >= selected.mobo.form_factor);
        if (compatible.length === 0) {
            selCase.innerHTML = '<option value="">No compatible cases found</option>';
        } else {
            compatible.forEach(c => {
                selCase.innerHTML += `<option value="${c.id}">${c.name} - ${formatPrice(c.price)}</option>`;
            });
        }
    }

    // Logic 4: Filter PSU by CPU + GPU Wattage Buffer
    function updatePSUs() {
        if (!selected.cpu || !selected.gpu) {
            selPsu.innerHTML = '<option value="">Select CPU and GPU First...</option>';
            selPsu.disabled = true;
            selected.psu = null;
            return;
        }

        selPsu.disabled = false;
        selPsu.innerHTML = '<option value="">Select Power Supply...</option>';
        
        const totalTdp = parseInt(selected.cpu.tdp) + parseInt(selected.gpu.tdp);
        const recommendedWattage = totalTdp * 1.2; // 20% buffer

        const compatible = powerSupplies.filter(p => p.wattage >= recommendedWattage);
        if (compatible.length === 0) {
            selPsu.innerHTML = `<option value="">No PSU found for >${Math.ceil(recommendedWattage)}W</option>`;
        } else {
            compatible.forEach(p => {
                selPsu.innerHTML += `<option value="${p.id}">${p.name} (${p.wattage}W) - ${formatPrice(p.price)}</option>`;
            });
        }
    }

    // Price Update
    function updateTotalPrice() {
        let total = 0;
        if (selected.cpu) total += parseFloat(selected.cpu.price);
        if (selected.mobo) total += parseFloat(selected.mobo.price);
        if (selected.ram) total += parseFloat(selected.ram.price);
        if (selected.gpu) total += parseFloat(selected.gpu.price);
        if (selected.psu) total += parseFloat(selected.psu.price);
        if (selected.case) total += parseFloat(selected.case.price);

        const totalPriceEl = document.getElementById('total-price');
        if (totalPriceEl) totalPriceEl.innerText = formatPrice(total);
    }

    // Event Listeners
    selCpu.addEventListener('change', (e) => {
        selected.cpu = cpus.find(c => c.id == e.target.value) || null;
        const cpuInfo = document.getElementById('cpu-info');
        if (cpuInfo) cpuInfo.innerText = selected.cpu ? `Socket: ${selected.cpu.socket} | TDP: ${selected.cpu.tdp}W` : '';
        updateMotherboards();
        updatePSUs();
        updateTotalPrice();
    });

    selMobo.addEventListener('change', (e) => {
        selected.mobo = motherboards.find(m => m.id == e.target.value) || null;
        let ff = '';
        if (selected.mobo) {
            if(selected.mobo.form_factor == 4) ff = 'E-ATX';
            else if(selected.mobo.form_factor == 3) ff = 'ATX';
            else if(selected.mobo.form_factor == 2) ff = 'Micro-ATX';
            else ff = 'Mini-ITX';
        }
        const moboInfo = document.getElementById('mobo-info');
        if (moboInfo) moboInfo.innerText = selected.mobo ? `Socket: ${selected.mobo.socket} | RAM: ${selected.mobo.supported_ram_gen} | Size: ${ff}` : '';
        updateRam();
        updateCases();
        updateTotalPrice();
    });

    selRam.addEventListener('change', (e) => {
        selected.ram = rams.find(r => r.id == e.target.value) || null;
        const ramInfo = document.getElementById('ram-info');
        if (ramInfo) ramInfo.innerText = selected.ram ? `Gen: ${selected.ram.generation} | ${selected.ram.capacity}GB @ ${selected.ram.speed}MHz` : '';
        updateTotalPrice();
    });

    selGpu.addEventListener('change', (e) => {
        selected.gpu = gpus.find(g => g.id == e.target.value) || null;
        const gpuInfo = document.getElementById('gpu-info');
        if (gpuInfo) gpuInfo.innerText = selected.gpu ? `TDP: ${selected.gpu.tdp}W | Length: ${selected.gpu.length_mm}mm` : '';
        updatePSUs();
        updateTotalPrice();
    });

    selPsu.addEventListener('change', (e) => {
        selected.psu = powerSupplies.find(p => p.id == e.target.value) || null;
        const psuInfo = document.getElementById('psu-info');
        if (psuInfo) psuInfo.innerText = selected.psu ? `Wattage: ${selected.psu.wattage}W | ${selected.psu.form_factor}` : '';
        updateTotalPrice();
    });

    selCase.addEventListener('change', (e) => {
        selected.case = cases.find(c => c.id == e.target.value) || null;
        const caseInfo = document.getElementById('case-info');
        if (caseInfo) caseInfo.innerText = selected.case ? `Max Mobo: ${selected.case.max_mobo_size >= 3 ? 'ATX' : 'Micro-ATX'} | Max GPU: ${selected.case.max_gpu_length}mm` : '';
        updateTotalPrice();
    });

    // Bootstrap
    initBaseDropdowns();
});
