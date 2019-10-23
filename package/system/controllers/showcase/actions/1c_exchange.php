<?php

    class actionShowcase1cExchange extends cmsAction
    {
        private $type;
        private $mode;
        private $filename;
        private $dir;
        private $start_time;
        private $max_exec_time;
        private $ctype_name;
        private $ctype;
        private $options;

        public function run()
        {
            $model_content = cmsCore::getModel('content');

            $this->options = $this->getOptions();

            $this->type       = $this->request->get('type');
            $this->mode       = $this->request->get('mode');
            $this->filename   = $this->request->get('filename');
            $this->dir        = cmsConfig::get('cache_path') . '1c_exchange';
            $this->ctype_name = $this->options['ctype_name'];
            $this->ctype      = $model_content->getContentTypeByName($this->options['ctype_name']);

            $this->start_time    = microtime(true);
            $this->max_exec_time = @ini_get("max_execution_time");

            if (empty($this->max_exec_time)) {
                $this->max_exec_time = 30;
            }

            if (!$this->type || !$this->mode) {
                cmsCore::error404();
            }

            switch ($this->type) {
                case 'sale':

                    $this->exchangeSales();

                    break;

                case 'catalog':

                    $this->exchangeCatalog();

                    break;

                default:
                    cmsCore::error404();
            }

            $this->halt();
        }

        private function exchangeSales()
        {
            switch ($this->mode) {
                case 'checkauth':

                    $this->exchangeSalesCheckAuth();

                    break;

                case 'init':

                    $this->exchangeSalesInit();

                    break;

                case 'file':

                    $this->exchangeSalesFile();

                    break;

                case 'import':

                    $this->exchangeSalesImport();

                    break;

                default:
                    cmsCore::error404();

            }
        }

        private function exchangeSalesCheckAuth()
        {
            $logged_id = cmsUser::login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'], false);

            if ($logged_id) {
                print "success\n";
                print session_name() . "\n";
                print session_id();
            } else {
                print "error\n";
            }
        }

        private function exchangeSalesInit()
        {

        }

        private function exchangeSalesFile()
        {

        }

        private function exchangeSalesImport()
        {

        }

        private function exchangeCatalog()
        {
            switch ($this->mode) {
                case 'checkauth':

                    $this->exchangeCatalogCheckAuth();

                    break;

                case 'init':

                    $this->exchangeCatalogInit();

                    break;

                case 'file':

                    $this->exchangeCatalogFile();

                    break;

                case 'import':

                    $this->exchangeCatalogImport();

                    break;

                default:
                    cmsCore::error404();

            }
        }

        private function exchangeCatalogCheckAuth()
        {

            $logged_id = cmsUser::login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'], false);

            if ($logged_id) {
                print "success\r\n";
                print session_name() . "\r\n";
                print session_id() . "\r\n";
            } else {
                print "error\r\n";
            }
        }

        private function exchangeCatalogInit()
        {
            foreach (glob($this->dir . '/*.{xml,gif,jpg,png,bmp}', GLOB_BRACE) as $file) {
                unlink($file);
            }

            cmsUser::sessionUnset('last_1c_imported_variant_num');
            cmsUser::sessionUnset('last_1c_imported_product_num');
            cmsUser::sessionUnset('props_mapping');
            cmsUser::sessionUnset('props_val_mapping');
            cmsUser::sessionUnset('categories_mapping');
            cmsUser::sessionUnset('storage_mapping');

            print "zip=no\r\n";
            print "file_limit=100000000\r\n";
        }

        private function exchangeCatalogFile()
        {
            $f = fopen($this->dir . '/' . basename($this->filename), 'ab');
            fwrite($f, file_get_contents('php://input'));
            fclose($f);

            print "success\n";
        }

        private function exchangeCatalogImport()
        {

            if (strpos($this->filename, 'import')!== false) {
                if (isset($this->options['exchange'])) {
                    if (!cmsUser::isSessionSet('last_1c_imported_product_num')) {
                        $z = new XMLReader;

                        $z->open($this->dir . '/' . $this->filename);

                        while ($z->read() && $z->name !== 'Классификатор');

                        $xml = new SimpleXMLElement($z->readOuterXML());

                        $z->close();

                        $this->exchangeCatalogImportCategories($xml);

                        $this->exchangeCatalogImportProps($xml);
                    }

                    $z = new XMLReader;
                    $z->open($this->dir . '/' . $this->filename);

                    while ($z->read() && $z->name !== 'Товар');

                    $last_product_num = 0;

                    if (cmsUser::isSessionSet('last_1c_imported_product_num')) {
                        $last_product_num = cmsUser::sessionGet('last_1c_imported_product_num');
                    }

                    $current_product_num = 0;

                    while ($z->name === 'Товар') {
                        if ($current_product_num >= $last_product_num) {
                            $xml = new SimpleXMLElement($z->readOuterXML());

                            $this->exchangeCatalogImportProduct($xml);

                            $exec_time = microtime(true) - $this->start_time;

                            if ($exec_time + 1 >= $this->max_exec_time) {
                                header("Content-type: text/xml; charset=utf-8");
                                print "\xEF\xBB\xBF";
                                print "progress\r\n";
                                print "Выгружено товаров: $current_product_num\r\n";

                                cmsUser::sessionSet('last_1c_imported_product_num', $current_product_num);

                                $this->halt();
                            }
                        }

                        $z->next('Товар');
                        $current_product_num++;
                    }

                    $z->close();

                    cmsUser::sessionUnset('last_1c_imported_product_num');
                }

                print "success\r\n";

                $this->halt();

            } elseif (strpos($this->filename, 'offers')!== false) {

                if (isset($this->options['exchange'])) {

                    $z = new XMLReader;
                    $z->open($this->dir . '/' . $this->filename);

                    while ($z->read() && $z->name !== 'Предложение');

                    $last_variant_num = 0;

                    if (cmsUser::isSessionSet('last_1c_imported_variant_num')) {
                        $last_variant_num = cmsUser::sessionGet('last_1c_imported_variant_num');
                    }

                    $current_variant_num = 0;

                    while ($z->name === 'Предложение') {
                        if ($current_variant_num >= $last_variant_num) {
                            $xml = new SimpleXMLElement($z->readOuterXML());

                            $this->exchangeCatalogImportVariant($xml);

                            $exec_time = microtime(true) - $this->start_time;
                            if ($exec_time + 1 >= $this->max_exec_time) {
                                header("Content-type: text/xml; charset=utf-8");
                                print "\xEF\xBB\xBF";
                                print "progress\r\n";
                                print "Выгружено ценовых предложений: $current_variant_num\r\n";

                                cmsUser::sessionSet('last_1c_imported_variant_num', $current_variant_num);

                                $this->halt();
                            }
                        }
                        $z->next('Предложение');
                        $current_variant_num++;
                    }

                    $z->close();
                    cmsUser::sessionUnset('last_1c_imported_variant_num');
                }

                print "success\r\n";

                $this->halt();
            }

            print "success\r\n";

            $this->halt();
        }

        private function exchangeCatalogImportCategories($xml, $parent_id = 1)
        {
            $model_content = cmsCore::getModel('content');

            $type = $this->options['exchange']['cats']['type'];

            if ($type == 'parent') {
                $parent_id = $this->options['exchange']['cats']['parent_id'];
            }

            $mapping = null;

            if ($this->options['exchange']['cats']['mapping']) {
	            $mapping = array_flip($this->options['exchange']['cats']['mapping']);
            }

            if (isset($xml->Группы->Группа)) {
                foreach ($xml->Группы->Группа as $xml_group) {

                    $category = $model_content->getCategory($this->ctype_name, (string)$xml_group->Ид, 'guid');

                    if (!$category && $type != 'mapping') {
                        $category = [
                            'parent_id'   => $parent_id,
                            'title'       => (string)$xml_group->Наименование,
                            'guid'        => (string)$xml_group->Ид,
                            'is_hidden'   => null,
                            'description' => '',
                        ];

                        $category = $model_content->addCategory($this->ctype_name, $category);
                    }

                    if ($type != 'mapping') {
                        cmsUser::sessionSet('categories_mapping:' . (string)$xml_group->Ид, $category['id']);
                    } else {
                        cmsUser::sessionSet('categories_mapping:' . (string)$xml_group->Ид, $mapping[(string)$xml_group->Ид]);
                    }

                    $this->exchangeCatalogImportCategories($xml_group, $category['id']);
                }
            }
        }

        private function exchangeCatalogImportProps($xml)
        {
	        $type    = $this->options['exchange']['props']['type'];

	        if ($type=='skip') {
	        	return true;
	        }

            $model_content = cmsCore::getModel('content');

            $property = null;

            if (isset($xml->Свойства->СвойствоНоменклатуры)) {
                $property = $xml->Свойства->СвойствоНоменклатуры;
            }

            if (isset($xml->Свойства->Свойство)) {
                $property = $xml->Свойства->Свойство;
            }

            if ($property) {

                $categories = cmsUser::sessionGet('categories_mapping');

                $cats = [];

                foreach ($categories as $rec) {
                    $cats[] = $rec;
                }

                $mapping = array_flip($this->options['exchange']['props']['mapping']);


                foreach ($property as $xml_feature) {

                    if ($type == 'create') {
                        $prop = $this->getContentProp($this->ctype_name, (string)$xml_feature->Ид, 'guid');
                    } else {
                        $prop = $this->getContentProp($this->ctype_name, $mapping[(string)$xml_feature->Ид], 'guid');
                    }

                    $prop_values = [];

                    foreach ($xml_feature->ВариантыЗначений->Справочник as $xml_val) {
                        $prop_values[] = (string)$xml_val->ИдЗначения . '|' . (string)$xml_val->Значение;
                        cmsUser::sessionSet('props_val_mapping:' . (string)$xml_val->ИдЗначения, (string)$xml_val->Значение);
                    }

                    $prop_values = implode("\n", $prop_values);

                    $data = [
                        'ctype_id' => $this->ctype['id'],
                        'guid'     => (string)$xml_feature->Ид,
                        'title'    => (string)$xml_feature->Наименование,
                        'fieldset' => null,
                        'type'     => 'string',
                        'values'   => $prop_values,
                        'cats'     => $cats,
                    ];

                    if (!$prop && $type != 'mapping') {

                        $prop['id'] = $model_content->addContentProp($this->ctype_name, $data);

                    } else {
                        if (isset($prop['id'])) {
                            $model_content->updateContentProp($this->ctype_name, $prop['id'], $data);
                        }
                    }

                    if ($type == 'create') {
                        cmsUser::sessionSet('props_mapping:' . (string)$xml_feature->Ид, $prop['id']);
                    } else {
                        cmsUser::sessionSet('props_mapping:' . (string)$xml_feature->Ид, $mapping[(string)$xml_feature->Ид]);
                    }
                }
            }
        }

        private function getProductProps($xml)
        {
            $props_mapping     = cmsUser::sessionGet('props_mapping');
            $props_val_mapping = cmsUser::sessionGet('props_val_mapping');
            $props             = [];

            if (!isset($xml->ЗначенияСвойств)) {
                return $props;
            }

            foreach ($xml->ЗначенияСвойств->ЗначенияСвойства as $xml_property) {
                $prop_id     = (string)$xml_property->Ид;
                $prop_val_id = (string)$xml_property->Значение;
                if ($props_mapping[$prop_id]) {
                    $props[$props_mapping[$prop_id]] = $props_val_mapping[$prop_val_id];
                }
            }

            return $props;
        }

        private function getContentProp($ctype_name, $id, $field = 'guid')
        {

            $model_content = cmsCore::getModel('content');

            $props_table_name = $model_content->table_prefix . $ctype_name . '_props';
            $bind_table_name  = $model_content->table_prefix . $ctype_name . '_props_bind';

            $prop = $model_content->getItemByField($props_table_name, $field, $id, function ($item, $model) {
                $item['options'] = cmsModel::yamlToArray($item['options']);

                return $item;
            });

            if ($prop) {
                $model_content->filterEqual('prop_id', $prop['id']);

                $prop['cats'] = $model_content->get($bind_table_name, function ($item, $model) {
                    return (int)$item['cat_id'];
                });
            }

            return $prop;
        }

        private function exchangeCatalogImportProduct($xml)
        {
            $model_content = cmsCore::getModel('content');
            $table_name    = 'con_' . $this->ctype_name;
            $categories    = cmsUser::sessionGet('categories_mapping');
            $category_id   = 1;
            $item_var      = null;

            $fields = $model_content->getContentFields($this->ctype_name);

            @list($product_1c_id, $variant_1c_id) = explode('#', (string)$xml->Ид);

            if (isset($xml->Группы->Ид)) {
                $category_id = $categories[(string)$xml->Группы->Ид];
            }

            if ($category_id==1) {
                return false;
            }

            $item = $this->model->getItemByField($table_name, 'guid', (string)$product_1c_id);

            if (!$item && $variant_1c_id) {
                $item_var = $this->model->getItemByField('sc_variations', 'guid', (string)$variant_1c_id);

                if ($item_var) {
                    $item = $this->model->getItemById($table_name, $item_var['item_id']);
                }
            }

            $mapping = $this->options['exchange']['items']['mapping'];

            $data = [];

            $data['category_id'] = $category_id;
	        $data['guid'] = $variant_1c_id ? $variant_1c_id : $product_1c_id;
            $data['props'] = $this->getProductProps($xml);

            if (isset($mapping['art_no']) && $mapping['art_no']!='') {
                $data[$mapping['art_no']]=(string)$xml->Артикул;
            }

            if (isset($mapping['barcode']) && $mapping['barcode']!='') {
                $data[$mapping['barcode']]=(string)$xml->Штрихкод;
            }

            if (isset($mapping['unit']) && $mapping['unit']!='') {
                $data[$mapping['unit']]=(string)$xml->БазоваяЕдиница;
            }

            if (isset($mapping['title']) && $mapping['title']!='') {
                $data[$mapping['title']]=(string)$xml->Наименование;
            }

            if (isset($mapping['content']) && $mapping['content']!='') {
                $data[$mapping['content']]=(string)$xml->Описание;
            }

            if (isset($mapping['photo']) && $mapping['photo']!='') {
                $data[$mapping['photo']] = $this->getProductImage($xml, $fields['photo']['options']['sizes']);
            }

            if (!$item && !$item_var) {
                $item = $model_content->addContentItem($this->ctype, $data, $fields);
            } elseif ($item) {
                $model_content->updateContentItem($this->ctype, $item['id'], $data, $fields);
            }

            if ($xml->Статус == 'Удален') {
                $model_content->deleteContentItem($this->ctype_name, $item['id']);
            }
        }

        private function exchangeCatalogImportVariant($xml)
        {
            $variant = null;

            @list($product_1c_id, $variant_1c_id) = explode('#', $xml->Ид);

            $table_name    = 'con_' . $this->ctype_name;
            $model_content = cmsCore::getModel('content');

            $mapping = $this->options['exchange']['items']['mapping'];
            $prices = $this->options['exchange']['items']['prices'];

            //$price_type = '';

            //if (isset($this->options['items']['price_type'])) {
            //    $price_type = $this->options['exchange']['items']['price_type'];
            //}

            foreach ($xml->Цены->Цена as $xml_price) {
                $price_id = (string)$xml_price->ИдТипаЦены;
                if (isset($prices[$price_id])) {
                    $model_content->filterEqual('guid', $product_1c_id)->updateFiltered($table_name, [
                        $mapping['qty'] => (float)$xml->Количество,
                        $this->options['exchange']['items']['prices'][$price_id] => (float)$xml_price->ЦенаЗаЕдиницу,
                    ]);
                }
            }
        }

        private function getProductImage($xml, $presets)
        {
            $photo        = [];
            $model_images = cmsCore::getModel('images');
            $i            = 0;

            if (isset($xml->Картинка)) {
                foreach ($xml->Картинка as $img) {


                    if ($i == 0) {
                        $img_name = basename((string)$img);

                        if (file_exists($this->dir . '/' . $img_name)) {

                            $image = new cmsImages($this->dir . '/' . $img_name);

                            foreach ($presets as $key => $name) {
                                $preset = $model_images->getPresetByName($name);

                                $photo[0][$name] = $image->resizeByPreset($preset);
                            }
                        }
                    }
                }
            }

            return $photo;
        }
    }
