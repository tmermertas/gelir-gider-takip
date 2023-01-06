<?php
/*
* Plugin Name: Gelir Gider Takip
* Plugin URI: 
* Description: Bu eklenti, Google Sheets dosyasındaki verileri çeker ve WordPress sitesinde görüntüler.
* Version: 1.0
* Author: Tugrul Mermertas
* Author URI: 
*/

function ggt_register_settings() {
  // Eklentinin ayarlarını ana menüde en üstte gösterin
  add_menu_page(
    'Gelir Gider Takip Ayarları',
    'Gelir Gider Takip',
    'manage_options',
    'ggt-settings',
    'ggt_settings_page',
    'dashicons-chart-bar',
    1
  );
}
add_action('admin_menu', 'ggt_register_settings');



function ggt_settings_page() {
  // Ayarlar sayfasının HTML kodunu buraya yazın
  ?>
  <div class="wrap">
    <h1>Gelir Gider Takip Ayarları</h1>
    <form method="post" action="options.php">
      <?php
      // Ayarları kaydetmek için gerekli olan kodlar
      settings_fields('ggt_options_group');
      do_settings_sections('ggt-settings');
      submit_button();
      ?>
    </form>
    
    <!-- Kullanma kılavuzunu ekleyin -->
    <h2>Kullanma Kılavuzu</h2>
    <p>Bu eklenti, Google Sheets dosyasındaki verileri çeker ve WordPress sitesinde görüntüler. Bu sayede, gelirlerinizi ve giderlerinizi takip edebilirsiniz.</p>
    <h3>Kurulum</h3>
    <ol>
      <li>Eklentiyi indirin ve WordPress sitesine yükleyin.</li>
      <li>Eklentiyi etkinleştirin.</li>
      <li>Eklentinin ayarlarına gidin ve Google Sheets dosyasının URL'sini yapıştırın.</li>
    </ol>
    <h3>Kullanım</h3>
    <p>Verileri görüntülemek için, bir WordPress sayfasında veya gönderisinde <code>[ggt]</code> kısayolunu kullanın. Örneğin:</p>
    <pre>
Bu ayın gelirleri ve giderleri aşağıdaki gibidir:

[ggt]
    </pre>
    <p>Eklentinin çalışması için, "Bağlantıları Yükle" iznini etkinleştirin. Ayarlar &gt; Güvenlik &gt; Bağlantıları Yükle bölümünden bu izni etkinleştirebilirsiniz.</p>
    <h3>Önemli Notlar</h3>
    <ul>
  <li>Google Sheets dosyasında verilerinizin düzgün bir şekilde saklanmasını sağlayın. Örneğin, ilk satırda sütun başlıklarınız olmalıdır.</li>
  <li>Google Sheets dosyası paylaşılmış bir URL'si olmalıdır. Bu URL, dosyanın "Paylaşım Ayarları" bölümünde bulunur.</li>
    </ul>
  </div>
  <?php
}

function ggt_register_options() {
  // Eklentinin ayarları için bir sekme oluşturun
  register_setting(
    'ggt_options_group',
    'ggt_options',
    'ggt_callback'
  );

  add_settings_section(
    'ggt_section_id',
    'Google Sheets Ayarları',
    'ggt_section_callback',
    'ggt-settings'
  );

  add_settings_field(
    'ggt_field_url',
    'Google Sheets URL',
    'ggt_field_url_callback',
    'ggt-settings',
    'ggt_section_id'
  );
}
add_action('admin_init', 'ggt_register_options');

function ggt_section_callback() {
  echo 'Google Sheets dosyasının URL\'sini girin:';
}

function ggt_field_url_callback() {
  $options = get_option('ggt_options');
  $url = '';
  if (isset($options['url'])) {
    $url = $options['url'];
  }
  echo '<input type="text" name="ggt_options[url]" value="' . $url . '" style="width: 75%;" />';
}

function ggt_callback($input) {
  $new_input = array();
  if (isset($input['url'])) {
    $new_input['url'] = sanitize_text_field($input['url']);
  }
  return $new_input;
}

function ggt_shortcode() {
  // Google Sheets dosyasına bağlanıp verileri çekin
  $options = get_option('ggt_options');
  $url = $options['url'];

  $response = wp_remote_get($url);
  $body = wp_remote_retrieve_body($response);

  // Verileri işleyin ve görüntüleyin
  $data = json_decode($body);
  $output = '<table>';
  foreach ($data as $row) {
    $output .= '<tr>';
    foreach ($row as $cell) {
     $output .= '<td>' . $cell . '</td>';
    }
    $output .= '</tr>';
  }
  $output .= '</table>';

  return $output;
}
add_shortcode('ggt', 'ggt_shortcode');