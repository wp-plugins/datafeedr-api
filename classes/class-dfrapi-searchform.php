<?php



class Dfrapi_SearchForm
{
    function fields() {
        $opFulltext = array(
            'contain'       => __( 'contains', DFRAPI_DOMAIN ),
            'not_contain'   => __( 'doesn\'t contain', DFRAPI_DOMAIN ),
            'start'         => __( 'starts with', DFRAPI_DOMAIN ),
            'end'           => __( 'ends with', DFRAPI_DOMAIN ),
            'match'         => __( 'matches', DFRAPI_DOMAIN )
        );
        $opFulltextExact = array_merge($opFulltext, array(
            'is'  => __( 'is', DFRAPI_DOMAIN )
        ));
        $opRange = array(
            'lt'      => __( 'less than', DFRAPI_DOMAIN ),
            'gt'      => __( 'greater than', DFRAPI_DOMAIN ),
            'between' => __( 'between', DFRAPI_DOMAIN )
        );
        $opIs = array(
            'is'     => __( 'is', DFRAPI_DOMAIN )
        );
        $opIsIsnt = array(
            'is'     => __( 'is', DFRAPI_DOMAIN ),
            'is_not' => __( 'isn\'t', DFRAPI_DOMAIN )
        );
        $opYesNo = array(
            'yes'  => __( 'yes', DFRAPI_DOMAIN ),
            'no'   => __( 'no', DFRAPI_DOMAIN )
        );

        $sortOpts = array(
            ''              => __( 'Relevance', DFRAPI_DOMAIN ),
            '+price'        => __( 'Price Ascending', DFRAPI_DOMAIN ),
            '-price'        => __( 'Price Decending', DFRAPI_DOMAIN ),
            '+saleprice'    => __( 'Sale Price Ascending', DFRAPI_DOMAIN ),
            '-saleprice'    => __( 'Sale Price Decending', DFRAPI_DOMAIN ),
            '+salediscount' => __( 'Discount Ascending', DFRAPI_DOMAIN ),
            '-salediscount' => __( 'Discount Decending', DFRAPI_DOMAIN ),
            '+merchant'     => __( 'Merchant', DFRAPI_DOMAIN ),
            '+time_updated' => __( 'Last updated Ascending', DFRAPI_DOMAIN ),
            '-time_updated' => __( 'Last updated Decending', DFRAPI_DOMAIN )
        );

        return array(
            array(
                'title' => __( 'Any field', DFRAPI_DOMAIN ),
                'name' => 'any',
                'input' => 'text',
                'operator' => $opFulltext
            ),
            array(
                'title' => __( 'Product name', DFRAPI_DOMAIN ),
                'name' => 'name',
                'input' => 'text',
                'operator' => $opFulltextExact,
            ),
            array(
                'title' => __( 'Brand', DFRAPI_DOMAIN ),
                'name' => 'brand',
                'input' => 'text',
                'operator' => $opFulltextExact,
            ),
            array(
                'title' => __( 'Description', DFRAPI_DOMAIN ),
                'name' => 'description',
                'input' => 'text',
                'operator' => $opFulltext,
            ),
            array(
                'title' => __( 'Tags', DFRAPI_DOMAIN ),
                'name' => 'tags',
                'input' => 'text',
                'operator' => array(
                    'in'     => __( 'contain', DFRAPI_DOMAIN ),
                    'not_in' => __( 'don\'t contain', DFRAPI_DOMAIN )
                )
            ),
            array(
                'title' => __( 'Category', DFRAPI_DOMAIN ),
                'name' => 'category',
                'input' => 'text',
                'operator' => $opFulltext
            ),
            array(
                'title' => __( 'Product type', DFRAPI_DOMAIN ),
                'name' => 'type',
                'input' => 'select',
                'options' => array(
                    'products' => __( 'Product', DFRAPI_DOMAIN ),
                    'coupons' => __( 'Coupon', DFRAPI_DOMAIN )
                ),
                'operator' => $opIs
            ),
            array(
                'title' => __( 'Currency', DFRAPI_DOMAIN ),
                'name' => 'currency',
                'input' => 'select',
                'options' => array('USD' => 'USD', 'CAD' => 'CAD', 'GBP' => 'GBP', 'EUR' => 'EUR'),
                'operator' => $opIsIsnt
            ),
            array(
                'title' => __( 'Price', DFRAPI_DOMAIN ),
                'name' => 'price',
                'input' => 'range',
                'operator' => $opRange
            ),
            array(
                'title' => __( 'Sale Price', DFRAPI_DOMAIN ),
                'name' => 'saleprice',
                'input' => 'range',
                'operator' => $opRange
            ),
            array(
                'title' => __( 'Network', DFRAPI_DOMAIN ),
                'name' => 'source_id',
                'input' => 'network',
                'operator' => $opIsIsnt
            ),
            array(
                'title' => __( 'Merchant', DFRAPI_DOMAIN ),
                'name' => 'merchant_id',
                'input' => 'merchant',
                'operator' => $opIsIsnt
            ),
            array(
                'title' => __( 'On Sale', DFRAPI_DOMAIN ),
                'name' => 'onsale',
                'input' => 'none',
                'operator' => $opYesNo
            ),
            array(
                'title' => __( 'Discount', DFRAPI_DOMAIN ),
                'name' => 'salediscount',
                'input' => 'range',
                'operator' => $opRange
            ),
            array(
                'title' => __( 'Has Image', DFRAPI_DOMAIN ),
                'name' => 'image',
                'input' => 'none',
                'operator' => $opYesNo
            ),
            array(
                'title' => __( 'Last updated', DFRAPI_DOMAIN ),
                'name' => 'time_updated',
                'input' => 'range',
                'operator' => array('lt' => 'before', 'gt' => 'after', 'between' => 'between'),
            ),
            array(
                'title' => __( 'Limit', DFRAPI_DOMAIN ),
                'name' => 'limit',
                'input' => 'text',
                'operator' => array('is' => 'is')
            ),
            array(
                'title' => __( 'Sort By', DFRAPI_DOMAIN ),
                'name' => 'sort',
                'input' => 'none',
                'operator' => $sortOpts
            ),
            array(
                'title' => __( 'Exclude Duplicates', DFRAPI_DOMAIN ),
                'name' => 'duplicates',
                'input' => 'text',
                'operator' => array(
                    'is' => __( 'matching these fields', DFRAPI_DOMAIN ),
                )
            )
        );
    }

