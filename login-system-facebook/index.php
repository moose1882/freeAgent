<?php
/*
 * @author Shahrukh Khan
 * @website http://www.thesoftwareguy.in
 * @facebbok https://www.facebook.com/Thesoftwareguy7
 * @twitter https://twitter.com/thesoftwareguy7
 * @googleplus https://plus.google.com/+thesoftwareguyIn
 */
require_once './config.php';
if (isset($_SESSION["user_id"]) && $_SESSION["user_id"] != "") {
  // user already logged in the site
  header("location:".SITE_URL . "home.php");
}
include './header.php';
?>
<div class="container">
  <div class="margin10"></div>
  <div class="col-sm-3 col-sm-offset-4">
    <a class="btn btn-block btn-social btn-facebook" href="<?php echo $loginURL; ?>">
      <i class="fa fa-facebook"></i> Login with Facebook
    </a>
  </div>
</div>
<?php
include './footer.php';
?>