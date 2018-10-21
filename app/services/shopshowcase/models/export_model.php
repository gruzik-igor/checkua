<?php

class export_model
{

    private $groupsInList = array();
    public $allGroups = array();

    public function table($sufix = '', $useAliasTable = false)
    {
        if($useAliasTable)
            return $_SESSION['service']->table.$sufix.$_SESSION['alias']->table;
        return $_SESSION['service']->table.$sufix;
    }

    public function init()
    {
        $where = $where_ntkd = array();
        $where['wl_alias'] = $where_ntkd['alias'] = $_SESSION['alias']->id;
        $this->db->select($this->table('_groups') .' as g', '*', $where);

        $where_ntkd['content'] = "#-g.id";
        if($_SESSION['language']) $where_ntkd['language'] = $_SESSION['language'];
        $this->db->join('wl_ntkd', "name", $where_ntkd);
        if($groups = $this->db->get('array'))
            foreach ($groups as $g) {
                $this->allGroups[$g->id] = clone $g;
            }
    }

    private function setCategories($parent, &$categories)
    {
        if(!in_array($parent, $this->groupsInList))
        {
            $group = $this->allGroups[$parent];
            if($group->active)
            {
                $this->groupsInList[] = $group->id;
                $categories[] = $group;

                if($group->parent > 0)
                    $this->setCategories($group->parent, $categories);
            }
        }
        return true;
    }

    private function makeEndGroups($all, $parentGroups, $endGroups)
    {
        $endGroups = array_merge($endGroups, $parentGroups);
        foreach ($parentGroups as $parent) {
            if(isset($all[$parent]))
                $endGroups = $this->makeEndGroups ($all, $all[$parent], $endGroups);
        }
        return $endGroups;
    }

    private function getEndGroups($parentGroups)
    {
        $endGroups = $groups = array();
        foreach ($this->allGroups as $group) {
            if(isset($groups[$group->parent]))
                $groups[$group->parent][] = $group->id;
            else
                $groups[$group->parent] = array($group->id);
        }
        if(!is_array($parentGroups))
            $parentGroups = array($parentGroups);
        return $this->makeEndGroups($groups, $parentGroups, $endGroups);
    }

    public function getGroups($parent = -1)
    {
        $groups = $categories = array();
        if($parent > 0)
        {
            if(is_numeric($parent) && isset($this->allGroups[$parent]))
            {
                $groups[$parent] = $this->allGroups[$parent];
                if($childrens = $this->getEndGroups(array($parent)))
                    foreach ($childrens as $id) {
                        if(!isset($groups[$id]))
                            $groups[$id] = $this->allGroups[$id];
                    }
            }
            else
                return false;
        }
        else
            $groups = $this->allGroups;

        if($groups)
            foreach ($groups as $id => $Group) {
                if(!$Group->active)
                    unset($categories[$id]);
            }

        if($groups)
            foreach ($groups as $group) 
            {
                if($group->active)
                {
                    if(!in_array($group->id, $this->groupsInList))
                    {
                        $categories[] = $group;
                        $this->groupsInList[] = $group->id;
                    }

                    if($group->parent > 0)
                        $this->setCategories($group->parent, $categories);
                }
            }
        return $categories;
    }

    private function makeParents($parent, $parents)
    {
        if(isset($this->allGroups[$parent]))
        {
            $group = clone $this->allGroups[$parent];
            array_unshift ($parents, $group);
            if($this->allGroups[$parent]->parent > 0)
                $parents = $this->makeParents ($this->allGroups[$parent]->parent, $parents);
        }
        return $parents;
    }

