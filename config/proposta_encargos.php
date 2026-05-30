<?php

return [
    [
        'key' => 'bdi',
        'label' => 'BDI',
        'descricao' => 'Bonificação e Despesas Indiretas',
        'default_percent' => 25.0,
        'default_ativo' => true,
    ],
    [
        'key' => 'encargos_sociais',
        'label' => 'Encargos Sociais',
        'descricao' => 'Encargos sociais sobre mão de obra',
        'default_percent' => 0,
        'default_ativo' => false,
    ],
    [
        'key' => 'lucro',
        'label' => 'Lucro',
        'descricao' => 'Margem de lucro',
        'default_percent' => 0,
        'default_ativo' => false,
    ],
    [
        'key' => 'iss',
        'label' => 'ISS',
        'descricao' => 'Imposto Sobre Serviços',
        'default_percent' => 5.0,
        'default_ativo' => false,
    ],
    [
        'key' => 'desconto',
        'label' => 'Desconto',
        'descricao' => 'Desconto comercial (reduz o total)',
        'default_percent' => 0,
        'default_ativo' => false,
        'subtrai' => true,
    ],
];
