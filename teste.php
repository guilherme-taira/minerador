<?php
require_once('vendor/autoload.php');

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;

$host = 'http://localhost:4444/wd/hub'; // URL do Selenium Server

// Função para extrair dados de __PRELOADED_STATE__
function extractPreloadedState($url, $driver) {
    // Abra a URL fornecida
    $driver->get($url);

    // Aguarde até que a página carregue completamente
    $driver->manage()->timeouts()->implicitlyWait(10);

    // Executa um script JavaScript para capturar o conteúdo da variável window.__PRELOADED_STATE__
    $jsonData = $driver->executeScript('return window.__PRELOADED_STATE__;');

    // Decodifica o JSON em um array PHP
    if ($jsonData) {
        echo "<pre>";
        print_r($jsonData['initialState']['components']['others_products']['products'][0]['title']['text']);
       
        // print_r($jsonData['initialState']['components']['available_quantity']['picker']['track']['melidata_event']['event_data']['quantity']);
        // print_r( $jsonData['initialState']['components']['available_quantity']['picker']['track']['melidata_event']['event_data']['seller_name']);
        echo "</pre>";
    } else {
        echo "A variável __PRELOADED_STATE__ não foi encontrada ou está vazia.";
    }
}

// Inicializa o WebDriver
$driver = RemoteWebDriver::create($host, DesiredCapabilities::chrome());

// URL da página onde está o __PRELOADED_STATE__
$initialUrl = 'https://www.mercadolivre.com.br/blocos-de-construco-magneticos-minecraft-64-pecas/p/MLB34945300?pdp_filters=item_id:MLB3787930983';

// Inicia o processo de extração
extractPreloadedState($initialUrl, $driver);

// Fecha o navegador
$driver->quit();



// [track] => Array
// (
//     [melidata_event] => Array
//         (
//             [event_data] => Array
//                 (
//                     [catalog_product_id] => MLB30219246
//                     [item_id] => MLB3715709937
//                     [page_type] => PDP
//                     [quantity] => 51
//                     [seller_id] => 624032308
//                     [seller_name] => D-AZ Iluminação
//                     [user_product_id] => N/A
//                 )

//             [path] => /pdp/quantity_picker_open



// [rating_average_formatted] => 4.8
// [should_hide_button_all_reviews] => 
// [stars] => 5
// [text] => 44 avaliações
// [track] => Array


// [seller_experiment] => Array
// (
// [id] => seller_experiment
// [is_exclusive_official_store] => 
// [official_store_icon] => https://http2.mlstatic.com/frontend-assets/vpp-frontend/cockade.png
// [seller] => Array
// (
// [id] => 624032308
// [name] => D-AZ Iluminação
// [official_store_id] => 4877
// [reputation_level] => 5_green
// )

// [seller_info] => Array
// (
// [extra_info] => Array
//     (
//     )

// [header] => Informação do vendedor
// [is_exclusive_official_store] => 
// [logo] => https://http2.mlstatic.com/D_NQ_NP_722150-MLA74841345386_032024-G.jpg
// [official_store_icon] => https://http2.mlstatic.com/frontend-assets/vpp-frontend/cockade.png
// [power_seller_status] => Array
//     (
//         [subtitle] => É um dos melhores do site!
//         [title] => MercadoLíder Platinum
//     )

// [state] => VISIBLE
// [store_type] => OFFICIAL_STORE
// [subtitles] => Array
//     (
//         [0] => Array
//             (
//                 [color] => GRAY
//                 [font_size] => SMALL
//                 [text] => Loja oficial no Mercado Livre
//             )

//     )

// [thermometer] => Array
//     (
//         [info] => Array
//             (
//                 [0] => Array
//                     (
//                         [subtitle] => Vendas concluídas
//                         [title] => +50mil
//                     )

//                 [1] => Array
//                     (
//                         [icon] => Array
//                             (
//                                 [id] => REP_SELLER_ATTENTION_GOOD
//                             )

//                         [subtitle] => Oferece um bom atendimento
//                     )

//                 [2] => Array
//                     (
//                         [icon] => Array
//                             (
//                                 [id] => REP_SELLER_DELIVERY_TIME_GOOD
//                             )

//                         [subtitle] => Entrega os produtos dentro do prazo
//                     )

//             )

//         [rank] => 5
//     )

// [title] => D-AZ Iluminação
// )

// [seller_link] => Array
// (
// [close_modal_label] => Fechar
// [duration] => 0
// [label] => Array
//     (
//         [color] => BLUE
//         [text] => D-AZ Iluminação
//     )

// [timeout] => 0
// )

// [show_seller_logo] => 1
// [state] => VISIBLE
// [subtitles] => Array
// (
// [0] => Array
//     (
//         [color] => BLACK
//         [font_family] => SEMIBOLD
//         [font_size] => XSMALL
//         [text] => +50mil vendas
//     )

// )

// [title] => Loja oficial
// [title_value] => D-AZ Iluminação
// [type] => seller_experiment
// )

// [seller_info] => Array
// (
// [id] => seller_info
// [state] => HIDDEN
// [type] => seller_info
// )

// [share] => Array
// (





// tarifa 

// [melidata_event] => Array
// (
// [event_data] => Array
// (
// [available_promotions] => Array
// (
// [0] => Array
// (
// [campaign_id] => P-MLB14283060
// [original_value] => 149.99
// [type] => marketplace_campaign
// [value] => 128.99
// )

// )

// [bank_discounts] => Array
// (
// )

// [best_seller_position] => 9
// [bo_pick_up_conditions] => no_bo_pick_up
// [cac_item] => 
// [cac_status] => after_dispatch
// [cart_content] => 1
// [catalog_parent_id] => MLB24202814
// [catalog_product_id] => MLB30219246
// [category_id] => MLB189195
// [collapsed_pickers] => 
// [compats_info] => Array
// (
// [status] => NOT_SUPPORTED
// )

// [credit_view_components] => Array
// (
// [pricing] => Array
// (
// [actual_price] => 128.99
// [discount] => 14%
// [original_price] => 149.99
// [unit_of_measurement] => not_apply
// [with_mercado_credito] => 
// )

// [tooltip] => Array
// (
// [visible] => 
// )

// [widget] => Array
// (
// [visible] => 
// )

// )



// FRETES

// [shipping_mode] => me2
// [shipping_promise] => Array
//     (
//         [address_options] => Array
//             (
//             )

//         [agency_options] => Array
//             (
//             )

//         [destination] => 05211120
//         [shipping_failure_message] => N/A
//         [showing_melitag] => 
//         [showing_promises] => Array
//             (
//             )

//         [showing_upselling] => 
//     )

// [showing_puis] => 
// [stock_type] => normal
// [store_pick_up] => 
// [subtitle_types] => Array
//     (
//         [0] => SOLD_QUANTITY
//     )

// [tags] => Array
//     (
//         [0] => good_quality_thumbnail
//         [1] => immediate_payment
//         [2] => catalog_boost
//         [3] => cart_eligible
//     )

// [user_product_id] => N/A
// [vertical] => core
// )