<?php

if (!function_exists('hasReplicado')) {

    /** 
     * verifica se replicado está disponível
     */
    function hasReplicado()
    {
        return \class_exists('Uspdev\\Replicado\\Pessoa') ? true : false;
    }
}