    function defaults() {
        return array(
            'any'          => array('operator' => 'contain', 'value'=> ''),
            'name'         => array('operator' => 'contain', 'value'=> ''),
            'type'         => array('value'=> 'product'),
            'currency'     => array('value'=> 'USD'),
            'price'        => array('operator' => 'between', 'value'=> '0', 'value2' => '999999'),
            'saleprice'    => array('operator' => 'between', 'value'=> '0', 'value2' => '999999'),
            'source_id'    => array('value'=> array()),
            'merchant_id'  => array('value'=> array()),
            'onsale'       => array('value'=> '1'),
            'image'        => array('value'=> '1'),
            'thumbnail'    => array('value'=> '1'),
            'time_updated' => array('operator' => 'lt', 'value'=> 'today'),
            'limit'        => array('value'=> 1000),
        );
    }

    public $useSelected;
    public $prefix;

    function get($ary, $key, $default="") {
        return is_array($ary) && isset($ary[$key]) ? $ary[$key] : $default;
    }

    function inputPrefix($index) {
        return sprintf('%s[%s]', $this->prefix ? $this->prefix : "query", $index);
    }

    function byName($a, $b) {
        return strcasecmp($a['name'], $b['name']);
    }

    function ary($s) {
        if(!is_array($s))
            $s = explode(',', trim($s));
        return array_filter($s, 'strlen');
    }

    function ids($lst) {
        $ids = array();
        foreach($lst as $obj)
            $ids []= $obj['_id'];
        return $ids;
    }

    function selectOpts($opts, $selectedValue=NULL) {
        $html = "";
        foreach($opts as $value => $title) {
            $sel = $selectedValue == $value ? "selected='selected'" : "";
            $title = htmlspecialchars($title);
            $value = htmlspecialchars($value);
            $html .= "<option value=\"$value\" $sel>$title</option>";
        }
        return $html;
    }

    function allNetworks() {
        $lst = dfrapi_api_get_all_networks();
        usort($lst, array($this, 'byName'));
        return $lst;
    }

    function selectedNetworks() {
        $selected = $this->get(get_option( 'dfrapi_networks' ), 'ids');
        if(empty($selected))
            return array();

        $lst = array();
        foreach($this->allNetworks() as $net)
            if(isset($selected[$net['_id']]))
                $lst []= $net;
        return $lst;
    }
    
