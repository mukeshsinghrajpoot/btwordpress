<?php
/*Start title section*/
function headertitle()
{
wp_enqueue_style( 'fetureaddcss', plugins_url( '/bootstrap/css/fetureadd.css', __FILE__ ) ); 
wp_enqueue_script( "headertitle", plugin_dir_url( __FILE__ ) . '/bootstrap/js/headertitle.js', 'jQuery','1.0.0', true);
wp_localize_script( 'headertitle', 'headertitle_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
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
              <h2>Header title </h2>
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
              <h2>Submit New header</h2>
              <div id="result"></div>
            </div>
            <div class="request-title">
              <label>Enter header title</label>
              <input type="text" name="bets_title" id="bets_title" required>
            </div>
            <div class="upload-submitbtn">
              <button id="titlesubmit">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php   
}
function feture_key_title()
{
   $bets_title=$_POST['bets_title'];
   global $wpdb;
   $bets_update=date("Y-m-d H:i:s");
  $table_name = $wpdb->prefix . 'betstitle';
  $sss = $wpdb->insert($table_name, array('bets_title' => $bets_title,'bets_update'=>$bets_update),array('%s', '%s'));
    if($sss)
    { echo "Done";}
    else 
    { 
    echo "Not Done";
    }
wp_die();
}
add_action('wp_ajax_feture_key_title', 'feture_key_title');
add_action('wp_ajax_nopriv_feture_key_title', 'feture_key_title');
function titleedit()
{
wp_enqueue_style( 'fetureaddcss', plugins_url( '/bootstrap/css/fetureadd.css', __FILE__ ) ); 
wp_enqueue_script( "titleeditajax", plugin_dir_url( __FILE__ ) . '/bootstrap/js/titleeditajax.js', 'jQuery','1.0.0', true);
wp_localize_script( 'titleeditajax', 'titleeditajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
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
              <h2>Title Edit</h2>
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
              $table_name = $wpdb->prefix . 'betstitle';
              $results=$wpdb->get_results("SELECT * FROM $table_name WHERE titletable_id = $id");
              foreach($results as $row)
              {
                $titletable_id=$row->titletable_id;
                $bets_title=$row->bets_title;
              }
            } 
            ?>
        <div class="admin-upload-section">
          <form id="post-form" method="post" enctype="multipart/form-data">
            <div class="uploadsubmit-heading">
              <h2>Edit Title</h2>
              <div id="result"></div>
            </div>
            <div class="request-title">
              <label>Enter Tile</label>
              <input type="text" name="bets_title" id="bets_title" value="<?php echo $bets_title; ?>">
              <input type="hidden" name="titletable_id" id="titletable_id" value="<?php echo $titletable_id; ?>">
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
function feture_key_titleeditresponce()
{
$titletable_id=$_POST['titletable_id'];  
$bets_title=$_POST['bets_title'];
   global $wpdb;
   $bets_update=date("Y-m-d H:i:s");
  $table_name = $wpdb->prefix . 'betstitle';
  $sss=$wpdb->update($table_name, array('titletable_id'=>$titletable_id, 'bets_title'=>$bets_title,'bets_update'=>$bets_update), array('titletable_id'=>$titletable_id));
    if($sss)
    { echo "Done";}
    else 
    { 
    echo "Not Upadte data";
    }
wp_die();
}
add_action('wp_ajax_feture_key_titleeditresponce', 'feture_key_titleeditresponce');
add_action('wp_ajax_nopriv_feture_key_titleeditresponce', 'feture_key_titleeditresponce');

function viewtitle()
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
  $betstitle = $wpdb->prefix . 'betstitle';
    foreach ($chk1 as $id2)
    {
        $del=$wpdb->query(
                "DELETE FROM $betstitle
                WHERE titletable_id = $id2"
        );
    }
    if( $del==1) {
  echo  '<br><br><div class="containe"><div class="row"><div class="col-sm-8"><div class="alert alert-success"><strong>Delete data!</strong>  successfully.</div></div></div></div>';
      }
}
?>
<div class="container">
  <h2>View Data</h2>
  <form  action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post"/>
  <button type="submit" class="btn btn-danger" name="submit">DELETE SELECT DATA</button>
  <br>
  <br>       
  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th><input type="checkbox" id="select_all" value=""/></th>
        <th>Id</th>
        <th>Title</th>
        <th>last update</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
        <?php 
        global $wpdb;
        $betstitle = $wpdb->prefix . 'betstitle';
        $pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;      
        $limit = 5; // number of rows in page
        $offset = ( $pagenum - 1 ) * $limit;
        $total = $wpdb->get_var( "select count(*) as total from $betstitle" );
        $num_of_pages = ceil( $total / $limit );
        $rows = $wpdb->get_results( "SELECT * from $betstitle ORDER BY `titletable_id` DESC limit  $offset, $limit" );
        $rowcount = $wpdb->num_rows;
        if($rowcount>0){
        foreach ($rows as $row) {
            $id=$row->titletable_id ;
            $bets_title= $row->bets_title;
            $bets_update= $row->bets_update;
      ?>
      <tr>
        <td><input type="checkbox" name="checked_id[]" class="checkbox" value="<?php  echo $id; ?>"></td>
        <td><?php echo $id; ?></td>
        <td><?php echo $bets_title; ?></td>
        <td><?php echo $bets_update; ?></td>
        <td><a href="<?php echo admin_url('admin.php?page=titleedit&id=' . $id); ?>" class="btn btn-info">Edit</a></td>
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
/*close title section*/