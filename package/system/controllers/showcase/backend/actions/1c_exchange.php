<?php

    class actionShowcase1cExchange extends cmsAction
    {
        public function run()
        {
            $options = $this->getOptions();

            if ($options['ctype_name']) {

                $table_name = 'con_' . $options['ctype_name'];

                if (!$this->cms_database->isFieldExists($table_name . '_cats', 'guid')) {
                    $this->cms_database->addTableField($table_name . '_cats', 'guid', 'VARCHAR(36) DEFAULT NULL');
                    cmsUser::addSessionMessage('Добавлено поле GUID для категорий');
                }

                if (!$this->cms_database->isFieldExists($table_name, 'guid')) {
                    $this->cms_database->addTableField($table_name, 'guid', 'VARCHAR(36) DEFAULT NULL');
                    cmsUser::addSessionMessage('Добавлено поле GUID для записей');
                }

                if (!$this->cms_database->isFieldExists($table_name . '_props', 'guid')) {
                    $this->cms_database->addTableField($table_name . '_props', 'guid', 'VARCHAR(36) DEFAULT NULL');
                    cmsUser::addSessionMessage('Добавлено поле GUID для свойств');
                }
            }

            if ($this->request->has('submit')) {
                $options['exchange'] = [
                    'cats'=>[
                        'type'=>$this->request->get('ex_cats_type'),
                        'parent_id'=>$this->request->get('ex_cats_parent'),
                        'mapping'=>$this->request->get('ex_cats_mapping'),
                    ],
                    'props'=>[
                        'type'=>$this->request->get('ex_props_type'),
                        'mapping'=>$this->request->get('ex_props_mapping'),
                    ],
                    'items'=> [
                        'mapping'=>$this->request->get('ex_items_mapping'),
                        'prices'=>$this->request->get('ex_items_prices'),
                    ],
                ];

                $this->saveOptions('showcase',$options);
                cmsUser::addSessionMessage('Настройки успешно сохранены','success');
            }

            $this->cms_template->setPageH1('Настройка обмена с 1С');

            return $this->cms_template->render('backend/1c_exchange', [
                'options' => $options,
            ]);
        }
    }
