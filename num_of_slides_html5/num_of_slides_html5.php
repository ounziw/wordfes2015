<?php
/**
 * Plugin Name: Num of Slides html5
 * Version: 1.0
 * License: GPL ver.2 or later
 */

// プラグイン登録時の処理 -> なし
// アンインストール時の処理 -> なし

// 元プラグインのフィールドを消す
remove_action('admin_init', 'my_slider_field1', 15);

// フィールド表示を変えて登録する
function my_slider_field_html5() {

    add_settings_field(
        'num_of_slides', // フィールド名
        '枚数', // タイトル
        'num_of_slides_html5_callback_function', // コールバック関数。この関数の実行結果が出力される
        'my_slider_settings', // このフィールドを表示するページ名
        'my_slider_settings_section' // このフィールドを表示するセクション名
    );

    register_setting(
        'my_slider_settings-group', // グループ名
        'my_slider_settings_num_of_slides', // オプション名
        'my_slider_settings_num_of_slides_check' // 入力値をサニタイズする関数
    );
}
add_action('admin_init', 'my_slider_field_html5', 15);

function num_of_slides_html5_callback_function() {
    echo '<input name="my_slider_settings_num_of_slides" id="my_slider_settings_num_of_slides" type="range" min="1" max="9" value="';
    form_option( 'my_slider_settings_num_of_slides' ) ;
    echo '" />';
    echo '<input id="val1" type="text" size="2" value="';
    form_option( 'my_slider_settings_num_of_slides' ) ;
    echo '" />';
    echo <<<EOF
<script>
jQuery(function(){
    jQuery('#my_slider_settings_num_of_slides').change(function(){
        var value = jQuery(this).val();
        jQuery('#val1').val(value);
    });
    jQuery('#val1').change(function(){
        var value = jQuery(this).val();
        jQuery('#my_slider_settings_num_of_slides').val(value);
    });
});
</script>
EOF;
}