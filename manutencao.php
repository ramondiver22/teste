<?php
    $inicio = "";
    $final = "";
    
     if(!is_null($_POST['inicio'])) $inicio = $_POST['inicio'];
     if(!is_null($_POST['final'])) $final = $_POST['final'];
     $pagamentos = listarPagamentos(1, $inicio, $final);
     $depositos = $pagamentos->meta->total_value_cents / 100;
     $saldo = VerSaldo();
    
    include('tpl/dashboard/header.tpl');
    include('tpl/dashboard/manutencao.tpl');
    include('tpl/dashboard/footer.tpl');