    public function getProducts($Group = -1, $lastUpdated = false)
    {
        $all = true;
        $where = array('wl_alias' => $_SESSION['alias']->id);
        if($lastUpdated)
        {
        	$where['date_edit'] = '>'.(time() - 3600 * 23);
        	$where['date_add'] = '< p.date_edit';
        }

        if($_SESSION['option']->ProductMultiGroup)
        {
            $_product_group = array();
            if(is_array($Group) && !empty($Group))
            {
                $all = false;
                if($products = $this->db->getAllDataByFieldInArray($this->table('_product_group'), array('group' => $Group, 'active' => 1)))
                {
                    $where['id'] = array();
                    foreach ($products as $product)
                    {
                        array_push($where['id'], $product->product);
                        $id = $product->product;
                        if(isset($_product_group[$id]))
                            $_product_group[$id][] = $product->group;
                        else
                            $_product_group[$id] = array($product->group);
                    }
                }
                else
                    return null;
            }
            else
            {
                if($products = $this->db->getAllDataByFieldInArray($this->table('_product_group'), 1, 'active'))
                {
                    foreach ($products as $product)
                    {
                        $id = $product->product;
                        if(isset($_product_group[$id]))
                            $_product_group[$id][] = $product->group;
                        else
                            $_product_group[$id] = array($product->group);
                    }
                }
            }
        }
        else
        {
            if(is_array($Group) || (is_numeric($Group) && $Group > 0))
            {
                $all = false;
                $where['group'] = $Group;
            }
            $where['active'] = 1;
        }

        $this->db->select($this->table('_products').' as p', '*', $where);

        if($_SESSION['option']->useMarkUp)
            $this->db->join('s_shopshowcase_markup', 'value as markup', array('from' => '<p.price', 'to' => '>=p.price'));

        $where_ntkd['alias'] = $_SESSION['alias']->id;
        $where_ntkd['content'] = "#p.id";
        if($_SESSION['language']) $where_ntkd['language'] = $_SESSION['language'];
        $this->db->join('wl_ntkd as n', 'name, text', $where_ntkd);

        $products = $this->db->get('array');

        if($products)
        {
            $imagesAll = $where_img = array();
            $where_img['alias'] = $products[0]->wl_alias;
            if($_SESSION['option']->ProductMultiGroup)
            {
                if(isset($where['id']))
                    $where_img['content'] = $where['id'];
                else
                    $where_img['content'] = '>0';
            }
            else
            {
                if($all)
                    $where_img['content'] = '>0';
                else
                {
                    $where_img['content'] = array();
                    foreach ($products as $product)
                        $where_img['content'][] = $product->id;
                }
            }

            $this->db->select('wl_images', 'content, file_name', $where_img);
            if($images = $this->db->get('array'))
                foreach ($images as $image) {
                    $id = $image->content;
                    if(!isset($imagesAll[$id]))
                        $imagesAll[$id] = array();
                    if(count($imagesAll[$id]) > 9)
                        continue;
                    $imagesAll[$id][] = IMG_PATH.$_SESSION['option']->folder.'/'.$id.'/'.$image->file_name;
                }
            unset($images);

            $where = $main_options = array();
            if(is_array($where_img['content']))
            {
                $where['product'] = $where_img['content'];
                $_product_options = $this->db->getAllDataByFieldInArray($this->table('_product_options'), $where, 'product ASC');
            }
            else
                $_product_options = $this->db->getAllData($this->table('_product_options'), 'product ASC');
            if($_product_options)
            {
                $where = array('wl_alias' => $_SESSION['alias']->id, 'group' => '>=0', 'active' => 1);
                $where_name = array('option' => '#o.id');
                if($_SESSION['language']) $where_name['language'] = $_SESSION['language'];
                $main_options_list = $this->db->select($this->table('_options').' as o', 'id, group, type', $where)
                                        ->join($this->table('_options_name'), 'name, sufix', $where_name)
                                        ->get('array');
                if($main_options_list)
                {
                    $typesWithOptions = array();
                    if($wl_input_types = $this->db->getAllDataByFieldInArray('wl_input_types', 1, 'options'))
                        foreach ($wl_input_types as $type) {
                            $typesWithOptions[] = $type->id;
                        }

                    $where = array('wl_alias' => $_SESSION['alias']->id, 'group' => '<0', 'active' => 1);
                    $childrens = $this->db->select($this->table('_options').' as o', 'id, group', $where)
                                        ->join($this->table('_options_name'), 'name', $where_name)
                                        ->get('array');
                    
                    foreach ($main_options_list as $option) {
                        if(in_array($option->type, $typesWithOptions))
                        {
                            $option->values = array();
                            if($childrens)
                                foreach ($childrens as $child) {
                                    if(-$child->group == $option->id)
                                        $option->values[$child->id] = $child->name;
                                }
                        }
                        $main_options[$option->id] = clone $option;
                    }
                }
                unset($main_options_list, $typesWithOptions, $childrens, $where, $where_img, $where_name, $wl_input_types);
            }
$vendorOption = array(1, 27);
            foreach ($products as $product)
            {
                // if($product->article)
                //     $product->name = str_replace($product->article, '', $product->name);

                $product->useAvailability = $_SESSION['option']->useAvailability;

                $find = false;
                $product->vendor = false;
                $product->options = array();
                if($_product_options)
                    foreach ($_product_options as $po) {
                        if($po->product == $product->id)
                        {
                            $find = true;
                            if(!empty($po->value) && isset($main_options[$po->option]))
                            {
                                $option = $main_options[$po->option];
                                if(empty($po->language))
                                {
                                    if(isset($main_options[$po->option]->values))
                                    {
                                        if(isset($option->values[$po->value]))
                                        {
                                            if(in_array($po->option, $vendorOption))
                                                $product->vendor = $option->values[$po->value];
                                            else
                                                $product->options[$option->name] = $option->values[$po->value];
                                        }
                                    }
                                    else
                                    {
                                        if(in_array($po->option, $vendorOption))
                                            $product->vendor = $po->value;
                                        else
                                            $product->options[$option->name] = $po->value;
                                    }
                                }
                                elseif($po->language == $_SESSION['language'])
                                {
                                    if(in_array($po->option, $vendorOption))
                                        $product->vendor = $po->value;
                                    else
                                        $product->options[$option->name] = $po->value;
                                }
                            }
                        }
                        elseif($find)
                            break;
                    }


                $product->link = $_SESSION['alias']->alias.'/'.$product->alias;
                if($_SESSION['option']->ProductMultiGroup == 0 && $product->group > 0)
                {
                    $product->link = $_SESSION['alias']->alias.'/';
                    $parents = $this->makeParents($product->group, array());
                    foreach ($parents as $parent) {
                        $product->link .= $parent->alias .'/';
                    }
                    $product->link .= $product->alias;
                }

                if($_SESSION['option']->useMarkUp && $product->markup){
                    $product->price *= $product->markup;
                    $product->old_price *= $product->markup;
                }

                if($_SESSION['option']->currency)
                {
                    $product->price *= $_SESSION['option']->currency;
                    $product->old_price *= $_SESSION['option']->currency;
                }

                $product->price = ceil($product->price);
                $product->old_price = ceil($product->old_price);

                $id = $product->id;
                if($_SESSION['option']->ProductMultiGroup)
                {
                    $product->group = array();
                    if(isset($_product_group[$id]))
                        foreach ($_product_group[$id] as $gId) {
                            if(isset($this->allGroups[$gId]) && $this->allGroups[$gId]->active)
                                $product->group[] = $this->allGroups[$gId];
                        }
                }

                $product->images = array();
                if(isset($imagesAll[$id]))
                    $product->images = $imagesAll[$id];
            }

            unset($imagesAll, $_product_group);
            return $products;
        }

        return null;
    }

