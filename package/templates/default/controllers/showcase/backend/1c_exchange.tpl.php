<?php
	$this->addBreadcrumb('Работа с товарами', $this->href_to('goods'));
	$this->addBreadcrumb('Обменн данными с 1с');
	$this->setPageTitle('Обменн данными с 1с');
    if ($options['ctype_name']) {

        $cats_list = [0=>''];
        $cats_import = [];

        $model  = cmsCore::getModel('content');

        $cats = $model->getCategoriesTree($options['ctype_name'],false);

        if ($cats){
            foreach($cats as $cat) {
                if ($cat['ns_level'] > 1) {
                    $cat['title'] = str_repeat('-', $cat['ns_level']) . ' ' . $cat['title'];
                }
                $cats_list[$cat['id']] = $cat['title'];
            }
        }

        $props_list = $model->getContentProps($options['ctype_name']);
        $props_import = [];

        $fields_list = [''=>''];
        $fields = $model->getContentFields($options['ctype_name']);
        if ($fields){
            foreach($fields as $field) {
                $fields_list[$field['name']] = $field['title'];
            }
        }

        $exchange_path = PATH . cmsConfig::get('cache_root').'1c_exchange';
        $check_files = false;

        $file_import = null;

	    if (file_exists($exchange_path.'/import.xml')) {
		    $file_import = 'import.xml';
	    } elseif (file_exists($exchange_path.'/import0_1.xml')) {
		    $file_import = 'import0_1.xml';
	    }


        if ($file_import) {
            $check_files = true;

            $z = new XMLReader;

            $z->open($exchange_path . '/'.$file_import);

            while ($z->read() && $z->name !== 'Классификатор');

            $xml = new SimpleXMLElement($z->readOuterXML());

            $z->close();

            if (isset($xml->Группы->Группа)) {
                foreach ($xml->Группы->Группа as $xml_group) {
                    $cats_import[(string)$xml_group->Ид] = (string)$xml_group->Наименование;
                }
            }

            if (isset($xml->Свойства->Свойство)) {
                foreach ($xml->Свойства->Свойство as $property) {
                    $props_import[(string)$property->Ид] = (string)$property->Наименование;
                }
            }
        }

        $prices_list = [];

	    $file_offers = null;

	    if (file_exists($exchange_path.'/offers.xml')) {
		    $file_offers = 'offers.xml';
	    } elseif (file_exists($exchange_path.'/offers0_1.xml')) {
		    $file_offers = 'offers0_1.xml';
	    }

        if ($file_offers) {
            $z = new XMLReader;
            $z->open($exchange_path.'/'.$file_offers);

            while ($z->read() && $z->name !== 'ТипыЦен');

            $xml = new SimpleXMLElement($z->readOuterXML());

            $z->close();

            if (isset($xml->ТипЦены)) {
                foreach ($xml->ТипЦены as $xml_price) {
                    $prices_list[(string)$xml_price->Ид] = (string)$xml_price->Наименование;
                }
            }
        }
    }
?>