    function groupClass($nid) {
    	if (empty($nid)) { return ''; }
    	$networks = $this->allNetworks();
    	foreach($networks as $network) {
    		if ($network['_id'] == $nid) {
				$name = str_replace( array( " ", "-", "." ), "", $network['group'] );
				$type = ( $network['type'] == 'coupons' ) ? '_coupons' : '';
				return 'network_logo_16x16_' . strtolower( $name . $type );	
			}
		}
    }

    function selectedMerchants() {
        $selected = $this->get(get_option( 'dfrapi_merchants' ), 'ids');
        if(empty($selected))
            return array();

        $lst = array();
        foreach(dfrapi_api_get_merchants_by_id($selected) as $merchant) {
            $lst []= $merchant;
        }
        usort($lst, array($this, 'byName'));
        return $lst;
    }

    function networksMerchantsPopup($kind, $value) {
        $title = array(
            'network'  => __( 'Select Networks', DFRAPI_DOMAIN ),
            'merchant' => __( 'Select Merchants', DFRAPI_DOMAIN )
        );
        $clear = __( 'Clear search', DFRAPI_DOMAIN );
        $ok = __( 'OK', DFRAPI_DOMAIN );
        $cells = "";

        $all = ($kind == 'network') ?
            ($this->useSelected ? $this->selectedNetworks() : $this->allNetworks()) :
            $this->selectedMerchants();

        foreach($all as $obj) {
            $id = $obj['_id'];
            $nid = ($kind == 'network') ? $obj['_id'] : $obj['source_id'];
            $group_class = $this->groupClass($nid);

            $name = $obj['name'];
            $checked = in_array($id, $value) ? "checked='checked'" : "";

            $cells .= "
				<div class='inline_frame_element nid_{$nid}'>
					<label>
						<input type='checkbox' value='{$id}' $checked />
						<span class='element_name {$group_class}'>{$name}</span>
					</label>
				</div>
			";
        }

        return "
            <h1>{$title[$kind]}</h1>
            <div class='filter_action'>
                Search: <input type='text' />
                <a class='reset_search button' title='{$clear}'>&times;</a>
            </div>
            <div class='inline_frame'>
				<div>
					{$cells}
					<div class='clearfix'></div>
				</div>
			</div>
			<div><a class='button submit'>$ok</a></div>
		";
    }

    function networksMerchantsNames($kind, $value, $maxNames=5) {
        $all = ($kind == 'network') ? $this->allNetworks() : dfrapi_api_get_merchants_by_id($value);
        $names = array();
        foreach($all as $obj) {
            if(in_array($obj['_id'], $value)) {
                $names []= "<span>{$obj['name']}</span>";
                if(count($names) >= $maxNames)
                    break;
            }
        }
        $html = implode(', ', $names);
        if(count($value) - count($names)) {
            $html  .= ' ' . sprintf(__( 'and %s more', DFRAPI_DOMAIN ), count($value) - count($names));
        }
        return $html;
    }

    function chooseBox($kind, $field, $index, $value) {
        $pfx = $this->inputPrefix($index);
        $value = implode(',', $this->ary($value));
        $choose = __( 'choose', DFRAPI_DOMAIN );
        return "
            <div class='dfrapi_choose_box' rel='{$kind}'>
                <span class='names'></span>
                <a class='button choose_{$kind}'>{$choose}</a>
                <input name='{$pfx}[value]' type='hidden' value=\"$value\"/>
            </div>
        ";
    }

    function ajaxHandler() {
        $command = $this->get($_POST, 'command');
        $value = $this->ary($this->get($_POST, 'value'));
        $this->useSelected = intval($this->get($_POST, 'useSelected', 1));

        switch($command) {
            case "choose_network":
                return $this->networksMerchantsPopup('network', $value);
            case "choose_merchant":
                return $this->networksMerchantsPopup('merchant', $value);
            case "names_network":
                return $this->networksMerchantsNames('network', $value);
            case "names_merchant":
                return $this->networksMerchantsNames('merchant', $value);
        }
        return "";
    }

