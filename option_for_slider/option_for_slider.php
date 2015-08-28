<?php
/**
 * Plugin Name: Option for slider
 * Version: 1.0
 * License: GPL ver.2 or later
 */

// プラグイン登録時の処理
function option_for_slider_settings_activate() {
    // オプションの追加と初期値設定
    add_option('my_slider_settings_another', '', '', 'no' );
}
register_activation_hook( __FILE__, 'option_for_slider_settings_activate' );
// アンインストール時の処理は、uninstall.phpに記述

// my_slider_settings管理画面に、オプション項目を追加する
function option_for_slider_init() {

    add_settings_field(
        'another', // フィールド名
        'URL', // タイトル
        'another_callback_function', // コールバック関数。この関数の実行結果が出力される
        'my_slider_settings', // このフィールドを表示するページ名
        'my_slider_settings_section' // このフィールドを表示するセクション名
    );

    register_setting(
        'my_slider_settings-group', // グループ名
        'my_slider_settings_another', // オプション名
        'my_slider_settings_another_check' // 入力値をサニタイズする関数
    );
}
add_action('admin_init', 'option_for_slider_init', 30); // フック順序を変更すると、表示順が変えられる


function another_callback_function() {
    echo '<input name="my_slider_settings_another" id="my_slider_settings_another" type="text" value="';
    form_option( 'my_slider_settings_another' ) ;
    echo '" />';
}

// URLのチェック
// 必要に応じてエラーメッセージを出す
function my_slider_settings_another_check( $input ) {

    if ( filter_var( $input, FILTER_VALIDATE_URL ) ) {
        return $input;
    } else {
        add_settings_error(
            'my_slider_settings',
            'invalid_url',
            'URL: 妥当なURLを指定してください。',
            'error'
        );
        return esc_attr( get_option( 'my_slider_settings_another' ) );
    }
}