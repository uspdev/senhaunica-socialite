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

if (!function_exists('hasUspTheme')) {

    /** 
     * verifica se replicado está disponível
     */
    function hasUspTheme()
    {
        return \class_exists('Uspdev\\UspTheme\\UspTheme') ? true : false;
    }
}