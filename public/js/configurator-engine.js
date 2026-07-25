class ConfiguratorEngine {
    constructor(allComponents = [], initialBuild = {}) {
        this.allComponents = allComponents;
        this.currentBuild = {
            'Processor': initialBuild.Processor || null,
            'Video Card': initialBuild['Video Card'] || null,
            'Memory': initialBuild.Memory || null,
            'Primary Storage': initialBuild['Primary Storage'] || null,
            'Secondary Storage': initialBuild['Secondary Storage'] || null,
            'Motherboard': initialBuild.Motherboard || null,
            'Power Supply': initialBuild['Power Supply'] || null,
            'Case': initialBuild.Case || null,
            'Case Fan': initialBuild['Case Fan'] || null,
            'Cooling': initialBuild.Cooling || null,
        };
        this.listeners = [];
    }

    subscribe(callback) {
        this.listeners.push(callback);
    }

    notify() {
        this.listeners.forEach(cb => cb(this.currentBuild));
    }

    getComponent(category) {
        return this.currentBuild[category];
    }

    setComponent(category, component) {
        this.currentBuild[category] = component;
        this.notify();
    }

    removeComponent(category) {
        this.currentBuild[category] = null;
        this.notify();
    }

    calculateTotal() {
        let total = 0;
        Object.values(this.currentBuild).forEach(c => {
            if (c && c.price) total += parseFloat(c.price);
        });
        return total;
    }

    getRequiredWattage(overrides = {}) {
        const cpu = overrides['Processor'] !== undefined ? overrides['Processor'] : this.currentBuild['Processor'];
        const gpu = overrides['Video Card'] !== undefined ? overrides['Video Card'] : this.currentBuild['Video Card'];
        
        const cpuTdp = cpu ? parseInt(cpu.tdp || 0) : 0;
        const gpuTdp = gpu ? parseInt(gpu.tdp || 0) : 0;
        
        let baseWattage = 0;
        if (this.currentBuild['Motherboard']) baseWattage += 40;
        if (this.currentBuild['Memory']) baseWattage += 15;
        if (this.currentBuild['Primary Storage']) baseWattage += 10;
        if (this.currentBuild['Secondary Storage']) baseWattage += 10;
        if (this.currentBuild['CPU Cooler']) baseWattage += 15;
        if (this.currentBuild['Case Fan']) baseWattage += 5;
        
        return (cpuTdp + gpuTdp + baseWattage) * 1.2;
    }

    checkCompatibility(component, category) {
        let compatible = true;
        let reason = '';

        if (category === 'Processor') {
            if (this.currentBuild['Motherboard'] && component.socket !== this.currentBuild['Motherboard'].socket) {
                compatible = false; reason = 'Requires ' + this.currentBuild['Motherboard'].socket + ' Socket';
            }
            if (compatible && this.currentBuild['Power Supply']) {
                const requiredWattage = this.getRequiredWattage({ 'Processor': component });
                if (parseInt(this.currentBuild['Power Supply'].wattage) < requiredWattage) {
                    compatible = false; reason = 'PSU wattage too low (' + Math.ceil(requiredWattage) + 'W req)';
                }
            }
        } else if (category === 'Motherboard') {
            if (this.currentBuild['Processor'] && component.socket !== this.currentBuild['Processor'].socket) {
                compatible = false; reason = 'Requires ' + this.currentBuild['Processor'].socket + ' CPU';
            }
            if (compatible && this.currentBuild['Memory'] && component.supported_ram_gen !== this.currentBuild['Memory'].generation) {
                compatible = false; reason = 'Requires ' + this.currentBuild['Memory'].generation + ' RAM';
            }
            if (compatible && this.currentBuild['Case'] && parseInt(component.form_factor) > parseInt(this.currentBuild['Case'].max_mobo_size)) {
                compatible = false; reason = 'Too large for current Case';
            }
        } else if (category === 'Memory') {
            if (this.currentBuild['Motherboard'] && component.generation !== this.currentBuild['Motherboard'].supported_ram_gen) {
                compatible = false; reason = 'Requires ' + this.currentBuild['Motherboard'].supported_ram_gen;
            }
        } else if (category === 'Video Card') {
            if (this.currentBuild['Power Supply']) {
                const requiredWattage = this.getRequiredWattage({ 'Video Card': component });
                if (parseInt(this.currentBuild['Power Supply'].wattage) < requiredWattage) {
                    compatible = false; reason = 'PSU wattage too low (' + Math.ceil(requiredWattage) + 'W req)';
                }
            }
            if (compatible && this.currentBuild['Case']) {
                if (parseInt(component.length_mm) > parseInt(this.currentBuild['Case'].max_gpu_length)) {
                    compatible = false; reason = 'Too long for current Case';
                }
            }
        } else if (category === 'Case') {
            if (this.currentBuild['Motherboard'] && parseInt(component.max_mobo_size) < parseInt(this.currentBuild['Motherboard'].form_factor)) {
                compatible = false; reason = 'Does not fit motherboard';
            }
            if (compatible && this.currentBuild['Video Card']) {
                if (parseInt(this.currentBuild['Video Card'].length_mm) > parseInt(component.max_gpu_length)) {
                    compatible = false; reason = 'GPU is too long for this case';
                }
            }
        } else if (category === 'Power Supply') {
            const requiredWattage = this.getRequiredWattage({ 'Power Supply': component });
            if (parseInt(component.wattage) < requiredWattage) {
                compatible = false; reason = 'Requires at least ' + Math.ceil(requiredWattage) + 'W';
            }
        }

        return { compatible, reason };
    }

    getConflictsIfSelected(category, component) {
        let conflicts = [];
        
        if (category === 'Processor') {
            if (this.currentBuild['Motherboard'] && component.socket !== this.currentBuild['Motherboard'].socket) {
                conflicts.push('Motherboard');
                conflicts.push('Memory');
            }
            if (this.currentBuild['Power Supply']) {
                if (parseInt(this.currentBuild['Power Supply'].wattage) < this.getRequiredWattage({ 'Processor': component })) {
                    conflicts.push('Power Supply');
                }
            }
        } else if (category === 'Motherboard') {
            if (this.currentBuild['Processor'] && component.socket !== this.currentBuild['Processor'].socket) {
                conflicts.push('Processor');
            }
            if (this.currentBuild['Memory'] && component.supported_ram_gen !== this.currentBuild['Memory'].generation) {
                conflicts.push('Memory');
            }
            if (this.currentBuild['Case'] && parseInt(component.form_factor) > parseInt(this.currentBuild['Case'].max_mobo_size)) {
                conflicts.push('Case');
            }
        } else if (category === 'Video Card') {
            if (this.currentBuild['Power Supply']) {
                if (parseInt(this.currentBuild['Power Supply'].wattage) < this.getRequiredWattage({ 'Video Card': component })) {
                    conflicts.push('Power Supply');
                }
            }
            if (this.currentBuild['Case']) {
                if (parseInt(component.length_mm) > parseInt(this.currentBuild['Case'].max_gpu_length)) {
                    conflicts.push('Case');
                }
            }
        } else if (category === 'Case') {
            if (this.currentBuild['Motherboard'] && parseInt(component.max_mobo_size) < parseInt(this.currentBuild['Motherboard'].form_factor)) {
                conflicts.push('Motherboard');
            }
            if (this.currentBuild['Video Card']) {
                if (parseInt(this.currentBuild['Video Card'].length_mm) > parseInt(component.max_gpu_length)) {
                    conflicts.push('Video Card');
                }
            }
        }
        
        return conflicts;
    }

    getCartPayload() {
        return JSON.stringify(this.currentBuild);
    }
}
