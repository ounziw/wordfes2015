<?php
/**
 * Plugin Name: My Slider Settings
 * Version: 1.0
 * License: GPL ver.2 or later
 */

/**
 * テーマファイルに、下記のように書くとスライダーを出力する
 * <?php if ( function_exists( 'my_slider_settings_output' ) ) { my_slider_settings_output(); } ?>
 *
 */

// プラグイン登録時の処理
function my_slider_settings_activate() {
    // オプションの追加と初期値設定
    add_option('my_slider_settings_num_of_slides', 5, '', 'no' );
    add_option('my_slider_settings_random', 0, '', 'no' );
}
register_activation_hook( __FILE__, 'my_slider_settings_activate' );
// アンインストール時の処理は、uninstall.phpに記述

// スライダーを出力するコード
function my_slider_settings_output() {
    $filter_option = array(
        'options' => array(
            'min_range' => 1,
            'default' => 5,
        ),
    );
    $posts_per_page = filter_var( get_option( 'my_slider_settings_num_of_slides' ), FILTER_VALIDATE_INT, $filter_option ) ;
    $orderby = get_option( 'my_slider_settings_random' ) ? 'rand' : 'date';
    $args = array( 
        'post_type' => 'myslidersettings',
        'posts_per_page' => $posts_per_page,
        'orderby' => $orderby
    );
    $my_slider_posts = get_posts( $args );
    echo '<ul class="bxslider">';
    
    foreach ( $my_slider_posts as $post ) {
        setup_postdata( $post );
        $imgtitle = the_title_attribute( array( 'echo' => 0, 'post' => $post->ID ) );
        $thumnailid = get_post_thumbnail_id( $post->ID );
        echo '<li>';
        echo wp_get_attachment_image( $thumnailid, 'large', false, array( 'title' => $imgtitle ) );
        echo '</li>';
    }
    wp_reset_postdata();
    
    echo '</ul>';
    echo "<script>jQuery('.bxslider').bxSlider({
    mode: 'fade',
    adaptiveHeight: true,
    captions: true
});</script>";
}

//  スライダーで使用するcssとjsの登録
function my_slider_settings_scripts() {
    wp_enqueue_style( 'my_slider_settings_bxslider_css', plugins_url( 'css/jquery.bxslider.css' , __FILE__ ) );
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'my_slider_settings_bxslider_js', plugins_url( 'js/jquery.bxslider.js' , __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'my_slider_settings_scripts' );


// カスタム投稿の登録
function my_slider_settings_post_type() {
    register_post_type( 'myslidersettings', array(
        'label' => 'スライダー', // 表示名
        'show_ui' => true, // 管理画面でUIを表示する
        'supports' => array( 'title', 'thumbnail' ), // タイトルとアイキャッチ画像を有効にする
    ) );
}
add_action( 'init', 'my_slider_settings_post_type', 1 );


//  ここからSettings API

// 設定画面を追加する
function my_slider_settings_menu() {
    add_options_page(
        'スライダー詳細設定', // ページのタイトル
        'スライダー詳細設定', // メニューのタイトル
        'manage_options', // このページを操作する権限
        'my_slider_settings', // ページ名
        'my_slider_settings_plugin_options' // コールバック関数。この関数の実行結果が出力される
    );
}
add_action('admin_menu', 'my_slider_settings_menu');

// フォームの枠組を出力する
function my_slider_settings_plugin_options() {
?>
    <div class="wrap">
        <form action="options.php" method="post">
<?php do_settings_sections('my_slider_settings'); // ページ名 ?>
<?php settings_fields('my_slider_settings-group'); // グループ名 ?>
<?php submit_button(); ?>
        </form>
    </div>
<?php
}



// セクションの作成
function my_slider_settings_init() {
    add_settings_section(
        'my_slider_settings_section', // セクション名
        'スライダーの設定', // タイトル
        'my_slider_settings_section_callback_function', // コールバック関数。この関数の実行結果が出力される
        'my_slider_settings' // このセクションを表示するページ名。do_settings_sectionsで設定
    );
}
add_action('admin_init', 'my_slider_settings_init');

function my_slider_settings_section_callback_function() {
    echo '<p>スライダーの詳細設定を行います</p>';
}




// フィールドの追加1
function my_slider_field1() {

    add_settings_field(
        'num_of_slides', // フィールド名
        '枚数', // タイトル
        'num_of_slides_callback_function', // コールバック関数。この関数の実行結果が出力される
        'my_slider_settings', // このフィールドを表示するページ名。do_settings_sectionsで設定
        'my_slider_settings_section' // このフィールドを表示するセクション名。add_settings_sectionで設定
    );

    register_setting(
        'my_slider_settings-group', // グループ名。settings_fieldsで設定
        'my_slider_settings_num_of_slides', // オプション名
        'my_slider_settings_num_of_slides_check' // 入力値をサニタイズする関数
    );
}
add_action('admin_init', 'my_slider_field1', 15);

// フォーム項目を表示する
function num_of_slides_callback_function() {
    echo '<input name="my_slider_settings_num_of_slides" id="my_slider_settings_num_of_slides" type="text" value="';
    form_option( 'my_slider_settings_num_of_slides' );
    echo '" />';
}

// 入力値「スライド枚数」を検証する
// 必要に応じてエラーメッセージを出す
function my_slider_settings_num_of_slides_check( $input ) {
    $filter_option = array(
        'options' => array(
            'min_range' => 1,
        ),
    );
    if ( filter_var( $input, FILTER_VALIDATE_INT, $filter_option) ) {
        return $input;
    } else {
        add_settings_error(
            'my_slider_settings',
            'invalid_num',
            '枚数: ' . intval( $filter_option['options']['min_range'] ) . ' 以上の数字を指定してください。',
            'error'
        );
        return intval( get_option( 'my_slider_settings_num_of_slides' ) );
    }
}



// フィールドの追加2
function my_slider_field2() {

    add_settings_field(
        'random', // フィールド名
        'ランダム表示', // タイトル
        'random_callback_function', // コールバック関数。この関数の実行結果が出力される
        'my_slider_settings', // このフィールドを表示するページ名。do_settings_sectionsで設定
        'my_slider_settings_section' // このフィールドを表示するセクション名。add_settings_sectionで設定
    );

    register_setting(
        'my_slider_settings-group', // グループ名。settings_fieldsで設定
        'my_slider_settings_random', // オプション名
        'intval' // 入力値をサニタイズする関数
    );
}
add_action('admin_init', 'my_slider_field2', 25);

// フォーム項目を表示する
function random_callback_function() {
    echo '<input name="my_slider_settings_random" id="my_slider_settings_random" type="checkbox" value="1" ';
    checked( 1, get_option( 'my_slider_settings_random' ) ) ;
    echo ' />';
    echo 'ランダム表示する';
}