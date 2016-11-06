<?php
/*
 *  © CoinSlots 
 *  Demo: http://www.btcircle.com/coinslots
 *  Please do not copy or redistribute.
 *  More licences we sell, more products we develop in the future.  
*/


if (!isset($init)) exit();

if (isset($_POST['theme']))
    echo '<div class="zprava zpravagreen"><b>Success!</b> Settings was successfuly saved.</div>';  

?>





<h1>Appearance</h1>




<link type="text/css" rel="stylesheet" href="../styles/items.css">


<fieldset style="margin-top: 10px;">
  <legend>Custom wheel images</legend>
  <select onchange="javascript:c_img(this.selectedIndex);">
    <option>Item 0
    <option>Item 1
    <option>Item 2
    <option>Item 3
    <option>Item 4
    <option>Item 5
    <option>Item 6
  </select>
  
  <div style="width: 100%;padding: 20px;-moz-box-sizing:border-box;box-sizing:border-box;-webkit-box-sizing:border-box;" class="c_imgdiv"></div>
  
</fieldset>

    <?php
      echo '<style>';
      
      for ($i = 0; $i < 7; $i++) {
        
        $dir = __DIR__.'/../../content/custom_images/item-'.$i.'/';
        
        $iterator = new \FilesystemIterator($dir);
        
        $file = '';
        while($iterator->valid()) {
          $file_ = $iterator->getFilename();
          $iterator->next();
          
          $lower = strtolower($file_);
          if (substr($lower,-4) == '.jpg' || substr($lower,-4) == '.png' || substr($lower,-5) == '.jpeg') {
            $file = $file_;
            if ($i == 0) $emptyIm = true;
            break;
          }          
        }        
        
        
        if ($file != '') echo '.img'.$i.' { background-image: url(\'../content/custom_images/item-'.$i.'/'.$file.'\'); }';
      }
      echo '</style>';
    ?>

<div class="c-img-0" style="display: none;">
  <?php if (isset($emptyIm)) { ?>
  <div class="img0" style="width: 210px; height: 210px;background-size:contain;background-repeat:no-repeat;"></div>
  <?php } else { ?><i>This item has no image by default</i><?php } ?>
  <div style="width: 100%; height: 20px;margin-top: 20px; border-top: 1px solid gray;"></div>
  <small><b>How to change this image:</b> <br>If you want to change this image, please put your image file to <br><i>/content/custom_images/<b>item-0</b>/YOUR_IMAGE</i>, <br>where <i>YOUR_IMAGE</i>
  is your image file (supported file types are: <strong>.png</strong>, <strong>.jpg</strong>).</small>
  <br><small>The item files should have at least 210x210 px.</small>
  <br><small>Leave the directory empty for default image.</small>
  <br><small>Some ready-to-use images can be found in <b>/styles/imgs/slot_icons</b>.</small>
</div>
<div class="c-img-1" style="display: none;">
  <div class="img1" style="width: 210px; height: 210px;background-size:contain;background-repeat:no-repeat;"></div>
  <div style="width: 100%; height: 20px;margin-top: 20px; border-top: 1px solid gray;"></div>
  <small><b>How to change this image:</b> <br>If you want to change this image, please put your image file to <br><i>/content/custom_images/<b>item-1</b>/YOUR_IMAGE</i>, <br>where <i>YOUR_IMAGE</i>
  is your image file (supported file types are: <strong>.png</strong>, <strong>.jpg</strong>).</small>
  <br><small>The item files should have at least 210x210 px.</small>
  <br><small>Leave the directory empty for default image.</small>
</div>
<div class="c-img-2" style="display: none;">
  <div class="img2" style="width: 210px; height: 210px;background-size:contain;background-repeat:no-repeat;"></div>
  <div style="width: 100%; height: 20px;margin-top: 20px; border-top: 1px solid gray;"></div>
  <small><b>How to change this image:</b> <br>If you want to change this image, please put your image file to <br><i>/content/custom_images/<b>item-2</b>/YOUR_IMAGE</i>, <br>where <i>YOUR_IMAGE</i>
  is your image file (supported file types are: <strong>.png</strong>, <strong>.jpg</strong>).</small>
  <br><small>The item files should have at least 210x210 px.</small>
  <br><small>Leave the directory empty for default image.</small>
</div>
<div class="c-img-3" style="display: none;">
  <div class="img3" style="width: 210px; height: 210px;background-size:contain;background-repeat:no-repeat;"></div>
  <div style="width: 100%; height: 20px;margin-top: 20px; border-top: 1px solid gray;"></div>
  <small><b>How to change this image:</b> <br>If you want to change this image, please put your image file to <br><i>/content/custom_images/<b>item-3</b>/YOUR_IMAGE</i>, <br>where <i>YOUR_IMAGE</i>
  is your image file (supported file types are: <strong>.png</strong>, <strong>.jpg</strong>).</small>
  <br><small>The item files should have at least 210x210 px.</small>
  <br><small>Leave the directory empty for default image.</small>
</div>
<div class="c-img-4" style="display: none;">
  <div class="img4" style="width: 210px; height: 210px;background-size:contain;background-repeat:no-repeat;"></div>
  <div style="width: 100%; height: 20px;margin-top: 20px; border-top: 1px solid gray;"></div>
  <small><b>How to change this image:</b> <br>If you want to change this image, please put your image file to <br><i>/content/custom_images/<b>item-4</b>/YOUR_IMAGE</i>, <br>where <i>YOUR_IMAGE</i>
  is your image file (supported file types are: <strong>.png</strong>, <strong>.jpg</strong>).</small>
  <br><small>The item files should have at least 210x210 px.</small>
  <br><small>Leave the directory empty for default image.</small>
</div>
<div class="c-img-5" style="display: none;">
  <div class="img5" style="width: 210px; height: 210px;background-size:contain;background-repeat:no-repeat;"></div>
  <div style="width: 100%; height: 20px;margin-top: 20px; border-top: 1px solid gray;"></div>
  <small><b>How to change this image:</b> <br>If you want to change this image, please put your image file to <br><i>/content/custom_images/<b>item-5</b>/YOUR_IMAGE</i>, <br>where <i>YOUR_IMAGE</i>
  is your image file (supported file types are: <strong>.png</strong>, <strong>.jpg</strong>).</small>
  <br><small>The item files should have at least 210x210 px.</small>
  <br><small>Leave the directory empty for default image.</small>
</div>
<div class="c-img-6" style="display: none;">
  <div class="img6" style="width: 210px; height: 210px;background-size:contain;background-repeat:no-repeat;"></div>
  <div style="width: 100%; height: 20px;margin-top: 20px; border-top: 1px solid gray;"></div>
  <small><b>How to change this image:</b> <br>If you want to change this image, please put your image file to <br><i>/content/custom_images/<b>item-6</b>/YOUR_IMAGE</i>, <br>where <i>YOUR_IMAGE</i>
  is your image file (supported file types are: <strong>.png</strong>, <strong>.jpg</strong>).</small>
  <br><small>The item files should have at least 210x210 px.</small>
  <br><small>Leave the directory empty for default image.</small>
</div>
<script type="text/javascript">
  function c_img(i) {
    $('.c_imgdiv').html($('.c-img-'+i).html());
  }
  
  $(document).ready(function(){c_img(0)});
</script>