    private function getProductOptions($product)
    {
        $product_options = array();
        $where_language = '';
        if($_SESSION['language']) $where_language = "AND (po.language = '{$_SESSION['language']}' OR po.language = '')";
        $this->db->executeQuery("SELECT go.id, go.alias, go.filter, po.value, it.name as type_name, it.options FROM `{$this->table('_product_options')}` as po LEFT JOIN `{$this->table('_options')}` as go ON go.id = po.option LEFT JOIN `wl_input_types` as it ON it.id = go.type WHERE go.active = 1 AND po.product = '{$product->id}' {$where_language} ORDER BY go.position");
        if($this->db->numRows() > 0)
        {
            $options = $this->db->getRows('array');
            foreach ($options as $option) {
                if($option->value != '')
                {
                    @$product_options[$option->alias]->id = $option->id;
                    $product_options[$option->alias]->alias = $option->alias;
                    $product_options[$option->alias]->filter = $option->filter;
                    $where = array();
                    $where['option'] = $option->id;
                    if($_SESSION['language']) $where['language'] = $_SESSION['language'];
                    $name = $this->db->getAllDataById($this->table('_options_name'), $where);
                    $photo = $this->db->getAllDataById($this->table('_options'), array('id' => $option->value));

                    if($name)
                    {
                        $product_options[$option->alias]->name = $name->name;
                        $product_options[$option->alias]->sufix = $name->sufix;
                    }

                    if($option->options == 1)
                    {
                        if($option->type_name == 'checkbox' || $option->type_name == 'checkbox-select2' )
                        {
                            $option->value = explode(',', $option->value);
                            $product_options[$option->alias]->value = array();
                            foreach ($option->value as $value) {
                                if($value != '')
                                {
                                    $where = array();
                                    $where['option'] = $value;
                                    if($_SESSION['language']) $where['language'] = $_SESSION['language'];
                                    $value = $this->db->getAllDataById($this->table('_options_name'), $where);
                                    if($value)
                                        $product_options[$option->alias]->value[] = $value->name;
                                }
                            }
                        }
                        else
                        {
                            $where = array('option' => $option->value);
                            if($_SESSION['language']) $where['language'] = $_SESSION['language'];
                            $value = $this->db->getAllDataById($this->table('_options_name'), $where);
                            if($value)
                                $product_options[$option->alias]->value = $value->name;
                        }
                    }
                    else
                        $product_options[$option->alias]->value = $option->value;
                }
            }
        }
        return $product_options;
    }

    private function createDescription($name, $link)
    {
        return '<h2>'.$name.'</h2>
            <ul>
            <li> Доставка товару триває від 3 до 14 днів, в залежності від типу товару і його наявності на складі.</li>
            <li> Весь товар доставляється зі складів Франції та Італії!</li>
            <li> Вартість послуг доставки товарів та зворотньої доставки коштів оплачує покупець.</li>
            <li> При одноразовому замовленні товарів на суму більше 3000 грн. ― <strong>БЕЗКОШТОВНА</strong> доставка в межах України.</li>
            </ul>

            <p style=""><strong><u>Гарантії:</u></strong> Якщо товар Вам не підійшов, не сподобався або має заводський брак, Ви маєте право повернути його протягом 14 днів, з моменту отримання. Витрати за повернення або заміну товару несе покупець! Для повернення товару необхідні:</p>

            <ol>
            <li>Збережені товарні транспортні накладні на товар.</li>
            <li>Товар без ознак використання  з всіма бірками та етикетками.</li>
            </ol>

            <div class="ck-alert ck-alert_theme_green" style="">
            <h2><a href="'.$link.'">Інші товари компанії</a></h2>
            </div>';
    }

}
?>