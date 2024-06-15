<?php

function obterNivelDetritos($percentual) {
    if ($percentual > 75) { // Quanto maior a porcentagem, mais cheio está
        return 'Vazio';
    } elseif ($percentual >= 40 && $percentual <= 75) {
        return 'Precisa de Atenção';
    } elseif ($percentual < 40) {
        return 'Entupido';
    }
}