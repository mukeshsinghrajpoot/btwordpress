<?php
add_action('admin_head', 'my_custom_fonts');
function my_custom_fonts() 
{
  echo '<style>#adminmenu .wp-menu-image img {opacity: 2.6;width: 20px;height: 20px;}</style>';
}
add_action('admin_menu', 'my_menu_betstodaypost');
function my_menu_betstodaypost(){
    add_menu_page(
      'betstoday post',  
      'betstodaypost Add',
      'manage_options',
      'betstodaypost',
      'betstodaypostadd',
      plugins_url('/image/logo.png', __FILE__),6
      );
      add_submenu_page('betstodaypost', 'View betstodaypost', 'View betstodaypost', 'manage_options', 'betstodaypost-view','betstodaypostview');
      add_submenu_page('betstodaypost', 'Add Header Title', 'Add Header Title', 'manage_options', 'headertitle','headertitle');
      add_submenu_page('betstodaypost', 'View Header Title', 'View title', 'manage_options', 'view-title','viewtitle');
      add_submenu_page('null', 'betstodaypostedit', 'betstodaypostedit', 'manage_options', 'betstodaypostedit','betstodaypostedit');
      add_submenu_page('null', 'title edit', 'titleedit', 'manage_options', 'titleedit','titleedit');
}
include(plugin_dir_path(__FILE__) . '/titlesection.php');
function betstodaypostadd()
{
wp_enqueue_style( 'fetureaddcss', plugins_url( '/bootstrap/css/fetureadd.css', __FILE__ ) ); 
wp_enqueue_script( "fetureaddajax", plugin_dir_url( __FILE__ ) . '/bootstrap/js/fetureaddajax.js', 'jQuery','1.0.0', true);
wp_localize_script( 'fetureaddajax', 'fetureaddajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<div class="roadmap-header-wrraper">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="roadmap-header-section">
          <div class="roadmap-logoleft">
            <div class="roadmap-logo">
              <h2>betstoday post</h2>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>    
<div class="admin-upload-wrraper">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="admin-upload-section">
          <form id="post-form" method="post" enctype="multipart/form-data">
            <div class="uploadsubmit-heading">
              <h2>Submit New matchup</h2>
              <div id="result"></div>
            </div>
            <div class="request-title">
              <label>Enter matchup</label>
              <input type="text" name="matchup" id="matchup" required>
            </div>
                        <div class="choose-imgsecton">
                        <label for="contentcetegory">Choose a oddsoption:</label>
                        <select id="oddsoption" id="contentcetegory" name="contentcetegory" required>
                          <option value="0">Open</option>
                          <option value="1">Close</option>
                        </select>
                        </div>
              <div class="request-title">
              <label>Enter odds</label>
              <input type="text" name="odds" id="odds" required>
            </div>
            <div class="request-title">
              <label>Enter odds Links</label>
              <input type="text" name="links" id="links" required>
            </div>
            <div class="request-title">
              <label>Enter result</label>
              <input type="text" name="result1" id="result1" required>
            </div>
            <div class="request-title">
              <label>Enter match date</label>
              <input type="date" id="matchdate" name="matchdate">
            </div>
            <div class="upload-submitbtn">
              <button id="submit">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php   
}
function feture_key_responce()
{
$matchup=$_POST['matchup'];
$oddsoption=$_POST['oddsoption'];
$odds=$_POST['odds'];
$links=$_POST['links'];
$result1=$_POST['result1'];
$matchdate=$_POST['matchdate'];
   global $wpdb;
   $last_update=date("Y-m-d H:i:s");
  $table_name = $wpdb->prefix . 'betstodaypost';
  $sss = $wpdb->insert($table_name, array('matchup' => $matchup, 'oddsoption' => $oddsoption, 'odds' => $odds,'links' => $links, 'result' => $result1, 'matchdate' => $matchdate,'last_update'=>$last_update),array( '%s', '%s', '%s', '%s', '%s', '%s'));
    if($sss)
    { echo "Done";}
    else 
    { 
    echo "Not Done";
    }
wp_die();
}
add_action('wp_ajax_feture_key_responce', 'feture_key_responce');
add_action('wp_ajax_nopriv_feture_key_responce', 'feture_key_responce'); 
/*export*/
function myplugin_get_data() {
  global $wpdb;
  $table_name = $wpdb->prefix . 'betstodaypost';
  $results = $wpdb->get_results( "SELECT * from $table_name");
  $data = array();
  foreach ($results as $result) {
    $data[] = array(
      $result->match_id,
      $result->matchup,
      $result->oddsoption,
      $result->odds,
      $result->links,
      $result->result,
      $result->matchdate,
      $result->last_update
    );
  }

  return $data;
}

function myplugin_format_csv($data) {
  $output = fopen('php://temp', 'w');

  fputcsv($output, array('Id', 'matchup', 'oddsoption','odds','links','result','matchdate','last update'));

  foreach ($data as $row) {
    fputcsv($output, $row);
  }

  rewind($output);

  return stream_get_contents($output);
}

function myplugin_export_data() {
  // Retrieve data to be exported
  $data = myplugin_get_data();

  // Format data as CSV string
  $csv = myplugin_format_csv($data);

  // Set headers for CSV download
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename=data.csv');

  // Send CSV data to browser for download
  echo $csv;

  // Stop WordPress from printing any additional output
  exit();
}

add_action('admin_post_myplugin_export_data', 'myplugin_export_data');
function myplugin_export_data_button() {
  ?>
  <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
    <input type="hidden" name="action" value="myplugin_export_data">
    <input class="export-customerbtn" type="submit" value="Export Data">
  </form>
  <?php
}
/*close export*/
function betstodaypostview()
{
  wp_enqueue_style( 'fetureaddcss', plugins_url( '/bootstrap/css/fetureadd.css', __FILE__ ) );
?>
<link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/betstodaypost/admin/new/css/bootstrap.min.css" rel="stylesheet" />
<script type='text/javascript' src='<?php echo WP_PLUGIN_URL; ?>/betstodaypost/admin/new/js/myjs.js'></script>
<?php  
global $wpdb;
$id=$_GET['id']??'';
$chk1=$_POST['checked_id']??'';
if (is_array($chk1) || is_object($chk1))
{
  $table_name = $wpdb->prefix . 'betstodaypost';
    foreach ($chk1 as $id2)
    {
        $del=$wpdb->query(
                "DELETE FROM $table_name
                WHERE match_id = $id2"
        );
    }
    if( $del==1) {
  echo  '<br><br><div class="containe"><div class="row"><div class="col-sm-8"><div class="alert alert-success"><strong>Delete data!</strong>  successfully.</div></div></div></div>';
      }
}
?>
<div class="container">
  <h2>View Data</h2>
  <?php myplugin_export_data_button(); ?>
  <form  action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post"/>
  <button type="submit" class="btn btn-danger" name="submit">DELETE SELECT DATA</button>
  <br>
  <br>       
  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th><input type="checkbox" id="select_all" value=""/></th>
        <th>Id</th>
        <th>matchup</th>
        <th>oddsoption</th>
        <th>odds</th>
        <th>Links</th>
        <th>result</th>
        <th>matchdate</th>
        <th>last update</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
        <?php 
        global $wpdb;
        $table_name = $wpdb->prefix . 'betstodaypost';
        $pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;      
        $limit = 5; // number of rows in page
        $offset = ( $pagenum - 1 ) * $limit;
        $total = $wpdb->get_var( "select count(*) as total from $table_name" );
        $num_of_pages = ceil( $total / $limit );
        $rows = $wpdb->get_results( "SELECT * from $table_name ORDER BY `match_id` DESC limit  $offset, $limit" );
        $rowcount = $wpdb->num_rows;
        if($rowcount>0){
        foreach ($rows as $row) {
            $id=$row->match_id ;
            $matchup= $row->matchup;
            $oddsoption= $row->oddsoption;
            $odds= $row->odds;
            $links= $row->links;
            $result= $row->result;
            $matchdate= $row->matchdate;
            $last_update= $row->last_update;
      ?>
      <tr>
        <td><input type="checkbox" name="checked_id[]" class="checkbox" value="<?php  echo $id; ?>"></td>
        <td><?php echo $id; ?></td>
        <td><?php echo $matchup; ?></td>
        <td><?php if($oddsoption==0){echo "Open";}if($oddsoption==1){echo "Close";}?></td>
        <td><?php echo $odds; ?></td>
        <td><?php echo $links; ?></td>
        <td><?php echo $result; ?></td>
        <td><?php echo $matchdate; ?></td>
        <td><?php echo $last_update; ?></td>
        <td><a href="<?php echo admin_url('admin.php?page=betstodaypostedit&id=' . $id); ?>" class="btn btn-info">Edit</a></td>
      </tr>
  <?php }}else{
    echo "<tr><td style='text-align: center;font-size: 27px;color: red;' colspan='7'>No records found</td></tr>";}?>
    </tbody>
  </table>
  <?php
        $page_links = paginate_links( array(
            'base' => add_query_arg( 'pagenum', '%#%' ),
            'format' => '',
            'prev_text' => __( 'Back', 'text-domain' ),
            'next_text' => __( 'Next', 'text-domain' ),
            'total' => $num_of_pages,
            'current' => $pagenum
        ) );

        if ( $page_links ) {
            echo '<div class="tablenav" style="width: 99%;"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
        }
        ?>
  <button type="submit" class="btn btn-danger" name="submit">DELETE SELECT DATA</button>
</form>
</div>
<?php
}
function betstodaypostedit()
{
wp_enqueue_style( 'fetureaddcss', plugins_url( '/bootstrap/css/fetureadd.css', __FILE__ ) ); 
wp_enqueue_script( "fetureaddeditajax", plugin_dir_url( __FILE__ ) . '/bootstrap/js/fetureaddeditajax.js', 'jQuery','1.0.0', true);
wp_localize_script( 'fetureaddeditajax', 'fetureaddeditajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<div class="roadmap-header-wrraper">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="roadmap-header-section">
          <div class="roadmap-logoleft">
            <div class="roadmap-logo">
              <h2>betstoday post Edit</h2>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>    
<div class="admin-upload-wrraper">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <?php $id=$_GET['id']??'';
            if($id)
            {
              global $wpdb;
              $table_name = $wpdb->prefix . 'betstodaypost';
              $results=$wpdb->get_results("SELECT * FROM $table_name WHERE match_id = $id");
              foreach($results as $row)
              {
                $match_id=$row->match_id;
                $matchup=$row->matchup;
                $oddsoption=$row->oddsoption;
                $odds=$row->odds;
                $links=$row->links;
                $result=$row->result;
                $matchdate=$row->matchdate;
              }
            } 
            ?>
        <div class="admin-upload-section">
          <form id="post-form" method="post" enctype="multipart/form-data">
            <div class="uploadsubmit-heading">
              <h2>Edit New matchup</h2>
              <div id="result"></div>
            </div>
            <div class="request-title">
              <label>Enter matchup</label>
              <input type="text" name="matchup" id="matchup" value="<?php echo $matchup; ?>">
              <input type="hidden" name="match_id" id="match_id" value="<?php echo $match_id; ?>">
            </div>
                        <div class="choose-imgsecton">
                        <label for="contentcetegory">Choose a oddsoption:</label>
                        <select id="oddsoption" id="contentcetegory" name="contentcetegory" required>
                          <option value="0"<?php if($oddsoption == '0') { ?> selected="selected"<?php } ?>>Open</option>
                          <option value="1"<?php if($oddsoption == '1') { ?> selected="selected"<?php } ?>>Close</option>
                        </select>
                        </div>
              <div class="request-title">
              <label>Enter odds</label>
              <input type="text" name="odds" id="odds" value="<?php echo $odds; ?>">
            </div>
            <div class="request-title">
              <label>Enter odds Links</label>
              <input type="text" name="links" id="links" value="<?php echo $links; ?>">
            </div>
            <div class="request-title">
              <label>Enter result</label>
              <input type="text" name="result1" id="result1" value="<?php echo $result; ?>">
            </div>
            <div class="request-title">
              <label>Enter match date</label>
              <input type="date" id="matchdate" name="matchdate" value="<?php echo $matchdate;  ?>">
            </div>
            <div class="upload-submitbtn">
              <button id="submit">Edit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php   
}
function feture_key_editresponce()
{
$match_id=$_POST['match_id'];  
$matchup=$_POST['matchup'];
$oddsoption=$_POST['oddsoption'];
$odds=$_POST['odds'];
$links=$_POST['links'];
$result1=$_POST['result1'];
$matchdate=$_POST['matchdate'];
   global $wpdb;
   $last_update=date("Y-m-d H:i:s");
  $table_name = $wpdb->prefix . 'betstodaypost';
  $sss=$wpdb->update($table_name, array('match_id'=>$match_id, 'matchup'=>$matchup, 'oddsoption'=>$oddsoption,'odds'=>$odds,'links'=>$links,'result'=>$result1,'matchdate'=>$matchdate,'last_update'=>$last_update), array('match_id'=>$match_id));
    if($sss)
    { echo "Done";}
    else 
    { 
    echo "Not Upadte data";
    }
wp_die();
}
add_action('wp_ajax_feture_key_editresponce', 'feture_key_editresponce');
add_action('wp_ajax_nopriv_feture_key_editresponce', 'feture_key_editresponce');
add_shortcode( 'betsstodaypost','betsstoday');
function betsstoday() 
{  
wp_enqueue_style( 'fetureaddcss', plugins_url( '/bootstrap/css/fetureadd.css', __FILE__ ) );  
wp_enqueue_style( 'mycustom', plugins_url( '/bootstrap/css/mycustom.css', __FILE__ ) );
?>
<link type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
  <?php 
        global $wpdb;
        $betstitle = $wpdb->prefix . 'betstitle';
        $datarow = $wpdb->get_results( "SELECT * from $betstitle" );
        $today=$_POST['today']??'';
        $yesturday=$_POST['yesturday']??'';
        $table_name = $wpdb->prefix . 'betstodaypost';
        $pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;      
        $limit = 10; // number of rows in page
        $offset = ( $pagenum - 1 ) * $limit;
        if($today)
        {
         $total = $wpdb->get_var( "select count(*) as total from $table_name" );
         $num_of_pages = ceil( $total / $limit );
          $rows1 = $wpdb->get_results( "SELECT * from $table_name  ORDER BY `match_id` DESC limit  $offset, $limit" );
          $rowcount1 = $wpdb->num_rows;
        }elseif($yesturday)
        {
          $total = $wpdb->get_var( "select count(*) as total from $table_name where matchdate='".$yesturday."'" );
          $num_of_pages = ceil( $total / $limit );
          $rows1 = $wpdb->get_results( "SELECT * from $table_name where matchdate='".$yesturday."' ORDER BY `match_id` DESC limit  $offset, $limit" );
          $rowcount1 = $wpdb->num_rows;
        }else
        {
          $total = $wpdb->get_var( "select count(*) as total from $table_name" );
          $num_of_pages = ceil( $total / $limit );
          $rows1 = $wpdb->get_results( "SELECT * from $table_name ORDER BY `match_id` DESC limit  $offset, $limit" );
          $rowcount1 = $wpdb->num_rows;
        }
  ?>
<div class="table-responsive" style="margin-top: -18px;border:2px solid #ababab">
  <table class="table  table-bordered" style="width:100%;margin-top:0px;background:#fefef6;">
    <thead>
      <tr style="background-color:#f1de3a;">
      <?php $title=''; foreach ($datarow as $data) {
        $title=$data->bets_title;
      } ?>  
		  <th colspan="4"><h3 style="margin:0px;"><?php if($title){ echo $title; }else{echo 'Best Picks Today';}?></b><b style="float: right;">
          <span><form style="float: left;"  action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post"/>
            <input type="hidden" name='today' value='<?php echo date('Y-m-d');  ?>'>
            <button type="submit" class="btnnew"  name="submit">Today</button>
			  </form> | <form style="float: right;"  action="<?php $url_dir=$_SERVER['REQUEST_URI']; 
        $url_dir_no_get_param= explode("?",$url_dir)[0];
        echo $url_dir_no_get_param; ?>" method="post"/>
            <input type="hidden" name='yesturday' value='<?php echo date('Y-m-d', strtotime("yesterday"));  ?>'>
            <button type="submit" class="btnnew" name="submit">Yesterday</button> <!-- <i class="fa fa-calendar" aria-hidden="true"></i> -->
			  </form></span> </h3></th>
      </tr>
      <tr class="trnew">
        <th>Date</th>
        <th>Matchup</th>
        <th>Odds</th>
        <th>Results</th>
      </tr>
    </thead>
    <tbody>
        <?php 
        if($rowcount1>0){
        foreach ($rows1 as $row) {
            $id=$row->match_id ;
            $matchup= $row->matchup;
            $oddsoption= $row->oddsoption;
            $odds= $row->odds;
            $links= $row->links;
            $result= $row->result;
            $matchdate= $row->matchdate;
      ?>
      <tr style="font-weight:bold;">
        <td><?php echo date('m/d',strtotime($matchdate)); ?></td>
        <td><?php echo $matchup;; ?></td>
        <td><a href="<?php echo $links; ?>" target="_blank"><?php if($oddsoption==0){ echo '<i class="fa fa-check-circle" style="font-size:20px;color:green"></i>';}if($oddsoption==1){ echo '<i class="fa fa-times-circle" style="font-size:20px;color:red"></i>';} echo '&nbsp;&nbsp;&nbsp;'.$odds; ?></td>
        <td><?php echo $result; ?></a></td>
      </tr>
  <?php }}else{
    echo "<tr><td style='text-align: center;font-size: 27px;color: red;' colspan='7'>No records found</td></tr>";}?>
    </tbody>
  </table>
  <?php
        $page_links = paginate_links( array(
            'base' => add_query_arg( 'pagenum', '%#%' ),
            'format' => '',
            'prev_text' => __( 'Back', 'text-domain' ),
            'next_text' => __( 'Next', 'text-domain' ),
            'total' => $num_of_pages,
            'current' => $pagenum
        ) );

        if ( $page_links ) 
        {
            echo '<center>
				<div class="tablenav-pages" style="margin: 1em 0;font-weight:bold;">' . $page_links . '</div>
			</center>';
        } ?>  
</div>
<?php } ?>