    function renderField($field, $index, $params) {
        $pfx = $this->inputPrefix($index);
        $value = $this->get($params, 'value');
        $operator = $this->get($params, 'operator');
        $input = "";

        switch($field['input']) {
            case 'text':
                $value = htmlspecialchars($value);
                $input = "<input class='long' name='{$pfx}[value]' type='text' value=\"$value\"/>";
                break;
            case 'select':
                $opts  = $this->selectOpts($field['options'], $value);
                $input = "<select name='{$pfx}[value]'>{$opts}</select>";
                break;
            case 'range':
                $value  = htmlspecialchars($value);
                $value2 = htmlspecialchars($this->get($params, 'value2'));
                $and = __( 'and', DFRAPI_DOMAIN );
                $input = "
        			<input class='short' name='{$pfx}[value]' type='text' value=\"$value\"/>
		        	<span class='value2' style='display:none'>
			            $and
			            <input class='short' name='{$pfx}[value2]' type='text' value=\"$value2\" />
                    </span>
		        ";
                break;
            case 'network':
                $input = $this->chooseBox('network', $field, $index, $value);
                break;
            case 'merchant':
                $input = $this->chooseBox('merchant', $field, $index, $value);
                break;
            case 'none':
                $input = "";
                break;
        }

        if(count($field['operator']) == 1) {
            $key = key($field['operator']);
            $val = current($field['operator']);
            $operator = "
                <input type='hidden' name='{$pfx}[operator]' value=\"{$key}\" />
                <span>{$val}</span>
            ";
        } else {
            $opts = $this->selectOpts($field['operator'], $operator);
            $operator = "<select name='{$pfx}[operator]'>{$opts}</select>";
        }
        return array($operator, $input);
    }

    function renderRow($field, $index, $params, $show) {
        $pfx = $this->inputPrefix($index);
        list($operator, $input) = $this->renderField($field, $index, $params);
        $fieldOpts = '';
        foreach($this->fields() as $f) {
            if(!$this->useSelected && $f['name'] == 'merchant_id')
                continue;
            $sel = $field['name'] == $f['name'] ? "selected='selected'" : "";
            $fieldOpts .= "<option value=\"{$f['name']}\" $sel>{$f['title']}</option>";
        }
        $style = $show ? "" : "style='display:none'";
        return "
            <div class='filter filter_{$field['name']}' {$style}>
                <div class='valuewrapper'>
                    <div class='value'>{$input}</div>
                </div>
                <div class='plusminus'><a class='minus'></a> </div>
                <div class='field'><select name='{$pfx}[field]' style='width: 95%'>{$fieldOpts}</select></div>
                <div class='operator'>{$operator}</div>
            </div>
        ";
    }

    function render($prefix, $query, $useSelected=TRUE) {
        $this->prefix = $prefix;
        $this->useSelected = intval($useSelected);
        if(!$query)
            $query = array(
                array('field'=>'any', 'value'=>'')
            );
        $defaults = $this->defaults();

        $fieldMap = array();
        $fieldUsed = array();
        foreach($this->fields() as $f) {
            if(!$this->useSelected && $f['name'] == 'merchant_id')
                continue;
            $fieldMap[$f['name']] = $f;
        }

        $form = "";
        $index = 0;
        foreach($query as $params) {
            $field = $this->get($fieldMap, $this->get($params, 'field'));
            if(!$field)
                continue;
            $fieldUsed[$field['name']] = 1;
            $form .= $this->renderRow($field, $index++, $params, TRUE);
        }
        $show = ($index == 0); // if none shown, show the first
        foreach($fieldMap as $field) {
            if(!isset($fieldUsed[$field['name']])) {
                $form .= $this->renderRow($field, $index++, $defaults[$field['name']], $show);
                $show = FALSE;
            }
        }

        $loading = __( 'Loading, please wait', DFRAPI_DOMAIN );
        $add = __( 'add filter', DFRAPI_DOMAIN );
        return "
            <div id='dfrapi_search_form'>
                <input type='hidden' id='dfrapi_useSelected' value='{$this->useSelected}' />
                <div id='dfprs_loading_content' style='display:none'>
                    <div class='dfrapi_loading'></div>
                    <h3>{$loading}</h3>
                </div>
                {$form}
                <div class='clearfix'></div>
                <div id='dfrapi_search_form_filter'><a href='#'><span class='dashicons dashicons-plus'> </span> $add</a></div>
            </div>
        ";
    }

    function combineLists($lst, $func) {
        if(!count($lst))
            return array();
        if(count($lst) == 1)
            return current($lst);
        $lst = call_user_func_array($func, $lst);
        return count($lst) ? $lst : NULL;
    }
    
    function idFilter($inList, $exList) {
        $inList = $this->combineLists($inList, 'array_intersect');
        $exList = $this->combineLists($exList, 'array_merge');

        if(is_null($inList) || is_null($exList)) {
            return NULL;
        }
        if(count($inList)) {
            if(count($exList)) {
                $inList = array_diff($inList, $exList);
                if(!count($inList))
                    return NULL;
            }
            return array("IN", $inList);
        }
        if(count($exList)) {
            return array("!IN", $exList);
        }
        return array(NULL, NULL);
    }

