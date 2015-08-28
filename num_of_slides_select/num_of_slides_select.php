<?php
/**
 * Plugin Name: Num of Slides Select
 * Version: 1.0
 * License: GPL ver.2 or later
 */

// プラグイン登録時の処理 -> なし
// アンインストール時の処理 -> なし

// 元プラグインのフィールドを消す
remove_action('admin_init', 'my_slider_field1', 15);

// フィールド表示を変えて登録する
function my_slider_field_select() {

    add_settings_field(
        'num_of_slides', // フィールド名
        '枚数', // タイトル
        'num_of_slides_select_callback_function', // コールバック関数。この関数の実行結果が出力される
        'my_slider_settings', // このフィールドを表示するページ名
        'my_slider_settings_section' // このフィールドを表示するセクション名
    );

    register_setting(
        'my_slider_settings-group', // グループ名
        'my_slider_settings_num_of_slides', // オプション名
        'my_slider_settings_num_of_slides_check' // 入力値をサニタイズする関数
    );
}
add_action('admin_init', 'my_slider_field_select', 15);

function num_of_slides_select_callback_function() {
    echo '<select name="my_slider_settings_num_of_slides" id="my_slider_settings_num_of_slides">';
    $format = '<option value="%1$d" %2$s>%1$d</option>';
    for ( $i = 1; $i <= 9; $i++ ) {
        $select = selected( get_option( 'my_slider_settings_num_of_slides' ), $i, false );
        printf( $format, $i, $select );
    }
    echo '</select>';
}