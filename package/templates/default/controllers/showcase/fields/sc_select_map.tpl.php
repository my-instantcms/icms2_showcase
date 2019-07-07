<?php if ($field->title) { ?><label><?php echo $field->title; ?></label><?php } ?>
<?php $value = $value ? $value : ''; ?>
<script src="//api-maps.yandex.ru/2.1/?lang=ru_RU"></script>
<script src="//maps.googleapis.com/maps/api/js?key=AIzaSyDx6-8xOE5uktxgsOmfN7ElgSjaTRd0h_w"></script>
<div id="geo-widget-<?php echo $field->element_name; ?>" class="city-input">
    <?php echo html_input('hidden', $field->element_name, $value, array('id' => $field->element_name)); ?>
    <span <?php if (!$value){ ?>style="display:none"<?php } ?>><?php echo $value; ?></span>
    <a onclick="icms.modal.openAjax('<?php echo href_to('showcase', 'delivery_map', array('add', $field->element_name)); ?>' + '/' + $('#geo-widget-<?php echo $field->element_name; ?> input').val()); return false"><?php echo LANG_SELECT; ?></a>
</div>
<style>
.city-input{height:30px;line-height:22px}
.city-input a{padding:3px 10px 2px}
</style>