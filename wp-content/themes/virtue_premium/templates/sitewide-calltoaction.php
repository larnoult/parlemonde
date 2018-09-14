<?php global $virtue_premium; 
?>
<div class="kt-call-sitewide-to-action">
  <div class="container">
    <div class="kt-cta row">
      <div class="col-md-10 kad-call-sitewide-title-case">
        <h2 class="kad-call-title"><?php if(isset($virtue_premium['sitewide_action_text'])) echo $virtue_premium['sitewide_action_text'];?></h2>
      </div>
      <div class="col-md-2 kad-call-sitewide-button-case">
      <a href="<?php if(isset($virtue_premium['sitewide_action_link'])) echo $virtue_premium['sitewide_action_link'];?>" class="kad-btn-primary kad-btn lg-kad-btn"><?php if(isset($virtue_premium['sitewide_action_text_btn'])) echo $virtue_premium['sitewide_action_text_btn'];?></a>    
      </div>
    </div>
  </div><!--container-->
</div><!--call class-->