<style>
    #wrapper{background-color:#f1f1f1;}
    .box-container{display:flex;align-items: flex-start;justify-content: flex-start;}
    .box {background-color:#fff;box-shadow:6px 6px 12px rgba(0,0,0,.15);margin:0 15px 15px;}
    .box .header{padding:10px 20px;font-size:20px;font-weight:500;color:#68809B;text-align:center;}
    .box .content {padding:0px 20px;font-size: 15px;}
    .box .footer {padding: 10px 20px;text-align: right;}
    .box.medium {width:33%;}
    fieldset .select{width:100%;}
    .list-unstyled{margin:0;padding:0;list-style:none;}
    .grey {color:#2b3b4e;font-weight:700;}
    .green {color: #1e7e34;font-weight:700;}
    .hidden {visibility:hidden;height:0;overflow: hidden;}
</style>

<?php
    $this->addJSFromContext($this->getJavascriptFileName('jquery-chosen'));
    $this->addCSSFromContext($this->getTemplateStylesFileName('jquery-chosen'));
?>

<?php if ($options['ctype_name']) { ?>
    <form class="" action="" method="post" enctype="multipart/form-data">
        <div class="box-container">
            <div class="box medium">
                <div class="box-wrapper">
                    <div class="header">Настройка обмена "Категорий"</div>
                    <div class="content">
                        <fieldset id="options_cats">
                            <div id="f_ex_cats_type" class="field ft_select">
                                <label for="ex_cats_type">Вариант создания категории</label>
                                <?php echo html_select('ex_cats_type', [''=>'','create'=>'Создавать в корне','parent'=>'Создавать в родителе','mapping'=>'Использовать сопоставление'], isset($options['exchange']['cats']['type'])?$options['exchange']['cats']['type']:'', ['id'=>'ex_cats_type', 'class'=>'select']); ?>
                            </div>
                            <div class="cats_select hidden" id="cats_parent">
                                <div id="f_ex_cats_parent" class="field ft_select">
                                    <label for="ex_cats_parent">Родительская категория</label>
                                    <?php echo html_select('ex_cats_parent', $cats_list, isset($options['exchange']['cats']['parent_id'])?$options['exchange']['cats']['parent_id']:'', ['id'=>'ex_cats_parent', 'class'=>'select']); ?>
                                </div>
                            </div>
                            <div class="cats_select hidden" id="cats_mapping">
                                <?php foreach ($cats_list as $cat_id=>$cat) { ?>
                                    <?php if ($cat_id) { ?>
                                        <div id="f_ex_cats_mapping_<?php $cat_id;?>" class="field ft_select">
                                            <label for="ex_cats_mapping_<?php $cat_id;?>"><?php echo trim(str_replace('--','',$cat));?></label>
                                            <select name="ex_cats_mapping[<?php echo $cat_id;?>]" id="ex_cats_mapping_<?php $cat_id;?>" class="select">
                                                <option value="">Выбрать</option>
                                                <?php foreach ($cats_import as $ic=>$icat) { ?>
                                                    <option value="<?php echo $ic;?>"><?php echo $icat;?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                            <script>
                                $(function(){
                                    $(document).on('change','#ex_cats_type', function(){
                                        var _this = $(this),
                                            val = _this.val();
                                        $('.cats_select').addClass('hidden');
                                        $('#cats_'+ val).removeClass('hidden');
                                    });
                                });
                            </script>
                        </fieldset>
                    </div>
                    <div class="footer">
                        <div class="buttons">
                            <input class="button-submit button" type="submit" name="submit" value="Сохранить" title="Сохранить">
                        </div>
                    </div>
                </div>
                <div class="box-wrapper">
                    <div class="header">Настройка обмена "Характеристики"</div>
                    <div class="content">
                        <fieldset id="options_props">
                            <div id="f_ex_props_type" class="field ft_select">
                                <label for="ex_cats_type">Вариант создания свойств</label>
                                <?php echo html_select('ex_props_type', [''=>'','skip'=>'Не переносить','create'=>'Создавать новые','mapping'=>'Использовать сопоставление'], isset($options['exchange']['props']['type'])?$options['exchange']['props']['type']:'', ['id'=>'ex_props_type', 'class'=>'select']); ?>
                            </div>
                            <div class="props_select hidden" id="props_mapping">
                                <?php if ($props_list) { ?>
                                    <?php foreach ($props_list as $prop) { ?>
                                        <?php if ($prop['id']) { ?>
                                            <div id="f_ex_props_mapping_<?php $prop['id'];?>" class="field ft_select">
                                                <label for="ex_props_mapping_<?php $prop['id'];?>"><?php echo $prop['title']?></label>
                                                <select name="ex_props_mapping[<?php echo $prop['id'];?>]" id="ex_props_mapping_<?php $prop['id'];?>" class="select">
                                                    <option value="">Выбрать</option>
                                                    <?php foreach ($props_import as $ip=>$iprop) { ?>
                                                        <option value="<?php echo $ip;?>"><?php echo $iprop;?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </fieldset>
                        <script>
                            $(function(){
                                $(document).on('change','#ex_props_type', function(){
                                    var _this = $(this),
                                        val = _this.val();
                                    $('.props_select').addClass('hidden');
                                    $('#props_'+ val).removeClass('hidden');
                                });
                            });
                        </script>
                    </div>
                    <div class="footer">
                        <div class="buttons">
                            <input class="button-submit button" type="submit" name="submit" value="Сохранить" title="Сохранить">
                        </div>
                    </div>
                </div>
            </div>
            <div class="box medium">
                <div class="header">Настройка обмена "Товары"</div>
                <div class="content">
                    <fieldset id="options_items">
                        <div id="f_ex_items_art_no" class="field ft_select">
                            <label for="ex_items_art_no">Артикул</label>
                            <?php echo html_select("ex_items_mapping['art_no']", $fields_list, isset($options['exchange']['items']['mapping']['art_no'])?$options['exchange']['items']['mapping']['art_no']:'', ['id'=>'ex_items_art_no', 'class'=>'select']); ?>
                        </div>
                        <div id="f_ex_items_barcode" class="field ft_select">
                            <label for="ex_items_barcode">Штрих-код</label>
                            <?php echo html_select("ex_items_mapping['barcode']", $fields_list, isset($options['exchange']['items']['mapping']['barcode'])?$options['exchange']['items']['mapping']['barcode']:'', ['id'=>'ex_items_barcode', 'class'=>'select']); ?>
                        </div>
                        <div id="f_ex_items_title" class="field ft_select">
                            <label for="ex_items_title">Наименование</label>
                            <?php echo html_select("ex_items_mapping['title']", $fields_list, isset($options['exchange']['items']['mapping']['title'])?$options['exchange']['items']['mapping']['title']:'', ['id'=>'ex_items_title', 'class'=>'select']); ?>
                        </div>
                        <div id="f_ex_items_content" class="field ft_select">
                            <label for="ex_items_content">Описание</label>
                            <?php echo html_select("ex_items_mapping['content']", $fields_list, isset($options['exchange']['items']['mapping']['content'])?$options['exchange']['items']['mapping']['content']:'', ['id'=>'ex_items_content', 'class'=>'select']); ?>
                        </div>
                        <div id="f_ex_items_unit" class="field ft_select">
                            <label for="ex_items_unit">Ед. измерения</label>
                            <?php echo html_select("ex_items_mapping['unit']", $fields_list, isset($options['exchange']['items']['mapping']['unit'])?$options['exchange']['items']['mapping']['unit']:'', ['id'=>'ex_items_unit', 'class'=>'select']); ?>
                        </div>
                        <div id="f_ex_items_photo" class="field ft_select">
                            <label for="ex_items_photo">Изображение</label>
                            <?php echo html_select("ex_items_mapping['photo']", $fields_list, isset($options['exchange']['items']['mapping']['photo'])?$options['exchange']['items']['mapping']['photo']:'', ['id'=>'ex_items_photo', 'class'=>'select']); ?>
                        </div>
                        <div id="f_ex_items_qty" class="field ft_select">
                            <label for="ex_items_qty">Количество</label>
                            <?php echo html_select("ex_items_mapping['qty']", $fields_list, isset($options['exchange']['items']['mapping']['qty'])?$options['exchange']['items']['mapping']['qty']:'', ['id'=>'ex_items_qty', 'class'=>'select']); ?>
                        </div>
                    </fieldset>
                </div>
                <div class="footer">
                    <div class="buttons">
                        <input class="button-submit button" type="submit" name="submit" value="Сохранить" title="Сохранить">
                    </div>
                </div>
            </div>
            <div class="box medium">
                <div class="header">Настройка обмена "Товары"</div>
                <div class="content">
                    <fieldset id="options_prices">
                        <?php foreach ($prices_list as $key=>$pr_item) { ?>
                            <div id="f_ex_items_price_type" class="field ft_select">
                                <label for="ex_items_price_type"><?php echo $pr_item;?></label>
		                        <?php echo html_select("ex_items_prices[".$key."]", $fields_list, isset($options['exchange']['items']['prices'][$key])?$options['exchange']['items']['prices'][$key]:'', ['id'=>'ex_items_prices', 'class'=>'select']); ?>
                            </div>
                        <?php } ?>
                    </fieldset>
                </div>
            </div>
        </div>
    </form>
<?php } else { ?>
    <div class="alert error">Требуется выбрать тип контента в качестве Магазина!</div>
<?php } ?>
<script type="text/javascript">
    $('.select').chosen({placeholder_text_single: '<?php echo LANG_SELECT; ?>'});
</script>
