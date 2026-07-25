<?php

return [
    'statusStyles' => [
        'Building'  => ['pill' => 'bg-nexora-warning/80 text-nexora-off-white', 'dot' => 'bg-nexora-warning'],
        'QC Check'  => ['pill' => 'bg-nexora-info/80 text-nexora-off-white',    'dot' => 'bg-nexora-info'],
        'Cancelled' => ['pill' => 'bg-nexora-gray/80 text-nexora-off-white',    'dot' => 'bg-nexora-gray'],
        'Pending'   => ['pill' => 'bg-nexora-danger/80 text-nexora-off-white',  'dot' => 'bg-nexora-danger'],
        'Finished'  => ['pill' => 'bg-nexora-success/80 text-nexora-off-white', 'dot' => 'bg-nexora-success'],
        'Completed' => ['pill' => 'bg-nexora-success/80 text-nexora-off-white', 'dot' => 'bg-nexora-success'],
        'Rework'    => ['pill' => 'bg-nexora-warning/80 text-nexora-off-white', 'dot' => 'bg-nexora-warning'],
    ],

    'partStyles' => [
        'Ready'    => ['dot' => 'bg-nexora-success', 'text' => 'text-nexora-success'],
        'Sourcing' => ['dot' => 'bg-nexora-warning', 'text' => 'text-nexora-warning'],
        'Missing'  => ['dot' => 'bg-nexora-danger',  'text' => 'text-nexora-danger'],
    ],

    'rangeStyles' => [
        'high-end'  => 'bg-purple-500/80 text-white',
        'mid-range' => 'bg-nexora-info/80 text-white',
        'budget'    => 'bg-nexora-warning/80 text-white',
        'office'    => 'bg-nexora-slate-500/80 text-white',
    ],

    'benchmarkTargets' => [
        'HE' => [
            'CPU_cinebench'  => ['name' => 'Cinebench R23 Multicore', 'tool' => 'Cinebench R23', 'target' => 30000, 'operator' => '>=', 'unit' => 'pts'],
            'CPU_temp'       => ['name' => 'CPU Temp Under Load',      'tool' => 'HWiNFO64',      'target' => 85,    'operator' => '<=', 'unit' => '°C'],
            'GPU_3dmark'     => ['name' => '3DMark TimeSpy',           'tool' => '3DMark',         'target' => 18000, 'operator' => '>=', 'unit' => 'pts'],
            'GPU_temp'       => ['name' => 'GPU Temp Under Load',      'tool' => 'HWiNFO64',      'target' => 85,    'operator' => '<=', 'unit' => '°C'],
            'RAM_speed'      => ['name' => 'RAM Speed (XMP/EXPO)',     'tool' => 'CPU-Z',          'target' => 5600,  'operator' => '>=', 'unit' => 'MT/s'],
            'Storage_read'   => ['name' => 'NVMe Sequential Read',     'tool' => 'CrystalDiskMark','target' => 6500,  'operator' => '>=', 'unit' => 'MB/s'],
            'Storage_write'  => ['name' => 'NVMe Sequential Write',    'tool' => 'CrystalDiskMark','target' => 5000,  'operator' => '>=', 'unit' => 'MB/s'],
            'System_post'    => ['name' => 'POST & Boot Sequence',     'tool' => 'Visual',         'target' => 1,     'operator' => '=',  'unit' => 'pass'],
            'System_cables'  => ['name' => 'Cable Management Check',   'tool' => 'Visual',         'target' => 1,     'operator' => '=',  'unit' => 'pass'],
        ],
        'MR' => [
            'CPU_cinebench'  => ['name' => 'Cinebench R23 Multicore', 'tool' => 'Cinebench R23', 'target' => 18000, 'operator' => '>=', 'unit' => 'pts'],
            'CPU_temp'       => ['name' => 'CPU Temp Under Load',      'tool' => 'HWiNFO64',      'target' => 85,    'operator' => '<=', 'unit' => '°C'],
            'GPU_3dmark'     => ['name' => '3DMark TimeSpy',           'tool' => '3DMark',         'target' => 10000, 'operator' => '>=', 'unit' => 'pts'],
            'GPU_temp'       => ['name' => 'GPU Temp Under Load',      'tool' => 'HWiNFO64',      'target' => 85,    'operator' => '<=', 'unit' => '°C'],
            'RAM_speed'      => ['name' => 'RAM Speed (XMP/EXPO)',     'tool' => 'CPU-Z',          'target' => 3600,  'operator' => '>=', 'unit' => 'MT/s'],
            'Storage_read'   => ['name' => 'NVMe Sequential Read',     'tool' => 'CrystalDiskMark','target' => 3500,  'operator' => '>=', 'unit' => 'MB/s'],
            'System_post'    => ['name' => 'POST & Boot Sequence',     'tool' => 'Visual',         'target' => 1,     'operator' => '=',  'unit' => 'pass'],
            'System_cables'  => ['name' => 'Cable Management Check',   'tool' => 'Visual',         'target' => 1,     'operator' => '=',  'unit' => 'pass'],
        ],
        'BU' => [
            'CPU_cinebench'  => ['name' => 'Cinebench R23 Multicore', 'tool' => 'Cinebench R23', 'target' => 8000,  'operator' => '>=', 'unit' => 'pts'],
            'CPU_temp'       => ['name' => 'CPU Temp Under Load',      'tool' => 'HWiNFO64',      'target' => 90,    'operator' => '<=', 'unit' => '°C'],
            'GPU_3dmark'     => ['name' => '3DMark Night Raid',        'tool' => '3DMark',         'target' => 15000, 'operator' => '>=', 'unit' => 'pts'],
            'RAM_speed'      => ['name' => 'RAM Speed',                'tool' => 'CPU-Z',          'target' => 3200,  'operator' => '>=', 'unit' => 'MT/s'],
            'Storage_read'   => ['name' => 'Storage Read Speed',       'tool' => 'CrystalDiskMark','target' => 400,   'operator' => '>=', 'unit' => 'MB/s'],
            'System_post'    => ['name' => 'POST & Boot Sequence',     'tool' => 'Visual',         'target' => 1,     'operator' => '=',  'unit' => 'pass'],
        ],
        'OF' => [
            'CPU_cinebench'  => ['name' => 'Cinebench R23 Multicore', 'tool' => 'Cinebench R23', 'target' => 3000,  'operator' => '>=', 'unit' => 'pts'],
            'CPU_temp'       => ['name' => 'CPU Temp Under Load',      'tool' => 'HWiNFO64',      'target' => 90,    'operator' => '<=', 'unit' => '°C'],
            'Storage_read'   => ['name' => 'Storage Read Speed',       'tool' => 'CrystalDiskMark','target' => 300,   'operator' => '>=', 'unit' => 'MB/s'],
            'System_post'    => ['name' => 'POST & Boot Sequence',     'tool' => 'Visual',         'target' => 1,     'operator' => '=',  'unit' => 'pass'],
        ],
    ],
];