    function fulltextFilter($operator, $value) {
        if($operator == 'match')
            return array("LIKE", $value);
        $value = trim(preg_replace('~[!\\[\\]"^$]+~', " ", $value));
        if(strlen($value)) {
            switch($operator) {
                case 'is':          return array('LIKE', "^$value\$");
                case 'contain':     return array('LIKE', $value);
                case 'not_contain': return array('!LIKE', $value);
                case 'start':       return array('LIKE', "^$value");
                case 'end':         return array('LIKE', "$value\$");
            }
        }
        return array(NULL, NULL);
    }

    function makeFilters($query, $useSelected=TRUE) {

        $filters   = array();

        $selected = array(
            'in:source_id'   => array(),
            'ex:source_id'   => array(),
            'in:merchant_id' => array(),
            'ex:merchant_id' => array(),
        );

        $allNetworks = $this->ids($this->allNetworks());

        if($useSelected) {
            $selected['in:source_id']   []= $this->ids($this->selectedNetworks());
            $selected['in:merchant_id'] []= $this->ids($this->selectedMerchants());
        } else {
            $selected['in:source_id']   []= $allNetworks;
        }

        foreach($query as $params) {
            $fname    = $params['field'];
            $value    = $this->get($params, 'value');
            $value2   = $this->get($params, 'value2');
            $operator = strtolower($this->get($params, 'operator'));

            switch($fname) {
                case 'any':
                case 'name':
                case 'brand':
                case 'description':
                case 'category':
                    $s = $this->fulltextFilter($operator, $value);
                    if(!is_null($s[0])) {
                        $filters []= "{$fname} {$s[0]} {$s[1]}";
                    }
                    break;
                case 'tags':
                    $operator = ($operator == 'in') ? "LIKE" : "!LIKE";
                    $filters []= "{$fname} {$operator} {$value}";
                    break;
                case 'currency':
                    $filters []= "{$fname} = {$value}";
                    break;
                case 'price':
                case 'saleprice':
                case 'salediscount':
                    $conv = $fname == 'salediscount' ? 'intval' : 'dfrapi_price_to_int';
                    $value = $conv($value);
                    switch($operator) {
                        case 'between':
                            $value2 = $conv($value2);
                            $filters []= "{$fname} > {$value}";
                            $filters []= "{$fname} < {$value2}";
                            break;
                        case 'lt':
                            $filters []= "{$fname} < {$value}";
                            break;
                        case 'gt':
                            $filters []= "{$fname} > {$value}";
                            break;
                    }
                    break;
                case 'source_id':
                case 'merchant_id':
                    $key = ($operator == 'is') ? 'in' : 'ex';
                    $selected["$key:$fname"] []= $this->ary($value);
                    break;
                case 'type':
                    $ids = array();
                    foreach($this->allNetworks() as $net) {
                        if($net['type'] == $value)
                            $ids []= $net['_id'];
                    }
                    $selected["in:source_id"] []= $ids;
                    break;
                case 'onsale':
                    $value = ($operator == 'yes') ? '1' : '0';
                    $filters []= "{$fname} = {$value}";
                    break;
                case 'image':
                case 'thumbnail':
                    $operator = ($operator == 'yes') ? '!EMPTY' : 'EMPTY';
                    $filters []= "image {$operator}";
                    break;
                case 'time_updated':
                    $value = @date('Y-m-d H:i:s', strtotime($value));
                    switch($operator) {
                        case 'between':
                            $value2 = @date('Y-m-d H:i:s', strtotime($value2));
                            $filters []= "{$fname} > {$value}";
                            $filters []= "{$fname} < {$value2}";
                            break;
                        case 'lt':
                            $filters []= "{$fname} < {$value}";
                            break;
                        case 'gt':
                            $filters []= "{$fname} > {$value}";
                            break;
                    }
                    break;
            }
        }

        $s = $this->idFilter($selected['in:source_id'], $selected['ex:source_id']);
        if(is_null($s))
            return array('error' => 'No networks selected');
        if(!is_null($s[0]) && count($s[1]) < count($allNetworks))
            $filters []= "source_id {$s[0]} ". implode(',', $s[1]);

        $s = $this->idFilter($selected['in:merchant_id'], $selected['ex:merchant_id']);
        if(is_null($s))
            return array('error' => 'No merchants selected');
        if(!is_null($s[0]))
            $filters []= "merchant_id {$s[0]} ". implode(',', $s[1]);

        return $filters;

    }

}
